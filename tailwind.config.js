import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                primary: { "50": "#e6eef5", "100": "#c0d4e5", "200": "#97b8d4", "300": "#6e9cc2", "400": "#4d85b4", "500": "#296fa5", "600": "#1f5983", "700": "#174462", "800": "#0f2f42", "900": "#081b24", "950": "#041017" }
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography, require("tailwind-scrollbar")],
};
