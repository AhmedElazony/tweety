function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, "+")
        .replace(/_/g, "/");
    const rawData = window.atob(base64);
    return new Uint8Array(rawData.split("").map((c) => c.charCodeAt(0)));
}

async function updateSubscriptionOnServer(subscription) {
    const response = await fetch("/push/subscribe", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                ?.content,
        },
        body: JSON.stringify({ subscription }),
    });

    if (!response.ok) throw new Error(`Server error: ${response.status}`);
    return response.json();
}

async function subscribeToPush() {
    const registration = await navigator.serviceWorker.ready;
    let subscription = await registration.pushManager.getSubscription();

    if (subscription) {
        try {
            await subscription.getKey("p256dh");
            return updateSubscriptionOnServer(subscription);
        } catch {
            await subscription.unsubscribe();
        }
    }

    const response = await fetch("/push/key");
    if (!response.ok)
        throw new Error(`VAPID key fetch failed: ${response.status}`);

    const { key } = await response.json();
    if (!key) throw new Error("Invalid VAPID key");

    subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(key),
    });

    return updateSubscriptionOnServer(subscription);
}

function requestNotificationPermission() {
    return new Promise((resolve) => {
        if (!("Notification" in window)) return resolve(false);

        Notification.requestPermission()
            .then((permission) => {
                if (permission === "granted") {
                    subscribeToPush()
                        .then(() => resolve(true))
                        .catch(() => resolve(false));
                } else {
                    resolve(false);
                }
            })
            .catch(() => resolve(false));
    });
}

if ("serviceWorker" in navigator && "PushManager" in window) {
    window.addEventListener("load", () => {
        navigator.serviceWorker
            .register("/serviceworker.js")
            .then(async (registration) => {
                if (registration.active) {
                    return requestNotificationPermission();
                }
                await new Promise((resolve) => {
                    registration.addEventListener("activate", resolve);
                    setTimeout(resolve, 5000);
                });
                return requestNotificationPermission();
            })
            .catch(console.error);
    });
}
