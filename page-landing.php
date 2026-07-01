<?php
/**
 * Template Name: Kids Club Landing
 * Dateiname: page-landing.php  (im Theme-Stammverzeichnis)
 *
 * Diese Seitenvorlage rendert ausschliesslich die ACF-Flexible-Content-Sektionen.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="top">
	<?php get_template_part( 'template-parts/flexible' ); ?>
</main>
<?php
get_footer();
