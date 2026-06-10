<?php
/**
 * SEO / AI: JSON-LD strukturierte Daten (schema.org).
 * Macht die Praxis für Google & KI-Assistenten maschinenlesbar.
 * In functions.php:  require get_theme_file_path('inc/schema.php');
 *
 * Werte an die echten Praxisdaten anpassen (Adresse, Tel, Geo, Öffnungszeiten).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		if ( ! is_page_template( 'page-landing.php' ) ) {
			return;
		}

		/* Bild fürs Rich Result: OG-Bild aus den SEO-Optionen, sonst Logo */
		$img     = function_exists( 'get_field' ) ? ( get_field( 'seo_og_image', 'option' ) ?: get_field( 'header_logo', 'option' ) ) : null;
		$img_url = ( $img && ! empty( $img['url'] ) ) ? $img['url'] : '';

		/* sameAs aus den Social-Profilen im Footer */
		$same_as = [];
		if ( function_exists( 'get_field' ) && ( $social = get_field( 'footer_social', 'option' ) ) ) {
			foreach ( $social as $s ) {
				if ( ! empty( $s['url'] ) ) {
					$same_as[] = $s['url'];
				}
			}
		}

		$data = [
			'@context'                  => 'https://schema.org',
			'@type'                     => 'Dentist',
			'@id'                       => home_url( '/#praxis' ),
			'name'                      => 'Kids Club by zacp',
			'description'               => 'Kinderzahnarztpraxis in Osnabrück – entspannt zum Zahnarzt vom ersten Zähnchen an.',
			'url'                       => home_url( '/' ),
			'telephone'                 => '+49 541 47140',
			'priceRange'                => '€€',
			'medicalSpecialty'          => 'Pediatric Dentistry',
			'address'                   => [
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Am Kirchenkamp 3',
				'postalCode'      => '49078',
				'addressLocality' => 'Osnabrück',
				'addressCountry'  => 'DE',
			],
			/* Geo = starkes Local-SEO-Signal. Koordinaten Am Kirchenkamp 3 — bei Go-Live
				mit Google Maps verifizieren (Rechtsklick auf Pin → Koordinaten kopieren). */
			'geo'                       => [
				'@type'     => 'GeoCoordinates',
				'latitude'  => 52.2865,
				'longitude' => 8.0277,
			],
			'hasMap'                    => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Kids Club by zacp, Am Kirchenkamp 3, 49078 Osnabrück' ),
			'openingHoursSpecification' => [
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => [ 'Monday','Tuesday','Wednesday','Thursday' ],
					'opens'     => '08:00',
					'closes'    => '18:00',
				],
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => 'Friday',
					'opens'     => '08:00',
					'closes'    => '13:00',
				],
			],
		];

		if ( $img_url ) {
			$data['image'] = $img_url;
		}
		if ( $same_as ) {
			$data['sameAs'] = $same_as;
		}

		echo "\n<script type=\"application/ld+json\">"
		. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. "</script>\n";
	},
	5
);

/*
 * TIPP für die FAQ-Sektion: zusätzlich ein FAQPage-Schema ausgeben
 * (Frage/Antwort aus dem ACF-Repeater), das erzeugt Rich-Results bei Google.
 */
