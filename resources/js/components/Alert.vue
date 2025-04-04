<template>
  <transition 
    enter-active-class="transform transition duration-300 ease-out"
    enter-from-class="translate-y-10 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transform transition duration-200 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-10 opacity-0"
  >
    <div v-if="show" 
      :class="[
        'shadow-lg rounded-lg border-l-4 overflow-visible break-words w-full',
        type === 'success' ? 'bg-green-50 dark:bg-green-900/40 border-green-500 text-green-800 dark:text-green-100' : '',
        type === 'error' ? 'bg-red-50 dark:bg-red-900/40 border-red-500 text-red-800 dark:text-red-100' : '',
        type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/40 border-yellow-500 text-yellow-800 dark:text-yellow-100' : '',
        type === 'info' ? 'bg-blue-50 dark:bg-blue-900/40 border-blue-500 text-blue-800 dark:text-blue-100' : ''
      ]"
    >
      <div class="flex items-start p-4">
        <div class="flex-shrink-0 pt-0.5">
          <i v-if="type === 'success'" class="fas fa-check-circle text-green-600 dark:text-green-300 text-xl"></i>
          <i v-else-if="type === 'error'" class="fas fa-exclamation-circle text-red-600 dark:text-red-300 text-xl"></i>
          <i v-else-if="type === 'warning'" class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-300 text-xl"></i>
          <i v-else-if="type === 'info'" class="fas fa-info-circle text-blue-600 dark:text-blue-300 text-xl"></i>
        </div>
        <div class="ml-3 flex-1 break-words">
          <p v-if="title" class="text-sm font-medium" :class="titleColor">
            {{ title }}
          </p>
          <p class="mt-1 text-sm break-words whitespace-normal">
            {{ message }}
          </p>
          <div v-if="action" class="mt-2">
            <button 
              type="button" 
              class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md"
              :class="actionButtonClass"
              @click="$emit('action')"
            >
              {{ actionText }}
            </button>
          </div>
        </div>
        <div class="ml-2 flex-shrink-0 flex">
          <button
            @click="closeAlert"
            class="inline-flex text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-100 transition-colors duration-200"
          >
            <span class="sr-only">Fechar</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div 
        v-if="autoClose && duration" 
        class="h-1" 
        :class="progressBarClass" 
        :style="{ width: progressWidth + '%' }"
      ></div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'Alert',
  props: {
    type: {
      type: String,
      default: 'info',
      validator: value => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
      type: String,
      default: ''
    },
    message: {
      type: String,
      required: true
    },
    autoClose: {
      type: Boolean,
      default: true
    },
    duration: {
      type: Number,
      default: 5000
    },
    action: {
      type: Boolean,
      default: false
    },
    actionText: {
      type: String,
      default: 'Ver detalhes'
    }
  },
  data() {
    return {
      show: true,
      progress: 100,
      progressInterval: null
    }
  },
  computed: {
    titleColor() {
      const colors = {
        success: 'text-green-800 dark:text-green-100',
        error: 'text-red-800 dark:text-red-100',
        warning: 'text-yellow-800 dark:text-yellow-100',
        info: 'text-blue-800 dark:text-blue-100'
      }
      return colors[this.type]
    },
    progressBarClass() {
      const colors = {
        success: 'bg-green-500 dark:bg-green-400',
        error: 'bg-red-500 dark:bg-red-400',
        warning: 'bg-yellow-500 dark:bg-yellow-400',
        info: 'bg-blue-500 dark:bg-blue-400'
      }
      return colors[this.type]
    },
    actionButtonClass() {
      const colors = {
        success: 'text-green-800 bg-green-100 hover:bg-green-200 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700',
        error: 'text-red-800 bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:text-red-100 dark:hover:bg-red-700',
        warning: 'text-yellow-800 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-100 dark:hover:bg-yellow-700',
        info: 'text-blue-800 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-100 dark:hover:bg-blue-700'
      }
      return colors[this.type]
    },
    progressWidth() {
      return this.progress
    }
  },
  mounted() {
    if (this.autoClose && this.duration) {
      const step = 10
      const totalSteps = this.duration / step
      const progressStep = 100 / totalSteps
      
      this.progressInterval = setInterval(() => {
        this.progress -= progressStep
        if (this.progress <= 0) {
          this.closeAlert()
        }
      }, step)
    }
  },
  beforeUnmount() {
    if (this.progressInterval) {
      clearInterval(this.progressInterval)
    }
  },
  methods: {
    closeAlert() {
      if (this.progressInterval) {
        clearInterval(this.progressInterval)
      }
      this.show = false
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.break-words {
  word-wrap: break-word;
  word-break: break-word;
}
</style> 