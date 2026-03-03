import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['assets/css/app.css', 'assets/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // This ensures the build folder is created directly in the root
        outDir: 'build', 
    }
});