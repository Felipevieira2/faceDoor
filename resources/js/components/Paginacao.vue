<template>
  <div class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <button @click="anterior" :disabled="!podeAnterior" :class="[podeAnterior ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-300 cursor-not-allowed', 'relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500']">
        Anterior
      </button>
      <button @click="proximo" :disabled="!podeProximo" :class="[podeProximo ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-300 cursor-not-allowed', 'ml-3 relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500']">
        Próximo
      </button>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Mostrando 
          <span class="font-medium">{{ início }}</span>
          a
          <span class="font-medium">{{ fim }}</span>
          de
          <span class="font-medium">{{ paginacao.total }}</span>
          resultados
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Paginação">
          <button @click="anterior" :disabled="!podeAnterior" :class="[podeAnterior ? 'text-gray-500 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' : 'text-gray-300 cursor-not-allowed', 'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500']">
            <span class="sr-only">Anterior</span>
            <i class="fas fa-chevron-left"></i>
          </button>
          
          <template v-for="page in pages" :key="page">
            <button v-if="page === '...'" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">
              ...
            </button>
            <button v-else @click="irParaPagina(page)" :class="[page === paginacao.current_page ? 'bg-primary-50 dark:bg-primary-900 border-primary-500 text-primary-600 dark:text-primary-200' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700', 'relative inline-flex items-center px-4 py-2 border text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500']">
              {{ page }}
            </button>
          </template>
          
          <button @click="proximo" :disabled="!podeProximo" :class="[podeProximo ? 'text-gray-500 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' : 'text-gray-300 cursor-not-allowed', 'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500']">
            <span class="sr-only">Próximo</span>
            <i class="fas fa-chevron-right"></i>
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    paginacao: {
      type: Object,
      required: true
    }
  },
  computed: {
    podeAnterior() {
      return this.paginacao.current_page > 1;
    },
    podeProximo() {
      return this.paginacao.current_page < this.paginacao.last_page;
    },
    início() {
      return (this.paginacao.current_page - 1) * this.paginacao.per_page + 1;
    },
    fim() {
      return Math.min(this.paginacao.current_page * this.paginacao.per_page, this.paginacao.total);
    },
    pages() {
      const lastPage = this.paginacao.last_page;
      const currentPage = this.paginacao.current_page;
      
      if (lastPage <= 7) {
        return Array.from({ length: lastPage }, (_, i) => i + 1);
      }
      
      // Sempre mostrar primeira página e última página
      // Mostrar 5 páginas ao redor da página atual
      let pages = [];
      
      pages.push(1);
      
      if (currentPage > 3) {
        pages.push('...');
      }
      
      // Páginas ao redor da atual
      const start = Math.max(2, currentPage - 1);
      const end = Math.min(lastPage - 1, currentPage + 1);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      if (currentPage < lastPage - 2) {
        pages.push('...');
      }
      
      if (lastPage > 1) {
        pages.push(lastPage);
      }
      
      return pages;
    }
  },
  methods: {
    anterior() {
      if (this.podeAnterior) {
        this.$emit('paginar', this.paginacao.current_page - 1);
      }
    },
    proximo() {
      if (this.podeProximo) {
        this.$emit('paginar', this.paginacao.current_page + 1);
      }
    },
    irParaPagina(page) {
      this.$emit('paginar', page);
    }
  }
};
</script> 