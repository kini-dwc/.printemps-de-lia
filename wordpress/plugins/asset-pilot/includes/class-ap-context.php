<?php
/**
 * Contexte de la page courante, sous forme de « jetons » : site, front, id:12, type:product, woo:cart…
 * Une règle s'applique si sa portée figure parmi les jetons et qu'aucune exception n'y figure.
 */

defined( 'ABSPATH' ) || exit;

class AP_Context {

	public static function tokens() {
		$t = array( 'site' );
		if ( is_front_page() ) {
			$t[] = 'front';
		}
		if ( is_singular() ) {
			$id  = get_queried_object_id();
			$t[] = 'id:' . $id;
			$t[] = 'type:' . get_post_type( $id );
		} elseif ( is_home() && ! is_front_page() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );
			if ( $page_for_posts ) {
				$t[] = 'id:' . $page_for_posts;
			}
		}
		if ( function_exists( 'is_woocommerce' ) ) {
			if ( function_exists( 'is_cart' ) && is_cart() ) {
				$t[] = 'woo:cart';
			}
			if ( function_exists( 'is_checkout' ) && is_checkout() ) {
				$t[] = 'woo:checkout';
			}
			if ( function_exists( 'is_account_page' ) && is_account_page() ) {
				$t[] = 'woo:account';
			}
			if ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) {
				$t[] = 'woo:archive';
			}
			if ( function_exists( 'is_product' ) && is_product() ) {
				$t[] = 'woo:product';
			}
		}
		if ( is_search() ) {
			$t[] = 'search';
		} elseif ( is_archive() || ( is_home() && ! is_front_page() ) ) {
			$t[] = 'archive';
		}
		if ( is_404() ) {
			$t[] = '404';
		}
		return array_values( array_unique( $t ) );
	}

	/** Libellé lisible de la page courante. */
	public static function label() {
		if ( is_front_page() ) {
			return __( "Page d'accueil", 'asset-pilot' );
		}
		if ( is_singular() ) {
			$obj  = get_post_type_object( get_post_type() );
			$type = $obj ? $obj->labels->singular_name : get_post_type();
			return $type . ' : ' . wp_strip_all_tags( get_the_title( get_queried_object_id() ) );
		}
		if ( is_search() ) {
			return __( 'Recherche', 'asset-pilot' );
		}
		if ( is_404() ) {
			return __( 'Page 404', 'asset-pilot' );
		}
		if ( is_archive() || is_home() ) {
			return __( 'Archive', 'asset-pilot' ) . ' : ' . wp_strip_all_tags( get_the_archive_title() );
		}
		return __( 'Page', 'asset-pilot' );
	}

	/** Libellé lisible d'un jeton (pour l'interface). */
	public static function token_label( $token ) {
		$fixed = array(
			'site'         => __( 'Tout le site', 'asset-pilot' ),
			'front'        => __( "Page d'accueil", 'asset-pilot' ),
			'archive'      => __( 'Archives et blog', 'asset-pilot' ),
			'search'       => __( 'Recherche', 'asset-pilot' ),
			'404'          => __( 'Page 404', 'asset-pilot' ),
			'woo:cart'     => __( 'Panier', 'asset-pilot' ),
			'woo:checkout' => __( 'Commande (paiement)', 'asset-pilot' ),
			'woo:account'  => __( 'Mon compte', 'asset-pilot' ),
			'woo:archive'  => __( 'Boutique et catégories produits', 'asset-pilot' ),
			'woo:product'  => __( 'Fiches produits', 'asset-pilot' ),
		);
		if ( isset( $fixed[ $token ] ) ) {
			return $fixed[ $token ];
		}
		if ( 0 === strpos( $token, 'type:' ) ) {
			$obj = get_post_type_object( substr( $token, 5 ) );
			return sprintf( __( 'Tous les contenus « %s »', 'asset-pilot' ), $obj ? $obj->labels->name : substr( $token, 5 ) );
		}
		if ( 0 === strpos( $token, 'id:' ) ) {
			$id = (int) substr( $token, 3 );
			return sprintf( __( 'Uniquement : %s', 'asset-pilot' ), $id ? wp_strip_all_tags( get_the_title( $id ) ) : '#' . $id );
		}
		return $token;
	}
}
