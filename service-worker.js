self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('financeiro-cache-v1').then((cache) => {
            return cache.addAll([
                '/index.php',
                '/includes/header.php',
                '/pages/cadastro_pessoa.php',
                '/assets/icons/icon-192x192.png',
                '/assets/icons/icon-512x512.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
