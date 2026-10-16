<?php
/**
 * LMDD – Vitesse du site (extrait WPCode unique)
 *
 * Remplace les deux extraits précédents (« lmdd-vitesse-images » et « lmdd-vitesse-scripts ») : n'en garder qu'un seul.
 *
 * WPCode → Ajouter un extrait → « Ajouter votre code personnalisé » → Type : Extrait PHP
 * Insertion : Insertion automatique → Exécuter partout. Activer, enregistrer, puis :
 * Elementor → Outils → « Vider les fichiers et les données », et WP Rocket → Vider le cache.
 *
 * 1. Images : l'image principale de la page (LCP) passe en priorité haute ; les images des méga-menus (cachées)
 *    et du pied de page sont différées ; le logo ne prend plus la priorité haute.
 * 2. Émojis WordPress retirés.
 * 3. Fichiers inutiles retirés là où ils ne servent pas : Turnstile, Alma, ProfilePress, effets Royal non utilisés,
 *    Font Awesome sur l'accueil (ce que faisait Asset Pilot).
 * 4. FAQ en accordéon : titres sans lien vide (audit SEO « liens non explorables »), accessibles au clavier.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------- 1. Images
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

// ---------------------------------------------------------------- 2. Émojis
// Émojis WordPress : script et styles inutiles (les navigateurs affichent les émojis eux-mêmes).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

// ---------------------------------------------------------------- 3. Scripts inutiles
// Fichiers retirés là où ils ne servent pas. Vérifié sur l'accueil, les pages et les fiches produits du site.
function lmdd_vitesse_retirer() {
	if ( is_admin() || ! function_exists( 'is_woocommerce' ) ) {
		return;
	}
	$accueil  = is_front_page();
	$commande = is_cart() || is_checkout();

	// Turnstile (anti-robot de Contact Form 7) : inutile là où il n'y a jamais de formulaire.
	// Il reste sur les fiches produits (devis), la page SAV, la page contact et les autres pages.
	if ( $accueil || is_shop() || is_product_taxonomy() || $commande ) {
		wp_dequeue_script( 'cloudflare-turnstile' );
	}
	// Alma (paiement en plusieurs fois) : bandeau sur les fiches, paiement au panier et à la commande.
	if ( ! ( is_product() || $commande ) ) {
		foreach ( array( 'alma-widget', 'alma-widget-cdn', 'alma-widget-block', 'alma-widget-block-view' ) as $h ) {
			wp_dequeue_style( $h );
		}
	}
	if ( ! $commande ) {
		foreach ( array( 'alma-classic-checkout', 'alma-gateway-block', 'alma-gateway-block-react-component' ) as $h ) {
			wp_dequeue_style( $h );
		}
		wp_dequeue_script( 'alma-classic-checkout' );
	}
	// ProfilePress : seulement sur « Mon compte » et les pages qui contiennent un de ses formulaires.
	$post = get_queried_object();
	$pp   = is_account_page() || ( $post instanceof WP_Post && preg_match( '/profilepress|ppress|\[(?:pp-|user-)/i', $post->post_content ) );
	if ( ! $pp ) {
		foreach ( array( 'ppress-frontend', 'ppress-flatpickr', 'ppress-select2' ) as $h ) {
			wp_dequeue_style( $h );
		}
		foreach ( array( 'ppress-frontend-script', 'ppress-flatpickr', 'ppress-select2' ) as $h ) {
			wp_dequeue_script( $h );
		}
	}
	// Royal : particules, parallaxe et texte animé ne sont utilisés sur aucune section du site.
	foreach ( array( 'wpr-particles', 'wpr-jarallax', 'wpr-parallax-hover' ) as $h ) {
		wp_dequeue_script( $h );
	}
	wp_dequeue_style( 'wpr-text-animations-css' );
	// Accueil : pas d'icônes Font Awesome (Elementor les affiche en SVG), ni de bloc Prisna.
	// (Les polices se règlent dans Elementor : l'extrait ne les retire pas.)
	if ( $accueil ) {
		wp_dequeue_style( 'font-awesome-5-all' );
		wp_dequeue_style( 'prisna-wp-translate-blocks' );
	}
}
add_action( 'wp_enqueue_scripts', 'lmdd_vitesse_retirer', 999 );
add_action( 'wp_footer', 'lmdd_vitesse_retirer', 5 ); // fichiers ajoutés pendant l'affichage de la page

// ---------------------------------------------------------------- 4. FAQ en accordéon
// FAQ en accordéon Elementor : le titre est un lien <a> sans adresse, signalé par PageSpeed (SEO : « liens non explorables »).
// Il devient un simple texte ; le titre lui-même reste accessible au clavier (Tab, puis Entrée).
add_filter( 'elementor/widget/render_content', function ( $html, $widget ) {
	if ( ! in_array( $widget->get_name(), array( 'accordion', 'toggle' ), true ) ) {
		return $html;
	}
	$html = preg_replace( '#<a class="elementor-(accordion|toggle)-title" tabindex="0">(.*?)</a>#s', '<span class="elementor-$1-title">$2</span>', $html );
	return str_replace( 'class="elementor-tab-title"', 'class="elementor-tab-title" tabindex="0"', $html );
}, 10, 2 );
