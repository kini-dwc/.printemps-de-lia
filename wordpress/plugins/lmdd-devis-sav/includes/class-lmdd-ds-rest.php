<?php
/**
 * Réception des formulaires : POST /wp-json/lmdd/v1/demande (multipart).
 * Compatible avec le cache de pages (aucun jeton lié à la session) ; protections : champ piège, délai minimal,
 * limite d'envois par visiteur, Cloudflare Turnstile si les clés sont configurées dans Contact Form 7.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Rest {

	const MAX_PHOTOS   = 3;
	const MAX_SIZE     = 8388608; // 8 Mo.
	const RATE_LIMIT   = 8;       // Envois par visiteur…
	const RATE_WINDOW  = 900;     // … par tranche de 15 minutes.
	const PHOTO_MIMES  = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
		'heic'         => 'image/heic',
		'heif'         => 'image/heif',
	);

	public static function init() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'lmdd/v1', '/demande', array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'submit' ),
				'permission_callback' => '__return_true', // Formulaire public : les contrôles sont faits dans submit().
			) );
		} );
	}

	/** Clés Turnstile de Contact Form 7 (Intégration → Cloudflare Turnstile) : [ clé du site, clé secrète ] ou null. */
	public static function turnstile_keys() {
		if ( class_exists( 'WPCF7' ) && method_exists( 'WPCF7', 'get_option' ) ) {
			$keys = WPCF7::get_option( 'turnstile' );
			if ( is_array( $keys ) && $keys ) {
				$site = array_key_first( $keys );
				if ( $site && ! empty( $keys[ $site ] ) ) {
					return array( $site, $keys[ $site ] );
				}
			}
		}
		$keys = apply_filters( 'lmdd_ds_turnstile_keys', null );
		return is_array( $keys ) && 2 === count( $keys ) ? $keys : null;
	}

	private static function error( $message, $status = 400, $fields = array() ) {
		return new WP_REST_Response( array( 'ok' => false, 'message' => $message, 'fields' => (object) $fields ), $status );
	}

	public static function submit( WP_REST_Request $request ) {
		$p    = $request->get_body_params();
		$type = isset( $p['type'] ) && 'sav' === $p['type'] ? 'sav' : 'devis';

		// 1. Champ piège (invisible pour les humains) et délai minimal de remplissage.
		if ( ! empty( $p['site_web'] ) || ( isset( $p['_t'] ) && (int) $p['_t'] < 3000 ) ) {
			return new WP_REST_Response( array( 'ok' => true, 'message' => 'Merci.' ), 200 ); // Réponse neutre pour ne rien apprendre aux robots.
		}

		// 2. Limite d'envois par visiteur.
		$ip_key = 'lmdd_ds_' . md5( wp_salt( 'nonce' ) . self::ip() );
		$count  = (int) get_transient( $ip_key );
		if ( $count >= self::RATE_LIMIT ) {
			return self::error( 'Trop de demandes envoyées depuis votre connexion. Merci de réessayer dans quelques minutes, ou de nous appeler.', 429 );
		}

		// 3. Cloudflare Turnstile, si configuré.
		$keys = self::turnstile_keys();
		if ( $keys ) {
			$token = isset( $p['cf-turnstile-response'] ) ? sanitize_text_field( $p['cf-turnstile-response'] ) : '';
			$res   = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
				'timeout' => 10,
				'body'    => array( 'secret' => $keys[1], 'response' => $token, 'remoteip' => self::ip() ),
			) );
			$ok = ! is_wp_error( $res ) && ! empty( json_decode( wp_remote_retrieve_body( $res ), true )['success'] );
			if ( ! $ok ) {
				return self::error( 'La vérification anti-robot n\'a pas abouti. Merci de réessayer.', 403 );
			}
		}

		// 4. Validation des champs.
		list( $values, $errors ) = LMDD_DS_Forms::validate( $type, $p );
		if ( $errors ) {
			return self::error( 'Merci de vérifier les champs signalés.', 422, $errors );
		}

		// 5. Contexte : produit (devis), configuration choisie, page d'origine, photos (SAV).
		$context = array( 'page' => esc_url_raw( wp_get_referer() ?: ( isset( $p['page'] ) ? $p['page'] : '' ) ) );
		if ( 'devis' === $type ) {
			$product = ! empty( $p['produit_id'] ) && function_exists( 'wc_get_product' ) ? wc_get_product( absint( $p['produit_id'] ) ) : null;
			if ( $product ) {
				$context['produit_id'] = $product->get_id();
				$context['produit']    = wp_strip_all_tags( LMDD_DS_Front::short_title( $product ) );
				$context['page']       = get_permalink( $product->get_id() );
			}
			$context['configuration'] = isset( $p['configuration'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $p['configuration'] ) ), 0, 3000 ) : '';
		} else {
			$photos = self::handle_photos( $request->get_file_params() );
			if ( is_wp_error( $photos ) ) {
				return self::error( $photos->get_error_message(), 422, array( 'photos' => $photos->get_error_message() ) );
			}
			$context['photos'] = $photos;
		}

		set_transient( $ip_key, $count + 1, self::RATE_WINDOW );

		$id = LMDD_DS_Store::save( $type, $values, $context );
		if ( is_wp_error( $id ) ) {
			return self::error( 'Votre demande n\'a pas pu être enregistrée. Merci de nous appeler.', 500 );
		}
		LMDD_DS_Store::notify( $id );

		return new WP_REST_Response( array(
			'ok'        => true,
			'reference' => LMDD_DS_Store::reference( $id ),
			'message'   => 'Merci, votre demande est bien arrivée.',
		), 200 );
	}

	/** Photos SAV : 3 maximum, 8 Mo chacune, images uniquement (type réel vérifié), noms aléatoires, dossier non listable. */
	private static function handle_photos( array $files ) {
		if ( empty( $files['photos'] ) || empty( $files['photos']['name'] ) ) {
			return array();
		}
		$f    = $files['photos'];
		$list = array();
		foreach ( (array) $f['name'] as $i => $name ) {
			if ( '' === $name || UPLOAD_ERR_NO_FILE === (int) ( (array) $f['error'] )[ $i ] ) {
				continue;
			}
			$list[] = array(
				'name'     => $name,
				'type'     => ( (array) $f['type'] )[ $i ],
				'tmp_name' => ( (array) $f['tmp_name'] )[ $i ],
				'error'    => ( (array) $f['error'] )[ $i ],
				'size'     => ( (array) $f['size'] )[ $i ],
			);
		}
		if ( count( $list ) > self::MAX_PHOTOS ) {
			return new WP_Error( 'photos', 'Trois photos au maximum.' );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$dir_filter = function ( $dirs ) {
			$dirs['subdir'] = '/lmdd-demandes' . $dirs['subdir'];
			$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
			$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
			return $dirs;
		};
		$out = array();
		add_filter( 'upload_dir', $dir_filter );
		foreach ( $list as $file ) {
			if ( UPLOAD_ERR_OK !== (int) $file['error'] || $file['size'] > self::MAX_SIZE ) {
				remove_filter( 'upload_dir', $dir_filter );
				return new WP_Error( 'photos', 'Une photo dépasse 8 Mo ou n\'a pas pu être envoyée.' );
			}
			$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], self::PHOTO_MIMES );
			if ( empty( $check['type'] ) ) {
				remove_filter( 'upload_dir', $dir_filter );
				return new WP_Error( 'photos', 'Seules les photos (JPEG, PNG, WebP, HEIC) sont acceptées.' );
			}
			$file['name'] = 'sav-' . wp_generate_password( 16, false ) . '.' . $check['ext'];
			$up           = wp_handle_upload( $file, array( 'test_form' => false, 'mimes' => self::PHOTO_MIMES ) );
			if ( ! empty( $up['error'] ) ) {
				remove_filter( 'upload_dir', $dir_filter );
				return new WP_Error( 'photos', 'Photo refusée : ' . $up['error'] );
			}
			$out[] = array( 'file' => $up['file'], 'url' => $up['url'] );
		}
		remove_filter( 'upload_dir', $dir_filter );
		if ( $out ) {
			$base = trailingslashit( wp_upload_dir()['basedir'] ) . 'lmdd-demandes/';
			if ( ! file_exists( $base . 'index.php' ) ) {
				file_put_contents( $base . 'index.php', "<?php // Silence.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				file_put_contents( $base . '.htaccess', "Options -Indexes\n<FilesMatch \"\\.(php|phtml|phar)$\">\nRequire all denied\n</FilesMatch>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			}
		}
		return $out;
	}

	private static function ip() {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}
}
