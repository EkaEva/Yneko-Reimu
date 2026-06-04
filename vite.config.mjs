import { defineConfig } from 'vite';
import { resolve } from 'node:path';

const themeRoot = resolve('theme/Yneko-Reimu');

export default defineConfig({
  base: './',
  build: {
    assetsInlineLimit: 0,
    emptyOutDir: true,
    manifest: false,
    minify: 'esbuild',
    outDir: resolve(themeRoot, 'assets/dist'),
    rollupOptions: {
      input: {
        style: resolve(themeRoot, 'assets/src/reimu.css')
      },
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') {
            return 'reimu.css';
          }
          return '[name][extname]';
        }
      }
    },
    sourcemap: Boolean(process.env.YNEKO_REIMU_SOURCEMAP)
  }
});
