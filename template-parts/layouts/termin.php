<?php
/**
 * Layout: Termin buchen — QR-Code + Buchungs-Embed
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow    = get_sub_field( 'tr_eyebrow' );
$title      = get_sub_field( 'tr_title' );
$text       = get_sub_field( 'tr_text' );
$qr         = get_sub_field( 'qr_image' );
$embed_code = get_sub_field( 'embed_code' );
?>
<section class="section-termin reveal" id="termin">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?><p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
        <div class="termin-layout">
            <?php if ( $qr ) : ?>
            <div class="termin-qr">
                <img src="<?php echo esc_url( $qr['url'] ); ?>"
                     alt="QR-Code für Online-Terminbuchung"
                     width="200" height="200" loading="lazy">
                <p class="termin-qr__label">QR-Code scannen</p>
            </div>
            <?php endif; ?>
            <?php if ( $embed_code ) : ?>
            <div class="termin-embed">
                <?php echo wp_kses_post( $embed_code ); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
