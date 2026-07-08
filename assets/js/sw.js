/**
 * Kids Club by zacp — Service Worker
 * Cache-first pour les assets, network-first pour les pages HTML.
 */

const CACHE = 'kidsclub-v3.5.0';
const THEME = '/wp-content/themes/kidsclub';
const OFFLINE_URL = '/offline';

const PRECACHE = [
    '/',
    OFFLINE_URL,
    THEME + '/assets/css/kidsclub.min.css',
    THEME + '/assets/css/fonts.css',
    THEME + '/assets/js/kidsclub.min.js',
    THEME + '/assets/vendor/swiper-bundle.min.css',
    THEME + '/assets/vendor/swiper-bundle.min.js',
    THEME + '/assets/vendor/alpine.min.js',
    THEME + '/assets/img/logo-quer-white.svg',
    THEME + '/assets/img/pwa-icon-192.png',
];

// ── Install : pré-cache ──────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

// ── Activate : vider les anciens caches ─────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ── Fetch ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorer : non-GET, wp-admin, wp-json, wp-cron, masinga (booking externe)
    if (request.method !== 'GET') return;
    if (/\/(wp-admin|wp-json|wp-cron|masinga)/.test(url.pathname)) return;
    if (url.origin !== self.location.origin) return;

    // Assets statiques (css, js, fonts, images) → cache-first
    // ignoreSearch:true : WordPress ajoute ?ver=X.X aux assets, le PRECACHE ne les inclut pas.
    if (/\.(css|js|woff2?|ttf|otf|svg|png|jpe?g|webp|ico|gif)(\?.*)?$/.test(url.pathname)) {
        event.respondWith(
            caches.match(request, { ignoreSearch: true }).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((resp) => {
                    if (resp.ok) {
                        const respClone = resp.clone();
                        caches.open(CACHE).then((c) => c.put(request, respClone));
                    }
                    return resp;
                });
            })
        );
        return;
    }

    // Pages HTML → network-first, fallback cache puis /offline
    if (request.headers.get('accept') && request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((resp) => {
                    if (resp.ok) {
                        const respClone = resp.clone();
                        caches.open(CACHE).then((c) => c.put(request, respClone));
                    }
                    return resp;
                })
                .catch(() =>
                    caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL))
                )
        );
    }
});
