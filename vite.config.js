import { resolve } from 'path'
import { rmSync } from 'fs'
import kirby from 'vite-plugin-kirby'

export default ({ mode }) => ({
  root: 'src',
  base: mode === 'development' ? '/' : '/public/dist/',

  build: {
    outDir: resolve(process.cwd(), 'public/dist'),
    assetsDir: 'assets',

    rollupOptions: {
      input: ['src/assets/js/app.js', 'src/assets/css/style.scss'],
      output: {
        assetFileNames: (assetInfo) => {
          const name = assetInfo.names?.[0] ?? assetInfo.name ?? ''
          if (/\.(woff2?|ttf|eot|otf)$/.test(name)) {
            return 'fonts/[name][extname]'
          }
          return 'assets/[name][extname]'
        },
      },
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