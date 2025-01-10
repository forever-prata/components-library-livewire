import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'node_modules/@govbr-ds/core/dist/core.min.css',
                'node_modules/@govbr-ds/core/dist/core.min.js',
                'node_modules/@govbr-ds/core/dist/core-init.js',
            ],
            refresh: true,
        }),
    ],
});
