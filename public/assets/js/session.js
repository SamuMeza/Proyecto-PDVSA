(function () {
    const KEEPALIVE_URL = BASE_PATH + '/session/keepalive';
    const THROTTLE_MS = 60000;
    let lastPing = 0;

    function now() {
        return Date.now();
    }

    function canPing() {
        return now() - lastPing > THROTTLE_MS;
    }

    function sendKeepalive() {
        if (!canPing()) {
            return;
        }
        lastPing = now();

        fetch(KEEPALIVE_URL, { credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Sesión no válida');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data.ok) {
                    console.warn('No se renovó sesión:', data.error);
                    return;
                }
            })
            .catch(function () {
                window.location.href = BASE_PATH + '/login';
            });
    }

    function onActivity() {
        sendKeepalive();
    }

    ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(function (eventName) {
        window.addEventListener(eventName, onActivity, { passive: true });
    });

    sendKeepalive();
})();
