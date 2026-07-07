<?php
/**
 * Layout: FAQ — Alpine.js accordion + JSON-LD FAQPage schema
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'fq_eyebrow' );
$title   = get_sub_field( 'fq_title' );
$items   = get_sub_field( 'items' );
?>
<section class="section-faq reveal" id="faq">
	<div class="container">
		<?php
		if ( $eyebrow ) :
			?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $items ) : ?>
		<div class="accordion faq-accordion" x-data="{ open: null }" x-cloak>
			<?php foreach ( $items as $i => $item ) : ?>
			<div class="accordion-item">
				<button class="accordion-trigger"
						@click="open === <?php echo absint( $i ); ?> ? open = null : open = <?php echo absint( $i ); ?>"
						:aria-expanded="open === <?php echo absint( $i ); ?>"
						aria-controls="faq-panel-<?php echo absint( $i ); ?>"
						type="button">
					<span><?php echo esc_html( $item['fq_question'] ); ?></span>
					<span class="accordion-icon" aria-hidden="true"
						:class="open === <?php echo absint( $i ); ?> ? 'is-open' : ''">
						<span class="icon-plus"><?php echo kc_svg( 'accordion-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="icon-minus"><?php echo kc_svg( 'accordion-close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</span>
				</button>
				<div class="accordion-panel"
					id="faq-panel-<?php echo absint( $i ); ?>"
					x-show="open === <?php echo absint( $i ); ?>"
					x-transition>
					<p><?php echo wp_kses( $item['fq_answer'], [ 'strong' => [] ] ); ?></p>
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
						"text": <?php echo wp_json_encode( wp_strip_all_tags( $item['fq_answer'] ) ); ?>
					}
				}<?php echo ( $i < count( $items ) - 1 ) ? ',' : ''; ?>

				<?php endforeach; ?>
			]
		}
		</script>
		<?php endif; ?>
	</div>
</section>
