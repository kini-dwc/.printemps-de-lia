# LMDD – Installation : page d'accueil, en-tête (méga-menus), pied de page, fiche produit, page SAV

Crée la nouvelle page d'accueil, l'en-tête, le pied de page et le menu **sans passer par l'import de fichiers d'Elementor**
(qui échouait sur le serveur).

## Installer

1. *Extensions → Ajouter → Téléverser une extension* → `lmdd-import-accueil.zip` → *Installer*.
   Si une version précédente est installée, WordPress propose de **remplacer la version actuelle** → acceptez. Puis *Activer*.
2. *Outils → Import accueil LMDD* : **cochez ce que vous voulez installer**, puis **Installer la sélection**.

| Case | Ce qui est créé ou mis à jour |
|---|---|
| Page d'accueil | La page brouillon « Accueil – nouvelle version » et le modèle « Accueil » |
| En-tête, menu principal et méga-menus | Le modèle d'en-tête (constructeur de thème Royal), le menu « Menu principal – La Maison du Dos » et les **6 méga-menus** |
| Pied de page | Le modèle de pied de page (constructeur de thème Royal) |
| Fiche produit | Le nouveau modèle de fiche produit (constructeur de thème Royal, *Produit unique*), **sans l'activer** |
| Page SAV | La page brouillon « SAV lit à eau » (Elementor) avec les fiches symptômes et le formulaire en étapes |

**Fiche produit et page SAV** utilisent l'extension **LMDD – Devis & SAV** (`lmdd-devis-sav.zip`) : installez-la et activez-la **avant**.

Un élément **déjà installé est décoché par défaut** : le cocher le réinstalle et **remplace vos retouches** faites dans Elementor.
Exemple : pour mettre à jour seulement l'en-tête sans toucher à votre page d'accueil publiée, cochez uniquement « En-tête… ».

## Méga-menus

Mêmes onglets que l'en-tête actuel, dans le même ordre : **Lits à eau, Matelas réglables, Accessoires, Linge de lit, Couettes Hefel, Marques**.
Sur ordinateur, chaque onglet ouvre un méga-menu pleine largeur (widget *Méga-menu* de Royal Elementor Addons, version gratuite) :

| Onglet | Contenu |
|---|---|
| Lits à eau | 2 cartes (en ligne / sur devis) + photo et « Tous les lits à eau » |
| Matelas réglables | 2 cartes (léger / bébé, télécommande) + photo |
| Accessoires | 3 cartes (entretien / SAV, accessoires, têtes & cadres) + encart conseil avec le téléphone |
| Linge de lit | 3 colonnes (couettes, draps & protections, oreillers & surmatelas) + photo |
| Couettes Hefel | 5 colonnes (doubles 4 saisons, fibres animales, végétales, synthétiques, duvets : les 33 couettes du menu actuel) + catalogue PDF + promotion |
| Marques | Les 12 marques + encart « Toutes nos marques » |

Sur tablette et mobile, le menu burger affiche les mêmes onglets, avec leurs liens en accordéon.

**Modifier un méga-menu** : *Apparence → Menus* → menu « Menu principal – La Maison du Dos » → survolez l'onglet → bouton **Méga-menu** de Royal →
*Modifier*. Ou directement : les contenus « Méga-menu – … » s'ouvrent avec Elementor.

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
| Barre du haut (vert foncé) | Livraison gratuite dès 60 € · téléphone · WhatsApp · horaires · contact | Livraison + téléphone |
| Principale | Logo · recherche · bouton « Demander un devis » · souhaits · compte · panier | Logo · (souhaits) · compte · panier · menu burger |
| Menu | Lits à eau, Matelas réglables, Accessoires, Linge de lit, Couettes Hefel, Marques, avec méga-menus | Dans le menu burger, liens en accordéon |
| Recherche | Dans la rangée principale | Rangée dédiée, pleine largeur |

Widgets : Elementor gratuit (image, icône, liste d'icônes, bouton) et Royal Elementor Addons gratuit, déjà installé sur le site
(*Méga-menu*, *Menu*, *Recherche*, *Mini panier*). Avec Royal Addons **Pro**, vous pouvez en plus : limiter la recherche aux produits
(*Recherche → Requête : Produits*), afficher le contenu du panier en panneau latéral (*Mini panier → Contenu*), élargir les sous-menus.

## Fiche produit : aperçu, activation, retour arrière

Un seul modèle pour toutes les fiches, qui s'adapte tout seul :

| | Fiche sur devis | Fiche vendue en ligne |
|---|---|---|
| En-tête | Badge **Sur devis**, catégorie · marque, titre court (H1), note des avis | Catégorie · marque, titre court (H1), note des avis, **prix** |
| Achat | Points clés, options de configuration, **Demander un devis** → panneau | Points clés, options, **Ajouter au panier** |
| Réassurance | Rappel par un conseiller, livraison et installation, devis sur mesure | Livraison gratuite dès 60 €, facilités de paiement, conseil 7j/7 |
| Ensuite | Description et questions complémentaires (onglets), avis, produits similaires | idem |

- Fil d'Ariane en haut, images qui restent visibles pendant la lecture (ordinateur), barre « Demander un devis » fixe sur mobile.
- **Avis** : les codes courts de votre modèle actuel (Société des Avis Garantis…) sont **repris automatiquement**. Le message de
  l'étape d'installation les liste ; l'ancien bouton de devis n'est pas repris.
- Cadre **Fiche produit** en bas de la page de l'outil : *Aperçu : fiche sur devis* / *fiche vendue en ligne* (visible par vous seul),
  **Utiliser sur toutes les fiches produits**, puis **Revenir au modèle de fiche d'origine** si besoin.
- Pour de meilleures fiches : renseignez **Titre affiché (court)** et **Points clés** dans chaque produit (*Produit → Général*).
  Sans eux, la fiche affiche le nom complet du produit et pas de points clés.

## Page SAV

Créée en **brouillon** : *Modifier avec Elementor* ou *Prévisualiser* depuis le cadre « Page SAV ». Contenu :
- introduction et encart « De l'eau qui s'écoule, maintenant ? » (appel direct) ;
- **diagnostic** : accordéon Elementor natif, modifiable directement, avec les pièces et leur prix en direct ;
- marques prises en charge, entretien et garantie (renvoi vers les CGV pour les durées) ;
- **formulaire SAV** en étapes : formulaire Contact Form 7 « SAV lit à eau (LMDD) », modifiable dans *Contact → Formulaires*.

Publiez-la, puis ajoutez un lien « SAV » au menu ou au pied de page.
**Version 1 déjà installée ?** Cochez « Page SAV » pour la réinstaller avec l'accordéon et le formulaire Contact Form 7.

## En-tête sur les fiches produits (correction)

Royal n'affiche l'en-tête et le pied de page sur les pages « canevas », comme les fiches produits, que si l'option
**Afficher sur le canevas** est cochée dans le modèle. Les versions précédentes ne la cochaient pas. La version 1.3 la coche
**à chaque installation**, quels que soient les éléments cochés et **sans toucher au contenu** de votre en-tête et de votre pied de page.

## Supprimer l'extension une fois tout en place

**Oui, elle doit être supprimée** une fois l'installation terminée et validée : *Extensions → LMDD – Import… → Désactiver*, puis *Supprimer*.

- Tout ce qu'elle a créé **reste en place** (enregistré dans WordPress, Elementor et Royal Elementor Addons) : page d'accueil, en-tête,
  méga-menus, pied de page, menu, images et réglages d'affichage. Vérifié sur le site de test après suppression.
- La suppression n'efface que son propre suivi d'installation (une option WordPress).
- Vous perdez seulement l'aperçu `?lmdd_hf_preview=1` et le bouton « Revenir ». L'ancien en-tête et l'ancien pied de page restent
  disponibles dans *Royal Addons → Constructeur de thème*, où vous pouvez les réactiver à tout moment.
- Si vous la réinstallez plus tard, elle retrouve la page et les modèles existants (pas de doublon).

## Bon à savoir

- Chaque étape est une petite requête : pas de dépassement du temps maximal de l'hébergeur. Relançable sans doublon.
- Si Royal Elementor Addons n'est pas actif, l'en-tête et le pied de page restent dans *Elementor → Modèles*.

Testé sur WordPress 7.1.2, Elementor 4.3.2, Royal Elementor Addons 1.7 (thème Royal Elementor Kit) et WooCommerce 11.1.2 :
import complet en 3 s, aucun débordement de 390 à 1366 px, aucune erreur JavaScript, éditeur Elementor fonctionnel.
Reconstruire le paquet : `./elementor/package-import-plugin.sh`.
