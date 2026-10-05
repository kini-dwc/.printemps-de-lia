<?php
/**
 * Plugin Name:       LMDD – Import de la nouvelle page d'accueil
 * Description:       Installe la nouvelle page d'accueil La Maison du Dos (Elementor, widgets natifs), l'en-tête avec ses méga-menus, le pied de page et le menu, et importe les images, sans passer par l'import de fichiers d'Elementor. Outils → Import accueil LMDD. À supprimer une fois l'installation terminée : tout ce qu'elle a créé reste en place.
 * Version:           1.2.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * License:           GPL-2.0-or-later
 *
 * Fonctionnement : chaque étape est une petite requête (création des modèles, de la page, puis une image à la fois),
 * pour ne jamais dépasser le temps maximal d'exécution de l'hébergeur. L'extension peut être supprimée une fois l'import terminé.
 */

defined( 'ABSPATH' ) || exit;

final class LMDD_Import_Accueil {

	const OPTION = 'lmdd_import_accueil';
	const SLUG   = 'lmdd-import-accueil';

	/** Modèles du constructeur de thème de Royal Elementor Addons (identifiés par leur slug, comme le fait Royal). */
	const HF = array(
		'header' => array( 'slug' => 'user-header-lmdd-en-tete', 'data' => 'en-tete', 'state' => 'wpr_header' ),
		'footer' => array( 'slug' => 'user-footer-lmdd-pied-de-page', 'data' => 'pied-de-page', 'state' => 'wpr_footer' ),
	);

	/** Modèles de la bibliothèque Elementor : fichier de données => clé d'état. */
	const LIBRARY = array( 'accueil' => 'tpl_accueil', 'en-tete' => 'tpl_entete', 'pied-de-page' => 'tpl_pied' );

	/** Éléments installables séparément (cases à cocher) : fichier de données => élément. */
	const PART_OF = array( 'accueil' => 'accueil', 'en-tete' => 'entete', 'pied-de-page' => 'pied', 'header' => 'entete', 'footer' => 'pied' );
	const PARTS   = array(
		'accueil' => 'Page d\'accueil (page brouillon « Accueil – nouvelle version »)',
		'entete'  => 'En-tête, menu principal et méga-menus',
		'pied'    => 'Pied de page',
	);

	/** Préfixe des méga-menus (contenus Elementor du type « wpr_mega_menu » de Royal Elementor Addons). */
	const MEGA_PREFIX = 'lmdd-mega-';

	/** Images fournies : fichier => texte alternatif. */
	const IMAGES = array(
		'lit-a-eau-altura.webp'            => 'Lit à eau Altura de Poseïdon avec tête de lit, dans une chambre lumineuse',
		'schema-pression-matelas-eau.png'  => 'Schéma : sur un matelas classique la pression se concentre sur les épaules et le bassin, sur un matelas à eau elle est répartie uniformément',
		'lit-a-eau-havre.webp'             => 'Lit à eau Havre avec tête de lit capitonnée grise',
		'lit-a-eau-tec-line.webp'          => 'Lit à eau Tec-Line avec socle noir',
		'matelas-eau-leger-aqualight.webp' => 'Matelas à eau léger Aqualight Premium',
		'drap-housse-bella-donna.webp'     => 'Drap-housse jersey Bella Donna bordeaux',
		'logo-la-maison-du-dos.webp'       => 'La Maison du Dos – accueil',
	);

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'wp_ajax_lmdd_import_step', array( __CLASS__, 'ajax_step' ) );
		add_action( 'admin_post_lmdd_hf', array( __CLASS__, 'handle_hf_action' ) );
		add_action( 'init', array( __CLASS__, 'maybe_preview' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
			array_unshift( $links, '<a href="' . esc_url( admin_url( 'tools.php?page=' . self::SLUG ) ) . '">Lancer l\'import</a>' );
			return $links;
		} );
	}

	public static function menu() {
		add_management_page( 'Import de la page d\'accueil LMDD', 'Import accueil LMDD', 'manage_options', self::SLUG, array( __CLASS__, 'render' ) );
	}

	// ------------------------------------------------------------------ Étapes

	/** Liste ordonnée des étapes exécutées par l'interface. */
	private static function steps() {
		$steps = array(
			array( 'id' => 'check', 'label' => 'Vérification de l\'environnement' ),
			array( 'id' => 'templates', 'label' => 'Création des modèles (accueil, en-tête, pied de page) dans la bibliothèque Elementor' ),
			array( 'id' => 'page', 'label' => 'Création de la page « Accueil – nouvelle version » (brouillon)' ),
			array( 'id' => 'mega', 'label' => 'Création des 6 méga-menus (Lits à eau, Matelas réglables, Accessoires, Linge de lit, Couettes Hefel, Marques)' ),
			array( 'id' => 'menu', 'label' => 'Création du menu « Menu principal – La Maison du Dos » et rattachement des méga-menus' ),
			array( 'id' => 'hf', 'label' => 'Création de l\'en-tête et du pied de page dans le constructeur de thème Royal (sans les activer)' ),
		);
		foreach ( array_keys( self::IMAGES ) as $file ) {
			$steps[] = array( 'id' => 'image:' . $file, 'label' => 'Image : ' . $file );
		}
		$steps[] = array( 'id' => 'link', 'label' => 'Mise en place des images dans la page et les modèles' );
		return $steps;
	}

	public static function ajax_step() {
		if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'lmdd_import', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Accès refusé (session expirée ? rechargez la page).' ), 403 );
		}
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		$step = isset( $_POST['step'] ) ? sanitize_text_field( wp_unslash( $_POST['step'] ) ) : '';
		$t0   = microtime( true );
		try {
			if ( 'check' === $step ) {
				$msg = self::step_check();
			} elseif ( 'templates' === $step ) {
				$msg = self::step_templates();
			} elseif ( 'page' === $step ) {
				$msg = self::step_page();
			} elseif ( 'mega' === $step ) {
				$msg = self::step_mega();
			} elseif ( 'menu' === $step ) {
				$msg = self::step_menu();
			} elseif ( 'hf' === $step ) {
				$msg = self::step_hf();
			} elseif ( 0 === strpos( $step, 'image:' ) ) {
				$msg = self::step_image( substr( $step, 6 ) );
			} elseif ( 'link' === $step ) {
				$msg = self::step_link();
			} else {
				throw new Exception( 'Étape inconnue : ' . $step );
			}
			wp_send_json_success( array( 'message' => $msg, 'seconds' => round( microtime( true ) - $t0, 1 ) ) );
		} catch ( Throwable $e ) {
			wp_send_json_error( array(
				'message' => $e->getMessage(),
				'where'   => basename( $e->getFile() ) . ':' . $e->getLine(),
				'seconds' => round( microtime( true ) - $t0, 1 ),
			), 500 );
		}
	}

	/** Éléments cochés dans l'interface (envoyés avec chaque étape). */
	private static function parts() {
		$raw = isset( $_POST['parts'] ) ? sanitize_text_field( wp_unslash( $_POST['parts'] ) ) : implode( ',', array_keys( self::PARTS ) ); // phpcs:ignore WordPress.Security.NonceVerification
		return array_values( array_intersect( array_keys( self::PARTS ), explode( ',', $raw ) ) );
	}

	private static function wants( $name ) {
		return in_array( self::PART_OF[ $name ], self::parts(), true );
	}

	private static function state() {
		$s = get_option( self::OPTION, array() );
		return is_array( $s ) ? $s : array();
	}

	private static function save_state( array $patch ) {
		update_option( self::OPTION, array_merge( self::state(), $patch ), false );
	}

	/**
	 * Identifiant d'un contenu déjà créé : d'après le suivi d'installation, sinon d'après le marqueur « _lmdd_import »
	 * posé sur chaque contenu (utile si l'extension a été supprimée puis réinstallée : pas de doublon).
	 */
	private static function existing( $state_key, $post_type, $marker ) {
		$state = self::state();
		if ( ! empty( $state[ $state_key ] ) && get_post( $state[ $state_key ] ) ) {
			return (int) $state[ $state_key ];
		}
		$q = get_posts( array(
			'post_type'   => $post_type,
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_lmdd_import', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $marker, // phpcs:ignore WordPress.DB.SlowDBQuery
		) );
		if ( $q ) {
			self::save_state( array( $state_key => (int) $q[0] ) );
			return (int) $q[0];
		}
		return 0;
	}

	private static function data( $name, $key = 'content' ) {
		$file = __DIR__ . '/data/' . $name . '.json';
		if ( ! is_readable( $file ) ) {
			throw new Exception( 'Fichier manquant dans l\'extension : data/' . $name . '.json' );
		}
		$json = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! is_array( $json ) || empty( $json[ $key ] ) ) {
			throw new Exception( 'Modèle illisible : data/' . $name . '.json (' . json_last_error_msg() . ')' );
		}
		return $json;
	}

	private static function step_check() {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			throw new Exception( 'Elementor n\'est pas actif.' );
		}
		foreach ( array_keys( self::LIBRARY ) as $name ) {
			self::data( $name );
		}
		self::data( 'menu', 'items' );
		if ( ! self::parts() ) {
			throw new Exception( 'Cochez au moins un élément à installer.' );
		}
		foreach ( array_keys( self::IMAGES ) as $file ) {
			if ( ! is_readable( __DIR__ . '/images/' . $file ) ) {
				throw new Exception( 'Image manquante dans l\'extension : images/' . $file );
			}
		}
		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			throw new Exception( 'Dossier des médias inaccessible : ' . $uploads['error'] );
		}
		return sprintf(
			'Elementor %s, Royal Elementor Addons %s, PHP %s, limite de temps %s s, mémoire %s.',
			defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '?',
			self::royal_active() ? ( defined( 'WPR_ADDONS_VERSION' ) ? WPR_ADDONS_VERSION : 'actif' ) : 'absent (en-tête et pied de page resteront dans la bibliothèque)',
			PHP_VERSION,
			ini_get( 'max_execution_time' ),
			ini_get( 'memory_limit' )
		);
	}

	/** Crée (ou met à jour) les deux modèles de la bibliothèque, via l'API d'Elementor. */
	private static function step_templates() {
		$source = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );
		$state  = self::state();
		$out    = array();
		foreach ( self::LIBRARY as $name => $key ) {
			if ( ! self::wants( $name ) ) {
				continue;
			}
			$d        = self::data( $name );
			$existing = self::existing( $key, 'elementor_library', 'tpl-' . $name );
			if ( $existing ) {
				self::write_elementor_data( $existing, $d['content'] );
				update_post_meta( $existing, '_lmdd_import', 'tpl-' . $name );
				$out[] = $d['title'] . ' (mis à jour, #' . $existing . ')';
				continue;
			}
			$id = $source->save_item( array(
				'title'         => $d['title'],
				'type'          => $d['type'],
				'content'       => $d['content'],
				'page_settings' => isset( $d['page_settings'] ) ? $d['page_settings'] : array(),
			) );
			if ( is_wp_error( $id ) ) {
				throw new Exception( 'Elementor a refusé le modèle « ' . $d['title'] . ' » : ' . $id->get_error_message() );
			}
			update_post_meta( $id, '_lmdd_import', 'tpl-' . $name );
			self::save_state( array( $key => (int) $id ) );
			$out[] = $d['title'] . ' (#' . $id . ')';
		}
		return $out ? 'Modèles : ' . implode( ', ', $out ) . '.' : 'Ignoré (aucun modèle sélectionné).';
	}

	/** Crée (ou met à jour) la page brouillon construite avec Elementor. */
	private static function step_page() {
		if ( ! self::wants( 'accueil' ) ) {
			return 'Ignoré (page d\'accueil non sélectionnée).';
		}
		$d     = self::data( 'accueil' );
		$id = self::existing( 'page', 'page', 'page-accueil' );
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'  => 'Accueil – nouvelle version',
				'post_type'   => 'page',
				'post_status' => 'draft',
			), true );
			if ( is_wp_error( $id ) ) {
				throw new Exception( 'Impossible de créer la page : ' . $id->get_error_message() );
			}
			self::save_state( array( 'page' => (int) $id ) );
		}
		update_post_meta( $id, '_lmdd_import', 'page-accueil' );
		update_post_meta( $id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
		update_post_meta( $id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
		self::write_elementor_data( $id, $d['content'] );
		return 'Page brouillon #' . $id . ' prête (mise en page « Elementor pleine largeur »).';
	}

	/** Importe une image fournie (fichier local, sans téléchargement), une seule par requête. */
	private static function step_image( $file ) {
		if ( ! isset( self::IMAGES[ $file ] ) ) {
			throw new Exception( 'Image inconnue : ' . $file );
		}
		$existing = self::attachment_for( $file );
		if ( $existing ) {
			return $file . ' déjà présente dans les médias (#' . $existing . ').';
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( $file );
		if ( ! $tmp || ! copy( __DIR__ . '/images/' . $file, $tmp ) ) {
			throw new Exception( 'Copie temporaire impossible pour ' . $file . ' (dossier temporaire du serveur ?).' );
		}
		$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, pathinfo( $file, PATHINFO_FILENAME ) );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
			throw new Exception( 'Import de ' . $file . ' refusé : ' . $id->get_error_message() );
		}
		update_post_meta( $id, '_wp_attachment_image_alt', self::IMAGES[ $file ] );
		update_post_meta( $id, '_lmdd_source', $file );
		return $file . ' ajoutée aux médias (#' . $id . ') avec son texte alternatif.';
	}

	private static function attachment_for( $file ) {
		$q = get_posts( array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_lmdd_source', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
		) );
		return $q ? (int) $q[0] : 0;
	}

	/** Remplace les images « à choisir » par les images importées, dans la page et les deux modèles. */
	private static function step_link() {
		$state = self::state();
		$map   = array();
		foreach ( self::IMAGES as $file => $alt ) {
			$id = self::attachment_for( $file );
			if ( $id ) {
				$map[ $file ] = array( 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'alt' => $alt, 'source' => 'library', 'size' => '' );
			}
		}
		$done = 0;
		$keys = array_merge( array( 'page' ), array_values( self::LIBRARY ), wp_list_pluck( self::HF, 'state' ) );
		foreach ( array_keys( self::data( 'mega-menus', 'lits' ) ) as $mega ) {
			$keys[] = 'mega_' . $mega;
		}
		foreach ( $keys as $key ) {
			if ( empty( $state[ $key ] ) || ! get_post( $state[ $key ] ) ) {
				continue;
			}
			$raw  = get_post_meta( $state[ $key ], '_elementor_data', true );
			$data = is_string( $raw ) ? json_decode( $raw, true ) : $raw;
			if ( ! is_array( $data ) ) {
				continue;
			}
			$n = self::replace_images( $data, $map );
			if ( $n ) { // Seuls les contenus qui contiennent encore des images « à choisir » sont réécrits.
				$done += $n;
				self::write_elementor_data( (int) $state[ $key ], $data );
			}
		}
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		return $done . ' image(s) mise(s) en place. Cache CSS d\'Elementor régénéré.';
	}

	private static function replace_images( array &$elements, array $map ) {
		$n = 0;
		foreach ( $elements as &$el ) {
			if ( isset( $el['settings']['image']['url'] ) && 0 === strpos( (string) $el['settings']['image']['url'], 'a-choisir:' ) ) {
				$file = substr( $el['settings']['image']['url'], 10 );
				if ( isset( $map[ $file ] ) ) {
					$el['settings']['image'] = $map[ $file ];
					if ( isset( $el['settings']['_title'] ) && 0 === strpos( $el['settings']['_title'], 'Image à choisir' ) ) {
						$el['settings']['_title'] = 'Image : ' . $file;
					}
					$n++;
				}
			}
			if ( ! empty( $el['elements'] ) ) {
				$n += self::replace_images( $el['elements'], $map );
			}
		}
		return $n;
	}

	private static function write_elementor_data( $post_id, array $content ) {
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $content ) ) );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		delete_post_meta( $post_id, '_elementor_css' );
		delete_post_meta( $post_id, '_elementor_element_cache' );
	}

	// ------------------------------------------------------------------ Menu

	/** Crée (ou recrée) le menu WordPress utilisé par le widget « Menu » de l'en-tête. */
	private static function step_menu() {
		if ( ! in_array( 'entete', self::parts(), true ) ) {
			return 'Ignoré (en-tête non sélectionné).';
		}
		$d    = self::data( 'menu', 'items' );
		$menu = wp_get_nav_menu_object( $d['slug'] );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $d['name'] );
			if ( is_wp_error( $menu_id ) ) {
				throw new Exception( 'Impossible de créer le menu : ' . $menu_id->get_error_message() );
			}
			wp_update_term( $menu_id, 'nav_menu', array( 'slug' => $d['slug'] ) );
		} else {
			$menu_id = (int) $menu->term_id;
			foreach ( (array) wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) ) as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}
		$n     = 0;
		$megas = 0;
		foreach ( $d['items'] as $i => $item ) {
			$parent = self::add_menu_item( $menu_id, $item, 0, $i + 1 );
			$n++;
			$mega_id = ! empty( $item['mega'] ) ? self::mega_id( $item['mega'] ) : 0;
			if ( $mega_id ) {
				// Mêmes réglages que l'écran « Méga-menu » de Royal (Apparence → Menus) : pleine largeur, sous l'onglet.
				update_post_meta( $parent, 'wpr-mega-menu-item', $mega_id );
				update_post_meta( $parent, 'wpr-mega-menu-settings', array(
					'wpr_mm_enable'         => 'true',
					'wpr_mm_position'       => 'default',
					'wpr_mm_width'          => 'full',
					'wpr_mm_custom_width'   => 600,
					'wpr_mm_mobile_content' => 'submenu',
					'wpr_mm_render'         => 'default',
				) );
				$megas++;
			}
			foreach ( $item['children'] as $j => $child ) {
				self::add_menu_item( $menu_id, $child, $parent, $j + 1 );
				$n++;
			}
		}
		self::save_state( array( 'menu' => $menu_id ) );
		return 'Menu #' . $menu_id . ' : ' . count( $d['items'] ) . ' onglets, ' . $n . ' liens, ' . $megas . ' méga-menus rattachés. Modifiable dans Apparence → Menus.';
	}

	// ------------------------------------------------------------------ Méga-menus (Royal Elementor Addons)

	private static function mega_id( $key ) {
		if ( ! post_type_exists( 'wpr_mega_menu' ) ) {
			return 0;
		}
		$post = get_page_by_path( self::MEGA_PREFIX . $key, OBJECT, 'wpr_mega_menu' );
		return $post ? (int) $post->ID : 0;
	}

	/** Crée (ou met à jour) un contenu Elementor par méga-menu. */
	private static function step_mega() {
		if ( ! in_array( 'entete', self::parts(), true ) ) {
			return 'Ignoré (en-tête non sélectionné).';
		}
		if ( ! post_type_exists( 'wpr_mega_menu' ) ) {
			return 'Royal Elementor Addons n\'est pas actif : le menu s\'affichera avec des sous-menus simples.';
		}
		$out = array();
		foreach ( self::data( 'mega-menus', 'lits' ) as $key => $mega ) {
			$id = self::mega_id( $key );
			if ( ! $id ) {
				$id = wp_insert_post( array(
					'post_type'   => 'wpr_mega_menu',
					'post_title'  => $mega['title'],
					'post_name'   => self::MEGA_PREFIX . $key,
					'post_status' => 'publish',
				), true );
				if ( is_wp_error( $id ) ) {
					throw new Exception( 'Impossible de créer « ' . $mega['title'] . ' » : ' . $id->get_error_message() );
				}
			}
			update_post_meta( $id, '_elementor_template_type', 'wp-post' );
			update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
			self::write_elementor_data( $id, $mega['content'] );
			self::save_state( array( 'mega_' . $key => (int) $id ) );
			$out[] = $key . ' #' . $id;
		}
		return count( $out ) . ' méga-menus prêts (' . implode( ', ', $out ) . ').';
	}

	private static function add_menu_item( $menu_id, array $item, $parent, $position ) {
		$id = wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $item['title'],
			'menu-item-url'       => 0 === strpos( $item['url'], '/' ) ? home_url( $item['url'] ) : $item['url'],
			'menu-item-type'      => 'custom',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
			'menu-item-position'  => $position,
		) );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Lien de menu « ' . $item['title'] . ' » refusé : ' . $id->get_error_message() );
		}
		return (int) $id;
	}

	// ------------------------------------------------------------------ En-tête et pied de page (Royal Elementor Addons)

	private static function royal_active() {
		return post_type_exists( 'wpr_templates' ) && taxonomy_exists( 'wpr_template_type' );
	}

	/** Crée (ou met à jour) l'en-tête et le pied de page dans le constructeur de thème de Royal, sans les activer. */
	private static function step_hf() {
		if ( ! self::royal_active() ) {
			return 'Royal Elementor Addons n\'est pas actif : l\'en-tête et le pied de page restent disponibles dans Elementor → Modèles.';
		}
		$out = array();
		foreach ( self::HF as $type => $hf ) {
			if ( ! self::wants( $type ) ) {
				continue;
			}
			$d  = self::data( $hf['data'] );
			$id = self::hf_template_id( $type );
			if ( ! $id ) {
				$id = wp_insert_post( array(
					'post_type'    => 'wpr_templates',
					'post_title'   => $d['title'],
					'post_name'    => $hf['slug'],
					'post_content' => '',
					'post_status'  => 'publish',
				), true );
				if ( is_wp_error( $id ) ) {
					throw new Exception( 'Impossible de créer le modèle « ' . $d['title'] . ' » : ' . $id->get_error_message() );
				}
			}
			// Mêmes réglages que le bouton « Créer un modèle » du constructeur de thème de Royal.
			wp_set_object_terms( $id, array( $type, 'user' ), 'wpr_template_type' );
			update_post_meta( $id, '_elementor_template_type', 'wpr-theme-builder-' . $type );
			update_post_meta( $id, '_wpr_template_type', $type );
			update_post_meta( $id, '_wp_page_template', 'elementor_canvas' );
			self::write_elementor_data( $id, $d['content'] );
			self::save_state( array( $hf['state'] => (int) $id ) );
			$out[] = $d['title'] . ' (#' . $id . ')';
		}
		return $out ? 'Dans le constructeur de thème de Royal : ' . implode( ', ', $out ) . '. Leur affichage sur le site n\'est pas modifié.' : 'Ignoré (en-tête et pied de page non sélectionnés).';
	}

	private static function hf_template_id( $type ) {
		$post = get_page_by_path( self::HF[ $type ]['slug'], OBJECT, 'wpr_templates' );
		return $post ? (int) $post->ID : 0;
	}

	/** Conditions d'affichage Royal qui affichent nos modèles sur tout le site. */
	private static function hf_conditions( $type ) {
		return wp_json_encode( array( self::HF[ $type ]['slug'] => array( 'global' ) ) );
	}

	private static function hf_is_active( $type ) {
		$c = json_decode( (string) get_option( 'wpr_' . $type . '_conditions', '[]' ), true );
		return is_array( $c ) && isset( $c[ self::HF[ $type ]['slug'] ] );
	}

	/**
	 * Aperçu réservé aux administrateurs : ?lmdd_hf_preview=1 sur n'importe quelle page du site
	 * affiche le nouvel en-tête et le nouveau pied de page, sans rien changer pour les visiteurs.
	 */
	public static function maybe_preview() {
		if ( empty( $_GET['lmdd_hf_preview'] ) || ! current_user_can( 'manage_options' ) || ! self::royal_active() ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		foreach ( array_keys( self::HF ) as $type ) {
			if ( self::hf_template_id( $type ) ) {
				add_filter( 'pre_option_wpr_' . $type . '_conditions', function () use ( $type ) {
					return self::hf_conditions( $type );
				} );
			}
		}
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
	}

	/** Boutons « Afficher sur tout le site » et « Revenir à l'en-tête d'origine ». */
	public static function handle_hf_action() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Accès refusé.' );
		}
		check_admin_referer( 'lmdd_hf' );
		$do    = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
		$state = self::state();
		$msg   = '';
		if ( 'activate' === $do ) {
			$backup = isset( $state['hf_backup'] ) ? $state['hf_backup'] : array();
			foreach ( array_keys( self::HF ) as $type ) {
				if ( ! self::hf_template_id( $type ) ) {
					wp_die( 'Lancez d\'abord l\'import : le modèle « ' . esc_html( $type ) . ' » n\'existe pas.' );
				}
				if ( ! self::hf_is_active( $type ) ) {
					// Conditions d'origine, rétablies par « Revenir ».
					$backup[ $type ] = get_option( 'wpr_' . $type . '_conditions', '' );
				}
				update_option( 'wpr_' . $type . '_conditions', self::hf_conditions( $type ) );
			}
			self::save_state( array( 'hf_backup' => $backup ) );
			$msg = 'activated';
		} elseif ( 'restore' === $do ) {
			foreach ( array_keys( self::HF ) as $type ) {
				if ( isset( $state['hf_backup'][ $type ] ) ) {
					update_option( 'wpr_' . $type . '_conditions', $state['hf_backup'][ $type ] );
				}
			}
			$msg = 'restored';
		}
		self::purge_caches();
		wp_safe_redirect( admin_url( 'tools.php?page=' . self::SLUG . '&lmdd_hf=' . $msg . '#lmdd-hf' ) );
		exit;
	}

	private static function purge_caches() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		do_action( 'litespeed_purge_all' );
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}
	}

	// ------------------------------------------------------------------ Interface

	public static function render() {
		$state = self::state();
		$steps = self::steps();
		?>
		<div class="wrap">
			<h1>Installation de la nouvelle page d'accueil, de l'en-tête et du pied de page – La Maison du Dos</h1>
			<p>Cochez ce que vous voulez installer. Rien n'est affiché à vos visiteurs sans votre accord :
			la page d'accueil est créée en <strong>brouillon</strong>, l'en-tête et le pied de page sont créés <strong>sans être activés</strong>
			(aperçu puis activation en un clic, en bas de cette page).</p>
			<fieldset id="lmdd-parts" style="background:#fff;border:1px solid #c3c4c7;padding:12px 16px;max-width:760px">
				<?php
				$installed = array(
					'accueil' => (bool) self::existing( 'page', 'page', 'page-accueil' ),
					'entete'  => self::royal_active() && self::hf_template_id( 'header' ),
					'pied'    => self::royal_active() && self::hf_template_id( 'footer' ),
				);
				foreach ( self::PARTS as $part => $label ) :
					?>
					<p style="margin:6px 0"><label><input type="checkbox" value="<?php echo esc_attr( $part ); ?>" <?php checked( ! $installed[ $part ] ); ?>>
					<strong><?php echo esc_html( $label ); ?></strong>
					<?php if ( $installed[ $part ] ) : ?>
						<span style="color:#b32d2e"> – déjà installé : cochez seulement pour le <strong>réinstaller</strong> (vos modifications faites dans Elementor seraient remplacées)</span>
					<?php endif; ?>
					</label></p>
				<?php endforeach; ?>
				<p style="margin:10px 0 0;color:#646970">Les 7 images optimisées sont ajoutées à la médiathèque si besoin (une seule fois). Chaque étape est une petite requête :
				l'installation ne peut pas dépasser le temps maximal de l'hébergeur.</p>
			</fieldset>
			<p><button type="button" class="button button-primary button-hero" id="lmdd-go">Installer la sélection</button></p>
			<ol id="lmdd-steps" style="font-size:14px;line-height:1.9">
				<?php foreach ( $steps as $s ) : ?>
					<li data-step="<?php echo esc_attr( $s['id'] ); ?>"><?php echo esc_html( $s['label'] ); ?> <span class="lmdd-status" style="color:#646970"></span></li>
				<?php endforeach; ?>
			</ol>
			<div id="lmdd-result" hidden style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid #00a32a;padding:12px 16px;max-width:760px">
				<p><strong>Installation terminée.</strong></p>
				<p id="lmdd-links"></p>
			</div>
			<div id="lmdd-error" hidden style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid #d63638;padding:12px 16px;max-width:760px">
				<p><strong>L'import s'est arrêté.</strong> Copiez le message ci-dessous et envoyez-le pour diagnostic :</p>
				<pre id="lmdd-error-text" style="white-space:pre-wrap;background:#f6f7f7;padding:10px"></pre>
				<p><button type="button" class="button" id="lmdd-retry">Reprendre à cette étape</button></p>
			</div>
		</div>
		<script>
		(function () {
			var steps = <?php echo wp_json_encode( wp_list_pluck( $steps, 'id' ) ); ?>;
			var nonce = <?php echo wp_json_encode( wp_create_nonce( 'lmdd_import' ) ); ?>;
			var ajax = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
			var go = document.getElementById('lmdd-go'), retry = document.getElementById('lmdd-retry');
			var current = 0;
			function parts() { return Array.prototype.map.call(document.querySelectorAll('#lmdd-parts input:checked'), function (c) { return c.value; }).join(','); }
			function li(id) { return document.querySelector('[data-step="' + id + '"] .lmdd-status'); }
			function run(i) {
				current = i;
				if (i >= steps.length) return finish();
				var id = steps[i];
				li(id).textContent = '… en cours';
				li(id).style.color = '#2271b1';
				var body = new FormData();
				body.append('action', 'lmdd_import_step'); body.append('step', id); body.append('nonce', nonce); body.append('parts', parts());
				fetch(ajax, { method: 'POST', credentials: 'same-origin', body: body })
					.then(function (r) { return r.text().then(function (t) { return { status: r.status, text: t }; }); })
					.then(function (res) {
						var j = null;
						try { j = JSON.parse(res.text); } catch (e) {}
						if (j && j.success) {
							li(id).textContent = '✓ ' + j.data.message + ' (' + j.data.seconds + ' s)';
							li(id).style.color = '#00a32a';
							return run(i + 1);
						}
						var msg = j && j.data ? j.data.message + (j.data.where ? '\n(' + j.data.where + ')' : '') : 'Réponse HTTP ' + res.status + ' :\n' + res.text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').slice(0, 800);
						fail(id, msg);
					})
					.catch(function (e) { fail(id, 'Erreur réseau : ' + e.message); });
			}
			function fail(id, msg) {
				li(id).textContent = '✗ échec';
				li(id).style.color = '#d63638';
				document.getElementById('lmdd-error-text').textContent = 'Étape « ' + id + ' » :\n' + msg;
				document.getElementById('lmdd-error').hidden = false;
				go.disabled = false;
			}
			function finish() {
				document.getElementById('lmdd-result').hidden = false;
				document.getElementById('lmdd-links').textContent = 'Rechargement de la page pour afficher les liens…';
				setTimeout(function () { location.reload(); }, 1200);
			}
			go.addEventListener('click', function () {
				go.disabled = true;
				document.getElementById('lmdd-error').hidden = true;
				document.querySelectorAll('.lmdd-status').forEach(function (s) { s.textContent = ''; });
				run(0);
			});
			retry.addEventListener('click', function () { document.getElementById('lmdd-error').hidden = true; go.disabled = true; run(current); });
		})();
		</script>
		<?php
		if ( ! empty( $state['page'] ) && get_post( $state['page'] ) ) {
			$page = (int) $state['page'];
			echo '<div class="wrap"><div style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid #00a32a;padding:12px 16px;max-width:760px;margin-top:12px">';
			echo '<p><strong>Page créée :</strong> « ' . esc_html( get_the_title( $page ) ) . ' » (' . esc_html( get_post_status( $page ) ) . ').</p><p>';
			echo '<a class="button button-primary" href="' . esc_url( admin_url( 'post.php?post=' . $page . '&action=elementor' ) ) . '">Modifier avec Elementor</a> ';
			echo '<a class="button" href="' . esc_url( get_preview_post_link( $page ) ) . '" target="_blank" rel="noopener">Prévisualiser</a> ';
			echo '<a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ) . '">Voir les modèles</a></p>';
			echo '<p>Quand la page vous convient : publiez-la, puis choisissez-la dans <em>Réglages → Lecture → Page d\'accueil</em>.</p></div></div>';
		}
		self::render_hf_box();
		?>
		<div class="wrap"><div style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid #dba617;padding:12px 16px;max-width:760px;margin-top:12px">
			<h2 style="margin-top:4px">Une fois tout en place : supprimez cette extension</h2>
			<p>Tout ce qu'elle a créé est enregistré dans WordPress, Elementor et Royal Elementor Addons, et <strong>reste en place</strong> après sa suppression :
			page d'accueil, en-tête, méga-menus, pied de page, menu, images et réglages d'affichage.</p>
			<p><em>Extensions → LMDD – Import… → Désactiver</em>, puis <em>Supprimer</em>. La suppression efface uniquement son propre suivi d'installation.
			Vous perdez seulement l'aperçu <code>?lmdd_hf_preview=1</code> et le bouton « Revenir » : l'ancien en-tête et l'ancien pied de page restent
			disponibles dans <em>Royal Addons → Constructeur de thème</em>, où vous pouvez les réactiver à tout moment.</p>
		</div></div>
		<?php
	}

	private static function render_hf_box() {
		if ( ! self::royal_active() || ! self::hf_template_id( 'header' ) || ! self::hf_template_id( 'footer' ) ) {
			return;
		}
		$state   = self::state();
		$active  = self::hf_is_active( 'header' ) && self::hf_is_active( 'footer' );
		$page    = ! empty( $state['page'] ) ? (int) $state['page'] : 0;
		$notice  = isset( $_GET['lmdd_hf'] ) ? sanitize_key( $_GET['lmdd_hf'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$preview = add_query_arg( 'lmdd_hf_preview', '1', home_url( '/' ) );
		$button  = function ( $do, $label, $class ) {
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline-block;margin-right:6px">';
			wp_nonce_field( 'lmdd_hf' );
			echo '<input type="hidden" name="action" value="lmdd_hf"><input type="hidden" name="do" value="' . esc_attr( $do ) . '">';
			echo '<button type="submit" class="button ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</button></form>';
		};
		echo '<div class="wrap" id="lmdd-hf"><div style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid ' . ( $active ? '#00a32a' : '#2271b1' ) . ';padding:12px 16px;max-width:760px;margin-top:12px">';
		echo '<h2 style="margin-top:4px">En-tête et pied de page</h2>';
		if ( 'activated' === $notice ) {
			echo '<p style="color:#00a32a"><strong>✓ Nouvel en-tête et nouveau pied de page affichés sur tout le site.</strong> Videz aussi le cache de votre hébergeur s\'il en a un.</p>';
		} elseif ( 'restored' === $notice ) {
			echo '<p style="color:#00a32a"><strong>✓ En-tête et pied de page d\'origine rétablis.</strong></p>';
		}
		echo '<p>État : <strong>' . ( $active ? 'affichés sur tout le site' : 'créés, pas encore affichés (vos visiteurs voient toujours l\'ancien en-tête)' ) . '</strong>.</p><p>';
		echo '<a class="button" href="' . esc_url( $preview ) . '" target="_blank" rel="noopener">Aperçu sur la page d\'accueil actuelle</a> ';
		if ( $page ) {
			echo '<a class="button" href="' . esc_url( add_query_arg( 'lmdd_hf_preview', '1', get_preview_post_link( $page ) ) ) . '" target="_blank" rel="noopener">Aperçu avec la nouvelle page d\'accueil</a> ';
		}
		echo '</p><p style="color:#646970">L\'aperçu n\'est visible que par vous. Ajoutez <code>?lmdd_hf_preview=1</code> à l\'adresse de n\'importe quelle page pour la voir avec le nouvel en-tête.</p><p>';
		foreach ( array( 'header' => 'Modifier l\'en-tête avec Elementor', 'footer' => 'Modifier le pied de page avec Elementor' ) as $type => $label ) {
			echo '<a class="button" href="' . esc_url( admin_url( 'post.php?post=' . self::hf_template_id( $type ) . '&action=elementor' ) ) . '">' . esc_html( $label ) . '</a> ';
		}
		echo '</p><p>';
		if ( ! $active ) {
			$button( 'activate', 'Afficher sur tout le site', 'button-primary' );
		}
		if ( $active && ! empty( $state['hf_backup'] ) ) {
			$button( 'restore', 'Revenir à l\'en-tête et au pied de page d\'origine', '' );
		}
		echo '</p><p style="color:#646970">L\'activation remplace les conditions d\'affichage de Royal Addons (<em>Constructeur de thème → En-tête / Pied de page</em>). Les anciennes conditions sont sauvegardées : le bouton « Revenir » les rétablit.</p>';
		echo '</div></div>';
	}
}

add_action( 'plugins_loaded', array( 'LMDD_Import_Accueil', 'init' ) );
