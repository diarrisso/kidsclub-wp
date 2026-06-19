<?php
/**
 * Layout: Termin buchen — QR-Code + Buchungs-Embed
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = get_sub_field( 'tr_eyebrow' );
$title      = get_sub_field( 'tr_title' );
$text       = get_sub_field( 'tr_text' );
$embed_code = get_sub_field( 'embed_code' );
?>
<section class="section-termin reveal" id="termin">
	<div class="container">
		<?php
		if ( $eyebrow ) :
			?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( $text ) :
			?>
			<p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
		<div class="termin-layout">
			<?php if ( shortcode_exists( 'masinga_booking' ) ) : ?>
			<div class="termin-cta">
				<button type="button" class="btn btn-primary btn-lg" data-booking-open aria-haspopup="dialog">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
					Buchen
				</button>
			</div>
			<?php elseif ( $embed_code ) : ?>
			<div class="termin-embed">
				<?php
				// iframe-only allowlist — <script> intentionally excluded (XSS risk).
				// Booking tools like Doctolib provide an <iframe> embed option.
				$allowed_embed = [
					'iframe' => [
						'src'             => true,
						'width'           => true,
						'height'          => true,
						'frameborder'     => true,
						'allowfullscreen' => true,
						'loading'         => true,
						'title'           => true,
						'style'           => true,
					],
				];
				$embed_html    = wp_kses( $embed_code, $allowed_embed );

				if ( kc_embed_hosts_allowed( $embed_html ) ) {
					echo $embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- deja filtre par wp_kses iframe-only + allowlist host Doctolib
				} elseif ( current_user_can( 'edit_pages' ) ) {
					echo '<p class="termin-embed__error">⚠️ Embed-Code abgelehnt: nur Doctolib-iframes (https://…doctolib.de/.fr) sind erlaubt. (Hinweis nur für Redakteure sichtbar.)</p>';
				}
				?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
