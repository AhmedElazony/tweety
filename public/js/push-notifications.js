// minimal-push.js
async function setupPushNotifications() {
    // 1. Register service worker
    const registration = await navigator.serviceWorker.register(
        "/minimal-sw.js"
    );
    console.log("Service Worker registered");

    // 2. Request permission
    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
        throw new Error("Permission not granted");
    }

    // 3. Get the VAPID key
    const response = await fetch("/push/key");
    const data = await response.json();

    // 4. Convert the key
    const applicationServerKey = urlBase64ToUint8Array(data.key);

    // 5. Subscribe
    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey,
    });

    // 6. Send to server
    await fetch("/push/subscribe", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ subscription }),
    });

    console.log("Subscription complete");
    return true;
}

// Helper function
function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, "+")
        .replace(/_/g, "/");

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Call when needed
document
    .getElementById("enable-notifications")
    .addEventListener("click", async () => {
        try {
            await setupPushNotifications();
            alert("Notifications enabled!");
        } catch (error) {
            console.error("Error:", error);
            alert("Failed to enable notifications: " + error.message);
        }
    });
