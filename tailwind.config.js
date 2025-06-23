/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/js/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        text: '#222651',
        primary: '#1BB4D8',
        accent: '#90E0EF',
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}

