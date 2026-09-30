# Import de la page d'accueil dans Elementor

## Fichiers

| Fichier | Contenu |
|---|---|
| `accueil-la-maison-du-dos.json` | Page d'accueil complète : hero, engagements, catalogue, lit à eau, produits, matelas à télécommande, accompagnement, qui sommes-nous, avis, FAQ, appel à l'action |
| `pied-de-page-la-maison-du-dos.json` | Pied de page (modèle séparé, à placer dans le constructeur de pied de page) |
| `images/schema-pression-matelas-eau.png` | Schéma « matelas classique / matelas à eau » |
| `dist/elementor-modeles-la-maison-du-dos.zip` | Les deux modèles dans une seule archive à importer |
| `dist/elementor-images-la-maison-du-dos.zip` | Les 7 images optimisées (WebP/PNG) + leurs textes alternatifs |
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

## Pourquoi l'import échouait (erreur 500)

La première version des modèles contenait l'adresse des photos : pendant l'import, Elementor **téléchargeait chaque photo**
depuis votre site, puis générait toutes ses tailles de vignettes. Sur un serveur lent (3 à 5 s par requête PHP sur `prod.`),
l'opération dépassait le temps maximal accordé par l'hébergeur : longue attente, puis erreur 500.
Reproduit sur un site de test : environ 35 s pour l'ancien fichier, contre **1 à 2 s** pour la nouvelle version.

**Nouvelle version : aucune image n'est téléchargée pendant l'import.** Chaque image est remplacée par l'image d'attente
d'Elementor et le widget est nommé « **Image à choisir : nom-du-fichier** ». Vous choisissez ensuite les photos dans
votre médiathèque (étape 5), ce qui conserve leurs textes alternatifs et évite les doublons.

## Importer

> Avant toute chose, **sauvegardez le site**, et travaillez sur une **nouvelle page** plutôt que sur l'accueil actuel.

1. **Importer les modèles** : *Elementor → Modèles → Modèles enregistrés → Importer des modèles*, choisissez le fichier, puis *Importer maintenant*.
   Elementor affiche deux fenêtres :
   - « Avertissement : les fichiers JSON peuvent être dangereux » → **Continuer** ;
   - « Permettre les téléversements de fichier non filtré » : la réponse dépend du fichier choisi.

   | Fichier importé | Réponse à la 2e fenêtre |
   |---|---|
   | **`elementor-modeles-la-maison-du-dos.zip`** (les 2 modèles d'un coup) | **Activer et importer**. Obligatoire : sans cette option, Elementor 4.3 ignore les fichiers du ZIP **sans message d'erreur**. Vous pouvez la désactiver ensuite dans *Elementor → Réglages → Avancé → Téléversement de fichiers non filtrés* |
   | `accueil-la-maison-du-dos.json` puis `pied-de-page-la-maison-du-dos.json` (un par un) | **Importer sans activer** : fonctionne sans changer le réglage de sécurité |

2. **Téléverser les images** : décompressez `elementor-images-la-maison-du-dos.zip` sur votre ordinateur,
   puis glissez les 7 images dans *Médias → Ajouter*. Collez leur texte alternatif (fichier `TEXTES-ALTERNATIFS.txt`) :

   | Fichier | Texte alternatif |
   |---|---|
   | lit-a-eau-altura.webp | Lit à eau Altura de Poseïdon avec tête de lit, dans une chambre lumineuse |
   | lit-a-eau-havre.webp | Lit à eau Havre avec tête de lit capitonnée grise |
   | lit-a-eau-tec-line.webp | Lit à eau Tec-Line avec socle noir |
   | matelas-eau-leger-aqualight.webp | Matelas à eau léger Aqualight Premium |
   | drap-housse-bella-donna.webp | Drap-housse jersey Bella Donna bordeaux |
   | schema-pression-matelas-eau.png | Schéma : sur un matelas classique la pression se concentre sur les épaules et le bassin, sur un matelas à eau elle est répartie uniformément |
   | logo-la-maison-du-dos.webp | La Maison du Dos – accueil |

   Ces versions sont recadrées et 2 à 3 fois plus légères que les originaux. Vous pouvez aussi utiliser les photos déjà présentes dans votre médiathèque.
3. **Créer la page** : *Pages → Ajouter*, titre « Accueil », puis *Modifier avec Elementor*.
   Icône ⚙ (Paramètres de la page) : *Mise en page : Elementor pleine largeur* et *Masquer le titre* : oui.
4. **Insérer le modèle** : icône dossier (*Ajouter un modèle*) → *Mes modèles* → « Accueil – La Maison du Dos » → *Insérer*.
5. **Choisir les images** : ouvrez le panneau **Structure** (icône en haut à gauche, ou Ctrl/Cmd + I). Les 6 widgets
   « Image à choisir : … » y sont listés. Cliquez sur chacun, puis *Choisir une image* et sélectionnez le fichier du même nom.
6. **Publier**, vérifier la page, puis la définir comme page d'accueil (*Réglages → Lecture*).
7. **Pied de page** : dans le constructeur de thème de Royal Elementor Addons (pied de page actuel → *Modifier avec Elementor*),
   insérez le modèle « Pied de page – La Maison du Dos », puis choisissez le logo dans le widget « Image à choisir : logo-la-maison-du-dos.webp ».

### Si l'import échoue encore
- Vérifiez que le fichier est bien la nouvelle version : ouvert dans un éditeur de texte, il doit contenir `a-choisir:` et aucune adresse `https://…/wp-content/uploads/`.
- Consultez le journal d'erreurs PHP de l'hébergeur (o2switch : cPanel → *Erreurs*), et envoyez-moi la ligne correspondant à l'heure de l'import.
- Une extension de sécurité (pare-feu applicatif) peut bloquer l'envoi de fichiers JSON : désactivez-la le temps de l'import.

## Bon à savoir

- **Polices** : les modèles n'imposent aucune police. Ils utilisent celle de *Paramètres du site → Typographie globale*.
  Pour la vitesse, choisissez une seule police (deux graisses au maximum), ou la police système.
- **Titre H1** : la partie verte est entourée de `<span style="color:#2E8B3A">…</span>`. Modifiez le texte entre les balises, sans les supprimer.
- **Prix des produits** : ce sont des textes, à mettre à jour à la main. Pour des prix automatiques, remplacez la grille
  par un widget *Code court* : `[products ids="ID1,ID2,ID3,ID4" columns="4"]`.
- **Arrière-plans** : Elementor charge les arrière-plans des sections en différé, à partir de la 4e.
  C'est normal et bon pour la vitesse.
- **Couleurs** : vert `#035C11` (titres, boutons), rouge `#C80C25` (appels à l'action), vert foncé `#012E08` (fonds sombres).
  Pour les centraliser, créez-les dans *Paramètres du site → Couleurs globales*.
- **Régénérer les fichiers** : `node elementor/build.mjs`. Avec `IMAGES=distantes`, le générateur remet les adresses des photos du site
  (import plus lent, à éviter sur `prod.`).
