<?php
/**
 * Layout: Kundenstimmen — Swiper (2 breite Karten = volle Breite, wie PDF).
 * Felder: st_eyebrow, st_title, st_text, items[](st_quote, st_name, st_role)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'st_eyebrow' );
$title   = get_sub_field( 'st_title' );
$lead    = get_sub_field( 'st_text' );
$items   = get_sub_field( 'items' );
?>
<section class="section-stimmen reveal" id="stimmen"<?php echo kc_section_bg_style(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<div class="section-head center reveal">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="section-lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $items ) : ?>
		<div class="stimmen-swiper-wrap">

			<button class="stimmen-swiper__prev swiper-nav-btn" type="button" aria-label="<?php esc_attr_e( 'Vorherige Stimme', 'kidsclub' ); ?>">
				<?php echo kc_svg( 'slide-prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="stimmen-swiper__next swiper-nav-btn" type="button" aria-label="<?php esc_attr_e( 'Nächste Stimme', 'kidsclub' ); ?>">
				<?php echo kc_svg( 'slide-next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<div class="stimmen-swiper swiper"
				aria-roledescription="Karussell"
				aria-label="Kundenstimmen">
				<div class="swiper-wrapper">
					<?php foreach ( $items as $item ) : ?>
					<div class="swiper-slide">
						<div class="stimmen-card">
							<blockquote class="stimmen-quote">
								<p><?php echo esc_html( $item['st_quote'] ); ?></p>
								<footer class="stimmen-author">
									<strong><?php echo esc_html( $item['st_name'] ); ?></strong>
									<?php if ( ! empty( $item['st_role'] ) ) : ?>
										<span><?php echo esc_html( $item['st_role'] ); ?></span>
									<?php endif; ?>
								</footer>
							</blockquote>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="stimmen-swiper__pagination"></div>
		</div>
		<?php endif; ?>
	</div>
</section>
