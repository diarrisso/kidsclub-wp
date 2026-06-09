<?php
/**
 * Render-Schleife für das Flexible-Content-Feld "sections".
 * Pro Layout wird template-parts/layouts/{layout_name}.php geladen.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( have_rows( 'sections' ) ) :
	while ( have_rows( 'sections' ) ) : the_row();

		$layout = get_row_layout(); // z. B. "hero", "leistungen", "zimmer"

		get_template_part( 'template-parts/layouts/' . $layout );

	endwhile;
endif;
