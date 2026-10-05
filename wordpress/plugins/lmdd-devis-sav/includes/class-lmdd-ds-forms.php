<?php
/**
 * Définition des formulaires (source unique) : affichage, validation serveur, e-mails et écran d'administration.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Forms {

	const CRENEAUX = array( 'Peu importe', 'Le matin (9h – 12h)', 'Le midi (12h – 14h)', 'L\'après-midi (14h – 18h)', 'En soirée (18h – 21h)' );

	/** Symptômes SAV : clé => [libellé, icône]. */
	const SYMPTOMES = array(
		'fuite'      => array( 'Le lit perd de l\'eau', 'drop' ),
		'deforme'    => array( 'Le matelas est déformé ou inconfortable', 'bed' ),
		'chauffage'  => array( 'Le lit est froid, le chauffage ne répond plus', 'flame' ),
		'eau'        => array( 'L\'eau est trouble, ou ça sent', 'flask' ),
		'bruit'      => array( 'Ça grince, ça couine quand on bouge', 'wave-sound' ),
		'vagues'     => array( 'Le lit fait des vagues', 'waves' ),
		'demenage'   => array( 'Je déménage, ou je dois vider le lit', 'box' ),
		'revetement' => array( 'La housse ou le revêtement est abîmé', 'hanger' ),
	);

	/**
	 * Champs d'un formulaire, groupés par section (devis) ou par étape (SAV).
	 * Types : text, tel, email, textarea, select, radio, cards (radio en cartes), hidden, files.
	 */
	public static function get( $type ) {
		if ( 'devis' === $type ) {
			return array(
				'projet'    => array(
					'title'  => 'Votre projet',
					'fields' => array(
						'cadre' => array( 'label' => 'Le cadre du lit', 'type' => 'radio', 'options' => array( 'Un cadre neuf, avec le lit', 'Dans mon cadre de lit actuel', 'Je ne sais pas encore' ) ),
					),
				),
				'livraison' => array(
					'title'  => 'La livraison',
					'fields' => array(
						'code_postal' => array( 'label' => 'Code postal', 'type' => 'text', 'required' => true, 'max' => 10, 'autocomplete' => 'postal-code', 'inputmode' => 'numeric', 'half' => true ),
						'pays'        => array( 'label' => 'Pays', 'type' => 'select', 'options' => array( 'France', 'Belgique', 'Luxembourg', 'Pays-Bas', 'Suisse', 'Autre pays' ), 'half' => true ),
						'etage'       => array( 'label' => 'Étage', 'type' => 'select', 'options' => array( 'Rez-de-chaussée', '1er étage', '2e étage', '3e étage ou plus' ), 'half' => true ),
						'acces'       => array( 'label' => 'Accès', 'type' => 'select', 'options' => array( 'Ascenseur', 'Escalier large', 'Escalier étroit ou tournant', 'Je ne sais pas' ), 'half' => true ),
					),
				),
				'contact'   => array(
					'title'  => 'Vos coordonnées',
					'fields' => array(
						'nom'       => array( 'label' => 'Nom et prénom', 'type' => 'text', 'required' => true, 'max' => 120, 'autocomplete' => 'name' ),
						'telephone' => array( 'label' => 'Téléphone', 'type' => 'tel', 'required' => true, 'max' => 30, 'autocomplete' => 'tel', 'half' => true ),
						'email'     => array( 'label' => 'E-mail', 'type' => 'email', 'required' => true, 'max' => 160, 'autocomplete' => 'email', 'half' => true ),
						'creneau'   => array( 'label' => 'Quand vous joindre', 'type' => 'select', 'options' => self::CRENEAUX ),
						'message'   => array( 'label' => 'Une précision ? (facultatif)', 'type' => 'textarea', 'max' => 2000, 'placeholder' => 'Dimensions de votre chambre, contraintes d\'accès, questions…' ),
					),
				),
			);
		}
		$cards = array();
		foreach ( self::SYMPTOMES as $k => $s ) {
			$cards[ $k ] = $s[0];
		}
		return array(
			'probleme' => array(
				'title'  => 'Le problème',
				'intro'  => 'Choisissez ce qui correspond le mieux. Si rien ne colle, décrivez-le simplement.',
				'fields' => array(
					'symptome_type' => array( 'label' => 'Ce qui se passe', 'type' => 'cards', 'options' => $cards ),
					'symptome'      => array( 'label' => 'Que se passe-t-il ?', 'type' => 'textarea', 'required' => true, 'max' => 3000, 'help' => 'En une ou deux phrases, avec vos mots. S\'il y a de l\'eau, dites-nous où.' ),
				),
			),
			'lit'      => array(
				'title'  => 'Votre lit',
				'intro'  => 'Pour vous proposer un matelas identique ou 100 % compatible. « Je ne sais pas » est une réponse acceptée partout.',
				'fields' => array(
					'marque'        => array( 'label' => 'La marque du matelas', 'type' => 'text', 'max' => 120, 'list' => array( 'Akva', 'Poseïdon – Lunalife (ex-Luna-Rest)', 'Tasso', 'Highline', 'Je ne sais pas' ), 'half' => true ),
					'modele'        => array( 'label' => 'Le modèle et les dimensions', 'type' => 'text', 'max' => 160, 'placeholder' => 'Ex. : Soft, 160 x 200 cm', 'half' => true ),
					'annee'         => array( 'label' => 'L\'année d\'achat ou de fabrication', 'type' => 'text', 'max' => 40, 'half' => true, 'help' => 'Si vous la connaissez.' ),
					'bords'         => array( 'label' => 'Les bords du lit', 'type' => 'radio', 'options' => array( 'Bords durs (hardside)', 'Bords en mousse (softside)', 'Je ne sais pas' ) ),
					'couchage'      => array( 'label' => 'Le couchage', 'type' => 'radio', 'options' => array( 'Un seul matelas', 'Deux matelas, séparation conique', 'Deux matelas, séparation plate', 'Deux matelas, je ne sais pas' ) ),
					'stabilisation' => array( 'label' => 'Quand vous bougez, le matelas fait…', 'type' => 'radio', 'options' => array( 'Beaucoup de vagues', 'Un peu de vagues', 'Presque pas de vagues', 'Je ne sais pas' ) ),
					'photos'        => array( 'label' => 'Photos (facultatif)', 'type' => 'files', 'max_files' => 3, 'help' => 'L\'étiquette du matelas (souvent près de la valve) et la zone concernée. 3 photos, 8 Mo maximum chacune.' ),
				),
			),
			'contact'  => array(
				'title'  => 'Vos coordonnées',
				'intro'  => 'Nous vous rappelons avec une solution et, si besoin, un devis.',
				'fields' => array(
					'nom'         => array( 'label' => 'Nom et prénom', 'type' => 'text', 'required' => true, 'max' => 120, 'autocomplete' => 'name' ),
					'telephone'   => array( 'label' => 'Téléphone', 'type' => 'tel', 'required' => true, 'max' => 30, 'autocomplete' => 'tel', 'half' => true ),
					'email'       => array( 'label' => 'E-mail', 'type' => 'email', 'max' => 160, 'autocomplete' => 'email', 'half' => true, 'help' => 'Pour recevoir une copie de votre demande.' ),
					'code_postal' => array( 'label' => 'Code postal', 'type' => 'text', 'required' => true, 'max' => 10, 'autocomplete' => 'postal-code', 'inputmode' => 'numeric', 'half' => true ),
					'creneau'     => array( 'label' => 'Quand vous joindre', 'type' => 'select', 'options' => self::CRENEAUX, 'half' => true ),
				),
			),
		);
	}

	/** Tous les champs à plat : nom => définition. */
	public static function flat( $type ) {
		$out = array();
		foreach ( self::get( $type ) as $section ) {
			$out += $section['fields'];
		}
		return $out;
	}

	/**
	 * Valide et nettoie les valeurs reçues. Renvoie [ valeurs, erreurs ].
	 * Les listes fermées (select, radio, cartes) n'acceptent que leurs options.
	 */
	public static function validate( $type, array $input ) {
		$values = array();
		$errors = array();
		foreach ( self::flat( $type ) as $name => $f ) {
			if ( 'files' === $f['type'] ) {
				continue;
			}
			$raw = isset( $input[ $name ] ) ? wp_unslash( $input[ $name ] ) : '';
			$raw = is_scalar( $raw ) ? (string) $raw : '';
			$v   = 'textarea' === $f['type'] ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
			if ( ! empty( $f['max'] ) && mb_strlen( $v ) > $f['max'] ) {
				$v = mb_substr( $v, 0, $f['max'] );
			}
			if ( in_array( $f['type'], array( 'select', 'radio' ), true ) && '' !== $v && ! in_array( $v, $f['options'], true ) ) {
				$v = '';
			}
			if ( 'cards' === $f['type'] && '' !== $v && ! isset( $f['options'][ $v ] ) ) {
				$v = '';
			}
			if ( 'email' === $f['type'] && '' !== $v && ! is_email( $v ) ) {
				$errors[ $name ] = 'Adresse e-mail invalide.';
			}
			if ( 'tel' === $f['type'] && '' !== $v && strlen( preg_replace( '/\D/', '', $v ) ) < 9 ) {
				$errors[ $name ] = 'Numéro de téléphone incomplet.';
			}
			if ( ! empty( $f['required'] ) && '' === trim( $v ) ) {
				$errors[ $name ] = 'Ce champ est nécessaire.';
			}
			$values[ $name ] = $v;
		}
		return array( $values, $errors );
	}

	/** Libellé lisible d'une valeur (les cartes SAV stockent une clé). */
	public static function display_value( $type, $name, $value ) {
		$f = self::flat( $type );
		if ( isset( $f[ $name ] ) && 'cards' === $f[ $name ]['type'] && isset( $f[ $name ]['options'][ $value ] ) ) {
			return $f[ $name ]['options'][ $value ];
		}
		return $value;
	}
}
