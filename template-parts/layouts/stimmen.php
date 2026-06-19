<?php
/**
 * Layout: Kundenstimmen — grille 3 cartes statiques
 * Felder: st_eyebrow, st_title, items[](st_quote, st_name, st_role)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'st_eyebrow' );
$title   = get_sub_field( 'st_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-stimmen reveal" id="stimmen">
	<div class="container">
		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>

		<?php if ( $items ) : ?>
		<div class="stimmen-swiper-wrap">
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
