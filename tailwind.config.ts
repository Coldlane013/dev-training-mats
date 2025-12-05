/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue", 
  ],
  theme: {
    fontFamily: { 
      // This REPLACES the default 'font-sans' stack
      // The first name MUST match the font name used in the Google Fonts link ('Roboto').
      sans: ['Roboto', 'ui-sans-serif', 'system-ui', 'sans-serif'], 
      
      // Optionally, you can keep the default serif and mono fonts or define custom ones
      serif: ['ui-serif', 'Georgia', 'serif'],
      mono: ['ui-monospace', 'SFMono-Regular', 'monospace'],
    },
    extend: {},
  },
  plugins: [],
}