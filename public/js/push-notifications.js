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

async function subscribeToPush() {
    try {
        const registration = await navigator.serviceWorker.ready;
        console.log("Service Worker ready, attempting subscription...");

        // Check for existing subscription
        let subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            console.log("Existing subscription found:", subscription);
            return await updateSubscriptionOnServer(subscription);
        }

        // Get VAPID key
        const response = await fetch("/push/key");
        if (!response.ok) {
            throw new Error(`Failed to fetch VAPID key: ${response.status}`);
        }

        const data = await response.json();
        if (!data.key) {
            throw new Error("Invalid VAPID key received from server");
        }
        console.log("VAPID key received");

        // Create new subscription
        const convertedKey = urlBase64ToUint8Array(data.key);
        try {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedKey,
            });
            console.log("New push subscription created:", subscription);
            return await updateSubscriptionOnServer(subscription);
        } catch (error) {
            if (error.name === "NotAllowedError") {
                throw new Error("Push notification permission denied");
            }
            throw error;
        }
    } catch (error) {
        console.error("Push subscription error details:", error);
        throw error;
    }
}

async function updateSubscriptionOnServer(subscription) {
    const response = await fetch("/push/subscribe", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ subscription }),
    });

    if (!response.ok) {
        throw new Error(`Server subscription failed: ${response.status}`);
    }

    const data = await response.json();
    console.log("Push subscription saved successfully:", data);
    return data;
}

function requestNotificationPermission() {
    return new Promise((resolve, reject) => {
        if (!("Notification" in window)) {
            console.log("Notifications not supported");
            resolve(false);
            return;
        }

        Notification.requestPermission()
            .then((permission) => {
                if (permission === "granted") {
                    console.log("Notification permission granted");
                    subscribeToPush()
                        .then(() => resolve(true))
                        .catch((error) => {
                            console.error("Subscribe failed:", error);
                            resolve(false);
                        });
                } else {
                    console.log("Notification permission denied");
                    resolve(false);
                }
            })
            .catch((error) => {
                console.error("Permission request failed:", error);
                resolve(false);
            });
    });
}

// Initialize push notifications
if ("serviceWorker" in navigator && "PushManager" in window) {
    navigator.serviceWorker
        .register("/serviceworker.js")
        .then((registration) => {
            console.log("ServiceWorker registered successfully");
            return requestNotificationPermission();
        })
        .catch((error) => {
            console.error("ServiceWorker registration failed:", error);
        });
} else {
    console.log("Push notifications not supported");
}
