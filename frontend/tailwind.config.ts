/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#f0f6ff',
          100: '#e0edff',
          200: '#bae0ff',
          300: '#7cc0ff',
          400: '#389eff',
          500: '#097cfc',
          600: '#0066ff', // Official FinanceFlow Brand Blue
          700: '#0052cc',
          800: '#004099',
          900: '#002f73',
          950: '#001a44',
        },
        income: '#10B981',
        expense: '#F43F5E',
        transfer: '#F59E0B',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        xl: '1rem',
        '2xl': '1.25rem',
      },
    },
  },
  plugins: [],
}
