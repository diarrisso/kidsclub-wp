<?php
/**
 * Layout: 5 Behandlungszimmer — Swiper-Karussell
 * Felder: eyebrow, title, text, rooms[] (name, color)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rooms_rows = get_sub_field( 'rooms' );
?>
<section class="section section-zimmer" id="zimmer">
	<div class="container">

		<div class="section-head center" style="margin-bottom:30px">
			<span class="eyebrow"><?php echo esc_html( get_sub_field( 'eyebrow' ) ); ?></span>
			<h2 class="section-title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
			<?php
			$t = get_sub_field( 'text' );
			if ( $t ) :
				?>
				<p class="lead"><?php echo esc_html( $t ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $rooms_rows ) : ?>
		<div class="zimmer-swiper-wrap">

			<button class="zimmer-swiper__prev swiper-nav-btn" aria-label="Vorherige">
				<?php echo kc_svg( 'slide-prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="zimmer-swiper__next swiper-nav-btn" aria-label="Nächste">
				<?php echo kc_svg( 'slide-next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<div class="zimmer-swiper swiper"
				aria-roledescription="Karussell"
				aria-label="Behandlungszimmer">
				<div class="swiper-wrapper">
					<?php
					$rn = 0;
					while ( have_rows( 'rooms' ) ) :
						the_row();
						++$rn;
						$c = get_sub_field( 'color' ); // g | y | o | b | l
						?>
						<div class="swiper-slide" role="group" aria-roledescription="Folie" aria-label="Zimmer <?php echo esc_attr( (string) $rn ); ?>">
							<div class="room <?php echo esc_attr( $c ); ?>">
								<span class="room-nr"><?php echo esc_html( sprintf( '%02d', $rn ) ); ?></span>
								<span class="rh"><?php echo kc_icon( 'room_' . $c ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<b><?php echo esc_html( get_sub_field( 'name' ) ); ?></b>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
			</div>

			<div class="zimmer-swiper__pagination"></div>

		</div>
		<?php endif; ?>

	</div>
</section>
