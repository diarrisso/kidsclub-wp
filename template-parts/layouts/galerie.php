<?php
/**
 * Layout: Galerie mit Bereich-Filtern (ohne Lightbox).
 *
 * Source : CPT `praxis_foto` (Beitragsbild = Foto, Sortierung menu_order).
 * Filter : Taxonomie `bereich` → Chips (Alpine-Komponente praxisGallery).
 * Beim Filterwechsel wird die Scroll-Position verankert (kein Seiten-Sprung).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'gl_eyebrow' );
$title   = get_sub_field( 'gl_title' );
$text    = get_sub_field( 'gl_text' );

/* Fotos aus dem CPT sammeln. */
$photos     = [];
$cats_order = [];

$foto_posts = get_posts(
	[
		'post_type'      => 'praxis_foto',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);

foreach ( $foto_posts as $foto ) {
	$img_id = get_post_thumbnail_id( $foto );
	if ( ! $img_id ) {
		continue;
	}
	$terms = get_the_terms( $foto, 'bereich' );
	$slug  = '';
	if ( $terms && ! is_wp_error( $terms ) ) {
		$slug = $terms[0]->slug;
		if ( ! isset( $cats_order[ $slug ] ) ) {
			$cats_order[ $slug ] = $terms[0]->name;
		}
	}
	$photos[] = [
		'id'  => (int) $img_id,
		'cat' => $slug,
		'alt' => get_the_title( $foto ),
	];
}
?>
<section
	class="section-galerie reveal"
	id="galerie"
	x-data="praxisGallery"
>
	<div class="container">

		<div class="section-head center reveal">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $text ) : ?>
				<p class="lead"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cats_order ) : ?>
		<div class="chips" role="group" aria-label="<?php esc_attr_e( 'Galerie filtern', 'kidsclub' ); ?>">
			<button type="button" class="chip"
				:class="f === 'alle' && 'chip--active'"
				:aria-pressed="f === 'alle'"
				@click="setFilter('alle')"><?php esc_html_e( 'Alle', 'kidsclub' ); ?></button>
			<?php foreach ( $cats_order as $slug => $label ) : ?>
			<button type="button" class="chip"
				:class="f === '<?php echo esc_js( $slug ); ?>' && 'chip--active'"
				:aria-pressed="f === '<?php echo esc_js( $slug ); ?>'"
				@click="setFilter('<?php echo esc_js( $slug ); ?>')"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( $photos ) : ?>
		<div class="praxis-gallery" :class="f !== 'alle' && 'praxis-gallery--flat'">
			<?php foreach ( $photos as $p ) : ?>
			<figure
				class="praxis-gallery__item"
				x-show="f === 'alle'<?php echo $p['cat'] ? " || f === '" . esc_js( $p['cat'] ) . "'" : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_js appliqué ?>"
			>
				<?php
				echo wp_get_attachment_image(
					$p['id'],
					'large',
					false,
					[
						'loading'  => 'lazy',
						'decoding' => 'async',
						'alt'      => $p['alt'],
					]
				);
				?>
			</figure>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

	</div>
</section>
