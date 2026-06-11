<?php
/**
 * footer.php — Compact 4-col footer with accent bar + booking QR.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$social = get_field( 'footer_social', 'option' ) ?: [];
$legal  = get_field( 'footer_legal', 'option' ) ?: [];
$copy   = get_field( 'footer_copyright', 'option' ) ?: 'Kids Club by zacp · Alle Rechte vorbehalten.';

$soc_icons = [
	'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-2 0-3 1.5-3 3.5V11H9v3h2v7h3v-7h2.5l.5-3H14V9.5c0-.3.2-.5.5-.5Z"/></svg>',
	'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 4c.3 2.2 1.8 3.9 4 4.2v2.9c-1.5.1-2.9-.4-4-1.2v5.6c0 3.1-2.5 5.5-5.6 5.3-2.7-.2-4.8-2.5-4.8-5.2 0-3 2.6-5.4 5.6-5.2v3c-.4-.1-.8-.2-1.2-.1-1.2.1-2 1.1-1.9 2.3.1 1.1 1 1.9 2.1 1.9 1.3 0 2.2-1 2.2-2.3V4Z"/></svg>',
];

$qr_img = get_theme_file_uri( 'assets/img/booking-qr.svg' );
?>
<footer class="site-footer">
	<div class="footer-accent"></div>
	<div class="container footer-inner">

		<div class="footer-top">

			<!-- Col 1: Brand -->
			<div class="footer-brand">
				<svg class="arch" viewBox="0 0 120 132" fill="none" aria-hidden="true" width="44" height="48"><path class="a-navy" style="stroke:#fff" d="M16 17 Q60 9 106 16"/><path class="a-pink" d="M19 27 Q60 20 101 26"/><path class="a-navy" style="stroke:#fff" d="M24 122 L24 62 Q24 30 60 30 Q96 30 96 62 L96 122"/><path class="a-pink" d="M40 122 L40 64 Q40 46 60 46 Q80 46 80 64 L80 122"/><path class="h-fill" d="M60 100 C49 88 43 82 43 73 C43 66 48 62 53.5 62 C57 62 59 64.5 60 67 C61 64.5 63 62 66.5 62 C72 62 77 66 77 73 C77 82 71 88 60 100 Z"/></svg>
				<div class="fname">Kids Club<small>by zacp</small></div>
				<p>Kinder- und Jugendzahnheilkunde im Herzen von Osnabrück.</p>
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

			<!-- Col 2: Navigation -->
			<div class="footer-col">
				<h5>Navigation</h5>
				<a href="<?php echo esc_url( home_url( '/#leistungen' ) ); ?>">Leistungen</a>
				<a href="<?php echo esc_url( home_url( '/#praxis' ) ); ?>">Unsere Praxis</a>
				<a href="<?php echo esc_url( home_url( '/#team' ) ); ?>">Team</a>
				<a href="<?php echo esc_url( home_url( '/#eltern' ) ); ?>">Für Eltern</a>
				<a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>">FAQ</a>
				<a href="<?php echo esc_url( home_url( '/#kontakt' ) ); ?>">Kontakt</a>
			</div>

			<!-- Col 3: Kontakt -->
			<div class="footer-col">
				<h5>Kontakt</h5>
				<div class="footer-contact-line">
					<span class="footer-contact-icon" aria-hidden="true">📍</span>
					<span>Am Kirchenkamp 3<br>49078 Osnabrück</span>
				</div>
				<div class="footer-contact-line">
					<span class="footer-contact-icon" aria-hidden="true">📞</span>
					<a href="tel:+4954147140">+49 (0) 541 471 40</a>
				</div>
				<div class="footer-contact-line">
					<span class="footer-contact-icon" aria-hidden="true">🕐</span>
					<span>Mo.–Do. 08–13 &amp; 14–18 Uhr<br>Fr. 08–13 Uhr</span>
				</div>
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
				<button type="button" class="footer-qr-btn" data-booking-open aria-haspopup="dialog">Termin buchen →</button>
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

<?php wp_footer(); ?>
</body>
</html>
