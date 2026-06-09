<?php
/**
 * Layout: Kundenstimmen — Swiper.js slider (initialized in kidsclub.js)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'st_eyebrow' );
$title   = get_sub_field( 'st_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-stimmen reveal" id="stimmen">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $items ) : ?>
        <div class="swiper stimmen-swiper" aria-roledescription="Karussell">
            <div class="swiper-wrapper" aria-live="polite">
                <?php foreach ( $items as $i => $item ) : ?>
                <div class="swiper-slide stimmen-card" role="group"
                     aria-label="Bewertung <?php echo ( $i + 1 ); ?> von <?php echo count( $items ); ?>">
                    <blockquote class="stimmen-quote">
                        <p>&ldquo;<?php echo esc_html( $item['st_quote'] ); ?>&rdquo;</p>
                        <footer class="stimmen-author">
                            <strong><?php echo esc_html( $item['st_name'] ); ?></strong>
                            <?php if ( $item['st_role'] ) : ?>
                                <span><?php echo esc_html( $item['st_role'] ); ?></span>
                            <?php endif; ?>
                        </footer>
                    </blockquote>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination stimmen-swiper__pagination" aria-hidden="true"></div>
        </div>
        <?php endif; ?>
    </div>
</section>
