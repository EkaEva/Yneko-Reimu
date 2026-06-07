import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const functionsSource = await readFile(resolve(themeRoot, 'functions.php'), 'utf8');
const setupSource = await readFile(resolve(themeRoot, 'inc/setup.php'), 'utf8');
const editorSource = await readFile(resolve(themeRoot, 'inc/editor.php'), 'utf8');
const buildSource = await readFile(resolve(root, 'tools/build-reimu.mjs'), 'utf8');
const sizeSource = await readFile(resolve(root, 'tools/check-size.mjs'), 'utf8');
const packageSource = await readFile(resolve(root, 'tools/check-package.mjs'), 'utf8');
const adapterCss = await readFile(resolve(themeRoot, 'assets/src/yneko-reimu-adapter.css'), 'utf8');
const editorCss = await readFile(resolve(themeRoot, 'assets/src/reimu-editor.css'), 'utf8');

let failed = false;

function fail(message) {
  console.error(`[editor-contract] ${message}`);
  failed = true;
}

function requireIncludes(source, snippets, label) {
  for (const snippet of snippets) {
    if (!source.includes(snippet)) {
      fail(`${label} must include ${snippet}`);
    }
  }
}

requireIncludes(functionsSource, [
  "require_once YNEKO_REIMU_DIR . '/inc/editor.php';"
], 'functions.php');

requireIncludes(setupSource, [
  "add_editor_style( 'assets/dist/reimu.css' );",
  "add_editor_style( 'assets/dist/reimu-editor.css' );"
], 'inc/setup.php');

if (!/defined\(\s*['"]ABSPATH['"]\s*\)/.test(editorSource)) {
  fail('inc/editor.php must keep an ABSPATH direct-access guard.');
}

requireIncludes(editorSource, [
  "add_action( 'init', 'yneko_reimu_register_editor_blocks' );",
  "add_action( 'enqueue_block_editor_assets', 'yneko_reimu_enqueue_block_editor_assets' );",
  "wp_enqueue_style(\n\t\t'yneko-reimu-editor'",
  "YNEKO_REIMU_URI . '/assets/dist/reimu-editor.css'",
  "register_block_style(\n\t\t'core/table'",
  "register_block_style(\n\t\t'core/code'",
  "register_block_style(\n\t\t\t'core/group'",
  "is-style-reimu-field-table",
  "is-style-reimu-code-window",
  "is-style-reimu-notice-tip",
  "is-style-reimu-notice-info",
  "is-style-reimu-notice-warning",
  "custom-block tip",
  "custom-block info",
  "custom-block warning",
  "yneko-guide-table-wrap",
  "yneko-guide-field-table"
], 'inc/editor.php');

for (const slug of [
  'yneko-reimu/article-intro',
  'yneko-reimu/two-column-note',
  'yneko-reimu/settings-table',
  'yneko-reimu/code-window',
  'yneko-reimu/tip-notice',
  'yneko-reimu/info-notice',
  'yneko-reimu/warning-notice',
  'yneko-reimu/technical-note'
]) {
  if (!editorSource.includes(slug)) {
    fail(`inc/editor.php must preserve block pattern slug ${slug}.`);
  }
}

for (const styleClass of [
  '.editor-styles-wrapper',
  '.wp-block-table.is-style-reimu-field-table',
  '.wp-block-code.is-style-reimu-code-window',
  '.wp-block-group.is-style-reimu-notice-tip',
  '.wp-block-group.is-style-reimu-notice-info',
  '.wp-block-group.is-style-reimu-notice-warning',
  '.custom-block-title'
]) {
  if (!editorCss.includes(styleClass)) {
    fail(`assets/src/reimu-editor.css must style ${styleClass}.`);
  }
}

for (const frontendSelector of [
  '.wp-block-table.is-style-reimu-field-table table',
  '.wp-block-table.yneko-guide-field-table table'
]) {
  if (!adapterCss.includes(frontendSelector)) {
    fail(`assets/src/yneko-reimu-adapter.css must keep front-end support for ${frontendSelector}.`);
  }
}

requireIncludes(buildSource, [
  "'assets/src/reimu-editor.css'",
  "assets/dist/reimu-editor.css"
], 'tools/build-reimu.mjs');

requireIncludes(sizeSource, [
  "'assets/dist/reimu-editor.css', 20 * 1024"
], 'tools/check-size.mjs');

requireIncludes(packageSource, [
  'release-notes-v(?!0\\.2\\.17\\.md$)',
  "`Yneko-Reimu/docs/release-notes-v${packageVersion}.md`"
], 'tools/check-package.mjs');

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[editor-contract] block editor patterns, styles, editor asset, and front-end class boundaries are protected.');
}
