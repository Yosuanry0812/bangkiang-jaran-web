const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                editorial: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
