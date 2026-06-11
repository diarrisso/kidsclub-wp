<?php
/**
 * footer.php — Compact 4-col footer with accent bar + booking QR.
 * All content managed in Theme-Einstellungen (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about  = get_field( 'footer_about', 'option' );
$cols   = get_field( 'footer_cols', 'option' ) ?: [];
$addr   = get_field( 'footer_address', 'option' ) ?: 'Am Kirchenkamp 3, 49078 Osnabrück';
$phone  = get_field( 'footer_phone', 'option' ) ?: '+49 (0) 541 471 40';
$hours  = get_field( 'footer_hours', 'option' ) ?: "Mo.\u{2013}Do. 08\u{2013}13 & 14\u{2013}18 Uhr\nFr. 08\u{2013}13 Uhr";
$social = get_field( 'footer_social', 'option' ) ?: [];
$legal  = get_field( 'footer_legal', 'option' ) ?: [];
$copy   = get_field( 'footer_copyright', 'option' ) ?: 'Kids Club by zacp · Alle Rechte vorbehalten.';

$nav_col = ! empty( $cols ) ? $cols[0] : null;

$soc_icons = [
	'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-2 0-3 1.5-3 3.5V11H9v3h2v7h3v-7h2.5l.5-3H14V9.5c0-.3.2-.5.5-.5Z"/></svg>',
	'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 4c.3 2.2 1.8 3.9 4 4.2v2.9c-1.5.1-2.9-.4-4-1.2v5.6c0 3.1-2.5 5.5-5.6 5.3-2.7-.2-4.8-2.5-4.8-5.2 0-3 2.6-5.4 5.6-5.2v3c-.4-.1-.8-.2-1.2-.1-1.2.1-2 1.1-1.9 2.3.1 1.1 1 1.9 2.1 1.9 1.3 0 2.2-1 2.2-2.3V4Z"/></svg>',
];

$qr_img       = get_theme_file_uri( 'assets/img/booking-qr.svg' );
$phone_digits = preg_replace( '/[^+\d]/', '', $phone );
?>
<footer class="site-footer">
	<div class="footer-accent"></div>
	<div class="container footer-inner">

		<div class="footer-top">

			<!-- Col 1: Brand -->
			<div class="footer-brand">
				<svg class="arch" viewBox="0 0 120 132" fill="none" aria-hidden="true" width="44" height="48"><path class="a-navy" style="stroke:#fff" d="M16 17 Q60 9 106 16"/><path class="a-pink" d="M19 27 Q60 20 101 26"/><path class="a-navy" style="stroke:#fff" d="M24 122 L24 62 Q24 30 60 30 Q96 30 96 62 L96 122"/><path class="a-pink" d="M40 122 L40 64 Q40 46 60 46 Q80 46 80 64 L80 122"/><path class="h-fill" d="M60 100 C49 88 43 82 43 73 C43 66 48 62 53.5 62 C57 62 59 64.5 60 67 C61 64.5 63 62 66.5 62 C72 62 77 66 77 73 C77 82 71 88 60 100 Z"/></svg>
				<div class="fname">Kids Club<small>by zacp</small></div>
				<?php if ( $about ) : ?>
					<p><?php echo esc_html( $about ); ?></p>
				<?php endif; ?>
				<?php if ( $social ) : ?>
				<div class="footer-social">
					<?php
					foreach ( $social as $s ) :
						$ic = $soc_icons[ $s['network'] ] ?? '';
						?>
						<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $s['network'] ) ); ?>"><?php echo $ic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG hardcode statique ?></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>

			<!-- Col 2: Navigation (footer_cols[0] from Theme-Einstellungen) -->
			<?php if ( $nav_col ) : ?>
			<div class="footer-col">
				<h5><?php echo esc_html( $nav_col['heading'] ); ?></h5>
				<?php
				foreach ( $nav_col['links'] as $l ) :
					?>
					<a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<!-- Col 3: Kontakt (footer_address / footer_phone / footer_hours) -->
			<div class="footer-col">
				<h5>Kontakt</h5>
				<?php if ( $addr ) : ?>
				<div class="footer-contact-line">
					<svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
					<span><?php echo nl2br( esc_html( $addr ) ); ?></span>
				</div>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
				<div class="footer-contact-line">
					<svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
					<a href="tel:<?php echo esc_attr( $phone_digits ); ?>"><?php echo esc_html( $phone ); ?></a>
				</div>
				<?php endif; ?>
				<?php if ( $hours ) : ?>
				<div class="footer-contact-line">
					<svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					<span><?php echo nl2br( esc_html( $hours ) ); ?></span>
				</div>
				<?php endif; ?>
			</div>

			<!-- Col 4: QR Termin -->
			<div class="footer-qr">
				<h5>Online Termin</h5>
				<div class="footer-qr-box">
					<img src="<?php echo esc_url( $qr_img ); ?>"
						alt="QR-Code für Online-Terminbuchung bei Kids Club"
						width="90" height="90" loading="lazy">
				</div>
				<p class="footer-qr-label">Scanne &amp; buche direkt<br>vom Handy</p>
				<?php if ( shortcode_exists( 'masinga_booking' ) ) : ?>
				<button type="button" class="footer-qr-btn" data-booking-open aria-haspopup="dialog">Termin buchen →</button>
			<?php endif; ?>
			</div>

		</div>

		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $copy ); ?></span>
			<div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap">
				<?php foreach ( $legal as $l ) : ?>
					<a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</footer>

<?php if ( shortcode_exists( 'masinga_booking' ) ) : ?>
<div id="bookingModal" class="booking-modal" role="dialog" aria-modal="true" aria-label="Online Termin buchen" hidden>
	<div class="booking-modal__backdrop" id="bookingBackdrop"></div>
	<div class="booking-modal__card">
		<div class="booking-modal__body">
			<?php echo do_shortcode( '[masinga_booking]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML généré par masinga-booking, API-URL échappée via esc_url() ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
