<?php
/**
 * Theme-Setup: title-tag, thumbnails, SEO-Title.
 * In functions.php:  require get_theme_file_path('inc/setup.php');
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );        // WP rendert <title> — Pflicht für SEO
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	}
);

/**
 * SEO-Title für die Landing-Page: "Kinderzahnarzt Osnabrück | Kids Club by zacp"
 * statt des generischen "Seitenname – Blogname".
 * Überschreibbar im Backend: Theme-Einstellungen → SEO → Meta-Titel.
 */
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( is_page_template( 'page-landing.php' ) || is_front_page() ) {
			$custom         = function_exists( 'get_field' ) ? get_field( 'seo_title', 'option' ) : '';
			$parts['title'] = $custom ?: 'Kinderzahnarzt Osnabrück';
			// Front-Page nutzt 'tagline' statt 'site' → Marke explizit anhängen
			if ( array_key_exists( 'tagline', $parts ) ) {
				$parts['tagline'] = 'Kids Club by zacp';
			}
		}
		return $parts;
	}
);

add_filter(
	'document_title_separator',
	function () {
		return '|';
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( function_exists( 'get_field' ) ) {
			$variant = get_field( 'animation_variant', 'option' ) ?: 'floating';
			if ( 'none' !== $variant ) {
				$classes[] = 'anim-' . sanitize_html_class( $variant );
			}
		}
		return $classes;
	}
);

// SVG autorisé uniquement pour les admins — le SVG peut contenir du JS (XSS).
add_filter(
	'upload_mimes',
	function ( $mimes ) {
		if ( current_user_can( 'manage_options' ) ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}
		return $mimes;
	}
);

// Sanitize les SVG uploadés : supprime <script>, attributs on*, hrefs externes.
add_filter(
	'wp_handle_upload_prefilter',
	function ( $file ) {
		if ( ! isset( $file['type'] ) || 'image/svg+xml' !== $file['type'] ) {
			return $file;
		}

		$svg = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $svg ) {
			$file['error'] = 'SVG konnte nicht gelesen werden.';
			return $file;
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadXML( $svg, LIBXML_NONET );
		libxml_clear_errors();

		$xpath    = new DOMXPath( $dom );
		$ns       = 'http://www.w3.org/2000/svg';
		$html_ns  = 'http://www.w3.org/1999/xhtml';

		// Supprime les éléments dangereux : script, foreignObject, use (href ext.)
		$dangerous_tags = array( 'script', 'foreignObject' );
		foreach ( $dangerous_tags as $tag ) {
			foreach ( $xpath->query( "//*[local-name()='{$tag}']" ) as $node ) {
				$node->parentNode->removeChild( $node );
			}
		}

		// Supprime les attributs on* (event handlers) et href/xlink:href pointant vers JS
		foreach ( $xpath->query( '//@*' ) as $attr ) {
			if ( ! ( $attr instanceof DOMAttr ) ) {
				continue;
			}
			$name  = strtolower( $attr->localName );
			$value = strtolower( trim( $attr->value ) );
			if (
				0 === strpos( $name, 'on' ) ||
				( in_array( $name, array( 'href', 'src', 'action', 'xlink:href' ), true )
					&& 0 === strpos( $value, 'javascript' ) )
			) {
				if ( $attr->ownerElement instanceof DOMElement ) {
					$attr->ownerElement->removeAttributeNode( $attr );
				}
			}
		}

		$clean = $dom->saveXML( $dom->documentElement );
		if ( $clean ) {
			file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		return $file;
	}
);
