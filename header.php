<?php
/**
 * header.php — Kopf der Seite + Sticky-Navigation + Mobile-Menü.
 * Inhalte aus "Theme-Einstellungen" (inc/options.php).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$logo      = get_field( 'header_logo', 'option' );
$cta_label = get_field( 'header_cta_label', 'option' ) ?: 'Online Termin buchen';
$cta_link  = get_field( 'header_cta_link', 'option' ) ?: '#termin';

/* Brand-Markup (Logo-Bild ODER gezeichnetes Bogen-Logo) */
$arch = '<svg class="arch" viewBox="0 0 120 132" fill="none" aria-hidden="true"><path class="a-navy" d="M16 17 Q60 9 106 16"/><path class="a-pink" d="M19 27 Q60 20 101 26"/><path class="a-navy" d="M24 122 L24 62 Q24 30 60 30 Q96 30 96 62 L96 122"/><path class="a-pink" d="M40 122 L40 64 Q40 46 60 46 Q80 46 80 64 L80 122"/><path class="h-fill" d="M60 100 C49 88 43 82 43 73 C43 66 48 62 53.5 62 C57 62 59 64.5 60 67 C61 64.5 63 62 66.5 62 C72 62 77 66 77 73 C77 82 71 88 60 100 Z"/></svg>';
$brand = $logo
	? '<img src="' . esc_url( $logo['url'] ) . '" alt="' . esc_attr( $logo['alt'] ?: 'Kids Club by zacp' ) . '" style="height:48px;width:auto">'
	: $arch . '<span class="brand-name">Kids Club<small>by zacp</small></span>';

$nav = get_field( 'header_nav', 'option' ) ?: [];
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-bg="rosa" data-heads="rund" data-hearts="on" data-shapes="on">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="header">
	<div class="container nav">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Kids Club by zacp Startseite"><?php echo $brand; ?></a>

		<nav class="nav-links" aria-label="Hauptnavigation">
			<?php foreach ( $nav as $item ) : ?>
				<a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="nav-cta">
			<a class="btn btn-primary" href="<?php echo esc_url( $cta_link ); ?>"><?php echo esc_html( $cta_label ); ?></a>
			<button class="burger" id="burger" aria-label="Menü öffnen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
		</div>
	</div>
</header>

<div class="mobile-menu" id="mobileMenu">
	<div class="mm-top">
		<span class="brand-name">Kids Club<small>by zacp</small></span>
		<button class="close" id="menuClose" aria-label="Menü schließen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
	</div>
	<?php foreach ( $nav as $item ) : ?>
		<a href="<?php echo esc_url( $item['link'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
	<?php endforeach; ?>
	<a class="btn btn-primary" href="<?php echo esc_url( $cta_link ); ?>"><?php echo esc_html( $cta_label ); ?></a>
</div>
