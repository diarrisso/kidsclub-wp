<?php
/**
 * Layout: Praxis-Galerie (ACF Pro gallery field)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'prx_eyebrow' );
$title   = get_sub_field( 'prx_title' );
$gallery = get_sub_field( 'gallery' );
$chips   = get_sub_field( 'chips' );
?>
<section class="section-praxis reveal" id="praxis">
	<div class="container">
		<?php
		if ( $eyebrow ) :
			?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $chips ) : ?>
		<div class="chips" role="list">
			<?php foreach ( $chips as $chip ) : ?>
				<span class="chip" role="listitem"><?php echo esc_html( $chip['prx_chip_label'] ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<?php if ( $gallery ) : ?>
		<div class="praxis-gallery">
			<?php foreach ( $gallery as $img ) : ?>
			<figure class="praxis-gallery__item">
				<img src="<?php echo esc_url( $img['sizes']['large'] ?? $img['url'] ); ?>"
					alt="<?php echo esc_attr( $img['alt'] ?: 'Praxis Kids Club' ); ?>"
					loading="lazy"
					width="<?php echo esc_attr( $img['sizes']['large-width'] ?? $img['width'] ); ?>"
					height="<?php echo esc_attr( $img['sizes']['large-height'] ?? $img['height'] ); ?>">
			</figure>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
