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

// Cache on install
self.addEventListener("install", (event) => {
    this.skipWaiting();
    event.waitUntil(
        caches.open("tweety-cache-v1").then(function (cache) {
            const resources = [
                "/",
                "/css/app.css",
                "/js/app.js",
                // Add other resources you want to cache
            ];

            // Cache resources with error handling
            return Promise.all(
                resources.map((url) => {
                    return fetch(url)
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(
                                    `Failed to cache ${url}: ${response.status} ${response.statusText}`
                                );
                            }
                            return cache.put(url, response);
                        })
                        .catch((error) => {
                            console.error("Caching failed for:", url, error);
                            // Continue with other resources even if one fails
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
});

// Serve from Cache
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches
            .match(event.request)
            .then((response) => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match("offline");
            })
    );
});

// push notifications
self.addEventListener("push", function (event) {
    console.log("Push message received", event);

    if (!(self.Notification && self.Notification.permission === "granted")) {
        console.log("Notifications not granted");
        return;
    }

    try {
        const data = event.data.json();
        console.log("Push data:", data);

        event.waitUntil(
            self.registration.showNotification(data.title, {
                body: data.body,
                icon: data.icon,
                actions: data.actions,
                data: data.data,
            })
        );
    } catch (e) {
        console.error("Error processing push notification:", e);
    }
});
