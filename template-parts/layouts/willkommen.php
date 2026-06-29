<?php
/**
 * Layout: Willkommen (Intro).
 * Feld: text (wysiwyg) — zentrierter Intro-Absatz, „Herzlich Willkommen!" fett (Magenta).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wk_text = get_sub_field( 'text' );
if ( ! $wk_text ) {
	return;
}
?>
<section class="section intro-welcome reveal" id="willkommen">
	<div class="container intro-welcome__inner">
		<?php echo wp_kses_post( $wk_text ); ?>
	</div>
</section>
