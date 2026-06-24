<?php
/**
 * Layout: Hero (Banner) mit Spray-Video-Hintergrund.
 * Felder: hero_eyebrow, hero_title, hero_highlight, hero_text,
 *         hero_bg (image / poster), hero_media_type (image|video|video_slider),
 *         hero_video (file), hero_video_slides (repeater: slide_video, slide_poster).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$anim         = get_sub_field( 'hero_anim' ) ?: 'winken';
$bg           = get_sub_field( 'hero_bg' );
$media_type   = get_sub_field( 'hero_media_type' ) ?: 'image';
$acf_video    = get_sub_field( 'hero_video' );
$acf_url      = ( is_array( $acf_video ) && ! empty( $acf_video['url'] ) ) ? esc_url( $acf_video['url'] ) : '';
$video_slides = get_sub_field( 'hero_video_slides' ) ?: [];

$default_path  = get_theme_file_path( 'assets/video/spray-quer.mp4' );
$default_video = esc_url( get_theme_file_uri( 'assets/video/spray-quer.mp4' ) . '?v=' . ( file_exists( $default_path ) ? filemtime( $default_path ) : '1' ) );
$video_url     = $acf_url ?: $default_video;

$acf_video_mobile    = get_sub_field( 'hero_video_mobile' );
$acf_url_mobile      = ( is_array( $acf_video_mobile ) && ! empty( $acf_video_mobile['url'] ) ) ? esc_url( $acf_video_mobile['url'] ) : '';
$default_mobile_path = get_theme_file_path( 'assets/video/spray-hoch.mp4' );
$default_mobile_url  = file_exists( $default_mobile_path )
	? esc_url( get_theme_file_uri( 'assets/video/spray-hoch.mp4' ) . '?v=' . filemtime( $default_mobile_path ) )
	: '';
$video_url_mobile    = $acf_url_mobile ?: $default_mobile_url;

$cinematic = ( 'video' === $media_type && $acf_url );
$is_slider = ( 'video_slider' === $media_type && count( $video_slides ) >= 2 );

$bg_url = $bg ? esc_url( $bg['sizes']['large'] ?? $bg['url'] )
				: esc_url( get_theme_file_uri( 'assets/img/hero-spray-poster.jpg' ) );
?>
<?php
$hero_media = $cinematic ? 'video' : ( $is_slider ? 'video_slider' : '' );
?>
<section class="hero hero-banner"
		data-anim="<?php echo esc_attr( $anim ); ?>"
		<?php
		if ( $hero_media ) :
			?>
			data-media="<?php echo esc_attr( $hero_media ); ?>"<?php endif; ?>>


<?php if ( $is_slider ) : ?>
	<!-- Video Slider -->
	<div class="hero-video-swiper swiper" aria-hidden="true">
		<div class="swiper-wrapper">
			<?php
			foreach ( $video_slides as $slide ) :
				$sv = $slide['slide_video'];
				$sp = $slide['slide_poster'];
				if ( ! is_array( $sv ) || empty( $sv['url'] ) ) {
					continue;
				}
				$slide_url  = esc_url( $sv['url'] );
				$poster_url = $sp ? esc_url( $sp['sizes']['large'] ?? $sp['url'] ) : $bg_url;
				?>
			<div class="swiper-slide">
				<video class="hero-video"
					muted playsinline preload="metadata"
					poster="<?php echo esc_attr( $poster_url ); ?>">
					<source src="<?php echo esc_url( $slide_url ); ?>" type="video/mp4">
				</video>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="hero-video-swiper__progress" aria-hidden="true"></div>
	</div>

<?php else : ?>
	<!-- Single Video / Image -->
	<div class="hero-bg" role="img"
		aria-label="<?php echo esc_attr( $bg['alt'] ?? 'Kids Club' ); ?>"
		style="background:url('<?php echo esc_url( $bg_url ); ?>') center/cover no-repeat;">
		<video class="hero-video hero-video--desktop" autoplay muted playsinline preload="none" <?php echo $cinematic ? '' : 'loop'; ?>
				poster="<?php echo esc_attr( $bg_url ); ?>"
				aria-hidden="true">
			<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
		</video>
		<?php if ( $cinematic && $video_url_mobile ) : ?>
		<video class="hero-video hero-video--mobile" muted playsinline preload="none"
				poster="<?php echo esc_attr( $bg_url ); ?>"
				aria-hidden="true">
			<source src="<?php echo esc_url( $video_url_mobile ); ?>" type="video/mp4">
		</video>
		<?php endif; ?>
	</div>
<?php endif; ?>

	<div class="hero-scrim"></div>

	<div class="container hero-banner-inner">
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
