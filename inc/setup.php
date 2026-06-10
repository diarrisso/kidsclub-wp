<?php
/**
 * Theme-Setup: title-tag, thumbnails, SEO-Title.
 * In functions.php:  require get_theme_file_path('inc/setup.php');
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );        // WP rendert <title> — Pflicht für SEO
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
} );

/**
 * SEO-Title für die Landing-Page: "Kinderzahnarzt Osnabrück | Kids Club by zacp"
 * statt des generischen "Seitenname – Blogname".
 * Überschreibbar im Backend: Theme-Einstellungen → SEO → Meta-Titel.
 */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_page_template( 'page-landing.php' ) || is_front_page() ) {
		$custom = function_exists( 'get_field' ) ? get_field( 'seo_title', 'option' ) : '';
		$parts['title'] = $custom ?: 'Kinderzahnarzt Osnabrück';
		// Front-Page nutzt 'tagline' statt 'site' → Marke explizit anhängen
		if ( array_key_exists( 'tagline', $parts ) ) {
			$parts['tagline'] = 'Kids Club by zacp';
		}
	}
	return $parts;
} );

add_filter( 'document_title_separator', function () {
	return '|';
} );
