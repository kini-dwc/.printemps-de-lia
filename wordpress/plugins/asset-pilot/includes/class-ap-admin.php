<?php
/**
 * Page d'administration : Outils → Asset Pilot.
 * L'interface est une application JavaScript sans dépendance (assets/admin.js) qui dialogue avec l'API REST.
 */

defined( 'ABSPATH' ) || exit;

class AP_Admin {

	const SLUG = 'asset-pilot';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( ASSET_PILOT_FILE ), array( __CLASS__, 'action_links' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notice_disabled' ) );
	}

	public static function menu() {
		add_management_page(
			__( 'Asset Pilot – Gestionnaire de scripts', 'asset-pilot' ),
			__( 'Asset Pilot', 'asset-pilot' ),
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render' )
		);
	}

	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'tools.php?page=' . self::SLUG ) ) . '">' . esc_html__( 'Gérer les scripts', 'asset-pilot' ) . '</a>' );
		return $links;
	}

	public static function enqueue( $hook ) {
		if ( 'tools_page_' . self::SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'asset-pilot-admin', ASSET_PILOT_URL . 'assets/admin.css', array(), ASSET_PILOT_VERSION );
		wp_enqueue_script( 'asset-pilot-admin', ASSET_PILOT_URL . 'assets/admin.js', array(), ASSET_PILOT_VERSION, true );
		wp_localize_script( 'asset-pilot-admin', 'AssetPilotAdmin', array(
			'rest'  => esc_url_raw( rest_url( AP_Rest::NS . '/' ) ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		) );
	}

	public static function render() {
		echo '<div class="wrap ap-wrap"><h1>' . esc_html__( 'Asset Pilot – Gestionnaire de scripts', 'asset-pilot' ) . '</h1>';
		echo '<div id="asset-pilot-app"><p>' . esc_html__( 'Chargement…', 'asset-pilot' ) . '</p></div>';
		echo '<noscript><p>' . esc_html__( 'Cette interface nécessite JavaScript.', 'asset-pilot' ) . '</p></noscript></div>';
	}

	public static function notice_disabled() {
		if ( defined( 'ASSET_PILOT_DISABLE' ) && ASSET_PILOT_DISABLE && current_user_can( 'manage_options' ) ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'Asset Pilot est suspendu (ASSET_PILOT_DISABLE) : aucune règle n\'est appliquée.', 'asset-pilot' ) . '</p></div>';
		}
	}
}
