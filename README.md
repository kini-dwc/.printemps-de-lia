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
| `elementor/` | Modèles Elementor importables (accueil + pied de page), voir `elementor/LISEZ-MOI.md` |
| `wordpress/plugins/asset-pilot/` | **Asset Pilot** : gestionnaire de scripts avec interface, mode test et mesure d'impact (voir son `LISEZ-MOI.md`) |
| `wordpress/asset-pilot-regles-la-maison-du-dos.json` | 26 règles issues de l'audit, à importer dans Asset Pilot (arrivent en test) |
| `tools/asset-pilot-tester/` | Test d'impact automatisé : pages + parcours clients, avec et sans règles |
| `docs/captures/` | Captures de la maquette, du rendu Elementor et d'Asset Pilot |

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

## Asset Pilot : désactiver les scripts inutiles sans risque

`wordpress/mu-plugins/lmdd-performance.php` (listes figées dans le code) peut être remplacé par l'extension **Asset Pilot** :
1. installer `asset-pilot.zip` (ou le dossier `wordpress/plugins/asset-pilot/`) ;
2. importer `wordpress/asset-pilot-regles-la-maison-du-dos.json` (*Outils → Asset Pilot → Règles → Importer*) : les règles arrivent **en test** ;
3. vérifier avec *Tester l'impact* et avec `tools/asset-pilot-tester/run.mjs` (parcours panier et commande) ;
4. mettre en ligne les règles validées.

Si vous gardez le mu-plugin pour ses nettoyages globaux (emojis, heartbeat, `defer`), videz ses listes
`LMDD_HOME_DEQUEUE_*` pour éviter les doublons avec Asset Pilot.
