import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // kayıtlar için arkaplan renginin doğru şekilde uygulanması için
    safelist: [
        'bg-yellow-50',
        'bg-lime-50',
        'bg-red-50',
        'bg-green-50',
        'bg-green-500',
        'bg-blue-500',
        'bg-orange-500',
    ],
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
        },
    },
    plugins: [forms],
};
