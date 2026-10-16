<?php
/**
 * LMDD – Vitesse (2) : scripts inutiles et liens de la FAQ (à coller dans WPCode)
 *
 * WPCode → Ajouter un extrait → « Ajouter votre code personnalisé » → Type : Extrait PHP
 * Insertion : Insertion automatique → Exécuter partout. Activer, enregistrer, puis :
 * Elementor → Outils → « Vider les fichiers et les données » et WP Rocket → Vider le cache.
 *
 * Remplace les règles Asset Pilot proposées dans le plan : l'extension Asset Pilot peut ensuite être supprimée.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() || ! function_exists( 'is_woocommerce' ) ) {
		return;
	}
	// Turnstile (anti-robot de Contact Form 7) : inutile là où il n'y a jamais de formulaire.
	// Il reste chargé sur les fiches produits (devis), la page SAV, la page contact et les autres pages.
	if ( is_front_page() || is_shop() || is_product_taxonomy() || is_cart() || is_checkout() ) {
		wp_dequeue_script( 'cloudflare-turnstile' );
	}
	// Alma (paiement en plusieurs fois) : seulement sur les fiches produits, le panier et la commande.
	if ( ! ( is_product() || is_cart() || is_checkout() ) ) {
		wp_dequeue_style( 'alma-widget' );
		wp_dequeue_style( 'alma-widget-cdn' );
	}
}, 100 );

// FAQ en accordéon Elementor : le titre est un lien <a> sans adresse, signalé par PageSpeed (SEO : « liens non explorables »).
// Il devient un simple texte ; le titre lui-même reste accessible au clavier (Tab, puis Entrée).
add_filter( 'elementor/widget/render_content', function ( $html, $widget ) {
	if ( ! in_array( $widget->get_name(), array( 'accordion', 'toggle' ), true ) ) {
		return $html;
	}
	$html = preg_replace( '#<a class="elementor-(accordion|toggle)-title" tabindex="0">(.*?)</a>#s', '<span class="elementor-$1-title">$2</span>', $html );
	return str_replace( 'class="elementor-tab-title"', 'class="elementor-tab-title" tabindex="0"', $html );
}, 10, 2 );
