function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, "+")
        .replace(/_/g, "/");

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function subscribeToPush() {
    navigator.serviceWorker.ready
        .then(function (registration) {
            console.log("Service Worker ready, attempting subscription...");
            return registration.pushManager
                .getSubscription()
                .then(async function (subscription) {
                    if (subscription) {
                        console.log(
                            "Existing subscription found:",
                            subscription
                        );
                        return subscription;
                    }

                    const response = await fetch("/push/key");
                    const data = await response.json();
                    console.log("VAPID key received:", data.key);

                    const convertedKey = urlBase64ToUint8Array(data.key);
                    return registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedKey,
                    });
                });
        })
        .then(function (subscription) {
            console.log("Sending subscription to server:", subscription);
            return fetch("/push/subscribe", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    subscription: subscription,
                }),
            });
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Server response was not ok");
            }
            return response.json();
        })
        .then(function (data) {
            console.log("Push subscription saved successfully:", data);
        })
        .catch(function (error) {
            console.error("Push subscription error:", error);
        });
}

function requestNotificationPermission() {
    return new Promise(function (resolve, reject) {
        Notification.requestPermission(function (permission) {
            if (permission === "granted") {
                console.log("Notification permission granted");
                subscribeToPush();
                resolve(true);
            } else {
                console.log("Notification permission denied");
                resolve(false);
            }
        });
    });
}

// Initialize push notifications
if ("serviceWorker" in navigator && "PushManager" in window) {
    navigator.serviceWorker
        .register("/serviceworker.js")
        .then(function (registration) {
            console.log("ServiceWorker registered successfully");
            requestNotificationPermission();
        })
        .catch(function (error) {
            console.error("ServiceWorker registration failed:", error);
        });
}
