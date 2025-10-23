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
                'resources/js/themes/govbr.js',
                'resources/js/themes/bootstrap.js',
                'resources/js/themes/materialize.js',
            ],
            refresh: true,
        }),
    ],
});
