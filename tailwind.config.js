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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    light: "#EF3F3C",
                    DEFAULT: "#E4252C", // primary
                    dark: "#8F1924",
                    darker: "#6C0C1C",
                    },
                    neutral: {
                    100: "#DCDBDB",
                    300: "#BCBCBC",
                    600: "#737272",
                    900: "#010101",
                    },
            },
            borderColor: ({ theme }) => ({
                DEFAULT: theme('colors.neutral.300', 'currentColor'),
                brand: theme('colors.brand.DEFAULT'),
            }),
            ringColor: ({ theme }) => ({
                brand: theme('colors.brand.DEFAULT'),
            }),
        },
    },

    plugins: [
        forms({
            strategy: 'class', // biar lebih fleksibel (pakai .form-input, .form-select, dll)
        }),
    ],
};
