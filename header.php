<?php
/**
 * header.php — Kopf der Seite + Sticky-Navigation + Mobile-Menü.
 * Inhalte aus "Theme-Einstellungen" (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo        = get_field( 'header_logo', 'option' );
$logo_mobile = get_field( 'header_logo_mobile', 'option' );
$cta_label   = get_field( 'header_cta_label', 'option' ) ?: 'Online Termin buchen';
$cta_link    = get_field( 'header_cta_link', 'option' ) ?: '#termin';

/* Brand-Markup — Desktop-Logo + Mobil-Logo mit CSS show/hide */
$alt_desktop = esc_attr( ( is_array( $logo ) && $logo['alt'] ) ? $logo['alt'] : 'Kids Club by zacp' );
$alt_mobile  = esc_attr( ( is_array( $logo_mobile ) && $logo_mobile['alt'] ) ? $logo_mobile['alt'] : 'Kids Club by zacp' );
$src_desktop = $logo ? esc_url( $logo['url'] ) : esc_url( get_theme_file_uri( 'assets/img/logo-quer.svg' ) );
$src_mobile  = $logo_mobile ? esc_url( $logo_mobile['url'] ) : esc_url( get_theme_file_uri( 'assets/img/logo-hoch.svg' ) );

$brand = '<img class="brand-logo brand-logo--desktop" src="' . $src_desktop . '" alt="' . $alt_desktop . '" width="150" height="56">'
       . '<img class="brand-logo brand-logo--mobile"  src="' . $src_mobile  . '" alt="' . $alt_mobile  . '" width="48"  height="64">';

$nav           = get_field( 'header_nav', 'option' ) ?: [];
$section_align = get_field( 'section_alignment', 'option' ) ?: 'left';

/**
 * Anker-Links (#termin …) funktionieren nur auf der Startseite.
 * Auf Unterseiten (Impressum, Datenschutz) → home_url voranstellen.
 */
function kc_nav_url( $link ) {
	if ( is_string( $link ) && 0 === strpos( $link, '#' ) && ! is_front_page() ) {
		return home_url( '/' ) . $link;
	}
	return $link;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-bg="rosa" data-heads="rund" data-hearts="on" data-shapes="on" data-section-align="<?php echo esc_attr( $section_align ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://kidsclub.masingatech.com">
	<link rel="dns-prefetch" href="https://kidsclub.masingatech.com">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#top">Zum Inhalt springen</a>

<header class="site-header" id="header">
	<div class="container nav">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Kids Club by zacp Startseite"><?php echo $brand; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- construit avec esc_url()+esc_attr() ou SVG hardcode ?></a>

		<nav class="nav-links" aria-label="Hauptnavigation">
			<?php foreach ( $nav as $item ) : ?>
				<a href="<?php echo esc_url( kc_nav_url( $item['link'] ) ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="nav-cta">
			<button type="button" class="btn btn-primary btn-sm" data-booking-open aria-haspopup="dialog" style="display:inline-flex;align-items:center;gap:6px">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
				<?php echo esc_html( $cta_label ); ?>
			</button>
			<button class="burger" id="burger" aria-label="Menü öffnen"><?php echo kc_svg( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
	</div>
</header>

<div class="mobile-menu" id="mobileMenu">
	<div class="mm-top">
		<img src="<?php echo esc_url( $src_desktop ); ?>" alt="Kids Club by zacp" class="mm-logo" width="150" height="56">
		<button class="close" id="menuClose" aria-label="Menü schließen"><?php echo kc_svg( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
	<nav class="mm-nav">
		<?php foreach ( $nav as $item ) : ?>
			<a href="<?php echo esc_url( kc_nav_url( $item['link'] ) ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
		<?php endforeach; ?>
		<a class="btn btn-primary" href="<?php echo esc_url( kc_nav_url( $cta_link ) ); ?>"><?php echo esc_html( $cta_label ); ?></a>
	</nav>
</div>
