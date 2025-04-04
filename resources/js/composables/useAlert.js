import { inject } from 'vue';

export function useAlert() {
  const alertService = inject('alertService');
  
  if (!alertService) {
    // Fallback caso o provider não esteja configurado
    const noop = () => console.warn('AlertService não está disponível. Verifique se o AlertContainer está montado no app.');
    
    return {
      success: noop,
      error: noop,
      warning: noop,
      info: noop,
      remove: noop
    };
  }
  
  return alertService;
} 