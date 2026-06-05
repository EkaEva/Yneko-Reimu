import { copyFile, mkdir, readFile, readdir, rm, stat, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { build } from 'vite';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const cssSources = [
  'assets/src/yneko-reimu-base.css',
  'assets/src/yneko-reimu-adapter.css',
  'assets/src/reimu-player.css',
  'assets/src/reimu-photoswipe.css',
  'assets/src/reimu-share.css',
  'assets/src/reimu-code.css',
  'assets/src/reimu-search.css',
  'assets/src/reimu-comments.css'
];
const distRoot = resolve(themeRoot, 'assets/dist');

function minifyCss(css) {
  return css
    .replace(/\/\*[\s\S]*?\*\//g, '')
    .replace(/\s+/g, ' ')
    .replace(/\s*([{}:;,>+~])\s*/g, '$1')
    .replace(/;}/g, '}')
    .trim();
}

const loaderSource = await readFile(resolve(themeRoot, 'assets/src/loader.css'), 'utf8');
const loaderOutput = resolve(themeRoot, 'assets/dist/loader.css');
await mkdir(dirname(loaderOutput), { recursive: true });
await writeFile(loaderOutput, `${minifyCss(loaderSource)}\n`);

const playerStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-player.css'), 'utf8');
const playerStyleOutput = resolve(themeRoot, 'assets/dist/reimu-player.css');
await writeFile(playerStyleOutput, `${minifyCss(playerStyleSource)}\n`);

const photoswipeStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-photoswipe.css'), 'utf8');
const photoswipeStyleOutput = resolve(themeRoot, 'assets/dist/reimu-photoswipe.css');
await writeFile(photoswipeStyleOutput, `${minifyCss(photoswipeStyleSource)}\n`);

const shareStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-share.css'), 'utf8');
const shareStyleOutput = resolve(themeRoot, 'assets/dist/reimu-share.css');
await writeFile(shareStyleOutput, `${minifyCss(shareStyleSource)}\n`);

const codeStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-code.css'), 'utf8');
const codeStyleOutput = resolve(themeRoot, 'assets/dist/reimu-code.css');
await writeFile(codeStyleOutput, `${minifyCss(codeStyleSource)}\n`);

const searchStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-search.css'), 'utf8');
const searchStyleOutput = resolve(themeRoot, 'assets/dist/reimu-search.css');
await writeFile(searchStyleOutput, `${minifyCss(searchStyleSource)}\n`);

const commentsStyleSource = await readFile(resolve(themeRoot, 'assets/src/reimu-comments.css'), 'utf8');
const commentsStyleOutput = resolve(themeRoot, 'assets/dist/reimu-comments.css');
await writeFile(commentsStyleOutput, `${minifyCss(commentsStyleSource)}\n`);

const qrcodeOutput = resolve(themeRoot, 'assets/dist/qrcode.js');

try {
  await copyFile(resolve(root, 'node_modules/qrcode/build/qrcode.js'), qrcodeOutput);
} catch (error) {
  const qrcodeEntry = resolve(root, '.tmp/qrcode-browser-entry.mjs');
  await mkdir(dirname(qrcodeEntry), { recursive: true });
  await writeFile(
    qrcodeEntry,
    [
      "import QRCode from 'qrcode/lib/browser.js';",
      'window.QRCode = QRCode;',
      'export default QRCode;'
    ].join('\n')
  );

  await build({
    configFile: false,
    logLevel: 'silent',
    build: {
      emptyOutDir: false,
      minify: 'esbuild',
      outDir: distRoot,
      rollupOptions: {
        input: qrcodeEntry,
        output: {
          format: 'iife',
          entryFileNames: 'qrcode.js'
        }
      }
    }
  });

  await rm(resolve(root, '.tmp'), { recursive: true, force: true });
}

async function buildClassicScript(input, outputName) {
  await build({
    configFile: false,
    logLevel: 'silent',
    build: {
      emptyOutDir: false,
      minify: 'esbuild',
      outDir: distRoot,
      sourcemap: Boolean(process.env.YNEKO_REIMU_SOURCEMAP),
      rollupOptions: {
        input,
        output: {
          format: 'iife',
          entryFileNames: outputName
        }
      }
    }
  });
}

await buildClassicScript(resolve(themeRoot, 'assets/src/reimu.js'), 'reimu.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/reimu-comments.js'), 'reimu-comments.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/reimu-search.js'), 'reimu-search.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/reimu-photoswipe.js'), 'reimu-photoswipe.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/reimu-share.js'), 'reimu-share.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/admin-settings.js'), 'admin-settings.js');
await buildClassicScript(resolve(themeRoot, 'assets/src/customizer-restore-defaults.js'), 'customizer-restore-defaults.js');

const viteCopiedCursorFiles = [
  'lily-alternate.png',
  'lily-busy.png',
  'lily-cross.png',
  'lily-hand.png',
  'lily-help.png',
  'lily-link.png',
  'lily-move.png',
  'lily-normal.png',
  'lily-resize-ew.png',
  'lily-resize-nesw.png',
  'lily-resize-ns.png',
  'lily-resize-nwse.png',
  'lily-text.png',
  'lily-unavailable.png',
  'lily-work.png'
];

for (const file of viteCopiedCursorFiles) {
  await rm(resolve(distRoot, file), { force: true });
}

async function listDistOutputs(dir, prefix = 'assets/dist') {
  const entries = await readdir(dir, { withFileTypes: true });
  const files = [];

  for (const entry of entries) {
    const relativePath = `${prefix}/${entry.name}`;
    const absolutePath = resolve(dir, entry.name);

    if (entry.isDirectory()) {
      files.push(...await listDistOutputs(absolutePath, relativePath));
      continue;
    }

    if ('assets/dist/manifest.json' !== relativePath) {
      files.push(relativePath);
    }
  }

  return files.sort();
}

let upstreamPkg = {
  name: 'Yneko-Reimu CSS reference snapshot',
  version: 'local',
  repository: {
    url: 'https://github.com/D-Sketon/hexo-theme-reimu'
  },
  license: 'MIT'
};

try {
  upstreamPkg = JSON.parse(await readFile(resolve(root, 'vendor-src/reimu-upstream/package.json'), 'utf8'));
} catch (error) {
  // Local builds use the bundled CSS reference snapshot when the upstream mirror is absent.
}
const outputs = await listDistOutputs(distRoot);
const outputStats = Object.fromEntries(
  await Promise.all(
    outputs.map(async (output) => {
      const file = resolve(themeRoot, output);
      const info = await stat(file);
      return [output, info.size];
    })
  )
);
const manifest = {
  builder: 'vite',
  upstream: {
    name: upstreamPkg.name,
    version: upstreamPkg.version,
    repository: upstreamPkg.repository?.url ?? 'https://github.com/D-Sketon/hexo-theme-reimu',
    license: upstreamPkg.license
  },
  cssSources,
  outputs,
  outputStats
};

await writeFile(resolve(themeRoot, 'assets/dist/manifest.json'), `${JSON.stringify(manifest, null, 2)}\n`);
console.log(`Built Yneko-Reimu assets from ${manifest.upstream.name}@${manifest.upstream.version}`);
