<?php
/**
 * Kids Club by zacp — Asset-Enqueue
 * In functions.php einbinden:  require get_theme_file_path('inc/enqueue.php');
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {

		$dir    = get_stylesheet_directory_uri();
		$ver    = '3.13.0'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)
		$debug  = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$css_sf = $debug ? '' : '.min';
		$js_sf  = $debug ? '' : '.min';

		// 0. Schriften — SELBST GEHOSTET (DSGVO: keine IP an Google)
		wp_enqueue_style( 'kidsclub-fonts', $dir . '/assets/css/fonts.css', [], $ver );

		// 1. kidsclub CSS (minifiziert in Produktion)
		wp_enqueue_style( 'kidsclub', $dir . '/assets/css/kidsclub' . $css_sf . '.css', [ 'kidsclub-fonts' ], $ver );

		/**
		 * Karussells und Akkordeons gibt es NUR auf der Landing-Vorlage. Impressum und
		 * Datenschutz bekamen bisher trotzdem Swiper (154 KB) + Alpine (44 KB) mitgeliefert —
		 * knapp 200 KB JS ohne eine einzige Verwendung.
		 */
		$needs_interactive = is_front_page() || is_page_template( 'page-landing.php' );

		if ( $needs_interactive ) {
			// 2. Swiper CSS — selbst gehostet (DSGVO + kein Third-Party-SPOF)
			wp_enqueue_style( 'swiper', $dir . '/assets/vendor/swiper-bundle.min.css', [], '11.2.6' );

			// 3. Swiper JS — selbst gehostet
			wp_enqueue_script( 'swiper', $dir . '/assets/vendor/swiper-bundle.min.js', [], '11.2.6', true );
		}

		// 4. kidsclub JS (minifiziert in Produktion)
		// Die swiper-Abhängigkeit darf nur gesetzt werden, wenn swiper auch registriert ist —
		// sonst löst WordPress die Abhängigkeit nicht auf und kidsclub.js lädt gar nicht.
		wp_enqueue_script( 'kidsclub', $dir . '/assets/js/kidsclub' . $js_sf . '.js', $needs_interactive ? [ 'swiper' ] : [], $ver, true );
		// Eigene schwebende Symbole aus den Theme-Optionen (leer = Theme-Standard im JS).
		$floating_symbols = [];
		$floating_field   = get_field( 'floating_symbols', 'option' );
		if ( is_array( $floating_field ) ) {
			foreach ( $floating_field as $sym ) {
				if ( is_array( $sym ) && ! empty( $sym['url'] ) ) {
					$floating_symbols[] = esc_url_raw( $sym['url'] );
				}
			}
		}
		wp_localize_script(
			'kidsclub',
			'kcData',
			[
				'themeUri'        => $dir,
				'floatingSymbols' => $floating_symbols,
			]
		);

		if ( $needs_interactive ) {
			// 5. Galerie-Komponente — MUSS vor Alpine laufen (registriert Alpine.data
			//    bei 'alpine:init'). Als Abhängigkeit von Alpine => garantierte Reihenfolge.
			wp_enqueue_script( 'kc-gallery', $dir . '/assets/js/gallery.js', [], $ver, true );
			wp_script_add_data( 'kc-gallery', 'defer', true );

			// 6. Alpine.js — selbst gehostet, requis pour les accordéons (eltern, faq)
			wp_enqueue_script( 'alpinejs', $dir . '/assets/vendor/alpine.min.js', [ 'kc-gallery' ], '3.14.0', true );
			wp_script_add_data( 'alpinejs', 'defer', true );
		}
	},
	20
);

/* Preload der Display-Schrift (H1/LCP) — Fonts werden sonst erst nach dem CSS entdeckt */
add_action(
	'wp_head',
	function () {
		$dir = get_stylesheet_directory_uri();
		echo '<link rel="preload" href="' . esc_url( $dir . '/assets/fonts/jost/Jost-Bold.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	},
	2
);

/**
 * Reveal-Schalter — muss VOR dem ersten Paint laufen, darum inline im <head>.
 *
 * Ohne diese Klasse bleibt der Inhalt sichtbar (siehe .kc-js in kidsclub.css): eine
 * Enthüllung veredelt einen bereits lesbaren Zustand, sie ersetzt ihn nicht. Fällt das
 * Haupt-JS aus (Fehler in einem anderen Skript, blockiertes Netz), greift zusätzlich der
 * Notausgang nach 4 Sekunden — sonst stünde die Seite mit vollständigem DOM leer da.
 */
add_action(
	'wp_head',
	function () {
		echo '<script>document.documentElement.classList.add("kc-js");'
			. 'setTimeout(function(){var n=document.querySelectorAll(".reveal:not(.in)");'
			. 'for(var i=0;i<n.length;i++){n[i].classList.add("in");}},4000);</script>' . "\n";
	},
	3
);
