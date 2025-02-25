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
        // Wait for the service worker to be fully active
        await new Promise((resolve) => setTimeout(resolve, 2000));

        const registration = await navigator.serviceWorker.ready;
        console.log("Service Worker ready, attempting subscription...");

        // Check for existing subscription
        let subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            console.log("Using existing subscription");
            return await updateSubscriptionOnServer(subscription);
        }

        // Get VAPID key - remove the AbortController to prevent AbortError
        console.log("Fetching VAPID key...");
        const response = await fetch("/push/key", {
            headers: {
                Accept: "application/json",
                "Cache-Control": "no-cache",
            },
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch VAPID key: ${response.status}`);
        }

        const data = await response.json();
        console.log("Received key data:", data);

        if (!data.key || typeof data.key !== "string") {
            throw new Error("Invalid VAPID key format received from server");
        }

        const convertedKey = urlBase64ToUint8Array(data.key);
        console.log("Converted VAPID key, attempting to subscribe...");

        // Subscribe with just one attempt first
        try {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedKey,
            });

            console.log("Push subscription created successfully");
            return await updateSubscriptionOnServer(subscription);
        } catch (error) {
            console.error("Initial subscription attempt failed:", error);

            if (error.name === "NotAllowedError") {
                throw new Error("Push notification permission denied");
            }

            // Log more details about the error
            console.error("Error details:", {
                name: error.name,
                message: error.message,
                stack: error.stack,
            });

            throw error;
        }
    } catch (error) {
        console.error("Push subscription error details:", {
            name: error.name,
            message: error.message,
            stack: error.stack,
            timestamp: new Date().toISOString(),
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
