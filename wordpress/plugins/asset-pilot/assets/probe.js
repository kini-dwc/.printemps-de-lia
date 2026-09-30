/* Asset Pilot – sonde de mesure d'impact (injectée en tête de page avec ?ap_probe=1).
   Collecte erreurs JS, ressources en échec, poids, requêtes, Web Vitals et présence des éléments critiques,
   puis expose le résultat dans window.__apProbe et l'envoie à la fenêtre parente (interface d'admin). */
(function () {
  'use strict';
  var cfg = window.__apProbeConfig || {};
  var R = { mode: cfg.mode, rules: cfg.rules || [], js_errors: [], console_errors: [], failed: [], checks: [], done: false };
  var cut = function (s, n) { return String(s == null ? '' : s).slice(0, n || 300); };

  window.addEventListener('error', function (e) {
    var t = e.target;
    if (t && t !== window && (t.src || t.href)) {
      R.failed.push({ tag: t.tagName, url: cut(t.src || t.href) });
      return;
    }
    R.js_errors.push({ msg: cut(e.message), src: cut(e.filename, 200), line: e.lineno || 0 });
  }, true);

  window.addEventListener('unhandledrejection', function (e) {
    var r = e.reason;
    R.js_errors.push({ msg: 'Promesse rejetée : ' + cut(r && (r.message || r)), src: '', line: 0 });
  });

  var nativeError = console.error;
  console.error = function () {
    try { R.console_errors.push(cut(Array.prototype.map.call(arguments, String).join(' '))); } catch (x) {}
    return nativeError.apply(console, arguments);
  };

  var lcp = 0, cls = 0, tbt = 0;
  function observe(type, cb) {
    try { new PerformanceObserver(function (l) { l.getEntries().forEach(cb); }).observe({ type: type, buffered: true }); } catch (x) {}
  }
  observe('largest-contentful-paint', function (en) { lcp = en.startTime; });
  observe('layout-shift', function (en) { if (!en.hadRecentInput) cls += en.value; });
  observe('longtask', function (en) { tbt += Math.max(0, en.duration - 50); });

  function visible(el) { return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length); }
  // Taille compressée, identique que la ressource vienne du réseau ou du cache.
  function size(r) { return r.encodedBodySize || r.transferSize || 0; }

  function finish() {
    var res = performance.getEntriesByType('resource');
    var nav = performance.getEntriesByType('navigation')[0];
    var bytes = nav ? size(nav) : 0, js = 0, jsb = 0, css = 0, cssb = 0;
    res.forEach(function (r) {
      var u = r.name.split('?')[0], s = size(r);
      bytes += s;
      if (r.initiatorType === 'script' || /\.m?js$/.test(u)) { js++; jsb += s; }
      else if (/\.css$/.test(u)) { css++; cssb += s; }
    });
    R.requests = res.length + 1;
    R.bytes = bytes;
    R.js_files = js; R.js_bytes = jsb;
    R.css_files = css; R.css_bytes = cssb;
    R.lcp = Math.round(lcp);
    R.cls = Math.round(cls * 1000) / 1000;
    R.tbt = Math.round(tbt);
    R.load = nav ? Math.round(nav.loadEventEnd) : 0;
    R.dom = document.getElementsByTagName('*').length;
    R.jquery = typeof window.jQuery === 'function';
    R.checks = (cfg.checks || []).map(function (c) {
      var el = null;
      try { el = document.querySelector(c.selector); } catch (x) {}
      return { label: c.label, selector: c.selector, present: !!el, visible: !!(el && visible(el)) };
    });
    R.title = cut(document.title, 200);
    R.url = location.href;
    R.done = true;
    window.__apProbe = R;
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage({ source: 'asset-pilot-probe', data: R }, location.origin);
      }
    } catch (x) {}
  }

  window.addEventListener('load', function () { setTimeout(finish, 1500); });
})();
