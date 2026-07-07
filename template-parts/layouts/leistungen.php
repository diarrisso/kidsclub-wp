<?php
/**
 * Layout: Leistungsspektrum.
 * Felder: eyebrow, title, text, items[] (card_color, symbol, heading, body)
 *
 * "card_color" steuert die Pastell-Hintergrundfarbe der Karte (yellow|blue|green|pink).
 * "symbol" wählt die Symbol-Illustration via kc_symbol() (symbol1..symbol5).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="section" id="leistungen">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php echo esc_html( get_sub_field( 'eyebrow' ) ); ?></span>
			<h2 class="section-title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
			<?php
			if ( $t = get_sub_field( 'text' ) ) :
				?>
				<p class="lead"><?php echo wp_kses( $t, [ 'strong' => [] ] ); ?></p><?php endif; ?>
		</div>

		<div class="services-grid">
			<?php
			while ( have_rows( 'items' ) ) :
				the_row();
				?>
				<article class="svc svc--<?php echo esc_attr( get_sub_field( 'card_color' ) ?: 'yellow' ); ?> reveal">
					<?php echo wp_kses_post( kc_symbol( get_sub_field( 'symbol' ) ?: 'symbol1' ) ); ?>
					<h3><?php echo esc_html( get_sub_field( 'heading' ) ); ?></h3>
					<p><?php echo wp_kses( get_sub_field( 'body' ), [ 'strong' => [] ] ); ?></p>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
