import './bootstrap';
import './theme'; // Importa o gerenciador de temas
import './menu'; // Importa as funcionalidades do menu
import { createApp } from 'vue';

import AutorizacoesIndex from './components/autorizacoes/AutorizacoesIndex.vue';
import AutorizacaoItem from './components/autorizacoes/AutorizacaoItem.vue';
import Paginacao from './components/Paginacao.vue';
import Alert from './components/Alert.vue';
import AlertContainer from './components/AlertContainer.vue';

// Aplicação principal
const app = createApp({});
app.component('autorizacoes-index', AutorizacoesIndex);
app.component('autorizacao-item', AutorizacaoItem);
app.component('paginacao', Paginacao);
app.component('alert', Alert);
app.component('alert-container', AlertContainer);
app.mount('#app');

// Container de alertas/notificações (para garantir que esteja disponível globalmente)
const notificationApp = createApp({});
notificationApp.component('alert', Alert);
notificationApp.component('alert-container', AlertContainer);

// Montar o container de notificações se existir
if (document.getElementById('notification-container')) {
    notificationApp.mount('#notification-container');
}