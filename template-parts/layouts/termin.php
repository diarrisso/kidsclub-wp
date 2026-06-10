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
$qr         = get_sub_field( 'qr_image' );
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
			<?php if ( $qr ) : ?>
			<div class="termin-qr">
				<img src="<?php echo esc_url( $qr['url'] ); ?>"
					alt="QR-Code für Online-Terminbuchung"
					width="200" height="200" loading="lazy">
				<p class="termin-qr__label">QR-Code scannen</p>
			</div>
			<?php endif; ?>
			<?php if ( shortcode_exists( 'masinga_booking' ) ) : ?>
			<div class="termin-embed">
				<?php echo do_shortcode( '[masinga_booking]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML genere par le plugin masinga-booking, API-URL echappee via esc_url(). ?>
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
