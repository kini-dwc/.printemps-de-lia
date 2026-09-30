# LMDD – Import de la nouvelle page d'accueil

Crée la nouvelle page d'accueil **sans passer par l'import de fichiers d'Elementor** (qui échouait sur le serveur).

1. *Extensions → Ajouter → Téléverser une extension* → `lmdd-import-accueil.zip` → *Installer* → *Activer*.
2. *Outils → Import accueil LMDD* → **Lancer l'import**.
3. En quelques secondes, l'extension crée :
   - la page brouillon **« Accueil – nouvelle version »** (Elementor, 102 widgets natifs modifiables) ;
   - les modèles « Accueil » et « Pied de page – La Maison du Dos » dans *Elementor → Modèles* ;
   - les 7 images optimisées dans *Médias*, avec leurs textes alternatifs, déjà placées dans la page.
4. Cliquez sur **Modifier avec Elementor** ou **Prévisualiser**. Quand la page vous convient : *Publier*, puis
   *Réglages → Lecture → Page d'accueil*.
5. Pied de page : dans le constructeur de thème de Royal Elementor Addons, insérez le modèle « Pied de page – La Maison du Dos ».
6. L'extension peut ensuite être désactivée et supprimée (la page, les modèles et les images restent).

- Votre page d'accueil actuelle **n'est pas modifiée**.
- Chaque étape est une petite requête (une image à la fois) : pas de dépassement du temps maximal de l'hébergeur.
- Relançable sans doublon. En cas d'échec, le message exact de l'étape s'affiche, avec un bouton « Reprendre ».

Testé sur WordPress 7.1.2 + Elementor 4.3.2 + WooCommerce 11.1.2 : import complet en 3,4 s.
Reconstruire le paquet : `./elementor/package-import-plugin.sh`.
