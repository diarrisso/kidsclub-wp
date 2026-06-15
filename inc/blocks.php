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
											'image' => 'Bild (Standard)',
											'video' =>
												'Video (cinématique — Willkommen-Reveal)',
										],
									],
									[
										'key'           => 'field_kc_hero_video',
										'label'         => 'Video-Datei (.mp4)',
										'name'          => 'hero_video',
										'type'          => 'file',
										'return_format' => 'array',
										'mime_types'    => 'mp4',
										'instructions'  =>
											'Kurzes Intro-Video (≤ 15 Sek., max. 20 MB). Wird vor dem Willkommen-Text abgespielt.',
										'conditional_logic' => [
											[
												[
													'field'    =>
														'field_kc_hero_media_type',
													'operator' => '==',
													'value'    => 'video',
												],
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

							/* ---------- LEISTUNGEN ---------- */
							'layout_leistungen' => [
								'key'        => 'layout_leistungen',
								'name'       => 'leistungen',
								'label'      => 'Leistungsspektrum',
								'display'    => 'block',
								'sub_fields' => [
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
												'key'     => 'field_kc_ls_symbol',
												'label'   => 'Symbol',
												'name'    => 'symbol',
												'type'    => 'select',
												'choices' => [
													'symbol1' => 'Symbol 1',
													'symbol2' => 'Symbol 2',
													'symbol3' => 'Symbol 3',
													'symbol4' => 'Symbol 4',
													'symbol5' => 'Symbol 5',
												],
												'default_value' => 'symbol1',
											],
											kc_field( 'heading', 'Titel', 'text' ),
											kc_field( 'body', 'Beschreibung', 'textarea' ),
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
													'g' => 'Grün',
													'y' => 'Gelb',
													'o' => 'Orange',
													'b' => 'Blau',
													'l' => 'Lila',
												],
											],
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
									kc_field( 'abl_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'abl_title', 'Überschrift', 'text' ),
									kc_field( 'abl_text', 'Einleitung', 'textarea' ),
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

							/* ---------- PRAXIS ---------- */
							'layout_praxis'     => [
								'key'        => 'layout_praxis',
								'name'       => 'praxis',
								'label'      => 'Praxis-Galerie',
								'display'    => 'block',
								'sub_fields' => [
									kc_field( 'prx_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'prx_title', 'Überschrift', 'text' ),
									[
										'key'     => 'field_kc_prx_hinweis',
										'label'   => 'Fotos & Kategorien',
										'type'    => 'message',
										'message' => 'Die Fotos werden im Menü <strong>Praxis-Galerie</strong> gepflegt (Foto = Beitragsbild, Filter-Chips = Bereiche, Reihenfolge = Attribut Reihenfolge).',
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
									kc_field( 'tm_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'tm_title', 'Überschrift', 'text' ),
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
									kc_field( 'st_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'st_title', 'Überschrift', 'text' ),
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
									kc_field( 'fq_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'fq_title', 'Überschrift', 'text' ),
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
									kc_field( 'tr_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'tr_title', 'Überschrift', 'text' ),
									kc_field( 'tr_text', 'Text', 'textarea' ),
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

							/* ---------- KONTAKT ---------- */
							'layout_kontakt'    => [
								'key'        => 'layout_kontakt',
								'name'       => 'kontakt',
								'label'      => 'Kontakt',
								'display'    => 'block',
								'sub_fields' => [
									kc_field( 'kt_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'kt_title', 'Überschrift', 'text' ),
									kc_field( 'kt_text', 'Text', 'textarea' ),
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
