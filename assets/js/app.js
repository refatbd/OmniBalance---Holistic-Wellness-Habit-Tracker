import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// You can move your PWA Service Worker registration here if you prefer
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then(registration => {
            console.log('ServiceWorker registered with scope:', registration.scope);
        }).catch(err => {
            console.log('ServiceWorker registration failed:', err);
        });
    });
}