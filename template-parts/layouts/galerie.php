<?php
/**
 * Layout: Einblicke — Foto-Karussell (Swiper).
 *
 * Source : CPT `praxis_foto` (Beitragsbild = Foto, Sortierung menu_order).
 * Ersetzt die frühere Filter-Galerie: ein Karussell mit 3 sichtbaren Fotos
 * (Desktop) + prev/next-Pfeilen + Pagination. Vermeidet die Dopplung mit der
 * Räume-Sektion. Swiper-Regeln des Projekts beachtet (overflow/padding-bottom,
 * slidesPerView responsive, prefers-reduced-motion, a11y).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'gl_eyebrow' );
$title   = get_sub_field( 'gl_title' );
$text    = get_sub_field( 'gl_text' );

/* Fotos aus dem CPT sammeln. */
$photos = [];

$foto_posts = get_posts(
	[
		'post_type'      => 'praxis_foto',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

foreach ( $foto_posts as $foto ) {
	$img_id = get_post_thumbnail_id( $foto );
	if ( ! $img_id ) {
		continue;
	}
	$photos[] = [
		'id'  => (int) $img_id,
		'alt' => get_the_title( $foto ),
	];
}

$total = count( $photos );
?>
<section
	class="section section-galerie reveal"
	id="galerie"
	<?php echo kc_section_bg_style(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<div class="container">

		<div class="section-head center reveal">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p class="section-lead"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $photos ) : ?>
		<div class="einblicke-swiper-wrap">

			<button class="einblicke-swiper__prev swiper-nav-btn" type="button" aria-label="<?php esc_attr_e( 'Vorheriges Foto', 'kidsclub' ); ?>">
				<?php echo kc_svg( 'slide-prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="einblicke-swiper__next swiper-nav-btn" type="button" aria-label="<?php esc_attr_e( 'Nächstes Foto', 'kidsclub' ); ?>">
				<?php echo kc_svg( 'slide-next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<div class="einblicke-swiper swiper"
				aria-roledescription="Karussell"
				aria-label="<?php esc_attr_e( 'Einblicke in unsere Praxis', 'kidsclub' ); ?>">
				<div class="swiper-wrapper" aria-live="polite">
					<?php
					$i = 0;
					foreach ( $photos as $p ) :
						++$i;
						?>
						<div class="swiper-slide" role="group" aria-roledescription="Folie"
							aria-label="<?php echo esc_attr( sprintf( /* translators: 1: aktuelles Foto, 2: Gesamtzahl */ __( 'Foto %1$d von %2$d', 'kidsclub' ), $i, $total ) ); ?>">
							<figure class="einblicke-slide">
								<?php
								echo wp_get_attachment_image(
									$p['id'],
									'large',
									false,
									[
										'loading'  => 'lazy',
										'decoding' => 'async',
										'alt'      => $p['alt'],
									]
								);
								?>
							</figure>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="einblicke-swiper__pagination"></div>

		</div>
		<?php endif; ?>

	</div>
</section>
