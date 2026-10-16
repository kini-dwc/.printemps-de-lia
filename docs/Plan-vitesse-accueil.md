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
1. *WPCode → Ajouter un extrait → Code personnalisé → Extrait PHP*, puis coller le contenu de `wordpress/snippets/lmdd-vitesse-images.php` (sans la première ligne `<?php`).
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

### 5. Asset Pilot : retirer ce qui ne sert pas sur chaque page (20 min)
Créer des règles, en test d'abord, puis en ligne :

| Ressource | Portée | Exceptions |
|---|---|---|
| `cloudflare-turnstile` | tout le site | fiches produits, page SAV, page contact |
| `alma-widget`, `alma-widget-cdn` | tout le site | fiches produits, panier, commande |
| `wpb_wmca_*` (menu accordéon) | tout le site | boutique et catégories, si c'est là qu'il sert |
| `prisna-wp-translate` | à décider | si le sélecteur de langue n'est pas utilisé, désactiver l'extension |

### 6. Nettoyer les extraits WPCode obsolètes (15 min)
- Désactiver les extraits qui gèrent l'ancien formulaire de devis (`quote-product`, `devis_produit_nom`, `#trigger-quote-btn`) et le masquage du prix ou du bouton « sur devis ».
- Ensuite, vérifier une catégorie « sur devis » et une fiche produit.

### 7. Prestataires externes
- **xpay** : demander de retirer `nowprocket` de `storefront.js`, ou de le charger après la première interaction.
- **Zadarma** : le report JavaScript (étape 2) s'en occupe.

### 8. Images (gain faible)
Recompresser `akva_carla-1.webp` (104 Ko, environ 60 Ko possible), par exemple avec Imagify.

## Objectif
Après les étapes 1 à 4 : **mobile 80 à 90**, **ordinateur 90 et plus**.

Refaire PageSpeed après chaque étape (vider le cache WP Rocket avant) pour mesurer son effet.
