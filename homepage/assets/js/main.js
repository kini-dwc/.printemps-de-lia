/* La Maison du Dos — page d'accueil
   JS vanilla (~2 Ko), chargé en defer. Aucune dépendance (pas de jQuery). */
(function () {
  'use strict';

  var doc = document;
  var body = doc.body;

  /* ---- En-tête : ombre au scroll ---- */
  var header = doc.querySelector('[data-header]');
  if (header && 'IntersectionObserver' in window) {
    var sentinel = doc.createElement('div');
    sentinel.style.cssText = 'position:absolute;top:0;height:1px;width:1px';
    body.prepend(sentinel);
    new IntersectionObserver(function (entries) {
      header.classList.toggle('is-scrolled', !entries[0].isIntersecting);
    }, { rootMargin: '40px 0px 0px 0px' }).observe(sentinel);
  }

  /* ---- Menu mobile ---- */
  var burger = doc.querySelector('[data-burger]');
  var nav = doc.querySelector('[data-nav]');
  function setMenu(open) {
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
    nav.classList.toggle('is-open', open);
    body.classList.toggle('menu-open', open);
    if (open) { var first = nav.querySelector('a'); if (first) first.focus(); }
  }
  if (burger && nav) {
    burger.addEventListener('click', function () {
      setMenu(burger.getAttribute('aria-expanded') !== 'true');
    });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) { setMenu(false); burger.focus(); }
    });
    body.addEventListener('click', function (e) {
      if (nav.classList.contains('is-open') && !nav.contains(e.target) && !burger.contains(e.target)) setMenu(false);
    });
    window.matchMedia('(min-width: 1025px)').addEventListener('change', function (mq) {
      if (mq.matches) setMenu(false);
    });
  }

  /* ---- Panneau de recherche ---- */
  var searchBtn = doc.querySelector('[data-search-toggle]');
  var search = doc.querySelector('[data-search]');
  function setSearch(open) {
    search.hidden = !open;
    searchBtn.setAttribute('aria-expanded', String(open));
    if (open) search.querySelector('input[type="search"]').focus();
  }
  if (searchBtn && search) {
    searchBtn.addEventListener('click', function () { setSearch(search.hidden); });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !search.hidden) { setSearch(false); searchBtn.focus(); }
    });
  }

  /* ---- Apparition des blocs au scroll ---- */
  var reveals = doc.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* ---- FAQ : une seule question ouverte à la fois ---- */
  var faqs = doc.querySelectorAll('.faq details');
  faqs.forEach(function (d) {
    d.addEventListener('toggle', function () {
      if (d.open) faqs.forEach(function (o) { if (o !== d) o.open = false; });
    });
  });

  /* ---- Trustpilot : script tiers chargé seulement à l'approche de la section ----
     Évite ~150 Ko de JS + iframe au chargement initial. */
  var tp = doc.querySelector('[data-lazy-trustbox]');
  if (tp && tp.dataset.templateId && tp.dataset.templateId.indexOf('VOTRE_') !== 0) {
    var loadTP = function () {
      tp.className += ' trustpilot-widget';
      tp.setAttribute('data-locale', 'fr-FR');
      tp.setAttribute('data-style-height', '240px');
      tp.setAttribute('data-style-width', '100%');
      var s = doc.createElement('script');
      s.src = 'https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js';
      s.async = true;
      s.onload = function () { if (window.Trustpilot) window.Trustpilot.loadFromElement(tp, true); };
      doc.head.appendChild(s);
    };
    if ('IntersectionObserver' in window) {
      var tpio = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) { tpio.disconnect(); loadTP(); }
      }, { rootMargin: '400px 0px' });
      tpio.observe(tp);
    } else {
      window.addEventListener('load', loadTP);
    }
  } else if (tp) {
    tp.querySelector('.reviews__placeholder').textContent = 'Widget Trustpilot : renseignez vos identifiants TrustBox.';
  }

  /* ---- Année du copyright ---- */
  var y = doc.querySelector('[data-year]');
  if (y) y.textContent = new Date().getFullYear();
})();
