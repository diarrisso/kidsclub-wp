<?php
/**
 * Layout: Praxis-Galerie mit funktionalen Filter-Chips (Alpine.js)
 * Felder: prx_eyebrow, prx_title, prx_cats (repeater), prx_photos (repeater img+cat)
 * Legacy-Fallback: gallery (altes ACF-Gallery-Feld, bis Migration abgeschlossen)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'prx_eyebrow' );
$title   = get_sub_field( 'prx_title' );
$cats    = get_sub_field( 'prx_cats' ) ?: [];
$photos  = get_sub_field( 'prx_photos' ) ?: [];

/* Legacy-Fallback: altes gallery-Feld ohne Kategorien */
if ( ! $photos && ( $legacy = get_sub_field( 'gallery' ) ) ) {
	foreach ( $legacy as $img ) {
		$photos[] = [
			'img' => $img,
			'cat' => '',
		];
	}
}

/* Nur Kategorien als Chips zeigen, die auch Fotos haben */
$used_cats = [];
foreach ( $photos as $p ) {
	if ( ! empty( $p['cat'] ) ) {
		$used_cats[ $p['cat'] ] = true;
	}
}
$chips = [];
foreach ( $cats as $c ) {
	$slug = sanitize_title( $c['label'] );
	if ( isset( $used_cats[ $slug ] ) ) {
		$chips[] = [
			'slug'  => $slug,
			'label' => $c['label'],
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

		<?php if ( $chips ) : ?>
		<div class="chips" role="group" aria-label="<?php esc_attr_e( 'Galerie filtern', 'kidsclub' ); ?>">
			<button type="button" class="chip"
				:class="f === 'alle' && 'chip--active'"
				:aria-pressed="f === 'alle'"
				@click="f = 'alle'"><?php esc_html_e( 'Alle', 'kidsclub' ); ?></button>
			<?php foreach ( $chips as $chip ) : ?>
			<button type="button" class="chip"
				:class="f === '<?php echo esc_js( $chip['slug'] ); ?>' && 'chip--active'"
				:aria-pressed="f === '<?php echo esc_js( $chip['slug'] ); ?>'"
				@click="f = '<?php echo esc_js( $chip['slug'] ); ?>'"><?php echo esc_html( $chip['label'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( $photos ) : ?>
		<div class="praxis-gallery" :class="f !== 'alle' && 'praxis-gallery--flat'">
			<?php
			foreach ( $photos as $p ) :
				$img = $p['img'];
				if ( ! $img ) {
					continue;
				}
				$slug = ! empty( $p['cat'] ) ? sanitize_title( $p['cat'] ) : '';
				?>
			<figure class="praxis-gallery__item"
				x-show="f === 'alle'
				<?php
				if ( $slug ) :
					?>
					|| f === '<?php echo esc_js( $slug ); ?>'<?php endif; ?>"
				x-transition:enter="pg-enter" x-transition:enter-start="pg-enter-start" x-transition:enter-end="pg-enter-end"
				x-transition:leave="pg-leave" x-transition:leave-start="pg-enter-end" x-transition:leave-end="pg-enter-start">
				<img src="<?php echo esc_url( $img['sizes']['large'] ?? $img['url'] ); ?>"
					alt="<?php echo esc_attr( $img['alt'] ?: 'Praxis Kids Club' ); ?>"
					loading="lazy"
					width="<?php echo esc_attr( $img['sizes']['large-width'] ?? $img['width'] ); ?>"
					height="<?php echo esc_attr( $img['sizes']['large-height'] ?? $img['height'] ); ?>">
			</figure>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
