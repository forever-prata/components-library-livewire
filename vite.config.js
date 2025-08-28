import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/themes/govbr.css',
                'resources/css/themes/bootstrap.css',
                'resources/css/themes/materialize.css',
                'node_modules/@govbr-ds/core/dist/core-init.js',
                'node_modules/@govbr-ds/core/dist/core.min.js',
            ],
            refresh: true,
        }),
    ],
});
