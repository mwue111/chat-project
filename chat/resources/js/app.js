import './bootstrap';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '1234567890',
    wsHost: window.location.hostname,
    wsPort: 6001,
    encrypted: false,
    wssPort: 6001,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    });
