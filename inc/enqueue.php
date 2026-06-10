<?php
/**
 * Kids Club by zacp — Asset-Enqueue
 * In functions.php einbinden:  require get_theme_file_path('inc/enqueue.php');
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function () {

	$dir = get_stylesheet_directory_uri();
	$ver = '1.1.8'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)

	/*
	 * SCHRIFTEN — BEST PRACTICE (DSGVO):
	 * Schriften SELBST HOSTEN, nicht über das Google-CDN laden.
	 * Eine Arztpraxis-Seite ohne Cookie-Consent darf keine IP an Google senden.
	 *  1. Fredoka, Caveat, Nunito bei https://gwfh.mranftl.com herunterladen
	 *  2. nach /assets/fonts/ legen + assets/css/fonts.css mit @font-face anlegen
	 *  3. fonts.css hier einbinden:
	 * wp_enqueue_style( 'kidsclub-fonts', $dir . '/assets/css/fonts.css', [], $ver );
	 *
	 * Nur für die Entwicklung / Abnahme (NICHT produktiv ohne Consent):
	 */
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		wp_enqueue_style(
			'kidsclub-fonts-dev',
			'https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Caveat:wght@500;600;700&family=Nunito:wght@500;600;700;800;900&display=swap',
			[], null
		);
	}

	// 1. kidsclub CSS
	wp_enqueue_style( 'kidsclub', $dir . '/assets/css/kidsclub.css', [], $ver );

	// 2. Swiper CSS
	wp_enqueue_style(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		[], '11.0.0'
	);

	// 3. Swiper JS
	wp_enqueue_script(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
		[], '11.0.0', true
	);

	// 4. kidsclub JS (depends on swiper)
	wp_enqueue_script( 'kidsclub', $dir . '/assets/js/kidsclub.js', ['swiper'], $ver, true );

	// 5. Alpine.js — requis pour les accordéons (eltern, faq)
	wp_enqueue_script(
		'alpinejs',
		'https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js',
		[], '3.14.0', true
	);
	wp_script_add_data( 'alpinejs', 'defer', true );
}, 20 );
