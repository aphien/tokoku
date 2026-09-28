const CACHE_NAME = 'tokoku-pwa-cache-v2.4.8';
const PRECACHE_ASSETS = [
    '/',
    '/wp-content/themes/tokoku/style.css',
    '/wp-content/themes/tokoku/assets/css/main.css',
    '/wp-content/themes/tokoku/assets/js/main.js',
];

// Install: precache critical assets
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_ASSETS).catch(err => console.warn('Precache partial fail:', err));
        })
    );
});

// Activate: clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch handler
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Bypass non-GET requests and admin / AJAX / preview URLs
    if (
        request.method !== 'GET' ||
        url.pathname.includes('/wp-admin') ||
        url.pathname.includes('/wp-login.php') ||
        url.pathname.includes('admin-ajax.php') ||
        url.searchParams.has('preview')
    ) {
        return;
    }

    // 1. Navigation (HTML Pages) -> Network first, fallback to cache, then offline
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (response && response.status === 200) {
                        const copy = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(() => caches.match(request).then(cached => cached || caches.match('/')))
        );
        return;
    }

    // 2. Static Assets (CSS, JS, Fonts, Images) -> Cache first with network update (stale-while-revalidate)
    event.respondWith(
        caches.match(request).then(cachedResponse => {
            const fetchPromise = fetch(request).then(networkResponse => {
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, responseToCache));
                }
                return networkResponse;
            }).catch(() => {/* Ignore network fetch failures for assets */});

            return cachedResponse || fetchPromise;
        })
    );
});
