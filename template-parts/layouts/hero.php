<?php
/**
 * Layout: Hero (Banner) mit animierten Kindern.
 * Felder: hero_eyebrow, hero_title, hero_highlight, hero_text,
 *         hero_bg (image / poster), hero_media_type (image|video),
 *         hero_video (file), hero_anim (select), hero_show_kids (bool)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$anim       = get_sub_field( 'hero_anim' ) ?: 'winken';
$show_kids  = get_sub_field( 'hero_show_kids' );
$bg         = get_sub_field( 'hero_bg' );
$media_type = get_sub_field( 'hero_media_type' ) ?: 'image';
$acf_video  = get_sub_field( 'hero_video' );
$acf_url    = ( is_array( $acf_video ) && ! empty( $acf_video['url'] ) ) ? esc_url( $acf_video['url'] ) : '';

/* Spray-Video als Standard-Hintergrund (Loop), sofern kein eigenes Video gesetzt ist. */
$default_path  = get_theme_file_path( 'assets/video/spray-quer.mp4' );
$default_video = esc_url( get_theme_file_uri( 'assets/video/spray-quer.mp4' ) . '?v=' . ( file_exists( $default_path ) ? filemtime( $default_path ) : '1' ) );
$video_url     = $acf_url ?: $default_video;

/* "Cinématique" (Titel erst nach Video-Ende einblenden) nur, wenn explizit im Backend gewählt. */
$cinematic = ( 'video' === $media_type && $acf_url );
$is_video  = true; // Hero zeigt immer das (Spray-)Video als Hintergrund.

$bg_url = $bg ? esc_url( $bg['sizes']['large'] ?? $bg['url'] )
				: esc_url( get_theme_file_uri( 'assets/img/hero-spray-poster.jpg' ) );
?>
<section class="hero hero-banner"
		data-anim="<?php echo esc_attr( $anim ); ?>"
		<?php
		if ( $cinematic ) :
			?>
				data-media="video"<?php endif; ?>>

	<div class="hero-bg" role="img"
		aria-label="<?php echo esc_attr( $bg['alt'] ?? 'Kids Club' ); ?>"
		style="background:url('<?php echo esc_url( $bg_url ); ?>') center/cover no-repeat;">
		<?php if ( $is_video ) : ?>
		<video class="hero-video" autoplay muted playsinline preload="auto" <?php echo $cinematic ? '' : 'loop'; ?>
				poster="<?php echo esc_attr( $bg_url ); ?>"
				aria-hidden="true">
			<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
		</video>
		<?php endif; ?>
	</div>

	<?php
	if ( $show_kids && ! $is_video ) {
		get_template_part( 'template-parts/partials/kids-svg' );}
	?>

	<div class="hero-marquee" aria-hidden="true"><div class="marquee-track"></div></div>

	<div class="hero-scrim"></div>

	<div class="container hero-banner-inner
	<?php
	if ( ! $is_video ) {
		echo ' reveal';}
	?>
	">
		<?php if ( $eb = get_sub_field( 'hero_eyebrow' ) ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eb ); ?></span>
		<?php endif; ?>
		<h1 class="display">
			<?php echo esc_html( get_sub_field( 'hero_title' ) ); ?>
			<?php if ( $hl = get_sub_field( 'hero_highlight' ) ) : ?>
				<span class="hl"><?php echo esc_html( $hl ); ?></span>
			<?php endif; ?>
		</h1>
		<?php if ( $tx = get_sub_field( 'hero_text' ) ) : ?>
			<p class="lead"><?php echo esc_html( $tx ); ?></p>
		<?php endif; ?>
	</div>
</section>
