<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kein Internet – Kids Club by zacp</title>
	<style>
	*{box-sizing:border-box;margin:0;padding:0}
	body{min-height:100vh;display:flex;flex-direction:column;align-items:center;
		justify-content:center;background:#0E3A8E;color:#fff;font-family:Arial,sans-serif;
		padding:32px 24px;text-align:center}
	svg{width:64px;height:64px;margin-bottom:28px;opacity:.9}
	h1{font-size:clamp(1.4rem,4vw,2rem);font-weight:700;margin-bottom:12px}
	p{font-size:1rem;line-height:1.6;max-width:380px;opacity:.85;margin-bottom:8px}
	.phone{margin-top:28px;font-size:1.1rem;font-weight:600}
	.phone a{color:#E91E8C;text-decoration:none}
	.retry{margin-top:24px;padding:12px 28px;background:#E91E8C;color:#fff;
			border:none;border-radius:999px;font-size:.95rem;font-weight:600;
			cursor:pointer;font-family:inherit}
	.retry:hover{opacity:.88}
	.logo{margin-bottom:32px;opacity:.9}
	.logo img{height:40px;width:auto}
	</style>
</head>
<body>
	<div class="logo">
	<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-quer-white.svg' ) ); ?>"
		alt="Kids Club by zacp" width="170" height="49">
	</div>

	<!-- Wifi off icon -->
	<svg viewBox="0 0 24 24" fill="none" stroke="#E91E8C" stroke-width="1.8"
		stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
	<line x1="1" y1="1" x2="23" y2="23"/>
	<path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
	<path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
	<path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
	<path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
	<path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
	<circle cx="12" cy="20" r="1" fill="#E91E8C" stroke="none"/>
	</svg>

	<h1>Keine Internetverbindung</h1>
	<p>Diese Seite ist gerade nicht erreichbar. Bitte prüfen Sie Ihre Verbindung.</p>

	<?php
	// Die Nummer stand hart im Template — eine Praxis, die umzieht, hätte hier eine tote
	// Nummer stehen lassen. Quelle der Wahrheit ist jetzt dasselbe Optionsfeld wie im
	// Header und im Footer; ist es leer, verschwindet der Block, statt ins Leere zu führen.
	$kc_off_phone  = (string) ( function_exists( 'get_field' ) ? ( get_field( 'footer_phone', 'option' ) ?: '' ) : '' );
	$kc_off_digits = preg_replace( '/[^+\d]/', '', $kc_off_phone );
	?>
	<?php if ( '' !== $kc_off_phone ) : ?>
	<p>Für dringende Anfragen erreichen Sie uns telefonisch.</p>
	<p class="phone">
	<a href="tel:<?php echo esc_attr( $kc_off_digits ); ?>"><?php echo esc_html( $kc_off_phone ); ?></a>
	</p>
	<?php endif; ?>

	<button class="retry" onclick="window.location.reload()">
	Erneut versuchen
	</button>
</body>
</html>
