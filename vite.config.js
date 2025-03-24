import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Only if you're using Vue
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    tailwindcss(),
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: {
        paths: [
          'resources/views/**/*.blade.php'
        ],
      },
    }),
    
    vue(), // Only if you're using Vue
  ],
  server: {
    watch: {
      usePolling: false,  // Vamos desativar o polling
      ignored: ['!**/node_modules/**', '**/vendor/**', '**/app/**', '**/routes/**', '**/public/**', '**/storage/**'
        
      ]
    },
    hmr: {
      overlay: false
    }
  },
});