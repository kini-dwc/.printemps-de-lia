/* LMDD – Devis & SAV (v2) : devis affiché sur place, étapes des formulaires Contact Form 7, présélection de la situation SAV.
   Sans dépendance, chargé en différé, uniquement sur les fiches produits et les pages qui contiennent [lmdd_…]. */
(function () {
  'use strict';
  function $(sel, root) { return (root || document).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
  function clean(t) { return (t || '').replace(/\s+/g, ' ').replace(/\s*\(\s*\+?\s*0[,.]?0*\s*€\s*\)/, '').trim(); }
  function norm(t) { return clean(t).toLowerCase().replace(/[’']/g, "'"); }

  // ------------------------------------------------------------------ Options choisies sur la fiche (TM Extra Product Options, variations)
  function readConfig() {
    var out = [];
    $$('form.cart .tc-container, form.cart .tm-extra-product-options-field').forEach(function (c) {
      var label = $('.tc-epo-element-label-text, .tm-epo-element-label', c), val = '';
      var sel = $('select', c);
      if (sel && sel.selectedIndex >= 0 && sel.value !== '') val = clean(sel.options[sel.selectedIndex].text);
      if (!val) val = $$('input[type=radio]:checked, input[type=checkbox]:checked', c).map(function (i) { var l = i.closest('label'); return clean(l ? l.textContent : i.value); }).join(', ');
      var txt = $('input[type=text], input[type=number], textarea', c);
      if (!val && txt && txt.value) val = clean(txt.value);
      if (label && val && !/^(choisir|choose|select)/i.test(val)) out.push(clean(label.textContent) + ' : ' + val);
    });
    if (!out.length) {
      $$('form.cart table.variations tr').forEach(function (r) {
        var l = $('label', r), s = $('select', r);
        if (l && s && s.value) out.push(clean(l.textContent).replace(/:$/, '') + ' : ' + clean(s.options[s.selectedIndex].text));
      });
    }
    var qty = $('form.cart input.qty');
    if (qty && +qty.value > 1) out.push('Quantité : ' + qty.value);
    return out.join('\n');
  }
  // Juste avant l'envoi (capture : avant Contact Form 7), les options sont copiées dans le champ caché.
  document.addEventListener('submit', function (e) {
    var f = e.target.closest && e.target.closest('.lmdd-cf7--devis form');
    if (f) { var h = $('[name=configuration]', f); if (h) h.value = readConfig(); }
  }, true);

  // ------------------------------------------------------------------ Devis : le formulaire prend la place du bouton
  function openDevis() {
    var box = $('[data-lmdd-devis]'); if (!box) return;
    var panel = $('.lmdd-devis__panel', box), cta = $('.lmdd-devis__cta', box);
    panel.hidden = false; cta.hidden = true;
    $$('[data-lmdd-devis-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
    var r = panel.getBoundingClientRect();
    if (r.top < 0 || r.top > innerHeight * 0.6) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    var first = $('input:not([type=hidden]), select, textarea', panel);
    if (first) setTimeout(function () { first.focus({ preventScroll: true }); }, 350);
  }
  function closeDevis() {
    var box = $('[data-lmdd-devis]'); if (!box) return;
    $('.lmdd-devis__panel', box).hidden = true; $('.lmdd-devis__cta', box).hidden = false;
    $$('[data-lmdd-devis-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
  }

  document.addEventListener('click', function (e) {
    var t = e.target;
    if (!t.closest) return;
    // Ancien bouton du modèle de fiche (#trigger-quote-btn) : il ouvre aussi le formulaire sur place.
    if (t.closest('[data-lmdd-devis-open], #trigger-quote-btn, .btn-devis-custom') && $('[data-lmdd-devis]')) {
      e.preventDefault(); e.stopImmediatePropagation(); openDevis(); return;
    }
    if (t.closest('[data-lmdd-devis-close]')) { e.preventDefault(); closeDevis(); return; }
    // SAV : lien « C'est mon cas » (vers le formulaire) dans un accordéon → la situation est présélectionnée.
    var a = t.closest('a[href*="#formulaire-sav"]');
    if (a) {
      var item = a.closest('.elementor-accordion-item, .e-n-accordion-item, details, .elementor-toggle-item');
      var title = item && $('.elementor-tab-title, .e-n-accordion-item-title-text, summary, .elementor-toggle-title', item);
      var form = $('.lmdd-cf7--sav form');
      if (form) {
        if (title) {
          var want = norm(title.textContent);
          $$('input[type=radio], input[type=checkbox]', form).forEach(function (r) { if (norm(r.value) === want) r.checked = true; else if (r.name === 'situation' || r.name === 'situation[]') r.checked = false; });
        }
        if (form.querySelector('.lmdd-etape')) goStep(form, 0);
      }
    }
  }, true);

  // Mobile : barre « Demander un devis » visible quand le bouton principal n'est plus à l'écran.
  var sticky = $('.lmdd-sticky-cta'), devisBox = $('[data-lmdd-devis]');
  if (sticky && devisBox && 'IntersectionObserver' in window) {
    document.body.appendChild(sticky);
    new IntersectionObserver(function (en) {
      var past = !en[0].isIntersecting && en[0].boundingClientRect.top < 0;
      sticky.hidden = !past;
      document.documentElement.classList.toggle('lmdd-has-sticky', past);
    }).observe(devisBox);
  }

  // ------------------------------------------------------------------ Étapes : <fieldset class="lmdd-etape" data-titre="…"> dans le formulaire CF7
  function goStep(form, n) {
    var steps = $$('.lmdd-etape', form), total = steps.length;
    if (!total) return;
    n = Math.max(0, Math.min(n, total - 1));
    steps.forEach(function (s, i) { s.hidden = i !== n; });
    form.dataset.lmddStep = n;
    var bar = $('.lmdd-progress', form);
    $('.lmdd-progress__label', bar).textContent = 'Étape ' + (n + 1) + ' sur ' + total + (steps[n].dataset.titre ? ' · ' + steps[n].dataset.titre : '');
    $('.lmdd-progress__bar span', bar).style.width = Math.round((n + 1) / total * 100) + '%';
    $('[data-lmdd-prev]', form).hidden = n === 0;
    $('[data-lmdd-next]', form).hidden = n === total - 1;
  }
  function stepValid(step) {
    var ok = true, first = null;
    $$('[aria-required="true"]', step).forEach(function (el) {
      var wrap = el.closest('.wpcf7-form-control-wrap');
      var empty = el.type === 'checkbox' || el.type === 'radio' ? !$('input:checked', wrap) : !el.value.trim();
      var tip = wrap && $('.lmdd-tip', wrap);
      if (empty) {
        ok = false; first = first || el;
        if (wrap && !tip) { tip = document.createElement('span'); tip.className = 'wpcf7-not-valid-tip lmdd-tip'; tip.textContent = 'Ce champ est nécessaire.'; wrap.appendChild(tip); }
        el.setAttribute('aria-invalid', 'true');
      } else if (tip) { tip.remove(); el.setAttribute('aria-invalid', 'false'); }
    });
    if (first) first.focus();
    return ok;
  }
  $$('.lmdd-cf7 form').forEach(function (form) {
    if (!$('.lmdd-etape', form)) return;
    form.classList.add('lmdd-steps');
    var bar = document.createElement('div');
    bar.className = 'lmdd-progress';
    bar.innerHTML = '<p class="lmdd-progress__label"></p><div class="lmdd-progress__bar"><span></span></div>';
    form.insertBefore(bar, $('.lmdd-etape', form));
    var nav = document.createElement('div');
    nav.className = 'lmdd-steps__nav';
    nav.innerHTML = '<button type="button" class="lmdd-btn lmdd-btn--ghost" data-lmdd-prev>Retour</button><button type="button" class="lmdd-btn lmdd-btn--primary" data-lmdd-next>Continuer</button>';
    var last = $$('.lmdd-etape', form).pop();
    last.parentNode.insertBefore(nav, last.nextSibling);
    nav.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      var n = +form.dataset.lmddStep || 0;
      if (b.hasAttribute('data-lmdd-next')) { if (!stepValid($$('.lmdd-etape', form)[n])) return; goStep(form, n + 1); } else goStep(form, n - 1);
      if (form.getBoundingClientRect().top < 0) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    // Entrée dans un champ d'une étape intermédiaire = « Continuer ».
    form.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.target.tagName === 'INPUT' && !$('[data-lmdd-next]', form).hidden) { e.preventDefault(); $('[data-lmdd-next]', form).click(); }
    });
    goStep(form, 0);
  });

  // Erreur renvoyée par Contact Form 7 (événement émis sur le bloc .wpcf7) : retour à l'étape du premier champ en erreur.
  document.addEventListener('wpcf7invalid', function (e) {
    var form = e.target.querySelector ? (e.target.matches('form') ? e.target : e.target.querySelector('form')) : null;
    if (!form || !$('.lmdd-etape', form)) return;
    var bad = $('[aria-invalid="true"], .wpcf7-not-valid', form), st = bad && bad.closest('.lmdd-etape');
    if (st) goStep(form, $$('.lmdd-etape', form).indexOf(st));
  });

  // ------------------------------------------------------------------ Après l'envoi : message de confirmation mis en avant
  document.addEventListener('wpcf7mailsent', function (e) {
    var wrap = e.target.closest && e.target.closest('.lmdd-cf7');
    if (!wrap) return;
    wrap.classList.add('is-sent');
    var out = $('.wpcf7-response-output', wrap);
    if (out) { out.setAttribute('tabindex', '-1'); setTimeout(function () { out.scrollIntoView({ behavior: 'smooth', block: 'center' }); out.focus({ preventScroll: true }); }, 50); }
    if (window.dataLayer) window.dataLayer.push({ event: 'lmdd_demande', lmdd_type: wrap.getAttribute('data-lmdd-cf7') });
  });
})();
