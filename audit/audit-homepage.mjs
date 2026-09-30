#!/usr/bin/env node
/**
 * Audit de performance d'une page WordPress / Elementor / WooCommerce.
 *
 * Usage :
 *   npm i -D playwright            (ou playwright installé globalement)
 *   node audit/audit-homepage.mjs https://prod.la-maison-du-dos.com/ [--desktop]
 *
 * Produit dans audit/rapport/ :
 *   - rapport-<profil>.md   : rapport lisible (plugins, scripts, CSS, poids, code inutilisé, Web Vitals)
 *   - requetes-<profil>.json: toutes les requêtes brutes
 *
 * Profil par défaut : mobile (Moto G Power, 4G lente simulée), comme PageSpeed Insights.
 */
import { chromium, devices } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const url = process.argv[2];
if (!url) { console.error('Usage : node audit-homepage.mjs <url> [--desktop]'); process.exit(1); }
const desktop = process.argv.includes('--desktop');
const profile = desktop ? 'desktop' : 'mobile';
const outDir = path.join(path.dirname(fileURLToPath(import.meta.url)), 'rapport');
const origin = new URL(url).origin;

const kb = (b) => (b / 1024).toFixed(1) + ' Ko';

/** Classe une URL : plugin WordPress, thème, cœur WP, uploads ou tiers. */
function classify(u) {
  const { hostname, pathname } = new URL(u);
  let m;
  if ((m = pathname.match(/\/wp-content\/plugins\/([^/]+)/))) return `plugin:${m[1]}`;
  if ((m = pathname.match(/\/wp-content\/themes\/([^/]+)/))) return `theme:${m[1]}`;
  if (pathname.includes('/wp-content/uploads/elementor/')) return 'elementor:generated-css';
  if (pathname.includes('/wp-content/uploads/')) return 'uploads';
  if (pathname.includes('/wp-includes/')) {
    if (pathname.includes('jquery')) return 'wp-core:jquery';
    return 'wp-core';
  }
  if (pathname.includes('/wp-json/') || pathname.includes('admin-ajax.php')) return 'wp-ajax';
  if (u.startsWith(origin)) return 'site';
  return `tiers:${hostname}`;
}

const browser = await chromium.launch({
  executablePath: process.env.CHROMIUM_PATH || undefined,
});
const context = await browser.newContext(
  desktop
    ? { viewport: { width: 1366, height: 900 } }
    : { ...devices['Moto G4'], locale: 'fr-FR' }
);
const page = await context.newPage();
const cdp = await context.newCDPSession(page);
await cdp.send('Network.enable');
if (!desktop) {
  // Réseau « 4G lente » + CPU x4 : conditions Lighthouse mobile
  await cdp.send('Network.emulateNetworkConditions', {
    offline: false, latency: 150, downloadThroughput: 1.6 * 1024 * 1024 / 8, uploadThroughput: 750 * 1024 / 8,
  });
  await cdp.send('Emulation.setCPUThrottlingRate', { rate: 4 });
}

// Taille réelle transférée (compressée) par requête
const sizes = new Map();
cdp.on('Network.loadingFinished', (e) => sizes.set(e.requestId, e.encodedDataLength));
const reqIds = new Map();
cdp.on('Network.requestWillBeSent', (e) => reqIds.set(e.request.url, e.requestId));

// Web Vitals collectés dans la page
await page.addInitScript(() => {
  window.__vitals = { lcp: 0, lcpEl: '', cls: 0, longTasks: [], fcp: 0 };
  new PerformanceObserver((l) => {
    for (const e of l.getEntries()) {
      window.__vitals.lcp = e.startTime;
      const el = e.element;
      window.__vitals.lcpEl = el ? `${el.tagName.toLowerCase()}${el.id ? '#' + el.id : ''}${el.className && typeof el.className === 'string' ? '.' + el.className.trim().split(/\s+/).slice(0, 3).join('.') : ''} ${e.url || ''}` : '';
    }
  }).observe({ type: 'largest-contentful-paint', buffered: true });
  new PerformanceObserver((l) => {
    for (const e of l.getEntries()) if (!e.hadRecentInput) window.__vitals.cls += e.value;
  }).observe({ type: 'layout-shift', buffered: true });
  new PerformanceObserver((l) => {
    for (const e of l.getEntries()) window.__vitals.longTasks.push({ start: e.startTime, duration: e.duration });
  }).observe({ type: 'longtask', buffered: true });
  new PerformanceObserver((l) => {
    for (const e of l.getEntries()) if (e.name === 'first-contentful-paint') window.__vitals.fcp = e.startTime;
  }).observe({ type: 'paint', buffered: true });
});

await page.coverage.startJSCoverage({ resetOnNavigation: false });
await page.coverage.startCSSCoverage({ resetOnNavigation: false });

const t0 = Date.now();
const response = await page.goto(url, { waitUntil: 'load', timeout: 120_000 });
const loadMs = Date.now() - t0;
// Laisse les scripts différés / tiers se charger, puis simule un scroll
await page.waitForTimeout(3000);
await page.evaluate(async () => {
  for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 120)); }
});
await page.waitForTimeout(2000);

const jsCov = await page.coverage.stopJSCoverage();
const cssCov = await page.coverage.stopCSSCoverage();

// Scripts / CSS bloquants dans le <head> + analyse DOM/SEO
const dom = await page.evaluate(() => {
  const head = document.head;
  const blockingJS = [...head.querySelectorAll('script[src]')]
    .filter((s) => !s.async && !s.defer && s.type !== 'module').map((s) => s.src);
  const blockingCSS = [...head.querySelectorAll('link[rel="stylesheet"]')]
    .filter((l) => !l.media || l.media === 'all' || l.media === 'screen').map((l) => l.href);
  const inlineScripts = [...document.querySelectorAll('script:not([src])')]
    .filter((s) => !s.type || s.type === 'text/javascript')
    .map((s) => ({ id: s.id || '', size: s.textContent.length, start: s.textContent.trim().slice(0, 90).replace(/\s+/g, ' ') }));
  const imgs = [...document.images].map((i) => ({
    src: i.currentSrc || i.src, alt: i.getAttribute('alt'), w: i.getAttribute('width'), h: i.getAttribute('height'),
    loading: i.loading, natural: `${i.naturalWidth}x${i.naturalHeight}`, rendered: `${Math.round(i.getBoundingClientRect().width)}x${Math.round(i.getBoundingClientRect().height)}`,
  }));
  const meta = (n) => document.querySelector(`meta[name="${n}"],meta[property="${n}"]`)?.content || null;
  return {
    blockingJS, blockingCSS, inlineScripts, imgs,
    domNodes: document.getElementsByTagName('*').length,
    maxDepth: (function depth(el) { let m = 0; for (const c of el.children) m = Math.max(m, depth(c)); return m + 1; })(document.documentElement),
    elementorVersion: document.querySelector('meta[name="generator"][content*="Elementor"]')?.content || null,
    generators: [...document.querySelectorAll('meta[name="generator"]')].map((m) => m.content),
    seo: {
      title: document.title,
      description: meta('description'),
      canonical: document.querySelector('link[rel="canonical"]')?.href || null,
      robots: meta('robots'),
      ogImage: meta('og:image'),
      h1: [...document.querySelectorAll('h1')].map((h) => h.textContent.trim().replace(/\s+/g, ' ')),
      h2Count: document.querySelectorAll('h2').length,
      lang: document.documentElement.lang,
      jsonLdTypes: [...document.querySelectorAll('script[type="application/ld+json"]')].flatMap((s) => {
        try { const j = JSON.parse(s.textContent); const arr = j['@graph'] || [j]; return arr.map((x) => [].concat(x['@type']).join('/')); } catch { return ['(JSON-LD invalide)']; }
      }),
    },
    fonts: [...document.fonts].filter((f) => f.status === 'loaded').map((f) => `${f.family} ${f.weight} ${f.style}`),
    vitals: window.__vitals,
    nav: performance.getEntriesByType('navigation')[0]?.toJSON(),
  };
});

// Assemblage des requêtes
const entries = await page.evaluate(() => performance.getEntriesByType('resource').map((r) => ({
  url: r.name, type: r.initiatorType, start: Math.round(r.startTime), duration: Math.round(r.duration), transfer: r.transferSize, decoded: r.decodedBodySize,
})));
const requests = entries.map((r) => {
  const id = reqIds.get(r.url);
  const transfer = (id && sizes.get(id)) || r.transfer || 0;
  return { ...r, transfer, group: classify(r.url) };
});
const docTransfer = sizes.get(reqIds.get(response.url())) || 0;

// Code inutilisé par fichier
const unused = [];
for (const e of jsCov) {
  if (!e.url.startsWith('http')) continue;
  const total = e.source?.length || 0;
  // Plages imbriquées (fonction puis blocs) : la plus interne l'emporte
  const covered = new Uint8Array(total);
  for (const fn of e.functions) for (const r of fn.ranges) covered.fill(r.count > 0 ? 1 : 0, r.startOffset, r.endOffset);
  const used = covered.reduce((a, b) => a + b, 0);
  unused.push({ url: e.url, kind: 'JS', total, unusedPct: total ? Math.round((1 - used / total) * 100) : 0, group: classify(e.url) });
}
for (const e of cssCov) {
  if (!e.url.startsWith('http')) continue;
  const total = e.text.length;
  const used = e.ranges.reduce((a, r) => a + (r.end - r.start), 0);
  unused.push({ url: e.url, kind: 'CSS', total, unusedPct: total ? Math.round((1 - used / total) * 100) : 0, group: classify(e.url) });
}

// Agrégation par groupe (plugin, thème, tiers…)
const byGroup = {};
for (const r of requests) {
  const g = (byGroup[r.group] ??= { requests: 0, transfer: 0, js: 0, css: 0, img: 0, font: 0, other: 0 });
  g.requests++; g.transfer += r.transfer;
  const ext = new URL(r.url).pathname.split('.').pop().toLowerCase();
  if (r.type === 'script' || ext === 'js') g.js += r.transfer;
  else if (r.type === 'link' && ext === 'css' || ext === 'css') g.css += r.transfer;
  else if (r.type === 'img' || /^(png|jpe?g|gif|webp|avif|svg)$/.test(ext)) g.img += r.transfer;
  else if (/^(woff2?|ttf|otf|eot)$/.test(ext)) g.font += r.transfer;
  else g.other += r.transfer;
}
const groups = Object.entries(byGroup).sort((a, b) => b[1].transfer - a[1].transfer);
const totalTransfer = requests.reduce((a, r) => a + r.transfer, 0) + docTransfer;
const tbt = dom.vitals.longTasks.reduce((a, t) => a + Math.max(0, t.duration - 50), 0);

// Rapport Markdown
const L = [];
L.push(`# Audit performance — ${url}`, '', `Profil : **${profile}**${desktop ? '' : ' (4G lente, CPU ×4)'} · ${new Date().toISOString()}`, '');
L.push('## Synthèse', '', '| Indicateur | Valeur | Objectif |', '|---|---|---|');
L.push(`| TTFB (réponse serveur) | ${Math.round(dom.nav?.responseStart || 0)} ms | < 800 ms |`);
L.push(`| First Contentful Paint | ${Math.round(dom.vitals.fcp)} ms | < 1 800 ms |`);
L.push(`| Largest Contentful Paint | ${Math.round(dom.vitals.lcp)} ms | < 2 500 ms |`);
L.push(`| Total Blocking Time (approx.) | ${Math.round(tbt)} ms | < 200 ms |`);
L.push(`| Cumulative Layout Shift | ${dom.vitals.cls.toFixed(3)} | < 0,1 |`);
L.push(`| Événement load | ${loadMs} ms | — |`);
L.push(`| Requêtes | ${requests.length + 1} | < 50 |`);
L.push(`| Poids total transféré | ${kb(totalTransfer)} | < 1 500 Ko |`);
L.push(`| HTML (compressé) | ${kb(docTransfer)} | < 60 Ko |`);
L.push(`| Nœuds DOM / profondeur | ${dom.domNodes} / ${dom.maxDepth} | < 1 500 / < 32 |`);
L.push('', `Élément LCP : \`${dom.vitals.lcpEl || 'n/d'}\``, '');
L.push(`Générateurs : ${dom.generators.join(' · ') || 'n/d'}`, '');

L.push('## Poids par plugin / thème / service tiers', '', '| Source | Req. | Total | JS | CSS | Images | Polices |', '|---|---:|---:|---:|---:|---:|---:|');
for (const [g, v] of groups) L.push(`| \`${g}\` | ${v.requests} | ${kb(v.transfer)} | ${kb(v.js)} | ${kb(v.css)} | ${kb(v.img)} | ${kb(v.font)} |`);

L.push('', '## Ressources bloquant le rendu (dans le `<head>`)', '');
L.push(`### JavaScript synchrone (${dom.blockingJS.length})`, '');
dom.blockingJS.forEach((s) => L.push(`- \`${classify(s)}\` — ${s}`));
L.push('', `### CSS (${dom.blockingCSS.length})`, '');
dom.blockingCSS.forEach((s) => L.push(`- \`${classify(s)}\` — ${s}`));

L.push('', '## Code chargé mais inutilisé sur cette page', '', '| Type | Source | Taille | Inutilisé | Fichier |', '|---|---|---:|---:|---|');
unused.filter((u) => u.total > 2048).sort((a, b) => b.total * b.unusedPct - a.total * a.unusedPct)
  .forEach((u) => L.push(`| ${u.kind} | \`${u.group}\` | ${kb(u.total)} | ${u.unusedPct} % | ${u.url.split('?')[0].replace(origin, '')} |`));

L.push('', '## Toutes les requêtes (triées par poids)', '', '| Poids | Durée | Source | URL |', '|---:|---:|---|---|');
[...requests].sort((a, b) => b.transfer - a.transfer)
  .forEach((r) => L.push(`| ${kb(r.transfer)} | ${r.duration} ms | \`${r.group}\` | ${r.url.length > 110 ? r.url.slice(0, 110) + '…' : r.url} |`));

L.push('', '## Scripts inline', '', '| Taille | id | Début |', '|---:|---|---|');
dom.inlineScripts.sort((a, b) => b.size - a.size).forEach((s) => L.push(`| ${kb(s.size)} | ${s.id} | \`${s.start.replace(/\|/g, '\\|').replace(/`/g, "'")}\` |`));

L.push('', '## Images', '', '| Rendu | Naturel | width/height | loading | alt | URL |', '|---|---|---|---|---|---|');
dom.imgs.forEach((i) => L.push(`| ${i.rendered} | ${i.natural} | ${i.w && i.h ? 'oui' : '**non (CLS)**'} | ${i.loading} | ${i.alt === null ? '**absent**' : i.alt || '(vide)'} | ${i.src.slice(0, 90)} |`));

L.push('', '## Polices chargées', '');
dom.fonts.forEach((f) => L.push(`- ${f}`));

L.push('', '## SEO', '', '```json', JSON.stringify(dom.seo, null, 2), '```');

await mkdir(outDir, { recursive: true });
await writeFile(path.join(outDir, `rapport-${profile}.md`), L.join('\n'));
await writeFile(path.join(outDir, `requetes-${profile}.json`), JSON.stringify({ url, profile, requests, unused, dom }, null, 2));
await browser.close();

console.log(`LCP ${Math.round(dom.vitals.lcp)} ms · CLS ${dom.vitals.cls.toFixed(3)} · TBT ~${Math.round(tbt)} ms · ${requests.length + 1} requêtes · ${kb(totalTransfer)}`);
console.log(`Rapport : ${path.join(outDir, `rapport-${profile}.md`)}`);
