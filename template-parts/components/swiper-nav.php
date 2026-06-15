<?php
/**
 * Reusable Swiper prev/next navigation buttons.
 * Args (via get_template_part 3rd arg):
 *   prefix      (required) → generates {prefix}__prev / {prefix}__next  (Swiper navigation els)
 *   prev_class  (optional) extra positioning class
 *   next_class  (optional) extra positioning class
 * Fallback chevron SVGs inline; optional theme icons via get_field('swiper_prev_icon'/'swiper_next_icon','option').
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$args   = wp_parse_args(
	$args ?? [],
	[
		'prefix'     => '',
		'prev_class' => '',
		'next_class' => '',
	]
);
$prefix = sanitize_html_class( $args['prefix'] );
if ( ! $prefix ) {
	return;
}
$icon_prev = function_exists( 'get_field' ) ? get_field( 'swiper_prev_icon', 'option' ) : '';
$icon_next = function_exists( 'get_field' ) ? get_field( 'swiper_next_icon', 'option' ) : '';
$svg_prev  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>';
$svg_next  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>';
?>
<button type="button" class="swiper-nav swiper-nav--prev <?php echo esc_attr( $prefix . '__prev' ); ?> <?php echo esc_attr( $args['prev_class'] ); ?>" aria-label="Vorherige Folie">
	<?php echo $icon_prev ? wp_get_attachment_image( $icon_prev['ID'], 'thumbnail', false, [ 'alt' => '' ] ) : $svg_prev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG / wp_get_attachment_image ?>
</button>
<button type="button" class="swiper-nav swiper-nav--next <?php echo esc_attr( $prefix . '__next' ); ?> <?php echo esc_attr( $args['next_class'] ); ?>" aria-label="Nächste Folie">
	<?php echo $icon_next ? wp_get_attachment_image( $icon_next['ID'], 'thumbnail', false, [ 'alt' => '' ] ) : $svg_next; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG / wp_get_attachment_image ?>
</button>
