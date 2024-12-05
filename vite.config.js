import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                'https://code.jquery.com/jquery-3.6.0.min.js',
                'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~jquery': path.resolve(__dirname, 'node_modules/jquery'),
            '~datatables.net': path.resolve(__dirname, 'node_modules/datatables.net'),
            '~datatables.net-bs5': path.resolve(__dirname, 'node_modules/datatables.net-bs5')
        }
    }
});