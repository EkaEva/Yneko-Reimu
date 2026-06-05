import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const runtimeScripts = [
  'assets/dist/reimu.js',
  'assets/dist/reimu-search.js',
  'assets/dist/reimu-share.js',
  'assets/dist/reimu-photoswipe.js',
  'assets/dist/admin-settings.js',
  'assets/dist/qrcode.js'
];

const sourceFiles = {
  frontend: await readFile(resolve(themeRoot, 'assets/src/reimu.js'), 'utf8'),
  searchEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-search.js'), 'utf8'),
  shareEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-share.js'), 'utf8'),
  photoswipeEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-photoswipe.js'), 'utf8'),
  modals: await readFile(resolve(themeRoot, 'inc/comments/modals.php'), 'utf8'),
  enqueue: await readFile(resolve(themeRoot, 'inc/enqueue.php'), 'utf8')
};

const failures = [];

function fail(message) {
  failures.push(message);
}

function requireSnippet(label, snippet, haystack) {
  if (!haystack.includes(snippet)) {
    fail(`${label}: missing ${snippet}`);
  }
}

function forbidPattern(label, pattern, haystack) {
  if (pattern.test(haystack)) {
    fail(`${label}: matched forbidden ${pattern}`);
  }
}

for (const relativePath of runtimeScripts) {
  const script = await readFile(resolve(themeRoot, relativePath), 'utf8');
  try {
    // Validates classic-script parseability without executing browser APIs.
    new Function(script);
  } catch (error) {
    fail(`${relativePath}: classic script parse failed: ${error.message}`);
  }

  forbidPattern(`${relativePath} classic script`, /\bimport\.meta\b/, script);
  forbidPattern(`${relativePath} classic script`, /\bimport\s*\(/, script);
  forbidPattern(`${relativePath} classic script`, /^\s*(?:import|export)\b/m, script);
}

for (const snippet of [
  'window.REIMU_CONFIG',
  'window.ReimuWP',
  'init: init',
  'function syncInlineConfig(nextDoc)',
  'function replayPjaxScripts(nextDoc)',
  'function navigateTo(url, options)',
  "script.id = 'yneko-reimu-search-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-search.js'",
  "script.id = 'yneko-reimu-share-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-share.js'",
  "script.id = 'yneko-reimu-photoswipe-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-photoswipe.js'",
  "replaceElement('#reimu-login-modal', nextDoc",
  "replaceElement('#reimu-profile-modal', nextDoc",
  "event.target.closest('[data-reimu-profile-open]')",
  'function refreshCommentLoginState()',
  'form.dataset.ajaxCommentReady',
  'button.dataset.commentDeleteReady'
]) {
  requireSnippet('main runtime smoke anchor', snippet, sourceFiles.frontend);
}

for (const [label, haystack, snippets] of [
  ['search runtime global', sourceFiles.searchEntry, ['window.ReimuSearchRuntime = {', 'init: function ()', 'open: function ()']],
  ['share runtime global', sourceFiles.shareEntry, ['window.ReimuShareRuntime = {', 'init: function ()']],
  ['photoswipe runtime global', sourceFiles.photoswipeEntry, ['window.ReimuPhotoSwipeRuntime = {', 'init: function ()', 'destroy: function ()']]
]) {
  for (const snippet of snippets) {
    requireSnippet(label, snippet, haystack);
  }
}

for (const snippet of [
  'id="reimu-login-modal"',
  'id="reimu-profile-modal"',
  'reimu-login-panel',
  'reimu-profile-form'
]) {
  requireSnippet('comments/profile modal smoke anchor', snippet, sourceFiles.modals);
}

for (const snippet of [
  'assets/dist/reimu.js',
  'assets/dist/reimu-comments.css',
  'window.REIMU_CONFIG='
]) {
  requireSnippet('enqueue smoke anchor', snippet, sourceFiles.enqueue);
}

if (failures.length) {
  console.error('[runtime-smoke] Runtime smoke test failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[runtime-smoke] ${runtimeScripts.length} built scripts parse as classic scripts and key runtime anchors are present.`);
