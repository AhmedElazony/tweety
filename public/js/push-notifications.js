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
        // Wait a bit longer for the service worker to be fully ready
        await new Promise((resolve) => setTimeout(resolve, 1000));

        const registration = await navigator.serviceWorker.ready;
        console.log("Service Worker ready, attempting subscription...");

        // Check for existing subscription and unsubscribe first
        let subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            await subscription.unsubscribe();
            console.log("Unsubscribed from existing subscription");
        }

        // Get VAPID key with timeout
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 5000);

        const response = await fetch("/push/key", {
            signal: controller.signal,
        });
        clearTimeout(timeout);

        if (!response.ok) {
            throw new Error(`Failed to fetch VAPID key: ${response.status}`);
        }

        const data = await response.json();
        if (!data.key) {
            throw new Error("Invalid VAPID key received from server");
        }

        // Log VAPID key format for debugging
        console.log("VAPID key received, length:", data.key.length);

        const convertedKey = urlBase64ToUint8Array(data.key);

        // Add retry logic for subscribe
        let retries = 3;
        while (retries > 0) {
            try {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedKey,
                });
                console.log("New push subscription created:", subscription);
                return await updateSubscriptionOnServer(subscription);
            } catch (error) {
                retries--;
                if (error.name === "NotAllowedError") {
                    throw new Error("Push notification permission denied");
                }
                if (retries === 0) throw error;
                console.log(
                    `Subscribe attempt failed, retrying... (${retries} attempts left)`
                );
                await new Promise((resolve) => setTimeout(resolve, 1000));
            }
        }
    } catch (error) {
        console.error("Push subscription error details:", {
            name: error.name,
            message: error.message,
            stack: error.stack,
        });
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
    // Ensure page is fully loaded
    window.addEventListener("load", () => {
        navigator.serviceWorker
            .register("/serviceworker.js")
            .then(async (registration) => {
                console.log("ServiceWorker registered successfully");
                // Wait for service worker to be activated
                if (registration.active) {
                    return requestNotificationPermission();
                }

                await new Promise((resolve) => {
                    registration.addEventListener("activate", () => resolve());
                });
                return requestNotificationPermission();
            })
            .catch((error) => {
                console.error("ServiceWorker registration failed:", error);
            });
    });
} else {
    console.log("Push notifications not supported");
}
