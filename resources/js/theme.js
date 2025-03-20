// Temas disponíveis na aplicação (invertidos)
const temas = {
    light: {
        name: 'Escuro',
        icon: 'moon',
        class: '', // agora o modo escuro é o padrão
    },
    dark: {
        name: 'Claro',
        icon: 'sun',
        class: 'dark', // agora .dark representa o tema claro
    }
};

// Configura o tema baseado na preferência do usuário ou sistema
function configurarTema() {
    // Verificar se existe um tema salvo no localStorage
    const temaSalvo = localStorage.getItem('tema');
    const prefereEscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Se não tiver tema salvo, usa a preferência do sistema (dark mode)
    const temaAtual = temaSalvo || (prefereEscuro ? 'dark' : 'light');
    
    // Aplica o tema
    aplicarTema(temaAtual);
    
    return temaAtual;
}

// Aplica o tema escolhido
function aplicarTema(tema) {
    // Remove todas as classes de tema anteriores
    document.documentElement.classList.remove('dark');
    
    // Adiciona a classe do tema escolhido, se não for o tema light (que é o padrão)
    if (temas[tema] && temas[tema].class) {
        document.documentElement.classList.add(temas[tema].class);
    }
    
    // Salva a preferência no localStorage
    localStorage.setItem('tema', tema);
    
    return tema;
}

// Inicializa o gerenciador de temas
document.addEventListener('DOMContentLoaded', () => {
    // Configura o tema inicial
    configurarTema();
});

// Tornando aplicarTema disponível globalmente
window.aplicarTema = aplicarTema;

// Exporta as funções para uso em outros arquivos, se necessário
export { configurarTema, aplicarTema, temas }; 