<div>
    <button id="theme-toggle-button" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-dark-700 dark:hover:bg-gray-200 transition-colors">
        <!-- Ícone Lua (Modo Escuro) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <!-- Ícone Sol (Modo Claro) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('theme-toggle-button');
        
        toggleButton.addEventListener('click', () => {
            // Verifica a classe dark no html
            const isDarkMode = document.documentElement.classList.contains('dark');
            
            // Alterna o tema
            const newTheme = isDarkMode ? 'light' : 'dark';
            
            // Aplica o tema usando a função do theme.js
            if (window.aplicarTema) {
                window.aplicarTema(newTheme);
            } else {
                // Fallback se a função não estiver disponível
                if (isDarkMode) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('tema', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('tema', 'dark');
                }
            }
        });
    });
</script> 