# Import de la page d'accueil dans Elementor

## Fichiers

| Fichier | Contenu |
|---|---|
| `accueil-la-maison-du-dos.json` | Page d'accueil complète : hero, engagements, catalogue, lit à eau, produits, matelas à télécommande, accompagnement, qui sommes-nous, avis, FAQ, appel à l'action |
| `pied-de-page-la-maison-du-dos.json` | Pied de page (modèle séparé, à placer dans le constructeur de pied de page) |
| `images/schema-pression-matelas-eau.png` | Schéma « matelas classique / matelas à eau », à téléverser à la main (étape 5) |
| `build.mjs` | Générateur des deux JSON (`node elementor/build.mjs`) |

**Testé** sur WordPress 7.1.2 + **Elementor 4.3.2** (version gratuite, la même que sur votre site) :
import sans erreur, rendu desktop et mobile conforme à la maquette, éditeur Elementor sans erreur JS.

## 100 % widgets Elementor natifs (version gratuite)

- **Page d'accueil** : **102 widgets** dans **55 conteneurs** Flexbox/Grille : Titre (43), Éditeur de texte (23),
  Bouton (14), Boîte d'icône (12), Image (6), Icône (1), Liste d'icônes (1), Accordéon (1, avec **schéma FAQ activé**), HTML (1).
- **Pied de page** : **12 widgets** dans **7 conteneurs** : Liste d'icônes (4), Titre (3), Éditeur de texte (3), Image (1), Bouton (1).

Chaque texte, lien, couleur, image, icône et espacement se modifie depuis le panneau Elementor,
avec des réglages distincts pour ordinateur, tablette et mobile. Les sections sont nommées dans le panneau
**Structure** : Hero, Engagements, Catalogue, Pourquoi un matelas à eau, Produits, Matelas à télécommande,
Accompagnement, Qui sommes-nous, Avis clients, FAQ, Appel à l'action.

**Seule exception** : le widget des avis est un widget *HTML* contenant l'iframe officielle de la Société des Avis
Garantis. Ce service ne propose pas d'autre intégration.

## Importer

> Avant toute chose, **sauvegardez le site**, et travaillez sur une **nouvelle page** plutôt que sur l'accueil actuel.

1. **Importer les modèles** : *Modèles → Modèles enregistrés → Importer des modèles*, puis choisissez
   `accueil-la-maison-du-dos.json` → *Importer*. Faites de même avec `pied-de-page-la-maison-du-dos.json`.
   Les photos sont téléchargées automatiquement depuis votre site vers la médiathèque.
2. **Créer la page** : *Pages → Ajouter*, titre « Accueil », puis *Modifier avec Elementor*.
3. **Mise en page** : icône ⚙ (Paramètres de la page), puis *Mise en page : Elementor pleine largeur*
   (garde l'en-tête et le pied de page de votre thème) et *Masquer le titre* : oui.
4. **Insérer le modèle** : icône dossier (*Ajouter un modèle*) → *Mes modèles* → « Accueil – La Maison du Dos » → *Insérer*.
   Si Elementor demande d'appliquer les réglages de page du modèle, répondez *Oui*.
5. **Schéma** : cliquez sur l'image grise de la section « Pourquoi un matelas à eau », puis *Choisir une image*
   et téléversez `images/schema-pression-matelas-eau.png`.
6. **Textes alternatifs (SEO)** : Elementor n'importe pas les textes alt. Dans *Médias*, renseignez-les :

   | Image | Texte alternatif |
   |---|---|
   | altura-pos.jpg | Lit à eau Altura de Poseïdon avec tête de lit, dans une chambre lumineuse |
   | Havre3-1.webp | Lit à eau Havre avec tête de lit capitonnée grise |
   | tec-line-standard.webp | Lit à eau Tec-Line avec socle noir |
   | matelas-eau-leger.jpg | Matelas à eau léger Aqualight Premium |
   | bella-donna-standard-0030-bordeaux.jpg | Drap-housse jersey Bella Donna bordeaux |
   | schema-pression-matelas-eau.png | Schéma : sur un matelas classique la pression se concentre sur les épaules et le bassin, sur un matelas à eau elle est répartie uniformément |
   | cropped-template_images_logo-boutique-pc.webp | La Maison du Dos – accueil |

   Astuce : ces photos existent déjà dans votre médiathèque. Vous pouvez aussi resélectionner les originaux
   dans chaque widget Image et supprimer les copies créées par l'import.
7. **Publier**, vérifier la page, puis la définir comme page d'accueil (*Réglages → Lecture*).
8. **Pied de page** : dans le constructeur de thème de Royal Elementor Addons (pied de page actuel → *Modifier avec Elementor*),
   insérez le modèle « Pied de page – La Maison du Dos ».

## Bon à savoir

- **Polices** : les modèles n'imposent aucune police. Ils utilisent celle de *Paramètres du site → Typographie globale*.
  Pour la vitesse, choisissez une seule police (deux graisses au maximum), ou la police système.
- **Titre H1** : la partie verte est entourée de `<span style="color:#2E8B3A">…</span>`. Modifiez le texte entre les balises, sans les supprimer.
- **Prix des produits** : ce sont des textes, à mettre à jour à la main. Pour des prix automatiques, remplacez la grille
  par un widget *Code court* : `[products ids="ID1,ID2,ID3,ID4" columns="4"]`.
- **Arrière-plans** : Elementor charge les arrière-plans des sections en différé, à partir de la 4e.
  C'est normal et bon pour la vitesse.
- **Réimport** : réimporter le JSON réutilise les photos déjà importées, sans doublon.
- **Couleurs** : vert `#035C11` (titres, boutons), rouge `#C80C25` (appels à l'action), vert foncé `#012E08` (fonds sombres).
  Pour les centraliser, créez-les dans *Paramètres du site → Couleurs globales*.
