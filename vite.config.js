import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                /* Global */
                'resources/css/shared/app.css',
                'resources/css/shared/icons.css',
                'resources/css/shared/sidebar.css',
                'resources/css/shared/theme.css',
                'resources/css/shared/topbar.css',
                'resources/js/shared/app.js',
                'resources/js/shared/bootstrap.js',
                'resources/js/shared/icons.js',
                'resources/js/shared/sidebar.js',
                'resources/js/shared/theme.js',
                /* Auth */
                'resources/css/pages/login.css',
                'resources/js/pages/login.js',
                /* Dashboard */
                'resources/css/pages/dashboard.css',
                'resources/js/pages/dashboard.js',
                /* Analytics */
                'resources/css/pages/analytics.css',
                'resources/js/pages/analytics.js',

                /* Species Observation */
                'resources/css/pages/species_observations.css',
                'resources/css/pages/species_observation_modal.css',
                'resources/js/pages/species_observation.js',
                'resources/js/pages/species_observation_modal.js',
                /* Protected Areas */
                'resources/css/pages/protected_areas.css',
                'resources/css/pages/protected_area_modal.css',
                'resources/js/pages/protected_area_modal.js',
                'resources/js/pages/protected_areas.js',
                /* Reports */
                'resources/css/pages/endemic_species_report.css',
                'resources/css/pages/migratory_species_report.css',
                'resources/js/pages/endemic_species_report.js',
                'resources/js/pages/migratory_species_report.js',
                'resources/css/pages/species_ranking.css',
                'resources/js/pages/species_ranking.js',
                /* Protected Area Sites */
                'resources/css/pages/protected_area_sites.css',
                'resources/css/pages/protected_area_sites_modal.css',
                'resources/js/pages/protected_area_sites.js',
                'resources/js/pages/protected_area_sites_modal.js',
                /* Settings */
                'resources/css/pages/settings.css',
                'resources/js/pages/settings.js',
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
