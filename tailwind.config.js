import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    50:  '#f0f4ff',
                    100: '#e0e9ff',
                    200: '#c7d6ff',
                    300: '#a0b8ff',
                    400: '#718fff',
                    500: '#4a67ff',
                    600: '#2a46f5',
                    700: '#1e34e1',
                    800: '#1a2cb7',
                    900: '#1a2a90',
                    950: '#0f1a5a',
                    DEFAULT: '#1a2a90',
                },
                brand: {
                    navy:   '#0f2557',
                    orange: '#f97316',
                    offwhite: '#f8f7f4',
                    surface: '#ffffff',
                    charcoal: '#1a1a2e',
                    border:  '#e5e7eb',
                },
            },
        },
    },

    plugins: [forms],
};
