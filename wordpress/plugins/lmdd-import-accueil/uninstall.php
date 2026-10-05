<?php
/**
 * Suppression de l'extension : efface uniquement son suivi d'installation.
 * La page d'accueil, les modèles, l'en-tête, les méga-menus, le pied de page, le menu et les images restent en place.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
delete_option( 'lmdd_import_accueil' );
