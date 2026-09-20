// Kelvs Admin Panel Service Worker
const CACHE_NAME = 'kelvs-admin-pwa-v2';
const PRECACHE_ASSETS = [
    '/admin.webmanifest',
    '/images/pwa/kelvs-icon-192.png',
    '/images/pwa/kelvs-icon-512.png',
    '/images/pwa/kelvs-apple-touch-icon.png'
];

// Install: precache essential shell assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Network-First strategy
// CRITICAL: We always want fresh order data, never stale cached orders or outdated status!
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Only intercept requests for our own origin
    if (url.origin !== self.location.origin) {
        return;
    }

    // Only intercept admin, lunar, and PWA precache assets
    if (!url.pathname.startsWith('/lunar') && !url.pathname.startsWith('/admin') && !PRECACHE_ASSETS.includes(url.pathname)) {
        return;
    }

    // Static assets in PRECACHE_ASSETS: Cache first with network fallback
    if (PRECACHE_ASSETS.includes(url.pathname)) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                return cachedResponse || fetch(request);
            })
        );
        return;
    }

    // All admin navigation and API calls: ALWAYS network-first to ensure live data
    event.respondWith(
        fetch(request)
            .then((response) => {
                return response;
            })
            .catch(() => {
                // If offline and request is for a cached asset, try returning from cache
                return caches.match(request);
            })
    );
});
