import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { cssSplitPlan, cssSplitSummary } from './css-split-plan.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const adapterPath = resolve(themeRoot, 'assets/src/yneko-reimu-adapter.css');
const basePath = resolve(themeRoot, 'assets/src/yneko-reimu-base.css');
const playerPath = resolve(themeRoot, 'assets/src/reimu-player.css');
const photoswipePath = resolve(themeRoot, 'assets/src/reimu-photoswipe.css');
const sharePath = resolve(themeRoot, 'assets/src/reimu-share.css');
const codePath = resolve(themeRoot, 'assets/src/reimu-code.css');
const searchPath = resolve(themeRoot, 'assets/src/reimu-search.css');
const commentsPath = resolve(themeRoot, 'assets/src/reimu-comments.css');
const editorPath = resolve(themeRoot, 'assets/src/reimu-editor.css');
const buildPath = resolve(root, 'tools/build-reimu.mjs');
const sizePath = resolve(root, 'tools/check-size.mjs');
const adapterCss = await readFile(adapterPath, 'utf8');
const baseCss = await readFile(basePath, 'utf8');
const playerCss = await readFile(playerPath, 'utf8');
const photoswipeCss = await readFile(photoswipePath, 'utf8');
const shareCss = await readFile(sharePath, 'utf8');
const codeCss = await readFile(codePath, 'utf8');
const searchCss = await readFile(searchPath, 'utf8');
const commentsCss = await readFile(commentsPath, 'utf8');
const editorCss = await readFile(editorPath, 'utf8');
const combinedCss = `${baseCss}\n${adapterCss}\n${playerCss}\n${photoswipeCss}\n${shareCss}\n${codeCss}\n${searchCss}\n${commentsCss}\n${editorCss}`;
const buildSource = await readFile(buildPath, 'utf8');
const sizeSource = await readFile(sizePath, 'utf8');
let failed = false;

function fail(message) {
  console.error(`[css-split] ${message}`);
  failed = true;
}

const requiredFeatures = ['comments-profile', 'aplayer', 'code-content', 'photoswipe', 'share', 'search'];
const seenFeatures = new Set(cssSplitPlan.map((entry) => entry.feature));

for (const feature of requiredFeatures) {
  if (!seenFeatures.has(feature)) {
    fail(`Missing split candidate: ${feature}.`);
  }
}

for (const entry of cssSplitPlan) {
  for (const key of ['feature', 'owner', 'currentLoading', 'targetLoading', 'targetOutput', 'trigger', 'gate', 'notes']) {
    if (!entry[key] || 'string' !== typeof entry[key]) {
      fail(`${entry.feature || 'unknown'} must define string field ${key}.`);
    }
  }

  if (!Number.isFinite(entry.maxBytes) || entry.maxBytes <= 0) {
    fail(`${entry.feature} must define a positive maxBytes budget.`);
  }

  if (!Array.isArray(entry.selectors) || entry.selectors.length < 3) {
    fail(`${entry.feature} must define at least three contract selectors.`);
    continue;
  }

  for (const selector of entry.selectors) {
    if (!combinedCss.includes(selector)) {
      fail(`${entry.feature} selector is missing from source CSS: ${selector}`);
    }
  }
}

for (const source of [
  'assets/src/yneko-reimu-base.css',
  'assets/src/yneko-reimu-adapter.css',
  'assets/src/reimu-player.css',
  'assets/src/reimu-photoswipe.css',
  'assets/src/reimu-share.css',
  'assets/src/reimu-code.css',
  'assets/src/reimu-search.css',
  'assets/src/reimu-comments.css',
  'assets/src/reimu-editor.css'
]) {
  if (!buildSource.includes(source)) {
    fail(`build-reimu.mjs must keep ${source} in cssSources.`);
  }
}

if (!sizeSource.includes("'assets/dist/reimu.css', 150 * 1024")) {
  fail('check-size.mjs must keep the main reimu.css 150 KB post-comments-split budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-player.css', 20 * 1024")) {
  fail('check-size.mjs must keep the reimu-player.css 20 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-photoswipe.css', 12 * 1024")) {
  fail('check-size.mjs must keep the reimu-photoswipe.css 12 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-share.css', 14 * 1024")) {
  fail('check-size.mjs must keep the reimu-share.css 14 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-code.css', 24 * 1024")) {
  fail('check-size.mjs must keep the reimu-code.css 24 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-search.css', 16 * 1024")) {
  fail('check-size.mjs must keep the reimu-search.css 16 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-comments.css', 52 * 1024")) {
  fail('check-size.mjs must keep the reimu-comments.css 52 KB budget.');
}

if (!sizeSource.includes("'assets/dist/reimu-editor.css', 20 * 1024")) {
  fail('check-size.mjs must keep the reimu-editor.css 20 KB budget.');
}

for (const line of cssSplitSummary()) {
  console.log(line);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[css-split] CSS split candidates, selector contracts, and main CSS budget are documented.');
}
