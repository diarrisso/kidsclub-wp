<?php
/**
 * Kids Club by zacp — Options-Seite für Header & Footer
 * In functions.php:  require get_theme_file_path('inc/options.php');
 *
 * Globale Inhalte (Logo, Navigation, CTA, Footer, Social) zentral im Backend
 * pflegbar – unter "Theme-Einstellungen". Werte via get_field('name','option').
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Options-Seite registrieren */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}
		acf_add_options_page(
			[
				'page_title' => 'Theme-Einstellungen',
				'menu_title' => 'Theme-Einstellungen',
				'menu_slug'  => 'kidsclub-settings',
				'capability' => 'edit_theme_options',
				'icon_url'   => 'dashicons-heart',
				'position'   => 59,
			]
		);
	}
);

/* Felder: Header + Footer */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'      => 'group_kidsclub_global',
				'title'    => 'Header & Footer',
				'location' => [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'kidsclub-settings',
						],
					],
				],
				'fields'   => [

					/* ===== HEADER ===== */
					[
						'key'   => 'tab_header',
						'label' => 'Header',
						'type'  => 'tab',
					],
					[
						'key'          => 'f_logo',
						'label'        => 'Logo (optional)',
						'name'         => 'header_logo',
						'type'         => 'image',
						'instructions' => 'Leer lassen = gezeichnetes Bogen-Logo + Schriftzug.',
					],
					[
						'key'          => 'f_nav',
						'label'        => 'Navigation',
						'name'         => 'header_nav',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'Menüpunkt',
						'sub_fields'   => [
							[
								'key'   => 'f_nav_label',
								'label' => 'Label',
								'name'  => 'label',
								'type'  => 'text',
							],
							[
								'key'   => 'f_nav_link',
								'label' => 'Link (z. B. #leistungen)',
								'name'  => 'link',
								'type'  => 'text',
							],
						],
					],
					[
						'key'           => 'f_cta_label',
						'label'         => 'Button-Text',
						'name'          => 'header_cta_label',
						'type'          => 'text',
						'default_value' => 'Online Termin buchen',
					],
					[
						'key'           => 'f_cta_link',
						'label'         => 'Button-Link',
						'name'          => 'header_cta_link',
						'type'          => 'text',
						'default_value' => '#termin',
					],

					/* ===== FOOTER ===== */
					[
						'key'   => 'tab_footer',
						'label' => 'Footer',
						'type'  => 'tab',
					],
					[
						'key'   => 'f_about',
						'label' => 'Kurztext',
						'name'  => 'footer_about',
						'type'  => 'textarea',
						'rows'  => 3,
					],
					[
						'key'          => 'f_cols',
						'label'        => 'Spalten',
						'name'         => 'footer_cols',
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => 'Spalte',
						'sub_fields'   => [
							[
								'key'   => 'f_col_head',
								'label' => 'Überschrift',
								'name'  => 'heading',
								'type'  => 'text',
							],
							[
								'key'          => 'f_col_links',
								'label'        => 'Links',
								'name'         => 'links',
								'type'         => 'repeater',
								'layout'       => 'table',
								'button_label' => 'Link',
								'sub_fields'   => [
									[
										'key'   => 'f_col_link_label',
										'label' => 'Label',
										'name'  => 'label',
										'type'  => 'text',
									],
									[
										'key'   => 'f_col_link_url',
										'label' => 'URL',
										'name'  => 'url',
										'type'  => 'text',
									],
								],
							],
						],
					],
					[
						'key'          => 'f_social',
						'label'        => 'Social Media',
						'name'         => 'footer_social',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'Profil',
						'sub_fields'   => [
							[
								'key'     => 'f_soc_net',
								'label'   => 'Netzwerk',
								'name'    => 'network',
								'type'    => 'select',
								'choices' => [
									'instagram' => 'Instagram',
									'facebook'  => 'Facebook',
									'tiktok'    => 'TikTok',
								],
							],
							[
								'key'   => 'f_soc_url',
								'label' => 'URL',
								'name'  => 'url',
								'type'  => 'url',
							],
						],
					],
					[
						'key'          => 'f_legal',
						'label'        => 'Rechtliches',
						'name'         => 'footer_legal',
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => 'Link',
						'sub_fields'   => [
							[
								'key'   => 'f_legal_label',
								'label' => 'Label',
								'name'  => 'label',
								'type'  => 'text',
							],
							[
								'key'   => 'f_legal_url',
								'label' => 'URL',
								'name'  => 'url',
								'type'  => 'text',
							],
						],
					],
					[
						'key'           => 'f_copy',
						'label'         => 'Copyright-Zeile',
						'name'          => 'footer_copyright',
						'type'          => 'text',
						'default_value' => 'Kids Club by zacp · Alle Rechte vorbehalten.',
					],
					[
						'key'          => 'f_kontakt_addr',
						'label'        => 'Adresse',
						'name'         => 'footer_address',
						'type'         => 'text',
						'placeholder'  => 'Am Kirchenkamp 3, 49078 Osnabrück',
						'instructions' => 'Erscheint im Footer-Kontakt-Block.',
					],
					[
						'key'          => 'f_kontakt_phone',
						'label'        => 'Telefon',
						'name'         => 'footer_phone',
						'type'         => 'text',
						'placeholder'  => '+49 (0) 541 471 40',
						'instructions' => 'Wird als tel:-Link verwendet.',
					],
					[
						'key'          => 'f_kontakt_hours',
						'label'        => 'Öffnungszeiten',
						'name'         => 'footer_hours',
						'type'         => 'textarea',
						'rows'         => 2,
						'placeholder'  => "Mo.\u{2013}Do. 08\u{2013}13 & 14\u{2013}18 Uhr\nFr. 08\u{2013}13 Uhr",
						'instructions' => 'Zeilenumbrüche werden im Footer übernommen.',
					],
					[
						'key'           => 'f_footer_qr',
						'label'         => 'QR-Code (Online-Termin)',
						'name'          => 'footer_qr',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'mime_types'    => 'png,jpg,jpeg,svg,webp',
						'instructions'  => 'QR-Code, der zur Online-Terminbuchung führt. Erscheint im Footer (Spalte „Online Termin“). Leer lassen = QR-Code wird ausgeblendet.',
					],

					/* ===== DESIGN ===== */
					[
						'key'   => 'tab_design',
						'label' => 'Design',
						'type'  => 'tab',
					],
					[
						'key'           => 'f_section_align',
						'label'         => 'Ausrichtung der Abschnitte',
						'name'          => 'section_alignment',
						'type'          => 'select',
						'instructions'  => 'Zentriert: Titel, Eyebrow und Lead-Text werden mittig ausgerichtet. Links: alles linksbündig.',
						'choices'       => [
							'left'   => 'Links (Standard)',
							'center' => 'Zentriert',
						],
						'default_value' => 'left',
						'return_format' => 'value',
					],

					[
						'key'           => 'f_anim_variant',
						'label'         => 'Animation-Variante',
						'name'          => 'animation_variant',
						'type'          => 'select',
						'instructions'  => 'Dekorative Animation für die Seite. Standard: schwebende Symbole.',
						'choices'       => [
							'floating' => '🦷 Schwebende Symbole (Standard)',
							'sparkle'  => '✨ Cursor-Funken',
							'confetti' => '🎊 Confetti beim Scrollen',
							'none'     => 'Keine',
						],
						'default_value' => 'floating',
						'return_format' => 'value',
					],

					/* ===== SEO ===== */
					[
						'key'   => 'tab_seo',
						'label' => 'SEO',
						'type'  => 'tab',
					],
					[
						'key'          => 'f_seo_title',
						'label'        => 'Meta-Titel',
						'name'         => 'seo_title',
						'type'         => 'text',
						'instructions' => 'Erscheint im Google-Snippet und Browser-Tab. Leer = "Kinderzahnarzt Osnabrück". Der Praxisname wird automatisch angehängt.',
						'placeholder'  => 'Kinderzahnarzt Osnabrück',
					],
					[
						'key'          => 'f_seo_desc',
						'label'        => 'Meta-Beschreibung',
						'name'         => 'seo_description',
						'type'         => 'textarea',
						'rows'         => 3,
						'instructions' => '150–160 Zeichen. Erscheint im Google-Snippet und beim Teilen (WhatsApp, Facebook).',
						'maxlength'    => 170,
					],
					[
						'key'          => 'f_seo_og',
						'label'        => 'Vorschau-Bild (Open Graph)',
						'name'         => 'seo_og_image',
						'type'         => 'image',
						'instructions' => '1200×630 px empfohlen. Wird beim Teilen des Links angezeigt (WhatsApp, Facebook, LinkedIn).',
					],
				],
			]
		);
	}
);
