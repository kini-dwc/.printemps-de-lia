# Rapport de sécurité — prod.la-maison-du-dos.com

Audit réalisé le 20 octobre 2026, à la demande et avec l'autorisation du propriétaire du site, sur le sous-domaine de test **prod.la-maison-du-dos.com**.

**Nature de l'audit :** contrôle non destructif (reconnaissance externe, revue de configuration, revue du code des extensions LMDD). Aucune donnée client n'a été extraite, aucun mot de passe réellement forcé, aucune attaque par surcharge menée. Le forçage de mot de passe a été évalué par l'analyse des voies d'attaque ouvertes, pas par une exécution massive (voir § Authentification).

**Bonne nouvelle d'ensemble :** le site est globalement bien protégé. Wordfence (pare-feu + sécurité de connexion) est actif et coupe les requêtes qui ressemblent à des attaques. Les pages privées ne sont pas mises en cache, l'énumération des comptes par les voies habituelles est bloquée, et le code des extensions LMDD est sain. Les points à corriger sont surtout des réglages.

---

## Synthèse par gravité

| Gravité | Point | Correction |
|---|---|---|
| **Important** | `xmlrpc.php` actif avec `system.multicall` et `pingback.ping` | Désactiver XML-RPC (ou au moins pingback + multicall) |
| **Moyen** | Listage de répertoires activé (`/wp-includes/`, `/wp-content/upgrade/`) | Désactiver le listage (Options -Indexes) |
| **Moyen** | Aucun en-tête de sécurité HTTP (HSTS, CSP, X-Frame-Options…) | Ajouter les en-têtes |
| **Faible** | Version de WordPress visible (`readme.html`, `license.txt`, meta generator) | Supprimer/bloquer ces fichiers, masquer la version |
| **Faible** | Endpoint REST « elementor-mcp-composer » exposé | Vérifier son utilité, le restreindre |
| **Pour mémoire** | Points déjà conformes | — |

---

## 1. Important — XML-RPC ouvert (forçage groupé, amplification, SSRF)

**Constat.** `https://prod.la-maison-du-dos.com/xmlrpc.php` répond aux requêtes POST et expose notamment :
- `system.multicall` : permet de tester **des centaines d'identifiants en une seule requête**, ce qui contourne la limitation « X tentatives par minute » d'une page de connexion classique ;
- `wp.getUsersBlogs` : la méthode visée pour deviner les mots de passe ;
- `pingback.ping` : détournable pour des attaques par rebond vers d'autres sites (participation involontaire à un déni de service) et pour sonder le réseau interne du serveur (SSRF).

**Risque.** C'est la voie de forçage de mot de passe la plus exploitée contre WordPress. Même avec Wordfence, le forçage groupé par `multicall` peut passer sous le radar de certaines règles. `pingback` expose par ailleurs le serveur à un usage abusif par des tiers.

**Pourquoi le forçage n'a pas été exécuté en masse.** Le constat « la voie est ouverte » suffit : la lancer réellement n'aurait rien prouvé de plus et aurait risqué de bloquer le compte administrateur et de faire bannir l'adresse par l'hébergeur mutualisé o2switch.

**Correction (au choix) :**
- **Wordfence → Firewall → Brute Force Protection :** activer « Disable XML-RPC authentication » et désactiver les pingbacks ; ou
- **.htaccess** à la racine :
  ```apache
  # Bloquer XML-RPC
  <Files xmlrpc.php>
    Require all denied
  </Files>
  ```
- Vérifier qu'aucun service légitime n'en dépend (application mobile Jetpack/WordPress, publication à distance). Le site n'en a a priori pas besoin.

---

## 2. Moyen — Listage de répertoires activé

**Constat.** Les dossiers suivants affichent la liste de leur contenu :
- `https://prod.la-maison-du-dos.com/wp-includes/` → « Index of /wp-includes »
- `https://prod.la-maison-du-dos.com/wp-content/upgrade/`

`/wp-content/plugins/`, `/themes/` et `/uploads/` ne listent pas (le pare-feu bloque même la tentative sur `/plugins/`).

**Risque.** Facilite le repérage des composants et des versions par un attaquant. Fuite d'information.

**Correction.** Ajouter en haut du `.htaccess` racine :
```apache
Options -Indexes
```
(ou activer l'option correspondante chez o2switch / dans Wordfence).

---

## 3. Moyen — En-têtes de sécurité HTTP absents

**Constat.** Aucun des en-têtes de sécurité recommandés n'est envoyé (ni sur l'accueil, ni sur la page de connexion) :
- `Strict-Transport-Security` (HSTS) — manquant
- `Content-Security-Policy` — manquant
- `X-Frame-Options` — manquant (risque de « clickjacking »)
- `X-Content-Type-Options: nosniff` — manquant
- `Referrer-Policy` — manquant
- `Permissions-Policy` — manquant

La redirection HTTP → HTTPS fonctionne (301), et les pages de connexion/compte sont correctement en « no-store ».

**Correction.** Ajouter dans le `.htaccess` racine :
```apache
<IfModule mod_headers.c>
  Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  Header always set X-Content-Type-Options "nosniff"
  Header always set X-Frame-Options "SAMEORIGIN"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>
```
`Content-Security-Policy` est plus délicat à cause d'Elementor, de GTM et des scripts externes : à mettre en place progressivement, en mode « Report-Only » d'abord. À **ne pas activer HSTS sur le sous-domaine de test si vous ne voulez pas le forcer en HTTPS durablement** ; sur le domaine principal, c'est recommandé.

---

## 4. Faible — Version de WordPress et de ses composants visible

**Constat.**
- `readme.html` et `license.txt` sont accessibles publiquement.
- Le code source de l'accueil affiche « WordPress 7.1.3 » (balise generator) et le thème « royal-elementor-kit ».

**Risque.** Permet de cibler les failles connues de la version exacte. Information seule : à corriger mais sans urgence.

**Correction.**
- Supprimer ou bloquer `readme.html` et `license.txt`.
- Masquer la version : `remove_action('wp_head','wp_generator');` (ou via Wordfence / un réglage de thème).
- Surtout : **garder WordPress, les extensions et le thème à jour**, c'est la meilleure protection contre les failles connues.

---

## 5. Faible — Endpoint REST « elementor-mcp-composer »

**Constat.** `https://prod.la-maison-du-dos.com/wp-json/elementor-mcp-composer/v1.0.19` répond sans authentification (il décrit ses routes). Il s'agit d'un connecteur d'Elementor pour l'IA (MCP).

**Risque.** À vérifier : si ses actions sont réellement accessibles sans être connecté, cela pourrait permettre de lire ou modifier du contenu. L'audit n'a pas testé ses actions pour rester non intrusif.

**Correction.** Vérifier l'utilité de cette extension ; si elle n'est pas utilisée, la désactiver. Si elle l'est, s'assurer que ses actions exigent une authentification.

---

## Points déjà conformes (à conserver)

- **Wordfence actif** : le pare-feu coupe les requêtes d'attaque (énumération des extensions, accès à `.htaccess`). La sécurité de connexion Wordfence est disponible — **activer l'authentification à deux facteurs (2FA)** sur les comptes administrateurs est vivement conseillé.
- **Énumération des comptes bloquée** : `/wp-json/wp/v2/users` → 401 ; `/?author=1` → 404.
- **Pages privées non mises en cache** : `/cart/`, `/checkout/`, `/my-account/` sont en « private / no-store » → pas de fuite de données entre visiteurs.
- **Données des demandes clients protégées** : le dossier `wp-content/uploads/lmdd-demandes/` n'est pas listable et son `.htaccess` interdit l'exécution de PHP ; les fichiers portent des noms aléatoires.
- **Redirection HTTPS** en place (301).

## Revue du code des extensions LMDD (Devis & SAV, Import)

Aucune faille relevée :
- **Droits et jetons** : tous les traitements d'administration (`admin-post`, AJAX) vérifient les droits (`manage_options` / `edit_post`) **et** un jeton de sécurité (nonce).
- **Entrées** : les champs publics des formulaires sont nettoyés (`sanitize_text_field`, `sanitize_textarea_field`) ; le champ caché « configuration » est tronqué.
- **Sorties** : échappement systématique (`esc_html`, `esc_attr`, `esc_url`) dans les cartes produits, le détail des demandes et l'affichage des options de commande.
- **Base de données** : la seule requête SQL directe (tri du catalogue) n'utilise que des identifiants convertis en entiers et des noms de tables internes — pas d'injection possible.
- **Envoi de fichiers** (photos SAV) : extensions limitées à une liste sûre, noms aléatoires, dossier protégé.

---

## Recommandations prioritaires (dans l'ordre)

1. **Désactiver XML-RPC** (point 1) — le plus important pour le forçage de mot de passe.
2. **Activer la 2FA** sur les comptes administrateurs (Wordfence → Login Security).
3. **Désactiver le listage de répertoires** (point 2).
4. **Ajouter les en-têtes de sécurité** (point 3).
5. Supprimer `readme.html` / `license.txt` et masquer la version (point 4).
6. Vérifier l'endpoint MCP d'Elementor (point 5).
7. Maintenir WordPress, les extensions et le thème à jour.

Une fois ces réglages appliqués sur le sous-domaine, je peux refaire une passe de vérification, puis vous accompagner pour reporter les mêmes réglages sur le domaine principal.
