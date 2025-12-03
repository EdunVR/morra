import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        // Code splitting untuk vendor libraries
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["vue", "axios"],
                    sweetalert: ["sweetalert2"],
                },
            },
        },
        // Minification settings
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },
        // Chunk size warning limit
        chunkSizeWarningLimit: 1000,
        // Source maps untuk debugging (disable di production jika tidak perlu)
        sourcemap: false,
    },
    // Optimasi dependencies
    optimizeDeps: {
        include: ["vue", "axios", "sweetalert2"],
    },
});
