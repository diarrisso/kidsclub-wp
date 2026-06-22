<?php
/**
 * footer.php — Footer (Design): Logo · Adresse · Öffnungszeiten · Termin-Buchung (QR + Button),
 * darunter Social · Rechtliches · Copyright. Inhalte aus Theme-Einstellungen (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$addr  = get_field( 'footer_address', 'option' );
$phone = get_field( 'footer_phone', 'option' );
$hours = get_field( 'footer_hours', 'option' );
// Termin-Button im Footer: NULL = noch nie konfiguriert → Standard anzeigen;
// '' (leer gespeichert) = vom Admin bewusst ausgeblendet; sonst der eingegebene Text.
$booking_btn = get_field( 'footer_booking_btn', 'option' );
if ( null === $booking_btn ) {
	$booking_btn = 'Termin buchen';
}
$social = get_field( 'footer_social', 'option' ) ?: array();
$legal  = get_field( 'footer_legal', 'option' ) ?: array();
$copy   = get_field( 'footer_copyright', 'option' ) ?: 'Kids Club by ZACP';

$soc_icons = array(
	'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-2 0-3 1.5-3 3.5V11H9v3h2v7h3v-7h2.5l.5-3H14V9.5c0-.3.2-.5.5-.5Z"/></svg>',
	'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 4c.3 2.2 1.8 3.9 4 4.2v2.9c-1.5.1-2.9-.4-4-1.2v5.6c0 3.1-2.5 5.5-5.6 5.3-2.7-.2-4.8-2.5-4.8-5.2 0-3 2.6-5.4 5.6-5.2v3c-.4-.1-.8-.2-1.2-.1-1.2.1-2 1.1-1.9 2.3.1 1.1 1 1.9 2.1 1.9 1.3 0 2.2-1 2.2-2.3V4Z"/></svg>',
);

$phone_digits = preg_replace( '/[^+\d]/', '', (string) $phone );
$qr_url       = get_theme_file_uri( 'assets/img/qr-placeholder.svg' );

// Cache-Bust für SVGs (Browser cachen <img>-SVGs nach Dateiname, nicht via $ver).
$logo_path = get_theme_file_path( 'assets/img/logo-quer-white.svg' );
$logo_url  = get_theme_file_uri( 'assets/img/logo-quer-white.svg' ) . '?v=' . ( file_exists( $logo_path ) ? filemtime( $logo_path ) : '1' );
?>
<footer class="site-footer">
	<div class="container footer-inner">

		<div class="footer-top">

			<!-- Col 1: Logo -->
			<div class="footer-logo-col">
				<img src="<?php echo esc_url( $logo_url ); ?>"
					alt="Kids Club by zacp" class="footer-logo" width="170" height="49">
			</div>

			<!-- Col 2: Adresse -->
			<div class="footer-col footer-address">
				<p class="footer-col-title">Kontakt</p>
				<?php if ( $addr ) : ?>
					<address><?php echo nl2br( esc_html( $addr ) ); ?></address>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<div class="footer-contact-line">
						<span class="fc-key"><?php echo kc_svg( 'phone', 'Telefon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<a href="tel:<?php echo esc_attr( $phone_digits ); ?>"><?php echo esc_html( $phone ); ?></a>
					</div>
				<?php endif; ?>
				<div class="footer-contact-line">
					<span class="fc-key"><?php echo kc_svg( 'email', 'E-Mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<a href="mailto:info@zacp.de">info@zacp.de</a>
				</div>
			</div>

			<!-- Col 3: Öffnungszeiten -->
			<div class="footer-col footer-hours">
				<p class="footer-col-title">Öffnungszeiten</p>
				<?php if ( $hours ) : ?>
					<p><?php echo nl2br( esc_html( $hours ) ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Col 4: Online Termin QR + Text -->
			<div class="footer-col footer-booking">
				<p class="footer-col-title">Online Termin</p>
				<div class="footer-booking__card">
					<div class="footer-booking__head">
						<img class="footer-qr" src="<?php echo esc_url( $qr_url ); ?>" alt="QR-Code für die Online-Terminbuchung" width="92" height="92">
						<p class="footer-booking__text">Bequem mit dem Smartphone den Termin buchen.</p>
					</div>
					<?php if ( ! empty( $booking_btn ) ) : ?>
					<button type="button" class="btn btn-primary footer-booking__btn" data-booking-open aria-haspopup="dialog">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
						<?php echo esc_html( $booking_btn ); ?>
					</button>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<div class="footer-bottom">
			<?php if ( $social ) : ?>
			<div class="footer-bottom__social">
				<?php
				foreach ( $social as $s ) :
					$ic = $soc_icons[ $s['network'] ] ?? '';
					?>
					<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $s['network'] ) ); ?>"><?php echo $ic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- statisches Inline-SVG ?></a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="footer-bottom__legal">
				<?php foreach ( $legal as $l ) : ?>
					<a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
				<?php endforeach; ?>
			</div>

			<div class="footer-bottom__copy">&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $copy ); ?></div>
		</div>

	</div>
</footer>

<?php if ( shortcode_exists( 'masinga_booking' ) ) : ?>
<div id="bookingModal" class="booking-modal" role="dialog" aria-modal="true" aria-label="Online Termin buchen" hidden>
	<div class="booking-modal__backdrop" id="bookingBackdrop"></div>
	<div class="booking-modal__card">
		<button class="booking-modal__close" id="bookingClose" aria-label="Schließen">
			<?php echo kc_svg( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<div class="booking-modal__body">
			<?php echo do_shortcode( '[masinga_booking]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML généré par masinga-booking, API-URL échappée via esc_url() ?>
		</div>
	</div>
</div>
<?php endif; ?>

<button class="back-to-top" id="backToTop" aria-label="Nach oben" aria-hidden="true" tabindex="-1">
	<?php echo kc_svg( 'back-to-top' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
