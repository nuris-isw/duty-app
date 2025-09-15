import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Di v4, tidak ada lagi blok 'extend'. Semua kustomisasi berada langsung di bawah 'theme'.
    theme: {
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
        
        // Utilitas harus didefinisikan secara eksplisit agar bisa menggunakan warna kustom Anda.
        backgroundColor: ({ theme }) => ({
            ...theme('colors'),
        }),
        textColor: ({ theme }) => ({
            ...theme('colors'),
        }),
        borderColor: ({ theme }) => ({
            ...theme('colors'),
            DEFAULT: theme('colors.neutral.300'),
            brand: theme('colors.brand.DEFAULT'),
        }),
        ringColor: ({ theme }) => ({
            ...theme('colors'),
            brand: theme('colors.brand.DEFAULT'),
        }),
    },

    plugins: [],
};