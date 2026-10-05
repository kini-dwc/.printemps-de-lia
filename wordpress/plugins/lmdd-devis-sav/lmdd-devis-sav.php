<?php
/**
 * Plugin Name:       LMDD – Devis & SAV
 * Description:       Demande de devis directement sur la fiche produit (panneau qui s'ouvre au clic, configuration reprise), formulaire SAV en étapes avec photos, fiches symptômes avec prix des pièces en direct. Chaque demande est enregistrée (menu « Demandes clients ») et envoyée par e-mail.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'LMDD_DS_VERSION', '1.0.0' );
define( 'LMDD_DS_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-lmdd-ds-forms.php';
require_once __DIR__ . '/includes/class-lmdd-ds-store.php';
require_once __DIR__ . '/includes/class-lmdd-ds-rest.php';
require_once __DIR__ . '/includes/class-lmdd-ds-front.php';

add_action( 'plugins_loaded', function () {
	LMDD_DS_Store::init();
	LMDD_DS_Rest::init();
	if ( class_exists( 'WooCommerce' ) ) {
		LMDD_DS_Front::init();
	}
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'edit.php?post_type=lmdd_demande&page=lmdd-ds-reglages' ) ) . '">Réglages</a>' );
	return $links;
} );
