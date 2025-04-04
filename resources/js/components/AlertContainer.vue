<template>
  <div class="alert-container fixed bottom-4 right-4 z-50 space-y-2 flex flex-col items-end max-h-[80vh] overflow-y-auto">
    <Alert
      v-for="alert in alerts"
      :key="alert.id"
      :type="alert.type"
      :title="alert.title"
      :message="alert.message"
      :auto-close="alert.autoClose"
      :duration="alert.duration"
      :action="alert.action"
      :action-text="alert.actionText"
      @close="removeAlert(alert.id)"
      @action="handleAction(alert.id)"
      class="mb-2 w-full"
    />
  </div>
</template>

<script>
import { ref, provide } from 'vue';
import Alert from './Alert.vue';

export default {
  name: 'AlertContainer',
  components: {
    Alert
  },
  setup() {
    const nextId = ref(1);
    const alerts = ref([]);

    const addAlert = (alert) => {
      const id = nextId.value++;
      
      // Limitar o número máximo de alertas visíveis ao mesmo tempo (opcional)
      if (alerts.value.length >= 5) {
        const oldestAlert = alerts.value[0];
        removeAlert(oldestAlert.id);
      }
      
      alerts.value.push({
        id,
        type: alert.type || 'info',
        title: alert.title || '',
        message: alert.message,
        autoClose: alert.autoClose !== undefined ? alert.autoClose : true,
        duration: alert.duration || 5000,
        action: alert.action || false,
        actionText: alert.actionText || 'Ver detalhes',
        actionCallback: alert.actionCallback || (() => {})
      });
      
      return id;
    };

    const removeAlert = (id) => {
      const index = alerts.value.findIndex(alert => alert.id === id);
      if (index !== -1) {
        alerts.value.splice(index, 1);
      }
    };

    const handleAction = (id) => {
      const alert = alerts.value.find(a => a.id === id);
      if (alert && alert.actionCallback) {
        alert.actionCallback();
      }
    };

    const showSuccess = (message, options = {}) => {
      return addAlert({
        type: 'success',
        message,
        ...options
      });
    };

    const showError = (message, options = {}) => {
      return addAlert({
        type: 'error',
        message,
        ...options
      });
    };

    const showWarning = (message, options = {}) => {
      return addAlert({
        type: 'warning',
        message,
        ...options
      });
    };

    const showInfo = (message, options = {}) => {
      return addAlert({
        type: 'info',
        message,
        ...options
      });
    };

    // Exportar funções para uso global
    const alertService = {
      success: showSuccess,
      error: showError,
      warning: showWarning,
      info: showInfo,
      remove: removeAlert
    };

    // Disponibilizar através de provide/inject API
    provide('alertService', alertService);

    return {
      alerts,
      removeAlert,
      handleAction,
      alertService
    };
  }
};
</script>

<style scoped>
.alert-container {
  width: 100%;
  max-width: 320px;
}

@media (max-width: 640px) {
  .alert-container {
    max-width: 95%;
  }
}
</style> 