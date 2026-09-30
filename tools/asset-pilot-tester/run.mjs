#!/usr/bin/env node
/**
 * Asset Pilot – testeur d'impact automatisé (V2).
 *
 * Charge chaque page et joue chaque parcours deux fois :
 *   1. référence  : sans aucune règle (?ap_off=1)
 *   2. avec règles : règles en ligne + en test (?ap_test=<jeton>)
 * puis compare erreurs JavaScript, ressources en échec, éléments critiques, poids, requêtes et temps d'affichage,
 * et vérifie que les parcours (ajout au panier, commande, scénarios personnalisés) fonctionnent toujours.
 *
 * Usage :
 *   node tools/asset-pilot-tester/run.mjs --config tools/asset-pilot-tester/config.exemple.json
 *   options : --no-post (ne pas envoyer le rapport au site)  --baseline live (référence = règles en ligne seulement)
 *             --headed (voir le navigateur)
 * Code de sortie : 0 si aucune régression, 1 sinon (utilisable en intégration continue).
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import path from 'node:path';

// ------------------------------------------------------------------ Options
const args = process.argv.slice(2);
const opt = (name, def) => { const i = args.indexOf('--' + name); return i === -1 ? def : (args[i + 1] && !args[i + 1].startsWith('--') ? args[i + 1] : true); };
const cfgPath = opt('config');
if (!cfgPath) { console.error('Usage : node run.mjs --config config.json [--no-post] [--baseline off|live] [--headed]'); process.exit(2); }
const cfg = JSON.parse(readFileSync(cfgPath, 'utf8'));
const BASE = cfg.baseUrl.replace(/\/$/, '');
const TOKEN = process.env.ASSET_PILOT_TOKEN || cfg.token;
const baselineMode = opt('baseline', cfg.baseline || 'off');
const post = !args.includes('--no-post') && cfg.postReport !== false;
const outDir = path.resolve(path.dirname(cfgPath), cfg.outDir || 'rapports');
const abs = (u) => new URL(u, BASE + '/').toString();
if (!TOKEN) { console.error('Jeton de test manquant (config.token ou variable ASSET_PILOT_TOKEN).'); process.exit(2); }

const S = Object.assign({
  addToCart: '.single_add_to_cart_button',
  cartItem: '.woocommerce-cart-form__cart-item, .wc-block-cart-items__row',
  placeOrder: '#place_order, .wc-block-components-checkout-place-order-button',
  paymentMethods: '.wc_payment_methods li, .wc-block-components-radio-control__option',
}, cfg.selectors || {});

// Paramètres ajoutés à chaque page du site selon le mode
// (ap_run transmet le jeton à la sonde sans activer le mode test)
const modeParams = (mode) => mode === 'base'
  ? (baselineMode === 'live' ? { ap_probe: '1', ap_run: TOKEN } : { ap_off: '1', ap_probe: '1', ap_run: TOKEN })
  : { ap_test: TOKEN, ap_probe: '1' };

// ------------------------------------------------------------------ Navigateur
const browser = await chromium.launch({
  headless: !args.includes('--headed'),
  executablePath: process.env.CHROMIUM_PATH || undefined,
  args: process.env.CHROMIUM_ARGS ? process.env.CHROMIUM_ARGS.split(' ') : [],
});

/** Contexte isolé (cookies, panier) où toute page du site reçoit les paramètres du mode. */
async function newContext(mode, device) {
  const ctx = await browser.newContext(device === 'mobile'
    ? { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true, locale: 'fr-FR' }
    : { viewport: { width: 1366, height: 900 }, locale: 'fr-FR' });
  const params = modeParams(mode);
  const origin = new URL(BASE).origin;
  await ctx.route('**/*', (route) => {
    const req = route.request();
    if (req.resourceType() !== 'document' || !req.url().startsWith(origin)) return route.continue();
    const u = new URL(req.url());
    if (u.pathname.startsWith('/wp-admin') || u.pathname.startsWith('/wp-json')) return route.continue();
    Object.entries(params).forEach(([k, v]) => u.searchParams.set(k, v));
    return route.continue({ url: u.toString() });
  });
  return ctx;
}

/** Récupère le rapport de la sonde injectée par l'extension. */
async function readProbe(page) {
  try {
    await page.waitForFunction(() => window.__apProbe && window.__apProbe.done, null, { timeout: 45000 });
    return await page.evaluate(() => window.__apProbe);
  } catch { return null; }
}

function metrics(r) {
  return {
    js_errors: r.js_errors.length, console_errors: r.console_errors.length, failed: r.failed.length,
    requests: r.requests, bytes: r.bytes, js_files: r.js_files, js_bytes: r.js_bytes, css_files: r.css_files,
    lcp: r.lcp, tbt: r.tbt, cls: r.cls, dom: r.dom,
    checks_failed: r.checks.filter((c) => !c.visible).length,
  };
}

function compare(label, url, base, test) {
  const p = { url, label, verdict: 'ok', base: null, test: null, new_errors: [], new_failed: [], lost_checks: [], notes: [] };
  if (!base || !test) {
    p.verdict = 'error';
    p.notes.push(!base ? "La page de référence n'a pas répondu (sonde absente : l'extension est-elle active ?)" : "La page avec règles n'a pas répondu");
    return p;
  }
  p.base = metrics(base); p.test = metrics(test);
  const baseMsgs = new Set(base.js_errors.map((e) => e.msg));
  p.new_errors = test.js_errors.filter((e) => !baseMsgs.has(e.msg)).map((e) => e.msg + (e.src ? ` (${e.src.split('/').pop()}:${e.line})` : ''));
  const baseFailed = new Set(base.failed.map((f) => f.url));
  p.new_failed = test.failed.filter((f) => !baseFailed.has(f.url)).map((f) => f.url);
  base.checks.forEach((c, i) => { if (c.visible && test.checks[i] && !test.checks[i].visible) p.lost_checks.push(c.label); });
  const newConsole = test.console_errors.filter((m) => !base.console_errors.includes(m));
  if (p.new_errors.length || p.lost_checks.length || p.new_failed.length) p.verdict = 'regression';
  else if (newConsole.length) { p.verdict = 'warn'; p.notes.push(...newConsole.slice(0, 5).map((m) => 'console : ' + m)); }
  if (base.jquery && !test.jquery) { p.verdict = 'regression'; p.notes.push("jQuery n'est plus disponible"); }
  return p;
}

// ------------------------------------------------------------------ 1. Pages
async function probePage(url, mode, device) {
  const ctx = await newContext(mode, device);
  const page = await ctx.newPage();
  try {
    await page.goto(abs(url), { waitUntil: 'load', timeout: 90000 });
    return await readProbe(page);
  } catch (e) {
    return null;
  } finally { await ctx.close(); }
}

// ------------------------------------------------------------------ 2. Parcours
/** Joue un parcours dans un mode ; renvoie { ok, steps[], errors[] }. */
async function runScenario(sc, mode) {
  const ctx = await newContext(mode, sc.device);
  const page = await ctx.newPage();
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message.slice(0, 300)));
  const steps = [];
  const step = async (label, fn) => {
    try { await fn(); steps.push({ label, ok: true }); return true; }
    catch (e) { steps.push({ label, ok: false, error: String(e.message || e).split('\n')[0].slice(0, 200) }); return false; }
  };
  try {
    for (const a of sc.steps) {
      const ok = await step(a.label || JSON.stringify(a).slice(0, 80), async () => {
        if (a.goto) await page.goto(abs(a.goto), { waitUntil: 'load', timeout: 90000 });
        if (a.click) {
          const sel = S[a.click] || a.click;
          await page.locator(sel).first().click({ timeout: 15000 });
          await page.waitForLoadState('load', { timeout: 60000 }).catch(() => {});
        }
        if (a.fill) await page.locator(a.fill[0]).first().fill(a.fill[1], { timeout: 15000 });
        if (a.select) await page.locator(a.select[0]).first().selectOption(a.select[1], { timeout: 15000 });
        if (a.wait) await page.waitForTimeout(a.wait);
        if (a.expectVisible) {
          const sel = S[a.expectVisible] || a.expectVisible;
          await page.locator(sel).first().waitFor({ state: 'visible', timeout: a.timeout || 20000 });
        }
        if (a.expectCount) {
          const sel = S[a.expectCount[0]] || a.expectCount[0];
          await page.locator(sel).first().waitFor({ state: 'attached', timeout: 20000 });
          const n = await page.locator(sel).count();
          if (n < a.expectCount[1]) throw new Error(`${n} élément(s) « ${sel} » au lieu d'au moins ${a.expectCount[1]}`);
        }
        if (a.expectText) {
          const txt = await page.locator(a.expectText[0]).first().innerText({ timeout: 15000 });
          if (!txt.includes(a.expectText[1])) throw new Error(`texte « ${a.expectText[1]} » absent`);
        }
      });
      if (!ok) break; // inutile de continuer un parcours cassé
    }
  } finally { await ctx.close(); }
  return { ok: steps.every((s) => s.ok), steps, errors };
}

/** Parcours WooCommerce générés automatiquement à partir de la configuration. */
function builtinScenarios() {
  const out = [];
  const w = cfg.woocommerce;
  if (w && w.productUrl) {
    out.push({
      name: 'Ajout au panier', steps: [
        { label: 'Ouvrir la fiche produit', goto: w.productUrl },
        { label: 'Bouton « Ajouter au panier » visible', expectVisible: 'addToCart' },
        { label: 'Cliquer sur « Ajouter au panier »', click: 'addToCart' },
        { label: 'Ouvrir le panier', goto: w.cartUrl || '/cart/' },
        { label: 'Le produit est dans le panier', expectCount: ['cartItem', 1] },
      ],
    });
    out.push({
      name: 'Commande (sans payer)', steps: [
        { label: 'Ouvrir la fiche produit', goto: w.productUrl },
        { label: 'Ajouter au panier', click: 'addToCart' },
        { label: 'Ouvrir la page de commande', goto: w.checkoutUrl || '/checkout/' },
        { label: 'Moyens de paiement affichés', expectVisible: 'paymentMethods', timeout: 30000 },
        { label: 'Bouton « Commander » visible', expectVisible: 'placeOrder', timeout: 30000 },
      ],
    });
  }
  return out;
}

// ------------------------------------------------------------------ Exécution
const kb = (b) => (b / 1024).toFixed(1).replace('.', ',') + ' Ko';
console.log(`Asset Pilot – test d'impact sur ${BASE} (référence : ${baselineMode === 'off' ? 'sans aucune règle' : 'règles en ligne'})\n`);

const results = [];
for (const pg of cfg.pages || []) {
  for (const device of pg.devices || ['desktop']) {
    const label = pg.label + (device === 'mobile' ? ' (mobile)' : '');
    process.stdout.write(`• Page ${label}… `);
    const base = await probePage(pg.url, 'base', device);
    const test = await probePage(pg.url, 'test', device);
    const r = compare(label, abs(pg.url), base, test);
    results.push(r);
    console.log(r.verdict === 'ok' ? 'OK' : r.verdict.toUpperCase(), r.base ? `(${kb(r.base.bytes)} → ${kb(r.test.bytes)}, ${r.base.requests} → ${r.test.requests} requêtes)` : '');
    [...r.new_errors.map((e) => 'erreur JS : ' + e), ...r.lost_checks.map((e) => 'élément perdu : ' + e), ...r.new_failed.map((e) => 'ressource en échec : ' + e), ...r.notes].forEach((l) => console.log('    ↳ ' + l));
  }
}

for (const sc of [...builtinScenarios(), ...(cfg.scenarios || [])]) {
  process.stdout.write(`• Parcours « ${sc.name} »… `);
  const base = await runScenario(sc, 'base');
  const test = await runScenario(sc, 'test');
  const r = { url: '', label: 'Parcours : ' + sc.name, verdict: 'ok', base: null, test: null, new_errors: [], new_failed: [], lost_checks: [], notes: [] };
  if (!base.ok) {
    r.verdict = 'warn';
    const f = base.steps.find((s) => !s.ok);
    r.notes.push(`Le parcours échoue déjà sans règle, à l'étape « ${f.label} » : ${f.error}. Vérifiez la configuration.`);
  } else if (!test.ok) {
    r.verdict = 'regression';
    const f = test.steps.find((s) => !s.ok);
    r.notes.push(`Échec avec les règles à l'étape « ${f.label} » : ${f.error}`);
  }
  const baseErr = new Set(base.errors);
  r.new_errors = [...new Set(test.errors.filter((e) => !baseErr.has(e)))];
  if (r.new_errors.length && r.verdict === 'ok') r.verdict = 'regression';
  results.push(r);
  console.log(r.verdict === 'ok' ? 'OK' : r.verdict.toUpperCase());
  [...r.new_errors.map((e) => 'erreur JS : ' + e), ...r.notes].forEach((l) => console.log('    ↳ ' + l));
}
await browser.close();

// ------------------------------------------------------------------ Rapport
const pagesOnly = results.filter((r) => r.base);
const sum = (k, side) => pagesOnly.reduce((s, r) => s + (r[side][k] || 0), 0);
const report = {
  source: 'outil de test (ligne de commande)',
  summary: {
    pages: results.length,
    regressions: results.filter((r) => r.verdict === 'regression' || r.verdict === 'error').length,
    warnings: results.filter((r) => r.verdict === 'warn').length,
    bytes_base: sum('bytes', 'base'), bytes_test: sum('bytes', 'test'),
    requests_base: sum('requests', 'base'), requests_test: sum('requests', 'test'),
    js_files_base: sum('js_files', 'base'), js_files_test: sum('js_files', 'test'),
  },
  pages: results,
};

mkdirSync(outDir, { recursive: true });
const stamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
const V = { ok: '✅ OK', warn: '⚠️ À surveiller', regression: '❌ Régression', error: '❌ Pas de réponse' };
const md = [
  `# Test d'impact Asset Pilot – ${new Date().toLocaleString('fr-FR')}`, '',
  `Site : ${BASE} · Référence : ${baselineMode === 'off' ? 'sans aucune règle' : 'règles en ligne'}`, '',
  `**${report.summary.regressions} régression(s)** · ${report.summary.warnings} avertissement(s) · ` +
  `poids : ${kb(report.summary.bytes_base)} → ${kb(report.summary.bytes_test)} · requêtes : ${report.summary.requests_base} → ${report.summary.requests_test}`, '',
  '| Page / parcours | Verdict | Erreurs JS | Requêtes | Poids | LCP | Détails |', '|---|---|---|---|---|---|---|',
  ...results.map((r) => `| ${r.label} | ${V[r.verdict]} | ${r.base ? `${r.base.js_errors} → ${r.test.js_errors}` : r.new_errors.length ? '+' + r.new_errors.length : '—'} | ` +
    `${r.base ? `${r.base.requests} → ${r.test.requests}` : '—'} | ${r.base ? `${kb(r.base.bytes)} → ${kb(r.test.bytes)}` : '—'} | ` +
    `${r.base ? `${r.base.lcp} → ${r.test.lcp} ms` : '—'} | ${[...r.new_errors, ...r.lost_checks.map((x) => 'élément perdu : ' + x), ...r.new_failed, ...r.notes].join('<br>').replace(/\|/g, '\\|') || '—'} |`),
].join('\n');
writeFileSync(path.join(outDir, `impact-${stamp}.md`), md);
writeFileSync(path.join(outDir, `impact-${stamp}.json`), JSON.stringify(report, null, 2));
console.log(`\nRapport : ${path.join(outDir, `impact-${stamp}.md`)}`);

if (post) {
  try {
    const res = await fetch(BASE + '/wp-json/asset-pilot/v1/reports', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Asset-Pilot-Token': TOKEN },
      body: JSON.stringify(report),
    });
    console.log(res.ok ? "Rapport envoyé à l'extension (onglet « Tester l'impact » → Historique)." : `Envoi du rapport refusé : HTTP ${res.status}`);
  } catch (e) { console.log("Envoi du rapport impossible : " + e.message); }
}

console.log(report.summary.regressions ? `\n❌ ${report.summary.regressions} régression(s) détectée(s).` : '\n✅ Aucune régression détectée.');
process.exit(report.summary.regressions ? 1 : 0);
