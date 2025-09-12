import 'flowbite';

// Escuchar evento reload-page para forzar recarga de página
document.addEventListener('DOMContentLoaded', function () {
    // Esperar a que Livewire esté disponible
    function setupReloadListener() {
        if (typeof Livewire !== 'undefined') {
            Livewire.on('reload-page', function () {
                console.log('Evento reload-page recibido, recargando página...');
                window.location.reload();
            });
        } else {
            // Si Livewire no está disponible, intentar de nuevo en 100ms
            setTimeout(setupReloadListener, 100);
        }
    }
    
    setupReloadListener();
    
    // También escuchar el evento livewire:load como respaldo
    document.addEventListener('livewire:load', function () {
        Livewire.on('reload-page', function () {
            console.log('Evento reload-page recibido (livewire:load), recargando página...');
            window.location.reload();
        });
    });
});