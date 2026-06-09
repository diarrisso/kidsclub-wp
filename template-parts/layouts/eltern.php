<?php
/**
 * Layout: Für Eltern — Alpine.js accordion
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'el_eyebrow' );
$title   = get_sub_field( 'el_title' );
$text    = get_sub_field( 'el_text' );
$items   = get_sub_field( 'items' );
?>
<section class="section-eltern reveal" id="eltern">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $text ) : ?><p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
        <?php if ( $items ) : ?>
        <div class="accordion" x-data="{ open: null }">
            <?php foreach ( $items as $i => $item ) : ?>
            <div class="accordion-item">
                <button class="accordion-trigger"
                        @click="open === <?php echo $i; ?> ? open = null : open = <?php echo $i; ?>"
                        :aria-expanded="open === <?php echo $i; ?>"
                        type="button">
                    <?php if ( $item['el_icon'] ) : ?><?php echo kc_icon( esc_attr( $item['el_icon'] ) ); ?><?php endif; ?>
                    <span><?php echo esc_html( $item['el_question'] ); ?></span>
                    <svg class="accordion-chevron" :class="{ 'is-open': open === <?php echo $i; ?> }"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
                <div class="accordion-panel" x-show="open === <?php echo $i; ?>" x-transition>
                    <p><?php echo esc_html( $item['el_answer'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
