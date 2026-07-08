<?php
/**
 * Layout: Leistungsspektrum.
 * Felder: eyebrow, title, text, accordion_title, items[] (card_color, symbol, heading, body)
 *
 * "card_color" steuert die Pastell-Hintergrundfarbe der Karte (yellow|blue|green|pink).
 * "symbol" wählt die Symbol-Illustration via kc_symbol() (symbol1..symbol5).
 *
 * Nur die ersten 4 Einträge werden als Karten dargestellt (Design-Vorgabe: max. 4 Karten).
 * Ab dem 5. Eintrag erscheinen die restlichen Leistungen als Akkordeon darunter, in derselben
 * Sektion — reutilisiert die generische .accordion/.accordion-item-Struktur (wie FAQ/Eltern),
 * kein zweites Akkordeon-System.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items           = get_sub_field( 'items' ) ?: [];
$cards           = array_slice( $items, 0, 4 );
$accordion_items = array_slice( $items, 4 );
$accordion_title = get_sub_field( 'accordion_title' );
?>

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
			<?php foreach ( $cards as $card ) : ?>
				<article class="svc svc--<?php echo esc_attr( $card['card_color'] ?: 'yellow' ); ?> reveal">
					<?php echo wp_kses_post( kc_symbol( $card['symbol'] ?: 'symbol1' ) ); ?>
					<h3><?php echo esc_html( $card['heading'] ); ?></h3>
					<p><?php echo wp_kses( $card['body'], [ 'strong' => [] ] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( $accordion_items ) : ?>
		<div class="ls-accordion-wrap">
			<?php if ( $accordion_title ) : ?>
				<span class="eyebrow ls-accordion-title"><?php echo esc_html( $accordion_title ); ?></span>
			<?php endif; ?>
			<div class="accordion accordion--plain ls-accordion" x-data="{ open: null }" x-cloak>
				<?php foreach ( $accordion_items as $i => $item ) : ?>
				<div class="accordion-item">
					<button class="accordion-trigger"
							@click="open === <?php echo absint( $i ); ?> ? open = null : open = <?php echo absint( $i ); ?>"
							:aria-expanded="open === <?php echo absint( $i ); ?>"
							aria-controls="ls-panel-<?php echo absint( $i ); ?>"
							type="button">
						<span><?php echo esc_html( $item['heading'] ); ?></span>
						<span class="accordion-icon" aria-hidden="true"
							:class="open === <?php echo absint( $i ); ?> ? 'is-open' : ''">
							<span class="icon-plus"><?php echo kc_svg( 'accordion-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="icon-minus"><?php echo kc_svg( 'accordion-close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</span>
					</button>
					<div class="accordion-panel"
						id="ls-panel-<?php echo absint( $i ); ?>"
						x-show="open === <?php echo absint( $i ); ?>"
						x-transition>
						<p><?php echo wp_kses( $item['body'], [ 'strong' => [] ] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>
