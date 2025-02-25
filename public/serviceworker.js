var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    "/offline",
    "/css/app.css",
    "/js/app.js",
    "/images/icons/icon-72x72.png",
    "/images/icons/icon-96x96.png",
    "/images/icons/icon-128x128.png",
    "/images/icons/icon-144x144.png",
    "/images/icons/icon-152x152.png",
    "/images/icons/icon-192x192.png",
    "/images/icons/icon-384x384.png",
    "/images/icons/icon-512x512.png",
];

// Cache on install with better error handling
self.addEventListener("install", (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName).then(function (cache) {
            console.log("Caching app assets");

            // Use individual promises for each resource
            return Promise.allSettled(
                filesToCache.map((url) => {
                    return fetch(url)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(`Failed to fetch ${url}`);
                            }
                            return cache.put(url, response);
                        })
                        .catch((error) => {
                            console.warn(`Caching failed for ${url}:`, error);
                            // Continue with other resources
                            return Promise.resolve();
                        });
                })
            );
        })
    );
});

// Clear cache on activate
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith("pwa-"))
                    .filter((cacheName) => cacheName !== staticCacheName)
                    .map((cacheName) => caches.delete(cacheName))
            );
        })
    );
    // Take control immediately
    return self.clients.claim();
});

// Modified fetch handler - network first for navigation requests
self.addEventListener("fetch", (event) => {
    // Check if this is a navigation request (for HTML)
    if (event.request.mode === "navigate") {
        event.respondWith(
            // Try network first, fall back to cache
            fetch(event.request).catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // If not in cache, try the offline page
                    return caches.match("/offline");
                });
            })
        );
    } else {
        // For non-navigation requests, try cache first
        event.respondWith(
            caches.match(event.request).then((response) => {
                return (
                    response ||
                    fetch(event.request).catch(() => {
                        // For images, could return a default image
                        // For now, just return null
                        return null;
                    })
                );
            })
        );
    }
});

// push notifications
self.addEventListener("push", function (event) {
    if (!event.data) return;

    const data = event.data.json();

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || "/icon.png",
        })
    );
});

self.addEventListener("notificationclick", function (event) {
    event.notification.close();
    event.waitUntil(clients.openWindow("/"));
});
