import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const enqueuePaths = [
  resolve(themeRoot, 'inc/enqueue.php'),
  resolve(themeRoot, 'inc/enqueue/assets.php'),
  resolve(themeRoot, 'inc/enqueue/head.php'),
  resolve(themeRoot, 'inc/enqueue/styles.php'),
  resolve(themeRoot, 'inc/enqueue/config.php'),
  resolve(themeRoot, 'inc/enqueue/vendors.php'),
  resolve(themeRoot, 'inc/enqueue/runtime.php')
];
const enqueueSource = (await Promise.all(enqueuePaths.map((path) => readFile(path, 'utf8')))).join('\n');
const commentModalSource = await readFile(resolve(themeRoot, 'inc/comments/modals.php'), 'utf8');
const setupSource = await readFile(resolve(themeRoot, 'inc/setup.php'), 'utf8');
const editorSource = await readFile(resolve(themeRoot, 'inc/editor.php'), 'utf8');
const settingsAdminAssetsSource = await readFile(resolve(themeRoot, 'inc/settings/admin/assets.php'), 'utf8');
const themeUpdaterSource = await readFile(resolve(themeRoot, 'inc/theme-updater.php'), 'utf8');
const settingsPageGeneralSource = await readFile(resolve(themeRoot, 'inc/settings/page/general.php'), 'utf8');
const commentRenderingPaths = [
  resolve(themeRoot, 'inc/comments/rendering.php'),
  resolve(themeRoot, 'inc/comments/rendering/toolbar.php'),
  resolve(themeRoot, 'inc/comments/rendering/identity.php'),
  resolve(themeRoot, 'inc/comments/rendering/environment.php'),
  resolve(themeRoot, 'inc/comments/rendering/markdown.php'),
  resolve(themeRoot, 'inc/comments/rendering/list-helpers.php'),
  resolve(themeRoot, 'inc/comments/rendering/list.php'),
  resolve(themeRoot, 'inc/comments/rendering/external-panels.php'),
  resolve(themeRoot, 'inc/comments/rendering/external.php')
];
const commentRenderingSource = (await Promise.all(commentRenderingPaths.map((path) => readFile(path, 'utf8')))).join('\n');

const runtimeScripts = [
  'assets/dist/reimu.js',
  'assets/dist/reimu-comments.js',
  'assets/dist/reimu-search.js',
  'assets/dist/reimu-share.js',
  'assets/dist/reimu-photoswipe.js',
  'assets/dist/admin-settings.js',
  'assets/dist/qrcode.js'
];

const frontendEntry = await readFile(resolve(themeRoot, 'assets/src/reimu.js'), 'utf8');
const commentsEntry = await readFile(resolve(themeRoot, 'assets/src/reimu-comments.js'), 'utf8');
const commentsRuntimePaths = [
  resolve(themeRoot, 'assets/src/reimu/comments-profile.js'),
  resolve(themeRoot, 'assets/src/reimu/auth-forms.js'),
  resolve(themeRoot, 'assets/src/reimu/comment-upload.js'),
  resolve(themeRoot, 'assets/src/reimu/comment-mutations.js'),
  resolve(themeRoot, 'assets/src/reimu/login-state.js')
];
const commentsRuntime = (await Promise.all(commentsRuntimePaths.map((path) => readFile(path, 'utf8')))).join('\n');

const sourceFiles = {
  frontend: `${frontendEntry}\n${commentsEntry}\n${commentsRuntime}`,
  frontendEntry,
  runtimeLoader: await readFile(resolve(themeRoot, 'assets/src/reimu/runtime-loader.js'), 'utf8'),
  pjaxUtils: await readFile(resolve(themeRoot, 'assets/src/reimu/pjax-utils.js'), 'utf8'),
  commentsEntry,
  commentsRuntime,
  searchEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-search.js'), 'utf8'),
  shareEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-share.js'), 'utf8'),
  photoswipeEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-photoswipe.js'), 'utf8'),
  modals: `${commentModalSource}\n${commentRenderingSource}`,
  enqueue: enqueueSource,
  editor: `${setupSource}\n${editorSource}`,
  adminSettings: settingsAdminAssetsSource
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
  'createLazyRuntimeLoader',
  "script.src = getAssetBaseUrl() + scriptName",
  "scriptId: 'yneko-reimu-search-runtime'",
  "scriptName: 'reimu-search.js'",
  "scriptId: 'yneko-reimu-share-runtime'",
  "scriptName: 'reimu-share.js'",
  "scriptId: 'yneko-reimu-photoswipe-runtime'",
  "scriptName: 'reimu-photoswipe.js'",
  "scriptId: 'yneko-reimu-comments-runtime'",
  "scriptName: 'reimu-comments.js'",
  "replaceElement('#reimu-login-modal', nextDoc",
  "replaceElement('#reimu-profile-modal', nextDoc",
  "event.target.closest('[data-reimu-profile-open]')",
  'function refreshCommentLoginState()',
  'form.dataset.ajaxCommentReady',
  'button.dataset.commentDeleteReady'
]) {
  requireSnippet('main runtime smoke anchor', snippet, `${sourceFiles.frontend}
${sourceFiles.runtimeLoader}
${sourceFiles.pjaxUtils}`);
}

for (const [label, haystack, snippets] of [
  ['comments runtime global', sourceFiles.commentsEntry, ['window.ReimuCommentsRuntime = {', 'init: init', 'refreshCommentLoginState: function ()']],
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

for (const snippet of [
  "add_editor_style( 'assets/dist/reimu-editor.css' );",
  "wp_enqueue_style(\n\t\t'yneko-reimu-editor'",
  "YNEKO_REIMU_URI . '/assets/dist/reimu-editor.css'",
  "register_block_pattern_category(\n\t\t'yneko-reimu'",
  'yneko-reimu/settings-table',
  'yneko-reimu/code-window',
  'yneko-reimu/technical-note',
  'is-style-reimu-field-table',
  'is-style-reimu-code-window',
  'is-style-reimu-notice-warning'
]) {
  requireSnippet('editor smoke anchor', snippet, sourceFiles.editor);
}

for (const snippet of [
  "'appearance_page_yneko-reimu-settings'",
  "wp_register_script( 'yneko-reimu-admin-settings'",
  "YNEKO_REIMU_URI . '/assets/dist/admin-settings.js'",
  'window.YNEKO_REIMU_ADMIN_I18N=',
  'data-yneko-theme-update-status',
  'data-yneko-theme-update-force',
  'data-yneko-theme-update-clear',
  'function yneko_reimu_theme_updater_fetch_status',
  'function yneko_reimu_theme_updater_handle_admin_action',
  "check_admin_referer( 'yneko_reimu_theme_update_' . $action )",
  "current_user_can( 'manage_options' )",
  "'wp_http_error'",
  "'http_error'",
  "'invalid_json'",
  "'unstable_release'",
  "'invalid_tag'",
  "'missing_asset'"
]) {
  requireSnippet('admin settings smoke anchor', snippet, `${sourceFiles.adminSettings}\n${themeUpdaterSource}\n${settingsPageGeneralSource}`);
}

if (failures.length) {
  console.error('[runtime-smoke] Runtime smoke test failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[runtime-smoke] ${runtimeScripts.length} built scripts parse as classic scripts and key runtime anchors are present.`);
