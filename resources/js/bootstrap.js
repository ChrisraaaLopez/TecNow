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

// Utilidad global para tiempo real — DEBE estar antes de echo-ready
window.RealtimeUtils = {
    updateVote: function(postId, karma) {
        const el = document.getElementById('vote-bar-' + postId);
        if (el && window.Alpine && window.Alpine.$data) window.Alpine.$data(el).karma = karma;
    },
    updateCommentCount: function(postId, count) {
        const el = document.getElementById('comment-count-' + postId);
        if (el) el.textContent = count;
    },
    prependPost: function(html) {
        const feed = document.getElementById('posts-feed');
        if (!feed) return;
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const card = wrap.firstElementChild;
        if (!card) return;
        feed.insertBefore(card, feed.firstChild);
        // Alpine v3 detecta nuevos nodos x-data automáticamente via MutationObserver
    },
    prependComment: function(html) {
        const list = document.getElementById('comments-list');
        if (!list) return;
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const card = wrap.firstElementChild;
        if (!card) return;
        // Insertar comentario al inicio + separador debajo
        const sep = document.createElement('div');
        sep.className = 'border-t border-border';
        list.insertBefore(sep, list.firstChild);
        list.insertBefore(card, list.firstChild);
    }
};

// Notificar que Echo está listo (DESPUÉS de definir RealtimeUtils)
window.dispatchEvent(new Event('echo-ready'));
