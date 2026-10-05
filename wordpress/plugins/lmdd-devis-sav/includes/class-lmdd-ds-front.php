<?php
/**
 * Partie visible : devis sur la fiche produit (bouton + panneau), formulaire SAV, fiches symptômes,
 * et petits blocs de fiche produit utilisés par le modèle Elementor (en-tête, points clés, réassurance).
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Front {

	private static $dialog_done = false;
	private static $assets_done = false;

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'devis_cta' ) );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'devis_cta_fallback' ), 31 );
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'block_cart_when_quote' ), 10, 2 );

		add_shortcode( 'lmdd_sav_formulaire', array( __CLASS__, 'sc_sav_form' ) );
		add_shortcode( 'lmdd_sav_symptomes', array( __CLASS__, 'sc_symptomes' ) );
		add_shortcode( 'lmdd_produit_entete', array( __CLASS__, 'sc_product_header' ) );
		add_shortcode( 'lmdd_points_cles', array( __CLASS__, 'sc_key_points' ) );
		add_shortcode( 'lmdd_reassurance', array( __CLASS__, 'sc_reassurance' ) );
		add_shortcode( 'lmdd_devis_bouton', array( __CLASS__, 'sc_devis_button' ) );

		if ( is_admin() ) {
			add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'product_fields' ) );
			add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_product_fields' ) );
		}
	}

	// ------------------------------------------------------------------ Produits sur devis

	public static function is_devis( $product ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
		if ( ! $product ) {
			return false;
		}
		$force = $product->get_meta( '_lmdd_devis' );
		if ( 'oui' === $force ) {
			return true;
		}
		if ( 'non' === $force ) {
			return false;
		}
		$words = array_filter( array_map( 'trim', explode( ',', LMDD_DS_Store::settings()['categories'] ) ) );
		if ( ! $words ) {
			return false;
		}
		foreach ( wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) ) as $slug ) {
			foreach ( $words as $w ) {
				if ( false !== strpos( $slug, sanitize_title( $w ) ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/** Titre court affiché (facultatif), sinon le nom du produit. */
	public static function short_title( $product ) {
		$t = $product->get_meta( '_lmdd_titre_court' );
		return $t ? $t : $product->get_name();
	}

	/**
	 * Un produit sur devis ne s'ajoute pas au panier. Le formulaire reste affiché (les options de configuration
	 * en font partie) ; seul le bouton est masqué, et l'ajout est refusé côté serveur.
	 */
	public static function block_cart_when_quote( $passed, $product_id ) {
		if ( $passed && self::is_devis( $product_id ) ) {
			wc_add_notice( 'Ce produit est vendu sur devis : utilisez le bouton « Demander un devis » de sa fiche.', 'error' );
			return false;
		}
		return $passed;
	}

	public static function body_class( $classes ) {
		if ( function_exists( 'is_product' ) && is_product() ) {
			$classes[] = 'lmdd-fiche';
			if ( self::is_devis( get_queried_object_id() ) ) {
				$classes[] = 'lmdd-devis';
			}
		}
		return $classes;
	}

	public static function product_fields() {
		echo '<div class="options_group">';
		woocommerce_wp_select( array(
			'id'          => '_lmdd_devis',
			'label'       => 'Vente sur devis',
			'options'     => array( '' => 'Automatique (selon la catégorie)', 'oui' => 'Oui : bouton « Demander un devis »', 'non' => 'Non : vente en ligne' ),
			'desc_tip'    => true,
			'description' => 'Automatique : « sur devis » si une catégorie du produit contient « ' . LMDD_DS_Store::settings()['categories'] . ' » (Demandes clients → Réglages).',
		) );
		woocommerce_wp_text_input( array(
			'id'          => '_lmdd_titre_court',
			'label'       => 'Titre affiché (court)',
			'placeholder' => 'Ex. : Lit à eau Akva Soft / Soft Q',
			'desc_tip'    => true,
			'description' => 'Titre principal de la fiche, plus lisible. Vide : le nom du produit. Le titre SEO (balise title) n\'est pas modifié.',
		) );
		woocommerce_wp_textarea_input( array(
			'id'          => '_lmdd_points_cles',
			'label'       => 'Points clés',
			'placeholder' => "Garantie d'usine de 10 ans sur les soudures\nLivraison, installation et réglage en France et au Benelux",
			'desc_tip'    => true,
			'description' => 'Un point par ligne (3 ou 4 conseillés), affichés avec une coche sous le titre.',
		) );
		echo '</div>';
	}

	public static function save_product_fields( $product ) {
		// Le nonce et les droits sont vérifiés par WooCommerce avant ce crochet.
		// phpcs:disable WordPress.Security.NonceVerification
		$devis = isset( $_POST['_lmdd_devis'] ) ? sanitize_key( $_POST['_lmdd_devis'] ) : '';
		$product->update_meta_data( '_lmdd_devis', in_array( $devis, array( 'oui', 'non' ), true ) ? $devis : '' );
		$product->update_meta_data( '_lmdd_titre_court', isset( $_POST['_lmdd_titre_court'] ) ? sanitize_text_field( wp_unslash( $_POST['_lmdd_titre_court'] ) ) : '' );
		$product->update_meta_data( '_lmdd_points_cles', isset( $_POST['_lmdd_points_cles'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_lmdd_points_cles'] ) ) : '' );
		// phpcs:enable
	}

	// ------------------------------------------------------------------ Ressources

	public static function register_assets() {
		$v   = LMDD_DS_VERSION;
		$url = plugin_dir_url( LMDD_DS_FILE ) . 'assets/';
		wp_register_style( 'lmdd-ds', $url . 'lmdd-ds.css', array(), $v );
		wp_register_script( 'lmdd-ds', $url . 'lmdd-ds.js', array(), $v, array( 'strategy' => 'defer', 'in_footer' => true ) );
		$keys = LMDD_DS_Rest::turnstile_keys();
		wp_localize_script( 'lmdd-ds', 'LMDD_DS', array(
			'endpoint'  => esc_url_raw( rest_url( 'lmdd/v1/demande' ) ),
			'turnstile' => $keys ? $keys[0] : '',
		) );
		// Chargement dès l'en-tête (pas de saut d'affichage) sur les fiches produits et les pages qui contiennent nos codes courts.
		if ( self::page_needs_assets() ) {
			self::enqueue();
		}
	}

	private static function page_needs_assets() {
		if ( function_exists( 'is_product' ) && is_product() ) {
			return true;
		}
		if ( ! is_singular() ) {
			return false;
		}
		$id = get_queried_object_id();
		$p  = get_post( $id );
		return $p && ( false !== strpos( $p->post_content, '[lmdd_' ) || false !== strpos( (string) get_post_meta( $id, '_elementor_data', true ), '[lmdd_' ) );
	}

	public static function enqueue() {
		if ( ! self::$assets_done ) {
			wp_enqueue_style( 'lmdd-ds' );
			wp_enqueue_script( 'lmdd-ds' );
			self::$assets_done = true;
		}
	}

	// ------------------------------------------------------------------ Rendu des champs

	public static function icon( $name ) {
		$p = array(
			'drop'       => '<path d="M12 3s6 6.4 6 11a6 6 0 0 1-12 0c0-4.6 6-11 6-11z"/>',
			'bed'        => '<path d="M3 18v-7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v7M3 14h18M7 9V7a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v2M3 18v2M21 18v2"/>',
			'flame'      => '<path d="M12 3c1 3.5 5 5.5 5 10a5 5 0 0 1-10 0c0-2.4 1.4-4 2.5-5 .3 1.6 1 2.6 2 3 0-3 .5-5.5.5-8z"/>',
			'flask'      => '<path d="M9 3h6M10 3v6L4.8 18.1A2 2 0 0 0 6.5 21h11a2 2 0 0 0 1.7-2.9L14 9V3M7.5 15h9"/>',
			'wave-sound' => '<path d="M4 10v4M8 7v10M12 4v16M16 7v10M20 10v4"/>',
			'waves'      => '<path d="M2 9c2.5 0 2.5-2 5-2s2.5 2 5 2 2.5-2 5-2 2.5 2 5 2M2 15c2.5 0 2.5-2 5-2s2.5 2 5 2 2.5-2 5-2 2.5 2 5 2"/>',
			'box'        => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8zM3 8l9 5 9-5M12 13v8"/>',
			'hanger'     => '<path d="M12 7a2 2 0 1 1 2-2M12 7v2L3 16a1 1 0 0 0 .6 1.8h16.8A1 1 0 0 0 21 16l-9-7"/>',
			'check'      => '<path d="M5 12.5 10 17 19 7"/>',
			'phone'      => '<path d="M6.6 3h3l1.5 4.5-2.2 1.4a11 11 0 0 0 5.2 5.2l1.4-2.2L20 13.4v3A2.6 2.6 0 0 1 17.4 19 14.4 14.4 0 0 1 4 5.6 2.6 2.6 0 0 1 6.6 3z"/>',
			'truck'      => '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7M7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM17 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>',
			'card'       => '<path d="M3 6h18v12H3zM3 10h18M7 15h4"/>',
			'shield'     => '<path d="M12 3 5 6v6c0 4.4 3 7.6 7 9 4-1.4 7-4.6 7-9V6l-7-3zM9 12l2 2 4-4"/>',
			'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'close'      => '<path d="M6 6l12 12M18 6 6 18"/>',
			'camera'     => '<path d="M4 8h3l2-3h6l2 3h3v11H4zM12 17a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
			'alert'      => '<path d="M12 4 2.5 20h19L12 4zM12 10v4M12 17h.01"/>',
		);
		return '<svg class="lmdd-i" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . ( isset( $p[ $name ] ) ? $p[ $name ] : '' ) . '</svg>';
	}

	private static function field( $form, $name, array $f ) {
		$id   = 'lmdd-' . $form . '-' . $name;
		$req  = ! empty( $f['required'] );
		$star = $req ? ' <span class="lmdd-req" aria-hidden="true">*</span>' : '';
		$help = ! empty( $f['help'] ) ? '<span class="lmdd-help" id="' . esc_attr( $id ) . '-aide">' . esc_html( $f['help'] ) . '</span>' : '';
		$desc = ! empty( $f['help'] ) ? ' aria-describedby="' . esc_attr( $id ) . '-aide"' : '';
		$cls  = 'lmdd-field lmdd-field--' . $f['type'] . ( ! empty( $f['half'] ) ? ' lmdd-field--half' : '' );
		$err  = '<span class="lmdd-err" role="alert"></span>';
		$h    = '';

		switch ( $f['type'] ) {
			case 'radio':
			case 'cards':
				$h .= '<fieldset class="' . esc_attr( $cls ) . '" data-name="' . esc_attr( $name ) . '"><legend>' . esc_html( $f['label'] ) . $star . '</legend><div class="lmdd-choices">';
				foreach ( $f['options'] as $k => $label ) {
					$value = 'cards' === $f['type'] ? $k : $label;
					$icon  = 'cards' === $f['type'] ? self::icon( LMDD_DS_Forms::SYMPTOMES[ $k ][1] ) : '';
					$h    .= '<label class="lmdd-choice' . ( 'cards' === $f['type'] ? ' lmdd-card' : '' ) . '"><input type="radio" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"' . ( $req ? ' required' : '' ) . '><span>' . $icon . esc_html( $label ) . '</span></label>';
				}
				return $h . '</div>' . $help . $err . '</fieldset>';

			case 'select':
				$h .= '<div class="' . esc_attr( $cls ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . $star . '</label><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . $desc . '>';
				foreach ( $f['options'] as $o ) {
					$h .= '<option>' . esc_html( $o ) . '</option>';
				}
				return $h . '</select>' . $help . $err . '</div>';

			case 'textarea':
				return '<div class="' . esc_attr( $cls ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . $star . '</label><textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="3" maxlength="' . (int) $f['max'] . '"' . ( $req ? ' required' : '' ) . ( ! empty( $f['placeholder'] ) ? ' placeholder="' . esc_attr( $f['placeholder'] ) . '"' : '' ) . $desc . '></textarea>' . $help . $err . '</div>';

			case 'files':
				return '<div class="' . esc_attr( $cls ) . '"><span class="lmdd-label">' . esc_html( $f['label'] ) . '</span><label class="lmdd-drop" for="' . esc_attr( $id ) . '">' . self::icon( 'camera' ) . '<span>Ajouter des photos</span><input id="' . esc_attr( $id ) . '" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp,image/heic,image/heif,.heic" multiple data-max="' . (int) $f['max_files'] . '"' . $desc . '></label><ul class="lmdd-files" aria-live="polite"></ul>' . $help . $err . '</div>';

			default:
				$list = '';
				$attr = '';
				if ( ! empty( $f['list'] ) ) {
					$attr .= ' list="' . esc_attr( $id ) . '-liste"';
					$list  = '<datalist id="' . esc_attr( $id ) . '-liste">' . implode( '', array_map( function ( $o ) {
						return '<option value="' . esc_attr( $o ) . '">';
					}, $f['list'] ) ) . '</datalist>';
				}
				foreach ( array( 'autocomplete', 'inputmode', 'placeholder' ) as $a ) {
					if ( ! empty( $f[ $a ] ) ) {
						$attr .= ' ' . $a . '="' . esc_attr( $f[ $a ] ) . '"';
					}
				}
				return '<div class="' . esc_attr( $cls ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . $star . '</label><input id="' . esc_attr( $id ) . '" type="' . esc_attr( $f['type'] ) . '" name="' . esc_attr( $name ) . '" maxlength="' . (int) $f['max'] . '"' . ( $req ? ' required' : '' ) . $attr . $desc . '>' . $list . $help . $err . '</div>';
		}
	}

	/** Champs communs : type, piège à robots, emplacement Turnstile. */
	private static function common( $type ) {
		return '<input type="hidden" name="type" value="' . esc_attr( $type ) . '">'
			. '<div class="lmdd-hp" aria-hidden="true"><label>Site web <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>';
	}

	private static function privacy_note( $what ) {
		$s   = LMDD_DS_Store::settings();
		$url = $s['confidentialite'] ?: get_privacy_policy_url();
		return '<p class="lmdd-note">Vos réponses servent uniquement à ' . esc_html( $what ) . ' : pas de liste de diffusion, aucune transmission à un tiers.'
			. ( $url ? ' <a href="' . esc_url( $url ) . '">Données personnelles</a>' : '' ) . '</p>';
	}

	private static function tel_link( $class = '' ) {
		$t = LMDD_DS_Store::settings()['telephone'];
		return '<a class="' . esc_attr( $class ) . '" href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', preg_replace( '/^0/', '+33', str_replace( ' ', '', $t ) ) ) ) . '">' . esc_html( $t ) . '</a>';
	}

	// ------------------------------------------------------------------ Devis

	public static function devis_cta() {
		global $product;
		if ( ! $product || ! is_product() || ! self::is_devis( $product ) ) {
			return;
		}
		echo self::sc_devis_button(); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/** Produit sur devis sans formulaire d'ajout au panier (ex. : produit simple sans prix) : bouton quand même. */
	public static function devis_cta_fallback() {
		if ( ! self::$dialog_done ) {
			self::devis_cta();
		}
	}

	public static function sc_devis_button() {
		global $product;
		$product = $product ?: ( function_exists( 'wc_get_product' ) ? wc_get_product( get_queried_object_id() ) : null );
		if ( ! $product || self::$dialog_done ) {
			return '';
		}
		self::$dialog_done = true;
		self::enqueue();
		$s     = LMDD_DS_Store::settings();
		$title = self::short_title( $product );
		$img   = $product->get_image_id() ? wp_get_attachment_image( $product->get_image_id(), 'thumbnail', false, array( 'class' => 'lmdd-dlg__thumb', 'alt' => '' ) ) : '';

		$h  = '<div class="lmdd-devis-cta">';
		$h .= '<button type="button" class="lmdd-btn lmdd-btn--accent lmdd-btn--block" data-lmdd-open="devis" aria-haspopup="dialog">Demander un devis ' . self::icon( 'arrow' ) . '</button>';
		$h .= '<p class="lmdd-devis-cta__alt">Réponse personnalisée · ou par téléphone : ' . self::tel_link() . '</p>';
		$h .= '</div>';

		$h .= '<dialog class="lmdd-dlg" id="lmdd-devis" aria-labelledby="lmdd-devis-titre">';
		$h .= '<form class="lmdd-form" data-type="devis" novalidate>' . self::common( 'devis' );
		$h .= '<input type="hidden" name="produit_id" value="' . esc_attr( $product->get_id() ) . '"><textarea name="configuration" hidden></textarea>';
		$h .= '<header class="lmdd-dlg__head">' . $img . '<div><p class="lmdd-dlg__kicker">Demande de devis</p><h2 id="lmdd-devis-titre" class="lmdd-dlg__title">' . esc_html( $title ) . '</h2></div>'
			. '<button type="button" class="lmdd-dlg__close" data-lmdd-close aria-label="Fermer">' . self::icon( 'close' ) . '</button></header>';
		$h .= '<div class="lmdd-dlg__body">';
		$h .= '<section class="lmdd-config" data-lmdd-config><div class="lmdd-config__head"><h3>Votre configuration</h3><button type="button" class="lmdd-link" data-lmdd-close data-lmdd-goto-options>Modifier</button></div>'
			. '<ul class="lmdd-config__list"></ul><p class="lmdd-config__empty">Vous préciserez le modèle, les dimensions et les options avec votre conseiller.</p></section>';
		foreach ( LMDD_DS_Forms::get( 'devis' ) as $key => $section ) {
			$h .= '<fieldset class="lmdd-section lmdd-section--' . esc_attr( $key ) . '"><legend class="lmdd-section__title">' . esc_html( $section['title'] ) . '</legend><div class="lmdd-grid">';
			foreach ( $section['fields'] as $name => $f ) {
				$h .= self::field( 'devis', $name, $f );
			}
			$h .= '</div></fieldset>';
		}
		$h .= '<div class="lmdd-turnstile"></div>';
		$h .= '</div>';
		$h .= '<footer class="lmdd-dlg__foot"><p class="lmdd-form__error" role="alert"></p><button type="submit" class="lmdd-btn lmdd-btn--primary lmdd-btn--block">Envoyer ma demande ' . self::icon( 'arrow' ) . '</button>'
			. self::privacy_note( 'établir votre devis' ) . '</footer>';
		$h .= '<div class="lmdd-success" hidden tabindex="-1"><div class="lmdd-success__icon">' . self::icon( 'check' ) . '</div><h3>Merci, votre demande est bien arrivée</h3>'
			. '<p>Un conseiller vous recontacte, sur le créneau choisi, pour affiner le devis avec vous. <span data-lmdd-email-note>Une copie vous a été envoyée par e-mail.</span></p><p class="lmdd-success__ref"></p>'
			. '<p>Une question d\'ici là ? ' . self::tel_link() . ' · ' . esc_html( $s['horaires'] ) . '</p><button type="button" class="lmdd-btn lmdd-btn--ghost" data-lmdd-close>Revenir à la fiche</button></div>';
		$h .= '</form></dialog>';
		return $h;
	}

	// ------------------------------------------------------------------ SAV

	public static function sc_sav_form() {
		self::enqueue();
		$steps = LMDD_DS_Forms::get( 'sav' );
		$n     = count( $steps ) + 1;
		$h     = '<form class="lmdd-form lmdd-steps" data-type="sav" novalidate enctype="multipart/form-data">' . self::common( 'sav' );
		$h    .= '<div class="lmdd-progress"><p><span data-lmdd-step-label>Étape 1 sur ' . $n . '</span><span data-lmdd-step-pct>' . round( 100 / $n ) . ' %</span></p><div class="lmdd-progress__bar"><span style="width:' . round( 100 / $n ) . '%"></span></div></div>';
		$i     = 0;
		foreach ( $steps as $key => $step ) {
			$h .= '<fieldset class="lmdd-step" data-step="' . (int) $i . '"' . ( $i ? ' hidden' : '' ) . '><legend class="lmdd-step__title">' . esc_html( $step['title'] ) . '</legend>';
			$h .= ! empty( $step['intro'] ) ? '<p class="lmdd-step__intro">' . esc_html( $step['intro'] ) . '</p>' : '';
			$h .= '<div class="lmdd-grid">';
			foreach ( $step['fields'] as $name => $f ) {
				$h .= self::field( 'sav', $name, $f );
			}
			$h .= '</div></fieldset>';
			$i++;
		}
		$h .= '<fieldset class="lmdd-step" data-step="' . (int) $i . '" hidden><legend class="lmdd-step__title">Vérifiez et envoyez</legend><p class="lmdd-step__intro">Un dernier coup d\'œil : vous pouvez revenir en arrière pour corriger.</p><dl class="lmdd-recap"></dl><div class="lmdd-turnstile"></div></fieldset>';
		$h .= '<p class="lmdd-form__error" role="alert"></p>';
		$h .= '<div class="lmdd-steps__nav"><button type="button" class="lmdd-btn lmdd-btn--ghost" data-lmdd-prev hidden>Retour</button>'
			. '<button type="button" class="lmdd-btn lmdd-btn--primary" data-lmdd-next>Continuer ' . self::icon( 'arrow' ) . '</button>'
			. '<button type="submit" class="lmdd-btn lmdd-btn--primary" hidden>Envoyer ma demande ' . self::icon( 'arrow' ) . '</button>'
			. '<span class="lmdd-steps__call">ou appelez le ' . self::tel_link() . '</span></div>';
		$h .= self::privacy_note( 'traiter votre demande' );
		$h .= '<div class="lmdd-success" hidden tabindex="-1"><div class="lmdd-success__icon">' . self::icon( 'check' ) . '</div><h3>Merci, votre demande SAV est bien arrivée</h3>'
			. '<p>Nous vous rappelons avec une solution et, si besoin, un devis. <span data-lmdd-email-note>Une copie vous a été envoyée par e-mail.</span></p><p class="lmdd-success__ref"></p>'
			. '<p>De l\'eau qui s\'écoule en ce moment ? N\'attendez pas : ' . self::tel_link() . '</p></div>';
		return $h . '</form>';
	}

	/** Pièces par symptôme : slugs des produits de la boutique (prix lus en direct). */
	const PIECES = array(
		'fuite'      => array(),
		'deforme'    => array(),
		'chauffage'  => array( 'chauffage-sigma-k', 'chauffage-carbon-classic-carbonclassicheatherl' ),
		'eau'        => array( 'long-life-anti-algues-akva-waterbeds', 'conditionneur-multi-usage-waterclean-plus', 'conditionneur-firstfiller', 'desodorisant-efficace-et-sans-parfum-pour-traiter-tout-support' ),
		'bruit'      => array( 'quietus-lubrifiant-special-vinyle', 'aquafit-nettoyant-vinyle-b-m-europe' ),
		'vagues'     => array(),
		'demenage'   => array( 'pompe-a-vide-dair', 'adaptateur-pour-vidage' ),
		'revetement' => array(),
	);

	const SYMPTOMES_TEXTES = array(
		'fuite'      => array( 'Une fuite vient presque toujours de la soudure ou d\'une perforation, pas du bouchon. Un matelas qui perd de l\'eau se dégonfle : l\'eau n\'est pas sous pression, et le liner — la poche de sécurité sous le matelas — la récupère. Un lit à eau n\'inonde pas une maison.', 'Nous identifions le matelas, nous le remplaçons à l\'identique ou en 100 % compatible, et nous changeons le liner dans le même passage. Sur mesure si le modèle n\'existe plus.' ),
		'deforme'    => array( 'Deux causes. Une perte d\'eau par évaporation au bouchon, fréquente et souvent minime, qui se corrige en refaisant le niveau. Ou le vinyle lui-même : avec les années, il peut devenir plus rigide et sujet aux microfissures, surtout sur les anciennes fabrications.', 'Un matelas à eau de remplacement, identique ou compatible, avec la stabilisation que vous aviez — ou une autre si vous voulez changer de sensation.' ),
		'chauffage'  => array( 'Un chauffage de lit à eau se remplace ; il ne se répare pas. C\'est la panne la plus fréquente et la moins grave, et c\'est aussi celle qui fait croire que le lit est fini.', 'Nous vous disons quelle puissance correspond à votre matelas et vous l\'expédions. Le changement peut souvent se faire sans vider le lit ; sinon, nous vous guidons.' ),
		'eau'        => array( 'C\'est le plus souvent une prolifération d\'algues, conséquence d\'un entretien interrompu — pas d\'un défaut. Elle ne part pas toute seule et elle abîme le vinyle de l\'intérieur.', 'Un traitement anti-algues, puis une dose de conditionneur chaque année : c\'est le geste le moins cher, et celui qui décide de la durée de vie du matelas. Un matelas neuf peut sentir le plastique neuf : un désodorisant sans parfum suffit.' ),
		'bruit'      => array( 'Du vinyle qui frotte sur du vinyle, ou sur la mousse de contour. Désagréable, sans gravité, et sans rapport avec une fuite.', 'Un lubrifiant spécial vinyle appliqué aux points de contact. Vous pouvez le faire vous-même.' ),
		'vagues'     => array( 'C\'est la stabilisation : les fibres ou mousses à l\'intérieur du matelas qui amortissent les mouvements. Elle se tasse avec le temps, et les anciens modèles en ont peu.', 'Cela se règle en changeant le matelas — c\'est le bon moment pour choisir une stabilisation plus forte.' ),
		'demenage'   => array( 'Un matelas à eau se vide, se transporte à plat et se remonte. Mal fait, un pli peut marquer le vinyle définitivement.', 'Vidage seul ou déménagement complet avec remontage, sur devis. Si vous le faites vous-même, il vous faut une pompe et le bon adaptateur — appelez-nous avant de commencer.' ),
		'revetement' => array( 'Le tissu vieillit plus vite que le vinyle. Sur beaucoup de lits, c\'est la seule chose réellement usée.', 'Housse de matelas complète ou dessus seul, revêtements tissus, séparations, mousses de contour. Sur devis.' ),
	);

	public static function sc_symptomes( $atts ) {
		self::enqueue();
		$atts = shortcode_atts( array( 'formulaire' => '#formulaire-sav' ), $atts );
		$h    = '<div class="lmdd-symptoms">';
		foreach ( LMDD_DS_Forms::SYMPTOMES as $k => $s ) {
			list( $what, $do ) = self::SYMPTOMES_TEXTES[ $k ];
			$h .= '<details class="lmdd-symptom" id="sav-' . esc_attr( $k ) . '"><summary><span class="lmdd-symptom__icon">' . self::icon( $s[1] ) . '</span><span class="lmdd-symptom__title">' . esc_html( $s[0] ) . '</span><span class="lmdd-symptom__chev" aria-hidden="true"></span></summary>';
			$h .= '<div class="lmdd-symptom__body"><div><h4>Ce que c\'est, en général</h4><p>' . esc_html( $what ) . '</p></div><div><h4>Ce que nous faisons</h4><p>' . esc_html( $do ) . '</p></div>';
			$h .= '<div class="lmdd-symptom__parts"><h4>Les pièces concernées</h4>' . self::parts( $k ) . '</div>';
			$h .= '<p class="lmdd-symptom__cta"><a class="lmdd-btn lmdd-btn--primary lmdd-btn--sm" href="' . esc_attr( $atts['formulaire'] ) . '" data-lmdd-symptom="' . esc_attr( $k ) . '">C\'est mon cas : décrire le problème ' . self::icon( 'arrow' ) . '</a></p>';
			$h .= '</div></details>';
		}
		return $h . '</div>';
	}

	private static function parts( $k ) {
		$items = '';
		foreach ( self::PIECES[ $k ] as $slug ) {
			$post = get_page_by_path( $slug, OBJECT, 'product' );
			$p    = $post ? wc_get_product( $post->ID ) : null;
			if ( ! $p || 'publish' !== $p->get_status() ) {
				continue;
			}
			$items .= '<li><a href="' . esc_url( $p->get_permalink() ) . '">' . ( $p->get_image_id() ? wp_get_attachment_image( $p->get_image_id(), 'thumbnail', false, array( 'loading' => 'lazy', 'alt' => '' ) ) : '' )
				. '<span>' . esc_html( wp_strip_all_tags( self::short_title( $p ) ) ) . '</span><strong>' . wp_kses_post( $p->get_price_html() ) . '</strong></a></li>';
		}
		$items .= in_array( $k, array( 'fuite', 'deforme', 'vagues', 'demenage', 'revetement' ), true ) ? '<li class="lmdd-part--quote"><span>Matelas, liner, housse : remplacement sur mesure</span><strong>Sur devis</strong></li>' : '';
		return '<ul class="lmdd-parts">' . $items . '</ul>';
	}

	// ------------------------------------------------------------------ Blocs de fiche produit (modèle Elementor)

	private static function current_product() {
		global $product;
		return $product && is_a( $product, 'WC_Product' ) ? $product : ( function_exists( 'wc_get_product' ) ? wc_get_product( get_queried_object_id() ) : null );
	}

	/** Badges + « Catégorie · Marque » au-dessus du titre. */
	public static function sc_product_header() {
		$p = self::current_product();
		if ( ! $p ) {
			return '';
		}
		self::enqueue();
		$badges = array();
		if ( self::is_devis( $p ) ) {
			$badges[] = '<span class="lmdd-badge lmdd-badge--devis">Sur devis</span>';
		} elseif ( $p->is_on_sale() ) {
			$badges[] = '<span class="lmdd-badge lmdd-badge--promo">Promotion</span>';
		}
		$cats  = wc_get_product_terms( $p->get_id(), 'product_cat', array( 'orderby' => 'parent' ) );
		$cat   = $cats ? $cats[0] : null;
		$tags  = wc_get_product_terms( $p->get_id(), 'product_tag' );
		$brand = $tags ? $tags[0] : null;
		$eye   = array();
		if ( $cat ) {
			$eye[] = '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
		}
		if ( $brand ) {
			$eye[] = '<a href="' . esc_url( get_term_link( $brand ) ) . '">' . esc_html( $brand->name ) . '</a>';
		}
		return '<div class="lmdd-ph">' . ( $badges ? '<div class="lmdd-ph__badges">' . implode( '', $badges ) . '</div>' : '' )
			. ( $eye ? '<p class="lmdd-ph__eyebrow">' . implode( ' · ', $eye ) . '</p>' : '' ) . '</div>';
	}

	/** Points clés : champ du produit, sinon rien (pas de promesse générique non vérifiée). */
	public static function sc_key_points() {
		$p = self::current_product();
		if ( ! $p ) {
			return '';
		}
		$lines = array_filter( array_map( 'trim', explode( "\n", (string) $p->get_meta( '_lmdd_points_cles' ) ) ) );
		if ( ! $lines ) {
			return '';
		}
		self::enqueue();
		return '<ul class="lmdd-points">' . implode( '', array_map( function ( $l ) {
			return '<li>' . self::icon( 'check' ) . '<span>' . esc_html( $l ) . '</span></li>';
		}, $lines ) ) . '</ul>';
	}

	/** Réassurance sous le bouton d'achat (vente en ligne) ou de devis. */
	public static function sc_reassurance() {
		$p = self::current_product();
		self::enqueue();
		$s     = LMDD_DS_Store::settings();
		$items = $p && self::is_devis( $p )
			? array( array( 'phone', 'Un conseiller vous rappelle', $s['horaires'] ), array( 'truck', 'Livraison et installation', 'avec réglage personnalisé' ), array( 'shield', 'Un devis sur mesure', 'selon votre chambre et vos besoins' ) )
			: array( array( 'truck', 'Livraison gratuite', 'dès 60 € d\'achat' ), array( 'card', 'Facilités de paiement', 'payez à votre rythme' ), array( 'phone', 'Conseil ' . preg_replace( '/^7 jours sur 7, /', '7j/7 ', $s['horaires'] ), self::tel_link() ) );
		$h = '<ul class="lmdd-reassure">';
		foreach ( $items as $it ) {
			$h .= '<li>' . self::icon( $it[0] ) . '<span><strong>' . esc_html( $it[1] ) . '</strong><small>' . ( 0 === strpos( $it[2], '<a' ) ? $it[2] : esc_html( $it[2] ) ) . '</small></span></li>';
		}
		return $h . '</ul>';
	}
}
