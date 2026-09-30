<?php
/**
 * API REST de l'interface : asset-pilot/v1
 * Toutes les routes exigent manage_options, sauf l'envoi de rapport d'impact qui accepte aussi le jeton de test
 * (utilisé par l'outil de test automatisé en ligne de commande).
 */

defined( 'ABSPATH' ) || exit;

class AP_Rest {

	const NS = 'asset-pilot/v1';

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function can_manage() {
		return current_user_can( 'manage_options' );
	}

	public static function routes() {
		$admin = array( __CLASS__, 'can_manage' );

		register_rest_route( self::NS, '/state', array(
			'methods'             => 'GET',
			'callback'            => array( __CLASS__, 'state' ),
			'permission_callback' => $admin,
		) );
		register_rest_route( self::NS, '/rules', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'create_rule' ),
			'permission_callback' => $admin,
		) );
		register_rest_route( self::NS, '/rules/(?P<id>[a-z0-9]+)', array(
			array(
				'methods'             => 'PATCH',
				'callback'            => array( __CLASS__, 'update_rule' ),
				'permission_callback' => $admin,
			),
			array(
				'methods'             => 'DELETE',
				'callback'            => array( __CLASS__, 'delete_rule' ),
				'permission_callback' => $admin,
			),
		) );
		register_rest_route( self::NS, '/bulk', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'bulk_rules' ),
			'permission_callback' => $admin,
		) );
		register_rest_route( self::NS, '/settings', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'update_settings' ),
			'permission_callback' => $admin,
		) );
		register_rest_route( self::NS, '/assets', array(
			'methods'             => 'DELETE',
			'callback'            => array( __CLASS__, 'clear_assets' ),
			'permission_callback' => $admin,
		) );
		register_rest_route( self::NS, '/reports', array(
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'add_report' ),
				'permission_callback' => array( __CLASS__, 'can_report' ),
			),
			array(
				'methods'             => 'DELETE',
				'callback'            => array( __CLASS__, 'clear_reports' ),
				'permission_callback' => $admin,
			),
		) );
		register_rest_route( self::NS, '/import', array(
			'methods'             => 'POST',
			'callback'            => array( __CLASS__, 'import_rules' ),
			'permission_callback' => $admin,
		) );
	}

	// ------------------------------------------------------------------ État complet

	public static function state() {
		$assets = AP_Store::assets();
		$rules  = AP_Store::rules();

		// Index inverse des dépendances : « requis par »
		$required_by = array();
		foreach ( $assets as $a ) {
			foreach ( (array) $a['deps'] as $d ) {
				$required_by[ $a['kind'] . ':' . $d ][] = $a['handle'];
			}
		}
		$list = array();
		foreach ( $assets as $key => $a ) {
			$a['key']        = $key;
			$a['requiredBy'] = isset( $required_by[ $key ] ) ? array_values( array_unique( $required_by[ $key ] ) ) : array();
			$a['sensitive']  = in_array( $a['handle'], AP_Store::SENSITIVE, true );
			$a['pages']      = array_values( $a['pages'] );
			$list[]          = $a;
		}

		$scopes = array(
			array( 'token' => 'site', 'label' => AP_Context::token_label( 'site' ) ),
			array( 'token' => 'front', 'label' => AP_Context::token_label( 'front' ) ),
		);
		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $pt ) {
			if ( 'attachment' === $pt->name ) {
				continue;
			}
			$scopes[] = array( 'token' => 'type:' . $pt->name, 'label' => AP_Context::token_label( 'type:' . $pt->name ) );
		}
		if ( class_exists( 'WooCommerce' ) ) {
			foreach ( array( 'woo:product', 'woo:archive', 'woo:cart', 'woo:checkout', 'woo:account' ) as $t ) {
				$scopes[] = array( 'token' => $t, 'label' => AP_Context::token_label( $t ) );
			}
		}
		foreach ( array( 'archive', 'search', '404' ) as $t ) {
			$scopes[] = array( 'token' => $t, 'label' => AP_Context::token_label( $t ) );
		}
		// Pages déjà rencontrées → portée « uniquement cette page »
		foreach ( $assets as $a ) {
			foreach ( $a['pages'] as $p ) {
				foreach ( (array) $p['tokens'] as $t ) {
					if ( 0 === strpos( $t, 'id:' ) ) {
						$scopes[ $t ] = array( 'token' => $t, 'label' => AP_Context::token_label( $t ) );
					}
				}
			}
		}

		foreach ( $rules as &$r ) {
			$r['scopeLabel']   = AP_Context::token_label( $r['scope'] );
			$r['exceptLabels'] = array_map( array( 'AP_Context', 'token_label' ), (array) $r['except'] );
		}
		unset( $r );

		$settings = AP_Store::settings();
		return array(
			'assets'   => $list,
			'rules'    => $rules,
			'scopes'   => array_values( $scopes ),
			'urls'     => array_values( AP_Store::urls() ),
			'reports'  => AP_Store::reports(),
			'settings' => array(
				'token'    => $settings['token'],
				'checks'   => $settings['checks'],
				'preview'  => (bool) get_user_meta( get_current_user_id(), 'asset_pilot_preview', true ),
				'disabled' => defined( 'ASSET_PILOT_DISABLE' ) && ASSET_PILOT_DISABLE,
				'home'     => home_url( '/' ),
				'testUrl'  => add_query_arg( 'ap_test', $settings['token'], home_url( '/' ) ),
				'offUrl'   => add_query_arg( 'ap_off', 1, home_url( '/' ) ),
				'restUrl'  => rest_url( self::NS . '/' ),
			),
		);
	}

	// ------------------------------------------------------------------ Règles

	public static function create_rule( WP_REST_Request $req ) {
		$rule = AP_Store::sanitize_rule( (array) $req->get_json_params() );
		if ( is_wp_error( $rule ) ) {
			return $rule;
		}
		$rules = AP_Store::rules();
		// Pas de doublon exact
		foreach ( $rules as $r ) {
			if ( $r['kind'] === $rule['kind'] && $r['handle'] === $rule['handle'] && $r['scope'] === $rule['scope'] ) {
				return new WP_Error( 'ap_duplicate', __( 'Une règle identique existe déjà.', 'asset-pilot' ), array( 'status' => 409 ) );
			}
		}
		$rules[] = $rule;
		AP_Store::save_rules( $rules );
		return $rule;
	}

	public static function update_rule( WP_REST_Request $req ) {
		$id    = $req['id'];
		$rules = AP_Store::rules();
		foreach ( $rules as $i => $r ) {
			if ( $r['id'] === $id ) {
				$new = AP_Store::sanitize_rule( (array) $req->get_json_params(), $r );
				if ( is_wp_error( $new ) ) {
					return $new;
				}
				$rules[ $i ] = $new;
				AP_Store::save_rules( $rules );
				return $new;
			}
		}
		return new WP_Error( 'ap_not_found', __( 'Règle introuvable.', 'asset-pilot' ), array( 'status' => 404 ) );
	}

	public static function delete_rule( WP_REST_Request $req ) {
		$id    = $req['id'];
		$rules = AP_Store::rules();
		$left  = array_values( array_filter( $rules, function ( $r ) use ( $id ) {
			return $r['id'] !== $id;
		} ) );
		if ( count( $left ) === count( $rules ) ) {
			return new WP_Error( 'ap_not_found', __( 'Règle introuvable.', 'asset-pilot' ), array( 'status' => 404 ) );
		}
		AP_Store::save_rules( $left );
		return array( 'deleted' => $id );
	}

	/** Actions groupées : promote (test → en ligne), demote (en ligne → test), delete_test, delete_all. */
	public static function bulk_rules( WP_REST_Request $req ) {
		$action = (string) $req->get_param( 'action' );
		$ids    = (array) $req->get_param( 'ids' );
		$rules  = AP_Store::rules();
		$target = function ( $r ) use ( $ids ) {
			return empty( $ids ) || in_array( $r['id'], $ids, true );
		};
		switch ( $action ) {
			case 'promote':
				foreach ( $rules as &$r ) {
					if ( $target( $r ) ) {
						$r['status'] = 'live';
					}
				}
				unset( $r );
				break;
			case 'demote':
				foreach ( $rules as &$r ) {
					if ( $target( $r ) ) {
						$r['status'] = 'test';
					}
				}
				unset( $r );
				break;
			case 'delete':
				$rules = array_values( array_filter( $rules, function ( $r ) use ( $target ) {
					return ! $target( $r );
				} ) );
				break;
			case 'delete_test':
				$rules = array_values( array_filter( $rules, function ( $r ) {
					return 'test' !== $r['status'];
				} ) );
				break;
			default:
				return new WP_Error( 'ap_action', __( 'Action inconnue.', 'asset-pilot' ), array( 'status' => 400 ) );
		}
		AP_Store::save_rules( $rules );
		return array( 'rules' => $rules );
	}

	public static function import_rules( WP_REST_Request $req ) {
		$payload = (array) $req->get_json_params();
		$in      = isset( $payload['rules'] ) ? (array) $payload['rules'] : $payload;
		$replace = ! empty( $payload['replace'] );
		$rules   = $replace ? array() : AP_Store::rules();
		$count   = 0;
		foreach ( $in as $raw ) {
			if ( ! is_array( $raw ) ) {
				continue;
			}
			$raw['status'] = 'test'; // toujours importées en test, par sécurité
			unset( $raw['id'] );
			$rule = AP_Store::sanitize_rule( $raw );
			if ( ! is_wp_error( $rule ) ) {
				$rules[] = $rule;
				$count++;
			}
		}
		AP_Store::save_rules( $rules );
		return array( 'imported' => $count );
	}

	// ------------------------------------------------------------------ Réglages

	public static function update_settings( WP_REST_Request $req ) {
		$p = (array) $req->get_json_params();
		if ( isset( $p['preview'] ) ) {
			update_user_meta( get_current_user_id(), 'asset_pilot_preview', $p['preview'] ? 1 : 0 );
		}
		$patch = array();
		if ( ! empty( $p['regenerateToken'] ) ) {
			$patch['token'] = wp_generate_password( 24, false, false );
		}
		if ( isset( $p['checks'] ) && is_array( $p['checks'] ) ) {
			$checks = array();
			foreach ( $p['checks'] as $c ) {
				$sel = isset( $c['selector'] ) ? trim( wp_strip_all_tags( (string) $c['selector'] ) ) : '';
				if ( '' !== $sel ) {
					$checks[] = array(
						'selector' => $sel,
						'label'    => sanitize_text_field( isset( $c['label'] ) ? $c['label'] : $sel ),
					);
				}
			}
			$patch['checks'] = $checks;
		}
		if ( $patch ) {
			AP_Store::update_settings( $patch );
		}
		return self::state();
	}

	public static function clear_assets() {
		delete_option( AP_Store::ASSETS );
		delete_option( AP_Store::URLS );
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_ap\_seen\_%' OR option_name LIKE '\_transient\_timeout\_ap\_seen\_%'" ); // phpcs:ignore WordPress.DB
		return array( 'cleared' => true );
	}

	// ------------------------------------------------------------------ Rapports d'impact

	public static function can_report( WP_REST_Request $req ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$token = (string) $req->get_header( 'x-asset-pilot-token' );
		return AP_Store::token_is_valid( $token );
	}

	public static function add_report( WP_REST_Request $req ) {
		$r = (array) $req->get_json_params();
		// On ne garde que les champs attendus, bornés en taille.
		$report = array(
			'id'      => 'rep' . substr( md5( uniqid( '', true ) ), 0, 8 ),
			'date'    => time(),
			'source'  => sanitize_text_field( isset( $r['source'] ) ? $r['source'] : 'interface' ),
			'summary' => isset( $r['summary'] ) ? self::clean( $r['summary'] ) : array(),
			'pages'   => isset( $r['pages'] ) ? array_slice( self::clean( (array) $r['pages'] ), 0, 50 ) : array(),
			'rules'   => isset( $r['rules'] ) ? array_slice( array_map( 'sanitize_text_field', (array) $r['rules'] ), 0, 200 ) : array(),
		);
		AP_Store::add_report( $report );
		return $report;
	}

	public static function clear_reports() {
		delete_option( AP_Store::REPORTS );
		return array( 'cleared' => true );
	}

	/** Nettoyage récursif : chaînes nettoyées et tronquées, nombres et booléens conservés. */
	private static function clean( $v, $depth = 0 ) {
		if ( $depth > 6 ) {
			return null;
		}
		if ( is_array( $v ) ) {
			$out = array();
			foreach ( array_slice( $v, 0, 200, true ) as $k => $x ) {
				$out[ is_int( $k ) ? $k : sanitize_key( $k ) ] = self::clean( $x, $depth + 1 );
			}
			return $out;
		}
		if ( is_bool( $v ) || is_int( $v ) || is_float( $v ) || null === $v ) {
			return $v;
		}
		return mb_substr( sanitize_text_field( (string) $v ), 0, 500 );
	}
}
