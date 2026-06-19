<?php
/**
 * Layout: Ablauf — Erster Besuch (Akkordeon mit farbigen Bändern)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'abl_eyebrow' );
$title   = get_sub_field( 'abl_title' );
$text    = get_sub_field( 'abl_text' );
$items   = get_sub_field( 'items' );
?>
<section class="section-ablauf reveal" id="ablauf">
	<div class="container">
		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="section-lead"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php if ( $items ) : ?>
		<div class="accordion ablauf-accordion" x-data="{ open: 0 }" x-cloak>
			<?php foreach ( $items as $i => $step ) : ?>
			<div class="ablauf-item">
				<button class="ablauf-trigger"
						@click="open === <?php echo absint( $i ); ?> ? open = null : open = <?php echo absint( $i ); ?>"
						:aria-expanded="open === <?php echo absint( $i ); ?>"
						aria-controls="ablauf-panel-<?php echo absint( $i ); ?>"
						type="button">
					<span class="ablauf-num" aria-hidden="true"><?php echo esc_html( $step['abl_nr'] ?: (string) ( $i + 1 ) ); ?></span>
					<span class="ablauf-q"><?php echo esc_html( $step['abl_heading'] ); ?></span>
					<span class="accordion-icon" aria-hidden="true"
						:class="open === <?php echo absint( $i ); ?> ? 'is-open' : ''">
						<span class="icon-plus"><?php echo kc_svg( 'accordion-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="icon-minus"><?php echo kc_svg( 'accordion-close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</span>
				</button>
				<div class="ablauf-panel"
					id="ablauf-panel-<?php echo absint( $i ); ?>"
					x-show="open === <?php echo absint( $i ); ?>"
					x-transition>
					<?php if ( $step['abl_body'] ) : ?>
						<p><?php echo esc_html( $step['abl_body'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
