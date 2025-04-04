<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="flex items-center" v-if="autorizacao.authorizable && autorizacao.authorizable.user">
        <div class="flex-shrink-0 h-12 w-12">
          <img v-if="autorizacao.authorizable.user.foto" class="h-12 w-12 rounded-full object-cover shadow-sm border-2 border-gray-200 dark:border-gray-700" :src="`/storage/${autorizacao.authorizable.user.foto}`" :alt="autorizacao.authorizable.user.name">
          <div v-else class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-800 flex items-center justify-center shadow-sm">
            <span class="text-primary-600 dark:text-primary-200 font-bold text-lg">{{ userInitial }}</span>
          </div>
        </div>
        <div class="ml-4">
          <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ autorizacao.authorizable.user.name }}</div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ autorizacao.authorizable.user.email }}</div>
        </div>
      </div>
      <div v-else class="text-sm italic text-gray-500 dark:text-gray-400">Usuário não encontrado</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
      <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm" :class="tipoClass">
        {{ tipoLabel }}
      </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
      <div v-if="autorizacao.dispositivo">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ autorizacao.dispositivo.nome }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          <i class="fas fa-map-marker-alt mr-1"></i> {{ autorizacao.dispositivo.localizacao_string }}
        </div>
      </div>
      <div v-else class="text-sm text-gray-500 dark:text-gray-400">
        <i class="fas fa-microchip mr-1"></i> {{ autorizacao.identificador_dispositivo }}
      </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
      <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm" :class="statusClass">
        <i :class="statusIcon" class="mr-1"></i> {{ statusLabel }}
      </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
      <div class="text-sm text-gray-500 dark:text-gray-400">
        <i class="far fa-calendar-alt mr-1"></i> {{ dataFormatada }}
      </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
      <div class="flex justify-end space-x-3">
        <button v-if="autorizacao.status === 'autorizado'" type="button" 
          @click="$emit('revogar', autorizacao)"
          class="inline-flex items-center text-xs px-2.5 py-1.5 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
          <i class="fas fa-ban mr-1"></i> Revogar
        </button>
        
        <button v-if="['nao_autorizado', 'erro', 'bloqueado', 'processando'].includes(autorizacao.status)" type="button" 
          @click="$emit('autorizar', autorizacao)"
          class="inline-flex items-center text-xs px-2.5 py-1.5 border border-transparent rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
          <i class="fas fa-check-circle mr-1"></i> Autorizar
        </button>
        
        <a :href="`/admin/autorizacoes/${autorizacao.id}/edit`" 
          class="inline-flex items-center text-xs px-2.5 py-1.5 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
          <i class="fas fa-edit mr-1"></i> Editar
        </a>
        
        <button type="button" 
          @click="$emit('excluir', autorizacao.id)"
          class="inline-flex items-center text-xs px-2.5 py-1.5 border border-gray-300 rounded-md shadow-sm text-gray-700 dark:text-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
          <i class="fas fa-trash mr-1"></i> Excluir
        </button>
        
        <a :href="`/admin/${isMorador ? 'moradores' : 'visitantes'}/${autorizacao.authorizable_id}`" 
          class="inline-flex items-center text-xs px-2.5 py-1.5 border border-gray-300 rounded-md shadow-sm text-gray-700 dark:text-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
          <i class="fas fa-eye mr-1"></i> Ver
        </a>
      </div>
    </td>
  </tr>
</template>

<script>
export default {
  props: {
    autorizacao: {
      type: Object,
      required: true
    }
  },
  computed: {
    isMorador() {
      return this.autorizacao.authorizable_type === 'App\\Models\\Morador';
    },
    tipoClass() {
      return this.isMorador
        ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
    },
    tipoLabel() {
      return this.isMorador ? 'Morador' : 'Visitante';
    },
    statusClass() {
      const statusClasses = {
        'autorizado': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
        'nao_autorizado': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
        'processando': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
        'erro': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
        'bloqueado': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
      };
      return statusClasses[this.autorizacao.status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    },
    statusIcon() {
      const statusIcons = {
        'autorizado': 'fas fa-check-circle',
        'nao_autorizado': 'fas fa-times-circle',
        'processando': 'fas fa-spinner fa-spin',
        'erro': 'fas fa-exclamation-triangle',
        'bloqueado': 'fas fa-lock'
      };
      return statusIcons[this.autorizacao.status] || 'fas fa-question-circle';
    },
    statusLabel() {
      const statusLabels = {
        'autorizado': 'Autorizado',
        'nao_autorizado': 'Não Autorizado',
        'processando': 'Processando',
        'erro': 'Erro',
        'bloqueado': 'Bloqueado'
      };
      return statusLabels[this.autorizacao.status] || this.autorizacao.status.charAt(0).toUpperCase() + this.autorizacao.status.slice(1);
    },
    userInitial() {
      if (this.autorizacao.authorizable && this.autorizacao.authorizable.user && this.autorizacao.authorizable.user.name) {
        return this.autorizacao.authorizable.user.name.charAt(0);
      }
      return '?';
    },
    dataFormatada() {
      if (!this.autorizacao.created_at) return '';
      
      const data = new Date(this.autorizacao.created_at);
      return data.toLocaleDateString('pt-BR') + ' ' + data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
    }
  }
};
</script>

<style scoped>
/* Adiciona animação de hover suave */
tr {
  transition: all 0.2s ease-in-out;
}
</style> 