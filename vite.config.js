import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                /* Global */
                'resources/css/app.css',
                'resources/css/icons.css',
                'resources/css/sidebar.css',
                'resources/css/theme.css',
                'resources/css/topbar.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/icons.js',
                'resources/js/sidebar.js',
                'resources/js/theme.js',
                /* Auth */
                'resources/css/Login.css',
                'resources/js/Login.js',
                /* Dashboard */
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
                /* Species Observation */
                'resources/css/species-observations.css',
                'resources/css/species-observation-modal.css',
                'resources/js/species-observation.js',
                'resources/js/species-observation-modal.js',
                /* Protected Areas */
                'resources/css/protected-areas.css',
                'resources/css/protected-area-modal.css',
                'resources/js/protected-area-modal.js',
                'resources/js/protected-areas.js',
                /* Protected Area Sites */
                'resources/css/protected-area-sites.css',
                'resources/css/protected-area-sites-modal.css',
                'resources/js/protected-area-sites.js',
                'resources/js/protected-area-sites-modal.js',
                /* Analytics */
                'resources/css/analytics.css',
                'resources/js/analytics.js',
                /* Reports */
                'resources/css/reports.css',
                'resources/js/reports.js',
                /* Settings */
                'resources/css/settings.css',
                'resources/js/settings.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
