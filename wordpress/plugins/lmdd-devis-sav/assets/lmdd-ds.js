/* LMDD – Devis & SAV : panneau de devis, formulaire SAV en étapes, envoi. Sans dépendance. */
(function () {
  'use strict';
  var CFG = window.LMDD_DS || {};
  var started = Date.now();

  function $(sel, root) { return (root || document).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
  function clean(t) { return (t || '').replace(/\s+/g, ' ').replace(/\s*\(\s*\+?\s*0[,.]?0*\s*€\s*\)/, '').trim(); }

  // ------------------------------------------------------------------ Turnstile (chargé seulement si besoin)
  var tsLoading = null;
  function turnstile(form) {
    var box = $('.lmdd-turnstile', form);
    if (!CFG.turnstile || !box || box.dataset.done) return;
    box.dataset.done = '1';
    if (!tsLoading) {
      tsLoading = new Promise(function (ok) {
        window.lmddTsReady = ok;
        var s = document.createElement('script');
        s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=lmddTsReady&render=explicit';
        s.async = true; document.head.appendChild(s);
      });
    }
    tsLoading.then(function () { window.turnstile.render(box, { sitekey: CFG.turnstile, language: 'fr', size: 'flexible' }); });
  }

  // ------------------------------------------------------------------ Configuration choisie sur la fiche (TM Extra Product Options, variations)
  function readConfig() {
    var out = [];
    $$('form.cart .tc-container, form.cart .tm-extra-product-options-field').forEach(function (c) {
      var label = $('.tc-epo-element-label-text, .tm-epo-element-label', c);
      var name = label ? clean(label.textContent) : '';
      var val = '';
      var sel = $('select', c);
      if (sel && sel.selectedIndex >= 0 && sel.value !== '') val = clean(sel.options[sel.selectedIndex].text);
      var checked = $$('input[type=radio]:checked, input[type=checkbox]:checked', c).map(function (i) {
        var l = i.closest('label') || (i.id && $('label[for="' + i.id + '"]'));
        return clean(l ? l.textContent : i.value);
      });
      if (!val && checked.length) val = checked.join(', ');
      var txt = $('input[type=text], input[type=number], textarea', c);
      if (!val && txt && txt.value) val = clean(txt.value);
      if (name && val && !/^(choisir|choose|select)/i.test(val)) out.push([name, val]);
    });
    if (!out.length) {
      $$('form.cart table.variations tr, form.variations_form .variations tr').forEach(function (r) {
        var l = $('label', r), s = $('select', r);
        if (l && s && s.value) out.push([clean(l.textContent).replace(/:$/, ''), clean(s.options[s.selectedIndex].text)]);
      });
    }
    var qty = $('form.cart input.qty');
    if (qty && +qty.value > 1) out.push(['Quantité', qty.value]);
    return out;
  }

  function fillConfig(dlg) {
    var box = $('[data-lmdd-config]', dlg); if (!box) return;
    var list = $('.lmdd-config__list', box), items = readConfig();
    list.innerHTML = '';
    items.forEach(function (it) {
      var li = document.createElement('li');
      var k = document.createElement('span'); k.textContent = it[0];
      var v = document.createElement('strong'); v.textContent = it[1];
      li.appendChild(k); li.appendChild(v); list.appendChild(li);
    });
    box.classList.toggle('is-empty', !items.length);
    $('textarea[name=configuration]', dlg).value = items.map(function (it) { return it[0] + ' : ' + it[1]; }).join('\n');
  }

  // ------------------------------------------------------------------ Panneau de devis
  function openDevis() {
    var dlg = $('#lmdd-devis'); if (!dlg) return false;
    fillConfig(dlg);
    if (typeof dlg.showModal === 'function') dlg.showModal(); else dlg.setAttribute('open', '');
    document.documentElement.classList.add('lmdd-noscroll');
    turnstile($('form', dlg));
    var first = $('.lmdd-success:not([hidden])', dlg) || $('input:not([type=hidden]):not([tabindex="-1"]), select', $('.lmdd-dlg__body', dlg));
    if (first && window.matchMedia('(min-width: 768px)').matches) first.focus();
    return true;
  }
  function closeDlg(dlg) {
    if (dlg.open && typeof dlg.close === 'function') dlg.close(); else dlg.removeAttribute('open');
  }

  document.addEventListener('click', function (e) {
    var t = e.target;
    // Ancien bouton du thème (#trigger-quote-btn) : il ouvre désormais le panneau au lieu de changer de page.
    var legacy = t.closest && t.closest('#trigger-quote-btn, .btn-devis-custom');
    var opener = t.closest && t.closest('[data-lmdd-open="devis"]');
    if ((legacy || opener) && $('#lmdd-devis')) {
      e.preventDefault(); e.stopImmediatePropagation();
      openDevis(); return;
    }
    var closer = t.closest && t.closest('[data-lmdd-close]');
    if (closer) {
      var dlg = closer.closest('dialog'); if (dlg) closeDlg(dlg);
      if (closer.hasAttribute('data-lmdd-goto-options')) {
        var cart = $('form.cart'); if (cart) cart.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      return;
    }
    // Clic sur le fond (hors du panneau) : fermeture.
    if (t.tagName === 'DIALOG' && t.classList.contains('lmdd-dlg')) {
      var r = t.getBoundingClientRect();
      if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) closeDlg(t);
    }
    // Fiche symptôme → formulaire SAV avec le symptôme présélectionné.
    var sym = t.closest && t.closest('[data-lmdd-symptom]');
    if (sym) {
      var input = $('.lmdd-steps input[name=symptome_type][value="' + sym.getAttribute('data-lmdd-symptom') + '"]');
      if (input) {
        e.preventDefault();
        var form = input.closest('form');
        goStep(form, 0);
        input.checked = true;
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(function () { var ta = $('textarea[name=symptome]', form); if (ta) ta.focus({ preventScroll: true }); }, 500);
      }
    }
  }, true);

  document.addEventListener('close', function (e) {
    if (e.target.classList && e.target.classList.contains('lmdd-dlg')) document.documentElement.classList.remove('lmdd-noscroll');
  }, true);

  // ------------------------------------------------------------------ Validation
  function setError(field, msg) {
    var wrap = field.closest('.lmdd-field'); if (!wrap) return;
    wrap.classList.toggle('has-error', !!msg);
    var err = $('.lmdd-err', wrap); if (err) err.textContent = msg || '';
    $$('input, select, textarea', wrap).forEach(function (i) { if (msg) i.setAttribute('aria-invalid', 'true'); else i.removeAttribute('aria-invalid'); });
  }
  function checkField(el) {
    var v = (el.value || '').trim(), msg = '';
    if (el.required && !v) msg = 'Ce champ est nécessaire.';
    else if (v && el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) msg = 'Adresse e-mail invalide.';
    else if (v && el.type === 'tel' && v.replace(/\D/g, '').length < 9) msg = 'Numéro de téléphone incomplet.';
    setError(el, msg);
    return !msg;
  }
  function validate(scope) {
    var ok = true, first = null;
    $$('input[required], textarea[required], select[required], input[type=email], input[type=tel]', scope).forEach(function (el) {
      if (el.closest('[hidden]') && !scope.matches('[hidden]')) return;
      if (!checkField(el)) { ok = false; first = first || el; }
    });
    if (first) first.focus();
    return ok;
  }
  // Une erreur s'efface pendant la saisie (et non à la sortie du champ : le bouton cliqué ne doit pas bouger sous le curseur).
  function clearIfValid(e) {
    var el = e.target, wrap = el.closest && el.closest('.lmdd-form .has-error');
    if (!wrap || el.type === 'file') return;
    if (el.type === 'radio') setError(el, ''); else checkField(el);
  }
  document.addEventListener('input', clearIfValid);
  document.addEventListener('change', clearIfValid);

  // ------------------------------------------------------------------ Photos (SAV)
  document.addEventListener('change', function (e) {
    var el = e.target;
    if (!el.matches || !el.matches('.lmdd-form input[type=file]')) return;
    var max = +el.dataset.max || 3, list = $('.lmdd-files', el.closest('.lmdd-field')), msg = '';
    var files = Array.prototype.slice.call(el.files || []);
    if (files.length > max) msg = max + ' photos au maximum.';
    files.forEach(function (f) { if (f.size > 8388608) msg = '« ' + f.name + ' » dépasse 8 Mo.'; });
    list.innerHTML = '';
    files.forEach(function (f) { var li = document.createElement('li'); li.textContent = f.name + ' (' + Math.round(f.size / 1024) + ' Ko)'; list.appendChild(li); });
    if (msg) el.value = '';
    setError(el, msg);
  });

  // ------------------------------------------------------------------ SAV : étapes
  function goStep(form, n) {
    var steps = $$('.lmdd-step', form), total = steps.length;
    n = Math.max(0, Math.min(n, total - 1));
    steps.forEach(function (s, i) { s.hidden = i !== n; });
    form.dataset.step = n;
    var pct = Math.round((n + 1) / total * 100);
    $('[data-lmdd-step-label]', form).textContent = 'Étape ' + (n + 1) + ' sur ' + total;
    $('[data-lmdd-step-pct]', form).textContent = pct + ' %';
    $('.lmdd-progress__bar span', form).style.width = pct + '%';
    $('[data-lmdd-prev]', form).hidden = n === 0;
    $('[data-lmdd-next]', form).hidden = n === total - 1;
    $('button[type=submit]', form).hidden = n !== total - 1;
    if (n === total - 1) { recap(form); turnstile(form); }
  }
  function recap(form) {
    var dl = $('.lmdd-recap', form); dl.innerHTML = '';
    $$('.lmdd-step:not(:last-of-type) .lmdd-field', form).forEach(function (f) {
      var label = $('legend, label, .lmdd-label', f), val = '';
      var radio = $('input[type=radio]:checked', f);
      var file = $('input[type=file]', f);
      if (radio) val = clean(radio.closest('label').textContent);
      else if (file) val = file.files && file.files.length ? file.files.length + ' photo(s)' : '';
      else { var i = $('input, select, textarea', f); val = i ? i.value.trim() : ''; }
      if (!val || !label) return;
      var dt = document.createElement('dt'); dt.textContent = clean(label.textContent).replace(/\s*\*$/, '');
      var dd = document.createElement('dd'); dd.textContent = val;
      dl.appendChild(dt); dl.appendChild(dd);
    });
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest && e.target.closest('[data-lmdd-next], [data-lmdd-prev]');
    if (!b) return;
    var form = b.closest('form'), n = +form.dataset.step || 0;
    if (b.hasAttribute('data-lmdd-next')) {
      if (!validate($$('.lmdd-step', form)[n])) return;
      goStep(form, n + 1);
    } else goStep(form, n - 1);
    var top = form.getBoundingClientRect().top;
    if (top < 0) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  // Entrée dans un champ d'une étape intermédiaire = « Continuer », pas « Envoyer ».
  document.addEventListener('keydown', function (e) {
    var f = e.target.closest && e.target.closest('.lmdd-steps');
    if (f && e.key === 'Enter' && e.target.tagName === 'INPUT') {
      var next = $('[data-lmdd-next]', f);
      if (next && !next.hidden) { e.preventDefault(); next.click(); }
    }
  });

  // ------------------------------------------------------------------ Envoi
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.classList || !form.classList.contains('lmdd-form')) return;
    e.preventDefault();
    var errBox = $('.lmdd-form__error', form); errBox.textContent = '';
    if (!validate(form)) { errBox.textContent = 'Merci de compléter les champs signalés.'; return; }
    var btn = $('button[type=submit]', form);
    var data = new FormData(form);
    data.append('_t', String(Date.now() - started));
    data.append('page', location.href);
    btn.disabled = true; btn.classList.add('is-loading');
    fetch(CFG.endpoint, { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (r) { return r.json().catch(function () { return { ok: false, message: 'Réponse inattendue du serveur.' }; }); })
      .then(function (res) {
        if (res && res.ok) return done(form, res);
        errBox.textContent = (res && res.message) || 'L\'envoi n\'a pas abouti.';
        var fields = (res && res.fields) || {}, firstStep = null;
        Object.keys(fields).forEach(function (name) {
          var el = $('[name="' + name + '"], [name="' + name + '[]"]', form);
          if (el) { setError(el, fields[name]); var st = el.closest('.lmdd-step'); if (st && firstStep === null) firstStep = +st.dataset.step; }
        });
        if (firstStep !== null && form.classList.contains('lmdd-steps')) goStep(form, firstStep);
        if (window.turnstile) $$('.lmdd-turnstile', form).forEach(function (b) { try { window.turnstile.reset(b); } catch (x) {} });
      })
      .catch(function () { errBox.textContent = 'Connexion impossible. Vérifiez votre réseau, ou appelez-nous.'; })
      .then(function () { btn.disabled = false; btn.classList.remove('is-loading'); });
  });

  function done(form, res) {
    var ok = $('.lmdd-success', form);
    $$(':scope > *:not(.lmdd-success):not(input):not(textarea), .lmdd-dlg__body, .lmdd-dlg__foot', form).forEach(function (el) {
      if (!el.closest('.lmdd-dlg__head') && !el.classList.contains('lmdd-dlg__head')) el.hidden = true;
    });
    ok.hidden = false;
    $('.lmdd-success__ref', ok).textContent = res.reference ? 'Référence de votre demande : ' + res.reference : '';
    var email = $('input[name=email]', form), note = $('[data-lmdd-email-note]', ok);
    if (note) note.hidden = !(email && email.value);
    ok.focus();
    if (window.dataLayer) window.dataLayer.push({ event: 'lmdd_demande', lmdd_type: form.dataset.type });
  }

  // Formulaires SAV : état initial des boutons.
  $$('.lmdd-steps').forEach(function (f) { goStep(f, 0); });
})();
