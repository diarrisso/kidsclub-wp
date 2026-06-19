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
?>
<section class="section-kontakt reveal" id="kontakt">
	<div class="container">
		<div class="kontakt-card">
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
