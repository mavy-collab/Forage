const CACHE_VERSION = 'forage-v1';
const STATIC_CACHE  = CACHE_VERSION + '-static';
const PAGES_CACHE   = CACHE_VERSION + '-pages';

// Pages et assets mis en cache à l'installation
const PRECACHE_URLS = ['/offline.html'];

// Patterns d'assets statiques → cache-first
const STATIC_PATTERNS = [
    /\.(css|js|woff2?|ttf|eot|otf|ico|png|jpg|svg|gif)(\?.*)?$/,
    /fonts\.googleapis\.com/,
    /fonts\.gstatic\.com/,
    /cdnjs\.cloudflare\.com/,
    /cdn\.jsdelivr\.net/,
];

// ─── Install ────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// ─── Activate : nettoyage des anciens caches ─────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys
                    .filter(k => k !== STATIC_CACHE && k !== PAGES_CACHE)
                    .map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ─── Fetch ──────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // On ne touche pas aux requêtes POST/PUT/DELETE
    if (request.method !== 'GET') return;

    // Assets statiques (local + CDN) → cache-first
    if (STATIC_PATTERNS.some(p => p.test(url.href))) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // Pages HTML → network-first avec fallback offline
    if (request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(networkFirstHtml(request));
        return;
    }

    // Reste (JSON, etc.) → network-first simple
    event.respondWith(networkFirst(request, PAGES_CACHE));
});

// ─── Background Sync ────────────────────────────────────────────
self.addEventListener('sync', event => {
    if (event.tag === 'forage-sync') {
        event.waitUntil(notifyClientsToSync());
    }
});

async function notifyClientsToSync() {
    const clients = await self.clients.matchAll({ includeUncontrolled: true });
    clients.forEach(client => client.postMessage({ type: 'SW_SYNC_REQUESTED' }));
}

// ─── Stratégies de cache ────────────────────────────────────────
async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok || response.type === 'opaque') {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 503, statusText: 'Service Unavailable' });
    }
}

async function networkFirst(request, cacheName) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response('', { status: 503 });
    }
}

async function networkFirstHtml(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(PAGES_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        // Essaie la page en cache
        const cached = await caches.match(request);
        if (cached) return cached;
        // Page offline générique
        const offline = await caches.match('/offline.html');
        return offline || new Response(
            '<html><body style="font-family:sans-serif;text-align:center;padding:60px"><h1>Hors ligne</h1><p>Vérifiez votre connexion.</p></body></html>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}
