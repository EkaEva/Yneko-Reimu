import { readFile, stat } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const manifestPath = resolve(themeRoot, 'assets/dist/manifest.json');
const budgets = new Map([
  ['assets/dist/reimu.js', 120 * 1024],
  ['assets/dist/reimu.css', 220 * 1024]
]);

function formatBytes(bytes) {
  return `${(bytes / 1024).toFixed(1)} KB`;
}

async function fileSize(relativePath) {
  const info = await stat(resolve(themeRoot, relativePath));
  return info.size;
}

const manifest = JSON.parse(await readFile(manifestPath, 'utf8'));
const outputStats = manifest.outputStats && typeof manifest.outputStats === 'object' ? manifest.outputStats : {};
let failed = false;

for (const [relativePath, maxBytes] of budgets) {
  const bytes = Number(outputStats[relativePath] ?? await fileSize(relativePath));
  if (bytes > maxBytes) {
    console.error(`[size] ${relativePath} is ${formatBytes(bytes)}, over budget ${formatBytes(maxBytes)}.`);
    failed = true;
    continue;
  }
  console.log(`[size] ${relativePath} is ${formatBytes(bytes)} / ${formatBytes(maxBytes)}.`);
}

const mainScript = await readFile(resolve(themeRoot, 'assets/dist/reimu.js'), 'utf8');
const classicScriptPatterns = [
  { pattern: /\bimport\.meta\b/, label: 'import.meta' },
  { pattern: /\bimport\s*\(/, label: 'dynamic import(' }
];

for (const check of classicScriptPatterns) {
  if (check.pattern.test(mainScript)) {
    console.error(`[classic-script] assets/dist/reimu.js contains ${check.label}.`);
    failed = true;
  }
}

if (!failed) {
  console.log('[classic-script] assets/dist/reimu.js is compatible with classic script loading.');
}

if (failed) {
  process.exitCode = 1;
}
