<?php
/**
 * Layout: Ablauf — Erster Besuch (nummerierte Schritte)
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
		<?php
		if ( $eyebrow ) :
			?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( $text ) :
			?>
			<p class="section-lead"><?php echo esc_html( $text ); ?></p><?php endif; ?>
		<?php if ( $items ) : ?>
		<ol class="ablauf-steps">
			<?php foreach ( $items as $step ) : ?>
			<li class="ablauf-step">
				<span class="step-nr" aria-hidden="true"><?php echo esc_html( $step['abl_nr'] ); ?></span>
				<div class="step-body">
					<h3><?php echo esc_html( $step['abl_heading'] ); ?></h3>
					<?php
					if ( $step['abl_body'] ) :
						?>
						<p><?php echo esc_html( $step['abl_body'] ); ?></p><?php endif; ?>
				</div>
			</li>
			<?php endforeach; ?>
		</ol>
		<?php endif; ?>
	</div>
</section>
