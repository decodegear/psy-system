const CACHE_NAME = "psy-cache-v5"; // Mudar sempre que houver atualização
const urlsToCache = [
    "/index.php",
    "/admin/dashboard.php",
    "/admin/login.php",
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

// Instalação e cache inicial
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log("[Service Worker] Caching new files");
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

// Interceptação de requisições para buscar sempre a versão mais recente do servidor
self.addEventListener("fetch", event => {
    event.respondWith(
        fetch(event.request)
            .then(response => {
                return caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, response.clone());
                    return response;
                });
            })
            .catch(() => caches.match(event.request))
    );
});

// Verificação periódica para atualização
self.addEventListener("periodicsync", event => {
    if (event.tag === "update-sw") {
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
    }
});
