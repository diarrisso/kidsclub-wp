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
									kc_bg_field( 'hero' ),
									...kc_bg_color_field( 'hero' ),
									...kc_bg_settings_fields( 'hero' ),
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
											'Querformat-Video für Desktop (≤ 15 Sek., max. 20 MB). Leer = spray-quer.mp4.',
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
									kc_bg_field( 'willkommen' ),
									...kc_bg_color_field( 'willkommen' ),
									...kc_bg_settings_fields( 'willkommen' ),
									[
										'key'          => 'field_kc_wk_text',
										'label'        => 'Text',
										'name'         => 'text',
										'type'         => 'wysiwyg',
										'media_upload' => 0,
										'tabs'         => 'visual',
										'toolbar'      => 'basic',
										'instructions' =>
											'Zentrierter Intro-Absatz. „Herzlich Willkommen!“ fett für die Magenta-Hervorhebung.',
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
									kc_bg_field( 'leistungen' ),
									kc_bg_spray_field( 'leistungen' ),
									...kc_bg_color_field( 'leistungen' ),
									...kc_bg_settings_fields( 'leistungen' ),
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
											kc_field(
												'body',
												'Beschreibung',
												'textarea',
											),
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
									kc_bg_field( 'zimmer' ),
									...kc_bg_color_field( 'zimmer' ),
									...kc_bg_settings_fields( 'zimmer' ),
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
									kc_bg_field( 'ablauf' ),
									...kc_bg_color_field( 'ablauf' ),
									...kc_bg_settings_fields( 'ablauf' ),
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

							/* ---------- GALERIE ---------- */
							'layout_galerie'    => [
								'key'        => 'layout_galerie',
								'name'       => 'galerie',
								'label'      => 'Galerie',
								'display'    => 'block',
								'sub_fields' => [
									kc_bg_field( 'galerie' ),
									...kc_bg_color_field( 'galerie' ),
									...kc_bg_settings_fields( 'galerie' ),
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
									kc_bg_field( 'team' ),
									kc_bg_spray_field( 'team' ),
									...kc_bg_color_field( 'team' ),
									...kc_bg_settings_fields( 'team' ),
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
									kc_bg_field( 'eltern' ),
									...kc_bg_color_field( 'eltern' ),
									...kc_bg_settings_fields( 'eltern' ),
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
									kc_bg_field( 'stimmen' ),
									kc_bg_spray_field( 'stimmen' ),
									...kc_bg_color_field( 'stimmen' ),
									...kc_bg_settings_fields( 'stimmen' ),
									kc_field( 'st_eyebrow', 'Eyebrow', 'text' ),
									kc_field( 'st_title', 'Überschrift', 'text' ),
									kc_field( 'st_text', 'Einleitung', 'textarea' ),
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
									kc_bg_field( 'faq' ),
									kc_bg_spray_field( 'faq' ),
									...kc_bg_color_field( 'faq' ),
									...kc_bg_settings_fields( 'faq' ),
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
									kc_bg_field( 'termin' ),
									kc_bg_spray_field( 'termin' ),
									...kc_bg_color_field( 'termin' ),
									...kc_bg_settings_fields( 'termin' ),
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
									kc_bg_field( 'kontakt' ),
									...kc_bg_color_field( 'kontakt' ),
									...kc_bg_settings_fields( 'kontakt' ),
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

/** Optionales Hintergrundbild pro Sektion. Eindeutiger Key je Layout. */
function kc_bg_field( $layout ) {
	return [
		'key'           => 'field_kc_bg_' . $layout,
		'label'         => 'Hintergrundbild (optional)',
		'name'          => 'background_image',
		'type'          => 'image',
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'instructions'  => 'Optional. Leer lassen = CSS-Standard oder Spray-Voreinstellung nutzen.',
	];
}

/** Voreingestellte Spray-Dekoration (Theme-Asset, kein Upload nötig). */
function kc_bg_spray_field( $layout ) {
	return [
		'key'               => 'field_kc_spray_' . $layout,
		'label'             => 'Spray-Dekoration (voreingestellt)',
		'name'              => 'bg_spray_preset',
		'type'              => 'select',
		'choices'           => [
			''       => '— Keiner —',
			'Spray1' => 'Spray 1',
			'Spray2' => 'Spray 2',
			'Spray3' => 'Spray 3',
			'Spray4' => 'Spray 4',
			'Spray5' => 'Spray 5',
			'Spray6' => 'Spray 6',
		],
		'default_value'     => '',
		'allow_null'        => false,
		'instructions'      => 'Wähle einen voreingestellten Spray. Wird ignoriert wenn oben ein eigenes Bild gesetzt ist.',
		'conditional_logic' => [ [ [ 'field' => 'field_kc_bg_' . $layout, 'operator' => '==empty' ] ] ],
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
				''         => '— Standard (CSS) —',
				'#FFFFFF'  => '⬜ Weiß',
				'#EFF2F0'  => '🟩 Vert-gris (Band)',
				'#FBB9C4'  => '🌸 Rose poudré',
				'#F0F2F5'  => '🔲 Gris-bleu',
				'#FAECBF'  => '🟡 Crème',
				'#FDF0EB'  => '🍑 Pêche',
				'#D4DED7'  => '🌿 Sauge',
				'#DEDBDF'  => '💜 Lilas',
				'#BCC9D2'  => '🩶 Bleu-gris',
				'#102E79'  => '🟦 Navy',
				'#EA4589'  => '🌸 Rose vif',
				'#D52E72'  => '🌺 Rose profond',
				'custom'   => '🎨 Personnalisée…',
			],
			'default_value' => '',
			'allow_null'    => false,
			'instructions'  => 'Choisir une couleur de la palette ou "Personnalisée" pour saisir un code hex.',
		],
		[
			'key'               => 'field_kc_bgc_' . $layout,
			'label'             => 'Couleur personnalisée (hex)',
			'name'              => 'background_color',
			'type'              => 'color_picker',
			'default_value'     => '',
			'conditional_logic' => [ [ [ 'field' => $preset_key, 'operator' => '==', 'value' => 'custom' ] ] ],
			'instructions'      => 'Visible uniquement si "Personnalisée" est sélectionné ci-dessus.',
		],
	];
}

/** Darstellungs-Einstellungen fürs Hintergrundbild pro Section. */
function kc_bg_settings_fields( $layout ) {
	$img_key   = 'field_kc_bg_' . $layout;
	$show_cond = [ [ [ 'field' => $img_key, 'operator' => '!=empty' ] ] ];

	return [
		[
			'key'               => 'field_kc_bgopa_' . $layout,
			'label'             => 'Deckkraft des Hintergrundbilds (%)',
			'name'              => 'bg_opacity',
			'type'              => 'range',
			'default_value'     => 60,
			'min'               => 0,
			'max'               => 100,
			'step'              => 1,
			'append'            => '%',
			'instructions'      => 'Höher = Bild stärker sichtbar.',
			'conditional_logic' => $show_cond,
		],
		[
			'key'               => 'field_kc_bgsize_' . $layout,
			'label'             => 'Bildgröße',
			'name'              => 'bg_size',
			'type'              => 'select',
			'choices'           => [
				'115%'    => 'Standard (115%)',
				'cover'   => 'Füllend (cover)',
				'contain' => 'Einpassend (contain)',
				'auto'    => 'Originalgröße (auto)',
			],
			'default_value'     => '115%',
			'conditional_logic' => $show_cond,
		],
		[
			'key'               => 'field_kc_bgpos_' . $layout,
			'label'             => 'Bildposition',
			'name'              => 'bg_position',
			'type'              => 'select',
			'choices'           => [
				'center top'    => 'Oben mittig',
				'center'        => 'Mittig',
				'center bottom' => 'Unten mittig',
				'left center'   => 'Links',
				'right center'  => 'Rechts',
			],
			'default_value'     => 'center top',
			'conditional_logic' => $show_cond,
		],
	];
}
