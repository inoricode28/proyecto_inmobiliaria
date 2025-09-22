import 'flowbite';

// Dark mode functionality integrated with Filament
function initFilamentDarkMode() {
    // Check Filament's theme preference in localStorage
    const filamentTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Apply dark mode based on Filament's preference or system setting
    if (filamentTheme === 'dark' || (filamentTheme === null && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
        // Sync with Filament's localStorage
        localStorage.setItem('theme', 'dark');
    } else if (filamentTheme === 'light') {
        document.documentElement.classList.remove('dark');
    } else if (filamentTheme === null) {
        // Set system preference as default
        if (systemPrefersDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}

// Initialize dark mode on page load
initFilamentDarkMode();

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    const filamentTheme = localStorage.getItem('theme');
    if (filamentTheme === null || filamentTheme === 'system') {
        if (e.matches) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
});

// Listen for storage changes to sync across tabs/windows
window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
        initFilamentDarkMode();
    }
});

// Escuchar evento reload-page para forzar recarga de página
document.addEventListener('DOMContentLoaded', function () {
    // Initialize dark mode again after DOM is loaded
    initFilamentDarkMode();
    
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