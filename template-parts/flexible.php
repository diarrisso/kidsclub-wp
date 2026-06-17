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

		$kc_bg     = get_sub_field( 'background_image' );
		$kc_has_bg = is_array( $kc_bg ) && ! empty( $kc_bg['url'] );

		if ( $kc_has_bg ) {
			printf(
				'<div class="kc-section-bg" style="background-image:url(%s)">',
				esc_url( $kc_bg['url'] )
			);
		}

		get_template_part( 'template-parts/layouts/' . $layout );

		if ( $kc_has_bg ) {
			echo '</div>';
		}

		// Trenner nach jedem Block (außer Hero + letztem Block vor Footer) — wie im PDF.
		if ( 'hero' !== $layout && $kc_section_no < $kc_total ) {
			echo '<div class="container"><hr class="block-sep"></div>';
		}

	endwhile;
endif;
