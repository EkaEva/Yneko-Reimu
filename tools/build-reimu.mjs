import { mkdir, readFile, stat, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const cssSources = [
  'assets/src/reimu-upstream.css',
  'assets/src/yneko-reimu-adapter.css'
];
const outputs = [
  'assets/dist/loader.css',
  'assets/dist/reimu.js',
  'assets/dist/reimu.css'
];

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

let upstreamPkg = {
  name: 'hexo-theme-reimu',
  version: '1.11.0',
  repository: {
    url: 'https://github.com/D-Sketon/hexo-theme-reimu'
  },
  license: 'MIT'
};

try {
  upstreamPkg = JSON.parse(await readFile(resolve(root, 'vendor-src/reimu-upstream/package.json'), 'utf8'));
} catch (error) {
  // The upload ZIP intentionally excludes upstream source files; local builds can still
  // run from the bundled CSS snapshot when the source mirror is absent.
}
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
  builtAt: new Date().toISOString(),
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
