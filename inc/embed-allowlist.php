<?php
/**
 * Embed-Allowlist: iframe-src auf vertrauenswürdige Buchungs-Domains beschränken.
 * Verhindert, dass ein Redakteur (ohne unfiltered_html) beliebige Seiten
 * per iframe einbettet (Phishing-Overlay).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prüft, ob ALLE iframe-src im HTML auf eine erlaubte Domain (https) zeigen.
 * Erlaubt: doctolib.de / doctolib.fr inkl. Subdomains (www, partners, …).
 *
 * @param string $html Bereits durch wp_kses gefiltertes Embed-HTML.
 * @return bool True nur wenn mind. ein iframe existiert und alle src gültig sind.
 */
function kc_embed_hosts_allowed( $html ) {
	$allowed_hosts = [ 'doctolib.de', 'doctolib.fr' ];

	if ( ! preg_match_all( '/<iframe[^>]+src=["\']([^"\']+)["\']/i', $html, $matches ) ) {
		return false; // kein iframe mit src → nichts zu rendern
	}

	foreach ( $matches[1] as $src ) {
		$scheme = strtolower( (string) wp_parse_url( $src, PHP_URL_SCHEME ) );
		$host   = strtolower( (string) wp_parse_url( $src, PHP_URL_HOST ) );

		if ( 'https' !== $scheme || '' === $host ) {
			return false;
		}

		$host_ok = false;
		foreach ( $allowed_hosts as $allowed ) {
			// exakte Domain ODER Subdomain (".doctolib.de"-Suffix) — PHP 7.4-kompatibel
			if ( $host === $allowed
				|| substr( $host, -strlen( '.' . $allowed ) ) === '.' . $allowed ) {
				$host_ok = true;
				break;
			}
		}
		if ( ! $host_ok ) {
			return false;
		}
	}

	return true;
}
