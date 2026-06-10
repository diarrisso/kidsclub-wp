<?php
/**
 * Layout: Praxis-Galerie mit funktionalen Filter-Chips (Alpine.js)
 *
 * Source des photos : CPT `praxis_foto` (featured image = photo,
 * taxonomie `bereich` = chips, tri manuel via menu_order).
 * Fallback : repeater ACF `prx_photos` puis ancien champ `gallery`
 * tant que le CPT est vide.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'prx_eyebrow' );
$title   = get_sub_field( 'prx_title' );

/* Photos depuis le CPT — [ 'id' => attachment_id, 'cat' => slug, 'label' => term name ] */
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

if ( $foto_posts ) {
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
			'id'  => $img_id,
			'cat' => $slug,
			'alt' => get_the_title( $foto ),
		];
	}
} else {
	/* Fallback 1 : repeater prx_photos / Fallback 2 : altes gallery-Feld */
	$rows = get_sub_field( 'prx_photos' ) ?: [];
	if ( ! $rows && ( $legacy = get_sub_field( 'gallery' ) ) ) {
		foreach ( $legacy as $img ) {
			$rows[] = [
				'img' => $img,
				'cat' => '',
			];
		}
	}
	$defined_cats = [];
	foreach ( get_sub_field( 'prx_cats' ) ?: [] as $c ) {
		$defined_cats[ sanitize_title( $c['label'] ) ] = $c['label'];
	}
	foreach ( $rows as $row ) {
		if ( empty( $row['img'] ) ) {
			continue;
		}
		$slug = ! empty( $row['cat'] ) ? sanitize_title( $row['cat'] ) : '';
		if ( $slug && isset( $defined_cats[ $slug ] ) && ! isset( $cats_order[ $slug ] ) ) {
			$cats_order[ $slug ] = $defined_cats[ $slug ];
		}
		$photos[] = [
			'id'  => is_array( $row['img'] ) ? $row['img']['ID'] : (int) $row['img'],
			'cat' => $slug,
			'alt' => is_array( $row['img'] ) ? ( $row['img']['alt'] ?: 'Praxis Kids Club' ) : 'Praxis Kids Club',
		];
	}
}
?>
<section class="section-praxis reveal" id="praxis" x-data="{ f: 'alle' }">
	<div class="container">
		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>

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
			<figure class="praxis-gallery__item"
				x-show="f === 'alle'<?php echo $p['cat'] ? " || f === '" . esc_js( $p['cat'] ) . "'" : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_js appliqué ?>"
				x-transition:enter="pg-enter" x-transition:enter-start="pg-enter-start" x-transition:enter-end="pg-enter-end"
				x-transition:leave="pg-leave" x-transition:leave-start="pg-enter-end" x-transition:leave-end="pg-enter-start">
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
</section>
