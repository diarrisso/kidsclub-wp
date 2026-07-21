<?php
/**
 * Layout: 5 Behandlungszimmer — farbige Kreise (statisch).
 *
 * Der Kreis trägt NUR die Nummer (RAUM N). Name und erklärender Text stehen
 * dauerhaft DARUNTER — kein Mouseover, kein Tap, kein Aufklappen: der Text war
 * so auf Touch-Geräten nur per Tap erreichbar und blieb sonst unsichtbar.
 * Da nichts mehr aufklappt, sind die Kreise auch keine Bedienelemente mehr
 * (kein tabindex/role/aria-expanded — ein Button, der nichts tut, führt in die Irre).
 * Kein Karussell: Desktop = 5 Kreise nebeneinander, Mobile = Umbruch (flex-wrap).
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
					<div class="room <?php echo esc_attr( $c ); ?>" aria-hidden="true">
						<span class="room-nr"><?php echo esc_html( $label ); ?></span>
					</div>
					<?php // Der Kreis ist rein dekorativ (aria-hidden) — die Nummer steht hier für Screenreader mit im Text. ?>
					<div class="room__caption">
						<b class="room__name">
							<span class="screen-reader-text"><?php echo esc_html( $label . ' — ' ); ?></span>
							<?php echo esc_html( $name ); ?>
						</b>
						<?php if ( $desc ) : ?>
							<p class="room__desc"><?php echo wp_kses( $desc, [ 'strong' => [] ] ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php endif; ?>

	</div>
</section>
