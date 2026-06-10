<?php
/**
 * wp_head-Cleanup: entfernt unnötige Tags (Performance + keine Versions-Divulgation).
 * In functions.php:  require get_theme_file_path('inc/cleanup.php');
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Emoji-Script (~10 KB inline) — Landing-Page nutzt keine Emojis */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/* WordPress-Version nicht divulgieren (Security through obscurity — minimal, aber gratis) */
remove_action( 'wp_head', 'wp_generator' );

/* RSD / Shortlink / oEmbed-Discovery — auf einem One-Pager nutzlos */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

/* XML-RPC deaktivieren (Brute-Force-Amplification via system.multicall) */
add_filter( 'xmlrpc_enabled', '__return_false' );

/* REST-API User-Endpoint für anonyme Besucher sperren (User-Enumeration) */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );
