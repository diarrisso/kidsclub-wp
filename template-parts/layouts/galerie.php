<?php
/**
 * Layout: Galerie mit Bereich-Filtern und Lightbox (Alpine-Komponente praxisGallery).
 *
 * Source : CPT `praxis_foto` (Beitragsbild = Foto, Sortierung menu_order).
 * Filter : Taxonomie `bereich` → Chips. Lightbox: Prev/Next, Tastatur, Swipe,
 * Zähler + Caption, filtertreue Navigation. Logik in assets/js/gallery.js.
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
	$large = wp_get_attachment_image_src( $img_id, 'large' );
	$full  = wp_get_attachment_image_src( $img_id, 'full' );
	if ( ! $large ) {
		continue;
	}
	$photos[] = [
		'id'       => (int) $img_id,
		'cat'      => $slug,
		'alt'      => get_the_title( $foto ),
		'srcLarge' => $large[0],
		'srcFull'  => $full ? $full[0] : $large[0],
		'w'        => (int) $large[1],
		'h'        => (int) $large[2],
	];
}

/* JSON sicher in <script> einbetten (Tags/Ampersands hexen). */
$photos_json = wp_json_encode( $photos, JSON_HEX_TAG | JSON_HEX_AMP );
?>
<section
	class="section-galerie reveal"
	id="galerie"
	x-data="praxisGallery"
>
	<script type="application/json"><?php echo $photos_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode mit JSON_HEX_TAG|JSON_HEX_AMP ?></script>

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
				role="button"
				tabindex="0"
				aria-label="<?php echo esc_attr( $p['alt'] ); ?>"
				style="cursor:pointer"
				x-show="f === 'alle'<?php echo $p['cat'] ? " || f === '" . esc_js( $p['cat'] ) . "'" : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_js appliqué ?>"
				@click="openById(<?php echo (int) $p['id']; ?>, $event)"
				@keydown.enter="openById(<?php echo (int) $p['id']; ?>, $event)"
				@keydown.space.prevent="openById(<?php echo (int) $p['id']; ?>, $event)"
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

	<!-- Lightbox -->
	<div
		class="gal-lightbox"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Bild-Vorschau', 'kidsclub' ); ?>"
		x-show="open"
		x-cloak
		@keydown.escape.window="close()"
		@keydown.arrow-left.window="open && prev()"
		@keydown.arrow-right.window="open && next()"
		@click.self="close()"
	>
		<button type="button" class="gal-lightbox__close" x-ref="lbClose"
			aria-label="<?php esc_attr_e( 'Schließen', 'kidsclub' ); ?>" @click="close()">&times;</button>

		<button type="button" class="gal-lightbox__nav gal-lightbox__nav--prev"
			@click="prev()" :disabled="atStart" :aria-disabled="atStart"
			aria-label="<?php esc_attr_e( 'Vorheriges Bild', 'kidsclub' ); ?>">&lsaquo;</button>

		<figure class="gal-lightbox__figure">
			<img
				:src="current && current.srcLarge"
				:alt="current && current.alt"
				:width="current && current.w"
				:height="current && current.h"
				@touchstart="onTouchStart($event)"
				@touchend="onTouchEnd($event)">
			<figcaption class="gal-lightbox__caption">
				<span class="gal-lightbox__counter" aria-live="polite"
					x-text="'<?php esc_html_e( 'Bild', 'kidsclub' ); ?> ' + position + ' <?php esc_html_e( 'von', 'kidsclub' ); ?> ' + total"></span>
				<span class="gal-lightbox__alt" x-text="current && current.alt"></span>
			</figcaption>
		</figure>

		<button type="button" class="gal-lightbox__nav gal-lightbox__nav--next"
			@click="next()" :disabled="atEnd" :aria-disabled="atEnd"
			aria-label="<?php esc_attr_e( 'Nächstes Bild', 'kidsclub' ); ?>">&rsaquo;</button>
	</div>

</section>
