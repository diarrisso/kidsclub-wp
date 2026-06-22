<?php
/**
 * Kids Club by zacp — PWA Integration
 * - Manifest + meta Apple/Android dans <head>
 * - Service Worker servi à /sw.js via rewrite WP
 * - Page offline à /offline via rewrite WP
 * - Install prompt (banner discret)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── <head> : manifest + meta PWA ────────────────────────────────────────────
add_action(
	'wp_head',
	function () {
		$theme    = get_stylesheet_directory_uri();
		$icon_192 = $theme . '/assets/img/pwa-icon-192.png';
		$manifest = $theme . '/assets/manifest.json';
		?>
<link rel="manifest" href="<?php echo esc_url( $manifest ); ?>">
<meta name="theme-color" content="#E91E8C">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Kids Club">
<link rel="apple-touch-icon" href="<?php echo esc_url( $icon_192 ); ?>">
		<?php
	},
	1
);

// ── Rewrite rules : /sw.js et /offline ──────────────────────────────────────
add_action(
	'init',
	function () {
		add_rewrite_rule( '^sw\.js$', 'index.php?kc_pwa=sw', 'top' );
		add_rewrite_rule( '^offline$', 'index.php?kc_pwa=offline', 'top' );
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'kc_pwa';
		return $vars;
	}
);

add_action(
	'template_redirect',
	function () {
		$pwa = get_query_var( 'kc_pwa' );
		if ( ! $pwa ) {
			return;
		}

		if ( 'sw' === $pwa ) {
			$sw_path = get_theme_file_path( 'assets/js/sw.js' );
			if ( ! file_exists( $sw_path ) ) {
				status_header( 404 );
				exit;
			}
			header( 'Content-Type: application/javascript; charset=utf-8' );
			header( 'Cache-Control: no-cache, no-store, must-revalidate' );
			header( 'Service-Worker-Allowed: /' );
			readfile( $sw_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			exit;
		}

		if ( 'offline' === $pwa ) {
			status_header( 200 );
			header( 'Content-Type: text/html; charset=utf-8' );
			header( 'Cache-Control: no-store' );
			include get_theme_file_path( 'template-parts/offline.php' );
			exit;
		}
	}
);

// ── Flush rewrite rules à l'activation du thème ─────────────────────────────
add_action(
	'after_switch_theme',
	static function () {
		add_rewrite_rule( '^sw\.js$', 'index.php?kc_pwa=sw', 'top' );
		add_rewrite_rule( '^offline$', 'index.php?kc_pwa=offline', 'top' );
		flush_rewrite_rules();
	}
);

// ── Service Worker : enregistrement + install prompt ─────────────────────────
add_action(
	'wp_footer',
	function () {
		if ( is_admin() ) {
			return;
		}
		?>
<div id="kc-install-banner" class="kc-install-banner" hidden aria-live="polite">
	<p class="kc-install-banner__text">
		<strong>Kids Club</strong> als App speichern
	</p>
	<button type="button" id="kc-install-btn" class="kc-install-banner__btn">
		Installieren
	</button>
	<button type="button" id="kc-install-close" class="kc-install-banner__close" aria-label="Schließen">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
			stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
			<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
		</svg>
	</button>
</div>
<script>
(function () {
	'use strict';

	// ── Service Worker ──────────────────────────────────────────────────────
	if ('serviceWorker' in navigator) {
		window.addEventListener('load', function () {
			navigator.serviceWorker.register('/sw.js', { scope: '/' })
				.catch(function (err) {
					console.warn('[Kids Club SW]', err);
				});
		});
	}

	// ── Install prompt ──────────────────────────────────────────────────────
	var deferredPrompt = null;
	var banner  = document.getElementById('kc-install-banner');
	var btnInstall = document.getElementById('kc-install-btn');
	var btnClose   = document.getElementById('kc-install-close');

	// Vérifier le dismissal AVANT d'enregistrer le listener principal
	var isDismissed = false;
	try {
		var dismissed = localStorage.getItem('kc_pwa_dismissed');
		if (dismissed && (Date.now() - Number(dismissed)) < 7 * 24 * 60 * 60 * 1000) {
			isDismissed = true;
		}
	} catch (e) {}

	window.addEventListener('beforeinstallprompt', function (e) {
		e.preventDefault();
		if (isDismissed) return;
		deferredPrompt = e;
		if (banner) {
			// Afficher après 3 s pour ne pas interrompre le chargement
			setTimeout(function () { banner.hidden = false; }, 3000);
		}
	});

	if (btnInstall) {
		btnInstall.addEventListener('click', function () {
			if (!deferredPrompt) return;
			deferredPrompt.prompt();
			deferredPrompt.userChoice.then(function () {
				deferredPrompt = null;
				if (banner) banner.hidden = true;
			});
		});
	}

	if (btnClose) {
		btnClose.addEventListener('click', function () {
			if (banner) banner.hidden = true;
			// Ne plus montrer pendant 7 jours
			try {
				localStorage.setItem('kc_pwa_dismissed', String(Date.now()));
			} catch (e) {}
		});
	}

	// Masquer si déjà installé
	window.addEventListener('appinstalled', function () {
		if (banner) banner.hidden = true;
	});
}());
</script>
		<?php
	},
	99
);
