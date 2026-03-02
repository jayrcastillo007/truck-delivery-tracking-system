import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true,          // or '0.0.0.0'
        port: 5173,
        strictPort: true,
        hmr: {
            host: '192.168.18.11', // <-- CHANGE THIS to your PC IPv4
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
