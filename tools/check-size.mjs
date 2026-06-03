import { readFile, stat } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { featureLoadingPlan, featureLoadingSummary } from './feature-loading-plan.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const manifestPath = resolve(themeRoot, 'assets/dist/manifest.json');
const budgets = new Map([
  ['assets/dist/reimu.js', 120 * 1024],
  ['assets/dist/reimu-search.js', 24 * 1024],
  ['assets/dist/reimu-photoswipe.js', 24 * 1024],
  ['assets/dist/reimu-share.js', 24 * 1024],
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

const classicScripts = [
  'assets/dist/reimu.js',
  'assets/dist/reimu-search.js',
  'assets/dist/reimu-photoswipe.js',
  'assets/dist/reimu-share.js'
];
const classicScriptPatterns = [
  { pattern: /\bimport\.meta\b/, label: 'import.meta' },
  { pattern: /\bimport\s*\(/, label: 'dynamic import(' },
  { pattern: /^\s*(?:import|export)\b/m, label: 'top-level ESM import/export' }
];

for (const relativePath of classicScripts) {
  const script = await readFile(resolve(themeRoot, relativePath), 'utf8');
  for (const check of classicScriptPatterns) {
    if (check.pattern.test(script)) {
      console.error(`[classic-script] ${relativePath} contains ${check.label}.`);
      failed = true;
    }
  }
}

if (!failed) {
  console.log('[classic-script] public runtime scripts are compatible with classic script loading.');
}

for (const line of featureLoadingSummary()) {
  console.log(line);
}

const incompleteLoadingEntries = featureLoadingPlan.filter((entry) => !entry.trigger || !entry.targetLoading || !entry.gate);
if (incompleteLoadingEntries.length) {
  console.error('[loading] Feature loading plan entries must include trigger, targetLoading, and gate:');
  for (const entry of incompleteLoadingEntries) {
    console.error(`- ${entry.feature}`);
  }
  failed = true;
}

if (failed) {
  process.exitCode = 1;
}
