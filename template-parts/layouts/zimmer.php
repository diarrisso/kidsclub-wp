<?php
/**
 * Layout: 5 Behandlungszimmer — farbige Kreise (statisch).
 *
 * Jeder Raum = ein farbiger Kreis (RAUM N + Name). Beim Mouseover / Fokus /
 * Tap erscheint der erklärende Text (Feld `beschreibung`). Kein Karussell:
 * Desktop = 5 Kreise nebeneinander, Mobile = Umbruch (flex-wrap).
 * Felder: eyebrow, title, text, rooms[] (name, theme, color, beschreibung).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rooms_rows = get_sub_field( 'rooms' );
$eyebrow    = get_sub_field( 'eyebrow' );
$title      = get_sub_field( 'title' );
$lead       = get_sub_field( 'text' );
?>
<section class="section section-zimmer reveal" id="zimmer">
	<div class="container">

		<?php if ( $eyebrow || $title || $lead ) : ?>
		<div class="section-head center">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $lead ) : ?>
				<p class="section-lead"><?php echo wp_kses( $lead, [ 'strong' => [] ] ); ?></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( $rooms_rows ) : ?>
		<ul class="rooms-circles" role="list">
			<?php
			$rn = 0;
			while ( have_rows( 'rooms' ) ) :
				the_row();
				++$rn;
				$c    = (string) get_sub_field( 'color' ); // g | y | o | b | l
				$name = (string) get_sub_field( 'name' );
				$desc = (string) get_sub_field( 'beschreibung' );
				/* translators: %d = Zimmer-Nummer */
				$label = sprintf( __( 'Raum %d', 'kidsclub' ), $rn );
				?>
				<li class="rooms-circles__item">
					<div
						class="room <?php echo esc_attr( $c ); ?><?php echo $desc ? ' has-desc' : ''; ?>"
						<?php if ( $desc ) : ?>
						tabindex="0"
						role="button"
						aria-expanded="false"
						aria-label="<?php echo esc_attr( $label . ' ' . $name ); ?>"
						<?php endif; ?>
					>
						<span class="room__label">
							<span class="room-nr"><?php echo esc_html( $label ); ?></span>
							<b class="room__name"><?php echo esc_html( $name ); ?></b>
						</span>
						<?php if ( $desc ) : ?>
							<span class="room__desc"><?php echo wp_kses( $desc, [ 'strong' => [] ] ); ?></span>
						<?php endif; ?>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php endif; ?>

	</div>
</section>
