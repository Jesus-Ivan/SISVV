const CACHE_NAME = 'laravel-pwa-v1';

// Solo guardamos el inicio de la app
const urlsToCache = [
    '/'
];

// Instalación
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
            .then(() => self.skipWaiting()) // Fuerza al nuevo SW a activarse de inmediato
    );
});

// Activación
self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

// Estrategia Fetch para Livewire
self.addEventListener('fetch', event => {
    // Ignoramos peticiones que no sean GET o que sean de Livewire/AJAX directo
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .catch(() => {
                // Si falla la red, buscamos en el caché
                return caches.match(event.request).then(response => {
                    if (response) {
                        return response;
                    }
                    // Si tampoco está en caché, devolvemos una respuesta vacía válida para no romper el navegador
                    return new Response('Sin conexión', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: new Headers({ 'Content-Type': 'text/plain' })
                    });
                });
            })
    );
});