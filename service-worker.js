const CACHE_NAME = "psy-cache-v1";
const urlsToCache = [
    "/index.php",
    "/admin/dashboard.php",
    "/includes/header.php",
    "/includes/footer.php",
    "/pages/agendar_pacientes.php",
    "/pages/cadastro_transacao.php",
    "/pages/cadastro_pessoa.php",
    "/views/view_pessoa.php",
    "/views/view_relatorio.php",
    "/views/visualizar_agendamentos.php",
    "/views/visualizar_transacao.php"
];

// Instalação do Service Worker e cache dos arquivos necessários
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log("[Service Worker] Caching files");
                return cache.addAll(urlsToCache);
            })
            .then(() => self.skipWaiting())
    );
});

// Ativação e remoção de caches antigos
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        console.log("[Service Worker] Deleting old cache:", cache);
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Interceptação de requisições para fornecer arquivos do cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match("/index.php");
            })
    );
});
