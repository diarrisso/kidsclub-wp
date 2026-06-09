<?php
/**
 * Kids Club by zacp — Asset-Enqueue
 * In functions.php einbinden:  require get_theme_file_path('inc/enqueue.php');
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function () {

	$dir = get_stylesheet_directory_uri();
	$ver = '1.0.0'; // bei jedem CSS/JS-Update erhöhen (Cache-Busting)

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
	wp_enqueue_style(
		'kidsclub-fonts-dev',
		'https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Caveat:wght@500;600;700&family=Nunito:wght@500;600;700;800;900&display=swap',
		[], null
	);

	wp_enqueue_style( 'kidsclub', $dir . '/assets/css/kidsclub.css', [], $ver );

	wp_enqueue_script( 'kidsclub', $dir . '/assets/js/kidsclub.js', [], $ver, true );
}, 20 );
