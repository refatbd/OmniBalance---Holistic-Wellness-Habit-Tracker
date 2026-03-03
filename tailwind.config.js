/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class', // Enables dark mode toggling via class
  theme: {
    extend: {
      colors: {
        primary: '#10b981', // App theme color (Emerald)
      }
    },
  },
  plugins: [],
}