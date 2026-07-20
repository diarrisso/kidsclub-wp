<?php
/**
 * Zentrale Icon-Bibliothek. Aufruf: kc_icon('heart')
 * So liegt der SVG-Code an EINER Stelle statt im ACF-Feld.
 * In functions.php:  require get_theme_file_path('inc/icons.php');
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kc_icon( $slug ) {
	// Override aus den Theme-Optionen (Bild-Upload). Leer = Inline-SVG unten.
	if ( function_exists( 'get_field' ) && array_key_exists( $slug, kc_content_icon_slugs() ) ) {
		$custom = get_field( kc_icon_field_name( $slug ), 'option' );
		if ( is_array( $custom ) && ! empty( $custom['url'] ) ) {
			$u = esc_url( $custom['url'] );
			return '<span class="kc-icon-custom" aria-hidden="true" style="-webkit-mask:url(' . $u . ') center/contain no-repeat;mask:url(' . $u . ') center/contain no-repeat"></span>';
		}
	}
	$s   = 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"';
	$map = [
		'heart'         => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21C7 17 4 13.5 4 9.8 4 7 6 5 8.6 5c1.6 0 3 .8 3.4 2 .4-1.2 1.8-2 3.4-2C18 5 20 7 20 9.8c0 3.7-3 7.2-8 11.2Z"/></svg>',
		// Leistungs-Icons
		'vorsorge'      => '<svg ' . $s . '><path d="M12 3c-4 0-6 2.6-6 6.5 0 4 1.6 8 3.4 9 1.2.6 1.4-3.2 2.6-3.2s1.4 3.8 2.6 3.2c1.8-1 3.4-5 3.4-9C18 5.6 16 3 12 3Z"/><path d="m18 4 1.5-1.5M16 6l2-1"/></svg>',
		'fuellung'      => '<svg ' . $s . '><path d="M9 11V6a3 3 0 0 1 6 0v5"/><rect x="4" y="11" width="16" height="10" rx="3"/><path d="M12 15v2"/></svg>',
		'notfall'       => '<svg ' . $s . '><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/><circle cx="12" cy="12" r="5"/><path d="m9 12 2 2 4-4"/></svg>',
		'lachgas'       => '<svg ' . $s . '><path d="M17.5 19a4.5 4.5 0 1 0 0-9H17a6 6 0 1 0-11 2"/><path d="M3 15h6M5 19h5"/></svg>',
		'kfo'           => '<svg ' . $s . '><path d="M4 7h16M4 12h16M4 17h16"/><circle cx="8" cy="7" r="1.6" fill="currentColor"/><circle cx="15" cy="12" r="1.6" fill="currentColor"/><circle cx="10" cy="17" r="1.6" fill="currentColor"/></svg>',
		'angst'         => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21C7 17 4 13.5 4 9.8 4 7 6 5 8.6 5c1.6 0 3 .8 3.4 2 .4-1.2 1.8-2 3.4-2C18 5 20 7 20 9.8c0 3.7-3 7.2-8 11.2Z"/></svg>',
		// „mehr“-Overlay-Icons (Linien, für Karten-Header und Overlay-Abschnitte)
		'zahn'          => '<svg ' . $s . '><path d="M8 3.5C5.8 3.5 4 5.3 4 7.5c0 1 .2 2 .5 2.9l1.5 6.8c.2 1 .4 2.3 1.4 2.3s1.1-1.3 1.4-2.4l.6-2.4c.15-.6.9-.6 1.05 0l.6 2.4c.3 1.1.4 2.4 1.4 2.4s1.2-1.3 1.4-2.3l1.5-6.8c.3-.9.5-1.9.5-2.9C17 5.3 15.2 3.5 13 3.5c-1.3 0-2 .6-2.7 1.1-.7-.5-1.4-1.1-2.3-1.1Z"/></svg>',
		'buerste'       => '<svg ' . $s . '><path d="M4 20 13 11"/><path d="M12.5 7.5l4 4-1.4 1.4a2 2 0 0 1-2.8-2.8L12.5 7.5Z"/><path d="M15 6l1 1M17 8l1 1M14.5 6.5l3 3"/></svg>',
		'smiley'        => '<svg ' . $s . '><circle cx="12" cy="12" r="9"/><path d="M8 14c.9 1.6 2.4 2.5 4 2.5s3.1-.9 4-2.5"/><circle cx="9" cy="10" r=".8" fill="currentColor" stroke="none"/><circle cx="15" cy="10" r=".8" fill="currentColor" stroke="none"/></svg>',
		'gebiss'        => '<svg ' . $s . '><path d="M4 9c0-2.2 1.8-4 4-4h8c2.2 0 4 1.8 4 4 0 2.8-1.6 4.6-1.6 7 0 1-1 1-1.3 0l-.6-2c-.3-1-1-1-1.3 0l-.5 1.6c-.2.7-1 .7-1.2 0L12.6 13c-.3-1-1-1-1.3 0M4 9c0 2.8 1.6 4.6 1.6 7 0 1 1 1 1.3 0l.6-2c.3-1 1-1 1.3 0l.5 1.6c.2.7 1 .7 1.2 0L11.4 13M8 5v3M12 5v3M16 5v3"/></svg>',
		'herz'          => '<svg ' . $s . '><path d="M12 20C12 20 3.5 15 3.5 8.8 3.5 6.1 5.6 4 8.2 4 10 4 11.3 5 12 6.2 12.7 5 14 4 15.8 4 18.4 4 20.5 6.1 20.5 8.8 20.5 15 12 20 12 20Z"/></svg>',
		// Zimmer-Icons
		'room_g'        => '<svg ' . $s . '><path d="M3 21V10l9-7 9 7v11h-6v-6H9v6Z"/></svg>',
		'room_y'        => '<svg ' . $s . '><circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></svg>',
		'room_o'        => '<svg ' . $s . '><path d="M12 3c1.5 3 4 4 4 7a4 4 0 0 1-8 0c0-3 2.5-4 4-7Z"/></svg>',
		'room_b'        => '<svg ' . $s . '><path d="M5 16c-1.7 0-3-1.3-3-3s1.3-3 3-3c.2-2.8 2.5-5 5.3-5 2.4 0 4.4 1.6 5 3.7.5-.3 1.1-.4 1.7-.4 2 0 3.7 1.7 3.7 3.7S19.7 16 17.7 16Z"/></svg>',
		'room_l'        => '<svg ' . $s . '><path d="m12 3 2.4 5.3L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.6-.7Z"/></svg>',
		// Karussell-Navigation (Slider prev/next)
		'chevron_left'  => '<svg ' . $s . '><path d="m15 18-6-6 6-6"/></svg>',
		'chevron_right' => '<svg ' . $s . '><path d="m9 18 6-6-6-6"/></svg>',
	];
	return $map[ $slug ] ?? '';
}

/**
 * SVG aus assets/svg/{slug}.svg inline ausgeben.
 * Aufruf: kc_svg('arrow')  oder  kc_svg('phone', 'Telefon anrufen')
 * Ohne $label bleibt aria-hidden="true" (dekorativ).
 * Mit $label wird aria-hidden entfernt und role="img" + aria-label gesetzt.
 */
/**
 * UI-Icons, die im Backend (Theme-Optionen → Icons) überschrieben werden können.
 * slug => Label (Deutsch). Reihenfolge = Reihenfolge im Backend.
 */
function kc_ui_icon_slugs() {
	return [
		'menu'            => 'Menü (Burger)',
		'close'           => 'Schließen (X)',
		'phone'           => 'Telefon',
		'email'           => 'E-Mail',
		'fax'             => 'Fax',
		'back-to-top'     => 'Back-to-Top (Pfeil hoch)',
		'arrow'           => 'Pfeil',
		'plus'            => 'Plus',
		'accordion-open'  => 'Akkordeon geöffnet',
		'accordion-close' => 'Akkordeon geschlossen',
		'slide-prev'      => 'Slider zurück',
		'slide-next'      => 'Slider vor',
		'facebook'        => 'Facebook',
	];
}

/**
 * Inhalts-Icons (kc_icon), die überschrieben werden können.
 * Genutzt v. a. für die Leistungs-Icons (eltern) + Herz.
 */
function kc_content_icon_slugs() {
	return [
		'heart'    => 'Herz',
		'vorsorge' => 'Vorsorge',
		'fuellung' => 'Füllung',
		'notfall'  => 'Notfall',
		'lachgas'  => 'Lachgas',
		'kfo'      => 'Kieferorthopädie',
		'angst'    => 'Angstpatienten',
	];
}

/** ACF-Feldname für ein Icon-Override (Bindestrich → Unterstrich). */
function kc_icon_field_name( $slug ) {
	return 'icon_' . str_replace( '-', '_', $slug );
}

/** Baut die Bild-Felder für eine slug=>Label-Map (ohne Tab). */
function kc_icon_fields_from_map( array $map ) {
	$fields = [];
	foreach ( $map as $slug => $label ) {
		$name     = kc_icon_field_name( $slug );
		$fields[] = [
			'key'           => 'f_' . $name,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'array',
			'preview_size'  => 'thumbnail',
			'library'       => 'all',
			'mime_types'    => 'svg,png,webp',
			'instructions'  => 'Eigenes Icon (SVG/PNG/WebP). Leer = Standard-Icon des Themes.',
		];
	}
	return $fields;
}

/**
 * Baut die ACF-Felder (Tab + Bild-Felder) für die Icon-Overrides.
 * Wird in inc/options.php per Spread eingefügt.
 */
function kc_build_icon_option_fields() {
	return array_merge(
		[
			[
				'key'   => 'tab_icons',
				'label' => 'Icons',
				'type'  => 'tab',
			],
			[
				'key'     => 'msg_icons_ui',
				'label'   => 'UI-Icons',
				'name'    => '',
				'type'    => 'message',
				'message' => 'Bedien-Icons (Header, Menü, Slider, Footer). Leer = Standard-Icon des Themes.',
			],
		],
		kc_icon_fields_from_map( kc_ui_icon_slugs() ),
		[
			[
				'key'     => 'msg_icons_content',
				'label'   => 'Inhalts-Icons',
				'name'    => '',
				'type'    => 'message',
				'message' => 'Leistungs-Icons (Eltern-Bereich) + Herz. Leer = Standard-Icon des Themes.',
			],
		],
		kc_icon_fields_from_map( kc_content_icon_slugs() )
	);
}

function kc_svg( $slug, $label = '' ) {
	$allowed = [
		'arrow',
		'close',
		'email',
		'fax',
		'menu',
		'phone',
		'accordion-close',
		'accordion-open',
		'plus',
		'slide-prev',
		'slide-next',
		'back-to-top',
		'facebook',
	];
	if ( ! in_array( $slug, $allowed, true ) ) {
		return '';
	}

	// Override aus den Theme-Optionen (Bild-Upload). Leer = Theme-SVG unten.
	if ( function_exists( 'get_field' ) ) {
		$custom = get_field( kc_icon_field_name( $slug ), 'option' );
		if ( is_array( $custom ) && ! empty( $custom['url'] ) ) {
			$aria = $label ? ' role="img" aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true"';
			$u    = esc_url( $custom['url'] );
			return '<span class="kc-icon-custom"' . $aria . ' style="-webkit-mask:url(' . $u . ') center/contain no-repeat;mask:url(' . $u . ') center/contain no-repeat"></span>';
		}
	}

	$path = get_theme_file_path( "assets/svg/{$slug}.svg" );
	if ( ! file_exists( $path ) ) {
		return '';
	}
	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	// Préfixer tous les IDs pour éviter les conflits quand plusieurs SVGs sont inlinés sur la même page.
	static $counters   = [];
	$counters[ $slug ] = ( $counters[ $slug ] ?? 0 ) + 1;
	$prefix            = 'kc-' . $slug . '-' . $counters[ $slug ] . '-';
	$svg               = preg_replace( '/\bid="([^"]+)"/', 'id="' . $prefix . '$1"', $svg );
	$svg               = preg_replace( '/\burl\(#([^)]+)\)/', 'url(#' . $prefix . '$1)', $svg );
	$svg               = preg_replace( '/xlink:href="#([^"]+)"/', 'xlink:href="#' . $prefix . '$1"', $svg );

	if ( $label ) {
		$svg = str_replace( 'aria-hidden="true"', 'role="img" aria-label="' . esc_attr( $label ) . '"', $svg );
	}
	return $svg;
}

/**
 * Symbol-Illustration (Leistungs-Karten). Aufruf: kc_symbol('symbol1')
 * Gibt ein <img> auf die SVG-Datei zurück (Voll-Farbe, 691×573).
 */
function kc_symbol( $slug, $alt = '' ) {
	$allowed = [ 'symbol1', 'symbol2', 'symbol3', 'symbol4', 'symbol5', 'symbol6', 'symbol7', 'symbol8', 'symbol9' ];
	if ( ! in_array( $slug, $allowed, true ) ) {
		return '';
	}
	// Support "a" variant: 'symbol1a' → 'Symbol1a.svg'
	$file = preg_replace( '/^symbol(\d+)(a?)$/', 'Symbol$1$2', $slug );
	$path = get_theme_file_path( "assets/img/symbols/{$file}.svg" );
	$url  = get_theme_file_uri( "assets/img/symbols/{$file}.svg" ) . '?v=' . ( file_exists( $path ) ? filemtime( $path ) : '1' );
	return '<img class="svc-symbol" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" width="96" height="80">';
}
