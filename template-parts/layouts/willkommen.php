<?php
/**
 * Layout: Willkommen (Intro).
 *
 * Zwei Darstellungen:
 *  - „klassisch“ : ein zentrierter WYSIWYG-Block (Feld `text`) — das ursprüngliche Verhalten.
 *  - „editorial“ : Auftakt, zwei Spalten, Zitat-Bande über die volle Breite, Schlussabsatz.
 *
 * Der Text bleibt in beiden Fällen unverändert — nur die Anordnung ändert sich.
 * Fällt auf „klassisch“ zurück, wenn die Editorial-Felder leer sind.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wk_style      = get_sub_field( 'wk_style' ) ?: 'klassisch';
$wk_lead       = get_sub_field( 'wk_lead' );
$wk_title      = get_sub_field( 'wk_title' );
$wk_col1       = get_sub_field( 'wk_col1' );
$wk_col2       = get_sub_field( 'wk_col2' );
$wk_motto_line = get_sub_field( 'wk_motto_line' );
$wk_outro      = get_sub_field( 'wk_outro' );

// Reicht EIN gefülltes Editorial-Feld, dann editorial rendern.
// Nur auf `wk_lead` zu prüfen wäre eine Falle: wer „Editorial“ wählt, alles ausfüllt,
// aber den Auftakt leer lässt, fiele auf „klassisch“ zurück — und da das alte
// `text`-Feld bei neuen Sektionen leer ist, verschwände die GANZE Sektion samt Inhalt.
$wk_has_editorial = $wk_lead || $wk_title || $wk_col1 || $wk_col2 || $wk_motto_line || $wk_outro;

if ( 'editorial' !== $wk_style || ! $wk_has_editorial ) {
	$wk_text = get_sub_field( 'text' );
	if ( ! $wk_text ) {
		return;
	}
	?>
	<section class="section intro-welcome reveal" id="willkommen">
		<div class="container intro-welcome__inner">
			<?php echo wp_kses_post( $wk_text ); ?>
		</div>
	</section>
	<?php
	return;
}

$wk_eyebrow      = get_sub_field( 'wk_eyebrow' );
$wk_title_hl     = get_sub_field( 'wk_title_hl' );
$wk_motto_text   = get_sub_field( 'wk_motto_text' );
$wk_motto_kicker = get_sub_field( 'wk_motto_kicker' );
$wk_motto_bg     = kc_spray_url( get_sub_field( 'wk_motto_spray' ) );
?>

<section class="section wk-editorial" id="willkommen">
	<div class="container">
		<div class="wk-inner reveal">
			<?php if ( $wk_eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $wk_eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $wk_title || $wk_title_hl ) : ?>
				<h2 class="wk-title">
					<?php echo esc_html( $wk_title ); ?>
					<?php if ( $wk_title_hl ) : ?>
						<br><span class="wk-hl"><?php echo esc_html( $wk_title_hl ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $wk_lead ) : ?>
				<p class="wk-lead"><?php echo esc_html( $wk_lead ); ?></p>
			<?php endif; ?>

			<?php if ( $wk_col1 || $wk_col2 ) : ?>
				<div class="wk-cols">
					<?php
					if ( $wk_col1 ) :
						?>
						<p><?php echo esc_html( $wk_col1 ); ?></p><?php endif; ?>
					<?php
					if ( $wk_col2 ) :
						?>
						<p><?php echo esc_html( $wk_col2 ); ?></p><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $wk_motto_text || $wk_motto_line ) : ?>
		<?php // Volle Breite: die Bande verlässt bewusst den .container. ?>
		<div class="wk-motto reveal"<?php echo $wk_motto_bg ? ' style="background-image:url(' . esc_url( $wk_motto_bg ) . ')"' : ''; ?>>
			<div class="wk-motto__inner">
				<?php if ( $wk_motto_text ) : ?>
					<p><?php echo esc_html( $wk_motto_text ); ?></p>
				<?php endif; ?>
				<?php if ( $wk_motto_line ) : ?>
					<p class="wk-motto__line">
						<?php if ( $wk_motto_kicker ) : ?>
							<span class="wk-motto__kicker"><?php echo esc_html( $wk_motto_kicker ); ?></span>
						<?php endif; ?>
						<?php echo esc_html( $wk_motto_line ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $wk_outro ) : ?>
		<div class="container">
			<div class="wk-outro-band reveal">
				<p class="wk-outro"><?php echo esc_html( $wk_outro ); ?></p>
			</div>
		</div>
	<?php endif; ?>
</section>
