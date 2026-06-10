<?php
/**
 * footer.php — Footer mit Spalten, Social Media & Rechtlichem.
 * Inhalte aus "Theme-Einstellungen" (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about  = get_field( 'footer_about', 'option' );
$cols   = get_field( 'footer_cols', 'option' ) ?: [];
$social = get_field( 'footer_social', 'option' ) ?: [];
$legal  = get_field( 'footer_legal', 'option' ) ?: [];
$copy   = get_field( 'footer_copyright', 'option' ) ?: 'Kids Club by zacp · Alle Rechte vorbehalten.';

$soc_icons = [
	'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
	'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3V6h-3c-2 0-3 1.5-3 3.5V11H9v3h2v7h3v-7h2.5l.5-3H14V9.5c0-.3.2-.5.5-.5Z"/></svg>',
	'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 4c.3 2.2 1.8 3.9 4 4.2v2.9c-1.5.1-2.9-.4-4-1.2v5.6c0 3.1-2.5 5.5-5.6 5.3-2.7-.2-4.8-2.5-4.8-5.2 0-3 2.6-5.4 5.6-5.2v3c-.4-.1-.8-.2-1.2-.1-1.2.1-2 1.1-1.9 2.3.1 1.1 1 1.9 2.1 1.9 1.3 0 2.2-1 2.2-2.3V4Z"/></svg>',
];
?>
<footer class="site-footer">
	<div class="container">
		<div class="footer-top">
			<div class="footer-about">
				<svg class="arch" viewBox="0 0 120 132" fill="none" aria-hidden="true"><path class="a-navy" style="stroke:#fff" d="M16 17 Q60 9 106 16"/><path class="a-pink" d="M19 27 Q60 20 101 26"/><path class="a-navy" style="stroke:#fff" d="M24 122 L24 62 Q24 30 60 30 Q96 30 96 62 L96 122"/><path class="a-pink" d="M40 122 L40 64 Q40 46 60 46 Q80 46 80 64 L80 122"/><path class="h-fill" d="M60 100 C49 88 43 82 43 73 C43 66 48 62 53.5 62 C57 62 59 64.5 60 67 C61 64.5 63 62 66.5 62 C72 62 77 66 77 73 C77 82 71 88 60 100 Z"/></svg>
				<div class="fname">Kids Club<small>by zacp</small></div>
				<?php
				if ( $about ) :
					?>
					<p><?php echo esc_html( $about ); ?></p><?php endif; ?>

				<?php if ( $social ) : ?>
				<div class="footer-social">
					<?php
					foreach ( $social as $s ) :
						$ic = $soc_icons[ $s['network'] ] ?? '';
						?>
						<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $s['network'] ) ); ?>"><?php echo $ic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG hardcode du map statique ?></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>

			<?php foreach ( $cols as $col ) : ?>
				<div class="footer-col">
					<h4><?php echo esc_html( $col['heading'] ); ?></h4>
					<?php
					if ( ! empty( $col['links'] ) ) {
						foreach ( $col['links'] as $l ) :
							?>
						<a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
											<?php
					endforeach;
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $copy ); ?></span>
			<div style="display:flex;gap:22px;align-items:center;flex-wrap:wrap">
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
