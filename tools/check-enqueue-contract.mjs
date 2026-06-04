import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const enqueuePath = resolve(root, 'theme/Yneko-Reimu/inc/enqueue.php');
const source = await readFile(enqueuePath, 'utf8');
let failed = false;

function requireSnippet(label, snippet) {
  if (!source.includes(snippet)) {
    console.error(`[enqueue] Missing ${label}: ${snippet}`);
    failed = true;
  }
}

for (const snippet of [
  "add_action( 'wp_enqueue_scripts', 'yneko_reimu_enqueue_assets' )",
  'function yneko_reimu_enqueue_assets',
  'function yneko_reimu_enqueue_theme_styles',
  'function yneko_reimu_build_frontend_config',
  'function yneko_reimu_enqueue_optional_vendor_assets',
  'function yneko_reimu_enqueue_main_runtime',
  'window.REIMU_CONFIG=',
  "wp_enqueue_script( 'comment-reply' )"
]) {
  requireSnippet('entry/runtime contract', snippet);
}

for (const handle of [
  'yneko-reimu-theme',
  'yneko-reimu-fonts',
  'yneko-reimu-loader',
  'yneko-reimu-main',
  'yneko-reimu-code',
  'yneko-reimu-search',
  'yneko-reimu-comments',
  'yneko-reimu-share',
  'yneko-reimu-algoliasearch',
  'yneko-reimu-firework',
  'yneko-reimu-busuanzi',
  'yneko-reimu-aplayer',
  'yneko-reimu-player',
  'yneko-reimu-meting',
  'yneko-reimu-live2d',
  'yneko-reimu-live2d-core',
  'yneko-reimu-live2d-widget',
  'yneko-reimu-katex',
  'yneko-reimu-katex-auto',
  'yneko-reimu-photoswipe',
  'yneko-reimu-photoswipe-enhance',
  'yneko-reimu-mermaid',
  'yneko-reimu-waline',
  'yneko-reimu-twikoo',
  'yneko-reimu-valine'
]) {
  requireSnippet('style/script handle', handle);
}

for (const asset of [
  'assets/dist/loader.css',
  'assets/dist/reimu.css',
  'assets/dist/reimu-code.css',
  'assets/dist/reimu-search.css',
  'assets/dist/reimu-comments.css',
  'assets/dist/reimu-player.css',
  'assets/dist/reimu-photoswipe.css',
  'assets/dist/reimu-share.css',
  'assets/dist/reimu.js',
  'aplayer@1.10.1/dist/APlayer.min.css',
  'aplayer@1.10.1/dist/APlayer.min.js',
  'meting@2.0.1/dist/Meting.min.js',
  'katex@0.16.24/dist/katex.min.css',
  'katex@0.16.24/dist/katex.min.js',
  'photoswipe@5.4.4/dist/photoswipe.css',
  'mermaid@11.12.0/dist/mermaid.min.js',
  '@waline/client@2.15.8/dist/waline.js',
  'twikoo@1.6.42/dist/twikoo.all.min.js',
  'valine@1.5.3/dist/Valine.min.js'
]) {
  requireSnippet('asset URL contract', asset);
}

for (const configKey of [
  "'language'",
  "'i18nPrefix'",
  "'i18n'",
  "'darkModeDefault'",
  "'showThemeToggle'",
  "'navHide'",
  "'toc'",
  "'homeUrl'",
  "'loaderTexts'",
  "'firework'",
  "'customCursor'",
  "'pjax'",
  "'katex'",
  "'photoswipe'",
  "'mermaid'",
  "'search'",
  "'codeExpandThreshold'",
  "'login'",
  "'ajaxUrl'",
  "'nonce'",
  "'registerNonce'",
  "'registerCodeNonce'",
  "'lostNonce'",
  "'lostCodeNonce'",
  "'profileNonce'",
  "'logoutNonce'",
  "'commentUploads'",
  "'imageEnabled'",
  "'gifEnabled'",
  "'isLoggedIn'",
  "'comments'",
  "'aplayer'"
]) {
  requireSnippet('REIMU_CONFIG key', configKey);
}

for (const nonce of [
  'yneko_reimu_ajax_login',
  'yneko_reimu_ajax_register',
  'yneko_reimu_ajax_register_code',
  'yneko_reimu_ajax_lostpassword',
  'yneko_reimu_ajax_lostpassword_code',
  'yneko_reimu_profile',
  'yneko_reimu_ajax_logout',
  'yneko_reimu_comment_upload',
  'yneko_reimu_submit_comment'
]) {
  requireSnippet('nonce contract', nonce);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[enqueue] script/style handles and REIMU_CONFIG contracts are present.');
}
