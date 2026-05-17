import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configurar CSRF token para axios
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

// Inicializar Laravel Echo con Reverb
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        },
    },
});

// Notificar a Alpine.js que Echo está listo
window.dispatchEvent(new Event('echo-ready'));

// Utilidad global para tiempo real
window.RealtimeUtils = {
    updateVote: function(postId, karma) {
        const el = document.getElementById('vote-bar-' + postId);
        if (el && window.Alpine && window.Alpine.$data) window.Alpine.$data(el).karma = karma;
    },
    updateCommentCount: function(postId, count) {
        const el = document.getElementById('comment-count-' + postId);
        if (el) el.textContent = count;
    },
    showNewContentBanner: function(message) {
        let banner = document.getElementById('new-content-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'new-content-banner';
            banner.style.cssText = 'position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#2563eb;color:#fff;padding:10px 20px;border-radius:8px;cursor:pointer;z-index:9999;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,0.3);';
            banner.onclick = function() { window.location.reload(); };
            document.body.appendChild(banner);
        }
        banner.textContent = message + ' — Click para actualizar';
        banner.style.display = 'block';
    }
};
