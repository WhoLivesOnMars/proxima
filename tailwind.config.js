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
                primary: {
                    100: 'hsl(var(--primary-100) / <alpha-value>)',
                    300: 'hsl(var(--primary-300) / <alpha-value>)',
                    500: 'hsl(var(--primary-500) / <alpha-value>)',
                    700: 'hsl(var(--primary-700) / <alpha-value>)',
                    900: 'hsl(var(--primary-900) / <alpha-value>)',
                },
                secondary: {
                    100: 'hsl(var(--secondary-100) / <alpha-value>)',
                    300: 'hsl(var(--secondary-300) / <alpha-value>)',
                    500: 'hsl(var(--secondary-500) / <alpha-value>)',
                    700: 'hsl(var(--secondary-700) / <alpha-value>)',
                    900: 'hsl(var(--secondary-900) / <alpha-value>)',
                },
                surface: 'hsl(var(--surface) / <alpha-value>)',
                dark: 'hsl(var(--text-1) / <alpha-value>)',
                light: 'hsl(var(--text-2) / <alpha-value>)',
                accent:  'hsl(var(--accent) / <alpha-value>)',
            },
        },
    },

    plugins: [forms],
};
