/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
    './vendor/livewire/flux/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        agro: {
          50: '#f2f8f0',
          100: '#e0efe0',
          200: '#c2dfc3',
          300: '#96c69b',
          400: '#67a76f',
          500: '#458a4e',
          600: '#336e3c',
          700: '#2b5732',
          800: '#25462b',
          900: '#203b26',
          950: '#0f2013',
        },
        earth: {
          50: '#faf8f4',
          100: '#f1ece1',
          200: '#e2d6c0',
          300: '#cfba97',
          400: '#bd9f72',
          500: '#b08954',
          600: '#96744a',
          700: '#795c3d',
          800: '#644c34',
          900: '#53402d',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        card: '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
