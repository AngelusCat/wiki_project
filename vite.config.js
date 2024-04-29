import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/wiki.css', 'resources/js/main.js', 'resources/js/import.js', 'resources/js/search.js'],
            refresh: true,
        }),
    ],
});
