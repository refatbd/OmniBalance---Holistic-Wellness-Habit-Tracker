const CACHE_NAME = 'nutrition-tracker-v7'; // Bumped version
const urlsToCache = [
    '/',
    '/manifest.json',
    '/assets/css/app.css',
    '/assets/js/app.js'
];

// --- INDEXED DB HELPERS FOR OFFLINE SYNC ---
const DB_NAME = 'offline-sync-db';
const STORE_NAME = 'sync-requests';

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function saveRequestData(url, method, headers, body) {
    const db = await openDatabase();
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(STORE_NAME, 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        store.add({ url, method, headers, body, timestamp: Date.now() });
        transaction.oncomplete = () => resolve();
        transaction.onerror = () => reject(transaction.error);
    });
}

async function getRequests() {
    const db = await openDatabase();
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(STORE_NAME, 'readonly');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function deleteRequest(id) {
    const db = await openDatabase();
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(STORE_NAME, 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        store.delete(id);
        transaction.oncomplete = () => resolve();
        transaction.onerror = () => reject(transaction.error);
    });
}
// -------------------------------------------

self.addEventListener('install', event => {
    self.skipWaiting(); // Force active immediately
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim()); 
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', event => {
    
    // 1. Intercept POST requests
    if (event.request.method === 'POST') {
        
        // A. If user logs out, wipe the private pages from cache!
        if (event.request.url.includes('/logout')) {
            event.respondWith(
                fetch(event.request).then(response => {
                    caches.open(CACHE_NAME).then(cache => {
                        // Delete secure pages so offline mode falls back to welcome page
                        cache.delete('/dashboard', { ignoreSearch: true });
                        cache.delete('/items', { ignoreSearch: true });
                        cache.delete('/analytics', { ignoreSearch: true });
                        cache.delete('/profile', { ignoreSearch: true });
                    });
                    return response;
                }).catch(() => {
                    return Response.redirect('/', 302);
                })
            );
            return;
        }

        // B. Intercept logging for offline sync
        // --- NEW: Added prayer, metrics, and exercise routes ---
        if (event.request.url.includes('/toggle-log') || 
            event.request.url.includes('/toggle-water') || 
            event.request.url.includes('/log-weight') ||
            event.request.url.includes('/toggle-prayer') ||
            event.request.url.includes('/update-metrics') ||
            event.request.url.includes('/log-exercise')
        ) {
            event.respondWith(
                fetch(event.request.clone()).catch(async (error) => {
                    const clonedRequest = event.request.clone();
                    const headers = {};
                    for (const [key, value] of clonedRequest.headers.entries()) {
                        headers[key] = value;
                    }
                    const body = await clonedRequest.text();
                    
                    await saveRequestData(clonedRequest.url, clonedRequest.method, headers, body);
                    
                    if ('sync' in self.registration) {
                        await self.registration.sync.register('sync-logs');
                    }

                    return new Response(JSON.stringify({ 
                        success: true, 
                        offline: true, 
                        message: 'Saved offline. Will sync when connection is restored.' 
                    }), {
                        headers: { 'Content-Type': 'application/json' }
                    });
                })
            );
            return;
        }
        return; 
    }

    // 2. Process GET requests
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Dynamically cache ALL successful GET requests (HTML pages, CSS, JS)
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(async () => {
                // === WE ARE OFFLINE ===
                const url = new URL(event.request.url);

                // If requesting an HTML page
                if (event.request.mode === 'navigate' || (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'))) {
                    
                    // Check if we have the dashboard cached
                    const dashCache = await caches.match('/dashboard', { ignoreSearch: true });
                    
                    // IF we have the dashboard AND they are asking for the root URL
                    // => Override the welcome page and show the dashboard!
                    if (dashCache && (url.pathname === '/' || url.pathname === '/dashboard')) {
                        return dashCache;
                    }

                    // Try to return the exact page they asked for
                    const exactCache = await caches.match(event.request, { ignoreSearch: true });
                    if (exactCache) return exactCache;

                    // If they asked for a page we don't have, but we have the dashboard, show dashboard as fallback
                    if (dashCache) return dashCache;

                    // Ultimate fallback: The welcome page
                    return caches.match('/', { ignoreSearch: true });
                }

                // For Static Assets (CSS, JS, Images)
                return caches.match(event.request, { ignoreSearch: true });
            })
    );
});

// --- Background Sync Event Listener ---
self.addEventListener('sync', event => {
    if (event.tag === 'sync-logs') {
        event.waitUntil(syncOfflineRequests());
    }
});

async function syncOfflineRequests() {
    const requests = await getRequests();
    
    for (const req of requests) {
        try {
            const response = await fetch(req.url, {
                method: req.method,
                headers: req.headers,
                body: req.body
            });

            if (response.ok) {
                await deleteRequest(req.id);
            }
        } catch (error) {
            console.error('Sync failed for request ID:', req.id, error);
        }
    }
}

// Handle incoming background Web Push Notifications
self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon || '/assets/icons/icon-192x192.png',
            actions: msg.actions || [],
            vibrate: [200, 100, 200]
        }));
    }
});