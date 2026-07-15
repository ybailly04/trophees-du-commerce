import { resolve } from 'path'
import kirby from 'vite-plugin-kirby'

export default ({ mode }) => ({
  root: 'src',
  base: mode === 'development' ? '/' : '/dist/',

  build: {
    outDir: resolve(process.cwd(), 'public/dist'),
    assetsDir: 'assets',

    rollupOptions: {
      input: ['src/assets/js/app.js', 'src/assets/css/style.scss'],
    },
  },

  plugins: [
    kirby({
      watch: [
        '../site/(templates|snippets|controllers|models|layouts)/**/*.php',
        '../content/**/*',
      ],

      kirbyConfigDir: 'site/config', // default
    }),
  ],
})