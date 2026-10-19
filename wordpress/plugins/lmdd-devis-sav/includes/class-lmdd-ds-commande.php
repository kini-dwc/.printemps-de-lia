<?php
/**
 * Panier et commande : nouvelle mise en page des pages WooCommerce classiques (codes courts [woocommerce_cart] et
 * [woocommerce_checkout]). Étapes, récapitulatif toujours visible, réassurance et barre « livraison gratuite ».
 * Aucun champ, calcul, frais ou moyen de paiement n'est modifié : seuls l'affichage et quelques textes d'aide sont ajoutés.
 * Activation dans Demandes clients → Réglages ; aperçu pour l'administrateur avec ?lmdd_hf_preview=1.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Commande {

	const COOKIE = 'lmdd_apercu_commande';

	private static $active = null;

	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'boot' ), 5 );
		// Toujours actifs, avec ou sans la nouvelle mise en page :
		// options des produits lisibles (panier, commande, compte) et « Continuer mes achats » vers la page précédente.
		add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'clean_item_data' ), 99999 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'options_assets' ), 40 );
		add_action( 'wp_footer', array( __CLASS__, 'remember_page' ), 99 );
	}

	// ------------------------------------------------------------------ Options des produits (TM Extra Product Options…)

	/**
	 * Prix des options au même format partout :
	 * « (+ 698.00€) » ou « (+ 280,00 €) » deviennent « + 698,00 € » dans un petit encadré discret.
	 */
	public static function clean_item_data( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		foreach ( $data as &$d ) {
			foreach ( array( 'display', 'value' ) as $f ) {
				if ( isset( $d[ $f ] ) && is_string( $d[ $f ] ) ) {
					$d[ $f ] = self::format_option_prices( $d[ $f ] );
				}
			}
		}
		return $data;
	}

	public static function format_option_prices( $html ) {
		return preg_replace_callback( '/\(\s*([+-]?)\s*([\d][\d\s\x{00A0}\x{202F}.,]*)\s*(?:€|&euro;|EUR)\s*\)/u', function ( $m ) {
			$n = preg_replace( '/[\s\x{00A0}\x{202F}]/u', '', $m[2] );
			if ( preg_match( '/^\d{1,3}(\.\d{3})+(,\d+)?$/', $n ) ) {        // 1.280,00
				$n = str_replace( array( '.', ',' ), array( '', '.' ), $n );
			} else {                                                         // 1280,00 / 698.00 / 1,280.00
				$n = preg_match( '/,\d{1,2}$/', $n ) ? str_replace( array( '.', ',' ), array( '', '.' ), $n ) : str_replace( ',', '', $n );
			}
			$v = number_format( (float) $n, 2, ',', ' ' );
			return '<span class="lmdd-opt-price">' . ( '' !== $m[1] ? $m[1] . ' ' : '' ) . $v . ' €</span>';
		}, $html );
	}

	public static function options_assets() {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			wp_enqueue_style( 'lmdd-options', plugin_dir_url( LMDD_DS_FILE ) . 'assets/lmdd-options.css', array(), LMDD_DS_VERSION );
		}
	}

	// ------------------------------------------------------------------ « Continuer mes achats » : page précédente

	/**
	 * Mémorise la dernière page de la boutique visitée (produit, catégorie, recherche…) dans le navigateur ; sur le panier,
	 * les boutons « Continuer mes achats » / « Retour à la boutique » y ramènent. Fonctionne avec le cache des pages.
	 */
	public static function remember_page() {
		if ( is_admin() || ! function_exists( 'is_cart' ) ) {
			return;
		}
		if ( is_cart() ) {
			$fallback = wp_get_referer() ? wp_get_referer() : '';
			?>
<script id="lmdd-retour" nowprocket data-no-optimize="1" data-no-defer="1">(function(){var u='';try{u=sessionStorage.getItem('lmdd_retour')||'';}catch(e){}
var r=<?php echo wp_json_encode( esc_url_raw( $fallback ) ); ?>;if(!u&&r&&r.indexOf(location.host)>-1&&!/\/(cart|panier|checkout|commande)\b/.test(r)){u=r;}
if(!u)return;function go(){document.querySelectorAll('a.wc-backward, .return-to-shop a, a.lmdd-retour').forEach(function(a){a.href=u;});}
go();document.body.addEventListener('updated_wc_div',go);if(window.jQuery){jQuery(document.body).on('updated_wc_div updated_cart_totals',go);}})();</script>
			<?php
			return;
		}
		if ( is_checkout() || is_account_page() ) {
			return;
		}
		echo "<script id=\"lmdd-retour\" nowprocket data-no-optimize=\"1\" data-no-defer=\"1\">try{sessionStorage.setItem('lmdd_retour',location.href)}catch(e){}</script>\n";
	}


	/** Actif pour tous (réglage) ou en aperçu pour l'administrateur (?lmdd_hf_preview=1, mémorisé une heure). */
	public static function active() {
		if ( null !== self::$active ) {
			return self::$active;
		}
		self::$active = false;
		if ( ! function_exists( 'is_cart' ) || ! ( is_cart() || is_checkout() ) ) {
			return false;
		}
		if ( LMDD_DS_Store::settings()['commande'] ) {
			return self::$active = true;
		}
		if ( current_user_can( 'manage_options' ) ) {
			if ( ! empty( $_GET['lmdd_hf_preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				setcookie( self::COOKIE, '1', time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
				self::$active = true;
			} elseif ( ! empty( $_COOKIE[ self::COOKIE ] ) ) {
				self::$active = true;
			}
			if ( self::$active && ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
		}
		return self::$active;
	}

	public static function boot() {
		if ( ! self::active() ) {
			return;
		}
		add_filter( 'body_class', function ( $c ) {
			$c[] = 'lmdd-commande';
			return $c;
		} );
		add_action( 'wp_enqueue_scripts', function () {
			wp_enqueue_style( 'lmdd-commande', plugin_dir_url( LMDD_DS_FILE ) . 'assets/lmdd-commande.css', array(), LMDD_DS_VERSION );
		}, 30 );
		// Panier.
		add_action( 'woocommerce_before_cart', array( __CLASS__, 'steps_cart' ), 1 );
		add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'free_shipping_bar' ), 5 );
		add_action( 'woocommerce_after_cart_totals', array( __CLASS__, 'reassurance' ) );
		add_action( 'woocommerce_cart_is_empty', array( __CLASS__, 'steps_cart' ), 1 );
		// Commande.
		add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'steps_checkout' ), 1 );
		add_action( 'woocommerce_review_order_after_submit', array( __CLASS__, 'secure_note' ) );
		// Confirmation.
		add_action( 'woocommerce_before_thankyou', array( __CLASS__, 'steps_done' ), 1 );
	}

	// ------------------------------------------------------------------ Étapes

	private static function steps( $current, $title ) {
		$labels = array( 1 => 'Panier', 2 => 'Livraison et paiement', 3 => 'Confirmation' );
		$h      = '<div class="lmdd-co-head"><ol class="lmdd-steps-co" aria-label="Étapes de la commande">';
		foreach ( $labels as $n => $l ) {
			$state = $n < $current ? 'is-done' : ( $n === $current ? 'is-current' : '' );
			$inner = '<span class="lmdd-steps-co__n">' . ( $n < $current ? LMDD_DS_Front::icon( 'check' ) : $n ) . '</span><span class="lmdd-steps-co__l">' . esc_html( $l ) . '</span>';
			if ( 1 === $n && $current > 1 && $current < 3 ) {
				$inner = '<a href="' . esc_url( wc_get_cart_url() ) . '">' . $inner . '</a>';
			}
			$h .= '<li class="' . $state . '"' . ( $n === $current ? ' aria-current="step"' : '' ) . '>' . $inner . '</li>';
		}
		return $h . '</ol><h1 class="lmdd-co-title">' . esc_html( $title ) . '</h1></div>';
	}

	public static function steps_cart() {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;
		echo self::steps( 1, WC()->cart && ! WC()->cart->is_empty() ? 'Votre panier' : 'Votre panier est vide' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public static function steps_checkout() {
		echo self::steps( 2, 'Livraison et paiement' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public static function steps_done() {
		echo self::steps( 3, 'Merci pour votre commande' ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	// ------------------------------------------------------------------ Livraison gratuite

	/** Barre de progression vers la livraison gratuite (montant des réglages ; simple affichage). */
	public static function free_shipping_bar() {
		$min = LMDD_DS_Store::settings()['franco'];
		if ( '' === $min || ! WC()->cart ) {
			return;
		}
		$min   = (float) $min;
		$total = (float) WC()->cart->get_displayed_subtotal();
		if ( WC()->cart->display_prices_including_tax() ) {
			$total = $total - (float) WC()->cart->get_discount_tax();
		}
		$total = $total - (float) WC()->cart->get_discount_total();
		if ( $min <= 0 ) {
			return;
		}
		$pc = (int) min( 100, round( $total / $min * 100 ) );
		$tx = $total >= $min
			? '<strong>La livraison est offerte</strong> pour cette commande.'
			: 'Plus que <strong>' . wc_price( $min - $total ) . '</strong> pour profiter de la livraison gratuite.';
		echo '<div class="lmdd-franco' . ( $total >= $min ? ' is-ok' : '' ) . '"><p>' . LMDD_DS_Front::icon( 'truck' ) . '<span>' . wp_kses_post( $tx ) . '</span></p>' // phpcs:ignore WordPress.Security.EscapeOutput
			. '<div class="lmdd-franco__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( $pc ) . '" aria-label="Progression vers la livraison gratuite"><span style="width:' . esc_attr( $pc ) . '%"></span></div></div>';
	}

	// ------------------------------------------------------------------ Réassurance

	public static function reassurance() {
		$s     = LMDD_DS_Store::settings();
		$items = array(
			array( 'shield', 'Paiement sécurisé', 'carte bancaire, virement ou chèque' ),
			array( 'truck', 'Livraison soignée', 'et installation des lits à eau' ),
			array( 'phone', 'Une question ?', $s['horaires'] ),
		);
		$tel = LMDD_DS_Front::tel_link_public();
		echo '<ul class="lmdd-co-reassure">';
		foreach ( $items as $i => $it ) {
			echo '<li>' . LMDD_DS_Front::icon( $it[0] ) . '<span><strong>' . esc_html( $it[1] ) . '</strong><small>' . esc_html( $it[2] ) . ( 2 === $i ? ' · ' . $tel : '' ) . '</small></span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '</ul>';
	}

	public static function secure_note() {
		echo '<p class="lmdd-co-secure">' . LMDD_DS_Front::icon( 'shield' ) . '<span>Paiement sécurisé · confirmation de commande envoyée par e-mail</span></p>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
