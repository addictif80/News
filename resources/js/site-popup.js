document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('site-popup');

    if (!popup) {
        return;
    }

    const popupId = popup.dataset.popupId;
    const storageKey = `popup-last-shown-${popupId}`;
    const frequencyDays = Number(popup.dataset.frequencyDays || 1);
    const lastShown = localStorage.getItem(storageKey);

    if (lastShown) {
        const elapsedDays = (Date.now() - Number(lastShown)) / (1000 * 60 * 60 * 24);
        if (elapsedDays < frequencyDays) {
            return;
        }
    }

    const show = () => {
        popup.hidden = false;
        localStorage.setItem(storageKey, String(Date.now()));
    };

    const triggerType = popup.dataset.triggerType;
    const triggerValue = Number(popup.dataset.triggerValue || 0);

    if (triggerType === 'load') {
        show();
    } else if (triggerType === 'delay') {
        setTimeout(show, triggerValue * 1000);
    } else if (triggerType === 'scroll') {
        const onScroll = () => {
            const scrolled = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
            if (scrolled >= triggerValue) {
                show();
                window.removeEventListener('scroll', onScroll);
            }
        };
        window.addEventListener('scroll', onScroll);
    } else if (triggerType === 'exit_intent') {
        document.addEventListener('mouseleave', function onLeave(e) {
            if (e.clientY <= 0) {
                show();
                document.removeEventListener('mouseleave', onLeave);
            }
        });
    }

    popup.querySelector('[data-popup-close]')?.addEventListener('click', () => {
        popup.hidden = true;
    });
});
