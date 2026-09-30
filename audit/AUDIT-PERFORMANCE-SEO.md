# Audit performance & SEO : la-maison-du-dos.com

> **Important : ce qu'on a vu et ce qu'on n'a pas vu.** L'environnement où ce travail a été fait
> n'a pas pu se connecter à `prod.la-maison-du-dos.com` (hôte bloqué par sa politique réseau).
> Ce document distingue donc :
> - ✅ **Constaté** : faits observés dans l'index des moteurs de recherche et les annuaires ;
> - 🔎 **À mesurer** : causes de lenteur classiques d'une pile WordPress + WooCommerce + Elementor,
>   à confirmer avec l'outil fourni (`audit-homepage.mjs`) et le mode diagnostic du mu-plugin.
>
> Pour obtenir la liste **réelle et exhaustive** des scripts de la page actuelle, lancez :
> ```bash
> npm install
> npm run audit -- https://prod.la-maison-du-dos.com/
> npm run audit -- https://prod.la-maison-du-dos.com/ --desktop
> ```
> Rapports générés : `audit/rapport/rapport-mobile.md` et `rapport-desktop.md` (poids par plugin,
> JS/CSS bloquants, % de code inutilisé par fichier, Web Vitals, images sans dimensions, SEO).

---

## 1. Constats SEO (✅ observés dans l'index)

| # | Constat | Impact | Action |
|---|---|---|---|
| 1 | **Deux hôtes indexés** : `www.la-maison-du-dos.com` et `la-maison-du-dos.com` | Contenu dupliqué, popularité diluée | Choisir un hôte (recommandé : sans `www`, déjà utilisé par WordPress) et rediriger l'autre en **301** au niveau serveur |
| 2 | **Anciennes URL encore indexées** (ancienne boutique) : `/la-maison-du-dos-m-15.html`, `/hefel-m-16.html`, `/surmatelas-climacontrol-confort-xml-355_363-1017.html`… | Pages mortes (404) ou doublons ; perte du « jus » SEO historique | Plan de redirections **301** ancienne URL → nouvelle page WordPress équivalente (plugin *Redirection* ou règles `.htaccess`/Nginx) |
| 3 | Sous-domaine **`prod.`** | S'il est accessible et indexable, c'est un doublon complet du site | Vérifier : `noindex` + protection par mot de passe tant que ce n'est pas le domaine principal ; balise `canonical` vers le domaine final |
| 4 | Pages techniques indexées : `/my-wishlist/`, `/vos-devis-en-cours/` | Pages sans valeur dans Google, budget de crawl gaspillé | Les passer en `noindex` (Yoast / Rank Math → Avancé) |
| 5 | Titre de page d'accueil `Accueil - La Maison du Dos` | « Accueil » n'est pas un mot-clé : titre peu cliquable | Nouveau titre : `Lit à eau et literie ergonomique \| La Maison du Dos` |
| 6 | **Code postal incohérent** selon les sources : 52270 (site) / 52230 (annuaires) | Signal local (NAP) contradictoire pour Google | Unifier partout (site, Google Business Profile, annuaires). *La maquette utilise 52230, à confirmer.* |

## 2. Causes probables de lenteur (🔎 à confirmer par la mesure)

Classées par gain habituel sur ce type de pile.

### Serveur & cache
| Cause | Symptôme dans le rapport | Correctif |
|---|---|---|
| Pas de cache de page | TTFB > 800 ms | Cache de page (WP Rocket, LiteSpeed Cache ou cache serveur de l'hébergeur) ; exclure panier, commande et compte |
| PHP ancien, pas d'OPcache ni de cache objet | TTFB élevé même en cache « chaud » | PHP 8.2+, OPcache, Redis Object Cache |
| Table `wp_options` gonflée (options *autoload*) | TTFB élevé sur toutes les pages | Purger les transients et les options orphelines des plugins supprimés (WP-Optimize / Advanced DB Cleaner) |
| Pas de compression Brotli/Gzip, pas de HTTP/2-3 | HTML/CSS/JS transférés non compressés | Activer côté hébergeur ou via un CDN (Cloudflare) |

### WooCommerce
| Cause | Correctif (mu-plugin fourni) |
|---|---|
| `wc-cart-fragments` : requête AJAX `?wc-ajax=get_refreshed_fragments` à **chaque page vue**, non cachable | Retiré sur l'accueil ; ailleurs, ne le garder que si un mini-panier AJAX est vraiment utilisé |
| CSS/JS WooCommerce + blocs WooCommerce chargés partout | Retirés sur l'accueil |
| Attribution de commande (`sourcebuster-js`) | Retiré sur l'accueil |

### Elementor
| Cause | Correctif |
|---|---|
| DOM très profond (sections > colonnes > widgets imbriqués) | Réglages → Fonctionnalités : activer **Optimized DOM Output**, **Flexbox Container** ; reconstruire l'accueil en conteneurs (la maquette est pensée pour ça) |
| CSS/JS de tous les widgets chargés même inutilisés | Activer **Improved Asset Loading**, **Improved CSS Loading**, **Element Caching** |
| Font Awesome + eicons (≈ 100 à 150 Ko) | Activer **Inline Font Icons** (icônes en SVG) ; le mu-plugin retire FA sur l'accueil |
| Google Fonts distantes (requêtes externes + FOUT + CLS) | Désactivées par le mu-plugin ; utiliser la pile système (maquette) ou des polices auto-hébergées en `woff2` |
| Sliders / carrousels (Swiper) au-dessus de la ligne de flottaison | À supprimer : l'image LCP doit être une image unique, jamais un slider |
| Animations d'entrée Elementor (`animated fadeIn…`) | Remplacées par l'`IntersectionObserver` léger de la maquette, ou supprimées |

### Plugins annexes (détectés via les URL indexées)
| Plugin probable | Indice | Correctif |
|---|---|---|
| Wishlist (YITH ou TI) | page `/my-wishlist/` | JS/CSS (+ jQuery selectBox, prettyPhoto, Font Awesome) retirés sur l'accueil ; ne les charger que sur les fiches produit |
| Demande de devis (YITH Request a Quote ?) | page `/vos-devis-en-cours/` | Idem : charger uniquement là où le bouton « devis » apparaît |

### Front
| Cause | Correctif |
|---|---|
| Images JPEG/PNG lourdes, sans `width`/`height` | Conversion **AVIF/WebP**, tailles `srcset`, dimensions explicites (CLS = 0) |
| `loading="lazy"` sur l'image du hero | **Jamais** sur l'image LCP : `fetchpriority="high"` à la place |
| Scripts tiers chargés au démarrage (Trustpilot, chat, pixels, cartes Google) | Chargement différé à la visibilité ou à l'interaction (exemple Trustpilot dans `main.js`) |
| jQuery + jQuery Migrate | Migrate retiré par le mu-plugin ; la maquette n'utilise **aucun** jQuery |

## 3. Plan d'action recommandé

1. **Mesurer** : `npm run audit` sur la page actuelle, puis `?lmdd_assets=1` (connecté en admin) pour obtenir les *handles* exacts.
2. **Sauvegarder** le site (fichiers + base).
3. Installer `wordpress/mu-plugins/lmdd-performance.php`, ajuster les listes de handles selon le diagnostic, vérifier l'accueil connecté **et** déconnecté.
4. Réglages Elementor (section 2) + cache de page + conversion des images.
5. Intégrer la nouvelle page d'accueil (conversion Elementor JSON après validation de la maquette).
6. SEO : redirections 301 (hôte unique + anciennes URL), `noindex` des pages techniques, titre/description, soumission du sitemap dans la Search Console.
7. **Re-mesurer** avec `npm run audit` et PageSpeed Insights : comparer avant/après.

## 4. Objectifs cibles (mobile, PageSpeed Insights)

| Indicateur | Cible |
|---|---|
| LCP | < 2,5 s |
| INP | < 200 ms |
| CLS | < 0,1 |
| Poids de la page d'accueil | < 1 Mo (hors images produits en lazy-load) |
| Requêtes | < 40 |
| Score Performance | ≥ 90 |

### Référence : la maquette livrée
Mesurée avec l'outil fourni sur un serveur local (images de démonstration en SVG) :

| Profil | LCP | CLS | Requêtes | Poids | JS |
|---|---|---|---|---|---|
| Desktop | 0,1 à 0,35 s | 0 | 10 | ≈ 69 Ko non compressé (HTML 8 Ko + CSS 5,5 Ko en gzip) | 1 fichier de 4,6 Ko, aucune dépendance |
| Mobile (4G lente, CPU ×4) | 0,7 à 1,3 s | 0 | 10 | idem | idem |

Les vraies photos produits ajouteront du poids : prévoir de l'AVIF/WebP ≤ 120 Ko pour le hero.
