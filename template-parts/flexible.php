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
			// Voraussetzung: Das äußerste gerenderte Element des Layouts ist eine
			// <section> — neue Layouts müssen dieses Wrapper-Element beibehalten,
			// sonst bekommt die Section keinen Hintergrund (still, ohne Fehler).
			// Fallback auf $kc_html, falls preg_replace null liefert (pcre.backtrack_limit).
			echo preg_replace_callback(
				'/<section([^>]*)>/',
				function ( $m ) use ( $kc_bg_style ) {
					return '<section' . $m[1] . $kc_bg_style . '>';
				},
				$kc_html,
				1
			) ?? $kc_html;
		}

		// Hinweis: Trenner werden nun als eigenes ACF-Layout "trenner" eingefügt.

	endwhile;
endif;
