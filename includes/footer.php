<footer>
    <p>&copy; 2024 Sistema de Gestão. Todos os direitos reservados. | V0.92</p>
</footer>
<script src="../assets/js/scripts.js"></script>
<script>
    // Registra o Service Worker após o DOM estar completamente carregado
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('Service Worker registrado com sucesso:', registration.scope);

                // Detecta novas versões
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // Nova versão disponível
                            const updateButton = document.createElement('button');
                            updateButton.textContent = 'Nova versão disponível! Clique para atualizar.';
                            updateButton.style.cssText = 'position:fixed;bottom:10px;right:10px;background-color:#007bff;color:#fff;padding:10px 15px;border:none;cursor:pointer;border-radius:5px;z-index:9999;';
                            updateButton.onclick = () => location.reload();
                            document.body.appendChild(updateButton);
                        }
                    });
                });
            })
            .catch(error => console.error('Falha ao registrar Service Worker:', error));
    }
</script>
</body>

</html>