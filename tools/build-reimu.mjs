import { mkdir, copyFile, readFile, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const files = [
  ['assets/src/loader.css', 'assets/dist/loader.css'],
  ['assets/src/reimu.js', 'assets/dist/reimu.js']
];

for (const [src, dest] of files) {
  const from = resolve(themeRoot, src);
  const to = resolve(themeRoot, dest);
  await mkdir(dirname(to), { recursive: true });
  await copyFile(from, to);
}

const cssSources = [
  'assets/src/reimu-upstream.css',
  'assets/src/yneko-reimu-adapter.css'
];
const css = await Promise.all(
  cssSources.map(async (src) => {
    const content = await readFile(resolve(themeRoot, src), 'utf8');
    return `${content.trim()}\n`;
  })
);
await writeFile(resolve(themeRoot, 'assets/dist/reimu.css'), `${css.join('\n')}\n`);

let upstreamPkg = {
  name: 'hexo-theme-reimu',
  version: '1.11.0',
  repository: {
    url: 'https://github.com/D-Sketon/hexo-theme-reimu'
  },
  license: 'MIT'
};

try {
  upstreamPkg = JSON.parse(await readFile(resolve(themeRoot, 'assets/reimu-upstream/package.json'), 'utf8'));
} catch (error) {
  // The upload ZIP intentionally excludes upstream source files; local builds can still
  // run from the bundled CSS snapshot when the source mirror is absent.
}
const manifest = {
  builtAt: new Date().toISOString(),
  upstream: {
    name: upstreamPkg.name,
    version: upstreamPkg.version,
    repository: upstreamPkg.repository?.url ?? 'https://github.com/D-Sketon/hexo-theme-reimu',
    license: upstreamPkg.license
  },
  cssSources,
  outputs: [...files.map(([, dest]) => dest), 'assets/dist/reimu.css']
};

await writeFile(resolve(themeRoot, 'assets/dist/manifest.json'), `${JSON.stringify(manifest, null, 2)}\n`);
console.log(`Built Yneko-Reimu assets from ${manifest.upstream.name}@${manifest.upstream.version}`);
