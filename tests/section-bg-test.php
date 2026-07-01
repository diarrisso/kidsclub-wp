<?php
/**
 * Eigenständiger Test der reinen Helper aus inc/section-bg.php.
 * Kein WP-Bootstrap — die Escaping-Funktionen werden gestubbt.
 * Run: php tests/section-bg-test.php
 */
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $u ) {
		return $u; } }
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $s ) {
		return $s; } }

require __DIR__ . '/../inc/section-bg.php';

$failed = 0;
function check( $label, $actual, $expected ) {
	global $failed;
	if ( $actual === $expected ) {
		echo "PASS: $label\n";
	} else {
		++$failed;
		echo "FAIL: $label\n  expected: " . var_export( $expected, true ) . "\n  actual:   " . var_export( $actual, true ) . "\n";
	}
}

// hex -> rgb
check( 'hex 6-stellig', kc_section_bg_hex_to_rgb( '#0E3A8E' ), '14,58,142' );
check( 'hex 3-stellig', kc_section_bg_hex_to_rgb( '#fff' ), '255,255,255' );
check( 'hex ungültig->weiß', kc_section_bg_hex_to_rgb( 'xyz' ), '255,255,255' );

// build_style
check( 'leer -> ""', kc_section_bg_build_style( [] ), '' );

check( 'nur Farbe', kc_section_bg_build_style( [ 'color' => '#ffffff' ] ), 'background-color:#ffffff' );

check(
	'Bild Default (Deckkraft 8 -> alpha 0.92, weiß)',
	kc_section_bg_build_style( [ 'img' => 'http://x/a.png' ] ),
	'background-image:linear-gradient(rgba(255,255,255,0.92),rgba(255,255,255,0.92)),url(http://x/a.png);background-size:115%;background-position:center top;background-repeat:no-repeat'
);

check(
	'Bild + Farbe (Schleier nimmt Farbe)',
	kc_section_bg_build_style(
		[
			'img'      => 'http://x/a.png',
			'color'    => '#000000',
			'opacity'  => 20,
			'size'     => 'cover',
			'position' => 'center',
		]
	),
	'background-color:#000000;background-image:linear-gradient(rgba(0,0,0,0.8),rgba(0,0,0,0.8)),url(http://x/a.png);background-size:cover;background-position:center;background-repeat:no-repeat'
);

echo $failed === 0 ? "\nALL PASS\n" : "\n$failed FAILED\n";
exit( $failed === 0 ? 0 : 1 );
