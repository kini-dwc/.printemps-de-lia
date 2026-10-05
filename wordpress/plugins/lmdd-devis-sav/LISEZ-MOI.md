# LMDD – Devis & SAV

Extension **permanente** (à garder) qui gère les demandes clients directement sur le site :

| Fonction | Où |
|---|---|
| **Demande de devis dans la fiche produit** : le bouton « Demander un devis » ouvre un panneau (à droite sur ordinateur, plein écran sur mobile) avec la configuration choisie, sans changer de page | Toutes les fiches des produits vendus sur devis |
| **Formulaire SAV en 4 étapes** : le problème (8 situations), le lit, les coordonnées, le récapitulatif ; photos possibles (étiquette du matelas…) | Code court `[lmdd_sav_formulaire]` (page SAV) |
| **Fiches symptômes** : ce que c'est, ce que nous faisons, les pièces avec leur **prix lu en direct** dans la boutique | Code court `[lmdd_sav_symptomes]` |
| **Demandes clients** : chaque demande est enregistrée, l'équipe reçoit un e-mail, le client un accusé de réception | Menu *Demandes clients* |

## Installation

1. *Extensions → Ajouter → Téléverser* → `lmdd-devis-sav.zip` → *Installer* → *Activer*.
2. Ouvrez **Demandes clients → Réglages** et vérifiez l'adresse qui reçoit les demandes. Par défaut, c'est celle de votre
   formulaire de devis Contact Form 7, sinon l'e-mail de l'administration.
3. Faites une demande d'essai sur une fiche « sur devis » et vérifiez la réception de l'e-mail.

**Dès l'activation**, sur les fiches des produits vendus sur devis, le bouton « Demander un devis » ouvre le panneau.
Il ne renvoie plus vers la page `/demande-de-devis/`. Cela vaut aussi avec votre modèle de fiche actuel : l'ancien bouton est
remplacé, le nouveau se place sous les options. La page `/demande-de-devis/` (Contact Form 7) reste en place et peut servir de secours.

## Produits « sur devis »

- **Automatique** : un produit est « sur devis » si l'une de ses catégories contient `sur-devis` (ex. : *Lits à eau en vente sur devis*).
  Le mot se règle dans *Demandes clients → Réglages*.
- **Produit par produit** : *Produit → Général → Vente sur devis* : Automatique / Oui / Non.
- Sur ces produits : le prix et le bouton « Ajouter au panier » sont masqués, les **options de configuration restent**
  (TM Extra Product Options) et sont reprises dans la demande. L'ajout au panier est aussi refusé côté serveur.

Deux champs facultatifs dans *Produit → Général*, utilisés par le nouveau modèle de fiche :
- **Titre affiché (court)** : ex. « Lit à eau Akva Soft / Soft Q » au lieu du nom complet. Le titre SEO n'est pas modifié.
- **Points clés** : un par ligne, affichés avec une coche sous le titre (3 ou 4 conseillés).

## Le panneau de devis

1. **Votre configuration** : modèle, dimensions, couchage, stabilisation, options… repris automatiquement de la fiche,
   avec un lien « Modifier ».
2. **Votre projet** : le cadre du lit (neuf, cadre actuel, je ne sais pas).
3. **La livraison** : code postal, pays (France, Belgique, Luxembourg, Pays-Bas, Suisse…), étage, accès.
4. **Vos coordonnées** : nom, téléphone, e-mail, créneau de rappel (matin, midi, après-midi, soirée jusqu'à 21h), précision libre.

Après l'envoi, un message de confirmation s'affiche **dans le panneau**, avec la référence de la demande. Sur mobile, une barre
« Demander un devis » reste accessible en bas de l'écran une fois le bouton principal dépassé.

Les champs se modifient dans `includes/class-lmdd-ds-forms.php` : une seule définition sert à l'affichage, à la vérification et aux e-mails.

## Demandes clients (administration)

- Liste avec type (Devis / SAV), téléphone cliquable, créneau, code postal, statut **Nouvelle / Traitée**, et filtre Devis / SAV.
- Fiche de la demande : toutes les réponses, la configuration, les photos.
- E-mail à l'équipe avec **Répondre à** le client, et les photos du SAV en pièces jointes.

## Sécurité et anti-spam

- Champ piège invisible, délai minimal de remplissage, 8 envois au maximum par visiteur et par quart d'heure.
- **Cloudflare Turnstile** : repris automatiquement de *Contact Form 7 → Intégration* s'il y est configuré. Il n'est chargé
  qu'à l'ouverture d'un formulaire.
- Photos SAV : 3 au maximum, 8 Mo chacune, JPEG / PNG / WebP / HEIC uniquement (type réel vérifié). Elles sont renommées
  au hasard et stockées dans `uploads/lmdd-demandes/`, dossier non listable où aucun script ne peut s'exécuter.
- Compatible avec le cache de pages : aucun jeton de session dans les pages.

## Performance

CSS et JavaScript (environ 5 Ko chacun une fois compressés, sans jQuery) chargés **uniquement** sur les fiches produits et les pages
qui contiennent un code court `[lmdd_…]`. Turnstile n'est chargé qu'à l'ouverture d'un formulaire.

## Codes courts

| Code court | Rôle |
|---|---|
| `[lmdd_sav_formulaire]` | Formulaire SAV en étapes |
| `[lmdd_sav_symptomes formulaire="#formulaire-sav"]` | Fiches symptômes ; le bouton « C'est mon cas » présélectionne le problème dans le formulaire |
| `[lmdd_devis_bouton]` | Bouton + panneau de devis, à placer manuellement si besoin (sinon automatique) |
| `[lmdd_titre]`, `[lmdd_fil_ariane]`, `[lmdd_produit_entete]`, `[lmdd_points_cles]`, `[lmdd_reassurance]` | Blocs du nouveau modèle de fiche produit |

## Désinstallation

La suppression de l'extension efface ses réglages et **conserve les demandes reçues**. Pour les effacer aussi :
`define( 'LMDD_DS_EFFACER_DEMANDES', true );` dans `wp-config.php` avant de la supprimer.

Testé sur WordPress 7.1.2, WooCommerce 11.1.2, Elementor 4.3.2, Royal Elementor Addons et un balisage identique à TM Extra Product Options :
devis et SAV de bout en bout (ordinateur et mobile), erreurs de saisie, e-mails, photos, fichier piégé refusé, champ piège.
