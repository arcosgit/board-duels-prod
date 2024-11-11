import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: '0.0.0.0', // Разрешаем внешние подключения к Vite
        port: 5173,       // Устанавливаем стандартный порт Vite
        strictPort: true, // Убедимся, что используется именно этот порт
        hmr: {
          host: 'localhost', // Для HMR используем локальный хост
          port: 5173,
        },
      },
    
});
