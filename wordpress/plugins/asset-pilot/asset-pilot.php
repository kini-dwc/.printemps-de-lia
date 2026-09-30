<?php
/**
 * Plugin Name:       Asset Pilot – Gestionnaire de scripts
 * Description:       Désactivez les scripts et feuilles de style inutiles page par page ou sur tout le site, testez sans impacter les visiteurs, et mesurez l'impact sur la vitesse et le bon fonctionnement.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Asset Pilot
 * License:           GPL-2.0-or-later
 * Text Domain:       asset-pilot
 *
 * Désactivation d'urgence : ajoutez define( 'ASSET_PILOT_DISABLE', true ); dans wp-config.php,
 * ou ouvrez n'importe quelle page avec ?ap_off=1 (aucune règle appliquée pour cette requête).
 */

defined( 'ABSPATH' ) || exit;

define( 'ASSET_PILOT_VERSION', '1.0.0' );
define( 'ASSET_PILOT_FILE', __FILE__ );
define( 'ASSET_PILOT_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASSET_PILOT_URL', plugin_dir_url( __FILE__ ) );

require_once ASSET_PILOT_DIR . 'includes/class-ap-store.php';
require_once ASSET_PILOT_DIR . 'includes/class-ap-context.php';
require_once ASSET_PILOT_DIR . 'includes/class-ap-frontend.php';
require_once ASSET_PILOT_DIR . 'includes/class-ap-rest.php';
require_once ASSET_PILOT_DIR . 'includes/class-ap-admin.php';

add_action( 'plugins_loaded', function () {
	AP_Frontend::init();
	AP_Rest::init();
	AP_Admin::init();
} );

register_activation_hook( __FILE__, function () {
	AP_Store::settings(); // crée le jeton de test
} );
