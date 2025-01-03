const CACHE_NAME = "psy-cache-v2";  // Alterado para forçar atualização
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
    if (event.request.method !== "GET") return; // Evita cache de requisições POST, PUT, DELETE

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Atualiza o cache com a versão mais recente do arquivo
                return caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, response.clone());
                    return response;
                });
            })
            .catch(() => caches.match(event.request)) // Se offline, tenta pegar do cache
    );
});
