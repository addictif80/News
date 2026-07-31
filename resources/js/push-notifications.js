function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

function updateButtonState(button, subscribed) {
    button.textContent = subscribed
        ? 'Désactiver les notifications push'
        : 'Activer les notifications push';
    button.dataset.subscribed = subscribed ? '1' : '0';
}

async function postJson(url, csrfToken, body) {
    await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(body),
    });
}

async function initPushToggle(button) {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        button.disabled = true;
        button.textContent = 'Notifications push non disponibles sur ce navigateur';
        return;
    }

    const registration = await navigator.serviceWorker.ready;
    let subscription = await registration.pushManager.getSubscription();
    updateButtonState(button, !!subscription);

    button.addEventListener('click', async () => {
        button.disabled = true;

        try {
            if (subscription) {
                await postJson(button.dataset.unsubscribeUrl, button.dataset.csrf, {
                    endpoint: subscription.endpoint,
                });
                await subscription.unsubscribe();
                subscription = null;
                updateButtonState(button, false);
                return;
            }

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                return;
            }

            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(button.dataset.vapidKey),
            });

            await postJson(button.dataset.subscribeUrl, button.dataset.csrf, subscription.toJSON());
            updateButtonState(button, true);
        } finally {
            button.disabled = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-push-toggle]').forEach(initPushToggle);
});
