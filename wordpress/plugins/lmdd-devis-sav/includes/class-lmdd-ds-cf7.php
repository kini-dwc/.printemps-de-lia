<?php
/**
 * Formulaires gérés par Contact Form 7 : vous modifiez champs, textes et e-mails dans Contact → Formulaires.
 * Cette classe crée les deux formulaires recommandés (devis, SAV), les affiche, et enregistre chaque envoi
 * dans « Demandes clients » (copie de secours, photos comprises).
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_CF7 {

	public static function init() {
		add_action( 'wpcf7_mail_sent', array( __CLASS__, 'capture' ) );
		add_filter( 'wpcf7_posted_data', array( __CLASS__, 'clean_posted' ) );
		if ( is_admin() ) {
			add_action( 'admin_post_lmdd_ds_creer_formulaires', array( __CLASS__, 'handle_create' ) );
		}
	}

	public static function active() {
		return class_exists( 'WPCF7_ContactForm' );
	}

	/** Formulaire configuré pour « devis » ou « sav » (identifiant CF7), 0 sinon. */
	public static function form_id( $type ) {
		$s  = LMDD_DS_Store::settings();
		$id = (int) ( 'devis' === $type ? $s['form_devis'] : $s['form_sav'] );
		return $id && self::active() && 'wpcf7_contact_form' === get_post_type( $id ) ? $id : 0;
	}

	public static function render( $type ) {
		$id = self::form_id( $type );
		if ( ! $id ) {
			return '';
		}
		$html = do_shortcode( '[contact-form-7 id="' . $id . '"]' );
		// Contact Form 7 ne connaît la page (pour [_post_title] et [_post_url]) que dans la boucle WordPress ;
		// les modèles Royal / Elementor s'affichent hors de la boucle : on lui indique la page en cours.
		$post_id = get_queried_object_id();
		if ( $post_id ) {
			$html = str_replace( 'name="_wpcf7_container_post" value="0"', 'name="_wpcf7_container_post" value="' . (int) $post_id . '"', $html );
		}
		return '<div class="lmdd-cf7 lmdd-cf7--' . esc_attr( $type ) . '" data-lmdd-cf7="' . esc_attr( $type ) . '">' . $html . '</div>';
	}

	/** La configuration jointe par la fiche produit est limitée en taille. */
	public static function clean_posted( $data ) {
		if ( isset( $data['configuration'] ) && is_string( $data['configuration'] ) ) {
			$data['configuration'] = mb_substr( sanitize_textarea_field( $data['configuration'] ), 0, 3000 );
		}
		return $data;
	}

	// ------------------------------------------------------------------ Copie de secours dans « Demandes clients »

	public static function capture( $contact_form ) {
		$s    = LMDD_DS_Store::settings();
		$type = '';
		foreach ( array( 'devis', 'sav' ) as $t ) {
			if ( (int) $contact_form->id() === self::form_id( $t ) ) {
				$type = $t;
			}
		}
		if ( ! $type || empty( $s['enregistrer'] ) || ! class_exists( 'WPCF7_Submission' ) ) {
			return;
		}
		$sub = WPCF7_Submission::get_instance();
		if ( ! $sub ) {
			return;
		}
		$values = array();
		foreach ( $sub->get_posted_data() as $k => $v ) {
			if ( 0 === strpos( $k, '_' ) || false !== strpos( $k, 'turnstile' ) || false !== strpos( $k, 'g-recaptcha' ) ) {
				continue;
			}
			$values[ $k ] = is_array( $v ) ? implode( ', ', array_map( 'sanitize_text_field', $v ) ) : sanitize_textarea_field( (string) $v );
		}
		$context = array( 'page' => esc_url_raw( (string) $sub->get_meta( 'url' ) ), 'formulaire' => $contact_form->title() );
		$post_id = (int) $sub->get_meta( 'container_post_id' );
		if ( 'devis' === $type && $post_id && function_exists( 'wc_get_product' ) && ( $p = wc_get_product( $post_id ) ) ) {
			$context['produit_id'] = $p->get_id();
			$context['produit']    = wp_strip_all_tags( LMDD_DS_Front::short_title( $p ) );
		}
		// Photos : Contact Form 7 les supprime après l'envoi ; on en garde une copie.
		$context['photos'] = array();
		foreach ( (array) $sub->uploaded_files() as $files ) {
			foreach ( (array) $files as $file ) {
				$copy = self::keep_file( $file );
				if ( $copy ) {
					$context['photos'][] = $copy;
				}
			}
		}
		LMDD_DS_Store::save( $type, $values, $context );
	}

	private static function keep_file( $file ) {
		if ( ! is_readable( $file ) ) {
			return null;
		}
		$up   = wp_upload_dir();
		$dir  = trailingslashit( $up['basedir'] ) . 'lmdd-demandes/' . gmdate( 'Y/m' );
		$base = trailingslashit( $up['basedir'] ) . 'lmdd-demandes/';
		if ( ! wp_mkdir_p( $dir ) ) {
			return null;
		}
		if ( ! file_exists( $base . 'index.php' ) ) {
			file_put_contents( $base . 'index.php', "<?php // Silence.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			file_put_contents( $base . '.htaccess', "Options -Indexes\n<FilesMatch \"\\.(php|phtml|phar)$\">\nRequire all denied\n</FilesMatch>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		$ext  = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp', 'heic', 'heif', 'pdf' ), true ) ) {
			return null;
		}
		$name = 'cf7-' . wp_generate_password( 16, false ) . '.' . $ext;
		if ( ! copy( $file, $dir . '/' . $name ) ) {
			return null;
		}
		return array( 'file' => $dir . '/' . $name, 'url' => trailingslashit( $up['baseurl'] ) . 'lmdd-demandes/' . gmdate( 'Y/m' ) . '/' . $name );
	}

	// ------------------------------------------------------------------ Formulaires recommandés

	const CRENEAUX = '"Peu importe" "Le matin (9h – 12h)" "Le midi (12h – 14h)" "L\'après-midi (14h – 18h)" "En soirée (18h – 21h)"';

	private static function template( $type ) {
		if ( 'devis' === $type ) {
			return '<div class="lmdd-grille">
<label class="lmdd-champ lmdd-large"><span class="lmdd-titre">Nom et prénom *</span>[text* nom autocomplete:name]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Téléphone *</span>[tel* telephone autocomplete:tel]</label>
<label class="lmdd-champ"><span class="lmdd-titre">E-mail *</span>[email* email autocomplete:email]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Code postal de livraison *</span>[text* code_postal maxlength:10 autocomplete:postal-code]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Quand vous joindre</span>[select creneau ' . self::CRENEAUX . ']</label>
<label class="lmdd-champ lmdd-large"><span class="lmdd-titre">Une précision ? (facultatif)</span>[textarea message x3 maxlength:2000 placeholder "Dimensions de la chambre, accès, questions…"]</label>
</div>
[hidden configuration id:lmdd-configuration]
[turnstile]
[submit "Envoyer ma demande"]
<p class="lmdd-note">Vos réponses servent uniquement à établir votre devis : pas de liste de diffusion, aucune transmission à un tiers.</p>';
		}
		return '<fieldset class="lmdd-etape" data-titre="Le problème">
<p class="lmdd-intro">Choisissez ce qui correspond le mieux. Si rien ne colle, décrivez-le simplement.</p>
<div class="lmdd-cartes">[checkbox situation exclusive use_label_element "Le lit perd de l\'eau" "Le matelas est déformé ou inconfortable" "Le lit est froid, le chauffage ne répond plus" "L\'eau est trouble, ou ça sent" "Ça grince, ça couine quand on bouge" "Le lit fait des vagues" "Je déménage, ou je dois vider le lit" "La housse ou le revêtement est abîmé"]</div>
<label class="lmdd-champ lmdd-large"><span class="lmdd-titre">Que se passe-t-il ? *</span>[textarea* description x3 maxlength:3000 placeholder "En une ou deux phrases, avec vos mots. S\'il y a de l\'eau, dites-nous où."]</label>
</fieldset>
<fieldset class="lmdd-etape" data-titre="Votre lit">
<p class="lmdd-intro">Pour vous proposer un matelas identique ou 100 % compatible. « Je ne sais pas » est une réponse acceptée partout.</p>
<div class="lmdd-grille">
<label class="lmdd-champ"><span class="lmdd-titre">La marque du matelas</span>[text marque placeholder "Akva, Poseïdon, Tasso… ou je ne sais pas"]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Le modèle et les dimensions</span>[text modele placeholder "Ex. : Soft, 160 x 200 cm"]</label>
<label class="lmdd-champ"><span class="lmdd-titre">L\'année d\'achat, si vous la connaissez</span>[text annee]</label>
</div>
<div class="lmdd-champ lmdd-large"><span class="lmdd-titre">Les bords du lit</span>[checkbox bords exclusive use_label_element "Bords durs (hardside)" "Bords en mousse (softside)" "Je ne sais pas"]</div>
<div class="lmdd-champ lmdd-large"><span class="lmdd-titre">Le couchage</span>[checkbox couchage exclusive use_label_element "Un seul matelas" "Deux matelas, séparation conique" "Deux matelas, séparation plate" "Deux matelas, je ne sais pas"]</div>
<div class="lmdd-champ lmdd-large"><span class="lmdd-titre">Quand vous bougez, le matelas fait…</span>[checkbox vagues exclusive use_label_element "Beaucoup de vagues" "Un peu de vagues" "Presque pas de vagues" "Je ne sais pas"]</div>
<div class="lmdd-grille">
<label class="lmdd-champ"><span class="lmdd-titre">Photo de l\'étiquette du matelas (facultatif)</span>[file photo_etiquette limit:8mb filetypes:jpg|jpeg|png|webp|heic]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Photo du problème (facultatif)</span>[file photo_probleme limit:8mb filetypes:jpg|jpeg|png|webp|heic]</label>
</div>
</fieldset>
<fieldset class="lmdd-etape" data-titre="Vos coordonnées">
<p class="lmdd-intro">Nous vous rappelons avec une solution et, si besoin, un devis.</p>
<div class="lmdd-grille">
<label class="lmdd-champ lmdd-large"><span class="lmdd-titre">Nom et prénom *</span>[text* nom autocomplete:name]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Téléphone *</span>[tel* telephone autocomplete:tel]</label>
<label class="lmdd-champ"><span class="lmdd-titre">E-mail</span>[email email autocomplete:email]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Code postal *</span>[text* code_postal maxlength:10 autocomplete:postal-code]</label>
<label class="lmdd-champ"><span class="lmdd-titre">Quand vous joindre</span>[select creneau ' . self::CRENEAUX . ']</label>
</div>
[turnstile]
[submit "Envoyer ma demande"]
<p class="lmdd-note">Vos réponses servent uniquement à traiter votre demande : pas de liste de diffusion, aucune transmission à un tiers.</p>
</fieldset>';
	}

	/** Réglages d'e-mail du formulaire de devis existant (destinataire et expéditeur déjà configurés). */
	private static function existing_mail() {
		foreach ( get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 20, 's' => 'devis' ) ) as $p ) {
			if ( get_post_meta( $p->ID, '_lmdd_form', true ) ) {
				continue;
			}
			$mail = get_post_meta( $p->ID, '_mail', true );
			if ( ! empty( $mail['recipient'] ) ) {
				return $mail;
			}
		}
		return array();
	}

	private static function mails( $type ) {
		$old    = self::existing_mail();
		$to     = ! empty( $old['recipient'] ) ? $old['recipient'] : '[_site_admin_email]';
		$sender = ! empty( $old['sender'] ) ? $old['sender'] : '[_site_title] <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>';
		if ( 'devis' === $type ) {
			$body = "Demande de devis\n\nProduit : [_post_title]\n[_post_url]\n\nOptions choisies :\n[configuration]\n\nNom : [nom]\nTéléphone : [telephone]\nE-mail : [email]\nCode postal : [code_postal]\nÀ joindre : [creneau]\n\nPrécision :\n[message]\n\n-- \nEnvoyé depuis [_url] le [_date] à [_time]";
			$subj = '[Devis] [_post_title] – [nom] – [code_postal]';
			$ack  = "Bonjour [nom],\n\nMerci pour votre demande de devis pour : [_post_title].\nUn conseiller La Maison du Dos vous recontacte ([creneau]) pour l'affiner avec vous.\n\nVos options :\n[configuration]\n\nUne question d'ici là ? Appelez-nous au 03 25 04 20 19, 7 jours sur 7 de 9h à 21h.\n\nL'équipe La Maison du Dos";
			$ack_subj = 'La Maison du Dos – votre demande de devis est bien reçue';
		} else {
			$body = "Demande SAV\n\nSituation : [situation]\nDescription :\n[description]\n\nMarque : [marque]\nModèle et dimensions : [modele]\nAnnée : [annee]\nBords : [bords]\nCouchage : [couchage]\nVagues : [vagues]\n\nNom : [nom]\nTéléphone : [telephone]\nE-mail : [email]\nCode postal : [code_postal]\nÀ joindre : [creneau]\n\nPhotos jointes, s'il y en a.\n\n-- \nEnvoyé depuis [_url] le [_date] à [_time]";
			$subj = '[SAV] [situation] – [nom] – [code_postal]';
			$ack  = "Bonjour [nom],\n\nNous avons bien reçu votre demande SAV ([situation]).\nNous vous rappelons ([creneau]) avec une solution et, si besoin, un devis.\n\nDe l'eau qui s'écoule en ce moment ? N'attendez pas : 03 25 04 20 19.\n\nL'équipe La Maison du Dos";
			$ack_subj = 'La Maison du Dos – votre demande SAV est bien reçue';
		}
		return array(
			'mail'   => array(
				'active' => true, 'subject' => $subj, 'sender' => $sender, 'recipient' => $to, 'body' => $body,
				'additional_headers' => 'Reply-To: [nom] <[email]>', 'attachments' => 'sav' === $type ? '[photo_etiquette] [photo_probleme]' : '',
				'use_html' => false, 'exclude_blank' => true,
			),
			'mail_2' => array(
				'active' => true, 'subject' => $ack_subj, 'sender' => $sender, 'recipient' => '[email]', 'body' => $ack,
				'additional_headers' => '', 'attachments' => '', 'use_html' => false, 'exclude_blank' => true,
			),
		);
	}

	private static function messages() {
		return array(
			'mail_sent_ok'             => 'Merci, votre demande est bien arrivée. Un conseiller vous recontacte sur le créneau choisi.',
			'mail_sent_ng'             => 'L\'envoi n\'a pas abouti. Merci de réessayer, ou de nous appeler au 03 25 04 20 19.',
			'validation_error'         => 'Merci de compléter les champs signalés.',
			'spam'                     => 'L\'envoi n\'a pas abouti. Merci de réessayer, ou de nous appeler au 03 25 04 20 19.',
			'invalid_required'         => 'Ce champ est nécessaire.',
			'invalid_email'            => 'Adresse e-mail invalide.',
			'invalid_tel'              => 'Numéro de téléphone invalide.',
			'invalid_too_long'         => 'Ce texte est trop long.',
			'upload_failed'            => 'La photo n\'a pas pu être envoyée.',
			'upload_file_type_invalid' => 'Seules les photos (JPEG, PNG, WebP, HEIC) sont acceptées.',
			'upload_file_too_large'    => 'Cette photo dépasse 8 Mo.',
		);
	}

	/** Crée les formulaires recommandés manquants et les sélectionne dans les réglages. Renvoie les titres créés. */
	public static function create_recommended() {
		if ( ! self::active() ) {
			return array();
		}
		$created  = array();
		$settings = get_option( LMDD_DS_Store::OPTION, array() );
		$settings = is_array( $settings ) ? $settings : array();
		foreach ( array( 'devis' => 'Devis – fiche produit (LMDD)', 'sav' => 'SAV lit à eau (LMDD)' ) as $type => $title ) {
			$existing = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_lmdd_form', 'meta_value' => $type ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			$id       = $existing ? (int) $existing[0] : 0;
			if ( ! $id ) {
				$cf = WPCF7_ContactForm::get_template( array( 'title' => $title, 'locale' => 'fr_FR' ) );
				$cf->set_properties( array_merge( array( 'form' => self::template( $type ), 'messages' => array_merge( $cf->prop( 'messages' ), self::messages() ) ), self::mails( $type ) ) );
				$id = (int) $cf->save();
				if ( ! $id ) {
					continue;
				}
				update_post_meta( $id, '_lmdd_form', $type );
				$created[] = $title;
			}
			if ( empty( $settings[ 'form_' . $type ] ) ) {
				$settings[ 'form_' . $type ] = $id;
			}
		}
		update_option( LMDD_DS_Store::OPTION, $settings );
		return $created;
	}

	public static function handle_create() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Accès refusé.' );
		}
		check_admin_referer( 'lmdd_ds_creer_formulaires' );
		$created = self::create_recommended();
		wp_safe_redirect( admin_url( 'edit.php?post_type=lmdd_demande&page=lmdd-ds-reglages&crees=' . count( $created ) ) );
		exit;
	}
}
