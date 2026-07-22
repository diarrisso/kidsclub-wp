/**
 * Kids Club by zacp — Service Worker
 * Stale-while-revalidate für Assets, network-first für HTML-Seiten.
 *
 * VERSION wird beim Ausliefern von inc/pwa.php eingesetzt; einzige Quelle der
 * Wahrheit ist die Version in style.css (siehe kc_asset_version()). Diese Datei
 * wird ausschließlich über /sw.js ausgeliefert, nie direkt aus dem Theme-Ordner.
 */

const RAW_VERSION = '__KC_VERSION__';
// Wurde der Platzhalter nicht ersetzt, wäre der Cache-Name konstant — und damit
// wieder unbeweglich. Sichtbar machen statt still danebengehen.
const VERSION = ( RAW_VERSION.charAt( 0 ) === '_' ) ? 'unversioned' : RAW_VERSION;

const CACHE = 'kidsclub-v' + VERSION;
const THEME = '/wp-content/themes/kidsclub';
const OFFLINE_URL = '/offline';

/**
 * Nur das, was eine Offline-Navigation wirklich braucht.
 *
 * CSS und JS stehen bewusst NICHT mehr hier: WordPress hängt an jedes Asset ein
 * ?ver=…, der Precache kannte diese Query nicht, und der Fetch-Handler glich das
 * früher mit ignoreSearch:true aus. Damit traf die unversionierte Kopie IMMER —
 * eine neue Version konnte den Cache nie mehr durchbrechen. Jetzt werden CSS/JS
 * beim ersten echten Request unter ihrer exakten, versionierten URL abgelegt.
 */
const PRECACHE = [
    '/',
    OFFLINE_URL,
    THEME + '/assets/img/logo-quer-white.svg',
    THEME + '/assets/img/pwa-icon-192.png',
];

// ── Install : pré-cache ──────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            // cache:'reload' contourne le cache HTTP du navigateur. Sans cela, un SW
            // tout neuf peut mettre en cache un fichier PÉRIMÉ : le cache porte alors
            // le nouveau nom et l'ancien contenu — panne invisible, indébogable.
            .then((cache) => cache.addAll(
                PRECACHE.map((u) => new Request(u, { cache: 'reload' }))
            ))
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

    // Assets statiques → stale-while-revalidate, correspondance EXACTE (query comprise).
    // url.pathname ne contient jamais la query : inutile de la tester ici.
    if (/\.(css|js|woff2?|ttf|otf|svg|png|jpe?g|webp|ico|gif)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const fromNetwork = fetch(request)
                    .then((resp) => {
                        if (resp && resp.ok) {
                            const clone = resp.clone();
                            caches.open(CACHE).then((c) => c.put(request, clone));
                        }
                        return resp;
                    })
                    .catch(() => cached);

                // Réponse immédiate si on l'a, rafraîchissement en arrière-plan :
                // même une entrée périmée se répare d'elle-même au chargement suivant.
                return cached || fromNetwork;
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
