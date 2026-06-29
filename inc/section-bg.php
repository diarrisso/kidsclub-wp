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
		$veil    = 'linear-gradient(rgba(' . $rgb . ',' . $alpha . '),rgba(' . $rgb . ',' . $alpha . '))';
		$decls[] = 'background-image:' . $veil . ',url(' . esc_url( $img ) . ')';
		$decls[] = 'background-size:' . esc_attr( $size );
		$decls[] = 'background-position:' . esc_attr( $position );
		$decls[] = 'background-repeat:no-repeat';
	}

	return implode( ';', $decls );
}

/**
 * Résout l'URL d'image à utiliser : image ACF (médiathèque) en priorité,
 * puis spray prédéfini depuis les assets du thème.
 */
function kc_section_bg_resolve_img(): string {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return '';
	}

	$img = get_sub_field( 'background_image' );
	if ( is_array( $img ) && ! empty( $img['url'] ) ) {
		return $img['url'];
	}

	$preset = (string) get_sub_field( 'bg_spray_preset' );
	if ( '' !== $preset ) {
		return esc_url( get_theme_file_uri( 'assets/img/' . $preset . '.png' ) );
	}

	return '';
}

/**
 * Liest die Sub-Fields der aktuellen ACF-Row und liefert das fertige
 * `style`-Attribut (inkl. führendem Leerzeichen) oder '' zurück.
 */
function kc_section_bg_style(): string {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return '';
	}

	$bg_img     = get_sub_field( 'background_image' );
	$has_bg_img = is_array( $bg_img ) && ! empty( $bg_img['url'] );
	$preset     = (string) get_sub_field( 'bg_spray_preset' );
	$raw_opacity = get_sub_field( 'bg_opacity' );

	// Spray sans photo de fond → pas de voile (opacity 100 = alpha 0).
	// Pour une vraie photo, le fallback à 8 préserve la lisibilité du texte.
	// ACF retourne sa default_value (8) même quand le champ est masqué par
	// conditional_logic → on ignore bg_opacity pour les sprays décoratifs.
	$is_spray_only = '' !== $preset && ! $has_bg_img;
	$opacity       = $is_spray_only ? 100 : ( ( false !== $raw_opacity && '' !== $raw_opacity ) ? $raw_opacity : 8 );

	$style = kc_section_bg_build_style(
		[
			'img'      => kc_section_bg_resolve_img(),
			'color'    => (string) get_sub_field( 'background_color' ),
			'opacity'  => $opacity,
			'size'     => $is_spray_only ? 'cover' : ( (string) get_sub_field( 'bg_size' ) ),
			'position' => $is_spray_only ? 'center center' : ( (string) get_sub_field( 'bg_position' ) ),
		]
	);

	return '' === $style ? '' : ' style="' . $style . '"';
}
