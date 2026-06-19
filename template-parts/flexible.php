<?php
/**
 * Render-Schleife für das Flexible-Content-Feld "sections".
 * Pro Layout wird template-parts/layouts/{layout_name}.php geladen.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_rows( 'sections' ) ) :

	// Gesamtzahl der Sections vorab ermitteln. NIEMALS have_rows( 'sections' )
	// innerhalb dieser Schleife erneut aufrufen — das setzt ACFs internen
	// Row-Pointer zurück und erzeugt eine Endlosschleife (Memory Exhaustion).
	$kc_sections   = get_field( 'sections' );
	$kc_total      = is_array( $kc_sections ) ? count( $kc_sections ) : 0;
	$kc_section_no = 0;

	while ( have_rows( 'sections' ) ) :
		the_row();
		++$kc_section_no;

		$layout = get_row_layout(); // z. B. "hero", "leistungen", "zimmer"

		$kc_bg        = get_sub_field( 'background_image' );
		$kc_has_bg    = is_array( $kc_bg ) && ! empty( $kc_bg['url'] );
		$kc_color     = get_sub_field( 'background_color' );
		$kc_has_color = ! empty( $kc_color );

		$kc_needs_transform = $kc_has_bg || $kc_has_color;

		if ( $kc_needs_transform ) {
			// Capturer le HTML du layout pour injecter couleur + image DANS la <section>.
			// Le background-color inline surcharge le CSS du thème (spécificité inline > classe).
			// Le div bg-img positionné à l'intérieur de la section reste visible par-dessus sa couleur.
			ob_start();
		}

		get_template_part( 'template-parts/layouts/' . $layout );

		if ( $kc_needs_transform ) {
			$kc_html = ob_get_clean();

			$kc_style  = $kc_has_color
				? ' style="background-color:' . esc_attr( $kc_color ) . '"'
				: '';
			$kc_bg_div = $kc_has_bg
				? '<div class="kc-section-bg__img" style="background-image:url(' . esc_url( $kc_bg['url'] ) . ')" aria-hidden="true"></div>'
				: '';

			// Une seule passe regex : injecte style + div en même temps.
			echo preg_replace(
				'/<section([^>]*)>/',
				'<section$1' . $kc_style . '>' . $kc_bg_div,
				$kc_html,
				1
			);
		}

		// Trenner nach jedem Block (außer Hero + letztem Block vor Footer) — wie im PDF.
		if ( 'hero' !== $layout && $kc_section_no < $kc_total ) {
			echo '<div class="container"><hr class="block-sep"></div>';
		}

	endwhile;
endif;
