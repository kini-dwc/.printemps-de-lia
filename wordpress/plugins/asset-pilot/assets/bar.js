/* Asset Pilot – panneau « Scripts de cette page » dans la barre d'administration. */
// Les données de la page sont imprimées tout en fin de <body>, après ce script : on attend le DOM complet.
document.addEventListener('DOMContentLoaded', function () {
  'use strict';
  var D = window.AssetPilotBar;
  var node = document.getElementById('wp-admin-bar-asset-pilot');
  if (!D || !node) return;

  var esc = function (s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };
  var kb = function (b) { return b < 0 ? '?' : (b / 1024).toFixed(1).replace('.', ',') + ' Ko'; };

  function api(path, method, body) {
    return fetch(D.rest + path, {
      method: method || 'GET',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': D.nonce },
      body: body ? JSON.stringify(body) : undefined
    }).then(function (r) {
      return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || r.statusText); return j; });
    });
  }

  // « Requis par » calculé sur les ressources de la page
  var requiredBy = {};
  D.items.forEach(function (it) {
    it.deps.forEach(function (d) { (requiredBy[it.kind + ':' + d] = requiredBy[it.kind + ':' + d] || []).push(it.handle); });
  });

  function ruleFor(it) {
    return D.rules.filter(function (r) { return r.kind === it.kind && r.handle === it.handle; });
  }

  function itemHtml(it) {
    var rules = ruleFor(it);
    var deps = requiredBy[it.kind + ':' + it.handle] || [];
    var state = it.removed ? '<span class="ap-b ap-b-off">retiré</span>'
      : it.kept ? '<span class="ap-b ap-b-warn" title="Retiré par une règle mais réimprimé car une autre ressource en dépend">réintroduit</span>'
      : '<span class="ap-b ap-b-on">chargé</span>';
    var ruleBadges = rules.map(function (r) {
      return '<span class="ap-b ' + (r.status === 'live' ? 'ap-b-live' : 'ap-b-test') + '">' + (r.status === 'live' ? 'en ligne' : 'test') + '</span>';
    }).join('');
    var actions;
    if (rules.length) {
      actions = '<button type="button" class="ap-btn" data-ap="enable" data-ids="' + esc(rules.map(function (r) { return r.id; }).join(',')) + '">Réactiver</button>';
    } else {
      actions = '<select class="ap-scope">' + D.scopes.map(function (s) {
        return '<option value="' + esc(s.token) + '">' + esc(s.label) + '</option>';
      }).join('') + '</select><button type="button" class="ap-btn ap-btn-danger" data-ap="disable" data-kind="' + esc(it.kind) + '" data-handle="' + esc(it.handle) + '">Désactiver</button>';
    }
    return '<li class="ap-item' + (it.removed ? ' is-removed' : '') + '" data-search="' + esc((it.handle + ' ' + it.origin.label + ' ' + it.src).toLowerCase()) + '">' +
      '<div class="ap-row1"><span class="ap-kind ap-kind-' + it.kind + '">' + (it.kind === 'script' ? 'JS' : 'CSS') + '</span>' +
      '<strong class="ap-handle" title="' + esc(it.src) + '">' + esc(it.handle) + '</strong>' + state + ruleBadges +
      (it.sensitive ? '<span class="ap-b ap-b-warn" title="Ressource critique : la désactiver casse souvent des fonctionnalités">sensible</span>' : '') +
      '<span class="ap-size">' + kb(it.size) + '</span></div>' +
      (deps.length ? '<div class="ap-deps">Requis par : ' + esc(deps.join(', ')) + '</div>' : '') +
      '<div class="ap-actions">' + actions + '</div></li>';
  }

  function render() {
    var groups = {};
    D.items.forEach(function (it) { (groups[it.origin.label] = groups[it.origin.label] || []).push(it); });
    var names = Object.keys(groups).sort();
    var total = D.items.filter(function (i) { return !i.removed; }).length;
    var removed = D.items.filter(function (i) { return i.removed; }).length;
    var modeLabel = { test: 'Mode test', live: 'Règles en ligne', off: 'Règles suspendues' }[D.mode];
    return '<div class="ap-head"><div><strong>Scripts de cette page</strong><div class="ap-sub">' + esc(D.label) + '</div></div>' +
      '<button type="button" class="ap-close" data-ap="close" aria-label="Fermer">×</button></div>' +
      '<div class="ap-bar"><span class="ap-b ap-b-mode">' + modeLabel + '</span> ' + total + ' chargés · ' + removed + ' retirés</div>' +
      '<label class="ap-preview"><input type="checkbox" data-ap="preview"' + (D.preview ? ' checked' : '') + '> Voir le site avec les règles en test (visible par vous seul)</label>' +
      '<input type="search" class="ap-filter" placeholder="Filtrer (handle, extension, fichier)…">' +
      '<div class="ap-list">' + names.map(function (n) {
        return '<div class="ap-group"><h4>' + esc(n) + ' <span>(' + groups[n].length + ')</span></h4><ul>' + groups[n].map(itemHtml).join('') + '</ul></div>';
      }).join('') + '</div>' +
      '<div class="ap-foot"><a href="' + esc(D.adminUrl) + '">Ouvrir le gestionnaire complet →</a></div>';
  }

  var panel = document.createElement('div');
  panel.id = 'ap-panel';
  panel.setAttribute('role', 'dialog');
  panel.setAttribute('aria-label', 'Scripts de cette page');
  panel.hidden = true;
  document.body.appendChild(panel);

  function open() { panel.innerHTML = render(); panel.hidden = false; }
  function close() { panel.hidden = true; }

  node.querySelector('a').addEventListener('click', function (e) {
    e.preventDefault();
    panel.hidden ? open() : close();
  });

  function reloadInTest() {
    // Après un changement, on active l'aperçu pour voir immédiatement l'effet (visible par l'admin seul).
    var go = function () { location.reload(); };
    if (!D.preview) api('settings', 'POST', { preview: true }).then(go, go); else go();
  }

  panel.addEventListener('click', function (e) {
    var t = e.target.closest('[data-ap]');
    if (!t) return;
    var a = t.getAttribute('data-ap');
    if (a === 'close') return close();
    if (a === 'disable') {
      var scope = t.parentNode.querySelector('.ap-scope').value;
      t.disabled = true; t.textContent = '…';
      api('rules', 'POST', { kind: t.getAttribute('data-kind'), handle: t.getAttribute('data-handle'), scope: scope, status: 'test' })
        .then(reloadInTest, function (err) { alert(err.message); t.disabled = false; t.textContent = 'Désactiver'; });
    }
    if (a === 'enable') {
      t.disabled = true; t.textContent = '…';
      Promise.all(t.getAttribute('data-ids').split(',').map(function (id) { return api('rules/' + id, 'DELETE'); }))
        .then(function () { location.reload(); }, function (err) { alert(err.message); });
    }
  });

  panel.addEventListener('change', function (e) {
    if (e.target.matches('[data-ap="preview"]')) {
      api('settings', 'POST', { preview: e.target.checked }).then(function () { location.reload(); });
    }
  });

  panel.addEventListener('input', function (e) {
    if (!e.target.matches('.ap-filter')) return;
    var q = e.target.value.toLowerCase().trim();
    panel.querySelectorAll('.ap-item').forEach(function (li) {
      li.hidden = q && li.getAttribute('data-search').indexOf(q) === -1;
    });
    panel.querySelectorAll('.ap-group').forEach(function (g) {
      g.hidden = !g.querySelector('.ap-item:not([hidden])');
    });
  });

  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !panel.hidden) close(); });
});
