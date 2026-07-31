const STATIC_CACHE = 'site-static-v1';
const STATIC_PATTERNS = [/\/build\//, /\/icons\//];

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Cache-first for built assets and icons only. Everything else (HTML pages,
// API calls) goes straight to the network so visitors never see stale news.
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    const isStaticAsset = STATIC_PATTERNS.some((pattern) => pattern.test(url.pathname));

    if (event.request.method !== 'GET' || !isStaticAsset) {
        return;
    }

    event.respondWith(
        caches.open(STATIC_CACHE).then((cache) =>
            cache.match(event.request).then(
                (cached) =>
                    cached ||
                    fetch(event.request).then((response) => {
                        cache.put(event.request, response.clone());
                        return response;
                    })
            )
        )
    );
});

self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }

    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { title: 'Nouvelle notification', body: event.data.text() };
    }

    event.waitUntil(
        self.registration.showNotification(payload.title || 'Nouvelle notification', {
            body: payload.body || '',
            icon: payload.icon || '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: { url: payload.data?.url || payload.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
