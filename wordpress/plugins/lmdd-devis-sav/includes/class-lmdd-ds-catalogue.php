<?php
/**
 * Pages catalogue (boutique, catégories et sous-catégories) : blocs utilisés par le modèle Elementor « Catégorie ».
 * Le texte de chaque catégorie reste celui de sa description WooCommerce (Produits → Catégories) : rien n'est réécrit,
 * seule la mise en page change. Aucun JavaScript ; feuille de style chargée uniquement sur ces pages.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Catalogue {

	/** Catégories jamais proposées dans la navigation (catégories techniques). */
	const EXCLUDE = array( 'uncategorized', 'non-classe', 'vos-devis-en-cours' );

	private static $parts = null;

	public static function init() {
		add_shortcode( 'lmdd_cat_entete', array( __CLASS__, 'sc_header' ) );
		add_shortcode( 'lmdd_cat_navigation', array( __CLASS__, 'sc_navigation' ) );
		add_shortcode( 'lmdd_produits', array( __CLASS__, 'sc_products' ) );
		add_shortcode( 'lmdd_cat_description', array( __CLASS__, 'sc_description' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 20 );
	}

	public static function is_catalogue() {
		return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
	}

	public static function assets() {
		wp_register_style( 'lmdd-catalogue', plugin_dir_url( LMDD_DS_FILE ) . 'assets/lmdd-catalogue.css', array(), LMDD_DS_VERSION );
		// Seulement si notre modèle « Catégories et boutique » est utilisé (ou en aperçu) : sinon les blocs l'ajoutent eux-mêmes.
		if ( self::is_catalogue() && false !== strpos( (string) get_option( 'wpr_product_archive_conditions', '' ), 'lmdd-categorie' ) ) {
			wp_enqueue_style( 'lmdd-catalogue' );
		}
	}

	private static function term() {
		$t = is_product_taxonomy() ? get_queried_object() : null;
		return $t instanceof WP_Term ? $t : null;
	}

	/** Titre de la page : nom de la catégorie, ou titre de la page Boutique. */
	private static function title() {
		$t = self::term();
		if ( $t ) {
			return $t->name;
		}
		$shop = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
		return $shop > 0 ? get_the_title( $shop ) : __( 'Boutique', 'woocommerce' );
	}

	/**
	 * Description découpée : le premier paragraphe sert d'introduction (avec le suivant s'il est très court),
	 * le reste s'affiche sous les produits. Texte inchangé.
	 */
	private static function parts() {
		if ( null !== self::$parts ) {
			return self::$parts;
		}
		$t    = self::term();
		$html = '';
		if ( $t ) {
			$html = (string) $t->description;
		} elseif ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) {
			$html = (string) get_post_field( 'post_excerpt', wc_get_page_id( 'shop' ) );
		}
		$html = trim( do_shortcode( wpautop( wptexturize( $html ) ) ) );
		self::$parts = array( 'intro' => '', 'rest' => '' );
		if ( '' === $html ) {
			return self::$parts;
		}
		$blocks = self::blocks( $html );
		$intro  = array();
		$len    = 0;
		while ( $blocks && ( ! $intro || $len < 140 ) && count( $intro ) < 2 ) {
			$b      = array_shift( $blocks );
			$intro[] = $b['inner'];
			$len   += mb_strlen( wp_strip_all_tags( $b['inner'] ) );
		}
		self::$parts['intro'] = implode( ' ', $intro );
		self::$parts['rest']  = implode( "\n", wp_list_pluck( $blocks, 'html' ) );
		return self::$parts;
	}

	/** Blocs de premier niveau d'un fragment HTML : [ 'html' => bloc complet, 'inner' => contenu du bloc ]. */
	private static function blocks( $html ) {
		$out = array();
		if ( ! class_exists( 'DOMDocument' ) ) {
			return array( array( 'html' => $html, 'inner' => wp_strip_all_tags( $html ) ) );
		}
		$doc  = new DOMDocument();
		$prev = libxml_use_internal_errors( true );
		$doc->loadHTML( '<?xml encoding="utf-8"?><div id="lmdd-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		libxml_use_internal_errors( $prev );
		$root = $doc->getElementById( 'lmdd-root' );
		if ( ! $root ) {
			return array( array( 'html' => $html, 'inner' => wp_strip_all_tags( $html ) ) );
		}
		foreach ( iterator_to_array( $root->childNodes ) as $node ) {
			$outer = $doc->saveHTML( $node );
			if ( '' === trim( wp_strip_all_tags( $outer ) ) && false === strpos( $outer, '<img' ) ) {
				continue;
			}
			$inner = '';
			if ( XML_ELEMENT_NODE === $node->nodeType && in_array( strtolower( $node->nodeName ), array( 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
				foreach ( $node->childNodes as $c ) {
					$inner .= $doc->saveHTML( $c );
				}
			} else {
				$inner = $outer;
			}
			$out[] = array( 'html' => $outer, 'inner' => trim( $inner ) );
		}
		return $out;
	}

	private static function count_label( $n ) {
		return sprintf( _n( '%s produit', '%s produits', $n, 'lmdd-devis-sav' ), number_format_i18n( $n ) );
	}

	// ------------------------------------------------------------------ [lmdd_cat_entete]

	/** Fil d'Ariane, titre (H1), introduction et nombre de produits. */
	public static function sc_header() {
		if ( ! self::is_catalogue() ) {
			return '';
		}
		wp_enqueue_style( 'lmdd-catalogue' );
		$p   = self::parts();
		$bc  = '';
		if ( function_exists( 'woocommerce_breadcrumb' ) ) {
			ob_start();
			woocommerce_breadcrumb( array( 'delimiter' => '<span class="lmdd-bc__sep" aria-hidden="true">›</span>', 'wrap_before' => '<nav class="lmdd-bc" aria-label="Fil d\'Ariane">', 'wrap_after' => '</nav>', 'home' => 'Accueil' ) );
			$bc = ob_get_clean();
		}
		global $wp_query;
		$total = (int) $wp_query->found_posts;
		$h  = '<header class="lmdd-cath">' . $bc;
		$h .= '<h1 class="lmdd-cath__title">' . esc_html( self::title() ) . '</h1>';
		if ( $p['intro'] ) {
			$h .= '<p class="lmdd-cath__intro">' . wp_kses_post( $p['intro'] ) . '</p>';
		}
		$meta = array( '<span class="lmdd-cath__count">' . esc_html( self::count_label( $total ) ) . '</span>' );
		if ( $p['rest'] ) {
			$meta[] = '<a class="lmdd-cath__more" href="#lmdd-cat-texte">Lire la présentation complète</a>';
		}
		return $h . '<p class="lmdd-cath__meta">' . implode( '', $meta ) . '</p></header>';
	}

	// ------------------------------------------------------------------ [lmdd_cat_navigation]

	/**
	 * Raccourcis vers les catégories voisines : sous-catégories de la catégorie affichée, sinon catégories sœurs
	 * (avec « Tout voir » vers la catégorie parente), et catégories principales sur la page Boutique.
	 */
	public static function sc_navigation( $atts ) {
		if ( ! self::is_catalogue() ) {
			return '';
		}
		$atts    = shortcode_atts( array( 'exclure' => '' ), $atts );
		$exclude = array_merge( self::EXCLUDE, array_filter( array_map( 'trim', explode( ',', $atts['exclure'] ) ) ) );
		$t       = self::term();
		$all     = null;
		$current = $t ? $t->term_id : 0;
		if ( $t && 'product_cat' !== $t->taxonomy ) {
			return '';
		}
		if ( ! $t ) {
			$parent = 0;
		} else {
			$children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $t->term_id, 'hide_empty' => true ) );
			$parent   = ( $children && ! is_wp_error( $children ) ) ? $t->term_id : $t->parent;
			if ( $parent ) {
				$all = get_term( $parent, 'product_cat' );
			}
		}
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $parent, 'hide_empty' => true, 'orderby' => 'menu_order' ) );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}
		$terms = array_values( array_filter( $terms, function ( $x ) use ( $exclude ) {
			return ! in_array( $x->slug, $exclude, true );
		} ) );
		if ( count( $terms ) < 2 && ! $all ) {
			return '';
		}
		$chip = function ( $url, $label, $count, $on ) {
			return '<li><a class="lmdd-chip' . ( $on ? ' is-on' : '' ) . '" href="' . esc_url( $url ) . '"' . ( $on ? ' aria-current="page"' : '' ) . '>'
				. esc_html( $label ) . ( null !== $count ? '<span>' . number_format_i18n( $count ) . '</span>' : '' ) . '</a></li>';
		};
		$h = '<nav class="lmdd-chips" aria-label="Catégories"><ul>';
		if ( $all && ! is_wp_error( $all ) ) {
			$h .= $chip( get_term_link( $all ), $all->term_id === $current ? 'Tout voir' : 'Tous les ' . self::lower( $all->name ), null, $all->term_id === $current );
		} elseif ( ! $t && function_exists( 'wc_get_page_permalink' ) ) {
			$h .= $chip( wc_get_page_permalink( 'shop' ), 'Tout voir', null, true );
		}
		foreach ( $terms as $x ) {
			$h .= $chip( get_term_link( $x ), $x->name, $x->count, $x->term_id === $current );
		}
		return $h . '</ul></nav>';
	}

	private static function lower( $s ) {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $s ) : strtolower( $s );
	}

	// ------------------------------------------------------------------ [lmdd_produits]

	/** Barre (nombre, tri), grille des produits de la page affichée et pagination. */
	public static function sc_products() {
		if ( ! self::is_catalogue() || ! function_exists( 'wc_get_product' ) ) {
			return '';
		}
		wp_enqueue_style( 'lmdd-catalogue' );
		global $wp_query;
		if ( function_exists( 'wc_setup_loop' ) ) {
			wc_setup_loop( array( 'is_paginated' => true, 'total' => (int) $wp_query->found_posts, 'total_pages' => (int) $wp_query->max_num_pages, 'per_page' => (int) $wp_query->get( 'posts_per_page' ), 'current_page' => max( 1, (int) $wp_query->get( 'paged' ) ) ) );
		}
		ob_start();
		echo '<div class="lmdd-shop">';
		if ( ! have_posts() ) {
			echo '<div class="lmdd-shop__empty"><p>Aucun produit dans cette catégorie pour le moment.</p><a class="lmdd-chip" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Voir toute la boutique</a></div></div>';
			return ob_get_clean();
		}
		echo '<div class="lmdd-shop__bar">';
		woocommerce_result_count();
		woocommerce_catalog_ordering();
		echo '</div><ul class="lmdd-grid">';
		$i = 0;
		while ( have_posts() ) {
			the_post();
			$product = wc_get_product( get_the_ID() );
			if ( $product && $product->is_visible() ) {
				echo self::card( $product, $i++ ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		wp_reset_postdata();
		echo '</ul>';
		woocommerce_pagination();
		echo '</div>';
		return ob_get_clean();
	}

	/** Carte produit : image, badge, marque, titre court, avis, prix (ou « sur devis »), lien. */
	public static function card( WC_Product $p, $index = 0 ) {
		$url   = $p->get_permalink();
		$devis = LMDD_DS_Front::is_devis( $p );
		$title = LMDD_DS_Front::short_title( $p );
		$badge = '';
		if ( $devis ) {
			$badge = '<span class="lmdd-card__badge lmdd-card__badge--devis">Sur devis</span>';
		} elseif ( $p->is_on_sale() && $p->get_regular_price() > 0 && $p->get_sale_price() !== '' ) {
			$pc    = round( 100 - 100 * (float) $p->get_sale_price() / (float) $p->get_regular_price() );
			$badge = '<span class="lmdd-card__badge lmdd-card__badge--promo">' . ( $pc >= 1 ? '-' . $pc . ' %' : 'Promotion' ) . '</span>';
		}
		$img = $p->get_image( 'woocommerce_thumbnail', array(
			'class'   => 'lmdd-card__img',
			'loading' => $index < 2 ? 'eager' : 'lazy',
			'sizes'   => '(max-width: 767px) 50vw, (max-width: 1024px) 33vw, 280px',
			'alt'     => $title,
		) );
		$brand  = self::brand( $p );
		$rating = '';
		if ( $p->get_review_count() && wc_review_ratings_enabled() ) {
			$avg    = (float) $p->get_average_rating();
			$rating = '<p class="lmdd-card__rating" aria-label="' . esc_attr( sprintf( 'Note : %s sur 5', number_format_i18n( $avg, 1 ) ) ) . '"><span class="lmdd-stars" style="--r:' . esc_attr( $avg / 5 * 100 ) . '%"></span>'
				. '<small>' . esc_html( sprintf( _n( '%s avis', '%s avis', $p->get_review_count(), 'lmdd-devis-sav' ), $p->get_review_count() ) ) . '</small></p>';
		}
		$price = $devis
			? '<p class="lmdd-card__price lmdd-card__price--devis">Prix sur devis</p>'
			: ( $p->get_price_html() ? '<p class="lmdd-card__price">' . $p->get_price_html() . '</p>' : '' );
		$cta = $devis ? 'Demander un devis' : 'Voir le produit';
		return '<li class="lmdd-card' . ( $devis ? ' is-devis' : '' ) . '">'
			. '<a class="lmdd-card__media" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . $img . $badge . '</a>'
			. '<div class="lmdd-card__body">'
			. ( $brand ? '<p class="lmdd-card__brand">' . esc_html( $brand ) . '</p>' : '' )
			. '<h2 class="lmdd-card__title"><a href="' . esc_url( $url ) . '" title="' . esc_attr( $p->get_name() ) . '">' . esc_html( $title ) . '</a></h2>'
			. $rating . $price
			. '<span class="lmdd-card__cta" aria-hidden="true">' . esc_html( $cta ) . LMDD_DS_Front::icon( 'arrow' ) . '</span>'
			. '</div></li>';
	}

	/** Marque : taxonomie WooCommerce « Marques » si elle existe. */
	public static function brand( WC_Product $p ) {
		if ( ! taxonomy_exists( 'product_brand' ) ) {
			return '';
		}
		$b = get_the_terms( $p->get_id(), 'product_brand' );
		return $b && ! is_wp_error( $b ) ? $b[0]->name : '';
	}

	// ------------------------------------------------------------------ [lmdd_cat_description]

	/** Présentation complète de la catégorie (suite de sa description), sous les produits. */
	public static function sc_description( $atts ) {
		if ( ! self::is_catalogue() || is_paged() ) {
			return '';
		}
		$atts = shortcode_atts( array( 'titre' => '' ), $atts );
		$p    = self::parts();
		if ( ! $p['rest'] ) {
			return '';
		}
		$title = $atts['titre'] ? $atts['titre'] : 'Bien choisir : ' . self::lower( self::title() );
		return '<section class="lmdd-catdesc" id="lmdd-cat-texte"><p class="lmdd-catdesc__eyebrow">' . esc_html( $title ) . '</p>'
			. '<div class="lmdd-catdesc__text">' . wp_kses_post( $p['rest'] ) . '</div></section>';
	}
}
