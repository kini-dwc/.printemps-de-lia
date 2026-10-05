<?php
/**
 * Désinstallation : supprime les réglages. Les demandes reçues sont CONSERVÉES (elles restent en base) ;
 * pour les effacer aussi, ajoutez define( 'LMDD_DS_EFFACER_DEMANDES', true ); dans wp-config.php avant de supprimer l'extension.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
delete_option( 'lmdd_ds_settings' );
if ( defined( 'LMDD_DS_EFFACER_DEMANDES' ) && LMDD_DS_EFFACER_DEMANDES ) {
	foreach ( get_posts( array( 'post_type' => 'lmdd_demande', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
		wp_delete_post( $id, true );
	}
}
