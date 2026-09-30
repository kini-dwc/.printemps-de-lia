<?php
/**
 * Plugin Name: La Maison du Dos — Performance
 * Description: Allège le front WordPress / WooCommerce / Elementor. Désactive les scripts et styles inutiles, surtout sur la page d'accueil.
 * Version:     1.0.0
 *
 * Installation : copier ce fichier dans wp-content/mu-plugins/ (créer le dossier si besoin).
 * Un mu-plugin est chargé automatiquement, sans activation.
 *
 * MODE DIAGNOSTIC : connecté en administrateur, ouvrez n'importe quelle page avec ?lmdd_assets=1
 * → la liste exacte des handles JS/CSS chargés (et leur plugin d'origine) s'affiche en bas de page.
 * C'est cette liste qui permet de compléter LMDD_HOME_DEQUEUE_* ci-dessous en toute sécurité.
 */

defined( 'ABSPATH' ) || exit;

/* --------------------------------------------------------------------------
 * 1. Réglages : handles à retirer sur la page d'accueil
 *    Chaque ligne est à valider avec le mode diagnostic avant mise en prod.
 * -------------------------------------------------------------------------- */

// Handles relevés sur prod.la-maison-du-dos.com le 30/09/2026 (audit/rapport/).
const LMDD_HOME_DEQUEUE_SCRIPTS = array(
	// Prisna WP Translate : son script « blocks » tire TOUTE la pile de l'éditeur Gutenberg
	// sur le front (React, wp-components, wp-block-editor, moment… : 43 fichiers, 1,17 Mo, synchrones).
	'prisna-wp-translate-blocks',
	'jquery-ui-mouse',
	'jquery-ui-draggable',
	// ProfilePress (wp-user-avatar) : formulaires de compte, inutiles sur l'accueil
	'ppress-flatpickr',
	'ppress-select2',
	'ppress-frontend-script',
	// Royal Elementor Addons : effets non utilisés par la nouvelle page
	'wpr-particles',
	'wpr-jarallax',
	'wpr-parallax-hover',
	'wpr-perfect-scroll-js',
	'wpr-popup-scroll-js',
	'wpr-modal-popups-js',      // à garder si une popup Royal est active sur l'accueil
	// Paiement / commande : utiles au tunnel de commande seulement
	'alma-classic-checkout',
	'wc-jquery-blockui',
	'sourcebuster-js',
	'wc-order-attribution',
	// Anti-spam Cloudflare : utile uniquement sur les pages avec formulaire
	'cloudflare-turnstile',
	// Menu accordéon WPB (à garder s'il sert au menu de l'en-tête actuel)
	'wpb_wmca_jquery_cookie',
	'wpb_wmca_accordion_script',
	'wpb_wmca_accordion_init',
	'wp-embed',
	'comment-reply',
);

const LMDD_HOME_DEQUEUE_STYLES = array(
	'prisna-wp-translate-blocks',
	'ppress-frontend',
	'ppress-flatpickr',
	'ppress-select2',
	'alma-widget-cdn',
	'alma-widget',
	'alma-classic-checkout',
	'alma-gateway-block',
	'alma-gateway-block-react-component',
	'wcsag-font',                 // Google Fonts Open Sans + Oswald du plugin d'avis
	'flexible-shipping-free-shipping',
	'wpr-link-animations-css',
	'wpr-text-animations-css',
	'font-awesome-5-all',         // Font Awesome complet (icônes SVG inline sur la nouvelle page)
	'elementor-gf-local-poppins', // polices Elementor : la nouvelle page utilise la pile système
	'elementor-gf-local-roboto',
	'elementor-gf-local-robotoslab',
	'wp-block-library',
	'wp-block-library-theme',
	'classic-theme-styles',
	'global-styles',
	'wc-blocks-style',
);

/** Page(s) où appliquer le nettoyage agressif. */
function lmdd_is_light_page() {
	return is_front_page() && ! is_customize_preview() && ! lmdd_is_elementor_editor();
}

function lmdd_is_elementor_editor() {
	return isset( $_GET['elementor-preview'] ) || ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] );
}

/* --------------------------------------------------------------------------
 * 2. Nettoyage global (toutes les pages du front)
 * -------------------------------------------------------------------------- */

// Emojis : 1 script + 1 style inline sur chaque page
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

// Balises inutiles dans le <head>
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
add_filter( 'the_generator', '__return_empty_string' );

// Elementor : pas de Google Fonts distantes (utiliser les polices système ou auto-hébergées)
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// Heartbeat inutile côté visiteurs
add_action( 'init', function () {
	if ( ! is_admin() && ! is_user_logged_in() ) {
		wp_deregister_script( 'heartbeat' );
	}
}, 1 );

add_action( 'wp_enqueue_scripts', function () {
	// Dashicons uniquement pour la barre d'admin
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}
	// jQuery Migrate : uniquement utile à de très vieux plugins
	if ( ! is_admin() ) {
		$scripts = wp_scripts();
		if ( isset( $scripts->registered['jquery'] ) && $scripts->registered['jquery']->deps ) {
			$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
		}
	}
}, 100 );

/* --------------------------------------------------------------------------
 * 3. Page d'accueil : retrait des scripts/styles listés plus haut
 * -------------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', function () {
	if ( ! lmdd_is_light_page() ) {
		return;
	}
	foreach ( LMDD_HOME_DEQUEUE_SCRIPTS as $handle ) {
		wp_dequeue_script( $handle );
	}
	foreach ( LMDD_HOME_DEQUEUE_STYLES as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 9999 );

// Certains plugins s'enregistrent tard : second passage juste avant l'impression
add_action( 'wp_print_scripts', function () {
	if ( lmdd_is_light_page() ) {
		foreach ( LMDD_HOME_DEQUEUE_SCRIPTS as $handle ) {
			wp_dequeue_script( $handle );
		}
	}
}, 9999 );

/* --------------------------------------------------------------------------
 * 4. defer sur les scripts du front (hors exceptions)
 * -------------------------------------------------------------------------- */

add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( is_admin() || lmdd_is_elementor_editor() || is_user_logged_in() ) {
		return $tag;
	}
	// Scripts qui doivent rester synchrones (à compléter si un script casse)
	$keep_sync = array( 'jquery-core', 'jquery' );
	if ( in_array( $handle, $keep_sync, true ) || false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
		return $tag;
	}
	return str_replace( ' src=', ' defer src=', $tag );
}, 10, 3 );

/* --------------------------------------------------------------------------
 * 5. Mode diagnostic : ?lmdd_assets=1 (administrateurs uniquement)
 * -------------------------------------------------------------------------- */

add_action( 'wp_footer', function () {
	if ( empty( $_GET['lmdd_assets'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$rows = array();
	foreach ( array( 'JS' => wp_scripts(), 'CSS' => wp_styles() ) as $type => $deps ) {
		foreach ( $deps->done as $handle ) {
			$src = isset( $deps->registered[ $handle ] ) ? (string) $deps->registered[ $handle ]->src : '';
			$origin = 'inline / core';
			if ( preg_match( '#/wp-content/plugins/([^/]+)/#', $src, $m ) ) {
				$origin = 'plugin : ' . $m[1];
			} elseif ( preg_match( '#/wp-content/themes/([^/]+)/#', $src, $m ) ) {
				$origin = 'thème : ' . $m[1];
			} elseif ( false !== strpos( $src, '/wp-includes/' ) ) {
				$origin = 'WordPress';
			} elseif ( $src && 0 !== strpos( $src, home_url() ) && 0 === strpos( $src, 'http' ) ) {
				$origin = 'externe : ' . wp_parse_url( $src, PHP_URL_HOST );
			}
			$rows[] = array( $type, $handle, $origin, $src );
		}
	}
	usort( $rows, function ( $a, $b ) {
		return strcmp( $a[2] . $a[1], $b[2] . $b[1] );
	} );
	echo '<div style="position:relative;z-index:99999;background:#fff;color:#111;padding:24px;font:13px/1.5 monospace;border-top:4px solid #e8a33d">';
	echo '<h2 style="font:700 18px sans-serif">LMDD — ' . count( $rows ) . ' ressources chargées sur cette page</h2><table style="width:100%;border-collapse:collapse">';
	echo '<tr style="text-align:left"><th>Type</th><th>Handle</th><th>Origine</th><th>Fichier</th></tr>';
	foreach ( $rows as $r ) {
		printf(
			'<tr style="border-top:1px solid #ddd"><td>%s</td><td><b>%s</b></td><td>%s</td><td style="word-break:break-all">%s</td></tr>',
			esc_html( $r[0] ), esc_html( $r[1] ), esc_html( $r[2] ), esc_html( $r[3] )
		);
	}
	echo '</table></div>';
}, 9999 );
