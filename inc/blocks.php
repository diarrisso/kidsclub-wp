<?php
/**
 * Kids Club by zacp — ACF Flexible Content Feld
 *
 * Best Practice: dieses Feld als acf-json speichern (ACF erzeugt es automatisch,
 * wenn der Ordner /acf-json im Theme existiert und beschreibbar ist).
 * So ist die Feldstruktur versioniert und über alle Umgebungen synchron.
 *
 * Hier zur Übersicht als PHP registriert. In functions.php:
 *   require get_theme_file_path('inc/blocks.php');
 *
 * Jede LANDING-SEKTION = ein Flexible-Content-Layout.
 * Gerendert wird in template-parts/flexible.php über get_template_part().
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( [
		'key'      => 'group_kidsclub_landing',
		'title'    => 'Kids Club — Seiteninhalt',
		'location' => [ [ [
			'param'    => 'page_template',
			'operator' => '==',
			'value'    => 'page-landing.php',
		] ] ],
		'fields'   => [ [
			'key'          => 'field_kc_sections',
			'label'        => 'Sektionen',
			'name'         => 'sections',
			'type'         => 'flexible_content',
			'button_label' => 'Sektion hinzufügen',
			'layouts'      => [

				/* ---------- HERO ---------- */
				'layout_hero' => [
					'key'        => 'layout_hero',
					'name'       => 'hero',
					'label'      => 'Hero (Banner)',
					'display'    => 'block',
					'sub_fields' => [
						kc_field( 'hero_eyebrow', 'Eyebrow', 'text' ),
						kc_field( 'hero_title', 'Überschrift', 'text' ),
						kc_field( 'hero_highlight', 'Hervorgehobener Teil', 'text' ),
						kc_field( 'hero_text', 'Text', 'textarea' ),
						kc_field( 'hero_bg', 'Hintergrundbild / Video-Poster', 'image' ),
						[
							'key'           => 'field_kc_hero_anim',
							'label'         => 'Kinder-Animation',
							'name'          => 'hero_anim',
							'type'          => 'select',
							'default_value' => 'winken',
							'choices'       => [
								'winken'   => 'Winken (Schweben + Winken)',
								'huepfen'  => 'Hüpfen',
								'laufband' => 'Laufband (Motive endlos)',
								'aus'      => 'Aus (statisch / eigenes Foto)',
							],
						],
						[
							'key'   => 'field_kc_hero_show_kids',
							'label' => 'Illustrierte Kinder anzeigen',
							'name'  => 'hero_show_kids',
							'type'  => 'true_false',
							'ui'    => 1, 'default_value' => 1,
							'instructions' => 'Aus, wenn ein eigenes Foto/Video als Hintergrund genutzt wird.',
						],
					],
				],

				/* ---------- LEISTUNGEN ---------- */
				'layout_leistungen' => [
					'key'        => 'layout_leistungen',
					'name'       => 'leistungen',
					'label'      => 'Leistungsspektrum',
					'display'    => 'block',
					'sub_fields' => [
						kc_field( 'eyebrow', 'Eyebrow', 'text' ),
						kc_field( 'title', 'Überschrift', 'text' ),
						kc_field( 'text', 'Einleitung', 'textarea' ),
						[
							'key'        => 'field_kc_leistungen_items',
							'label'      => 'Leistungen',
							'name'       => 'items',
							'type'       => 'repeater',
							'layout'     => 'block',
							'button_label' => 'Leistung hinzufügen',
							'sub_fields' => [
								kc_field( 'icon', 'Icon (SVG-Slug)', 'text' ),
								kc_field( 'heading', 'Titel', 'text' ),
								kc_field( 'body', 'Beschreibung', 'textarea' ),
							],
						],
					],
				],

				/* ---------- 5 ZIMMER ---------- */
				'layout_zimmer' => [
					'key'        => 'layout_zimmer',
					'name'       => 'zimmer',
					'label'      => '5 Zimmer',
					'display'    => 'block',
					'sub_fields' => [
						kc_field( 'eyebrow', 'Eyebrow', 'text' ),
						kc_field( 'title', 'Überschrift', 'text' ),
						kc_field( 'text', 'Einleitung', 'textarea' ),
						[
							'key'        => 'field_kc_zimmer_rooms',
							'label'      => 'Zimmer',
							'name'       => 'rooms',
							'type'       => 'repeater',
							'max'        => 5,
							'layout'     => 'table',
							'sub_fields' => [
								kc_field( 'name', 'Name', 'text' ),
								kc_field( 'theme', 'Motto', 'text' ),
								[
									'key' => 'field_kc_room_color', 'label' => 'Farbe', 'name' => 'color',
									'type' => 'select',
									'choices' => [ 'g'=>'Grün','y'=>'Gelb','o'=>'Orange','b'=>'Blau','l'=>'Lila' ],
								],
							],
						],
					],
				],

				/*
				 * WEITERE LAYOUTS nach demselben Muster ergänzen:
				 * 'layout_ablauf'  => Erster Besuch (Steps-Repeater)
				 * 'layout_praxis'  => Galerie (ACF gallery)
				 * 'layout_team'    => Behandler (Repeater: Foto, Name, Rolle, Text)
				 * 'layout_eltern'  => Für Eltern (Repeater)
				 * 'layout_stimmen' => Kundenstimmen (Repeater)
				 * 'layout_faq'     => FAQ (Repeater: Frage, Antwort)
				 * 'layout_termin'  => Termin (QR-Bild + Buchungs-Embed-Code)
				 * 'layout_kontakt' => Kontakt (Formular-Shortcode, z. B. CF7)
				 */
			],
		] ],
	] );
} );

/** Kleiner Helfer für simple Felder. */
function kc_field( $name, $label, $type ) {
	return [
		'key'   => 'field_kc_' . $name,
		'label' => $label,
		'name'  => $name,
		'type'  => $type,
	];
}
