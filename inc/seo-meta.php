<?php
/**
 * SEO-Meta: Description + Open Graph + Twitter Card.
 * Werte aus Theme-Einstellungen → SEO (ACF options), mit sinnvollen Fallbacks.
 * In functions.php:  require get_theme_file_path('inc/seo-meta.php');
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_head', function () {
	if ( ! is_page_template( 'page-landing.php' ) && ! is_front_page() ) return;

	$desc = function_exists( 'get_field' ) ? get_field( 'seo_description', 'option' ) : '';
	if ( ! $desc ) {
		$desc = 'Kinderzahnarztpraxis in Osnabrück — entspannt zum Zahnarzt vom ersten Zähnchen an. Vorsorge, Füllungen, Lachgas & Behandlung in 5 liebevoll gestalteten Zimmern.';
	}

	$og_image     = function_exists( 'get_field' ) ? get_field( 'seo_og_image', 'option' ) : null;
	$og_image_url = ( $og_image && ! empty( $og_image['url'] ) ) ? $og_image['url'] : '';

	$title = wp_get_document_title();
	$url   = home_url( add_query_arg( null, null ) );

	echo "\n<!-- SEO Meta -->\n";
	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";

	/* Open Graph — WhatsApp / Facebook / LinkedIn */
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="de_DE">' . "\n";
	echo '<meta property="og:site_name" content="Kids Club by zacp">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	if ( $og_image_url ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image_url ) . '">' . "\n";
		if ( ! empty( $og_image['width'] ) && ! empty( $og_image['height'] ) ) {
			echo '<meta property="og:image:width" content="' . absint( $og_image['width'] ) . '">' . "\n";
			echo '<meta property="og:image:height" content="' . absint( $og_image['height'] ) . '">' . "\n";
		}
	}

	/* Twitter Card */
	echo '<meta name="twitter:card" content="' . ( $og_image_url ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	if ( $og_image_url ) {
		echo '<meta name="twitter:image" content="' . esc_url( $og_image_url ) . '">' . "\n";
	}
}, 4 ); // vor schema.php (priorité 5)
