import { defineConfig } from 'vite';
import { resolve } from 'node:path';

const themeRoot = resolve('theme/Yneko-Reimu');

export default defineConfig({
  build: {
    emptyOutDir: false,
    manifest: false,
    minify: 'esbuild',
    outDir: resolve(themeRoot, 'assets/dist'),
    rollupOptions: {
      input: {
        reimu: resolve(themeRoot, 'assets/src/reimu.js'),
        style: resolve(themeRoot, 'assets/src/reimu.css')
      },
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name === 'style.css') {
            return 'reimu.css';
          }
          return '[name][extname]';
        },
        entryFileNames: (chunkInfo) => {
          if (chunkInfo.name === 'reimu') {
            return 'reimu.js';
          }
          return '[name].js';
        }
      }
    },
    sourcemap: Boolean(process.env.YNEKO_REIMU_SOURCEMAP)
  }
});
