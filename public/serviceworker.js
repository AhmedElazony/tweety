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
        caches.open(staticCacheName).then((cache) => {
            return cache.addAll(filesToCache);
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
    if (!(self.Notification && self.Notification.permission === "granted")) {
        return;
    }

    const data = event.data?.json() ?? {};

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            actions: data.actions,
            data: data.data,
        })
    );
});

self.addEventListener("notificationclick", function (event) {
    event.notification.close();

    if (event.action === "view_message") {
        const messageUrl = "/chats"; // Adjust this URL as needed
        event.waitUntil(clients.openWindow(messageUrl));
    }
});
