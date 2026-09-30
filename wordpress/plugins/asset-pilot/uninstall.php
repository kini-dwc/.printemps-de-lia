<?php
/**
 * Désinstallation : suppression de toutes les données d'Asset Pilot.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

foreach ( array( 'asset_pilot_rules', 'asset_pilot_settings', 'asset_pilot_assets', 'asset_pilot_urls', 'asset_pilot_reports' ) as $opt ) {
	delete_option( $opt );
}
delete_metadata( 'user', 0, 'asset_pilot_preview', '', true );

global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_ap\_seen\_%' OR option_name LIKE '\_transient\_timeout\_ap\_seen\_%'" ); // phpcs:ignore WordPress.DB
