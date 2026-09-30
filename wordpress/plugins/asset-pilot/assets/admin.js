/* Asset Pilot – interface d'administration (Outils → Asset Pilot). JavaScript sans dépendance. */
(function () {
  'use strict';
  var CFG = window.AssetPilotAdmin;
  var root = document.getElementById('asset-pilot-app');
  if (!CFG || !root) return;

  // ------------------------------------------------------------------ Utilitaires
  var S = null;                         // état renvoyé par /state
  var ui = {
    tab: sessionStorage.getItem('ap_tab') || 'assets',
    q: '', kind: '', origin: '', page: '',
    openForm: null,                     // clé de la ressource dont le formulaire est ouvert
    selected: {},                       // règles cochées
    impact: null,                       // dernier résultat de vérification
    running: false, progress: '',
    extraUrls: [],
    openGroups: {}                      // groupes dépliés dans l'onglet Ressources
  };
  var esc = function (s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };
  var kb = function (b) { return b == null || b < 0 ? '—' : (b / 1024).toFixed(1).replace('.', ',') + ' Ko'; };
  var ms = function (v) { return v ? (v / 1000).toFixed(2).replace('.', ',') + ' s' : '—'; };
  var date = function (t) { return new Date(t * 1000).toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' }); };
  var withParams = function (url, params) {
    var u = new URL(url, location.origin);
    Object.keys(params).forEach(function (k) { u.searchParams.set(k, params[k]); });
    return u.toString();
  };

  function api(path, method, body) {
    return fetch(CFG.rest + path, {
      method: method || 'GET',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': CFG.nonce },
      body: body ? JSON.stringify(body) : undefined
    }).then(function (r) {
      return r.json().then(function (j) {
        if (!r.ok) throw new Error(j && j.message ? j.message : r.statusText);
        return j;
      });
    });
  }

  function load() {
    return api('state').then(function (s) { S = s; render(); })
      .catch(function (e) { root.innerHTML = '<div class="notice notice-error"><p>' + esc(e.message) + '</p></div>'; });
  }

  function toast(msg, type) {
    var n = document.createElement('div');
    n.className = 'ap-toast ap-toast-' + (type || 'ok');
    n.textContent = msg;
    document.body.appendChild(n);
    setTimeout(function () { n.remove(); }, 3500);
  }

  function rulesFor(a) {
    return S.rules.filter(function (r) { return r.kind === a.kind && r.handle === a.handle; });
  }

  // ------------------------------------------------------------------ Rendu général
  var TABS = [
    ['assets', 'Ressources'],
    ['rules', 'Règles'],
    ['impact', "Tester l'impact"],
    ['safety', 'Mode test & sécurité']
  ];

  function render() {
    var testCount = S.rules.filter(function (r) { return r.status === 'test'; }).length;
    var liveCount = S.rules.length - testCount;
    var html = '<div class="ap-summary">' +
      '<div><strong>' + S.assets.length + '</strong><span>ressources inventoriées</span></div>' +
      '<div><strong>' + liveCount + '</strong><span>règles en ligne</span></div>' +
      '<div class="ap-sum-test"><strong>' + testCount + '</strong><span>règles en test</span></div>' +
      '<div><strong>' + (S.settings.preview ? 'Activé' : 'Désactivé') + '</strong><span>votre aperçu du mode test</span></div>' +
      '</div>';
    if (S.settings.disabled) {
      html += '<div class="notice notice-warning inline"><p>La constante <code>ASSET_PILOT_DISABLE</code> est active : aucune règle n\'est appliquée.</p></div>';
    }
    html += '<nav class="nav-tab-wrapper ap-tabs">' + TABS.map(function (t) {
      return '<a href="#" class="nav-tab' + (ui.tab === t[0] ? ' nav-tab-active' : '') + '" data-tab="' + t[0] + '">' + t[1] +
        (t[0] === 'rules' && testCount ? ' <span class="ap-count">' + testCount + '</span>' : '') + '</a>';
    }).join('') + '</nav>';
    html += '<div class="ap-panel">' + ({ assets: renderAssets, rules: renderRules, impact: renderImpact, safety: renderSafety })[ui.tab]() + '</div>';
    root.innerHTML = html;
  }

  // ------------------------------------------------------------------ Onglet Ressources
  function filteredAssets() {
    var q = ui.q.toLowerCase();
    return S.assets.filter(function (a) {
      if (ui.kind && a.kind !== ui.kind) return false;
      if (ui.origin && a.origin.label !== ui.origin) return false;
      if (ui.page && !a.pages.some(function (p) { return p.url === ui.page; })) return false;
      if (q && (a.handle + ' ' + a.src + ' ' + a.origin.label).toLowerCase().indexOf(q) === -1) return false;
      return true;
    });
  }

  function renderAssets() {
    if (!S.assets.length) {
      return '<div class="ap-empty"><h2>Aucune ressource inventoriée pour l\'instant</h2>' +
        '<p>L\'inventaire se remplit quand vous naviguez sur le site connecté en administrateur, ou en lançant un scan.</p>' +
        scanBox() + '</div>';
    }
    var origins = {}, pages = {};
    S.assets.forEach(function (a) {
      origins[a.origin.label] = 1;
      a.pages.forEach(function (p) { pages[p.url] = p.label; });
    });
    var list = filteredAssets();
    var groups = {};
    list.forEach(function (a) { (groups[a.origin.label] = groups[a.origin.label] || []).push(a); });
    var total = list.reduce(function (s, a) { return s + (a.size > 0 ? a.size : 0); }, 0);

    var html = '<div class="ap-toolbar">' +
      '<input type="search" class="ap-q" placeholder="Rechercher un handle, une extension, un fichier…" value="' + esc(ui.q) + '">' +
      '<select class="ap-f" data-f="kind"><option value="">JS et CSS</option><option value="script"' + (ui.kind === 'script' ? ' selected' : '') + '>JavaScript</option><option value="style"' + (ui.kind === 'style' ? ' selected' : '') + '>CSS</option></select>' +
      '<select class="ap-f" data-f="origin"><option value="">Toutes les origines</option>' + Object.keys(origins).sort().map(function (o) {
        return '<option' + (ui.origin === o ? ' selected' : '') + '>' + esc(o) + '</option>';
      }).join('') + '</select>' +
      '<select class="ap-f" data-f="page"><option value="">Toutes les pages</option>' + Object.keys(pages).map(function (u) {
        return '<option value="' + esc(u) + '"' + (ui.page === u ? ' selected' : '') + '>' + esc(pages[u]) + '</option>';
      }).join('') + '</select>' +
      '</div>' +
      '<p class="ap-muted">' + list.length + ' ressource(s) · ' + kb(total) + ' de fichiers locaux (non compressés). ' +
      'Les nouvelles règles sont créées <strong>en test</strong> : elles ne concernent que vous tant qu\'elles ne sont pas mises en ligne.</p>';

    var filtering = !!(ui.q || ui.kind || ui.origin || ui.page);
    Object.keys(groups).sort().forEach(function (g) {
      var items = groups[g].sort(function (a, b) { return (b.size || 0) - (a.size || 0); });
      var size = items.reduce(function (s, a) { return s + (a.size > 0 ? a.size : 0); }, 0);
      var nRules = items.reduce(function (s, a) { return s + rulesFor(a).length; }, 0);
      var open = filtering || ui.openGroups[g] || items.some(function (a) { return a.key === ui.openForm; });
      html += '<details class="ap-group"' + (open ? ' open' : '') + ' data-group="' + esc(g) + '"><summary><strong>' + esc(g) + '</strong> ' +
        '<span class="ap-muted">' + items.length + ' ressource(s) · ' + kb(size) + '</span>' +
        (nRules ? ' <span class="ap-chip ap-chip-test">' + nRules + ' règle(s)</span>' : '') + '</summary>' +
        '<table class="widefat striped ap-table"><thead><tr>' +
        '<th>Ressource</th><th>Taille</th><th>Vue sur</th><th>Dépendances</th><th>Règles</th><th></th></tr></thead><tbody>' +
        items.map(assetRow).join('') + '</tbody></table></details>';
    });
    html += scanBox();
    return html;
  }

  function assetRow(a) {
    var rules = rulesFor(a);
    var chips = rules.map(function (r) {
      return '<span class="ap-chip ap-chip-' + r.status + '" title="' + esc(r.exceptLabels.length ? 'Sauf : ' + r.exceptLabels.join(', ') : '') + '">' +
        (r.status === 'live' ? 'En ligne' : 'Test') + ' · ' + esc(r.scopeLabel) + (r.force ? ' · forcé' : '') + '</span>';
    }).join(' ');
    var pagesTitle = a.pages.map(function (p) { return p.label; }).join('\n');
    var row = '<tr' + (a.sensitive ? ' class="ap-sensitive"' : '') + '><td><span class="ap-kind ap-kind-' + a.kind + '">' + (a.kind === 'script' ? 'JS' : 'CSS') + '</span> ' +
      '<strong>' + esc(a.handle) + '</strong>' + (a.sensitive ? ' <span class="ap-chip ap-chip-warn" title="Ressource critique : la désactiver casse souvent des fonctionnalités">sensible</span>' : '') +
      '<div class="ap-src">' + esc(a.src || '(inline / virtuel)') + '</div></td>' +
      '<td>' + kb(a.size) + '</td>' +
      '<td title="' + esc(pagesTitle) + '">' + a.pages.length + ' page(s)</td>' +
      '<td class="ap-deps">' + (a.deps.length ? 'Utilise : ' + esc(a.deps.join(', ')) : '') +
      (a.requiredBy.length ? '<div class="ap-req">Requis par : ' + esc(a.requiredBy.join(', ')) + '</div>' : '') + '</td>' +
      '<td>' + (chips || '<span class="ap-muted">—</span>') + '</td>' +
      '<td class="ap-right"><button type="button" class="button" data-act="form" data-key="' + esc(a.key) + '">' + (ui.openForm === a.key ? 'Fermer' : 'Désactiver…') + '</button></td></tr>';
    if (ui.openForm === a.key) row += '<tr class="ap-form-row"><td colspan="6">' + ruleForm(a) + '</td></tr>';
    return row;
  }

  var EXCEPTIONS = ['front', 'woo:product', 'woo:archive', 'woo:cart', 'woo:checkout', 'woo:account'];

  function ruleForm(a) {
    var scopeOpts = S.scopes.map(function (s) { return '<option value="' + esc(s.token) + '">' + esc(s.label) + '</option>'; }).join('');
    var exc = S.scopes.filter(function (s) { return EXCEPTIONS.indexOf(s.token) !== -1; });
    return '<form class="ap-form" data-key="' + esc(a.key) + '">' +
      (a.sensitive ? '<p class="ap-warn">⚠ <strong>' + esc(a.handle) + '</strong> est une ressource critique. Testez soigneusement avant de mettre en ligne.</p>' : '') +
      (a.requiredBy.length ? '<p class="ap-warn">Cette ressource est utilisée par <strong>' + esc(a.requiredBy.join(', ')) + '</strong>. ' +
        'Sans « forcer », elle restera chargée là où ces ressources sont présentes.</p>' : '') +
      '<div class="ap-form-grid">' +
      '<label>Désactiver sur<select name="scope">' + scopeOpts + '</select></label>' +
      '<fieldset><legend>Sauf sur</legend>' + exc.map(function (s) {
        return '<label class="ap-inline"><input type="checkbox" name="except" value="' + esc(s.token) + '"> ' + esc(s.label) + '</label>';
      }).join('') + '</fieldset>' +
      '<label class="ap-inline"><input type="checkbox" name="force"> Forcer (retire aussi les ressources qui en dépendent' +
        (a.requiredBy.length ? ' : ' + esc(a.requiredBy.join(', ')) : '') + ')</label>' +
      '<label>Note<input type="text" name="note" placeholder="Pourquoi cette règle ? (facultatif)"></label>' +
      '</div><p><button class="button button-primary">Créer la règle en test</button> ' +
      '<span class="ap-muted">Visible par vous seul jusqu\'à sa mise en ligne.</span></p></form>';
  }

  function scanBox() {
    var urls = [S.settings.home].concat(S.urls.map(function (u) { return u.url; }));
    urls = urls.filter(function (u, i) { return urls.indexOf(u) === i; });
    return '<div class="ap-box"><h3>Scanner des pages</h3>' +
      '<p class="ap-muted">Charge chaque page comme un visiteur anonyme pour inventorier ses scripts et styles. Ajoutez au moins une page de chaque type : accueil, fiche produit, catégorie, panier, commande, article, page de contact…</p>' +
      '<textarea class="ap-scan-urls" rows="5">' + esc(urls.join('\n')) + '</textarea>' +
      '<p><button type="button" class="button button-primary" data-act="scan"' + (ui.running ? ' disabled' : '') + '>Lancer le scan</button> ' +
      '<button type="button" class="button" data-act="reset-assets">Vider l\'inventaire</button> <span class="ap-progress">' + esc(ui.progress) + '</span></p></div>';
  }

  function scan() {
    var urls = root.querySelector('.ap-scan-urls').value.split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
    ui.running = true;
    var i = 0;
    var next = function () {
      if (i >= urls.length) { ui.running = false; ui.progress = ''; toast(urls.length + ' page(s) scannée(s)'); return load(); }
      ui.progress = 'Page ' + (i + 1) + '/' + urls.length + '…';
      var p = root.querySelector('.ap-progress'); if (p) p.textContent = ui.progress;
      var url = withParams(urls[i++], { ap_scan: S.settings.token });
      return fetch(url, { credentials: 'omit', cache: 'no-store' }).catch(function () {}).then(next);
    };
    next();
  }

  // ------------------------------------------------------------------ Onglet Règles
  function renderRules() {
    if (!S.rules.length) {
      return '<div class="ap-empty"><h2>Aucune règle</h2><p>Créez des règles depuis l\'onglet « Ressources » ou depuis le panneau « Scripts » de la barre d\'administration, sur le site.</p>' + importBox() + '</div>';
    }
    var test = S.rules.filter(function (r) { return r.status === 'test'; }).length;
    var html = '<div class="ap-toolbar">' +
      '<button type="button" class="button button-primary" data-act="bulk" data-bulk="promote"' + (test ? '' : ' disabled') + '>Mettre en ligne les règles en test (' + test + ')</button>' +
      '<button type="button" class="button" data-act="bulk" data-bulk="delete_test"' + (test ? '' : ' disabled') + '>Supprimer les règles en test</button>' +
      '<span class="ap-sep"></span>' +
      '<button type="button" class="button" data-act="bulk-sel" data-bulk="promote">Sélection : mettre en ligne</button>' +
      '<button type="button" class="button" data-act="bulk-sel" data-bulk="demote">Sélection : repasser en test</button>' +
      '<button type="button" class="button" data-act="bulk-sel" data-bulk="delete">Sélection : supprimer</button>' +
      '</div>' +
      '<p class="ap-muted">Avant de mettre en ligne, vérifiez l\'effet avec l\'onglet « Tester l\'impact ». Le cache de page connu (WP Rocket, LiteSpeed, W3TC…) est vidé à chaque changement.</p>' +
      '<table class="widefat striped ap-table"><thead><tr><th><input type="checkbox" data-act="sel-all"></th><th>Ressource</th><th>Portée</th><th>Exceptions</th><th>Statut</th><th>Note</th><th>Créée</th><th></th></tr></thead><tbody>' +
      S.rules.map(function (r) {
        return '<tr><td><input type="checkbox" data-act="sel" data-id="' + r.id + '"' + (ui.selected[r.id] ? ' checked' : '') + '></td>' +
          '<td><span class="ap-kind ap-kind-' + r.kind + '">' + (r.kind === 'script' ? 'JS' : 'CSS') + '</span> <strong>' + esc(r.handle) + '</strong>' + (r.force ? ' <span class="ap-chip ap-chip-warn">forcé</span>' : '') + '</td>' +
          '<td>' + esc(r.scopeLabel) + '</td>' +
          '<td>' + (r.exceptLabels.length ? esc(r.exceptLabels.join(', ')) : '<span class="ap-muted">—</span>') + '</td>' +
          '<td><span class="ap-chip ap-chip-' + r.status + '">' + (r.status === 'live' ? 'En ligne' : 'Test') + '</span></td>' +
          '<td>' + esc(r.note) + '</td><td>' + date(r.created) + '</td>' +
          '<td class="ap-right">' +
          (r.status === 'test'
            ? '<button type="button" class="button button-small" data-act="status" data-id="' + r.id + '" data-status="live">Mettre en ligne</button> '
            : '<button type="button" class="button button-small" data-act="status" data-id="' + r.id + '" data-status="test">Repasser en test</button> ') +
          '<button type="button" class="button button-small ap-danger" data-act="delete" data-id="' + r.id + '">Supprimer</button></td></tr>';
      }).join('') + '</tbody></table>' + importBox();
    return html;
  }

  function importBox() {
    return '<div class="ap-box"><h3>Exporter / importer</h3>' +
      '<p class="ap-muted">Pour réutiliser des règles sur un autre site. Les règles importées arrivent toujours <strong>en test</strong>.</p>' +
      '<p><button type="button" class="button" data-act="export"' + (S.rules.length ? '' : ' disabled') + '>Exporter les règles (JSON)</button> ' +
      '<label class="button">Importer un fichier JSON<input type="file" accept="application/json,.json" class="ap-import" hidden></label></p></div>';
  }

  // ------------------------------------------------------------------ Onglet Impact (V2)
  function impactUrls() {
    var urls = [{ url: S.settings.home, label: "Page d'accueil" }].concat(S.urls);
    ui.extraUrls.forEach(function (u) { urls.push({ url: u, label: u }); });
    var seen = {};
    return urls.filter(function (u) { if (seen[u.url]) return false; seen[u.url] = 1; return true; });
  }

  function renderImpact() {
    var test = S.rules.filter(function (r) { return r.status === 'test'; }).length;
    var html = '<div class="ap-box ap-intro"><h3>Mesurer l\'effet des règles en test</h3>' +
      '<p>Chaque page est chargée deux fois dans votre navigateur : <strong>sans aucune règle</strong> (référence), puis <strong>avec les règles en ligne et en test</strong>. ' +
      'La comparaison porte sur les erreurs JavaScript, les ressources en échec, les éléments critiques, le poids, le nombre de requêtes et le temps d\'affichage.</p>' +
      (test ? '' : '<p class="ap-warn">Aucune règle en test pour l\'instant : la comparaison portera uniquement sur les règles en ligne.</p>') +
      '<p class="ap-muted">Pour tester aussi les parcours (ajout au panier, commande), utilisez l\'outil en ligne de commande fourni (voir le README). Ses rapports apparaissent dans l\'historique ci-dessous.</p></div>';

    html += '<div class="ap-cols"><div class="ap-box"><h3>Pages à vérifier</h3>' + impactUrls().map(function (u, i) {
      return '<label class="ap-line"><input type="checkbox" class="ap-iu" value="' + esc(u.url) + '"' + (i < 8 ? ' checked' : '') + '> ' + esc(u.label) + ' <span class="ap-src">' + esc(u.url) + '</span></label>';
    }).join('') +
      '<p class="ap-add"><input type="url" class="ap-add-url" placeholder="https://… autre page"> <button type="button" class="button" data-act="add-url">Ajouter</button></p></div>' +
      '<div class="ap-box"><h3>Éléments critiques</h3><p class="ap-muted">Sélecteurs CSS dont la présence est vérifiée sur chaque page (s\'ils existent sans les règles, ils doivent exister avec).</p>' +
      '<div class="ap-checks">' + S.settings.checks.map(checkRow).join('') + '</div>' +
      '<p><button type="button" class="button" data-act="check-add">Ajouter</button> <button type="button" class="button" data-act="checks-save">Enregistrer</button></p></div></div>';

    html += '<p><button type="button" class="button button-primary button-hero" data-act="run"' + (ui.running ? ' disabled' : '') + '>Lancer la vérification</button> <span class="ap-progress">' + esc(ui.progress) + '</span></p>' +
      '<div class="ap-stage" hidden></div>';
    if (ui.impact) html += renderImpactResult(ui.impact);
    html += renderHistory();
    return html;
  }

  function checkRow(c) {
    return '<div class="ap-check"><input type="text" class="ap-c-sel" value="' + esc(c.selector) + '" placeholder=".mon-bouton"> ' +
      '<input type="text" class="ap-c-label" value="' + esc(c.label) + '" placeholder="Libellé"> <button type="button" class="button-link ap-danger" data-act="check-del">Retirer</button></div>';
  }

  /** Charge une URL dans un iframe caché et attend le rapport de la sonde. */
  function probe(url) {
    return new Promise(function (resolve) {
      var frame = document.createElement('iframe');
      frame.className = 'ap-probe-frame';
      frame.setAttribute('aria-hidden', 'true');
      var done = false;
      var finish = function (data) {
        if (done) return; done = true;
        window.removeEventListener('message', onMsg);
        clearTimeout(timer);
        frame.remove();
        resolve(data);
      };
      var onMsg = function (e) {
        if (e.source === frame.contentWindow && e.data && e.data.source === 'asset-pilot-probe') finish(e.data.data);
      };
      var timer = setTimeout(function () { finish(null); }, 60000);
      window.addEventListener('message', onMsg);
      frame.src = url;
      // Iframe visible (vignette réduite) : Chrome ralentit les iframes cachées, ce qui fausserait les mesures.
      var stage = root.querySelector('.ap-stage');
      if (stage) { stage.hidden = false; stage.innerHTML = ''; stage.appendChild(frame); }
      else document.body.appendChild(frame);
    });
  }

  function compare(url, label, base, test) {
    var p = { url: url, label: label, verdict: 'ok', base: null, test: null, new_errors: [], new_failed: [], lost_checks: [], notes: [] };
    if (!base || !test) {
      p.verdict = 'error';
      p.notes.push(!base ? 'La page de référence n\'a pas répondu' : 'La page avec règles n\'a pas répondu');
      return p;
    }
    var metrics = function (r) {
      return {
        js_errors: r.js_errors.length, console_errors: r.console_errors.length, failed: r.failed.length,
        requests: r.requests, bytes: r.bytes, js_files: r.js_files, js_bytes: r.js_bytes, css_files: r.css_files,
        lcp: r.lcp, tbt: r.tbt, cls: r.cls, dom: r.dom,
        checks_failed: r.checks.filter(function (c) { return !c.visible; }).length
      };
    };
    p.base = metrics(base);
    p.test = metrics(test);
    var baseMsgs = base.js_errors.map(function (e) { return e.msg; });
    p.new_errors = test.js_errors.filter(function (e) { return baseMsgs.indexOf(e.msg) === -1; }).map(function (e) { return e.msg + (e.src ? ' (' + e.src.split('/').pop() + ':' + e.line + ')' : ''); });
    var baseFailed = base.failed.map(function (f) { return f.url; });
    p.new_failed = test.failed.filter(function (f) { return baseFailed.indexOf(f.url) === -1; }).map(function (f) { return f.url; });
    base.checks.forEach(function (c, i) {
      var t = test.checks[i];
      if (c.visible && t && !t.visible) p.lost_checks.push(c.label);
    });
    var baseConsole = base.console_errors;
    var newConsole = test.console_errors.filter(function (m) { return baseConsole.indexOf(m) === -1; });
    if (p.new_errors.length || p.lost_checks.length || p.new_failed.length) p.verdict = 'regression';
    else if (newConsole.length) { p.verdict = 'warn'; p.notes = p.notes.concat(newConsole.slice(0, 5).map(function (m) { return 'console : ' + m; })); }
    if (base.jquery && !test.jquery) { p.verdict = 'regression'; p.notes.push('jQuery n\'est plus disponible'); }
    return p;
  }

  function runImpact() {
    var boxes = root.querySelectorAll('.ap-iu:checked');
    var pages = Array.prototype.map.call(boxes, function (b) {
      var u = impactUrls().filter(function (x) { return x.url === b.value; })[0];
      return { url: b.value, label: u ? u.label : b.value };
    });
    if (!pages.length) return toast('Cochez au moins une page', 'err');
    ui.running = true;
    var results = [];
    var i = 0;
    var setProgress = function (t) { ui.progress = t; var p = root.querySelector('.ap-progress'); if (p) p.textContent = t; };
    var next = function () {
      if (i >= pages.length) return done();
      var pg = pages[i++];
      setProgress('Page ' + i + '/' + pages.length + ' : référence…');
      return probe(withParams(pg.url, { ap_off: 1, ap_probe: 1 })).then(function (base) {
        setProgress('Page ' + i + '/' + pages.length + ' : avec les règles…');
        return probe(withParams(pg.url, { ap_test: S.settings.token, ap_probe: 1 })).then(function (test) {
          results.push(compare(pg.url, pg.label, base, test));
        });
      }).then(next);
    };
    var done = function () {
      var sum = function (k, side) { return results.reduce(function (s, r) { return s + (r[side] ? r[side][k] || 0 : 0); }, 0); };
      var report = {
        source: 'interface',
        rules: S.rules.map(function (r) { return r.id; }),
        summary: {
          pages: results.length,
          regressions: results.filter(function (r) { return r.verdict === 'regression' || r.verdict === 'error'; }).length,
          warnings: results.filter(function (r) { return r.verdict === 'warn'; }).length,
          bytes_base: sum('bytes', 'base'), bytes_test: sum('bytes', 'test'),
          requests_base: sum('requests', 'base'), requests_test: sum('requests', 'test'),
          js_files_base: sum('js_files', 'base'), js_files_test: sum('js_files', 'test'),
          rules_live: S.rules.filter(function (r) { return r.status === 'live'; }).length,
          rules_test: S.rules.filter(function (r) { return r.status === 'test'; }).length
        },
        pages: results
      };
      ui.impact = report;
      ui.running = false;
      ui.progress = '';
      api('reports', 'POST', report).then(function (saved) {
        S.reports.unshift(saved);
        render();
        toast(report.summary.regressions ? 'Vérification terminée : ' + report.summary.regressions + ' page(s) en régression' : 'Vérification terminée : aucune régression détectée', report.summary.regressions ? 'err' : 'ok');
      });
    };
    render();
    next();
  }

  var VERDICT = { ok: ['OK', 'ok'], warn: ['À surveiller', 'warn'], regression: ['Régression', 'bad'], error: ['Pas de réponse', 'bad'] };

  function delta(b, t, fmt, lowerIsBetter) {
    if (b == null || t == null) return '—';
    var d = t - b;
    var cls = d === 0 ? '' : ((d < 0) === (lowerIsBetter !== false) ? 'ap-good' : 'ap-bad');
    return fmt(b) + ' → <strong>' + fmt(t) + '</strong>' + (d ? ' <span class="' + cls + '">(' + (d > 0 ? '+' : '−') + fmt(Math.abs(d)) + ')</span>' : '');
  }
  var n = function (v) { return String(v); };

  function renderImpactResult(rep) {
    var s = rep.summary;
    var html = '<div class="ap-box"><h3>Résultat de la vérification</h3><div class="ap-summary ap-summary-sm">' +
      '<div class="' + (s.regressions ? 'ap-bad-bg' : 'ap-good-bg') + '"><strong>' + s.regressions + '</strong><span>page(s) en régression</span></div>' +
      '<div><strong>' + kb(s.bytes_base - s.bytes_test) + '</strong><span>économisés au total</span></div>' +
      '<div><strong>' + (s.requests_base - s.requests_test) + '</strong><span>requêtes en moins</span></div>' +
      '<div><strong>' + (s.js_files_base - s.js_files_test) + '</strong><span>fichiers JS en moins</span></div></div>' +
      '<table class="widefat striped ap-table"><thead><tr><th>Page</th><th>Verdict</th><th>Erreurs JS</th><th>Requêtes</th><th>Poids</th><th>Fichiers JS</th><th>Affichage (LCP)</th><th>Éléments critiques perdus</th></tr></thead><tbody>' +
      rep.pages.map(function (p) {
        var v = VERDICT[p.verdict] || VERDICT.error;
        var details = p.new_errors.map(function (e) { return '<li>Nouvelle erreur JS : ' + esc(e) + '</li>'; }).join('') +
          p.new_failed.map(function (e) { return '<li>Ressource en échec : ' + esc(e) + '</li>'; }).join('') +
          p.lost_checks.map(function (e) { return '<li>Élément disparu : ' + esc(e) + '</li>'; }).join('') +
          p.notes.map(function (e) { return '<li>' + esc(e) + '</li>'; }).join('');
        var title = p.url ? '<a href="' + esc(p.url) + '" target="_blank" rel="noopener">' + esc(p.label) + '</a>' : '<strong>' + esc(p.label) + '</strong>';
        return '<tr><td>' + title + (details ? '<ul class="ap-details">' + details + '</ul>' : '') + '</td>' +
          '<td><span class="ap-verdict ap-verdict-' + v[1] + '">' + v[0] + '</span></td>' +
          (p.base ? '<td>' + delta(p.base.js_errors, p.test.js_errors, n) + '</td><td>' + delta(p.base.requests, p.test.requests, n) + '</td><td>' + delta(p.base.bytes, p.test.bytes, kb) + '</td><td>' +
            delta(p.base.js_files, p.test.js_files, n) + '</td><td>' + delta(p.base.lcp, p.test.lcp, ms) + '</td><td>' +
            (p.lost_checks.length ? '<span class="ap-bad">' + p.lost_checks.length + '</span>' : '<span class="ap-good">0</span>') + '</td>'
            : '<td colspan="6">—</td>') + '</tr>';
      }).join('') + '</tbody></table>' +
      '<p class="ap-muted">Poids = taille compressée des ressources. Les temps d\'affichage varient d\'un chargement à l\'autre : fiez-vous d\'abord aux erreurs, au poids et au nombre de requêtes.</p></div>';
    return html;
  }

  function renderHistory() {
    if (!S.reports.length) return '';
    return '<div class="ap-box"><h3>Historique des vérifications</h3><table class="widefat striped ap-table"><thead><tr><th>Date</th><th>Source</th><th>Pages</th><th>Régressions</th><th>Poids économisé</th><th>Requêtes en moins</th><th>Règles (en ligne / test)</th><th></th></tr></thead><tbody>' +
      S.reports.map(function (r, i) {
        var s = r.summary || {};
        return '<tr><td>' + date(r.date) + '</td><td>' + esc(r.source) + '</td><td>' + (s.pages || 0) + '</td>' +
          '<td><span class="ap-verdict ap-verdict-' + (s.regressions ? 'bad' : 'ok') + '">' + (s.regressions || 0) + '</span></td>' +
          '<td>' + kb((s.bytes_base || 0) - (s.bytes_test || 0)) + '</td><td>' + ((s.requests_base || 0) - (s.requests_test || 0)) + '</td>' +
          '<td>' + (s.rules_live || 0) + ' / ' + (s.rules_test || 0) + '</td>' +
          '<td class="ap-right"><button type="button" class="button button-small" data-act="show-report" data-i="' + i + '">Détails</button></td></tr>';
      }).join('') + '</tbody></table><p><button type="button" class="button-link ap-danger" data-act="clear-reports">Vider l\'historique</button></p></div>';
  }

  // ------------------------------------------------------------------ Onglet Mode test & sécurité
  function renderSafety() {
    var st = S.settings;
    return '<div class="ap-box"><h3>Votre aperçu du mode test</h3>' +
      '<p>Quand l\'aperçu est activé, <strong>vous seul</strong> voyez le site avec les règles en test (tant que vous êtes connecté). Les visiteurs voient uniquement les règles en ligne.</p>' +
      '<p><label class="ap-switch"><input type="checkbox" data-act="preview"' + (st.preview ? ' checked' : '') + '> Voir le site avec les règles en test</label></p></div>' +

      '<div class="ap-box"><h3>Lien de test (sans connexion)</h3>' +
      '<p>Pour tester sur un autre appareil ou un autre navigateur, comme un visiteur anonyme. Le lien active le mode test pour la durée de la session du navigateur ; ajoutez <code>?ap_test=0</code> pour l\'arrêter.</p>' +
      '<p><input type="text" readonly class="large-text code ap-copy" value="' + esc(st.testUrl) + '"></p>' +
      '<p><button type="button" class="button" data-act="copy">Copier le lien</button> ' +
      '<button type="button" class="button" data-act="regen">Générer un nouveau jeton</button> <span class="ap-muted">(l\'ancien lien cessera de fonctionner)</span></p></div>' +

      '<div class="ap-box ap-danger-box"><h3>Si quelque chose casse</h3><ol>' +
      '<li>Connecté en administrateur, ajoutez <code>?ap_off=1</code> à l\'adresse d\'une page pour la voir <strong>sans aucune règle</strong> : <a href="' + esc(st.offUrl) + '" target="_blank" rel="noopener">' + esc(st.offUrl) + '</a></li>' +
      '<li>Repassez les règles en test (onglet Règles) : les visiteurs retrouvent immédiatement le site d\'origine.</li>' +
      '<li>En dernier recours, ajoutez dans <code>wp-config.php</code> : <code>define( \'ASSET_PILOT_DISABLE\', true );</code> (suspend toutes les règles, même sans accès à l\'administration).</li>' +
      '</ol></div>' +

      '<div class="ap-box"><h3>Ce qui n\'est jamais modifié</h3><p class="ap-muted">L\'administration, les requêtes AJAX et REST, l\'outil de personnalisation, les éditeurs visuels (Elementor, Beaver Builder, Divi, Oxygen, Bricks) et les flux RSS. ' +
      'Les pages en mode test ne sont ni mises en cache ni servies depuis le cache.</p></div>';
  }

  // ------------------------------------------------------------------ Événements
  root.addEventListener('click', function (e) {
    var t = e.target.closest('[data-tab],[data-act]');
    if (!t) return;
    if (t.hasAttribute('data-tab')) {
      e.preventDefault();
      ui.tab = t.getAttribute('data-tab');
      sessionStorage.setItem('ap_tab', ui.tab);
      return render();
    }
    var act = t.getAttribute('data-act');
    var id = t.getAttribute('data-id');
    switch (act) {
      case 'form':
        ui.openForm = ui.openForm === t.getAttribute('data-key') ? null : t.getAttribute('data-key');
        return render();
      case 'scan': return scan();
      case 'reset-assets':
        if (!confirm('Vider l\'inventaire des ressources ? Les règles sont conservées.')) return;
        return api('assets', 'DELETE').then(load);
      case 'status':
        return api('rules/' + id, 'PATCH', { status: t.getAttribute('data-status') }).then(function () { toast('Règle mise à jour'); load(); }, function (err) { toast(err.message, 'err'); });
      case 'delete':
        if (!confirm('Supprimer cette règle ?')) return;
        return api('rules/' + id, 'DELETE').then(function () { toast('Règle supprimée'); load(); });
      case 'bulk':
        var b = t.getAttribute('data-bulk');
        if (b === 'promote' && !confirm('Mettre en ligne toutes les règles en test ? Elles s\'appliqueront à tous les visiteurs.')) return;
        if (b === 'delete_test' && !confirm('Supprimer toutes les règles en test ?')) return;
        return api('bulk', 'POST', { action: b }).then(function () { toast('Fait'); load(); });
      case 'bulk-sel':
        var ids = Object.keys(ui.selected).filter(function (k) { return ui.selected[k]; });
        if (!ids.length) return toast('Sélectionnez au moins une règle', 'err');
        return api('bulk', 'POST', { action: t.getAttribute('data-bulk'), ids: ids }).then(function () { ui.selected = {}; toast('Fait'); load(); });
      case 'sel': ui.selected[id] = t.checked; return;
      case 'sel-all':
        S.rules.forEach(function (r) { ui.selected[r.id] = t.checked; });
        return render();
      case 'export':
        var blob = new Blob([JSON.stringify({ plugin: 'asset-pilot', version: 1, rules: S.rules }, null, 2)], { type: 'application/json' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'asset-pilot-regles-' + new Date().toISOString().slice(0, 10) + '.json';
        a.click();
        return;
      case 'preview':
        return api('settings', 'POST', { preview: t.checked }).then(function (s) { S = s; render(); toast(t.checked ? 'Aperçu du mode test activé' : 'Aperçu du mode test désactivé'); });
      case 'copy':
        var input = root.querySelector('.ap-copy');
        input.select();
        (navigator.clipboard ? navigator.clipboard.writeText(input.value) : Promise.resolve(document.execCommand('copy'))).then(function () { toast('Lien copié'); });
        return;
      case 'regen':
        if (!confirm('Générer un nouveau jeton ? L\'ancien lien de test cessera de fonctionner.')) return;
        return api('settings', 'POST', { regenerateToken: true }).then(function (s) { S = s; render(); toast('Nouveau jeton généré'); });
      case 'add-url':
        var v = root.querySelector('.ap-add-url').value.trim();
        if (v) { ui.extraUrls.push(v); render(); }
        return;
      case 'check-add':
        root.querySelector('.ap-checks').insertAdjacentHTML('beforeend', checkRow({ selector: '', label: '' }));
        return;
      case 'check-del':
        t.closest('.ap-check').remove();
        return;
      case 'checks-save':
        var checks = Array.prototype.map.call(root.querySelectorAll('.ap-check'), function (row) {
          return { selector: row.querySelector('.ap-c-sel').value, label: row.querySelector('.ap-c-label').value };
        });
        return api('settings', 'POST', { checks: checks }).then(function (s) { S = s; render(); toast('Éléments critiques enregistrés'); });
      case 'run': return runImpact();
      case 'show-report':
        ui.impact = S.reports[+t.getAttribute('data-i')];
        render();
        var box = root.querySelectorAll('.ap-box');
        if (box.length) box[box.length - 2].scrollIntoView({ behavior: 'smooth' });
        return;
      case 'clear-reports':
        if (!confirm('Vider l\'historique des vérifications ?')) return;
        return api('reports', 'DELETE').then(function () { ui.impact = null; load(); });
    }
  });

  root.addEventListener('submit', function (e) {
    var f = e.target.closest('.ap-form');
    if (!f) return;
    e.preventDefault();
    var key = f.getAttribute('data-key');
    var asset = S.assets.filter(function (a) { return a.key === key; })[0];
    var body = {
      kind: asset.kind, handle: asset.handle, status: 'test',
      scope: f.scope.value,
      except: Array.prototype.map.call(f.querySelectorAll('[name="except"]:checked'), function (c) { return c.value; }),
      force: f.force.checked,
      note: f.note.value
    };
    api('rules', 'POST', body).then(function () {
      ui.openForm = null;
      toast('Règle créée en test');
      load();
    }, function (err) { toast(err.message, 'err'); });
  });

  root.addEventListener('input', function (e) {
    if (e.target.matches('.ap-q')) {
      ui.q = e.target.value;
      var pos = e.target.selectionStart;
      render();
      var q = root.querySelector('.ap-q');
      q.focus(); q.setSelectionRange(pos, pos);
    }
  });

  root.addEventListener('toggle', function (e) {
    if (e.target.matches && e.target.matches('details.ap-group')) ui.openGroups[e.target.getAttribute('data-group')] = e.target.open;
  }, true);

  root.addEventListener('change', function (e) {
    if (e.target.matches('.ap-f')) { ui[e.target.getAttribute('data-f')] = e.target.value; render(); }
    if (e.target.matches('.ap-import')) {
      var file = e.target.files[0];
      if (!file) return;
      file.text().then(function (txt) {
        var data;
        try { data = JSON.parse(txt); } catch (x) { return toast('Fichier JSON invalide', 'err'); }
        return api('import', 'POST', { rules: data.rules || data }).then(function (r) { toast(r.imported + ' règle(s) importée(s) en test'); load(); });
      });
    }
  });

  load();
})();
