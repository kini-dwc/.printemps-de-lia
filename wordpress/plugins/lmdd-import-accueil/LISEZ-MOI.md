# LMDD – Import de la nouvelle page d'accueil, de l'en-tête et du pied de page

Crée la nouvelle page d'accueil, l'en-tête, le pied de page et le menu **sans passer par l'import de fichiers d'Elementor**
(qui échouait sur le serveur).

## Installer et importer

1. *Extensions → Ajouter → Téléverser une extension* → `lmdd-import-accueil.zip` → *Installer* → *Activer*.
   Si la version 1.0 est déjà installée : WordPress propose de **remplacer la version actuelle** → acceptez.
2. *Outils → Import accueil LMDD* → **Lancer l'import** (quelques secondes). L'extension crée :
   - la page brouillon **« Accueil – nouvelle version »** (Elementor, widgets natifs modifiables) ;
   - les modèles « Accueil », « En-tête » et « Pied de page – La Maison du Dos » dans *Elementor → Modèles* ;
   - le menu **« Menu principal – La Maison du Dos »** (6 entrées, 37 liens avec les sous-menus), modifiable dans *Apparence → Menus* ;
   - l'**en-tête** et le **pied de page** dans le constructeur de thème de Royal Elementor Addons, **sans les activer** ;
   - les 7 images optimisées dans *Médias*, avec leurs textes alternatifs, déjà placées.

Votre page d'accueil, votre en-tête et votre pied de page actuels **ne sont pas modifiés** à ce stade.

## En-tête et pied de page : aperçu, activation, retour arrière

Le cadre **« En-tête et pied de page »**, en bas de la page de l'outil, propose :

| Bouton | Effet |
|---|---|
| **Aperçu sur la page d'accueil actuelle** / **avec la nouvelle page d'accueil** | Affiche le site avec le nouvel en-tête et le nouveau pied de page, **pour vous seul**. Fonctionne sur toute page : ajoutez `?lmdd_hf_preview=1` à l'adresse |
| **Modifier l'en-tête / le pied de page avec Elementor** | Ouvre le modèle dans l'éditeur |
| **Afficher sur tout le site** | Remplace les conditions d'affichage de Royal (*Royal Addons → Constructeur de thème*). Les anciennes sont **sauvegardées** |
| **Revenir à l'en-tête et au pied de page d'origine** | Rétablit les conditions sauvegardées : l'ancien en-tête revient immédiatement |

Après l'activation ou le retour arrière, l'extension vide le cache d'Elementor (et celui de WP Rocket, LiteSpeed, W3 Total Cache
ou WP Super Cache s'ils sont présents). Si votre hébergeur a son propre cache, videz-le aussi.

## Contenu de l'en-tête

| Rangée | Ordinateur | Tablette et mobile |
|---|---|---|
| Barre du haut (vert foncé) | Livraison gratuite dès 60 € · téléphone · WhatsApp · horaires | Livraison + téléphone |
| Principale | Logo · recherche · bouton « Demander un devis » · souhaits · compte · panier | Logo · (souhaits) · compte · panier · menu burger |
| Menu | Lits à eau, Matelas réglables, Linge de lit, Accessoires & SAV, Nos marques (avec sous-menus), Contact | Dans le menu burger |
| Recherche | Dans la rangée principale | Rangée dédiée, pleine largeur |

Widgets : Elementor gratuit (image, icône, liste d'icônes, bouton) et Royal Elementor Addons gratuit, déjà installé sur le site
(*Menu*, *Recherche*, *Mini panier*). Avec Royal Addons **Pro**, vous pouvez en plus : limiter la recherche aux produits
(*Recherche → Requête : Produits*), afficher le contenu du panier en panneau latéral (*Mini panier → Contenu*), élargir les sous-menus.

## Bon à savoir

- Chaque étape est une petite requête : pas de dépassement du temps maximal de l'hébergeur. Relançable sans doublon.
  **Attention** : relancer l'import remet les modèles et le menu dans leur état d'origine (vos retouches dans Elementor seraient perdues).
- Si Royal Elementor Addons n'est pas actif, l'en-tête et le pied de page restent dans *Elementor → Modèles*.
- L'extension peut être désactivée une fois l'en-tête activé : la page, les modèles, le menu et les images restent.
  Gardez-la si vous voulez conserver l'aperçu et le bouton « Revenir ».

Testé sur WordPress 7.1.2, Elementor 4.3.2, Royal Elementor Addons 1.7 (thème Royal Elementor Kit) et WooCommerce 11.1.2 :
import complet en 3 s, aucun débordement de 390 à 1366 px, aucune erreur JavaScript, éditeur Elementor fonctionnel.
Reconstruire le paquet : `./elementor/package-import-plugin.sh`.
