<?php
/**
 * Layout: Angstpatienten (Vergleich).
 *
 * Der Text des Kunden VERGLEICHT zwei Optionen (Lachgas / Vollnarkose) — deshalb stehen sie
 * als zwei Karten nebeneinander statt als Fließtext untereinander: aus einer Textwand wird
 * eine Entscheidungshilfe. Kein Wort wird dabei geändert.
 *
 * Die Karten-Inhalte kommen aus einem WYSIWYG: Aufzählungen werden per CSS zu Häkchen,
 * Fettungen zu Zwischenüberschriften. So bleibt die Redaktion einfach.
 *
 * <section> MUSS das Wurzelelement bleiben (siehe template-parts/flexible.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ag_anchor        = sanitize_title( (string) get_sub_field( 'ag_anchor' ) );
$ag_eyebrow       = get_sub_field( 'ag_eyebrow' );
$ag_title         = get_sub_field( 'ag_title' );
$ag_intro         = get_sub_field( 'ag_intro' );
$ag_gruende_title = get_sub_field( 'ag_gruende_title' );
$ag_gruende_intro = get_sub_field( 'ag_gruende_intro' );
$ag_gruende       = get_sub_field( 'ag_gruende' ) ?: [];
$ag_gruende_after = get_sub_field( 'ag_gruende_after' );
$ag_compare_title = get_sub_field( 'ag_compare_title' );
$ag_cards         = get_sub_field( 'ag_cards' ) ?: [];
$ag_usp_title     = get_sub_field( 'ag_usp_title' );
$ag_usp           = get_sub_field( 'ag_usp' ) ?: [];
$ag_closing       = get_sub_field( 'ag_closing' );
$ag_cta_title     = get_sub_field( 'ag_cta_title' );
$ag_cta_text      = get_sub_field( 'ag_cta_text' );
$ag_cta_bg        = kc_spray_url( get_sub_field( 'ag_cta_spray' ) );
?>

<section class="section section-angst"<?php echo $ag_anchor ? ' id="' . esc_attr( $ag_anchor ) . '"' : ''; ?>>
	<div class="container">
		<?php if ( $ag_eyebrow || $ag_title ) : ?>
			<div class="section-head center reveal">
				<?php if ( $ag_eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $ag_eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $ag_title ) : ?>
					<h2 class="section-title"><?php echo esc_html( $ag_title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ag_intro ) : ?>
			<p class="ag-intro reveal"><?php echo esc_html( $ag_intro ); ?></p>
		<?php endif; ?>

		<?php if ( $ag_gruende ) : ?>
			<?php if ( $ag_gruende_title ) : ?>
				<h3 class="ag-h3 reveal"><?php echo esc_html( $ag_gruende_title ); ?></h3>
			<?php endif; ?>
			<?php if ( $ag_gruende_intro ) : ?>
				<p class="ag-sub reveal"><?php echo esc_html( $ag_gruende_intro ); ?></p>
			<?php endif; ?>
			<div class="ag-chips reveal">
				<?php foreach ( $ag_gruende as $g ) : ?>
					<span class="ag-chip"><?php echo esc_html( $g['ag_grund'] ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ag_gruende_after ) : ?>
			<p class="ag-after reveal"><?php echo esc_html( $ag_gruende_after ); ?></p>
		<?php endif; ?>

		<?php if ( $ag_cards ) : ?>
			<?php if ( $ag_compare_title ) : ?>
				<h3 class="ag-h3 reveal"><?php echo esc_html( $ag_compare_title ); ?></h3>
			<?php endif; ?>
			<div class="ag-compare">
				<?php foreach ( $ag_cards as $card ) : ?>
					<article class="ag-card ag-card--<?php echo esc_attr( $card['ag_card_color'] ?: 'pink' ); ?> reveal">
						<?php if ( $card['ag_card_heading'] ) : ?>
							<h4><?php echo esc_html( $card['ag_card_heading'] ); ?></h4>
						<?php endif; ?>
						<?php // ACF wendet wpautop bereits auf WYSIWYG-Felder an. ?>
						<div class="ag-card__body"><?php echo wp_kses_post( (string) ( $card['ag_card_body'] ?? '' ) ); ?></div>
						<?php if ( $card['ag_card_foot'] ) : ?>
							<p class="ag-card__foot"><?php echo esc_html( $card['ag_card_foot'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ag_usp ) : ?>
			<?php if ( $ag_usp_title ) : ?>
				<h3 class="ag-h3 reveal"><?php echo esc_html( $ag_usp_title ); ?></h3>
			<?php endif; ?>
			<div class="ag-usp reveal">
				<?php foreach ( $ag_usp as $u ) : ?>
					<div><?php echo esc_html( $u['ag_usp_text'] ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $ag_closing ) : ?>
			<p class="ag-closing reveal"><?php echo esc_html( $ag_closing ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $ag_cta_title || $ag_cta_text ) : ?>
		<?php // Volle Breite: die Bande verlässt bewusst den .container. ?>
		<div class="ag-cta reveal"<?php echo $ag_cta_bg ? ' style="background-image:url(' . esc_url( $ag_cta_bg ) . ')"' : ''; ?>>
			<div class="ag-cta__inner">
				<?php if ( $ag_cta_title ) : ?>
					<h3><?php echo esc_html( $ag_cta_title ); ?></h3>
				<?php endif; ?>
				<?php if ( $ag_cta_text ) : ?>
					<p><?php echo esc_html( $ag_cta_text ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
