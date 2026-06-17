<?php
/**
 * footer.php — Footer (Design): Logo · Adresse · Öffnungszeiten · Termin-Buchung (QR + Button),
 * darunter Social · Rechtliches · Copyright. Inhalte aus Theme-Einstellungen (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$addr   = get_field( 'footer_address', 'option' );
$phone  = get_field( 'footer_phone', 'option' );
$hours  = get_field( 'footer_hours', 'option' );
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
	<div class="footer-accent"></div>
	<div class="container footer-inner">

		<div class="footer-top">

			<!-- Col 1: Logo -->
			<div class="footer-logo-col">
				<img src="<?php echo esc_url( $logo_url ); ?>"
					alt="Kids Club by zacp" class="footer-logo" width="170" height="49">
			</div>

			<!-- Col 2: Adresse -->
			<div class="footer-col footer-address">
				<?php if ( $addr ) : ?>
					<address><?php echo nl2br( esc_html( $addr ) ); ?></address>
				<?php endif; ?>
				<?php if ( $phone ) : ?>
					<div class="footer-contact-line"><span class="fc-key">T</span><a href="tel:<?php echo esc_attr( $phone_digits ); ?>"><?php echo esc_html( $phone ); ?></a></div>
				<?php endif; ?>
				<div class="footer-contact-line"><span class="fc-key">E</span><a href="mailto:info@zacp.de">info@zacp.de</a></div>
			</div>

			<!-- Col 3: Öffnungszeiten -->
			<div class="footer-col footer-hours">
				<?php if ( $hours ) : ?>
					<p><?php echo nl2br( esc_html( $hours ) ); ?></p>
				<?php endif; ?>
			</div>

			<!-- Col 4: Termin bequem per Smartphone buchen (QR + Button) -->
			<div class="footer-col footer-booking">
				<div class="footer-booking__head">
					<p class="footer-booking__text">Bequem mit dem Smartphone den Termin buchen.</p>
					<img class="footer-qr" src="<?php echo esc_url( $qr_url ); ?>" alt="QR-Code für die Online-Terminbuchung" width="92" height="92">
				</div>
				<button type="button" class="btn btn-primary footer-booking__btn" data-booking-open aria-haspopup="dialog">Buchen</button>
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
		<div class="booking-modal__body">
			<?php echo do_shortcode( '[masinga_booking]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML généré par masinga-booking, API-URL échappée via esc_url() ?>
		</div>
	</div>
</div>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
