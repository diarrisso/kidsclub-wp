<?php
/**
 * Layout: 5 Zimmer — Swiper Karussell (DIE PRAXIS).
 * Felder: eyebrow, title, text, rooms[] (name, theme, color)
 * Reusable components: swiper-nav + swiper-pagination (prefix: zimmer-swiper)
 * Slider läuft full-bleed (außerhalb des .container), 2 volle Slides + Peek.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pre-count slides for aria-label "Folie X von Y".
$rooms_rows  = get_sub_field( 'rooms' );
$rooms_total = is_array( $rooms_rows ) ? count( $rooms_rows ) : 0;
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
	</div><!-- /.container : Slider läuft full-bleed außerhalb des Containers -->

	<div class="swiper zimmer-swiper" aria-roledescription="Karussell" aria-label="<?php echo esc_attr( get_sub_field( 'title' ) ?: 'Zimmer' ); ?>">
		<div class="swiper-wrapper" aria-live="polite">
			<?php
			$rn = 0;
			while ( have_rows( 'rooms' ) ) :
				the_row();
				++$rn;
				$c = get_sub_field( 'color' ); // g | y | o | b | l
				?>
				<div class="swiper-slide room <?php echo esc_attr( $c ); ?>" role="group" aria-label="<?php echo esc_attr( sprintf( 'Folie %d von %d', $rn, $rooms_total ) ); ?>">
					<span class="room-nr"><?php echo esc_html( sprintf( '%02d', $rn ) ); ?></span>
					<span class="rh"><?php echo kc_icon( 'room_' . $c ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG ?></span>
					<b><?php echo esc_html( get_sub_field( 'name' ) ); ?></b>
					<span class="room-motto"><?php echo esc_html( get_sub_field( 'theme' ) ); ?></span>
				</div>
			<?php endwhile; ?>
		</div>
		<?php get_template_part( 'template-parts/components/swiper-pagination', null, array( 'prefix' => 'zimmer-swiper' ) ); ?>
		<?php get_template_part( 'template-parts/components/swiper-nav', null, array( 'prefix' => 'zimmer-swiper' ) ); ?>
	</div>
</section>
