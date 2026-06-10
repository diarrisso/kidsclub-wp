<?php
/**
 * Custom Post Type: Praxis-Foto (Galerie)
 *
 * CPT interne : pas de single/archive publics (one-pager) — les photos
 * sont gérées dans l'admin (featured image = la photo) et affichées
 * par le block Praxis-Galerie avec filtres par Bereich.
 *
 * @package kidsclub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Praxis-Foto Post Type.
 */
function kc_register_praxis_foto_post_type() {
	$labels = [
		'name'               => __( 'Praxis-Fotos', 'kidsclub' ),
		'singular_name'      => __( 'Praxis-Foto', 'kidsclub' ),
		'menu_name'          => __( 'Praxis-Galerie', 'kidsclub' ),
		'add_new'            => __( 'Neu hinzufügen', 'kidsclub' ),
		'add_new_item'       => __( 'Neues Foto hinzufügen', 'kidsclub' ),
		'edit_item'          => __( 'Foto bearbeiten', 'kidsclub' ),
		'new_item'           => __( 'Neues Foto', 'kidsclub' ),
		'search_items'       => __( 'Fotos durchsuchen', 'kidsclub' ),
		'not_found'          => __( 'Keine Fotos gefunden', 'kidsclub' ),
		'not_found_in_trash' => __( 'Keine Fotos im Papierkorb', 'kidsclub' ),
		'all_items'          => __( 'Alle Fotos', 'kidsclub' ),
		'featured_image'     => __( 'Foto', 'kidsclub' ),
		'set_featured_image' => __( 'Foto festlegen', 'kidsclub' ),
	];

	$args = [
		'labels'              => $labels,
		// CPT interne : pas d'URLs publiques (pas de pages "thin content").
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'rewrite'             => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 22,
		'menu_icon'           => 'dashicons-format-gallery',
		// Reihenfolge in der Galerie = manuelle Sortierung (menu_order).
		'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
	];

	register_post_type( 'praxis_foto', $args );
}
add_action( 'init', 'kc_register_praxis_foto_post_type' );

/**
 * Register Bereich Taxonomy (Empfang, Wartezimmer, Behandlung, …) —
 * steuert die Filter-Chips der Galerie.
 */
function kc_register_bereich_taxonomy() {
	$labels = [
		'name'          => __( 'Bereiche', 'kidsclub' ),
		'singular_name' => __( 'Bereich', 'kidsclub' ),
		'search_items'  => __( 'Bereiche suchen', 'kidsclub' ),
		'all_items'     => __( 'Alle Bereiche', 'kidsclub' ),
		'edit_item'     => __( 'Bereich bearbeiten', 'kidsclub' ),
		'update_item'   => __( 'Bereich aktualisieren', 'kidsclub' ),
		'add_new_item'  => __( 'Neuen Bereich hinzufügen', 'kidsclub' ),
		'new_item_name' => __( 'Neuer Bereich', 'kidsclub' ),
		'menu_name'     => __( 'Bereiche', 'kidsclub' ),
	];

	$args = [
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => false,
		'show_ui'           => true,
		'show_in_rest'      => false,
		'show_admin_column' => true,
		'rewrite'           => false,
	];

	register_taxonomy( 'bereich', [ 'praxis_foto' ], $args );
}
add_action( 'init', 'kc_register_bereich_taxonomy' );
