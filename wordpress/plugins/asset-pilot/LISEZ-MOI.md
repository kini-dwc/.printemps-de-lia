# Asset Pilot – Gestionnaire de scripts

Extension WordPress pour **désactiver les scripts et feuilles de style inutiles**, page par page ou sur tout le site,
**tester sans impacter les visiteurs**, et **mesurer l'impact** sur la vitesse et le bon fonctionnement.
Générique : fonctionne sur n'importe quel site WordPress (avec ou sans WooCommerce / Elementor).

Testée sur WordPress 7.1.2, WooCommerce 11.1.2 et Elementor 4.3.2, avec PHP 8.4.

## Installation

1. Compressez le dossier `asset-pilot` en `.zip`, ou utilisez `asset-pilot.zip` s'il vous a été fourni.
2. *Extensions → Ajouter → Téléverser une extension*, puis activez-la.
3. Ouvrez *Outils → Asset Pilot*.

## Principe : rien ne change pour les visiteurs tant que vous n'avez pas validé

Chaque règle a un **statut** :

| Statut | Qui est concerné |
|---|---|
| **Test** (par défaut) | Vous seul : administrateur avec l'aperçu activé, ou navigateur ouvert avec le lien de test |
| **En ligne** | Tous les visiteurs |

Une règle = une ressource (handle JS ou CSS) + une **portée** + des **exceptions** éventuelles :

- **Portées** : tout le site, page d'accueil, un type de contenu (pages, articles, produits…), une page précise,
  fiches produits, boutique et catégories, panier, commande, mon compte, archives, recherche, 404.
- **Exceptions** : par exemple « tout le site **sauf** panier et commande ».
- **Forcer** : sans cette option, une ressource dont une autre dépend reste chargée (comportement de WordPress, l'interface l'indique :
  « réintroduit »). Avec « Forcer », elle est retirée **avec** les ressources qui en dépendent (listées avant validation).

## Les 3 façons de travailler

1. **Depuis le site** : le menu **« Scripts »** de la barre d'administration liste les ressources de la page affichée
   (groupées par extension, avec leur poids et leurs dépendances). Un clic suffit pour désactiver en test ou réactiver.
2. **Depuis l'administration** (*Outils → Asset Pilot*) :
   - **Ressources** : inventaire de tout le site, filtres, scan des pages ;
   - **Règles** : liste des règles, mise en ligne, retour en test, suppression, export/import JSON ;
   - **Tester l'impact** : comparaison automatique sans règles / avec règles ;
   - **Mode test & sécurité** : aperçu, lien de test, procédure d'urgence.
3. **En ligne de commande** (parcours clients) : voir « Tester l'impact », ci-dessous.

## Tester l'impact (V2)

### Dans l'interface
L'onglet **« Tester l'impact »** charge chaque page choisie deux fois : sans aucune règle, puis avec les règles en ligne et en test.
Pour chaque page, il compare :
- les **erreurs JavaScript** nouvelles (message, fichier, ligne) ;
- les **ressources en échec** ;
- les **éléments critiques** : sélecteurs CSS configurables (bouton « Ajouter au panier », « Commander », menu…) qui ne doivent pas disparaître ;
- le **poids**, le **nombre de requêtes**, les **fichiers JS** et le **temps d'affichage (LCP)**.

Verdict par page : *OK*, *À surveiller* ou *Régression*. Chaque vérification est conservée dans l'historique (20 dernières).

### En ligne de commande : parcours clients
`tools/asset-pilot-tester/run.mjs` (Node.js + Playwright) joue en plus de **vrais parcours**, dans les deux modes :
ajout au panier, affichage de la commande et des moyens de paiement (sans payer), et scénarios personnalisés
(clic, saisie, élément attendu…), sur ordinateur et sur mobile.

```bash
npm install && npx playwright install chromium
cp tools/asset-pilot-tester/config.exemple.json tools/asset-pilot-tester/config.json   # renseigner baseUrl + jeton
node tools/asset-pilot-tester/run.mjs --config tools/asset-pilot-tester/config.json
```

- Le jeton se copie depuis *Mode test & sécurité* (ou via la variable `ASSET_PILOT_TOKEN`).
- Rapports Markdown et JSON dans `tools/asset-pilot-tester/rapports/`, également envoyés à l'historique de l'extension.
- Code de sortie 1 en cas de régression : utilisable avant chaque mise en ligne ou en intégration continue.
- `--baseline live` compare aux règles déjà en ligne au lieu de « sans aucune règle » ; `--headed` affiche le navigateur.

## Sécurité et garde-fous

- **Jamais appliqué** dans : l'administration, AJAX, l'API REST, l'outil de personnalisation, les éditeurs visuels
  (Elementor, Beaver Builder, Divi, Oxygen, Bricks, WPBakery, Thrive) et les flux.
- **Mode test** : les pages ne sont ni mises en cache ni servies depuis le cache (DONOTCACHEPAGE, en-têtes no-cache,
  cookie exclu du cache WP Rocket).
- **Cache vidé automatiquement** à chaque changement de règle : WP Rocket, LiteSpeed, W3 Total Cache, WP Super Cache,
  Autoptimize, Breeze, plus le crochet `asset_pilot_purge_caches` pour les autres.
- `?ap_off=1` et `?ap_probe=1` n'ont d'effet que pour un administrateur ou avec le jeton : un visiteur ne peut pas
  s'en servir pour contourner le cache.
- Les ressources critiques (jQuery, WooCommerce panier/commande, Elementor…) sont signalées « sensible ».
- Les règles importées arrivent toujours en test.

### En cas de problème
1. Connecté en administrateur, ajoutez `?ap_off=1` à l'adresse : la page s'affiche sans aucune règle.
2. Repassez les règles en test : les visiteurs retrouvent immédiatement le site d'origine.
3. En dernier recours, dans `wp-config.php` : `define( 'ASSET_PILOT_DISABLE', true );`

## Données enregistrées

Options WordPress `asset_pilot_rules`, `asset_pilot_settings`, `asset_pilot_assets`, `asset_pilot_urls` et `asset_pilot_reports`,
plus la méta utilisateur `asset_pilot_preview`. Tout est supprimé à la désinstallation.

## Limites connues

- Agit sur les ressources déclarées proprement (`wp_enqueue_script` / `wp_enqueue_style`). Les scripts écrits en dur
  dans le HTML par un thème ou une extension ne sont pas gérés.
- La désactivation d'**extensions entières** par page (gain côté serveur) n'est pas incluse : prévue pour une version ultérieure.
- La vérification dans l'interface ne rejoue pas les parcours (clics, panier) : c'est le rôle de l'outil en ligne de commande.
