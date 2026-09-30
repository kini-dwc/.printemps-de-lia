<?php
/**
 * Côté site : application des règles, mode test, inventaire des ressources,
 * panneau de la barre d'administration et sonde de mesure d'impact.
 *
 * Modes d'une requête :
 *  - off  : aucune règle (constante ASSET_PILOT_DISABLE ou ?ap_off=1) ;
 *  - live : règles « en ligne » (tous les visiteurs) ;
 *  - test : règles « en ligne » + « en test » (admin avec l'aperçu activé, ou jeton de test valide).
 */

defined( 'ABSPATH' ) || exit;

class AP_Frontend {

	const COOKIE = 'ap_test';

	/** Handles propres à l'extension, jamais listés ni désactivables. */
	const OWN = array( 'asset-pilot-bar', 'admin-bar', 'dashicons' );

	private static $mode    = null;
	private static $tokens  = null;
	private static $active  = null;
	private static $removed = array(); // 'kind:handle' => [ids de règles]

	public static function init() {
		if ( is_admin() ) {
			return;
		}
		add_action( 'init', array( __CLASS__, 'handle_test_cookie' ), 1 );
		add_action( 'template_redirect', array( __CLASS__, 'no_cache_in_test' ), 0 );
		add_filter( 'rocket_cache_reject_cookies', array( __CLASS__, 'reject_cookie' ) );

		// Application des règles : après tous les enqueue, puis juste avant chaque impression.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'apply' ), PHP_INT_MAX - 100 );
		add_action( 'wp_print_styles', array( __CLASS__, 'apply' ), 0 );
		add_action( 'wp_print_scripts', array( __CLASS__, 'apply' ), 0 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'apply' ), 1 );

		add_action( 'wp_footer', array( __CLASS__, 'record_and_bar_data' ), PHP_INT_MAX );
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar' ), 999 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_bar' ) );

		// Sonde de mesure (V2) : ?ap_probe=1
		add_action( 'wp_head', array( __CLASS__, 'probe' ), -9999 );
		add_filter( 'show_admin_bar', array( __CLASS__, 'hide_bar_in_probe' ), 999 );
	}

	// ------------------------------------------------------------------ Modes

	public static function mode() {
		if ( null !== self::$mode ) {
			return self::$mode;
		}
		if ( defined( 'ASSET_PILOT_DISABLE' ) && ASSET_PILOT_DISABLE ) {
			return self::$mode = 'off';
		}
		// ?ap_off=1 : réservé aux administrateurs et aux porteurs du jeton (sinon n'importe qui pourrait contourner le cache).
		if ( ! empty( $_GET['ap_off'] ) && self::is_trusted() ) { // phpcs:ignore WordPress.Security.NonceVerification
			return self::$mode = 'off';
		}
		$token = self::request_token();
		if ( $token && AP_Store::token_is_valid( $token ) ) {
			return self::$mode = 'test';
		}
		if ( self::is_manager() && get_user_meta( get_current_user_id(), 'asset_pilot_preview', true ) ) {
			return self::$mode = 'test';
		}
		return self::$mode = 'live';
	}

	private static function request_token() {
		if ( isset( $_GET['ap_test'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return sanitize_text_field( wp_unslash( $_GET['ap_test'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( isset( $_COOKIE[ self::COOKIE ] ) ) {
			return sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE ] ) );
		}
		return '';
	}

	public static function is_manager() {
		return is_user_logged_in() && current_user_can( 'manage_options' );
	}

	/** Administrateur, ou requête portant un jeton valide (?ap_test, cookie de test ou ?ap_run). */
	public static function is_trusted() {
		if ( self::is_manager() || AP_Store::token_is_valid( self::request_token() ) ) {
			return true;
		}
		return isset( $_GET['ap_run'] ) && AP_Store::token_is_valid( sanitize_text_field( wp_unslash( $_GET['ap_run'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/** ?ap_test=<jeton> mémorise le mode test (cookie de session) ; ?ap_test=0 l'arrête. */
	public static function handle_test_cookie() {
		if ( ! isset( $_GET['ap_test'] ) || ! empty( $_GET['ap_probe'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$token = sanitize_text_field( wp_unslash( $_GET['ap_test'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( '0' === $token ) {
			setcookie( self::COOKIE, '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
			unset( $_COOKIE[ self::COOKIE ] );
			self::$mode = null;
		} elseif ( AP_Store::token_is_valid( $token ) ) {
			setcookie( self::COOKIE, $token, 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		}
	}

	/** Une page en mode test ne doit jamais être mise en cache ni servie depuis le cache. */
	public static function no_cache_in_test() {
		$probe = ! empty( $_GET['ap_probe'] ) && self::is_trusted(); // phpcs:ignore WordPress.Security.NonceVerification
		if ( 'test' === self::mode() || 'off' === self::mode() || $probe ) {
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
			nocache_headers();
		}
	}

	public static function reject_cookie( $cookies ) {
		$cookies[] = self::COOKIE;
		return $cookies;
	}

	/** Requêtes où rien ne doit jamais être retiré (éditeurs visuels, aperçus, flux…). */
	private static function excluded() {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_customize_preview() || is_feed() ) {
			return true;
		}
		foreach ( array( 'elementor-preview', 'fl_builder', 'et_fb', 'ct_builder', 'bricks', 'vc_editable', 'tve' ) as $param ) {
			if ( isset( $_GET[ $param ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return true;
			}
		}
		return false;
	}

	// ------------------------------------------------------------------ Règles

	public static function tokens() {
		if ( null === self::$tokens ) {
			self::$tokens = AP_Context::tokens();
		}
		return self::$tokens;
	}

	/** Règles qui s'appliquent à la requête courante. */
	public static function active_rules() {
		if ( null !== self::$active ) {
			return self::$active;
		}
		$mode = self::mode();
		if ( 'off' === $mode || self::excluded() ) {
			return self::$active = array();
		}
		$statuses = 'test' === $mode ? array( 'live', 'test' ) : array( 'live' );
		$tokens   = self::tokens();
		$out      = array();
		foreach ( AP_Store::rules() as $r ) {
			if ( ! in_array( $r['status'], $statuses, true ) ) {
				continue;
			}
			if ( self::rule_matches( $r, $tokens ) ) {
				$out[] = $r;
			}
		}
		return self::$active = $out;
	}

	public static function rule_matches( array $r, array $tokens ) {
		return in_array( $r['scope'], $tokens, true ) && ! array_intersect( (array) $r['except'], $tokens );
	}

	public static function apply() {
		if ( ! did_action( 'wp' ) ) {
			return; // contexte pas encore connu
		}
		foreach ( self::active_rules() as $r ) {
			if ( in_array( $r['handle'], self::OWN, true ) ) {
				continue;
			}
			$deps = 'style' === $r['kind'] ? wp_styles() : wp_scripts();
			$h    = $r['handle'];
			if ( ! isset( $deps->registered[ $h ] ) ) {
				continue; // pas (encore) enregistré sur cette page
			}
			$deps->dequeue( $h );
			if ( ! empty( $r['force'] ) ) {
				// Forcer : on retire l'enregistrement → les ressources qui en dépendent ne sont plus imprimées non plus.
				$deps->remove( $h );
			}
			$key = $r['kind'] . ':' . $h;
			if ( ! isset( self::$removed[ $key ] ) || ! in_array( $r['id'], self::$removed[ $key ], true ) ) {
				self::$removed[ $key ][] = $r['id'];
			}
		}
	}

	// ------------------------------------------------------------------ Inventaire

	/** Liste des ressources de la page : imprimées + retirées par une règle. */
	private static function page_items() {
		$items = array();
		foreach ( array( 'script' => wp_scripts(), 'style' => wp_styles() ) as $kind => $deps ) {
			$done = array_unique( (array) $deps->done );
			foreach ( $done as $h ) {
				if ( in_array( $h, self::OWN, true ) ) {
					continue;
				}
				$items[ $kind . ':' . $h ] = self::item( $kind, $h, $deps, false );
			}
		}
		foreach ( self::$removed as $key => $rule_ids ) {
			list( $kind, $h ) = explode( ':', $key, 2 );
			$deps = 'style' === $kind ? wp_styles() : wp_scripts();
			$printed = isset( $items[ $key ] );
			$item    = $printed ? $items[ $key ] : self::item( $kind, $h, $deps, true );
			$item['rules']   = $rule_ids;
			$item['removed'] = ! $printed;
			// Retirée mais réimprimée : une autre ressource de la page en dépend.
			$item['kept'] = $printed;
			$items[ $key ] = $item;
		}
		return $items;
	}

	private static function item( $kind, $h, $deps, $removed ) {
		$reg = isset( $deps->registered[ $h ] ) ? $deps->registered[ $h ] : null;
		$src = $reg && $reg->src ? (string) $reg->src : '';
		if ( $src && 0 === strpos( $src, '/' ) && 0 !== strpos( $src, '//' ) ) {
			$src = $deps->base_url . $src;
		}
		return array(
			'kind'    => $kind,
			'handle'  => $h,
			'src'     => $src,
			'deps'    => $reg ? array_values( (array) $reg->deps ) : array(),
			'ver'     => $reg && $reg->ver ? (string) $reg->ver : '',
			'removed' => $removed,
			'rules'   => array(),
			'kept'    => false,
		);
	}

	private static function current_url() {
		$req = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$url = home_url( $req );
		return remove_query_arg( array( 'ap_test', 'ap_off', 'ap_probe', 'ap_scan', 'ap_run' ), $url );
	}

	public static function record_and_bar_data() {
		if ( self::excluded() ) {
			return;
		}
		$scan    = isset( $_GET['ap_scan'] ) && AP_Store::token_is_valid( sanitize_text_field( wp_unslash( $_GET['ap_scan'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$manager = self::is_manager();
		if ( ! $manager && ! $scan && 'test' !== self::mode() ) {
			return;
		}
		$items  = self::page_items();
		$tokens = self::tokens();
		$label  = AP_Context::label();
		$url    = self::current_url();

		// Inventaire : écrit au plus une fois par heure et par type de page, sauf si la liste a changé.
		$sig  = md5( wp_json_encode( array_keys( $items ) ) );
		$tkey = 'ap_seen_' . md5( AP_Store::page_key( $tokens ) );
		if ( $scan || get_transient( $tkey ) !== $sig ) {
			AP_Store::record_assets( array_values( $items ), $label, $url, $tokens );
			set_transient( $tkey, $sig, HOUR_IN_SECONDS );
		}

		if ( ! $manager || ! is_admin_bar_showing() ) {
			return;
		}
		$assets = AP_Store::assets();
		$rules  = AP_Store::rules();
		$out    = array();
		foreach ( $items as $key => $it ) {
			$inv   = isset( $assets[ $key ] ) ? $assets[ $key ] : array();
			$out[] = array(
				'kind'    => $it['kind'],
				'handle'  => $it['handle'],
				'src'     => $it['src'],
				'origin'  => AP_Store::origin( $it['src'] ),
				'size'    => isset( $inv['size'] ) ? (int) $inv['size'] : -1,
				'deps'    => $it['deps'],
				'removed' => $it['removed'],
				'kept'    => $it['kept'],
				'rules'   => $it['rules'],
				'sensitive' => in_array( $it['handle'], AP_Store::SENSITIVE, true ),
			);
		}
		$page_rules = array();
		foreach ( $rules as $r ) {
			if ( self::rule_matches( $r, $tokens ) ) {
				$page_rules[] = $r;
			}
		}
		$scope_options = array();
		foreach ( array_reverse( $tokens ) as $t ) {
			$scope_options[] = array( 'token' => $t, 'label' => AP_Context::token_label( $t ) );
		}
		$data = array(
			'mode'      => self::mode(),
			'preview'   => (bool) get_user_meta( get_current_user_id(), 'asset_pilot_preview', true ),
			'label'     => $label,
			'tokens'    => $tokens,
			'scopes'    => $scope_options,
			'items'     => $out,
			'rules'     => $page_rules,
			'rest'      => esc_url_raw( rest_url( 'asset-pilot/v1/' ) ),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'adminUrl'  => admin_url( 'tools.php?page=asset-pilot' ),
		);
		echo '<script id="asset-pilot-data">window.AssetPilotBar=' . wp_json_encode( $data ) . ';</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	// ------------------------------------------------------------------ Barre d'admin

	public static function enqueue_bar() {
		if ( ! self::is_manager() || ! is_admin_bar_showing() || self::excluded() ) {
			return;
		}
		wp_enqueue_style( 'asset-pilot-bar', ASSET_PILOT_URL . 'assets/bar.css', array(), ASSET_PILOT_VERSION );
		wp_enqueue_script( 'asset-pilot-bar', ASSET_PILOT_URL . 'assets/bar.js', array(), ASSET_PILOT_VERSION, true );
	}

	public static function admin_bar( $bar ) {
		if ( ! self::is_manager() || self::excluded() ) {
			return;
		}
		$mode  = self::mode();
		$count = count( self::active_rules() );
		$title = 'test' === $mode
			? sprintf( '%s (%d)', __( 'Scripts – mode test', 'asset-pilot' ), $count )
			: sprintf( '%s (%d)', __( 'Scripts', 'asset-pilot' ), $count );
		if ( 'off' === $mode ) {
			$title = __( 'Scripts – règles suspendues', 'asset-pilot' );
		}
		$bar->add_node( array(
			'id'    => 'asset-pilot',
			'title' => '<span class="ab-label">' . esc_html( $title ) . '</span>',
			'href'  => admin_url( 'tools.php?page=asset-pilot' ),
			'meta'  => array( 'class' => 'asset-pilot-node asset-pilot-mode-' . $mode ),
		) );
	}

	// ------------------------------------------------------------------ Sonde (V2)

	public static function hide_bar_in_probe( $show ) {
		return ! empty( $_GET['ap_probe'] ) && self::is_trusted() ? false : $show; // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * ?ap_probe=1 : injecte en tout début de <head> un collecteur d'erreurs JavaScript, de ressources
	 * en échec et de métriques. Le résultat est exposé dans window.__apProbe et envoyé à la fenêtre parente.
	 */
	public static function probe() {
		if ( empty( $_GET['ap_probe'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( ! self::is_trusted() ) {
			return;
		}
		$settings = AP_Store::settings();
		$config   = array(
			'mode'   => self::mode(),
			'rules'  => wp_list_pluck( self::active_rules(), 'id' ),
			'checks' => array_values( $settings['checks'] ),
		);
		$js = file_get_contents( ASSET_PILOT_DIR . 'assets/probe.js' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		echo "<script id=\"asset-pilot-probe\">window.__apProbeConfig=" . wp_json_encode( $config ) . ";\n" . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
