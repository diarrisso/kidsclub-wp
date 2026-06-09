<?php
/**
 * Layout: FAQ — Alpine.js accordion + JSON-LD FAQPage schema
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'fq_eyebrow' );
$title   = get_sub_field( 'fq_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-faq reveal" id="faq">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $items ) : ?>
        <div class="accordion faq-accordion" x-data="{ open: null }">
            <?php foreach ( $items as $i => $item ) : ?>
            <div class="accordion-item">
                <button class="accordion-trigger"
                        @click="open === <?php echo $i; ?> ? open = null : open = <?php echo $i; ?>"
                        :aria-expanded="open === <?php echo $i; ?>"
                        type="button">
                    <span><?php echo esc_html( $item['fq_question'] ); ?></span>
                    <svg class="accordion-chevron" :class="{ 'is-open': open === <?php echo $i; ?> }"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
                <div class="accordion-panel" x-show="open === <?php echo $i; ?>" x-transition>
                    <p><?php echo esc_html( $item['fq_answer'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                <?php foreach ( $items as $i => $item ) : ?>
                {
                    "@type": "Question",
                    "name": <?php echo wp_json_encode( $item['fq_question'] ); ?>,
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": <?php echo wp_json_encode( $item['fq_answer'] ); ?>
                    }
                }<?php echo ( $i < count( $items ) - 1 ) ? ',' : ''; ?>

                <?php endforeach; ?>
            ]
        }
        </script>
        <?php endif; ?>
    </div>
</section>
