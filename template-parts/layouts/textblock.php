<?php
/**
 * Layout: Textblock (freier Text).
 * Felder: tb_anchor, tb_eyebrow, tb_title, tb_style, tb_card_color, tb_content
 *
 * Générique : sert aux textes longs du client (Angstpatienten, erster Zahnarztbesuch) et,
 * plus tard, à Philosophie / Lage / Preise. Le contenu vient d'un WYSIWYG, donc il peut
 * contenir des <h3>, des <ul> et des liens — d'où le rendu via wp_kses_post().
 *
 * <section> DOIT rester l'élément racine : template-parts/flexible.php y injecte le style
 * de fond (spray + couleur) par regex. Sans lui, le fond est perdu silencieusement.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// sanitize_title() statt roher Eingabe: „#angst“ oder „Erster Besuch“ ergäben sonst eine
// id, die kein #-Link je erreicht. esc_attr() allein schützt nur vor XSS, nicht vor Unsinn.
$kc_tb_anchor  = sanitize_title( (string) get_sub_field( 'tb_anchor' ) );
$kc_tb_eyebrow = get_sub_field( 'tb_eyebrow' );
$kc_tb_title   = get_sub_field( 'tb_title' );
$kc_tb_style   = get_sub_field( 'tb_style' ) ?: 'fliesstext';
$kc_tb_color   = get_sub_field( 'tb_card_color' ) ?: 'pink';
$kc_tb_content = get_sub_field( 'tb_content' );

$kc_tb_class = 'tb-prose reveal';
if ( 'karte' === $kc_tb_style ) {
	$kc_tb_class .= ' tb-card tb-card--' . $kc_tb_color;
}
?>

<section class="section section-textblock"<?php echo $kc_tb_anchor ? ' id="' . esc_attr( $kc_tb_anchor ) . '"' : ''; ?>>
	<div class="container">
		<?php if ( $kc_tb_eyebrow || $kc_tb_title ) : ?>
			<div class="section-head center reveal">
				<?php if ( $kc_tb_eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $kc_tb_eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( $kc_tb_title ) : ?>
					<h2 class="section-title"><?php echo esc_html( $kc_tb_title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $kc_tb_content ) : ?>
			<div class="<?php echo esc_attr( $kc_tb_class ); ?>">
				<?php echo wp_kses_post( $kc_tb_content ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
