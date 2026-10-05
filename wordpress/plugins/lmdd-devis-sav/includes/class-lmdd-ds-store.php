<?php
/**
 * Demandes clients : enregistrement (type de contenu privé « lmdd_demande »), écran d'administration, e-mails, réglages.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Store {

	const CPT    = 'lmdd_demande';
	const OPTION = 'lmdd_ds_settings';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		if ( is_admin() ) {
			add_filter( 'manage_' . self::CPT . '_posts_columns', array( __CLASS__, 'columns' ) );
			add_action( 'manage_' . self::CPT . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
			add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
			add_action( 'admin_post_lmdd_ds_statut', array( __CLASS__, 'toggle_status' ) );
			add_action( 'add_meta_boxes_' . self::CPT, array( __CLASS__, 'meta_boxes' ) );
			add_action( 'admin_menu', array( __CLASS__, 'menus' ) );
			add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
			add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
			add_action( 'pre_get_posts', array( __CLASS__, 'apply_filters' ) );
		}
	}

	// ------------------------------------------------------------------ Réglages

	public static function settings() {
		$s = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $s ) ? $s : array(), array(
			'destinataires' => self::default_recipient(),
			'accuse'        => 1,
			'categories'    => 'sur-devis',
			'telephone'     => '03 25 04 20 19',
			'whatsapp'      => '+33 6 74 39 39 87',
			'horaires'      => '7 jours sur 7, de 9h à 21h',
			'confidentialite' => '',
		) );
	}

	/** Destinataire par défaut : celui du formulaire de devis Contact Form 7 existant, sinon l'e-mail de l'administration. */
	private static function default_recipient() {
		$cf7 = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 5, 'fields' => 'ids', 's' => 'devis' ) );
		foreach ( $cf7 as $id ) {
			$mail = get_post_meta( $id, '_mail', true );
			if ( ! empty( $mail['recipient'] ) && false === strpos( $mail['recipient'], '[' ) ) {
				return $mail['recipient'];
			}
		}
		return get_option( 'admin_email' );
	}

	public static function register_settings() {
		register_setting( 'lmdd_ds', self::OPTION, array(
			'type'              => 'array',
			'sanitize_callback' => function ( $in ) {
				$in   = is_array( $in ) ? $in : array();
				$mails = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', isset( $in['destinataires'] ) ? $in['destinataires'] : '' ) ) ) );
				return array(
					'destinataires'   => $mails ? implode( ', ', $mails ) : get_option( 'admin_email' ),
					'accuse'          => empty( $in['accuse'] ) ? 0 : 1,
					'categories'      => sanitize_text_field( isset( $in['categories'] ) ? $in['categories'] : '' ),
					'telephone'       => sanitize_text_field( isset( $in['telephone'] ) ? $in['telephone'] : '' ),
					'whatsapp'        => sanitize_text_field( isset( $in['whatsapp'] ) ? $in['whatsapp'] : '' ),
					'horaires'        => sanitize_text_field( isset( $in['horaires'] ) ? $in['horaires'] : '' ),
					'confidentialite' => esc_url_raw( isset( $in['confidentialite'] ) ? $in['confidentialite'] : '' ),
				);
			},
		) );
	}

	public static function menus() {
		add_submenu_page( 'edit.php?post_type=' . self::CPT, 'Réglages des demandes', 'Réglages', 'manage_options', 'lmdd-ds-reglages', array( __CLASS__, 'settings_page' ) );
	}

	public static function settings_page() {
		$s = self::settings();
		$o = self::OPTION;
		?>
		<div class="wrap">
			<h1>Demandes clients – réglages</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'lmdd_ds' ); ?>
				<table class="form-table" role="presentation">
					<tr><th><label for="ds-dest">Recevoir les demandes à</label></th>
						<td><input id="ds-dest" class="regular-text" name="<?php echo esc_attr( $o ); ?>[destinataires]" value="<?php echo esc_attr( $s['destinataires'] ); ?>">
						<p class="description">Une ou plusieurs adresses, séparées par des virgules. Le client est en « Répondre à » : il suffit de répondre à l'e-mail.</p></td></tr>
					<tr><th>Accusé de réception</th>
						<td><label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[accuse]" value="1" <?php checked( $s['accuse'] ); ?>> Envoyer au client une copie de sa demande</label></td></tr>
					<tr><th><label for="ds-cat">Produits vendus sur devis</label></th>
						<td><input id="ds-cat" class="regular-text" name="<?php echo esc_attr( $o ); ?>[categories]" value="<?php echo esc_attr( $s['categories'] ); ?>">
						<p class="description">Un produit est « sur devis » si l'une de ses catégories contient l'un de ces mots (séparés par des virgules). Actuellement : <code>sur-devis</code> → « Lits à eau en vente sur devis ».
						Chaque produit peut aussi être forcé dans <em>Produit → Général → Vente</em>.</p></td></tr>
					<tr><th><label for="ds-tel">Téléphone affiché</label></th>
						<td><input id="ds-tel" name="<?php echo esc_attr( $o ); ?>[telephone]" value="<?php echo esc_attr( $s['telephone'] ); ?>">
						WhatsApp <input name="<?php echo esc_attr( $o ); ?>[whatsapp]" value="<?php echo esc_attr( $s['whatsapp'] ); ?>">
						Horaires <input class="regular-text" name="<?php echo esc_attr( $o ); ?>[horaires]" value="<?php echo esc_attr( $s['horaires'] ); ?>"></td></tr>
					<tr><th><label for="ds-rgpd">Page de confidentialité</label></th>
						<td><input id="ds-rgpd" class="regular-text" name="<?php echo esc_attr( $o ); ?>[confidentialite]" value="<?php echo esc_attr( $s['confidentialite'] ); ?>" placeholder="<?php echo esc_attr( get_privacy_policy_url() ); ?>">
						<p class="description">Lien affiché sous les formulaires. Vide : page de confidentialité de WordPress, si elle existe.</p></td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<h2>Anti-spam</h2>
			<p><?php echo LMDD_DS_Rest::turnstile_keys() ? '✓ Cloudflare Turnstile actif (clés reprises de Contact Form 7 → Intégration).' : 'Cloudflare Turnstile non configuré : les formulaires restent protégés par un champ piège, un délai minimal et une limite d\'envois par visiteur.'; ?></p>
		</div>
		<?php
	}

	// ------------------------------------------------------------------ Type de contenu

	public static function register() {
		register_post_type( self::CPT, array(
			'labels'          => array(
				'name'          => 'Demandes clients',
				'singular_name' => 'Demande',
				'menu_name'     => 'Demandes clients',
				'all_items'     => 'Toutes les demandes',
				'edit_item'     => 'Demande',
				'search_items'  => 'Rechercher',
				'not_found'     => 'Aucune demande pour le moment.',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-clipboard',
			'menu_position'   => 56,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		) );
	}

	/** Enregistre une demande ; renvoie son identifiant. */
	public static function save( $type, array $values, array $context ) {
		$subject = 'devis' === $type
			? ( isset( $context['produit'] ) ? $context['produit'] : 'Devis' )
			: ( LMDD_DS_Forms::display_value( 'sav', 'symptome_type', $values['symptome_type'] ) ?: 'SAV' );
		$id = wp_insert_post( array(
			'post_type'   => self::CPT,
			'post_status' => 'private',
			'post_title'  => sprintf( '%s – %s – %s', 'devis' === $type ? 'Devis' : 'SAV', $values['nom'], $subject ),
		), true );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		update_post_meta( $id, '_lmdd_type', $type );
		update_post_meta( $id, '_lmdd_values', $values );
		update_post_meta( $id, '_lmdd_context', $context );
		update_post_meta( $id, '_lmdd_statut', 'nouvelle' );
		return $id;
	}

	public static function reference( $id ) {
		$type = get_post_meta( $id, '_lmdd_type', true );
		return ( 'sav' === $type ? 'S-' : 'D-' ) . get_the_date( 'ymd', $id ) . '-' . $id;
	}

	// ------------------------------------------------------------------ E-mails

	/** Lignes « libellé → valeur » d'une demande, dans l'ordre du formulaire. */
	public static function rows( $type, array $values, array $context ) {
		$rows = array();
		if ( 'devis' === $type && ! empty( $context['produit'] ) ) {
			$rows['Produit'] = $context['produit'];
			if ( ! empty( $context['configuration'] ) ) {
				$rows['Configuration choisie'] = $context['configuration'];
			}
		}
		foreach ( LMDD_DS_Forms::get( $type ) as $section ) {
			foreach ( $section['fields'] as $name => $f ) {
				if ( 'files' === $f['type'] ) {
					if ( ! empty( $context['photos'] ) ) {
						$rows[ $f['label'] ] = implode( "\n", wp_list_pluck( $context['photos'], 'url' ) );
					}
					continue;
				}
				if ( isset( $values[ $name ] ) && '' !== $values[ $name ] ) {
					$rows[ rtrim( $f['label'], ' ?' ) ] = LMDD_DS_Forms::display_value( $type, $name, $values[ $name ] );
				}
			}
		}
		if ( ! empty( $context['page'] ) ) {
			$rows['Envoyée depuis'] = $context['page'];
		}
		return $rows;
	}

	private static function html_table( array $rows ) {
		$h = '<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;font:14px/1.5 Arial,sans-serif;color:#1B261D;max-width:640px">';
		foreach ( $rows as $label => $value ) {
			$v = esc_html( $value );
			if ( preg_match( '#^https?://#', $value ) ) {
				$v = implode( '<br>', array_map( function ( $u ) {
					return '<a href="' . esc_url( $u ) . '">' . esc_html( basename( $u ) ) . '</a>';
				}, explode( "\n", $value ) ) );
			}
			$h .= '<tr><th align="left" valign="top" style="border-bottom:1px solid #DFE7E0;color:#56625A;font-weight:600;width:190px">' . esc_html( $label ) . '</th><td style="border-bottom:1px solid #DFE7E0">' . nl2br( $v ) . '</td></tr>';
		}
		return $h . '</table>';
	}

	public static function notify( $id ) {
		$type    = get_post_meta( $id, '_lmdd_type', true );
		$values  = (array) get_post_meta( $id, '_lmdd_values', true );
		$context = (array) get_post_meta( $id, '_lmdd_context', true );
		$s       = self::settings();
		$rows    = self::rows( $type, $values, $context );
		$ref     = self::reference( $id );
		$label   = 'devis' === $type ? 'Demande de devis' : 'Demande SAV';
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		if ( ! empty( $values['email'] ) ) {
			$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $values['nom'] ) . ' <' . $values['email'] . '>';
		}
		$admin_url   = admin_url( 'post.php?post=' . $id . '&action=edit' );
		$attachments = ! empty( $context['photos'] ) ? array_filter( wp_list_pluck( $context['photos'], 'file' ), 'is_readable' ) : array();
		$sujet       = sprintf( '[%s %s] %s – %s', $label, $ref, $values['nom'], 'devis' === $type ? ( isset( $context['produit'] ) ? $context['produit'] : '' ) : ( isset( $rows['Ce qui se passe'] ) ? $rows['Ce qui se passe'] : 'SAV' ) );
		$body        = '<p style="font:15px Arial,sans-serif"><strong>' . esc_html( $label ) . '</strong> reçue le ' . esc_html( wp_date( 'd/m/Y à H:i' ) ) . ' (réf. ' . esc_html( $ref ) . ').</p>'
			. self::html_table( $rows )
			. '<p style="font:14px Arial,sans-serif"><a href="' . esc_url( $admin_url ) . '">Ouvrir la demande dans l\'administration</a></p>';
		$sent = wp_mail( array_map( 'trim', explode( ',', $s['destinataires'] ) ), $sujet, $body, $headers, $attachments );
		update_post_meta( $id, '_lmdd_mail_equipe', $sent ? 'envoyé' : 'échec' );

		if ( $s['accuse'] && ! empty( $values['email'] ) ) {
			unset( $rows['Envoyée depuis'] );
			$intro = 'devis' === $type
				? 'Merci pour votre demande de devis. Un conseiller La Maison du Dos vous recontacte' . ( ! empty( $values['creneau'] ) && 'Peu importe' !== $values['creneau'] ? ', ' . mb_strtolower( $values['creneau'] ) : '' ) . ', pour l\'affiner avec vous.'
				: 'Merci, nous avons bien reçu votre demande SAV. Nous vous rappelons avec une solution et, si besoin, un devis.';
			$body = '<div style="font:15px/1.6 Arial,sans-serif;color:#1B261D;max-width:640px"><p>Bonjour ' . esc_html( $values['nom'] ) . ',</p><p>' . esc_html( $intro ) . '</p>'
				. '<p>Voici le récapitulatif de votre demande (réf. ' . esc_html( $ref ) . ') :</p>' . self::html_table( $rows )
				. '<p>Une question en attendant ? Appelez-nous au <strong>' . esc_html( $s['telephone'] ) . '</strong>, ' . esc_html( $s['horaires'] ) . '.</p>'
				. '<p>L\'équipe La Maison du Dos</p></div>';
			wp_mail( $values['email'], 'La Maison du Dos – votre ' . ( 'devis' === $type ? 'demande de devis' : 'demande SAV' ) . ' est bien reçue', $body, array( 'Content-Type: text/html; charset=UTF-8' ) );
		}
		return $sent;
	}

	// ------------------------------------------------------------------ Administration

	public static function columns( $cols ) {
		return array(
			'cb'          => $cols['cb'],
			'title'       => 'Demande',
			'lmdd_type'   => 'Type',
			'lmdd_tel'    => 'Téléphone',
			'lmdd_cp'     => 'Code postal',
			'lmdd_statut' => 'Statut',
			'date'        => 'Reçue le',
		);
	}

	public static function column( $col, $id ) {
		$v = (array) get_post_meta( $id, '_lmdd_values', true );
		if ( 'lmdd_type' === $col ) {
			echo 'sav' === get_post_meta( $id, '_lmdd_type', true ) ? '<span style="background:#FCE8EB;color:#A50A1E;padding:2px 8px;border-radius:10px">SAV</span>' : '<span style="background:#EDF6EE;color:#035C11;padding:2px 8px;border-radius:10px">Devis</span>';
		} elseif ( 'lmdd_tel' === $col && ! empty( $v['telephone'] ) ) {
			echo '<a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', $v['telephone'] ) ) . '">' . esc_html( $v['telephone'] ) . '</a>';
			if ( ! empty( $v['creneau'] ) ) {
				echo '<br><small>' . esc_html( $v['creneau'] ) . '</small>';
			}
		} elseif ( 'lmdd_cp' === $col ) {
			echo esc_html( isset( $v['code_postal'] ) ? $v['code_postal'] : '' );
		} elseif ( 'lmdd_statut' === $col ) {
			$st = get_post_meta( $id, '_lmdd_statut', true );
			echo 'traitee' === $st ? '✓ Traitée' : '<strong style="color:#C80C25">● Nouvelle</strong>';
		}
	}

	public static function row_actions( $actions, $post ) {
		if ( self::CPT !== $post->post_type ) {
			return $actions;
		}
		$st  = get_post_meta( $post->ID, '_lmdd_statut', true );
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=lmdd_ds_statut&id=' . $post->ID ), 'lmdd_ds_statut_' . $post->ID );
		unset( $actions['inline hide-if-no-js'] );
		$actions['lmdd_statut'] = '<a href="' . esc_url( $url ) . '">' . ( 'traitee' === $st ? 'Remettre en « nouvelle »' : 'Marquer comme traitée' ) . '</a>';
		return $actions;
	}

	public static function toggle_status() {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		check_admin_referer( 'lmdd_ds_statut_' . $id );
		if ( ! current_user_can( 'edit_post', $id ) ) {
			wp_die( 'Accès refusé.' );
		}
		update_post_meta( $id, '_lmdd_statut', 'traitee' === get_post_meta( $id, '_lmdd_statut', true ) ? 'nouvelle' : 'traitee' );
		wp_safe_redirect( wp_get_referer() ?: admin_url( 'edit.php?post_type=' . self::CPT ) );
		exit;
	}

	public static function filters( $post_type ) {
		if ( self::CPT !== $post_type ) {
			return;
		}
		$cur = isset( $_GET['lmdd_type'] ) ? sanitize_key( $_GET['lmdd_type'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		echo '<select name="lmdd_type"><option value="">Devis et SAV</option><option value="devis"' . selected( $cur, 'devis', false ) . '>Devis</option><option value="sav"' . selected( $cur, 'sav', false ) . '>SAV</option></select>';
	}

	public static function apply_filters( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || self::CPT !== $q->get( 'post_type' ) || empty( $_GET['lmdd_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$q->set( 'meta_key', '_lmdd_type' );
		$q->set( 'meta_value', sanitize_key( $_GET['lmdd_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	public static function meta_boxes( $post ) {
		remove_meta_box( 'submitdiv', self::CPT, 'side' );
		add_meta_box( 'lmdd_demande', 'Détail de la demande', function ( $post ) {
			$type    = get_post_meta( $post->ID, '_lmdd_type', true );
			$values  = (array) get_post_meta( $post->ID, '_lmdd_values', true );
			$context = (array) get_post_meta( $post->ID, '_lmdd_context', true );
			echo '<p><strong>Référence :</strong> ' . esc_html( self::reference( $post->ID ) ) . ' — reçue le ' . esc_html( get_the_date( 'd/m/Y à H:i', $post ) ) . ' — e-mail à l\'équipe : ' . esc_html( get_post_meta( $post->ID, '_lmdd_mail_equipe', true ) ?: '?' ) . '</p>';
			echo self::html_table( self::rows( $type, $values, $context ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			if ( ! empty( $context['photos'] ) ) {
				echo '<p>';
				foreach ( $context['photos'] as $ph ) {
					echo '<a href="' . esc_url( $ph['url'] ) . '" target="_blank" rel="noopener"><img src="' . esc_url( $ph['url'] ) . '" style="max-width:220px;max-height:220px;margin:6px;border-radius:8px" alt=""></a>';
				}
				echo '</p>';
			}
		}, self::CPT, 'normal', 'high' );
		add_meta_box( 'lmdd_statut', 'Statut', function ( $post ) {
			$st  = get_post_meta( $post->ID, '_lmdd_statut', true );
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=lmdd_ds_statut&id=' . $post->ID ), 'lmdd_ds_statut_' . $post->ID );
			echo '<p>' . ( 'traitee' === $st ? '✓ Traitée' : '<strong style="color:#C80C25">● Nouvelle</strong>' ) . '</p>';
			echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">' . ( 'traitee' === $st ? 'Remettre en « nouvelle »' : 'Marquer comme traitée' ) . '</a></p>';
			echo '<p><a href="' . esc_url( get_delete_post_link( $post->ID ) ) . '" style="color:#b32d2e">Mettre à la corbeille</a></p>';
		}, self::CPT, 'side', 'high' );
	}
}
