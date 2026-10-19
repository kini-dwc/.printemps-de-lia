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
	private static $query = null;

	public static function init() {
		add_shortcode( 'lmdd_cat_entete', array( __CLASS__, 'sc_header' ) );
		add_shortcode( 'lmdd_cat_navigation', array( __CLASS__, 'sc_navigation' ) );
		add_shortcode( 'lmdd_produits', array( __CLASS__, 'sc_products' ) );
		add_shortcode( 'lmdd_cat_description', array( __CLASS__, 'sc_description' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 20 );
		// Fil d'Ariane sans « Boutique » : Accueil › Catégorie › Sous-catégorie (› Produit).
		add_filter( 'woocommerce_get_breadcrumb', array( __CLASS__, 'breadcrumb_without_shop' ), 20 );
		add_filter( 'rank_math/frontend/breadcrumb/items', array( __CLASS__, 'breadcrumb_without_shop' ), 20 );
		// Prix à 0 jamais affiché (« 0,00 € » n'est pas présentable).
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'hide_zero_price' ), 100, 2 );
		// Produits sur devis et produits à 0 € en tête de la boutique et des catégories, quel que soit le tri.
		add_filter( 'posts_clauses', array( __CLASS__, 'devis_first' ), 20, 2 );
		// Notre requête peut compter plus de pages que la requête principale : pas de page 404 sur ces pages.
		add_filter( 'pre_handle_404', array( __CLASS__, 'no_404_on_extra_pages' ), 10, 2 );
	}

	/**
	 * Produits de la page : requête dédiée (comme l'ancienne grille Royal), avec les mêmes règles que WooCommerce
	 * (visibilité du catalogue, filtres, tri choisi), mais indépendante des réglages ou extraits qui modifient
	 * la requête principale. Les produits sur devis y figurent donc toujours.
	 */
	public static function query() {
		if ( null !== self::$query ) {
			return self::$query;
		}
		global $wp_query;
		$per_page = (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
		$args     = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page > 0 ? $per_page : 12,
			'paged'               => max( 1, (int) $wp_query->get( 'paged' ) ),
			'ignore_sticky_posts' => true,
			'lmdd_catalogue'      => 1,
		);
		$tax = WC()->query->get_tax_query( array(), true );
		$t   = self::term();
		if ( $t ) {
			$tax[] = array( 'taxonomy' => $t->taxonomy, 'field' => 'term_id', 'terms' => array( $t->term_id ), 'include_children' => true );
		}
		$args['tax_query']  = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery
		$args['meta_query'] = WC()->query->get_meta_query( array(), true ); // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( $wp_query->get( 's' ) ) {
			$args['s'] = $wp_query->get( 's' );
		}
		$args = array_merge( $args, WC()->query->get_catalog_ordering_args() );
		self::$query = new WP_Query( $args );
		WC()->query->remove_ordering_args();
		return self::$query;
	}

	public static function no_404_on_extra_pages( $pre, $query ) {
		if ( ! $pre && $query->is_main_query() && $query->get( 'paged' ) > 1 && ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) && ! $query->is_singular() ) {
			$q = get_queried_object();
			if ( $query->is_post_type_archive( 'product' ) || ( $q instanceof WP_Term ) ) {
				status_header( 200 );
				return true;
			}
		}
		return $pre;
	}

	// ------------------------------------------------------------------ Règles d'affichage du catalogue

	/** Retire l'étape « Boutique » du fil d'Ariane, sauf sur la page Boutique elle-même. */
	public static function breadcrumb_without_shop( $crumbs ) {
		if ( ! is_array( $crumbs ) || ! function_exists( 'wc_get_page_id' ) || is_shop() ) {
			return $crumbs;
		}
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id <= 0 ) {
			return $crumbs;
		}
		$url = untrailingslashit( get_permalink( $shop_id ) );
		$out = array_values( array_filter( $crumbs, function ( $c ) use ( $url ) {
			return ! ( isset( $c[1] ) && $c[1] && untrailingslashit( $c[1] ) === $url );
		} ) );
		return $out ? $out : $crumbs;
	}

	/** Prix nul : rien n'est affiché (fiches, catégories, recherche, produits liés…). */
	public static function hide_zero_price( $html, $product ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $html;
		}
		$price = $product ? $product->get_price() : '';
		if ( '' === $price || null === $price ) {
			return $html;
		}
		if ( $product->is_type( 'variable' ) ) {
			return (float) $product->get_variation_price( 'max' ) <= 0 ? '' : $html;
		}
		return (float) $price <= 0 ? '' : $html;
	}

	/**
	 * Requête principale de la boutique et des catégories : les produits sur devis et les produits à 0 €
	 * passent en premier, puis l'ordre choisi par le visiteur (par défaut, popularité, prix…) s'applique.
	 */
	public static function devis_first( $clauses, $query ) {
		if ( $query->get( 'lmdd_catalogue' ) ) {
			// Notre requête de catalogue : toujours concernée.
		} elseif ( is_admin() || ! $query->is_main_query() || ! function_exists( 'is_shop' ) || ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) || ( function_exists( 'wc_get_page_id' ) && (int) $query->get( 'page_id' ) === wc_get_page_id( 'shop' ) ) ) ) {
			return $clauses;
		}
		global $wpdb;
		$tt_ids = array();
		$words  = array_filter( array_map( 'trim', explode( ',', LMDD_DS_Store::settings()['categories'] ) ) );
		if ( $words ) {
			$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
			foreach ( is_wp_error( $terms ) ? array() : $terms as $t ) {
				foreach ( $words as $w ) {
					if ( false !== strpos( $t->slug, sanitize_title( $w ) ) ) {
						$tt_ids[] = (int) $t->term_taxonomy_id;
					}
				}
			}
		}
		$in_cat = $tt_ids
			? "EXISTS ( SELECT 1 FROM {$wpdb->term_relationships} lmdd_tr WHERE lmdd_tr.object_id = {$wpdb->posts}.ID AND lmdd_tr.term_taxonomy_id IN (" . implode( ',', array_unique( $tt_ids ) ) . ') )'
			: '0';
		// Sous-requêtes (pas de jointure) : compatibles avec GROUP BY et ONLY_FULL_GROUP_BY, sans effet sur les lignes renvoyées.
		$price = "( SELECT MAX( lmdd_lk.max_price ) FROM {$wpdb->wc_product_meta_lookup} lmdd_lk WHERE lmdd_lk.product_id = {$wpdb->posts}.ID )";
		$force = "( SELECT MAX( lmdd_dv.meta_value ) FROM {$wpdb->postmeta} lmdd_dv WHERE lmdd_dv.post_id = {$wpdb->posts}.ID AND lmdd_dv.meta_key = '_lmdd_devis' )";
		$first = "( CASE WHEN COALESCE( {$price}, 0 ) <= 0 THEN 0 WHEN {$force} = 'oui' THEN 0 WHEN {$force} = 'non' THEN 1 WHEN {$in_cat} THEN 0 ELSE 1 END )";
		$clauses['orderby'] = $first . ' ASC' . ( $clauses['orderby'] ? ', ' . $clauses['orderby'] : '' );
		return $clauses;
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
		$total = (int) self::query()->found_posts;
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
		$q = self::query();
		if ( function_exists( 'wc_setup_loop' ) ) {
			wc_set_loop_prop( 'is_paginated', true );
			wc_set_loop_prop( 'total', (int) $q->found_posts );
			wc_set_loop_prop( 'total_pages', (int) $q->max_num_pages );
			wc_set_loop_prop( 'per_page', (int) $q->get( 'posts_per_page' ) );
			wc_set_loop_prop( 'current_page', max( 1, (int) $q->get( 'paged' ) ) );
		}
		ob_start();
		echo '<div class="lmdd-shop">';
		if ( ! $q->have_posts() ) {
			echo '<div class="lmdd-shop__empty"><p>Aucun produit dans cette catégorie pour le moment.</p><a class="lmdd-chip" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Voir toute la boutique</a></div></div>';
			return ob_get_clean();
		}
		echo '<div class="lmdd-shop__bar">';
		woocommerce_result_count();
		woocommerce_catalog_ordering();
		echo '</div><ul class="lmdd-grid">';
		$i = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
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
