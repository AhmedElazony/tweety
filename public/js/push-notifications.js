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
        // Increase initial wait time for service worker
        await new Promise((resolve) => setTimeout(resolve, 2000));

        const registration = await navigator.serviceWorker.ready;
        console.log("Service Worker ready, attempting subscription...");

        // Check if push manager is available
        if (!registration.pushManager) {
            throw new Error("Push manager not available");
        }

        // Check for existing subscription but don't unsubscribe immediately
        let subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            try {
                // Verify if existing subscription is still valid
                await subscription.getKey("p256dh");
                console.log("Using existing valid subscription");
                return await updateSubscriptionOnServer(subscription);
            } catch (e) {
                console.log("Existing subscription invalid, creating new one");
                await subscription.unsubscribe();
            }
        }

        // Get VAPID key with increased timeout
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000);

        try {
            const response = await fetch("/push/key", {
                signal: controller.signal,
                headers: {
                    Accept: "application/json",
                    "Cache-Control": "no-cache",
                },
            });
            clearTimeout(timeout);

            if (!response.ok) {
                throw new Error(
                    `Failed to fetch VAPID key: ${response.status}`
                );
            }

            const data = await response.json();
            if (!data.key || typeof data.key !== "string") {
                throw new Error(
                    "Invalid VAPID key format received from server"
                );
            }

            console.log("VAPID key validation passed");
            const convertedKey = urlBase64ToUint8Array(data.key);

            // Add exponential backoff retry logic
            let retries = 3;
            let delay = 1000;

            while (retries > 0) {
                try {
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedKey,
                    });

                    if (!subscription) {
                        throw new Error("Subscription creation returned null");
                    }

                    console.log("Push subscription successfully created");
                    return await updateSubscriptionOnServer(subscription);
                } catch (error) {
                    retries--;
                    if (error.name === "NotAllowedError") {
                        throw new Error("Push notification permission denied");
                    }
                    if (retries === 0) {
                        console.error(
                            "Final subscription attempt failed:",
                            error
                        );
                        throw error;
                    }
                    console.log(
                        `Subscribe attempt failed, retrying in ${
                            delay / 1000
                        }s... (${retries} attempts left)`
                    );
                    await new Promise((resolve) => setTimeout(resolve, delay));
                    delay *= 2; // Exponential backoff
                }
            }
        } finally {
            clearTimeout(timeout);
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
