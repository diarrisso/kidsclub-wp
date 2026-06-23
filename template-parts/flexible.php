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

		// Inline-Style (Farbe + Schleier-Bild) direkt auf die <section>.
		$kc_bg_style        = kc_section_bg_style(); // '' oder ' style="..."'
		$kc_needs_transform = '' !== $kc_bg_style;

		if ( $kc_needs_transform ) {
			ob_start();
		}

		get_template_part( 'template-parts/layouts/' . $layout );

		if ( $kc_needs_transform ) {
			$kc_html = ob_get_clean();

			// Eine Regex-Passe: Style ins erste <section>-Tag injizieren.
			// Fallback auf $kc_html, falls preg_replace null liefert (pcre.backtrack_limit).
			echo preg_replace(
				'/<section([^>]*)>/',
				'<section$1' . $kc_bg_style . '>',
				$kc_html,
				1
			) ?? $kc_html;
		}

		// Trenner nach jedem Block (außer Hero + letztem Block vor Footer) — wie im PDF.
		if ( 'hero' !== $layout && $kc_section_no < $kc_total ) {
			echo '<div class="container"><hr class="block-sep"></div>';
		}

	endwhile;
endif;
