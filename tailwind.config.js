/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/**/*.vue",
    "./resources/**/**/**/*.vue",
  ],
  theme: {
    extend: {
      backgroundImage:{
        'nav': 'linear-gradient(45deg, #67e8f9 0%, rgba(103, 232, 249, 0.95) 19.51%, rgba(103, 232, 249, 0.9) 35.13%, rgba(103, 232, 249, 0.7) 53.84%, rgba(103, 232, 249, 0.7) 100%)'
      },
      colors: {
        'custom-black': '#1e1e1e',
      },
      boxShadow:{
        'custom-blue': '0 4px 24px 0 rgba(103, 232, 249, 0.25)',
        'custom-white': '0 4px 10px 0 rgba(255, 255, 255, 0.25)',
      }
    },
  },
  plugins: [],
}

