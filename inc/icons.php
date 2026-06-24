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
	$path = get_theme_file_path( "assets/svg/{$slug}.svg" );
	if ( ! file_exists( $path ) ) {
		return '';
	}
	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
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
