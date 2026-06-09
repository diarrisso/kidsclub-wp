<?php
/**
 * SEO / AI: JSON-LD strukturierte Daten (schema.org).
 * Macht die Praxis für Google & KI-Assistenten maschinenlesbar.
 * In functions.php:  require get_theme_file_path('inc/schema.php');
 *
 * Werte an die echten Praxisdaten anpassen (Adresse, Tel, Geo, Öffnungszeiten).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_head', function () {
	if ( ! is_page_template( 'page-landing.php' ) ) return;

	$data = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Dentist',
		'name'        => 'Kids Club by zacp',
		'description' => 'Kinderzahnarztpraxis in Osnabrück – entspannt zum Zahnarzt vom ersten Zähnchen an.',
		'url'         => home_url( '/' ),
		'telephone'   => '+49 541 47140',
		'priceRange'  => '€€',
		'medicalSpecialty' => 'Pediatric Dentistry',
		'address'     => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Am Kirchenkamp 3',
			'postalCode'      => '49078',
			'addressLocality' => 'Osnabrück',
			'addressCountry'  => 'DE',
		],
		'openingHoursSpecification' => [
			[ '@type' => 'OpeningHoursSpecification',
			  'dayOfWeek' => [ 'Monday','Tuesday','Wednesday','Thursday' ],
			  'opens' => '08:00', 'closes' => '18:00' ],
			[ '@type' => 'OpeningHoursSpecification',
			  'dayOfWeek' => 'Friday', 'opens' => '08:00', 'closes' => '13:00' ],
		],
	];

	echo "\n<script type=\"application/ld+json\">"
	   . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	   . "</script>\n";
}, 5 );

/*
 * TIPP für die FAQ-Sektion: zusätzlich ein FAQPage-Schema ausgeben
 * (Frage/Antwort aus dem ACF-Repeater), das erzeugt Rich-Results bei Google.
 */
