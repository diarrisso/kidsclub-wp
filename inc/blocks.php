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
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'      => 'group_kidsclub_landing',
				'title'    => 'Kids Club — Seiteninhalt',
				'location' => [
					[
						[
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-landing.php',
						],
					],
				],
				'fields'   => [
					[
						'key'          => 'field_kc_sections',
						'label'        => 'Sektionen',
						'name'         => 'sections',
						'type'         => 'flexible_content',
						'button_label' => 'Sektion hinzufügen',
						'layouts'      => [
							/* ---------- HERO ---------- */
							'layout_hero'       => [
								'key'        => 'layout_hero',
								'name'       => 'hero',
								'label'      => 'Hero (Banner)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'hero' ),
									...kc_bg_color_field( 'hero' ),
									kc_field( 'hero_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'hero_title', 'Überschrift', 'text' ),
									kc_field(
										'hero_highlight',
										'Hervorgehobener Teil',
										'text',
									),
									kc_field( 'hero_text', 'Text', 'textarea' ),
									kc_field(
										'hero_bg',
										'Hintergrundbild / Video-Poster',
										'image',
									),
									[
										'key'           => 'field_kc_hero_media_type',
										'label'         => 'Medientyp',
										'name'          => 'hero_media_type',
										'type'          => 'select',
										'default_value' => 'image',
										'choices'       => [
											'image'        => 'Bild (Standard)',
											'video'        => 'Video (cinématique — Willkommen-Reveal)',
											'video_slider' => 'Video-Slider (mehrere Videos, Autoplay-Sequenz)',
										],
									],
									[
										'key'           => 'field_kc_hero_video',
										'label'         => 'Video Desktop (.mp4)',
										'name'          => 'hero_video',
										'type'          => 'file',
										'return_format' => 'array',
										'mime_types'    => 'mp4',
										'instructions'  =>
											'Querformat-Video für Desktop (≤ 20 Sek., max. 3 MB). Leer = hero-01.mp4 aus dem Theme. Bitte 1280 px breit und ohne Tonspur exportieren — das Video lädt bei jedem Seitenaufruf automatisch.',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_hero_media_type',
													'operator' => '==',
													'value'    => 'video',
												],
											],
										],
									],
									[
										'key'           => 'field_kc_hero_video_mobile',
										'label'         => 'Video Mobil (.mp4)',
										'name'          => 'hero_video_mobile',
										'type'          => 'file',
										'return_format' => 'array',
										'mime_types'    => 'mp4',
										'instructions'  =>
											'Hochformat-Video für Mobilgeräte (≤ 768 px). Leer = spray-hoch.mp4 wenn vorhanden.',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_hero_media_type',
													'operator' => '==',
													'value'    => 'video',
												],
											],
										],
									],
									[
										'key'          => 'field_kc_hero_video_slides',
										'label'        => 'Video-Slides',
										'name'         => 'hero_video_slides',
										'type'         => 'repeater',
										'instructions' => '2–5 Videos hinzufügen. Jedes Video spielt automatisch ab und wechselt zum nächsten.',
										'min'          => 2,
										'max'          => 5,
										'button_label' => 'Video hinzufügen',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_hero_media_type',
													'operator' => '==',
													'value'    => 'video_slider',
												],
											],
										],
										'sub_fields'   => [
											[
												'key'   => 'field_kc_hero_slide_video',
												'label' => 'Video (.mp4)',
												'name'  => 'slide_video',
												'type'  => 'file',
												'return_format' => 'array',
												'mime_types' => 'mp4',
											],
											[
												'key'   => 'field_kc_hero_slide_poster',
												'label' => 'Poster-Bild (optional)',
												'name'  => 'slide_poster',
												'type'  => 'image',
												'return_format' => 'array',
												'instructions' => 'Wird angezeigt bevor das Video lädt und auf Mobilgeräten.',
											],
										],
									],
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
										'key'           => 'field_kc_hero_show_kids',
										'label'         => 'Illustrierte Kinder anzeigen',
										'name'          => 'hero_show_kids',
										'type'          => 'true_false',
										'ui'            => 1,
										'default_value' => 1,
										'instructions'  =>
											'Aus, wenn ein eigenes Foto/Video als Hintergrund genutzt wird.',
									],
								],
							],

							/* ---------- WILLKOMMEN (Intro) ---------- */
							'layout_willkommen' => [
								'key'        => 'layout_willkommen',
								'name'       => 'willkommen',
								'label'      => 'Willkommen (Intro)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'willkommen' ),
									...kc_bg_color_field( 'willkommen' ),
									[
										'key'           => 'field_kc_wk_style',
										'label'         => 'Darstellung',
										'name'          => 'wk_style',
										'type'          => 'select',
										'choices'       => [
											'klassisch' => 'Klassisch (ein zentrierter Textblock)',
											'editorial' => 'Editorial (Auftakt, zwei Spalten, Zitat-Bande)',
										],
										'default_value' => 'klassisch',
										'instructions'  => 'Bei „Editorial“ werden die Felder darunter genutzt; „Klassisch“ nutzt nur das Textfeld ganz unten.',
									],
									[
										'key'   => 'field_kc_wk_eyebrow',
										'label' => 'Eyebrow',
										'name'  => 'wk_eyebrow',
										'type'  => 'text',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_title',
										'label' => 'Überschrift',
										'name'  => 'wk_title',
										'type'  => 'text',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'          => 'field_kc_wk_title_hl',
										'label'        => 'Überschrift — hervorgehobener Teil',
										'name'         => 'wk_title_hl',
										'type'         => 'text',
										'instructions' => 'Steht magenta in einer zweiten Zeile, z. B. „ZACP Kids Club“.',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_lead',
										'label' => 'Auftakt (erster Absatz)',
										'name'  => 'wk_lead',
										'type'  => 'textarea',
										'rows'  => 3,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_col1',
										'label' => 'Spalte links',
										'name'  => 'wk_col1',
										'type'  => 'textarea',
										'rows'  => 4,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_col2',
										'label' => 'Spalte rechts',
										'name'  => 'wk_col2',
										'type'  => 'textarea',
										'rows'  => 4,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_motto_text',
										'label' => 'Zitat-Bande — Einleitung',
										'name'  => 'wk_motto_text',
										'type'  => 'textarea',
										'rows'  => 3,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_motto_kicker',
										'label' => 'Zitat-Bande — Vorspann (magenta)',
										'name'  => 'wk_motto_kicker',
										'type'  => 'text',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_motto_line',
										'label' => 'Zitat-Bande — Motto (groß)',
										'name'  => 'wk_motto_line',
										'type'  => 'textarea',
										'rows'  => 2,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'           => 'field_kc_wk_motto_spray',
										'label'         => 'Zitat-Bande — Spray-Grafik',
										'name'          => 'wk_motto_spray',
										'type'          => 'select',
										'choices'       => kc_spray_choices(),
										'default_value' => 'Spray7',
										'instructions'  => 'Hintergrundbild der Bande über die volle Breite. „— Keiner —“ = weiß.',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'   => 'field_kc_wk_outro',
										'label' => 'Schlussabsatz',
										'name'  => 'wk_outro',
										'type'  => 'textarea',
										'rows'  => 3,
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'editorial',
												],
											],
										],
									],
									[
										'key'          => 'field_kc_wk_text',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_wk_style',
													'operator' => '==',
													'value'    => 'klassisch',
												],
											],
										],
										'label'        => 'Text (klassisch)',
										'name'         => 'text',
										'type'         => 'wysiwyg',
										'media_upload' => 0,
										'tabs'         => 'visual',
										'toolbar'      => 'basic',
										'instructions' =>
											'Nur bei Darstellung „Klassisch“. Zentrierter Intro-Absatz.',
									],
								],
							],

							/* ---------- ANGSTPATIENTEN (Vergleichs-Karten) ---------- */
							'layout_angst'      => [
								'key'        => 'layout_angst',
								'name'       => 'angst',
								'label'      => 'Angstpatienten (Vergleich)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'angst' ),
									...kc_bg_color_field( 'angst' ),
									[
										'key'          => 'field_kc_ag_anchor',
										'label'        => 'Anker-ID',
										'name'         => 'ag_anchor',
										'type'         => 'text',
										'instructions' => 'Ohne Raute, z. B. „angst“ — darauf verweist die Karte „AngstpatientInnen“ in den Leistungen.',
									],
									kc_field( 'ag_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'ag_title', 'Überschrift', 'text' ),
									kc_field( 'ag_intro', 'Einleitung', 'textarea' ),
									kc_field( 'ag_gruende_title', 'Gründe — Überschrift', 'text' ),
									kc_field( 'ag_gruende_intro', 'Gründe — Einleitung', 'textarea' ),
									[
										'key'          => 'field_kc_ag_gruende',
										'label'        => 'Gründe (Chips)',
										'name'         => 'ag_gruende',
										'type'         => 'repeater',
										'layout'       => 'table',
										'button_label' => 'Grund hinzufügen',
										'sub_fields'   => [ kc_field( 'ag_grund', 'Grund', 'text' ) ],
									],
									kc_field( 'ag_gruende_after', 'Text nach den Chips', 'textarea' ),
									kc_field( 'ag_compare_title', 'Vergleich — Überschrift', 'text' ),
									[
										'key'          => 'field_kc_ag_cards',
										'label'        => 'Vergleichs-Karten',
										'name'         => 'ag_cards',
										'type'         => 'repeater',
										'layout'       => 'block',
										'max'          => 2,
										'button_label' => 'Karte hinzufügen',
										'instructions' => 'Zwei Karten stehen nebeneinander und enden auf gleicher Höhe.',
										'sub_fields'   => [
											[
												'key'     => 'field_kc_ag_card_color',
												'label'   => 'Kartenfarbe',
												'name'    => 'ag_card_color',
												'type'    => 'select',
												'choices' => [
													'pink' => 'Rosa',
													'blue' => 'Blau',
													'green' => 'Grün',
													'yellow' => 'Gelb',
												],
												'default_value' => 'pink',
											],
											kc_field( 'ag_card_heading', 'Titel', 'text' ),
											[
												'key'     => 'field_kc_ag_card_body',
												'label'   => 'Inhalt',
												'name'    => 'ag_card_body',
												'type'    => 'wysiwyg',
												'media_upload' => 0,
												'tabs'    => 'visual',
												'toolbar' => 'basic',
												'instructions' => 'Aufzählungen erscheinen als Häkchen-Liste, Fettungen als Zwischenüberschrift.',
											],
											[
												'key'   => 'field_kc_ag_card_foot',
												'label' => 'Schlusssatz (unten in der Karte)',
												'name'  => 'ag_card_foot',
												'type'  => 'textarea',
												'rows'  => 3,
												'instructions' => 'Wird nach unten gedrückt, damit beide Karten bündig abschließen.',
											],
										],
									],
									kc_field( 'ag_usp_title', 'Merkmale — Überschrift', 'text' ),
									[
										'key'          => 'field_kc_ag_usp',
										'label'        => 'Merkmale',
										'name'         => 'ag_usp',
										'type'         => 'repeater',
										'layout'       => 'table',
										'max'          => 5,
										'button_label' => 'Merkmal hinzufügen',
										'sub_fields'   => [ kc_field( 'ag_usp_text', 'Merkmal', 'text' ) ],
									],
									kc_field( 'ag_closing', 'Schlussabsatz', 'textarea' ),
									kc_field( 'ag_cta_title', 'Abschluss-Bande — Überschrift', 'text' ),
									kc_field( 'ag_cta_text', 'Abschluss-Bande — Text', 'textarea' ),
									[
										'key'           => 'field_kc_ag_cta_spray',
										'label'         => 'Abschluss-Bande — Spray-Grafik',
										'name'          => 'ag_cta_spray',
										'type'          => 'select',
										'choices'       => kc_spray_choices(),
										'default_value' => 'Spray8',
										'instructions'  => 'Hintergrundbild über die volle Breite. „— Keiner —“ = weiß.',
									],
								],
							],

							/* ---------- TEXTBLOCK (générique, réutilisable) ---------- */
							'layout_textblock'  => [
								'key'        => 'layout_textblock',
								'name'       => 'textblock',
								'label'      => 'Textblock (freier Text)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'textblock' ),
									...kc_bg_color_field( 'textblock' ),
									[
										'key'          => 'field_kc_tb_anchor',
										'label'        => 'Anker-ID',
										'name'         => 'tb_anchor',
										'type'         => 'text',
										'instructions' => 'Optional. Ohne Raute, z. B. „angst“ — dieser Abschnitt ist dann über den Link #angst erreichbar.',
									],
									kc_field( 'tb_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'tb_title', 'Überschrift', 'text' ),
									[
										'key'           => 'field_kc_tb_style',
										'label'         => 'Darstellung',
										'name'          => 'tb_style',
										'type'          => 'select',
										'choices'       => [
											'fliesstext' => 'Fließtext (schlicht, zentriert)',
											'karte'      => 'Karte (Pastellfläche mit Schatten)',
										],
										'default_value' => 'fliesstext',
									],
									[
										'key'           => 'field_kc_tb_card_color',
										'label'         => 'Kartenfarbe',
										'name'          => 'tb_card_color',
										'type'          => 'select',
										'choices'       => [
											'yellow' => 'Gelb',
											'blue'   => 'Blau',
											'green'  => 'Grün',
											'pink'   => 'Rosa',
										],
										'default_value' => 'pink',
										'conditional_logic' => [
											[
												[
													'field'    => 'field_kc_tb_style',
													'operator' => '==',
													'value'    => 'karte',
												],
											],
										],
									],
									[
										'key'          => 'field_kc_tb_content',
										'label'        => 'Inhalt',
										'name'         => 'tb_content',
										'type'         => 'wysiwyg',
										'media_upload' => 0,
										'tabs'         => 'visual',
										'toolbar'      => 'full',
										'instructions' => 'Zwischenüberschriften (H3), Aufzählungen und Fettungen sind erlaubt.',
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
									...kc_bg_spray_field( 'leistungen' ),
									...kc_bg_color_field( 'leistungen' ),
									[
										'key'   => 'field_kc_ls_eyebrow',
										'label' => 'Eyebrow',
										'name'  => 'eyebrow',
										'type'  => 'text',
									],
									[
										'key'   => 'field_kc_ls_title',
										'label' => 'Überschrift',
										'name'  => 'title',
										'type'  => 'text',
									],
									[
										'key'   => 'field_kc_ls_text',
										'label' => 'Einleitung',
										'name'  => 'text',
										'type'  => 'textarea',
									],
									[
										'key'           => 'field_kc_ls_accordion_title',
										'label'         => 'Akkordeon-Titel (weitere Leistungen)',
										'name'          => 'accordion_title',
										'type'          => 'text',
										'default_value' => 'Moderne Behandlung',
										'instructions'  => 'Erscheint nur, wenn mehr als 4 Leistungen erfasst sind: ab der 5. Leistung werden sie als Akkordeon unter den 4 Karten angezeigt statt als weitere Karte.',
									],
									[
										'key'           => 'field_kc_ls_ov_duration',
										'label'         => 'Overlay-Animation: Dauer (ms)',
										'name'          => 'overlay_transition',
										'type'          => 'number',
										'default_value' => 1050,
										'min'           => 200,
										'max'           => 3000,
										'step'          => 50,
										'append'        => 'ms',
										'instructions'  => 'Dauer der Ein-/Ausblend-Animation der „mehr“-Overlays (Gleiten von rechts). Standard: 1050 ms – höher = langsamer/sanfter.',
									],
									[
										'key'          => 'field_kc_leistungen_items',
										'label'        => 'Leistungen',
										'name'         => 'items',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Leistung hinzufügen',
										'sub_fields'   => [
											[
												'key'     => 'field_kc_ls_card_color',
												'label'   => 'Kartenfarbe',
												'name'    => 'card_color',
												'type'    => 'select',
												'choices' => [
													'yellow' => 'Gelb (Putzschule)',
													'blue' => 'Blau (Prophylaxe)',
													'green' => 'Grün (Behandlung)',
													'pink' => 'Rosa (Angst)',
												],
												'default_value' => 'yellow',
											],
											[
												'key'     => 'field_kc_ls_card_icon',
												'label'   => 'Karten-Icon (Linie, oben rechts)',
												'name'    => 'card_icon',
												'type'    => 'select',
												'choices' => [
													'symbol5' => 'Zahn mit Gesicht (Spray)',
													'symbol7' => 'Zahn mit Gesicht 2 (Spray)',
													'symbol1' => 'Zahn + Bürste (Spray)',
													'symbol2' => 'Gebiss (Spray)',
													'symbol8' => 'Gebiss 2 (Spray)',
													'symbol3' => 'Zahnpasta (Spray)',
													'symbol4' => 'Herz (Spray)',
													'symbol9' => 'Herz 2 (Spray)',
													'symbol6' => 'Smiley (Spray)',
													'zahn' => 'Zahn (Linie)',
													'buerste' => 'Zahnbürste (Linie)',
													'smiley' => 'Smiley (Linie)',
													'gebiss' => 'Gebiss (Linie)',
													'herz' => 'Herz (Linie)',
												],
												[
													'key'  => 'field_kc_ls_card_icon_custom',
													'label' => 'Eigenes Karten-Icon (Upload)',
													'name' => 'card_icon_custom',
													'type' => 'image',
													'return_format' => 'array',
													'mime_types' => 'svg,png,webp',
													'instructions' => 'Überschreibt die Auswahl oben. Wird einfarbig weiß dargestellt.',
												],
												'default_value' => 'symbol5',
												'allow_null' => 1,
												'instructions' => 'Weißes Linien-Icon oben rechts auf der Karte (neues Design).',
											],
											[
												'key'     => 'field_kc_ls_symbol',
												'label'   => 'Symbol',
												'name'    => 'symbol',
												'type'    => 'select',
												'choices' => [
													'symbol1' => 'Symbol 1 (Zahn + Bürste, pink)',
													'symbol2' => 'Symbol 2 (Gebiss, navy)',
													'symbol3' => 'Symbol 3 (Zahnpasta, navy)',
													'symbol4' => 'Symbol 4 (Herz, pink)',
													'symbol5' => 'Symbol 5 (Zahn-Gesicht, pink)',
													'symbol6' => 'Symbol 6 (Smiley, pink)',
													'symbol7' => 'Symbol 7 (Zahn-Gesicht, navy)',
													'symbol8' => 'Symbol 8 (Gebiss, navy)',
													'symbol9' => 'Symbol 9 (Herz, pink)',
												],
												'default_value' => 'symbol1',
											],
											kc_field( 'heading', 'Titel', 'text' ),
											// Ex-textarea. La clé 'field_kc_body' NE DOIT PAS changer :
											// c'est celle que kc_field( 'body', … ) produisait — la modifier
											// perdrait tout le contenu déjà saisi.
											[
												'key'     => 'field_kc_body',
												'label'   => 'Beschreibung',
												'name'    => 'body',
												'type'    => 'wysiwyg',
												'media_upload' => 0,
												'tabs'    => 'visual',
												'toolbar' => 'basic',
												'instructions' => 'Aufzählungen und Links sind erlaubt. Bestehende Texte bleiben unverändert.',
											],

											/* ---------- „mehr“-Overlay: Detailinhalt (Vollbild, gleitet von rechts) ---------- */
											[
												'key'   => 'field_kc_ls_ov_enabled',
												'label' => '„mehr“-Overlay anzeigen',
												'name'  => 'overlay_enabled',
												'type'  => 'true_false',
												'ui'    => 1,
												'default_value' => 0,
												'instructions' => 'Blendet den „mehr“-Button und das Vollbild-Overlay dieser Karte ein. Ohne Inhalt bitte aus lassen.',
											],
											[
												'key'   => 'field_kc_ls_ov_button',
												'label' => 'Button-Text',
												'name'  => 'overlay_button',
												'type'  => 'text',
												'default_value' => 'mehr',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
											],
											[
												'key'   => 'field_kc_ls_ov_title',
												'label' => 'Overlay-Titel (optional)',
												'name'  => 'overlay_title',
												'type'  => 'text',
												'instructions' => 'Leer lassen = der Karten-Titel wird verwendet.',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
											],
											[
												'key'     => 'field_kc_ls_ov_intro',
												'label'   => 'Overlay-Einleitung',
												'name'    => 'overlay_intro',
												'type'    => 'wysiwyg',
												'media_upload' => 0,
												'tabs'    => 'visual',
												'toolbar' => 'basic',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
											],
											[
												'key'    => 'field_kc_ls_ov_slides',
												'label'  => 'Overlay-Bilder (Slider)',
												'name'   => 'overlay_slides',
												'type'   => 'repeater',
												'layout' => 'block',
												'button_label' => 'Bild hinzufügen',
												'instructions' => 'Bilder zu dieser Leistung — Räume, Geräte, Team bei der Arbeit. Erscheinen als Slider unter der Einleitung. Leer lassen = kein Slider. Ab zwei Bildern erscheinen Pfeile und Punkte; ein einzelnes Bild wird einfach angezeigt.',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
												'sub_fields' => [
													[
														'key'           => 'field_kc_ls_ov_slide_img',
														'label'         => 'Bild',
														'name'          => 'image',
														'type'          => 'image',
														'return_format' => 'array',
														'preview_size'  => 'medium',
														'mime_types'    => 'jpg,jpeg,png,webp',
														'instructions'  => 'Querformat empfohlen (16:10). Bitte im Medienbereich einen Alt-Text pflegen — er wird für Screenreader übernommen.',
													],
													[
														'key'          => 'field_kc_ls_ov_slide_cap',
														'label'        => 'Bildunterschrift',
														'name'         => 'caption',
														'type'         => 'text',
														'instructions' => 'Kurzer Text unter dem Bild. Leer lassen = Bild ohne Unterschrift.',
													],
												],
											],
											[
												'key'           => 'field_kc_ls_ov_slides_pos',
												'label'         => 'Position des Sliders',
												'name'          => 'overlay_slides_position',
												'type'          => 'select',
												'choices'       => [
													'mitte' => 'Nach den ersten beiden Textblöcken (Vorgabe)',
													'oben'  => 'Über dem Text — direkt nach der Einleitung',
													'unten' => 'Unter dem Text — am Ende des Overlays',
												],
												'default_value' => 'mitte',
												'instructions'  => 'Wirkt nur, wenn oben Bilder hinterlegt sind. Standard ist „nach den ersten beiden Textblöcken“ — der Slider unterbricht dann den Text, statt ihn anzukündigen oder abzuschließen.',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
											],
											[
												'key'    => 'field_kc_ls_ov_sections',
												'label'  => 'Overlay-Abschnitte',
												'name'   => 'overlay_sections',
												'type'   => 'repeater',
												'layout' => 'block',
												'button_label' => 'Abschnitt hinzufügen',
												'instructions' => 'Werden im Overlay in zwei Spalten angezeigt.',
												'conditional_logic' => [
													[
														[
															'field'    => 'field_kc_ls_ov_enabled',
															'operator' => '==',
															'value'    => '1',
														],
													],
												],
												'sub_fields' => [
													/* Die beiden Icon-Felder sind entfallen: das Symbol steht bereits auf der Karte,
														im Overlay war es reine Doppelung. „Eigenes Icon (Upload)“ war ohnehin nie
														registriert — seine Definition steckte im choices-Array des Auswahlfelds,
														ACF hat sie stillschweigend ignoriert. */
													[
														'key'   => 'field_kc_ls_ov_sec_title',
														'label' => 'Abschnitts-Titel',
														'name'  => 'title',
														'type'  => 'text',
													],
													[
														'key'          => 'field_kc_ls_ov_sec_body',
														'label'        => 'Text',
														'name'         => 'body',
														'type'         => 'wysiwyg',
														'media_upload' => 0,
														'tabs'         => 'visual',
														'toolbar'      => 'basic',
														'instructions' => 'Aufzählungen erlaubt (z. B. Vorteile Lachgas / Vollnarkose).',
													],
												],
											],
										],
									],
								],
							],

							/* ---------- 5 ZIMMER ---------- */
							'layout_zimmer'     => [
								'key'        => 'layout_zimmer',
								'name'       => 'zimmer',
								'label'      => '5 Zimmer',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'zimmer' ),
									...kc_bg_color_field( 'zimmer' ),
									[
										'key'   => 'field_kc_zm_eyebrow',
										'label' => 'Eyebrow',
										'name'  => 'eyebrow',
										'type'  => 'text',
									],
									[
										'key'   => 'field_kc_zm_title',
										'label' => 'Überschrift',
										'name'  => 'title',
										'type'  => 'text',
									],
									[
										'key'   => 'field_kc_zm_text',
										'label' => 'Einleitung',
										'name'  => 'text',
										'type'  => 'textarea',
									],
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
												'key'     => 'field_kc_room_color',
												'label'   => 'Farbe',
												'name'    => 'color',
												'type'    => 'select',
												'choices' => [
													'g' => 'Grün (Wald)',
													'y' => 'Gelb (Sonne)',
													'o' => 'Rosa (Blumen)',
													'b' => 'Blau (Eismeer)',
													'l' => 'Grau (Steine)',
												],
											],
											kc_field( 'beschreibung', 'Beschreibung (Mouseover)', 'textarea' ),
										],
									],
								],
							],

							/* ---------- ABLAUF ---------- */
							'layout_ablauf'     => [
								'key'        => 'layout_ablauf',
								'name'       => 'ablauf',
								'label'      => 'Erster Besuch (Ablauf)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'ablauf' ),
									...kc_bg_color_field( 'ablauf' ),
									kc_field( 'abl_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'abl_title', 'Überschrift', 'text' ),
									kc_field( 'abl_text', 'Einleitung', 'textarea' ),
									[
										'key'           => 'field_kc_abl_display',
										'label'         => 'Darstellung',
										'name'          => 'display_style',
										'type'          => 'select',
										'choices'       => [
											'grid'      => 'Kompakt (Raster wie PDF, alle 4 Karten immer sichtbar)',
											'accordion' => 'Standard (Akkordeon, eine Karte auf einmal)',
										],
										'default_value' => 'grid',
									],
									[
										'key'          => 'field_kc_abl_items',
										'label'        => 'Schritte',
										'name'         => 'items',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Schritt hinzufügen',
										'sub_fields'   => [
											kc_field( 'abl_nr', 'Nummer', 'text' ),
											kc_field( 'abl_heading', 'Titel', 'text' ),
											kc_field(
												'abl_body',
												'Beschreibung',
												'textarea',
											),
										],
									],
								],
							],

							/* ---------- GALERIE ---------- */
							'layout_galerie'    => [
								'key'        => 'layout_galerie',
								'name'       => 'galerie',
								'label'      => 'Galerie',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'galerie' ),
									...kc_bg_color_field( 'galerie' ),
									kc_field( 'gl_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'gl_title', 'Titel', 'text' ),
									kc_field( 'gl_text', 'Einleitung', 'textarea' ),
									[
										'key'           => 'field_kc_gl_photos',
										'name'          => 'gl_photos',
										'label'         => 'Fotos',
										'instructions'  => 'Mehrere Fotos auf einmal auswählen möglich.',
										'type'          => 'gallery',
										'return_format' => 'array',
										'preview_size'  => 'medium',
										'insert'        => 'append',
										'library'       => 'all',
										'min'           => 0,
										'max'           => 0,
										'min_width'     => '',
										'min_height'    => '',
										'min_size'      => '',
										'max_width'     => '',
										'max_height'    => '',
										'max_size'      => '',
										'mime_types'    => '',
									],
								],
							],

							/* ---------- TEAM ---------- */
							'layout_team'       => [
								'key'        => 'layout_team',
								'name'       => 'team',
								'label'      => 'Team / Behandler',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'team' ),
									...kc_bg_color_field( 'team' ),
									kc_field( 'tm_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'tm_title', 'Überschrift', 'text' ),
									kc_field( 'tm_text', 'Einleitung', 'textarea' ),
									[
										'key'          => 'field_kc_tm_members',
										'label'        => 'Teammitglieder',
										'name'         => 'members',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Teammitglied hinzufügen',
										'sub_fields'   => [
											[
												'key'   => 'field_kc_tm_photo',
												'label' => 'Foto',
												'name'  => 'photo',
												'type'  => 'image',
											],
											kc_field( 'tm_name', 'Name', 'text' ),
											kc_field( 'tm_role', 'Rolle', 'text' ),
											kc_field( 'tm_bio', 'Kurztext', 'textarea' ),
										],
									],
								],
							],

							/* ---------- ELTERN ---------- */
							'layout_eltern'     => [
								'key'        => 'layout_eltern',
								'name'       => 'eltern',
								'label'      => 'Für Eltern',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'eltern' ),
									...kc_bg_color_field( 'eltern' ),
									kc_field( 'el_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'el_title', 'Überschrift', 'text' ),
									kc_field( 'el_text', 'Einleitung', 'textarea' ),
									[
										'key'          => 'field_kc_el_items',
										'label'        => 'FAQ-Punkte',
										'name'         => 'items',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Punkt hinzufügen',
										'sub_fields'   => [
											kc_field( 'el_icon', 'Icon-Slug', 'text' ),
											kc_field( 'el_question', 'Frage', 'text' ),
											kc_field(
												'el_answer',
												'Antwort',
												'textarea',
											),
										],
									],
								],
							],

							/* ---------- STIMMEN ---------- */
							'layout_stimmen'    => [
								'key'        => 'layout_stimmen',
								'name'       => 'stimmen',
								'label'      => 'Kundenstimmen',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'stimmen' ),
									...kc_bg_color_field( 'stimmen' ),
									kc_field( 'st_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'st_title', 'Überschrift', 'text' ),
									kc_field( 'st_text', 'Einleitung', 'textarea' ),
									[
										'key'           => 'field_kc_st_autoplay',
										'label'         => 'Automatisch abspielen (Autoplay)',
										'name'          => 'st_autoplay',
										'type'          => 'true_false',
										'ui'            => 1,
										'default_value' => 0,
										'instructions'  => 'Wenn aktiviert, läuft das Karussell automatisch durch (alle 3,5 s, Pause bei Mouseover) und die Pfeile UND Punkte werden ausgeblendet. Wenn deaktiviert (Standard), steuern Besucher manuell über Pfeile und Punkte.',
									],
									[
										'key'          => 'field_kc_st_items',
										'label'        => 'Bewertungen',
										'name'         => 'items',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Bewertung hinzufügen',
										'sub_fields'   => [
											kc_field( 'st_quote', 'Zitat', 'textarea' ),
											kc_field( 'st_name', 'Name', 'text' ),
											kc_field( 'st_role', 'Rolle', 'text' ),
										],
									],
								],
							],

							/* ---------- FAQ ---------- */
							'layout_faq'        => [
								'key'        => 'layout_faq',
								'name'       => 'faq',
								'label'      => 'FAQ',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'faq' ),
									...kc_bg_color_field( 'faq' ),
									kc_field( 'fq_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'fq_title', 'Überschrift', 'text' ),
									kc_field( 'fq_text', 'Einleitung', 'textarea' ),
									[
										'key'          => 'field_kc_fq_items',
										'label'        => 'Fragen & Antworten',
										'name'         => 'items',
										'type'         => 'repeater',
										'layout'       => 'block',
										'button_label' => 'Frage hinzufügen',
										'sub_fields'   => [
											kc_field( 'fq_question', 'Frage', 'text' ),
											kc_field(
												'fq_answer',
												'Antwort',
												'textarea',
											),
										],
									],
								],
							],

							/* ---------- TERMIN ---------- */
							'layout_termin'     => [
								'key'        => 'layout_termin',
								'name'       => 'termin',
								'label'      => 'Termin buchen',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'termin' ),
									...kc_bg_color_field( 'termin' ),
									kc_field( 'tr_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'tr_title', 'Überschrift', 'text' ),
									kc_field( 'tr_text', 'Text', 'textarea' ),
									[
										'key'           => 'field_kc_tr_btn',
										'label'         => 'Button-Beschriftung',
										'name'          => 'tr_button_label',
										'type'          => 'text',
										'default_value' => 'Termin buchen',
										'instructions'  => 'Text des Buttons, der die Online-Terminbuchung öffnet. Leer lassen = „Termin buchen“.',
									],
									[
										'key'   => 'field_kc_tr_qr',
										'label' => 'QR-Code Bild',
										'name'  => 'qr_image',
										'type'  => 'image',
									],
									[
										'key'          => 'field_kc_tr_embed',
										'label'        => 'Buchungs-Embed-Code',
										'name'         => 'embed_code',
										'type'         => 'textarea',
										'instructions' =>
											'Nur Doctolib-iframe (https://…doctolib.de oder .fr). Script-Tags und andere Domains werden aus Sicherheitsgründen blockiert.',
									],
								],
							],

							/* ---------- TRENNER ---------- */
							'layout_trenner'    => [
								'key'        => 'layout_trenner',
								'name'       => 'trenner',
								'label'      => '─── Trenner (Trennlinie)',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_color_field( 'trenner' ),
								],
							],

							/* ---------- KONTAKT ---------- */
							'layout_kontakt'    => [
								'key'        => 'layout_kontakt',
								'name'       => 'kontakt',
								'label'      => 'Kontakt',
								'display'    => 'block',
								'sub_fields' => [
									...kc_bg_spray_field( 'kontakt' ),
									...kc_bg_color_field( 'kontakt' ),
									kc_field( 'kt_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'kt_title', 'Überschrift', 'text' ),
									kc_field( 'kt_text', 'Text', 'textarea' ),
									[
										'key'           => 'field_kc_kt_card_bg',
										'label'         => 'Farbiger Hintergrund (Box)',
										'name'          => 'kt_card_bg',
										'type'          => 'true_false',
										'ui'            => 1,
										'ui_on_text'    => 'Box',
										'ui_off_text'   => 'Schlank',
										'default_value' => 1,
										'instructions'  =>
											'An = Formular in farbiger Box (aktuelles Design). Aus = modernes, schlankes Formular ohne Hintergrundfarbe.',
									],
									[
										'key'          => 'field_kc_kt_shortcode',
										'label'        => 'Formular-Shortcode',
										'name'         => 'form_shortcode',
										'type'         => 'text',
										'instructions' =>
											'z. B. [contact-form-7 id="123" title="Kontakt"]',
									],
								],
							],
						],
					],
				],
			]
		);
	}
);

/** Kleiner Helfer für simple Felder. */
function kc_field( $name, $label, $type ) {
	return [
		'key'   => 'field_kc_' . $name,
		'label' => $label,
		'name'  => $name,
		'type'  => $type,
	];
}

/**
 * Die 8 Spray-Übergangsbänder des Themes — als Auswahl wiederverwendbar.
 * Genutzt von kc_bg_spray_field() (Section-Hintergrund) UND von den Zitat-/Abschluss-Bändern,
 * die eine Spray-Grafik über die volle Breite legen.
 */
function kc_spray_choices( $mit_leer = true ) {
	$choices = [
		'Spray1' => 'Spray 1 (Weiß → Hellgrau)',
		'Spray2' => 'Spray 2 (Hellgrau → Weiß)',
		'Spray3' => 'Spray 3 (Weiß → Salbei)',
		'Spray4' => 'Spray 4 (Salbei → Weiß)',
		'Spray5' => 'Spray 5 (Weiß → Creme)',
		'Spray6' => 'Spray 6 (Creme → Weiß)',
		'Spray7' => 'Spray 7 (Weiß → Rosé)',
		'Spray8' => 'Spray 8 (Rosé → Weiß)',
	];
	return $mit_leer ? [ '' => '— Keiner —' ] + $choices : $choices;
}

/**
 * URL der Spray-Grafik zu einem Preset ('Spray1'…'Spray8'), oder '' wenn keins gewählt ist.
 * Whitelist statt Interpolation: ein freier Wert dürfte nie in einen Dateipfad wandern.
 */
function kc_spray_url( $preset ) {
	$preset = (string) $preset;
	if ( ! array_key_exists( $preset, kc_spray_choices( false ) ) ) {
		return '';
	}
	return get_theme_file_uri( 'assets/img/' . $preset . '.png' );
}

/** Voreingestellte Spray-Dekoration (Theme-Asset, kein Upload nötig). Retourne 2 champs. */
function kc_bg_spray_field( $layout ) {
	$spray_key = 'field_kc_spray_' . $layout;
	return [
		[
			'key'           => $spray_key,
			'label'         => 'Spray-Dekoration (voreingestellt)',
			'name'          => 'bg_spray_preset',
			'type'          => 'select',
			'choices'       => [
				''       => '— Keiner —',
				'Spray1' => 'Spray 1 (Weiß → Hellgrau)',
				'Spray2' => 'Spray 2 (Hellgrau → Weiß)',
				'Spray3' => 'Spray 3 (Weiß → Salbei)',
				'Spray4' => 'Spray 4 (Salbei → Weiß)',
				'Spray5' => 'Spray 5 (Weiß → Creme)',
				'Spray6' => 'Spray 6 (Creme → Weiß)',
				'Spray7' => 'Spray 7 (Weiß → Rosé)',
				'Spray8' => 'Spray 8 (Rosé → Weiß)',
			],
			'default_value' => '',
			'allow_null'    => false,
			'instructions'  => 'Wähle eine voreingestellte Spray-Übergangsbande (erscheint oben in der Sektion, hinter der Überschrift).',
		],
		[
			'key'               => 'field_kc_sprayoff_' . $layout,
			'label'             => 'Spray-Versatz nach oben (px)',
			'name'              => 'bg_spray_offset',
			'type'              => 'number',
			'default_value'     => 0,
			'min'               => 0,
			'max'               => 500,
			'step'              => 10,
			'append'            => 'px',
			'instructions'      => 'Schiebt die Spray-Bande nach oben, damit die weißen Striche hinter der Überschrift sichtbar werden. 0 = Bande beginnt am Sektionsanfang.',
			'conditional_logic' => [
				[
					[
						'field'    => $spray_key,
						'operator' => '!=empty',
					],
				],
			],
		],
	];
}

/** Palette + champ libre pour la couleur de fond de section. Retourne 2 champs. */
function kc_bg_color_field( $layout ) {
	$preset_key = 'field_kc_bgcp_' . $layout;
	return [
		[
			'key'           => $preset_key,
			'label'         => 'Hintergrundfarbe',
			'name'          => 'bg_color_preset',
			'type'          => 'select',
			'choices'       => [
				''        => '— Standard (CSS) —',
				'#FFFFFF' => 'Weiß',
				'#F6F6F6' => 'Hellgrau (Spray 1/2)',
				'#EEF2EF' => 'Salbei hell (Spray 3/4)',
				'#FFFBEE' => 'Creme hell (Spray 5/6)',
				'#F9F2F0' => 'Rosé hell (Spray 7/8)',
				'#EFF2F0' => 'Grüngrau (Band)',
				'#FBB9C4' => 'Puderrosa',
				'#FAECBF' => 'Creme',
				'#D4DED7' => 'Salbei',
				'#BDCCC2' => 'Grün',
				'custom'  => 'Benutzerdefiniert…',
			],
			'default_value' => '',
			'allow_null'    => false,
			'instructions'  => 'Farbe aus der Palette wählen oder „Benutzerdefiniert“ für einen Hex-Code.',
		],
		[
			'key'               => 'field_kc_bgc_' . $layout,
			'label'             => 'Benutzerdefinierte Farbe (Hex)',
			'name'              => 'background_color',
			'type'              => 'color_picker',
			'default_value'     => '',
			'conditional_logic' => [
				[
					[
						'field'    => $preset_key,
						'operator' => '==',
						'value'    => 'custom',
					],
				],
			],
			'instructions'      => 'Nur sichtbar, wenn oben „Benutzerdefiniert“ gewählt ist.',
		],
	];
}
