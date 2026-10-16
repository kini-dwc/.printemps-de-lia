# Vitesse de la page d'accueil – plan d'action (16 octobre 2026)

PageSpeed (la-maison-du-dos.com) : **mobile 42**, ordinateur 68.

| Mesure | Mobile | Ordinateur |
|---|---|---|
| First Contentful Paint (premier affichage) | 3,2 s | 0,4 s |
| Largest Contentful Paint (LCP, plus grand élément affiché) | 13,7 s | 1,0 s |
| Total Blocking Time (TBT, temps où la page ne répond pas) | 860 ms | 1 080 ms |
| Cumulative Layout Shift (stabilité de la mise en page) | 0,001 | 0,026 |

Le serveur répond vite : environ 180 ms, avec le cache de WP Rocket. Ce qui ralentit la page, c'est **ce qu'elle charge**.

## Mesures faites sur la page en ligne

Mesures faites avec un mobile simulé (processeur ralenti 4 fois). Chaque scénario a été mesuré deux fois, et le tableau donne la moyenne.

| Scénario | Temps bloqué | Poids |
|---|---|---|
| Page actuelle | ≈ 1 950 ms | 2,1 Mo, 124 requêtes |
| Sans les scripts tiers (Google, Zadarma, xpay, Cloudflare, Clarity, CookieYes) | **≈ 330 ms** | **0,85 Mo** |

**Environ 85 % du temps bloqué et 60 % du poids viennent des scripts tiers.**

### Scripts Google : 927 Ko pour 5 fichiers

| Script | Chargé par | Remarque |
|---|---|---|
| `gtm.js` GTM-PF26W689 | Site Kit | conteneur Tag Manager |
| `gtag` **G-HH41XH2SV1** | code écrit en dur dans l'en-tête (WPCode ou thème) | **déjà chargé une seconde fois par GTM** : pages vues probablement comptées deux fois |
| `gtag` G-HH41XH2SV1 (`&gtm=`) | GTM | doublon du précédent |
| `gtag` **G-VJSQ20QJ4W** | Site Kit (Analytics) | une **deuxième propriété GA4** |
| `gtag` AW-969053143 | GTM | Google Ads (Pixel Manager déclare aussi ce compte) |
| `accounts.google.com/gsi/client` (101 Ko) | Site Kit « Se connecter avec Google » | chargé sur **toutes** les pages |

### Autres causes trouvées

- **WP Rocket** : le cache, le report des scripts (« defer ») et la minification CSS sont actifs.
  - **Non actifs** : « Reporter l'exécution JavaScript » (Delay JS) et « Supprimer les CSS inutilisés » (RUCSS).
  - D'où 28 feuilles de style qui bloquent l'affichage, dont `frontend.min.css` de Royal (45 Ko, 44 Ko inutilisés).
- **Image principale (ordinateur)** : `akva_carla-1.webp` est l'élément LCP, mais elle est en `loading="lazy"`, donc chargée en dernier.
  - Elementor ne charge tout de suite que les 3 premières images de la page.
  - Ces 3 places étaient prises par le logo et 2 images des méga-menus, invisibles.
- **Méga-menus** : les images « Lits à eau » (1040 × 910 px) et « Matelas » sont téléchargées dès l'ouverture de la page, même sur mobile, alors qu'elles sont cachées.
- **Scripts chargés pour rien sur l'accueil** :
  - Turnstile (Cloudflare, 28 Ko) : aucun formulaire sur la page ;
  - Alma (CSS de paiement en plusieurs fois) ;
  - menu accordéon WPB ;
  - Prisna Translate (24 Ko de JavaScript, plus jQuery UI et 20 Ko de code en ligne) ;
  - émojis WordPress.
- **Anciens extraits jQuery** (WPCode) exécutés sur toutes les pages : masquage du prix à 0 €, remplissage `quote-product` / `devis_produit_nom`, et masquage du bouton panier « sur devis ». L'extension LMDD Devis & SAV fait désormais ce travail côté serveur.
- **xpay** (`storefront.js`, `pill.js`, `track.js`, 65 Ko) : marqué `nowprocket`, WP Rocket ne peut donc pas le reporter. Il provoque aussi 82 ms de recalcul de mise en page.
- **Zadarma** (bouton de rappel, 86 Ko dont 63 Ko de bibliothèque WebRTC) : chargé immédiatement depuis un widget HTML de l'en-tête.

## Plan d'action, dans l'ordre

### 1. Correctif LCP : extrait WPCode fourni (5 min) – gain : LCP ordinateur, et les images cachées ne sont plus téléchargées
1. *WPCode → Ajouter un extrait → Code personnalisé → Extrait PHP*, puis coller le contenu de `wordpress/snippets/lmdd-vitesse.php` (sans la première ligne `<?php`).
2. Insertion : *Exécuter partout*. Activer.
3. *Elementor → Outils → « Vider les fichiers et les données »*. C'est indispensable : le cache des éléments Elementor garde l'ancien HTML.
4. *WP Rocket → Vider le cache*.

Effet :
- le logo est chargé normalement ;
- les images des méga-menus et du pied de page sont différées ;
- la première grande image du contenu passe en `fetchpriority="high"`, sans `lazy` ;
- les émojis WordPress sont retirés.

Méga-menus « Lits à eau » et « Matelas réglables » : sur le widget Image, réglez *Taille de l'image* sur **Medium Large (768)** au lieu de *Complète*. C'est déjà le réglage par défaut dans l'extension d'installation.

### 2. WP Rocket : reporter l'exécution JavaScript (10 min + tests) – le plus gros gain sur le temps bloqué
1. *WP Rocket → Fichiers → JavaScript → « Reporter l'exécution JavaScript »* : activer.
2. **CookieYes est chargé par GTM** : si GTM est reporté, la bannière n'apparaît qu'au premier geste (défilement, toucher). Deux possibilités :
   - **Simple** : accepter ce comportement. Aucun cookie n'est déposé avant ce geste, ce qui reste conforme.
   - **Recommandé** : charger CookieYes directement (script fourni par CookieYes, collé dans WPCode en en-tête) et l'exclure du report avec la ligne `cdn-cookieyes.com`.
3. Tester ensuite :
   - méga-menus et menu mobile ;
   - ajout au panier ;
   - formulaire de devis sur une fiche produit ;
   - formulaire SAV ;
   - commande jusqu'au choix du paiement ;
   - bouton de rappel Zadarma.

### 3. Google : un seul chargement par outil (30 min, avec la personne qui gère Google Ads et Analytics)
1. **Supprimer le code G-HH41XH2SV1 écrit en dur** (WPCode → extrait d'en-tête, ou *Apparence → Éditeur de fichiers → header* du thème). GTM le charge déjà.
2. **Choisir une seule propriété GA4** :
   - si **G-HH41XH2SV1** (dans GTM) est la bonne : *Site Kit → Réglages → Analytics* → sélectionner cette propriété et **désactiver « Placer l'extrait de code »** ;
   - sinon, faire l'inverse.
3. *Site Kit → Se connecter avec Google* : **désactiver One Tap sur toutes les pages**, ou le module entier s'il ne sert pas. Gain : 101 Ko sur chaque page.
4. *Site Kit → AdSense* : déconnecter le module s'il n'y a pas de publicité AdSense sur le site.
5. Vérifier que la conversion Google Ads (AW-969053143) n'est envoyée qu'une fois : soit par GTM, soit par Pixel Manager.

Résultat attendu : 3 scripts Google au lieu de 6, environ 400 Ko de moins, et des statistiques justes.

### 4. WP Rocket : supprimer les CSS inutilisés (10 min + tests) – gain : premier affichage mobile
1. *WP Rocket → Fichiers → CSS → « Optimiser la livraison du CSS » → « Supprimer les CSS inutilisés »*.
2. *Liste de sécurité CSS* : `.wpr-mobile-(.*)`, `.wpr-sub-mega-menu(.*)`, `.lmdd-(.*)`, `.wpcf7(.*)`, `.is-sent`, `.cky-(.*)`.
3. Refaire les tests de l'étape 2, sur ordinateur et sur mobile.

### 5. Scripts inutiles et FAQ : même extrait WPCode (5 min)
Inclus dans le même extrait `wordpress/snippets/lmdd-vitesse.php`. Il reprend ce que faisaient les règles Asset Pilot du site, ce qui permet de se passer de cette extension.
- **ProfilePress** n'est chargé que sur « Mon compte » et les pages qui contiennent un de ses formulaires.
- **Effets Royal non utilisés** (particules, parallaxe, texte animé) : retirés partout.
- **Font Awesome et le bloc Prisna** : retirés de l'accueil. Font Awesome reste sur les fiches produits et la page livraison, qui l'utilisent.
- **Turnstile** est retiré de l'accueil, de la boutique, des catégories, du panier et de la commande. Il reste partout où il y a un formulaire.
- **Alma (CSS)** n'est chargé que sur les fiches produits, le panier et la commande.
- **FAQ en accordéon** : le titre n'est plus un lien sans adresse, ce qui corrige l'audit SEO « liens non explorables ». Il reste utilisable au clavier.
- Prisna Translate sert au sélecteur de langue en haut à droite : il est conservé, et le report JavaScript s'en occupe.

### 6. Nettoyer les extraits WPCode obsolètes (15 min)
- Désactiver les extraits qui gèrent l'ancien formulaire de devis (`quote-product`, `devis_produit_nom`, `#trigger-quote-btn`) et le masquage du prix ou du bouton « sur devis ».
- Ensuite, vérifier une catégorie « sur devis » et une fiche produit.

### 7. Prestataires externes
- **xpay** ne doit pas être optimisé par WP Rocket. `storefront.js` porte déjà `nowprocket` : WP Rocket n'y touche pas, même avec le report JavaScript.
  - Pour exclure aussi le reste d'xpay, ajouter ces lignes dans *WP Rocket → Fichiers → JavaScript* : `agentic-commerce-for-woocommerce`, `xpay`, `widget.xpay.sh`.
  - Elles vont dans « Fichiers JavaScript exclus » (minification et chargement différé) et dans « Fichiers exclus » du report JavaScript.
- **Zadarma** : le report JavaScript (étape 2) s'en occupe.

### 8. Images (gain faible)
Recompresser `akva_carla-1.webp` (104 Ko, environ 60 Ko possible), par exemple avec Imagify.

## Objectif
Après les étapes 1 à 4 : **mobile 80 à 90**, **ordinateur 90 et plus**.

Refaire PageSpeed après chaque étape (vider le cache WP Rocket avant) pour mesurer son effet.

## Résultat après l'étape 1 (16 octobre, 14 h 25)
| | Mobile | Ordinateur |
|---|---|---|
| Score de performance | 42 → **79** | 68 → **73** |
| LCP | 13,7 s → **2,9 s** | 1,0 s → **0,7 s** |
| Temps bloqué | 860 → **440 ms** | 1 080 → **710 ms** |

Dans la page en ligne, les points suivants ne sont pas encore faits :
- le report JavaScript ;
- la suppression des CSS inutilisés (1,8 s de blocage du rendu sur mobile) ;
- les doublons Google (G-HH41 écrit en dur, G-VJSQ de Site Kit, « Se connecter avec Google ») ;
- l'ancien extrait `quote-product`.

**Anomalie trouvée** : sur les fiches produits, le script Turnstile n'est pas chargé, alors que le formulaire de devis contient le widget.
- Contact Form 7 refuse alors l'envoi, comme si c'était du spam.
- Sur la copie de test, le script se charge bien : c'est un réglage de la production qui le retire sur les pages WooCommerce (règle Asset Pilot, extrait WPCode ou autre extension).

## Résultat après la suppression d'Asset Pilot (16 octobre, 15 h 01)
| | Mobile | Ordinateur |
|---|---|---|
| Score | 79 → **53** | 73 → **96** |
| LCP | 2,9 s → **13,5 s** | 0,7 s → **0,9 s** |
| Temps bloqué | 440 → **280 ms** | 710 → **130 ms** |

- **Cause du recul sur mobile** : les règles d'Asset Pilot retiraient déjà une quinzaine de fichiers (Font Awesome, ProfilePress, Roboto, Alma, effets Royal…). En supprimant l'extension, ces fichiers sont revenus. Il y a désormais 37 feuilles de style au lieu de 28, et elles bloquent l'affichage sur mobile.
- **Correction** : l'extrait unique retire désormais ces fichiers.
- **Anomalie Turnstile résolue** : une règle d'Asset Pilot le retirait aussi des fiches produits. Depuis la suppression, Turnstile y est de nouveau chargé, et le formulaire de devis est envoyable.

## Mesure de 15 h 27 : mobile 57, ordinateur 81
- **Mobile** : le grand titre de l'accueil (le H1, qui est l'élément LCP sur mobile) est passé en police **Roboto** dans Elementor, ainsi que le titre « Conseil personnalisé ».
  - Le navigateur doit donc télécharger Roboto (7 graisses) avant de dessiner ce titre définitivement.
  - Sur une connexion 4G lente, cela repousse le LCP à 13–15 s. À 14 h 25, sans Roboto, il était de 2,9 s.
  - **Correction** : dans Elementor, mettre ces deux titres en police « Par défaut », comme le reste du texte. Dans *Paramètres du site → Typographie globale*, remplacer aussi Roboto (police par défaut d'Elementor).
- **Ordinateur** : le temps bloqué varie d'un test à l'autre (130 à 400 ms) selon les scripts Google, qui ne sont pas encore traités (doublons, report JavaScript).
- **Variabilité de PageSpeed** : sur mobile, deux tests de la même page peuvent différer de 10 à 15 points.
  - Après chaque vidage de cache, ouvrir la page une fois, attendre une minute, puis faire 3 tests et garder le résultat du milieu.
