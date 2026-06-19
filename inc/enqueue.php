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
		$ver    = '3.0.1'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)
		$debug  = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$css_sf = $debug ? '' : '.min';
		$js_sf  = $debug ? '' : '.min';

		// 0. Schriften — SELBST GEHOSTET (DSGVO: keine IP an Google)
		wp_enqueue_style( 'kidsclub-fonts', $dir . '/assets/css/fonts.css', [], $ver );

		// 1. kidsclub CSS (minifiziert in Produktion)
		wp_enqueue_style( 'kidsclub', $dir . '/assets/css/kidsclub' . $css_sf . '.css', [ 'kidsclub-fonts' ], $ver );

		// 2. Swiper CSS — selbst gehostet (DSGVO + kein Third-Party-SPOF)
		wp_enqueue_style( 'swiper', $dir . '/assets/vendor/swiper-bundle.min.css', [], '11.2.6' );

		// 3. Swiper JS — selbst gehostet
		wp_enqueue_script( 'swiper', $dir . '/assets/vendor/swiper-bundle.min.js', [], '11.2.6', true );

		// 4. kidsclub JS (minifiziert in Produktion)
		wp_enqueue_script( 'kidsclub', $dir . '/assets/js/kidsclub' . $js_sf . '.js', [ 'swiper' ], $ver, true );

		// 5. Alpine.js — selbst gehostet, requis pour les accordéons (eltern, faq)
		wp_enqueue_script( 'alpinejs', $dir . '/assets/vendor/alpine.min.js', [], '3.14.0', true );
		wp_script_add_data( 'alpinejs', 'defer', true );
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
