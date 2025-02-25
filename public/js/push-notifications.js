function urlBase64ToUint8Array(base64String) {
    console.log(
        "Converting base64 to Uint8Array:",
        base64String.substring(0, 10) + "..."
    );
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, "+")
        .replace(/_/g, "/");

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    console.log("Conversion complete, array length:", outputArray.length);
    return outputArray;
}

async function subscribeToPush() {
    console.log("=== DETAILED DEBUG: subscribeToPush started ===");
    console.log("Browser info:", navigator.userAgent);
    console.log("Protocol:", window.location.protocol);

    try {
        // Check service worker registration state
        if (!("serviceWorker" in navigator)) {
            throw new Error("Service Worker not supported in this browser");
        }

        const registrations = await navigator.serviceWorker.getRegistrations();
        console.log(
            "Existing service worker registrations:",
            registrations.length
        );

        // Wait for service worker to be ready
        console.log("Waiting for service worker to be ready...");
        const registration = await navigator.serviceWorker.ready;
        console.log(
            "Service Worker ready state:",
            registration.active ? "active" : "not active"
        );

        // Check for existing subscription
        console.log("Checking for existing push subscription...");
        let subscription = await registration.pushManager.getSubscription();
        if (subscription) {
            console.log(
                "Found existing subscription:",
                subscription.endpoint.substring(0, 30) + "..."
            );
            try {
                // Test existing subscription
                const keyTest = await subscription.getKey("p256dh");
                if (keyTest) {
                    console.log("Existing subscription is valid, using it");
                    return await updateSubscriptionOnServer(subscription);
                }
            } catch (e) {
                console.log("Existing subscription is invalid:", e.message);
                console.log("Unsubscribing from invalid subscription...");
                await subscription.unsubscribe();
                console.log("Successfully unsubscribed");
            }
        } else {
            console.log("No existing subscription found");
        }

        // Fetch VAPID key
        console.log("Fetching VAPID key from server...");
        let response;
        try {
            response = await fetch("/push/key", {
                headers: {
                    Accept: "application/json",
                    "Cache-Control": "no-cache",
                },
            });
            console.log("VAPID key fetch response status:", response.status);
        } catch (error) {
            console.error("Network error fetching VAPID key:", error);
            throw new Error("Network error: Unable to fetch VAPID key");
        }

        if (!response.ok) {
            throw new Error(
                `Server error fetching VAPID key: ${response.status}`
            );
        }

        let data;
        try {
            data = await response.json();
            console.log("VAPID key response parsed successfully");
        } catch (error) {
            console.error("Error parsing VAPID key response:", error);
            throw new Error("Invalid JSON in VAPID key response");
        }

        if (!data.key || typeof data.key !== "string") {
            console.error("Invalid key format:", data);
            throw new Error("Invalid VAPID key format");
        }

        console.log("VAPID key received:", data.key.substring(0, 10) + "...");
        console.log("Converting VAPID key...");
        const convertedKey = urlBase64ToUint8Array(data.key);

        // Create subscription
        console.log("Attempting to create push subscription...");
        console.log(
            "Push Manager state:",
            registration.pushManager ? "available" : "not available"
        );

        try {
            console.log("Calling pushManager.subscribe...");
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedKey,
            });

            if (!subscription) {
                throw new Error("Subscription creation returned null");
            }

            console.log("Subscription created successfully");
            console.log(
                "Subscription endpoint:",
                subscription.endpoint.substring(0, 30) + "..."
            );

            return await updateSubscriptionOnServer(subscription);
        } catch (error) {
            console.error(
                "Subscription creation error:",
                error.name,
                error.message
            );

            if (error.name === "NotAllowedError") {
                throw new Error("Push notification permission denied");
            }

            // Additional diagnostics for AbortError
            if (error.name === "AbortError") {
                console.error("AbortError detected - Additional diagnostics:");
                console.error(
                    "- Is the site served over HTTPS?",
                    window.location.protocol === "https:"
                );
                console.error(
                    "- Is the service worker active?",
                    registration.active ? "Yes" : "No"
                );
                console.error("- VAPID key length:", data.key.length);

                // Check browser compatibility
                const isChrome = navigator.userAgent.indexOf("Chrome") > -1;
                const isFirefox = navigator.userAgent.indexOf("Firefox") > -1;
                console.error(
                    "- Browser:",
                    isChrome ? "Chrome" : isFirefox ? "Firefox" : "Other"
                );
            }

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
    console.log("Updating subscription on server...");

    // Get CSRF token
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    console.log("CSRF token available:", !!csrfToken);

    try {
        const response = await fetch("/push/subscribe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ subscription }),
        });

        console.log("Server response status:", response.status);

        if (!response.ok) {
            throw new Error(`Server subscription failed: ${response.status}`);
        }

        const data = await response.json();
        console.log("Push subscription saved successfully:", data);
        return data;
    } catch (error) {
        console.error("Error updating subscription on server:", error);
        throw error;
    }
}

function requestNotificationPermission() {
    console.log("Requesting notification permission...");
    return new Promise((resolve, reject) => {
        if (!("Notification" in window)) {
            console.log("Notifications not supported in this browser");
            resolve(false);
            return;
        }

        console.log(
            "Current notification permission:",
            Notification.permission
        );

        Notification.requestPermission()
            .then((permission) => {
                console.log("Permission request result:", permission);
                if (permission === "granted") {
                    console.log("Notification permission granted");
                    subscribeToPush()
                        .then(() => {
                            console.log("Push subscription successful");
                            resolve(true);
                        })
                        .catch((error) => {
                            console.error("Push subscription failed:", error);
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
    console.log("Service Worker and Push API supported");
    console.log("Current protocol:", window.location.protocol);

    // Ensure page is fully loaded
    window.addEventListener("load", () => {
        console.log("Page loaded, registering service worker...");
        navigator.serviceWorker
            .register("/serviceworker.js")
            .then(async (registration) => {
                console.log(
                    "ServiceWorker registered successfully, state:",
                    registration.active ? "active" : "inactive"
                );

                // Test basic notification
                if (Notification.permission === "granted") {
                    console.log("Testing basic notification...");
                    try {
                        new Notification("Test Notification", {
                            body: "This is a basic notification test",
                        });
                        console.log(
                            "Basic notification displayed successfully"
                        );
                    } catch (e) {
                        console.error("Basic notification failed:", e);
                    }
                }

                // Wait for service worker to be activated
                if (registration.active) {
                    console.log("Service worker already active");
                    return requestNotificationPermission();
                }

                console.log("Waiting for service worker to be activated...");
                await new Promise((resolve) => {
                    registration.addEventListener("activate", () => {
                        console.log("Service worker activated");
                        resolve();
                    });

                    // Fallback if the event doesn't fire
                    setTimeout(() => {
                        console.log(
                            "Service worker activation timeout reached"
                        );
                        resolve();
                    }, 5000);
                });

                return requestNotificationPermission();
            })
            .catch((error) => {
                console.error("ServiceWorker registration failed:", error);
            });
    });
} else {
    console.error("Service Worker or Push API not supported in this browser");
}
