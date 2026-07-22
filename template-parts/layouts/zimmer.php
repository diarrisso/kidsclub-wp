<?php
/**
 * Layout: 5 Behandlungszimmer — farbige Kreise (statisch).
 *
 * Der Kreis trägt den NAMEN aus dem Feld (Sonne, Eismeer …), der erklärende Text
 * steht dauerhaft darunter — kein Mouseover, kein Tap, kein Aufklappen: der Text
 * war so auf Touch-Geräten nur per Tap erreichbar und blieb sonst unsichtbar.
 * Da nichts mehr aufklappt, sind die Kreise auch keine Bedienelemente mehr
 * (kein tabindex/role/aria-expanded — ein Button, der nichts tut, führt in die Irre).
 * Kein Karussell: Desktop = 5 Kreise nebeneinander, Mobile = Umbruch (flex-wrap).
 * Felder: eyebrow, title, text, rooms[] (name, color, beschreibung).
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
			while ( have_rows( 'rooms' ) ) :
				the_row();
				$c    = (string) get_sub_field( 'color' ); // g | y | o | b | l
				$name = trim( (string) get_sub_field( 'name' ) );
				$desc = (string) get_sub_field( 'beschreibung' );
				if ( '' === $name ) {
					continue; // Ohne Namen hätte der Kreis keine Beschriftung — Zeile überspringen.
				}
				?>
				<li class="rooms-circles__item">
					<?php
					/*
					 * Der Kreis trägt den Namen aus dem Feld. Früher stand hier ein im Template
					 * fest verdrahtetes „Raum %d“ — die Redaktion konnte das Wort nicht ändern,
					 * und die Nummer sagte nichts über den Raum aus.
					 */
					?>
					<div class="room <?php echo esc_attr( $c ); ?>">
						<span class="room__name"><?php echo esc_html( $name ); ?></span>
					</div>
					<?php if ( $desc ) : ?>
						<div class="room__caption">
							<p class="room__desc"><?php echo wp_kses( $desc, [ 'strong' => [] ] ); ?></p>
						</div>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php endif; ?>

	</div>
</section>
