#!/bin/sh
# Construit l'extension « LMDD – Import de la nouvelle page d'accueil » (dist/lmdd-import-accueil.zip)
# à partir des modèles générés par elementor/build.mjs et des images optimisées de la maquette.
set -e
cd "$(dirname "$0")/.."
node elementor/build.mjs
P=wordpress/plugins/lmdd-import-accueil
mkdir -p $P/data $P/images
cp elementor/accueil-la-maison-du-dos.json $P/data/accueil.json
cp elementor/pied-de-page-la-maison-du-dos.json $P/data/pied-de-page.json
cp elementor/en-tete-la-maison-du-dos.json $P/data/en-tete.json
cp elementor/menu-principal-la-maison-du-dos.json $P/data/menu.json
cp elementor/mega-menus-la-maison-du-dos.json $P/data/mega-menus.json
I=homepage/assets/img
cp $I/hero-lit-a-eau-altura-1040.webp $P/images/lit-a-eau-altura.webp
cp $I/lit-a-eau-havre.webp $I/lit-a-eau-tec-line.webp $I/matelas-eau-leger-aqualight.webp $I/drap-housse-bella-donna.webp $P/images/
cp $I/logo.webp $P/images/logo-la-maison-du-dos.webp
cp elementor/images/schema-pression-matelas-eau.png $P/images/
mkdir -p dist && rm -f dist/lmdd-import-accueil.zip
(cd wordpress/plugins && zip -qr ../../dist/lmdd-import-accueil.zip lmdd-import-accueil)
echo "dist/lmdd-import-accueil.zip prêt"
