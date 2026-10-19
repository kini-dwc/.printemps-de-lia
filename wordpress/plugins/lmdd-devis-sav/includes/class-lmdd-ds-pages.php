<?php
/**
 * Pages de contenu (Qui sommes-nous, Nos marques, FAQ, Livraison / Paiement, Contact, Mentions légales, CGV) :
 * mises en forme qu'Elementor gratuit ne propose pas (sommaire collant, frise, tableaux, formulaire de contact).
 * Chargées seulement sur les pages installées par « LMDD – Import » (méta _lmdd_contenu), brouillons compris.
 */
defined( 'ABSPATH' ) || exit;

final class LMDD_DS_Pages {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 30 );
		add_action( 'wp_footer', array( __CLASS__, 'sommaire_actif' ), 99 );
	}

	private static function concernee() {
		return is_singular( 'page' ) && get_post_meta( get_queried_object_id(), '_lmdd_contenu', true );
	}

	public static function assets() {
		if ( self::concernee() ) {
			wp_enqueue_style( 'lmdd-pages', plugin_dir_url( LMDD_DS_FILE ) . 'assets/lmdd-pages.css', array(), LMDD_DS_VERSION );
		}
	}

	/** Met en évidence, dans le sommaire, la partie en cours de lecture. */
	public static function sommaire_actif() {
		if ( ! self::concernee() ) {
			return;
		}
		?>
<script id="lmdd-sommaire">(function(){var l=document.querySelectorAll('.lmdd-sommaire a[href^="#"]');if(!l.length||!('IntersectionObserver' in window))return;var m={};
l.forEach(function(a){var t=document.getElementById(decodeURIComponent(a.getAttribute('href').slice(1)));if(t)m[t.id]=a;});
var o=new IntersectionObserver(function(e){e.forEach(function(x){if(x.isIntersecting&&m[x.target.id]){l.forEach(function(a){a.classList.remove('is-active');});m[x.target.id].classList.add('is-active');}});},{rootMargin:'-130px 0px -65% 0px'});
Object.keys(m).forEach(function(k){o.observe(document.getElementById(k));});})();</script>
		<?php
	}
}
