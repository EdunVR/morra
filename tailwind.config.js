import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.js",
    ],

    // Safelist untuk class yang di-generate dinamis (jika ada)
    safelist: [
        // Tambahkan class yang di-generate secara dinamis di sini
        // Contoh: 'bg-red-500', 'text-blue-600'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],

    // Optimasi untuk production build
    future: {
        hoverOnlyWhenSupported: true, // Hover hanya di device yang support
    },
};
