<?php
/**
 * LMDD – Vitesse : priorité à l'image principale (à coller dans WPCode)
 *
 * WPCode → Ajouter un extrait → « Ajouter votre code personnalisé » → Type : Extrait PHP
 * Insertion : Insertion automatique → Exécuter partout. Activer, enregistrer, puis vider le cache WP Rocket.
 *
 * Problème corrigé : WordPress ne charge « tout de suite » que les 3 premières images de la page et met
 * loading="lazy" sur les suivantes. Or le logo et les images des méga-menus (cachés) passaient en premier :
 * l'image principale de la page (élément LCP) était donc chargée en différé, et les images des méga-menus,
 * invisibles, étaient téléchargées dès l'ouverture de la page, même sur mobile.
 *
 * Après : logo de l'en-tête chargé normalement (sans priorité haute), images des méga-menus et du pied de page
 * en différé, première grande image du contenu en priorité haute et sans « lazy ».
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pile des modèles Elementor en cours d'affichage : « menu » (méga-menu Royal), « header », « footer » ou « contenu ».
$GLOBALS['lmdd_vitesse_pile'] = array();

add_action( 'elementor/frontend/before_get_builder_content', function ( $document ) {
	$id   = $document ? $document->get_main_id() : 0;
	$type = get_post_type( $id );
	if ( 'wpr_mega_menu' === $type ) {
		$role = 'menu';
	} elseif ( 'wpr_templates' === $type ) {
		$role = get_post_meta( $id, '_wpr_template_type', true ); // header, footer, product_single…
	} else {
		$role = 'contenu';
	}
	$GLOBALS['lmdd_vitesse_pile'][] = $role;
} );
add_action( 'elementor/frontend/get_builder_content', function () {
	array_pop( $GLOBALS['lmdd_vitesse_pile'] );
} );

// Elementor (« Chargement optimisé des images ») et WordPress traitent en priorité les 3 premières images de la page.
// Une image déjà marquée loading="lazy" n'est pas comptée, et fetchpriority="auto" lui interdit la priorité haute.
// Le filtre s'applique au HTML de chaque modèle Elementor, cache d'éléments compris.
add_filter( 'elementor/frontend/the_content', function ( $html ) {
	$pile = $GLOBALS['lmdd_vitesse_pile'];
	if ( is_admin() || empty( $pile ) || false === strpos( $html, '<img' ) ) {
		return $html;
	}
	$role = end( $pile );
	if ( 'menu' === $role || 'footer' === $role ) {
		$ajout = 'loading="lazy"';       // méga-menu (invisible au chargement) ou pied de page : différé
	} elseif ( 'header' === $role ) {
		$ajout = 'fetchpriority="auto"'; // en-tête (logo) : chargé normalement, sans voler la priorité haute
	} else {
		return $html;                    // contenu de la page ou fiche produit : règles habituelles
	}
	return preg_replace_callback( '/<img\b[^>]*>/i', function ( $m ) use ( $ajout ) {
		$img = $m[0];
		if ( 'loading="lazy"' === $ajout ) {
			$img = preg_replace( '/\sfetchpriority=["\'][^"\']*["\']/i', '', $img );
			return preg_match( '/\sloading=/i', $img ) ? $img : str_replace( '<img', '<img loading="lazy"', $img );
		}
		// En-tête : on ne touche pas aux images déjà réglées (celles des méga-menus, par exemple).
		return preg_match( '/\s(loading|fetchpriority)=/i', $img ) ? $img : str_replace( '<img', '<img fetchpriority="auto"', $img );
	}, $html );
}, 20 );

// Émojis WordPress : script et styles inutiles (les navigateurs affichent les émojis eux-mêmes).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
