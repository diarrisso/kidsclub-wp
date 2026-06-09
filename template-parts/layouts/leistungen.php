<?php
/**
 * Layout: Leistungsspektrum.
 * Felder: eyebrow, title, text, items[] (icon, heading, body)
 *
 * "icon" ist ein Slug, der auf kc_icon() (siehe inc/icons.php) gemappt wird —
 * so bleiben SVGs zentral und aus dem Editor wählbar, statt SVG-Code im Feld.
 */
if ( ! defined( 'ABSPATH' ) ) exit; ?>

<section class="section" id="leistungen">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php echo kc_icon( 'heart' ); ?><?php echo esc_html( get_sub_field( 'eyebrow' ) ); ?></span>
			<h2 class="section-title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
			<?php if ( $t = get_sub_field( 'text' ) ) : ?><p class="lead"><?php echo esc_html( $t ); ?></p><?php endif; ?>
		</div>

		<div class="services-grid">
			<?php while ( have_rows( 'items' ) ) : the_row(); ?>
				<article class="svc reveal">
					<span class="corner"></span>
					<span class="ic"><?php echo kc_icon( get_sub_field( 'icon' ) ); ?></span>
					<h3><?php echo esc_html( get_sub_field( 'heading' ) ); ?></h3>
					<p><?php echo esc_html( get_sub_field( 'body' ) ); ?></p>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
