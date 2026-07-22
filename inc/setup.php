<?php
/**
 * Theme-Setup: title-tag, thumbnails, SEO-Title.
 * In functions.php:  require get_theme_file_path('inc/setup.php');
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Asset-Version — EINZIGE Quelle der Wahrheit.
 *
 * Vorher lagen drei Versionen nebeneinander: $ver in inc/enqueue.php (3.13.0),
 * CACHE in assets/js/sw.js (3.13.0) und die Theme-Version in style.css (1.0.1),
 * die inc/uploads-cache-bust.php benutzt. Nichts erzwang, dass sie zusammen
 * wandern — und genau daraus entstand ein Service Worker, der veraltetes CSS
 * auslieferte, obwohl zwei der drei Werte korrekt erhöht worden waren.
 *
 * Ab jetzt: NUR die Version in style.css erhöhen. Alles andere folgt.
 *
 * @return string
 */
function kc_asset_version() {
	static $version = null;

	if ( null === $version ) {
		$version = (string) wp_get_theme()->get( 'Version' );
		if ( '' === $version ) {
			$version = '0';
		}
	}

	return $version;
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

// Sanitize les SVG uploadés — allowlist stricte des éléments + attributs dangereux.
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

		// Bloquer XXE et billion-laughs avant tout parsing DOM.
		// <!DOCTYPE> / <!ENTITY> permettent l'expansion d'entités (DoS) et l'exfiltration de fichiers.
		if ( preg_match( '/<!(DOCTYPE|ENTITY)/i', $svg ) ) {
			$file['error'] = 'SVG enthält unzulässige DOCTYPE/ENTITY-Deklaration.';
			return $file;
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadXML( $svg, LIBXML_NONET );
		libxml_clear_errors();

		$xpath = new DOMXPath( $dom );

		// Éléments dangereux : vecteurs XSS directs ou porteurs d'attributs exécutables.
		$dangerous_tags = array(
			'script',        // JS direct
			'foreignObject', // HTML arbitraire embarqué
			'style',         // @import (fuite IP/DSGVO), expression() hérité
			'animate',       // attributeName="onload" to="alert(1)"
			'animatetransform',
			'animatemotion',
			'set',           // <set attributeName="href" to="javascript:...">
			'handler',
			'listener',
		);
		// iterator_to_array() matérialise la DOMNodeList (live) en tableau PHP statique.
		// Sans ça, removeChild() pendant le foreach réindexe la liste et saute le nœud suivant.
		foreach ( $dangerous_tags as $tag ) {
			foreach ( iterator_to_array( $xpath->query( "//*[local-name()='" . $tag . "']" ) ) as $node ) {
				if ( $node->parentNode ) {
					$node->parentNode->removeChild( $node );
				}
			}
		}

		// Attributs dangereux : event handlers on* + protocoles exécutables.
		$unsafe_protocols = array( 'javascript', 'data', 'vbscript' );
		$url_attrs        = array( 'href', 'src', 'action' );

		foreach ( iterator_to_array( $xpath->query( '//@*' ) ) as $attr ) {
			if ( ! ( $attr instanceof DOMAttr ) ) {
				continue;
			}
			$name  = strtolower( $attr->localName );
			$value = strtolower( trim( $attr->value ) );

			$is_event_handler = ( 0 === strpos( $name, 'on' ) );
			$is_unsafe_url    = in_array( $name, $url_attrs, true ) &&
				array_reduce(
					$unsafe_protocols,
					static function ( $carry, $proto ) use ( $value ) {
						return $carry || ( 0 === strpos( $value, $proto ) );
					},
					false
				);

			if ( $is_event_handler || $is_unsafe_url ) {
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
