# LMDD – Devis & SAV (version 2)

Extension **permanente** (à garder). Les formulaires sont des formulaires **Contact Form 7** : vous modifiez vous-même
les champs, les textes, les messages et les e-mails dans *Contact → Formulaires*.

| Fonction | Où |
|---|---|
| **Devis sur la fiche produit** : au clic sur « Demander un devis », le formulaire apparaît **à la place du bouton**, sous les options. Pas de panneau, pas d'animation : on remplit et on envoie | Fiches des produits vendus sur devis |
| Les **options choisies** sur la fiche sont jointes automatiquement à la demande, **sans être réaffichées** | Champ caché `configuration` |
| **Formulaire SAV en étapes** (le problème, votre lit, vos coordonnées), photos possibles | Code court `[lmdd_formulaire type="sav"]` |
| **Pièces avec leur prix en direct** | Code court `[lmdd_pieces produits="slug-1, slug-2" devis="oui"]` |
| **Copie de secours** de chaque demande, photos comprises | Menu *Demandes clients* |

## Installation et mise à jour

1. *Extensions → Ajouter → Téléverser* → `lmdd-devis-sav.zip` (remplacez la version 1 si elle est installée) → *Activer*.
2. À la première visite de l'administration, l'extension **crée deux formulaires Contact Form 7** :
   - **« Devis – fiche produit (LMDD) »** : nom, téléphone, e-mail, code postal, créneau de rappel, précision facultative ;
   - **« SAV lit à eau (LMDD) »** : 3 étapes.
   Leurs e-mails reprennent le **destinataire et l'expéditeur de votre formulaire de devis actuel** (Contact Form 7), avec un
   accusé de réception envoyé au client.
3. Vérifiez dans **Demandes clients → Réglages** que ces deux formulaires sont sélectionnés, puis faites un essai.

## Modifier les formulaires (Contact → Formulaires)

- **Champs** : ajoutez, retirez ou renommez librement. Gardez seulement, dans le formulaire de devis, la ligne
  `[hidden configuration id:lmdd-configuration]` : c'est elle qui transporte les options choisies sur la fiche.
- **Mise en page** :
  - `<div class="lmdd-grille">…</div>` : champs sur 2 colonnes ;
  - `<label class="lmdd-champ lmdd-large">` : un champ sur toute la largeur ;
  - `<div class="lmdd-cartes">…</div>` : choix présentés en cartes ;
  - `<span class="lmdd-titre">` : intitulé d'un champ.
- **Étapes (SAV)** : chaque bloc `<fieldset class="lmdd-etape" data-titre="Votre lit"> … </fieldset>` devient une étape, avec barre
  de progression et boutons Continuer / Retour. Ajoutez, retirez ou réordonnez les blocs : les étapes suivent. Le bouton d'envoi
  `[submit]` va dans la dernière étape.
- **Choix uniques facultatifs** : utilisez `[checkbox nom exclusive use_label_element "Choix 1" "Choix 2"]`. Avec Contact Form 7 6,
  un groupe `[radio]` est toujours obligatoire.
- **E-mails** : onglets *E-mail* et *E-mail (2)* (accusé de réception). Dans le devis, `[_post_title]` et `[_post_url]` donnent le produit,
  `[configuration]` les options choisies.
- **Messages** (confirmation, erreurs) : onglet *Messages*. Après l'envoi, le message de confirmation remplace le formulaire.
- **Anti-spam** : Cloudflare Turnstile de Contact Form 7 (*Contact → Intégration*), placé avant le bouton d'envoi.

## Produits « sur devis »

- **Automatique** : un produit est « sur devis » si l'une de ses catégories contient `sur-devis` (réglable dans *Demandes clients → Réglages*).
- **Produit par produit** : *Produit → Général → Vente sur devis* (Automatique / Oui / Non).
- Sur ces produits, le prix et le bouton « Ajouter au panier » sont masqués, et l'ajout au panier est refusé côté serveur.
  Les options de configuration restent. Le bouton de l'ancien modèle de fiche (`#trigger-quote-btn`) ouvre lui aussi le formulaire sur place.
- Sur mobile, une barre « Demander un devis » reste en bas de l'écran une fois le bouton dépassé.
- Champs facultatifs du produit (*Produit → Général*), utilisés par le nouveau modèle de fiche : **Titre affiché (court)** et **Points clés**.

## Page SAV (Elementor)

- **Diagnostic** : un accordéon Elementor natif. Titres et textes se modifient directement dans Elementor. Dans chaque texte, la ligne
  `[lmdd_pieces produits="…" devis="oui"]` affiche les pièces et leur prix actuel : modifiez la liste des produits (leur identifiant
  ou leur slug, séparés par des virgules), ou supprimez la ligne.
- Le lien **« C'est mon cas »** (adresse `#formulaire-sav`) descend au formulaire et présélectionne la situation dont le libellé est
  **identique au titre** de l'élément. Si vous renommez un titre, renommez aussi le choix correspondant dans le formulaire SAV.
- **Formulaire** : widget Code court `[lmdd_formulaire type="sav"]`.

## Performance

- CSS et JavaScript de l'extension : moins de 5 Ko chacun une fois compressés (4,7 Ko et 3,4 Ko), sans jQuery, chargés en différé et **uniquement**
  sur les fiches produits et les pages qui contiennent un code court `[lmdd_…]`.
- Le formulaire de devis est déjà dans la page, masqué : aucun chargement au clic.
- Contact Form 7 charge ses propres fichiers sur tout le site par défaut (c'était déjà le cas). Pour l'alléger, limitez-les aux pages
  qui ont un formulaire, par exemple avec Asset Pilot : fiches produits, page SAV, contact.

## Demandes clients (copie de secours)

Liste avec le type (Devis / SAV), le téléphone, le créneau, le code postal et le statut *Nouvelle / Traitée*. La fiche d'une demande
affiche toutes les réponses et les photos, conservées dans `uploads/lmdd-demandes/` (dossier non listable). L'enregistrement se
désactive dans les réglages.

## Codes courts

| Code court | Rôle |
|---|---|
| `[lmdd_formulaire type="sav"]` / `type="devis"` | Formulaire Contact Form 7 choisi dans les réglages, mis en forme |
| `[lmdd_pieces produits="…" devis="oui"]` | Pièces avec prix en direct |
| `[lmdd_devis_bouton]` | Bouton + formulaire de devis, à placer manuellement si besoin (sinon automatique) |
| `[lmdd_titre]`, `[lmdd_fil_ariane]`, `[lmdd_produit_entete]`, `[lmdd_points_cles]`, `[lmdd_reassurance]` | Blocs du modèle de fiche produit |

## Désinstallation

La suppression de l'extension efface ses réglages. Les formulaires Contact Form 7 et les demandes enregistrées sont **conservés**.
Pour effacer aussi les demandes : `define( 'LMDD_DS_EFFACER_DEMANDES', true );` dans `wp-config.php` avant la suppression.

Testé sur WordPress 7.1.2, WooCommerce 11.1.2, Contact Form 7 6.1.7, Elementor 4.3.2, Royal Elementor Addons, avec la remise à zéro
des marges de Royal Pro reproduite. Devis (ancienne et nouvelle fiche, ordinateur et mobile) et SAV (présélection depuis l'accordéon,
étapes, photo, envoi) de bout en bout ; e-mails avec le produit et les options ; copie dans *Demandes clients*.
