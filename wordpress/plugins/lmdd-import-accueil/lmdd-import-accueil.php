<?php
/**
 * Plugin Name:       LMDD – Import de la nouvelle page d'accueil
 * Description:       Crée la nouvelle page d'accueil La Maison du Dos (Elementor, widgets natifs), le modèle de pied de page et importe les images, sans passer par l'import de fichiers d'Elementor. Outils → Import accueil LMDD.
 * Version:           1.0.0
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
			array( 'id' => 'templates', 'label' => 'Création des modèles (accueil + pied de page) dans la bibliothèque Elementor' ),
			array( 'id' => 'page', 'label' => 'Création de la page « Accueil – nouvelle version » (brouillon)' ),
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

	private static function state() {
		$s = get_option( self::OPTION, array() );
		return is_array( $s ) ? $s : array();
	}

	private static function save_state( array $patch ) {
		update_option( self::OPTION, array_merge( self::state(), $patch ), false );
	}

	private static function data( $name ) {
		$file = __DIR__ . '/data/' . $name . '.json';
		if ( ! is_readable( $file ) ) {
			throw new Exception( 'Fichier manquant dans l\'extension : data/' . $name . '.json' );
		}
		$json = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! is_array( $json ) || empty( $json['content'] ) ) {
			throw new Exception( 'Modèle illisible : data/' . $name . '.json (' . json_last_error_msg() . ')' );
		}
		return $json;
	}

	private static function step_check() {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			throw new Exception( 'Elementor n\'est pas actif.' );
		}
		self::data( 'accueil' );
		self::data( 'pied-de-page' );
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
			'Elementor %s, PHP %s, limite de temps %s s, mémoire %s.',
			defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '?',
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
		foreach ( array( 'accueil' => 'tpl_accueil', 'pied-de-page' => 'tpl_pied' ) as $name => $key ) {
			$d = self::data( $name );
			if ( ! empty( $state[ $key ] ) && get_post( $state[ $key ] ) ) {
				self::write_elementor_data( (int) $state[ $key ], $d['content'] );
				$out[] = $d['title'] . ' (mis à jour, #' . $state[ $key ] . ')';
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
			self::save_state( array( $key => (int) $id ) );
			$out[] = $d['title'] . ' (#' . $id . ')';
		}
		return 'Modèles créés : ' . implode( ', ', $out ) . '.';
	}

	/** Crée (ou met à jour) la page brouillon construite avec Elementor. */
	private static function step_page() {
		$d     = self::data( 'accueil' );
		$state = self::state();
		$id    = ! empty( $state['page'] ) && get_post( $state['page'] ) ? (int) $state['page'] : 0;
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
		foreach ( array( 'page', 'tpl_accueil', 'tpl_pied' ) as $key ) {
			if ( empty( $state[ $key ] ) ) {
				continue;
			}
			$raw  = get_post_meta( $state[ $key ], '_elementor_data', true );
			$data = is_string( $raw ) ? json_decode( $raw, true ) : $raw;
			if ( ! is_array( $data ) ) {
				continue;
			}
			$done += self::replace_images( $data, $map );
			self::write_elementor_data( (int) $state[ $key ], $data );
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

	// ------------------------------------------------------------------ Interface

	public static function render() {
		$state = self::state();
		$steps = self::steps();
		?>
		<div class="wrap">
			<h1>Import de la nouvelle page d'accueil – La Maison du Dos</h1>
			<p>Cet outil crée, <strong>sans toucher à votre page d'accueil actuelle</strong> :</p>
			<ul style="list-style:disc;margin-left:20px">
				<li>la page brouillon <strong>« Accueil – nouvelle version »</strong>, construite avec Elementor (widgets natifs, modifiables) ;</li>
				<li>les modèles « Accueil – La Maison du Dos » et « Pied de page – La Maison du Dos » dans <em>Elementor → Modèles</em> ;</li>
				<li>les 7 images optimisées dans la médiathèque, avec leurs textes alternatifs.</li>
			</ul>
			<p>Chaque étape est une petite requête : l'import ne peut pas dépasser le temps maximal de l'hébergeur. Il peut être relancé sans créer de doublons.</p>
			<p><button type="button" class="button button-primary button-hero" id="lmdd-go">Lancer l'import</button></p>
			<ol id="lmdd-steps" style="font-size:14px;line-height:1.9">
				<?php foreach ( $steps as $s ) : ?>
					<li data-step="<?php echo esc_attr( $s['id'] ); ?>"><?php echo esc_html( $s['label'] ); ?> <span class="lmdd-status" style="color:#646970"></span></li>
				<?php endforeach; ?>
			</ol>
			<div id="lmdd-result" hidden style="background:#fff;border:1px solid #c3c4c7;border-left:4px solid #00a32a;padding:12px 16px;max-width:760px">
				<p><strong>Import terminé.</strong></p>
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
			function li(id) { return document.querySelector('[data-step="' + id + '"] .lmdd-status'); }
			function run(i) {
				current = i;
				if (i >= steps.length) return finish();
				var id = steps[i];
				li(id).textContent = '… en cours';
				li(id).style.color = '#2271b1';
				var body = new FormData();
				body.append('action', 'lmdd_import_step'); body.append('step', id); body.append('nonce', nonce);
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
			echo '<p>Quand la page vous convient : publiez-la, puis choisissez-la dans <em>Réglages → Lecture → Page d\'accueil</em>. Vous pouvez ensuite désactiver et supprimer cette extension.</p></div></div>';
		}
	}
}

add_action( 'plugins_loaded', array( 'LMDD_Import_Accueil', 'init' ) );
