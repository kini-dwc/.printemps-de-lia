# Audit performance & SEO : page d'accueil La Maison du Dos

**Mesuré le 30/09/2026 sur `https://prod.la-maison-du-dos.com/`**, avec l'outil fourni (`audit/audit-homepage.mjs`,
Chromium piloté par Playwright) et `curl`. Rapports bruts : [`rapport-site-actuel/rapport-mobile.md`](rapport-site-actuel/rapport-mobile.md)
et [`rapport-site-actuel/rapport-desktop.md`](rapport-site-actuel/rapport-desktop.md).

> **Limites de la mesure.** Les tests tournent depuis un serveur cloud (hors de France) derrière un proxy, avec
> une simulation mobile « 4G lente + CPU ×4 ». Les valeurs absolues sont plus pessimistes que chez un visiteur
> français sur fibre. Les **rapports entre l'ancien et le nouveau** et le **classement des causes** restent fiables.
> Pour une référence officielle, comparer avec PageSpeed Insights.

## 1. Résultats

| Indicateur | Site actuel (mobile) | Site actuel (desktop) | Nouvelle maquette (mobile) | Objectif |
|---|---:|---:|---:|---:|
| Réponse serveur (TTFB) | 3,1 s | 3,8 s | *(statique)* | < 0,8 s |
| Largest Contentful Paint | **9,7 s** | 6,9 s | **0,97 s** | < 2,5 s |
| Total Blocking Time | 21,4 s* | 2,6 s | 0,19 s | < 0,2 s |
| Requêtes | **251** | 241 | **12** | < 50 |
| Poids transféré | **3,9 Mo** | 1,95 Mo | **271 Ko** | < 1,5 Mo |
| JavaScript exécuté (décompressé) | 4,5 Mo (125 fichiers) | | 4,6 Ko (1 fichier) | |
| HTML | 560 Ko (127 Ko compressé) | | 35 Ko | < 60 Ko |
| Nœuds DOM / profondeur | 2 517 / 35 | | 477 / 11 | < 1 500 / < 32 |

\* Le TBT mobile est gonflé par le CPU lent du serveur de test, mais il y a bien 64 tâches longues, dont une de 3,7 s.

**Vérification du TTFB** : un fichier statique (`.webp`) répond en 0,38 s, alors que `robots.txt` (généré par
WordPress) met **2,3 à 4,2 s**. La lenteur vient donc du démarrage de PHP/WordPress, pas du réseau.
Le domaine principal `la-maison-du-dos.com` répond en 0,4 à 0,7 s grâce à son cache (o2switch PowerBoost) :
**`prod.` n'est pas en cache**.

## 2. Causes de lenteur, par ordre d'impact (toutes constatées)

| # | Cause | Données mesurées | Correctif |
|---|---|---|---|
| 1 | **Éditeur de blocs Gutenberg chargé sur le site public** | 43 fichiers `wp-includes/js/dist/` (React, react-dom, wp-components, wp-block-editor, moment…) : **1,17 Mo transférés, 3 Mo de JS**, tous **synchrones**, + 59 Ko de traductions inline `wp-block-editor`. Ils sont déclarés juste avant `prisna-wp-translate-blocks` | Retirer `prisna-wp-translate-blocks` du front (mu-plugin) ou remplacer Prisna WP Translate. Vérifier avec `?lmdd_assets=1` |
| 2 | **PHP lent / pas de cache sur `prod.`** | TTFB 3 à 5 s, `robots.txt` 2 à 4 s | Activer le cache de page (WP Rocket est présent) et Redis ; réduire les 20+ plugins ; purger les options *autoload* |
| 3 | **Tags Google en triple** | `gtag/js` chargé **3 fois** + `gtm.js` : **725 Ko** (≈ 50 % inutilisés) via Site Kit, Pixel Manager (`woocommerce-google-adwords-conversion-tracking-tag`) et GTM | Un seul conteneur GTM, qui porte GA4, Ads et Clarity ; désactiver l'injection de balises dans Site Kit et Pixel Manager |
| 4 | **« Sign in with Google » (Site Kit)** | `accounts.google.com/gsi/client` **synchrone**, 267 Ko (82 % inutilisés) + script Site Kit | Désactiver la connexion Google dans Site Kit si elle ne sert pas |
| 5 | **Royal Elementor Addons** | `frontend.min.css` **438 Ko, 98 % inutilisés** ; `particles`, `jarallax`, `parallax`, `perfect-scrollbar` (×2), `modal-popups` synchrones | Retirés sur l'accueil (mu-plugin) ; à terme, reconstruire l'en-tête et le pied de page en Elementor natif |
| 6 | **Widget de rappel Zadarma** | 9 requêtes, 133 Ko, dont un **second jQuery 3.5.1** et `jssip.min.js` 276 Ko (88 % inutilisés) | Charger le widget au clic sur un bouton « Être rappelé », jamais au chargement |
| 7 | **xpay.sh (Agentic Commerce)** | **6 scripts synchrones dans le `<head>`** + `storefront.js` + un appel `execute-api.amazonaws.com` | Désactiver si non indispensable, sinon charger en `defer` |
| 8 | **ProfilePress (`wp-user-avatar`)** | flatpickr, select2 et frontend : 268 Ko décompressés, **100 % inutilisés** sur l'accueil | Retirés sur l'accueil (mu-plugin) |
| 9 | **Polices en surnombre** | Google Fonts Open Sans + Oswald (plugin d'avis) ; Poppins, Roboto, Roboto Slab locales (`roboto.css` 105 Ko, 100 % inutilisé) ; polices du thème **« howes »** (65 Ko, thème qui n'est pas le thème actif) ; Font Awesome complet ; Typekit | Pile système sur la nouvelle page ; supprimer le thème « howes » s'il n'est plus utilisé |
| 10 | **Scripts tiers au chargement** | CookieYes 84 Ko, Clarity, Cloudflare Turnstile (utile seulement sur les formulaires), Alma (5 CSS sur l'accueil), menu accordéon WPB | Turnstile et Alma limités aux pages concernées ; tiers via GTM avec consentement |
| 11 | **jQuery chargé en `async`** | `jquery-core` et `jquery-migrate` en `async`, alors que des scripts synchrones en dépendent | Risque d'erreurs JS aléatoires : remettre jQuery en synchrone ou tout passer en `defer` |
| 12 | **DOM trop lourd** | 2 517 nœuds, profondeur 35, 106 balises H2 (menus) | Nouvelle page : 477 nœuds, profondeur 11 |

## 3. SEO

| Constat | Statut | Action |
|---|---|---|
| `www` → sans `www`, `http` → `https` | ✅ Redirections 301 en place | Rien à faire |
| `prod.la-maison-du-dos.com` | ✅ `noindex, nofollow` | Garder tant que ce n'est pas le domaine principal |
| **Anciennes URL de la boutique précédente** (`/la-maison-du-dos-m-15.html`, `/hefel-m-16.html`, `/surmatelas-climacontrol-confort-xml-355_363-1017.html`…) | ❌ Encore indexées ; 301 vers le domaine sans `www` puis **404** | Plan de redirections 301 vers les pages équivalentes |
| `/my-wishlist/` sur le domaine principal | ❌ Indexable (`index, follow`) | Passer en `noindex` |
| `/vos-devis-en-cours/` | ✅ Déjà en `noindex` | Rien à faire |
| Titre `Accueil - La Maison du Dos` | ⚠️ « Accueil » n'apporte rien | `Lit à eau et matelas pour le mal de dos \| La Maison du Dos` |
| **3 balises H1** (nom de marque, slogan, paragraphe entier) | ⚠️ | Un seul H1 ciblé (nouvelle page) |
| Pas de `canonical` ni d'image Open Graph sur l'accueil `prod.` | ⚠️ | Ajoutés dans la nouvelle page |
| Code postal : **52270** (mentions légales) / 52230 (annuaires : Mappy, PagesJaunes) | ⚠️ Incohérent | Vérifier le code officiel et l'harmoniser partout (site, fiche Google, annuaires). La maquette reprend 52270, comme le site |
| Prix affichés sans mention TTC (ex. 2 229,17 € = 2 675 € / 1,2) et règle CSS « sur le panier masquer le prix ttc » | ⚠️ À vérifier | En vente aux particuliers, les prix doivent être affichés TTC |

## 4. Plan d'action

1. **Sauvegarder** le site (fichiers + base).
2. Installer `wordpress/mu-plugins/lmdd-performance.php` sur `prod.`, ouvrir l'accueil avec `?lmdd_assets=1` (connecté en
   admin) et confirmer les handles ; tester l'accueil connecté **et** déconnecté.
3. Traiter les causes 1 à 4 : ce sont les plus gros gains (Gutenberg, cache, Google en triple, connexion Google).
4. Intégrer la nouvelle page d'accueil (conversion JSON Elementor).
5. SEO : redirections 301 des anciennes URL, `noindex` de la liste de souhaits, harmonisation du code postal.
6. **Re-mesurer** : `npm run audit -- https://prod.la-maison-du-dos.com/` et PageSpeed Insights.
