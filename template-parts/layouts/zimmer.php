<?php
/**
 * Layout: 5 Zimmer.
 * Felder: eyebrow, title, text, rooms[] (name, theme, color)
 */
if ( ! defined( 'ABSPATH' ) ) exit; ?>

<section class="section" id="zimmer" style="padding-top:0">
	<div class="container">
		<div class="rooms reveal">
			<div class="section-head center" style="margin-bottom:30px">
				<span class="eyebrow"><?php echo kc_icon( 'heart' ); ?><?php echo esc_html( get_sub_field( 'eyebrow' ) ); ?></span>
				<h2 class="section-title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
				<?php if ( $t = get_sub_field( 'text' ) ) : ?><p class="lead"><?php echo esc_html( $t ); ?></p><?php endif; ?>
			</div>

			<div class="rooms-grid">
				<?php while ( have_rows( 'rooms' ) ) : the_row();
					$c = get_sub_field( 'color' ); // g | y | o | b | l
				?>
					<div class="room <?php echo esc_attr( $c ); ?>">
						<span class="rh"><?php echo kc_icon( 'room_' . $c ); ?></span>
						<b><?php echo esc_html( get_sub_field( 'name' ) ); ?></b>
						<span><?php echo esc_html( get_sub_field( 'theme' ) ); ?></span>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</section>
