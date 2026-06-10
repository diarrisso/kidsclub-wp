<?php
/**
 * Custom Post Type: Team (Behandler & Praxisteam)
 *
 * CPT interne : pas de single/archive publics (one-pager) — les membres
 * sont gérés dans l'admin et affichés par le block Team de la landing page.
 *
 * @package kidsclub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Team Post Type.
 */
function kc_register_team_post_type() {
	$labels = [
		'name'               => __( 'Team', 'kidsclub' ),
		'singular_name'      => __( 'Teammitglied', 'kidsclub' ),
		'menu_name'          => __( 'Team', 'kidsclub' ),
		'add_new'            => __( 'Neu hinzufügen', 'kidsclub' ),
		'add_new_item'       => __( 'Neues Teammitglied hinzufügen', 'kidsclub' ),
		'edit_item'          => __( 'Teammitglied bearbeiten', 'kidsclub' ),
		'new_item'           => __( 'Neues Teammitglied', 'kidsclub' ),
		'view_item'          => __( 'Teammitglied ansehen', 'kidsclub' ),
		'search_items'       => __( 'Team durchsuchen', 'kidsclub' ),
		'not_found'          => __( 'Keine Teammitglieder gefunden', 'kidsclub' ),
		'not_found_in_trash' => __( 'Keine Teammitglieder im Papierkorb', 'kidsclub' ),
		'all_items'          => __( 'Alle Teammitglieder', 'kidsclub' ),
		'featured_image'     => __( 'Porträtfoto', 'kidsclub' ),
		'set_featured_image' => __( 'Porträtfoto festlegen', 'kidsclub' ),
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
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-groups',
		// Reihenfolge der Karten = manuelle Sortierung (menu_order).
		'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
	];

	register_post_type( 'team', $args );
}
add_action( 'init', 'kc_register_team_post_type' );

/**
 * Register Funktion Taxonomy for Team (z.B. Behandler / Praxisteam).
 */
function kc_register_funktion_taxonomy() {
	$labels = [
		'name'          => __( 'Funktionen', 'kidsclub' ),
		'singular_name' => __( 'Funktion', 'kidsclub' ),
		'search_items'  => __( 'Funktionen suchen', 'kidsclub' ),
		'all_items'     => __( 'Alle Funktionen', 'kidsclub' ),
		'edit_item'     => __( 'Funktion bearbeiten', 'kidsclub' ),
		'update_item'   => __( 'Funktion aktualisieren', 'kidsclub' ),
		'add_new_item'  => __( 'Neue Funktion hinzufügen', 'kidsclub' ),
		'new_item_name' => __( 'Neue Funktion', 'kidsclub' ),
		'menu_name'     => __( 'Funktionen', 'kidsclub' ),
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

	register_taxonomy( 'funktion', [ 'team' ], $args );
}
add_action( 'init', 'kc_register_funktion_taxonomy' );

/**
 * ACF Fields for Team members (Foto = Beitragsbild / featured image).
 */
function kc_register_team_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		[
			'key'      => 'group_team_member',
			'title'    => 'Teammitglied Details',
			'fields'   => [
				[
					'key'      => 'field_team_role',
					'label'    => 'Rolle / Position',
					'name'     => 'tm_role',
					'type'     => 'text',
					'required' => 1,
				],
				[
					'key'   => 'field_team_bio',
					'label' => 'Kurz-Bio',
					'name'  => 'tm_bio',
					'type'  => 'textarea',
					'rows'  => 3,
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'team',
					],
				],
			],
		]
	);
}
add_action( 'acf/init', 'kc_register_team_fields' );
