<?php
/**
 * Layout: Leistungsspektrum.
 * Felder: eyebrow, title, text, accordion_title, overlay_transition, items[]
 *   items: card_color, card_icon, symbol, heading, body,
 *          overlay_enabled, overlay_button, overlay_title, overlay_intro,
 *          overlay_slides[] (image, caption), overlay_slides_position (oben|mitte|unten),
 *          overlay_sections[] (title, body)
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
						<?php
						// Priorität: eigener Upload → Spray-Symbol → Linien-Glyphe.
						$cic_url  = is_array( $card['card_icon_custom'] ?? null ) ? (string) ( $card['card_icon_custom']['url'] ?? '' ) : '';
						$cic_slug = (string) ( $card['card_icon'] ?? '' );
						$cic_html = '' !== $cic_url ? kc_icon_mask_url( $cic_url ) : ( '' === $cic_slug ? '' : ( 0 === strpos( $cic_slug, 'symbol' ) ? kc_symbol_mask( $cic_slug ) : kc_icon( $cic_slug ) ) );
						?>
						<?php if ( '' !== $cic_html ) : ?>
							<span class="ls2-card__ic" aria-hidden="true"><?php echo $cic_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
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
							aria-expanded="false"
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
	// Eigener Präfix pro Overlay: mehrere Overlays liegen gleichzeitig im DOM, ein
	// gemeinsamer Präfix würde die Pfeil-/Pagination-Klassen kollidieren lassen.
	$sw_prefix = $ov_id . '-sw';
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

			<?php
			/**
			 * Bilder-Slider. Nur Zeilen mit echtem Bild zählen — eine leere Repeater-Zeile
			 * würde sonst eine leere Folie erzeugen. Ab zwei Bildern Navigation + Pagination;
			 * ein einzelnes Bild braucht keinen Slider und wird direkt gezeigt.
			 * Swiper wird ERST beim Öffnen initialisiert (siehe kidsclub.js): in einem
			 * `hidden` Overlay misst Swiper eine Breite von 0 und alle Folien überlagern sich.
			 */
			$slides = array_values(
				array_filter(
					(array) ( $card['overlay_slides'] ?? [] ),
					static function ( $s ) {
						return ! empty( $s['image']['url'] );
					}
				)
			);
			// Position redaktionell wählbar. Der Slider wird EINMAL gebaut und dann an der
			// gewünschten Stelle ausgegeben — zwei Markup-Kopien würden früher oder später
			// auseinanderlaufen. Unbekannter/leerer Wert => 'mitte' (Vorgabe des Kunden).
			$slides_pos = (string) ( $card['overlay_slides_position'] ?? '' );
			$slides_pos = in_array( $slides_pos, [ 'oben', 'mitte', 'unten' ], true ) ? $slides_pos : 'mitte';
			ob_start();
			?>
			<?php if ( $slides ) : ?>
				<?php $multi = count( $slides ) > 1; ?>
				<div class="lsov__media<?php echo $multi ? '' : ' lsov__media--single'; ?>">
					<div class="lsov-swiper swiper"<?php echo $multi ? ' data-lsov-swiper' : ''; ?>>
						<div class="swiper-wrapper"<?php echo $multi ? ' aria-live="polite"' : ''; ?>>
							<?php foreach ( $slides as $si => $slide ) : ?>
								<figure class="swiper-slide lsov-slide">
									<?php
									echo wp_get_attachment_image(
										(int) $slide['image']['ID'],
										'large',
										false,
										[
											'class'   => 'lsov-slide__img',
											'loading' => 'lazy',
											'alt'     => esc_attr( (string) ( $slide['image']['alt'] ?? '' ) ),
										]
									);
									?>
									<?php if ( ! empty( $slide['caption'] ) ) : ?>
										<figcaption class="lsov-slide__cap"><?php echo esc_html( $slide['caption'] ); ?></figcaption>
									<?php endif; ?>
									<?php if ( $multi ) : ?>
										<span class="screen-reader-text"><?php printf( 'Folie %1$d von %2$d', (int) $si + 1, count( $slides ) ); ?></span>
									<?php endif; ?>
								</figure>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if ( $multi ) : ?>
						<?php
						get_template_part(
							'template-parts/components/swiper-nav',
							null,
							[ 'prefix' => $sw_prefix ]
						);
						get_template_part(
							'template-parts/components/swiper-pagination',
							null,
							[ 'prefix' => $sw_prefix ]
						);
						?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php
			$slider_html = (string) ob_get_clean();

			/**
			 * Ein Raster wird mehrfach gebraucht (bei „mitte“ zweimal), darum EINE
			 * Funktion statt zweier Markup-Kopien. Jedes Raster zählt seine Kinder
			 * selbst: die Trennlinie ab dem dritten Abschnitt (CSS nth-child) gilt
			 * damit pro Raster, die erste Zeile bleibt jeweils ohne Linie.
			 */
			$render_grid = static function ( array $secs ) {
				if ( ! $secs ) {
					return;
				}
				echo '<div class="lsov__grid">';
				foreach ( $secs as $sec ) {
					// Kein Icon mehr: das Symbol steht bereits auf der Karte, die zum Overlay führt.
					echo '<div class="lsov__sec">';
					if ( ! empty( $sec['title'] ) ) {
						echo '<h3 class="lsov__sec-title">' . esc_html( $sec['title'] ) . '</h3>';
					}
					echo '<div class="lsov__prose">' . wp_kses_post( (string) ( $sec['body'] ?? '' ) ) . '</div>';
					echo '</div>';
				}
				echo '</div>';
			};

			if ( 'oben' === $slides_pos ) {
				echo $slider_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oben zusammengesetzt, jeder Wert dort einzeln escaped
				$render_grid( $sections );
			} elseif ( 'unten' === $slides_pos ) {
				$render_grid( $sections );
				echo $slider_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- siehe oben
			} else {
				// „mitte“: nach den ersten beiden Textblöcken, also nach der ersten Rasterzeile.
				$render_grid( array_slice( $sections, 0, 2 ) );
				echo $slider_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- siehe oben
				$render_grid( array_slice( $sections, 2 ) );
			}
			?>
		</div>
	</div>
<?php endforeach; ?>
