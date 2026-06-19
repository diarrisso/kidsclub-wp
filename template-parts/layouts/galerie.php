<?php
/**
 * Layout: Galerie mit Filter-Chips und Lightbox (Alpine.js)
 *
 * Source : CPT `praxis_foto` (featured image = photo, tri menu_order).
 * Filtrage : taxonomie `bereich` → chips Alpine.js (même pattern que praxis.php).
 * Visuel : réutilise les classes .praxis-gallery / .praxis-gallery__item pour cohérence.
 * Lightbox : clic sur une photo → modal plein écran, fermeture Escape/fond/bouton ×.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'gl_eyebrow' );
$title   = get_sub_field( 'gl_title' );
$text    = get_sub_field( 'gl_text' );

/* Photos depuis le CPT praxis_foto */
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
	$full_src = wp_get_attachment_image_src( $img_id, 'full' );
	$photos[] = [
		'id'       => $img_id,
		'cat'      => $slug,
		'alt'      => get_the_title( $foto ),
		'full_url' => $full_src ? $full_src[0] : '',
	];
}
?>
<section
	class="section-galerie reveal"
	id="galerie"
	x-data="{ f: 'alle', lbOpen: false, lbSrc: '', lbAlt: '' }"
	@keydown.escape.window="lbOpen = false"
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
				@click="f = 'alle'"><?php esc_html_e( 'Alle', 'kidsclub' ); ?></button>
			<?php foreach ( $cats_order as $slug => $label ) : ?>
			<button type="button" class="chip"
				:class="f === '<?php echo esc_js( $slug ); ?>' && 'chip--active'"
				:aria-pressed="f === '<?php echo esc_js( $slug ); ?>'"
				@click="f = '<?php echo esc_js( $slug ); ?>'"><?php echo esc_html( $label ); ?></button>
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
				x-transition:enter="pg-enter"
				x-transition:enter-start="pg-enter-start"
				x-transition:enter-end="pg-enter-end"
				x-transition:leave="pg-leave"
				x-transition:leave-start="pg-enter-end"
				x-transition:leave-end="pg-enter-start"
				@click="lbSrc = '<?php echo esc_js( $p['full_url'] ); ?>'; lbAlt = '<?php echo esc_js( $p['alt'] ); ?>'; lbOpen = true"
				@keydown.enter="lbSrc = '<?php echo esc_js( $p['full_url'] ); ?>'; lbAlt = '<?php echo esc_js( $p['alt'] ); ?>'; lbOpen = true"
				@keydown.space.prevent="lbSrc = '<?php echo esc_js( $p['full_url'] ); ?>'; lbAlt = '<?php echo esc_js( $p['alt'] ); ?>'; lbOpen = true"
			>
				<?php
				echo wp_get_attachment_image(
					$p['id'],
					'large',
					false,
					[
						'loading' => 'lazy',
						'alt'     => $p['alt'],
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
		x-show="lbOpen"
		x-cloak
		@click.self="lbOpen = false"
	>
		<button
			type="button"
			class="gal-lightbox__close"
			aria-label="<?php esc_attr_e( 'Schließen', 'kidsclub' ); ?>"
			@click="lbOpen = false"
		>&times;</button>
		<img :src="lbSrc" :alt="lbAlt" width="1200" height="900">
	</div>

</section>
