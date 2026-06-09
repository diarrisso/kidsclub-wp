<?php
/**
 * Layout: Kontakt — CF7-Formular via Shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow        = get_sub_field( 'kt_eyebrow' );
$title          = get_sub_field( 'kt_title' );
$text           = get_sub_field( 'kt_text' );
$form_shortcode = get_sub_field( 'form_shortcode' );
?>
<section class="section-kontakt reveal" id="kontakt">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?><p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
        <?php if ( $form_shortcode ) : ?>
        <div class="kontakt-form">
            <?php echo do_shortcode( wp_kses( $form_shortcode, [] ) ); ?>
        </div>
        <?php endif; ?>
    </div>
</section>
