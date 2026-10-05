<?php
/**
 * Plugin Name:       LMDD – Devis & SAV
 * Description:       Demande de devis affichée sur place dans la fiche produit et formulaire SAV en étapes, avec Contact Form 7 (champs et e-mails modifiables), pièces avec prix en direct, copie de secours des demandes (menu « Demandes clients »).
 * Version:           2.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce, contact-form-7
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'LMDD_DS_VERSION', '2.0.0' );
define( 'LMDD_DS_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-lmdd-ds-store.php';
require_once __DIR__ . '/includes/class-lmdd-ds-cf7.php';
require_once __DIR__ . '/includes/class-lmdd-ds-front.php';

add_action( 'plugins_loaded', function () {
	LMDD_DS_Store::init();
	LMDD_DS_CF7::init();
	if ( class_exists( 'WooCommerce' ) ) {
		LMDD_DS_Front::init();
	}
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'edit.php?post_type=lmdd_demande&page=lmdd-ds-reglages' ) ) . '">Réglages</a>' );
	return $links;
} );
