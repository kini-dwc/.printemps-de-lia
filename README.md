# La Maison du Dos : refonte de la page d'accueil

Nouvelle page d'accueil HTML/CSS/JS rapide, orientée SEO et conversion, avec le kit d'audit et
d'optimisation du WordPress existant.

## Contenu

| Dossier | Rôle |
|---|---|
| `homepage/` | Maquette de la page d'accueil (`index.html`, `assets/css/main.css`, `assets/js/main.js`, visuels SVG provisoires) |
| `audit/AUDIT-PERFORMANCE-SEO.md` | Constats SEO, causes de lenteur, plan d'action |
| `audit/audit-homepage.mjs` | Outil d'audit Playwright : liste chaque script/CSS par plugin, poids, code inutilisé, Web Vitals |
| `wordpress/mu-plugins/lmdd-performance.php` | Mu-plugin qui retire les scripts inutiles (accueil) + mode diagnostic `?lmdd_assets=1` |
| `docs/captures/` | Captures desktop / mobile de la maquette |

## Voir la maquette

```bash
npm run dev          # puis http://localhost:8080
```

## Auditer le site actuel

```bash
npm install
npx playwright install chromium
npm run audit -- https://prod.la-maison-du-dos.com/            # profil mobile
npm run audit -- https://prod.la-maison-du-dos.com/ --desktop
```

Derrière un proxy d'entreprise, des arguments Chromium peuvent être passés via `CHROMIUM_ARGS`.

## Choix techniques de la maquette

- **Aucune dépendance** : ni jQuery, ni framework, ni police externe, ni police d'icônes (sprite SVG inline).
- **1 CSS + 1 JS différé** (≈ 5,5 Ko + 2 Ko en gzip). Pas de CSS ni de JS bloquant en dehors de la feuille principale.
- **Core Web Vitals** : image LCP préchargée en `fetchpriority="high"`, dimensions explicites partout (CLS = 0),
  `loading="lazy"` sous la ligne de flottaison, widget Trustpilot chargé seulement à l'approche de la section.
- **SEO** : un seul `H1` ciblé (« lit à eau », « literie ergonomique », « mal de dos »), hiérarchie H2/H3,
  `title` et `meta description` optimisés, `canonical`, Open Graph, JSON-LD
  (`Organization`/`FurnitureStore` avec adresse et horaires, `WebSite` + `SearchAction`, `FAQPage`),
  maillage interne vers toutes les catégories.
- **Accessibilité** : lien d'évitement, landmarks, `aria-*` sur le menu et la recherche, focus visible,
  `prefers-reduced-motion`, contrastes AA.
- **Pensé pour Elementor** : chaque section correspond à un conteneur Flexbox ; la grille produits
  sera remplacée par le widget WooCommerce « Produits » lors de la conversion JSON.

## Contenus vérifiés

Tous les textes, chiffres, liens et produits de la maquette ont été vérifiés le 30/09/2026 sur
`prod.la-maison-du-dos.com` : accueil, contact, mentions légales, qui sommes-nous, livraison/paiement,
FAQ « 35 réponses d'experts », fiches produits. Les avis viennent du widget officiel de la Société des Avis Garantis.
Photos : images du site (lit à eau Altura, Havre, Tec-Line, Aqualight, Bella Donna), recadrées et converties en WebP.

## À valider avant la conversion Elementor

1. **Code postal** : 52270 (mentions légales) ou 52230 (annuaires) ?
2. **Prix** : affichés tels que sur le site ; sont-ils HT ou TTC ?
3. **Produits mis en avant** : sélection actuelle (Havre, Tec-Line, Aqualight Premium, Bella Donna) à confirmer.
4. **Logo en SVG** si disponible (plus net que le WebP actuel, 425×130 px).
