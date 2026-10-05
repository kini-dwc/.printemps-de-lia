<?php
/**
 * Réglages, et copie de secours des demandes (type de contenu privé « lmdd_demande ») avec son écran d'administration.
 * Les e-mails sont envoyés par Contact Form 7 (modifiables dans Contact → Formulaires → onglet E-mail).
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Store {

	const CPT    = 'lmdd_demande';
	const OPTION = 'lmdd_ds_settings';

	/** Libellés lisibles des champs des formulaires recommandés (les autres champs gardent leur nom). */
	const LABELS = array(
		'nom' => 'Nom', 'telephone' => 'Téléphone', 'email' => 'E-mail', 'code_postal' => 'Code postal', 'creneau' => 'À joindre',
		'message' => 'Précision', 'configuration' => 'Options choisies', 'situation' => 'Situation', 'description' => 'Description',
		'marque' => 'Marque', 'modele' => 'Modèle et dimensions', 'annee' => 'Année', 'bords' => 'Bords', 'couchage' => 'Couchage', 'vagues' => 'Vagues',
	);

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
			add_action( 'admin_init', array( __CLASS__, 'first_setup' ) );
			add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
			add_action( 'pre_get_posts', array( __CLASS__, 'apply_filters' ) );
		}
	}

	// ------------------------------------------------------------------ Réglages

	public static function settings() {
		$s = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $s ) ? $s : array(), array(
			'form_devis'  => 0,
			'form_sav'    => 0,
			'enregistrer' => 1,
			'categories'  => 'sur-devis',
			'telephone'   => '03 25 04 20 19',
			'horaires'    => '7 jours sur 7, de 9h à 21h',
		) );
	}

	/** Première visite de l'administration après l'activation : création des formulaires Contact Form 7 recommandés. */
	public static function first_setup() {
		if ( get_option( 'lmdd_ds_setup_done' ) || ! current_user_can( 'manage_options' ) || ! LMDD_DS_CF7::active() ) {
			return;
		}
		update_option( 'lmdd_ds_setup_done', 1, false );
		LMDD_DS_CF7::create_recommended();
	}

	public static function register_settings() {
		register_setting( 'lmdd_ds', self::OPTION, array(
			'type'              => 'array',
			'sanitize_callback' => function ( $in ) {
				$in = is_array( $in ) ? $in : array();
				return array(
					'form_devis'  => absint( isset( $in['form_devis'] ) ? $in['form_devis'] : 0 ),
					'form_sav'    => absint( isset( $in['form_sav'] ) ? $in['form_sav'] : 0 ),
					'enregistrer' => empty( $in['enregistrer'] ) ? 0 : 1,
					'categories'  => sanitize_text_field( isset( $in['categories'] ) ? $in['categories'] : '' ),
					'telephone'   => sanitize_text_field( isset( $in['telephone'] ) ? $in['telephone'] : '' ),
					'horaires'    => sanitize_text_field( isset( $in['horaires'] ) ? $in['horaires'] : '' ),
				);
			},
		) );
	}

	public static function menus() {
		add_submenu_page( 'edit.php?post_type=' . self::CPT, 'Réglages des demandes', 'Réglages', 'manage_options', 'lmdd-ds-reglages', array( __CLASS__, 'settings_page' ) );
	}

	private static function form_select( $name, $current ) {
		$forms = LMDD_DS_CF7::active() ? get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 100, 'orderby' => 'title', 'order' => 'ASC' ) ) : array();
		$h     = '<select name="' . esc_attr( self::OPTION . '[' . $name . ']' ) . '"><option value="0">— Aucun —</option>';
		foreach ( $forms as $f ) {
			$h .= '<option value="' . (int) $f->ID . '"' . selected( (int) $current, $f->ID, false ) . '>' . esc_html( $f->post_title ) . ' (#' . (int) $f->ID . ')</option>';
		}
		$h .= '</select>';
		if ( $current && 'wpcf7_contact_form' === get_post_type( $current ) ) {
			$h .= ' <a class="button" href="' . esc_url( admin_url( 'admin.php?page=wpcf7&post=' . (int) $current . '&action=edit' ) ) . '">Modifier ce formulaire</a>';
		}
		return $h;
	}

	public static function settings_page() {
		$s = self::settings();
		$o = self::OPTION;
		echo '<div class="wrap"><h1>Demandes clients – réglages</h1>';
		if ( isset( $_GET['crees'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			echo '<div class="notice notice-success"><p>' . (int) $_GET['crees'] . ' formulaire(s) créé(s) dans Contact Form 7.</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( ! LMDD_DS_CF7::active() ) {
			echo '<div class="notice notice-error"><p>Contact Form 7 n\'est pas actif : les formulaires de devis et de SAV ne peuvent pas s\'afficher.</p></div>';
		}
		?>
		<form method="post" action="options.php">
			<?php settings_fields( 'lmdd_ds' ); ?>
			<h2>Formulaires (Contact Form 7)</h2>
			<p>Champs, textes, messages et e-mails se modifient dans <em>Contact → Formulaires</em>. Les formulaires recommandés ont été créés à l'activation ;
			vous pouvez aussi choisir les vôtres.</p>
			<table class="form-table" role="presentation">
				<tr><th>Devis (fiche produit)</th><td><?php echo self::form_select( 'form_devis', $s['form_devis'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p class="description">Affiché sur place, sous les options, au clic sur « Demander un devis ». Les options choisies sont jointes dans le champ caché
					<code>[hidden configuration id:lmdd-configuration]</code> ; le produit est disponible dans les e-mails avec <code>[_post_title]</code> et <code>[_post_url]</code>.</p></td></tr>
				<tr><th>SAV</th><td><?php echo self::form_select( 'form_sav', $s['form_sav'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p class="description">Code court à placer dans la page : <code>[lmdd_formulaire type="sav"]</code>. Chaque bloc <code>&lt;fieldset class="lmdd-etape" data-titre="…"&gt;</code>
					du formulaire devient une étape ; sans ces blocs, le formulaire s'affiche d'un seul tenant.</p></td></tr>
				<tr><th>Copie de secours</th><td><label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[enregistrer]" value="1" <?php checked( $s['enregistrer'] ); ?>>
					Enregistrer chaque demande dans <em>Demandes clients</em> (avec les photos), en plus de l'e-mail de Contact Form 7</label></td></tr>
			</table>
			<h2>Produits « sur devis »</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="ds-cat">Catégories</label></th>
					<td><input id="ds-cat" class="regular-text" name="<?php echo esc_attr( $o ); ?>[categories]" value="<?php echo esc_attr( $s['categories'] ); ?>">
					<p class="description">Un produit est « sur devis » si l'une de ses catégories contient l'un de ces mots (séparés par des virgules).
					Chaque produit peut aussi être forcé dans <em>Produit → Général → Vente sur devis</em>.</p></td></tr>
				<tr><th><label for="ds-tel">Téléphone affiché</label></th>
					<td><input id="ds-tel" name="<?php echo esc_attr( $o ); ?>[telephone]" value="<?php echo esc_attr( $s['telephone'] ); ?>">
					Horaires <input class="regular-text" name="<?php echo esc_attr( $o ); ?>[horaires]" value="<?php echo esc_attr( $s['horaires'] ); ?>"></td></tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<?php if ( LMDD_DS_CF7::active() ) : ?>
			<h2>Formulaires recommandés</h2>
			<p>Recrée les formulaires « Devis – fiche produit (LMDD) » et « SAV lit à eau (LMDD) » s'ils ont été supprimés (ceux qui existent ne sont pas modifiés).</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'lmdd_ds_creer_formulaires' ); ?><input type="hidden" name="action" value="lmdd_ds_creer_formulaires">
				<?php submit_button( 'Créer les formulaires manquants', 'secondary', 'submit', false ); ?>
			</form>
		<?php endif; ?>
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
		$name    = isset( $values['nom'] ) ? $values['nom'] : ( isset( $values['your-name'] ) ? $values['your-name'] : '' );
		$subject = 'devis' === $type ? ( isset( $context['produit'] ) ? $context['produit'] : 'Devis' ) : ( ! empty( $values['situation'] ) ? $values['situation'] : 'SAV' );
		$id      = wp_insert_post( array(
			'post_type'   => self::CPT,
			'post_status' => 'private',
			'post_title'  => sprintf( '%s – %s – %s', 'devis' === $type ? 'Devis' : 'SAV', $name ?: 'Sans nom', $subject ),
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
		return ( 'sav' === get_post_meta( $id, '_lmdd_type', true ) ? 'S-' : 'D-' ) . get_the_date( 'ymd', $id ) . '-' . $id;
	}

	/** Lignes « libellé → valeur » d'une demande (champs du formulaire, tels qu'envoyés). */
	public static function rows( $type, array $values, array $context ) {
		$rows = array();
		if ( ! empty( $context['produit'] ) ) {
			$rows['Produit'] = $context['produit'];
		}
		if ( ! empty( $context['configuration'] ) ) { // Demandes de la version 1.
			$rows['Options choisies'] = $context['configuration'];
		}
		foreach ( $values as $k => $v ) {
			if ( '' === trim( (string) $v ) ) {
				continue;
			}
			$label          = isset( self::LABELS[ $k ] ) ? self::LABELS[ $k ] : ucfirst( str_replace( array( '-', '_' ), ' ', $k ) );
			$rows[ $label ] = $v;
		}
		if ( ! empty( $context['photos'] ) ) {
			$rows['Photos'] = implode( "\n", wp_list_pluck( $context['photos'], 'url' ) );
		}
		if ( ! empty( $context['page'] ) ) {
			$rows['Envoyée depuis'] = $context['page'];
		}
		if ( ! empty( $context['formulaire'] ) ) {
			$rows['Formulaire'] = $context['formulaire'];
		}
		return $rows;
	}

	private static function html_table( array $rows ) {
		$h = '<table cellpadding="8" cellspacing="0" style="border-collapse:collapse;font:14px/1.5 Arial,sans-serif;color:#1B261D;max-width:760px">';
		foreach ( $rows as $label => $value ) {
			$v = nl2br( esc_html( $value ) );
			if ( preg_match( '#^https?://#', $value ) ) {
				$v = implode( '<br>', array_map( function ( $u ) {
					return '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $u ) . '</a>';
				}, explode( "\n", $value ) ) );
			}
			$h .= '<tr><th align="left" valign="top" style="border-bottom:1px solid #DFE7E0;color:#56625A;font-weight:600;width:190px">' . esc_html( $label ) . '</th><td style="border-bottom:1px solid #DFE7E0">' . $v . '</td></tr>';
		}
		return $h . '</table>';
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
			echo 'traitee' === get_post_meta( $id, '_lmdd_statut', true ) ? '✓ Traitée' : '<strong style="color:#C80C25">● Nouvelle</strong>';
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
			echo '<p><strong>Référence :</strong> ' . esc_html( self::reference( $post->ID ) ) . ' — reçue le ' . esc_html( get_the_date( 'd/m/Y à H:i', $post ) ) . '</p>';
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
