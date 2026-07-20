<?php
/**
 * Layout: Leistungsspektrum.
 * Felder: eyebrow, title, text, accordion_title, overlay_transition, items[]
 *   items: card_color, card_icon, symbol, heading, body,
 *          overlay_enabled, overlay_button, overlay_title, overlay_intro, overlay_sections[] (icon, title, body)
 *
 * "card_color" steuert die Pastell-Hintergrundfarbe der Karte (yellow|blue|green|pink).
 * "card_icon"  = weißes Linien-Icon oben rechts (kc_icon: zahn|buerste|smiley|gebiss|herz).
 *
 * Nur die ersten 4 Einträge werden als Karten dargestellt (Design-Vorgabe: max. 4 Karten).
 * Ab dem 5. Eintrag erscheinen die restlichen Leistungen als Akkordeon darunter.
 *
 * "mehr"-Overlay: ist "overlay_enabled" gesetzt, zeigt die Karte einen "mehr"-Button, der ein
 * Vollbild-Overlay öffnet (gleitet von rechts). Die Overlays werden AUSSERHALB des .container
 * gerendert (position:fixed) — Verhalten via assets/js/kidsclub.js (kc-lsov).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items           = get_sub_field( 'items' ) ?: [];
$cards           = array_slice( $items, 0, 4 );
$accordion_items = array_slice( $items, 4 );
$accordion_title = get_sub_field( 'accordion_title' );

$ov_duration = (int) ( get_sub_field( 'overlay_transition' ) ?: 1050 );
$ov_duration = max( 200, min( 3000, $ov_duration ) );

// Eindeutiger Präfix pro Sektion-Instanz — falls „leistungen“ mehrfach im Flexible Content steht,
// dürfen die Overlay-IDs nicht kollidieren (sonst öffnet „mehr“ das falsche Overlay).
$inst      = (int) ( $GLOBALS['kc_ls_inst'] = ( $GLOBALS['kc_ls_inst'] ?? 0 ) + 1 );
$ov_prefix = 'lsov-' . $inst . '-';

// Aktivierte Overlays einsammeln, um sie nach dem Abschnitt (fixed) zu rendern.
$overlays = [];
foreach ( $cards as $idx => $card ) {
	if ( ! empty( $card['overlay_enabled'] ) ) {
		$overlays[ $ov_prefix . ( $idx + 1 ) ] = $card;
	}
}

$close_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 5l14 14M19 5L5 19"/></svg>';
?>

<section class="section" id="leistungen">
	<div class="container">
		<div class="section-head center reveal">
			<span class="eyebrow"><?php echo esc_html( get_sub_field( 'eyebrow' ) ); ?></span>
			<h2 class="section-title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
			<?php
			$t = get_sub_field( 'text' );
			if ( $t ) :
				?>
				<p class="lead"><?php echo wp_kses( $t, [ 'strong' => [] ] ); ?></p><?php endif; ?>
		</div>

		<div class="ls2-grid">
			<?php
			foreach ( $cards as $idx => $card ) :
				$color  = $card['card_color'] ?: 'yellow';
				$ov_id  = $ov_prefix . ( $idx + 1 );
				$has_ov = ! empty( $card['overlay_enabled'] );
				?>
				<article class="ls2-card ls2-card--<?php echo esc_attr( $color ); ?> reveal">
					<div class="ls2-card__head">
						<h3 class="ls2-card__title"><?php echo esc_html( $card['heading'] ); ?></h3>
						<?php if ( ! empty( $card['card_icon'] ) ) : ?>
							<span class="ls2-card__ic" aria-hidden="true"><?php echo 0 === strpos( $card['card_icon'], 'symbol' ) ? kc_symbol_mask( $card['card_icon'] ) : kc_icon( $card['card_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
					</div>
					<div class="ls2-card__body"><?php echo wp_kses_post( (string) ( $card['body'] ?? '' ) ); ?></div>
					<?php if ( $has_ov ) : ?>
						<span class="ls2-card__spacer"></span>
						<?php $ov_label = $card['overlay_title'] ?: $card['heading']; ?>
						<button class="ls2-card__more" type="button"
								data-lsov-open="<?php echo esc_attr( $ov_id ); ?>"
								aria-label="<?php echo esc_attr( sprintf( 'Mehr über %s erfahren', $ov_label ) ); ?>">
							<span><?php echo esc_html( $card['overlay_button'] ?: 'mehr' ); ?></span>
							<span class="ls2-card__more-ic" aria-hidden="true"><?php echo kc_svg( 'accordion-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( $accordion_items ) : ?>
		<div class="ls-accordion-wrap">
			<?php if ( $accordion_title ) : ?>
				<h2 class="section-title ls-accordion-title"><?php echo esc_html( $accordion_title ); ?></h2>
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
						<div class="ls-acc-body"><?php echo wp_kses_post( (string) ( $item['body'] ?? '' ) ); ?></div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php
// „mehr“-Overlays: außerhalb des .container (position:fixed), Verhalten via kidsclub.js.
foreach ( $overlays as $ov_id => $card ) :
	$color    = $card['card_color'] ?: 'yellow';
	$ov_title = $card['overlay_title'] ?: $card['heading'];
	$sections = $card['overlay_sections'] ?: [];
	?>
	<div class="lsov lsov--<?php echo esc_attr( $color ); ?>"
		id="<?php echo esc_attr( $ov_id ); ?>"
		role="dialog" aria-modal="true"
		aria-labelledby="<?php echo esc_attr( $ov_id ); ?>-t"
		style="--lsov-duration:<?php echo esc_attr( (string) $ov_duration ); ?>ms"
		hidden>
		<div class="lsov__inner">
			<div class="lsov__bar">
				<h2 class="lsov__title" id="<?php echo esc_attr( $ov_id ); ?>-t"><?php echo esc_html( $ov_title ); ?></h2>
				<button class="lsov__close" type="button" data-lsov-close aria-label="Schließen"><?php echo $close_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
			<?php $intro = $card['overlay_intro'] ?? ''; ?>
			<?php if ( $intro ) : ?>
				<div class="lsov__intro"><?php echo wp_kses_post( (string) $intro ); ?></div>
			<?php endif; ?>
			<?php if ( $sections ) : ?>
				<div class="lsov__grid">
					<?php foreach ( $sections as $sec ) : ?>
						<div class="lsov__sec">
							<?php $sic = (string) ( $sec['icon'] ?? '' ); ?>
							<span class="lsov__ic" aria-hidden="true"><?php echo '' === $sic ? '' : ( 0 === strpos( $sic, 'symbol' ) ? kc_symbol_mask( $sic ) : kc_icon( $sic ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div>
								<?php if ( ! empty( $sec['title'] ) ) : ?>
									<h3 class="lsov__sec-title"><?php echo esc_html( $sec['title'] ); ?></h3>
								<?php endif; ?>
								<div class="lsov__prose"><?php echo wp_kses_post( (string) ( $sec['body'] ?? '' ) ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
