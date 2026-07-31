if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Registration failures (e.g. unsupported browser) are not fatal.
        });
    });
}

function isIos() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
}

function isInStandaloneMode() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
}

function showIosInstallBanner() {
    const dismissedAt = localStorage.getItem('pwa-ios-banner-dismissed');
    if (dismissedAt && Date.now() - Number(dismissedAt) < 1000 * 60 * 60 * 24 * 30) {
        return;
    }

    const banner = document.createElement('div');
    banner.className = 'ios-install-banner';
    banner.innerHTML = `
        <span>Installez cette application : appuyez sur <strong>Partager</strong>
        puis <strong>Sur l'écran d'accueil</strong>.</span>
        <button type="button" aria-label="Fermer">&times;</button>
    `;
    banner.querySelector('button').addEventListener('click', () => {
        localStorage.setItem('pwa-ios-banner-dismissed', String(Date.now()));
        banner.remove();
    });
    document.body.appendChild(banner);
}

document.addEventListener('DOMContentLoaded', () => {
    if (isIos() && !isInStandaloneMode()) {
        showIosInstallBanner();
    }
});
