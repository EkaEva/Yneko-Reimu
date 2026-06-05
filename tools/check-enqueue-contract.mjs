import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const enqueuePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/assets.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/head.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/favicon.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/styles.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/config.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/vendors.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/enqueue/runtime.php')
];
const source = (await Promise.all(enqueuePaths.map((path) => readFile(path, 'utf8')))).join('\n');
let failed = false;

function requireSnippet(label, snippet) {
  if (!source.includes(snippet)) {
    console.error(`[enqueue] Missing ${label}: ${snippet}`);
    failed = true;
  }
}

for (const snippet of [
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/assets.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/head.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/favicon.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/styles.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/config.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/vendors.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/enqueue/runtime.php';",
  "add_action( 'wp_enqueue_scripts', 'yneko_reimu_enqueue_assets' )",
  'function yneko_reimu_enqueue_assets',
  'function yneko_reimu_enqueue_theme_styles',
  'function yneko_reimu_build_frontend_config',
  'function yneko_reimu_enqueue_optional_vendor_assets',
  'function yneko_reimu_enqueue_main_runtime',
  'window.REIMU_CONFIG=',
  "wp_register_script( 'yneko-reimu-comments-runtime'",
  "wp_enqueue_script( 'comment-reply' )"
]) {
  requireSnippet('entry/runtime contract', snippet);
}

for (const snippet of [
  'function yneko_reimu_favicon',
  'function yneko_reimu_favicon_serve_root_icon',
  "add_action( 'wp_head', 'yneko_reimu_favicon', 5 )",
  "add_action( 'template_redirect', 'yneko_reimu_favicon_serve_root_icon', 0 )",
  "add_action( 'do_favicon', 'yneko_reimu_favicon_serve_root_icon', 0 )",
  "'/favicon.ico'",
  "'/favicon-32x32.png'",
  "'/favicon-192x192.png'",
  "'/apple-touch-icon.png'",
  "yneko_reimu_favicon_link( 'shortcut icon', yneko_reimu_favicon_root_url( 'favicon.ico' ), 'image/x-icon' )",
  "yneko_reimu_favicon_link( 'apple-touch-icon', yneko_reimu_favicon_root_url( 'apple-touch-icon.png' ), 'image/png', '180x180' )",
  'function yneko_reimu_favicon_build_ico'
]) {
  requireSnippet('favicon compatibility contract', snippet);
}

for (const handle of [
  'yneko-reimu-theme',
  'yneko-reimu-fonts',
  'yneko-reimu-loader',
  'yneko-reimu-main',
  'yneko-reimu-comments-runtime',
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
  'assets/dist/reimu-comments.js',
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

const stylesSource = await readFile(resolve(root, 'theme/Yneko-Reimu/inc/enqueue/styles.php'), 'utf8');
if (!stylesSource.includes("wp_add_inline_style( 'yneko-reimu-main', yneko_reimu_cursor_variables_css() );")) {
  console.error('[enqueue] Missing final cursor variable override after the main stylesheet.');
  failed = true;
}
if (/if\s*\(\s*!\s*yneko_reimu_feature_enabled\(\s*'yneko_reimu_custom_cursor'/.test(stylesSource)) {
  console.error('[enqueue] Cursor variables must not be emitted only for the disabled custom-cursor state.');
  failed = true;
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[enqueue] script/style handles and REIMU_CONFIG contracts are present.');
}
