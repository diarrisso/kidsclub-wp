<?php
/**
 * Template-Part: Rechtsseiten (Impressum, Datenschutz)
 * Gemeinsames Layout — Inhalt kommt aus dem WP-Editor (the_content).
 */
defined( 'ABSPATH' ) || exit;

while ( have_posts() ) :
	the_post();
	?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'legal-page' ); ?>>
	<div class="legal-page__wrap">
		<div class="container">
			<a class="legal-page__back" href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; <?php esc_html_e( 'Zurück zur Startseite', 'kidsclub' ); ?></a>
			<h1 class="legal-page__title"><?php the_title(); ?></h1>
			<div class="legal-page__content">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</article>
	<?php
endwhile;
