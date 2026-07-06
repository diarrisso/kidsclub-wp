<?php
/**
 * Section-Hintergrund-Helper.
 *
 * Erzeugt ein inline `style`-Attribut, das DIREKT auf die <section> gehört.
 * Die Bild-Abschwächung läuft über einen Schleier-Gradient (statt `opacity`),
 * damit der Section-Inhalt voll deckend bleibt. Der Schleier nimmt die
 * Section-Hintergrundfarbe an (sonst Weiß), funktioniert also auf weißem
 * wie farbigem Untergrund.
 *
 * @package KidsClub
 */

/**
 * Wandelt einen Hex-Farbwert in "r,g,b" um. Fällt bei Ungültigkeit auf Weiß zurück.
 */
function kc_section_bg_hex_to_rgb( string $hex ): string {
	$hex = ltrim( trim( $hex ), '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '255,255,255';
	}
	return hexdec( substr( $hex, 0, 2 ) ) . ',' . hexdec( substr( $hex, 2, 2 ) ) . ',' . hexdec( substr( $hex, 4, 2 ) );
}

/**
 * Baut die CSS-Deklarationen für den Section-Hintergrund (ohne `style=`).
 *
 * @param array $o { img?:string, color?:string, opacity?:int, size?:string, position?:string }
 * @return string CSS-Deklarationen mit `;` getrennt, oder '' wenn nichts zu setzen ist.
 */
function kc_section_bg_build_style( array $o ): string {
	$img      = isset( $o['img'] ) ? (string) $o['img'] : '';
	$color    = isset( $o['color'] ) ? (string) $o['color'] : '';
	$opacity  = isset( $o['opacity'] ) && '' !== $o['opacity'] ? (int) $o['opacity'] : 8;
	$size     = isset( $o['size'] ) && '' !== $o['size'] ? (string) $o['size'] : '115%';
	$position = isset( $o['position'] ) && '' !== $o['position'] ? (string) $o['position'] : 'center top';

	$has_img   = '' !== $img;
	$has_color = '' !== $color;

	if ( ! $has_img && ! $has_color ) {
		return '';
	}

	$opacity = max( 0, min( 100, $opacity ) );
	$alpha   = round( 1 - ( $opacity / 100 ), 3 );
	$rgb     = $has_color ? kc_section_bg_hex_to_rgb( $color ) : '255,255,255';

	$decls = [];
	if ( $has_color ) {
		$safe_color = sanitize_hex_color( $color ) ?? '';
		if ( '' !== $safe_color ) {
			$decls[] = 'background-color:' . esc_attr( $safe_color );
		}
	}
	if ( $has_img ) {
		// Voile d'atténuation seulement si une opacité < 100 est demandée.
		$veil    = $alpha > 0
			? 'linear-gradient(rgba(' . $rgb . ',' . $alpha . '),rgba(' . $rgb . ',' . $alpha . ')),'
			: '';
		$decls[] = 'background-image:' . $veil . 'url(' . esc_url( $img ) . ')';
		$decls[] = 'background-size:' . esc_attr( $size );
		$decls[] = 'background-position:' . esc_attr( $position );
		$decls[] = 'background-repeat:no-repeat';
	}

	return implode( ';', $decls );
}

/**
 * Liest die Sub-Fields der aktuellen ACF-Row und liefert das fertige
 * `style`-Attribut (inkl. führendem Leerzeichen) oder '' zurück.
 *
 * Maquette : les sprays sont des bandes de transition (Spray1–6 : 1700×984,
 * Spray7/8 : 1950×450) posées EN HAUT de la section à leur hauteur naturelle
 * (largeur 100 %), derrière le titre — jamais étirées, jamais voilées.
 */
function kc_section_bg_style(): string {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return '';
	}

	$preset = (string) get_sub_field( 'bg_spray_preset' );

	// Couleur : preset palette en priorité, sinon color picker (rétrocompat).
	$color_preset = (string) get_sub_field( 'bg_color_preset' );
	if ( '' !== $color_preset && 'custom' !== $color_preset ) {
		$color = $color_preset;
	} else {
		$color = (string) get_sub_field( 'background_color' );
	}

	$style = kc_section_bg_build_style(
		[
			'img'      => '' !== $preset ? get_theme_file_uri( 'assets/img/' . $preset . '.png' ) : '',
			'color'    => $color,
			'opacity'  => 100,
			'size'     => '100% auto',
			'position' => 'center top',
		]
	);

	return '' === $style ? '' : ' style="' . $style . '"';
}
