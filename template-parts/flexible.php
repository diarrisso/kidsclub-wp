<?php
/**
 * Render-Schleife für das Flexible-Content-Feld "sections".
 * Pro Layout wird template-parts/layouts/{layout_name}.php geladen.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_rows( 'sections' ) ) :
	while ( have_rows( 'sections' ) ) :
		the_row();

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

		// Trenner nach jedem Block (außer Hero) — wie im PDF.
		if ( 'hero' !== $layout ) {
			echo '<div class="container"><hr class="block-sep"></div>';
		}

	endwhile;
endif;
