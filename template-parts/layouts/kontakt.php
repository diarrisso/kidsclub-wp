<?php
/**
 * Layout: Kontakt — Contact Form 7 + bloc info
 * Felder: kt_eyebrow, kt_title, kt_text, form_shortcode
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow        = get_sub_field( 'kt_eyebrow' );
$title          = get_sub_field( 'kt_title' );
$text           = get_sub_field( 'kt_text' );
$form_shortcode = get_sub_field( 'form_shortcode' );
// Farbiger Hintergrund (Box) = Standard. Schlanke, moderne Variante NUR wenn der Admin den
// Schalter explizit AUS gesetzt hat. ACF true_false liefert einen Bool (true/false); nie
// konfigurierte Alt-Sektionen liefern null → behalten das Box-Design (keine Regression).
$kt_card_bg = get_sub_field( 'kt_card_bg' );
$plain_form = ( null !== $kt_card_bg && ! $kt_card_bg );
?>
<section class="section-kontakt reveal" id="kontakt"<?php echo kc_section_bg_style(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<div class="kontakt-card<?php echo $plain_form ? ' kontakt-card--plain' : ''; ?>">
			<?php
			if ( $eyebrow ) :
				?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php
			if ( $text ) :
				?>
				<p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>

			<div class="kontakt-inner kontakt-inner--form-only">

				<!-- CF7 Form -->
				<?php if ( $form_shortcode ) : ?>
				<div class="kontakt-form-wrap">
					<?php echo do_shortcode( sanitize_text_field( $form_shortcode ) ); ?>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
