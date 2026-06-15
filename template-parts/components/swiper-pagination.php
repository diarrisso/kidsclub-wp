<?php
/**
 * Reusable Swiper pagination container.
 * Args: prefix (required) → generates {prefix}__pagination ; type 'dots'|'progressbar'.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$args   = wp_parse_args(
	$args ?? [],
	[
		'prefix' => '',
		'type'   => 'dots',
	]
);
$prefix = sanitize_html_class( $args['prefix'] );
if ( ! $prefix ) {
	return;
}
$type_class = 'progressbar' === $args['type'] ? ' swiper-pagination--progressbar' : '';
?>
<div class="swiper-pagination <?php echo esc_attr( $prefix . '__pagination' ); ?><?php echo esc_attr( $type_class ); ?>" aria-hidden="true"></div>
