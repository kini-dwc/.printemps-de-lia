<?php
/**
 * Stockage : règles, réglages, inventaire des ressources, URL d'exemple et rapports d'impact.
 * Tout est rangé dans des options non chargées automatiquement (autoload = no), sauf les règles.
 */

defined( 'ABSPATH' ) || exit;

class AP_Store {

	const RULES    = 'asset_pilot_rules';
	const SETTINGS = 'asset_pilot_settings';
	const ASSETS   = 'asset_pilot_assets';
	const URLS     = 'asset_pilot_urls';
	const REPORTS  = 'asset_pilot_reports';

	/** Handles dont la désactivation casse presque toujours quelque chose. */
	const SENSITIVE = array(
		'jquery', 'jquery-core', 'jquery-migrate', 'wp-polyfill', 'wp-hooks', 'wp-i18n',
		'woocommerce', 'wc-cart', 'wc-checkout', 'wc-add-to-cart', 'wc-add-to-cart-variation', 'wc-single-product',
		'elementor-frontend', 'elementor-webpack-runtime', 'elementor-frontend-modules',
	);

	// ---------------------------------------------------------------- Règles

	public static function rules() {
		$rules = get_option( self::RULES, array() );
		return is_array( $rules ) ? array_values( $rules ) : array();
	}

	public static function save_rules( array $rules ) {
		update_option( self::RULES, array_values( $rules ), true );
		self::purge_caches();
	}

	/**
	 * Normalise une règle reçue de l'interface. Retourne WP_Error si invalide.
	 */
	public static function sanitize_rule( array $in, array $existing = array() ) {
		$r = array_merge(
			array(
				'id'      => 'r' . substr( md5( uniqid( '', true ) ), 0, 10 ),
				'kind'    => 'script',
				'handle'  => '',
				'scope'   => 'site',
				'except'  => array(),
				'force'   => false,
				'status'  => 'test',
				'note'    => '',
				'created' => time(),
				'author'  => get_current_user_id(),
			),
			$existing,
			array_intersect_key( $in, array_flip( array( 'kind', 'handle', 'scope', 'except', 'force', 'status', 'note' ) ) )
		);

		$r['kind']   = 'style' === $r['kind'] ? 'style' : 'script';
		$r['handle'] = sanitize_text_field( (string) $r['handle'] );
		$r['scope']  = self::sanitize_token( (string) $r['scope'] );
		$r['except'] = array_values( array_filter( array_map( array( __CLASS__, 'sanitize_token' ), (array) $r['except'] ) ) );
		$r['force']  = (bool) $r['force'];
		$r['status'] = 'live' === $r['status'] ? 'live' : 'test';
		$r['note']   = sanitize_text_field( (string) $r['note'] );

		if ( '' === $r['handle'] ) {
			return new WP_Error( 'ap_handle', __( 'Handle manquant.', 'asset-pilot' ), array( 'status' => 400 ) );
		}
		if ( '' === $r['scope'] ) {
			return new WP_Error( 'ap_scope', __( 'Portée invalide.', 'asset-pilot' ), array( 'status' => 400 ) );
		}
		return $r;
	}

	/** Jetons de contexte autorisés : site, front, archive, 404, id:123, type:product, woo:cart… */
	public static function sanitize_token( $t ) {
		$t = strtolower( trim( (string) $t ) );
		if ( in_array( $t, array( 'site', 'front', 'archive', '404', 'search' ), true ) ) {
			return $t;
		}
		if ( preg_match( '/^id:\d+$/', $t ) || preg_match( '/^type:[a-z0-9_\-]+$/', $t ) || preg_match( '/^woo:(cart|checkout|account|archive|product)$/', $t ) ) {
			return $t;
		}
		return '';
	}

	// ---------------------------------------------------------------- Réglages

	public static function settings() {
		$s = get_option( self::SETTINGS, array() );
		$s = is_array( $s ) ? $s : array();
		$changed = false;
		if ( empty( $s['token'] ) ) {
			$s['token'] = wp_generate_password( 24, false, false );
			$changed    = true;
		}
		if ( ! isset( $s['checks'] ) || ! is_array( $s['checks'] ) ) {
			// Éléments dont la présence est vérifiée lors des tests d'impact.
			$s['checks'] = array(
				array( 'selector' => '.single_add_to_cart_button', 'label' => 'Bouton « Ajouter au panier »' ),
				array( 'selector' => '#place_order', 'label' => 'Bouton « Commander »' ),
				array( 'selector' => '.woocommerce-cart-form, .wc-block-cart', 'label' => 'Formulaire du panier' ),
			);
			$changed = true;
		}
		if ( $changed ) {
			update_option( self::SETTINGS, $s, false );
		}
		return $s;
	}

	public static function update_settings( array $patch ) {
		$s = array_merge( self::settings(), $patch );
		update_option( self::SETTINGS, $s, false );
		return $s;
	}

	public static function token_is_valid( $token ) {
		$s = self::settings();
		return is_string( $token ) && '' !== $token && hash_equals( (string) $s['token'], $token );
	}

	// ---------------------------------------------------------------- Inventaire

	public static function assets() {
		$a = get_option( self::ASSETS, array() );
		return is_array( $a ) ? $a : array();
	}

	/**
	 * Fusionne les ressources vues sur une page dans l'inventaire.
	 *
	 * @param array  $seen   liste de [kind, handle, src, deps, ver, removed]
	 * @param string $label  libellé de la page (ex. « Produit : Lit à eau Havre »)
	 * @param string $url    URL de la page
	 * @param array  $tokens jetons de contexte de la page
	 */
	public static function record_assets( array $seen, $label, $url, array $tokens ) {
		$all  = self::assets();
		$now  = time();
		$page = self::page_key( $tokens );
		foreach ( $seen as $item ) {
			$key = $item['kind'] . ':' . $item['handle'];
			$cur = isset( $all[ $key ] ) ? $all[ $key ] : array(
				'kind'   => $item['kind'],
				'handle' => $item['handle'],
				'pages'  => array(),
			);
			// Une ressource retirée « de force » n'est plus enregistrée : on garde alors ses infos connues.
			if ( '' !== $item['src'] || ! isset( $cur['src'] ) ) {
				$cur['src']    = $item['src'];
				$cur['deps']   = $item['deps'];
				$cur['ver']    = $item['ver'];
				$cur['origin'] = self::origin( $item['src'] );
			}
			if ( ! isset( $cur['size'] ) || $cur['size'] < 0 ) {
				$cur['size'] = self::local_size( $item['src'] );
			}
			$cur['last'] = $now;
			$cur['pages'][ $page ] = array( 'label' => $label, 'url' => $url, 'tokens' => $tokens );
			if ( count( $cur['pages'] ) > 25 ) {
				$cur['pages'] = array_slice( $cur['pages'], -25, null, true );
			}
			$all[ $key ] = $cur;
		}
		update_option( self::ASSETS, $all, false );

		// URL d'exemple par type de page (utilisées pour les tests d'impact)
		$urls = get_option( self::URLS, array() );
		$urls = is_array( $urls ) ? $urls : array();
		$urls[ $page ] = array( 'label' => $label, 'url' => $url, 'last' => $now );
		update_option( self::URLS, $urls, false );
	}

	/** Clé de « type de page » : accueil, un type de contenu, une page WooCommerce… */
	public static function page_key( array $tokens ) {
		foreach ( array( 'front', 'woo:cart', 'woo:checkout', 'woo:account', 'woo:archive' ) as $t ) {
			if ( in_array( $t, $tokens, true ) ) {
				return $t;
			}
		}
		foreach ( $tokens as $t ) {
			if ( 0 === strpos( $t, 'type:' ) ) {
				return $t;
			}
		}
		foreach ( array( 'archive', 'search', '404' ) as $t ) {
			if ( in_array( $t, $tokens, true ) ) {
				return $t;
			}
		}
		return 'site';
	}

	public static function urls() {
		$u = get_option( self::URLS, array() );
		return is_array( $u ) ? $u : array();
	}

	/** Origine lisible d'une ressource : extension, thème, WordPress, externe, inline. */
	public static function origin( $src ) {
		$src = (string) $src;
		if ( '' === $src ) {
			return array( 'type' => 'inline', 'slug' => '', 'label' => 'Inline / virtuel' );
		}
		if ( preg_match( '#/wp-content/plugins/([^/]+)/#', $src, $m ) ) {
			return array( 'type' => 'plugin', 'slug' => $m[1], 'label' => self::plugin_name( $m[1] ) );
		}
		if ( preg_match( '#/wp-content/themes/([^/]+)/#', $src, $m ) ) {
			return array( 'type' => 'theme', 'slug' => $m[1], 'label' => 'Thème : ' . $m[1] );
		}
		if ( preg_match( '#/wp-content/uploads/([^/]+)/#', $src, $m ) ) {
			return array( 'type' => 'uploads', 'slug' => $m[1], 'label' => 'Fichiers générés : ' . $m[1] );
		}
		if ( false !== strpos( $src, '/wp-includes/' ) || false !== strpos( $src, '/wp-admin/' ) ) {
			return array( 'type' => 'core', 'slug' => 'wordpress', 'label' => 'WordPress' );
		}
		$host = wp_parse_url( $src, PHP_URL_HOST );
		$home = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( $host && $host !== $home ) {
			return array( 'type' => 'external', 'slug' => $host, 'label' => 'Externe : ' . $host );
		}
		return array( 'type' => 'site', 'slug' => 'site', 'label' => 'Site' );
	}

	private static function plugin_name( $slug ) {
		static $names = null;
		if ( null === $names ) {
			$names = array();
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			foreach ( get_plugins() as $file => $data ) {
				$names[ dirname( $file ) ] = $data['Name'];
			}
		}
		return isset( $names[ $slug ] ) ? $names[ $slug ] : $slug;
	}

	/** Taille sur disque d'un fichier local (octets), -1 si inconnue. */
	private static function local_size( $src ) {
		$src = (string) $src;
		if ( '' === $src ) {
			return 0;
		}
		$path = wp_parse_url( $src, PHP_URL_PATH );
		if ( ! $path ) {
			return -1;
		}
		$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( $home_path && '/' !== $home_path && 0 === strpos( $path, $home_path ) ) {
			$path = substr( $path, strlen( rtrim( $home_path, '/' ) ) );
		}
		$host = wp_parse_url( $src, PHP_URL_HOST );
		if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
			return -1;
		}
		$file = wp_normalize_path( ABSPATH . ltrim( $path, '/' ) );
		if ( 0 !== strpos( $file, wp_normalize_path( ABSPATH ) ) || ! is_file( $file ) ) {
			return -1;
		}
		return (int) filesize( $file );
	}

	// ---------------------------------------------------------------- Rapports

	public static function reports() {
		$r = get_option( self::REPORTS, array() );
		return is_array( $r ) ? $r : array();
	}

	public static function add_report( array $report ) {
		$all = self::reports();
		array_unshift( $all, $report );
		update_option( self::REPORTS, array_slice( $all, 0, 20 ), false );
	}

	// ---------------------------------------------------------------- Cache

	/** Vide les caches de page connus après un changement de règles. */
	public static function purge_caches() {
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}
		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			autoptimizeCache::clearall();
		}
		do_action( 'litespeed_purge_all' );
		do_action( 'breeze_clear_all_cache' );
		do_action( 'asset_pilot_purge_caches' );
	}
}
