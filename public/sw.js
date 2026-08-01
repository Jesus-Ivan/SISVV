const CACHE_NAME = 'laravel-pwa-v1';
const urlsToCache = [
    '/',
    '/offline' // Opcional: una vista de fallback cuando no hay internet
];

// Instalación del Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Estrategia: Red primero, fallback a caché
// (Ideal para Livewire, ya que requiere conexión para responder a las interacciones)
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});