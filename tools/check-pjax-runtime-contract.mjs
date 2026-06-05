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

const frontendEntry = await readFile(resolve(themeRoot, 'assets/src/reimu.js'), 'utf8');
const commentsRuntime = await readFile(resolve(themeRoot, 'assets/src/reimu/comments-profile.js'), 'utf8');

const files = {
  frontend: `${frontendEntry}\n${commentsRuntime}`,
  frontendEntry,
  commentsRuntime,
  searchEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-search.js'), 'utf8'),
  searchModule: await readFile(resolve(themeRoot, 'assets/src/reimu/search.js'), 'utf8'),
  shareEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-share.js'), 'utf8'),
  shareModule: await readFile(resolve(themeRoot, 'assets/src/reimu/share.js'), 'utf8'),
  photoswipeEntry: await readFile(resolve(themeRoot, 'assets/src/reimu-photoswipe.js'), 'utf8'),
  photoswipeModule: await readFile(resolve(themeRoot, 'assets/src/reimu/photoswipe.js'), 'utf8'),
  enqueue: enqueueSource,
  featurePlan: await readFile(resolve(root, 'tools/feature-loading-plan.mjs'), 'utf8'),
  cssPlan: await readFile(resolve(root, 'tools/css-split-plan.mjs'), 'utf8')
};

const source = Object.values(files).join('\n');
const failures = [];

function requireSnippet(label, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    failures.push(`${label}: missing ${snippet}`);
  }
}

function forbidSnippet(label, snippet, haystack = source) {
  if (haystack.includes(snippet)) {
    failures.push(`${label}: forbidden ${snippet}`);
  }
}

for (const snippet of [
  'function shouldPjaxLink(anchor, event)',
  "anchor.dataset.noPjax !== undefined || anchor.closest('[data-no-pjax]')",
  "anchor.target && anchor.target !== '_self'",
  'anchor.hasAttribute(\'download\')',
  '/^(?:mailto|tel|sms|javascript|data|blob|vbscript):/i',
  'url.origin !== window.location.origin',
  'isAssetPath(url.pathname)',
  '/\\/(?:wp-admin|wp-login\\.php|wp-json|xmlrpc\\.php)(?:\\/|$)/i',
  '/[?&](?:feed|preview|customize_changeset_uuid|replytocom)=/i',
  'isSamePageHashUrl(url)'
]) {
  requireSnippet('PJAX link exclusion contract', snippet, files.frontend);
}

for (const snippet of [
  'function syncInlineConfig(nextDoc)',
  "text.indexOf('window.REIMU_CONFIG=') !== -1",
  'config = window.REIMU_CONFIG || config',
  "text.indexOf('window.REIMU_HEATMAP_CONFIG') !== -1",
  'window.REIMU_HEATMAP_CONFIG = undefined',
  'function replayPjaxScripts(nextDoc)',
  "qsa('#wrap script, #mobile-nav script, .site-search script', nextDoc)",
  "text.indexOf('window.REIMU_HEATMAP_CONFIG') !== -1",
  "qsa('script[src]').some(function (existing) { return existing.getAttribute('src') === src; })"
]) {
  requireSnippet('PJAX config/script replay contract', snippet, files.frontend);
}

for (const snippet of [
  'function getAuthModalState()',
  'loginOpen:',
  'loginPanel:',
  'profileOpen:',
  'function restoreAuthModalState(state)',
  'initLoginModal();',
  'initProfileModal();',
  'modal._reimuSetLoginPanel',
  'profileModal._reimuSetProfileOpen',
  "replaceElement('#reimu-login-modal', nextDoc",
  "replaceElement('#reimu-profile-modal', nextDoc"
]) {
  requireSnippet('PJAX login/profile modal state contract', snippet, files.frontend);
}

for (const snippet of [
  'function preserveAPlayer()',
  "player.dataset.reimuPreserve !== 'true'",
  'captureAPlayerState();',
  'function placeAPlayerInSlot(player, scope)',
  'function mountAPlayerInFlow()',
  "qsa('[data-reimu-aplayer-anchor], #reimu-aplayer-portal')",
  'function bindAPlayerState()',
  'audio.dataset.reimuStateReady',
  'function bindAPlayerLayoutSync()',
  'player.dataset.reimuPreserve = \'true\';',
  'player.dataset.reimuLayoutReady = \'true\';',
  'restoreAPlayerState();'
]) {
  requireSnippet('PJAX APlayer preservation contract', snippet, files.frontend);
}

for (const snippet of [
  'var searchRuntimePromise = null;',
  'var photoSwipeRuntimePromise = null;',
  'var shareRuntimePromise = null;',
  "script.id = 'yneko-reimu-search-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-search.js'",
  "script.id = 'yneko-reimu-photoswipe-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-photoswipe.js'",
  "script.id = 'yneko-reimu-share-runtime'",
  "script.src = getAssetBaseUrl() + 'reimu-share.js'",
  'searchRuntimePromise = null;',
  'photoSwipeRuntimePromise = null;',
  'shareRuntimePromise = null;'
]) {
  requireSnippet('lazy runtime loader contract', snippet, files.frontend);
}

for (const snippet of [
  'window.ReimuSearchRuntime = {',
  'init: function ()',
  'open: function ()',
  'var module = createRuntimeModule();',
  'module.initSearch();',
  'module.openSearch();',
  "if (root.dataset.searchLazyReady)",
  "root.dataset.searchLazyReady = 'true'",
  "document.addEventListener('click', openLazySearch)",
  "document.addEventListener('keydown', openLazySearch)",
  "document.documentElement.dataset.searchDelegated",
  "event.target.closest('.popup-trigger, #nav-search-btn')"
]) {
  requireSnippet('PJAX search runtime/rebind contract', snippet, `${files.frontend}\n${files.searchEntry}\n${files.searchModule}`);
}

for (const snippet of [
  'window.ReimuShareRuntime = {',
  'module.initShare();',
  "qsa('.share-wrapper').forEach(function (wrapper)",
  'wrapper.dataset.shareReady',
  'wrapper.dataset.shareReady = \'true\';',
  "document.documentElement.dataset.shareDelegated",
  "event.target.closest('.share-link-weixin')",
  "script.src = (assetBaseUrl || '') + 'qrcode.js'",
  "script.dataset.reimuQrcode = 'true'"
]) {
  requireSnippet('PJAX share/Weixin runtime contract', snippet, `${files.frontend}\n${files.shareEntry}\n${files.shareModule}`);
}

for (const snippet of [
  'window.ReimuPhotoSwipeRuntime = {',
  'module.init();',
  'module.destroy();',
  "qsa('.article-entry img').forEach(function (img)",
  'img.dataset.reimuPhotoswipeReady',
  "link.setAttribute('data-no-pjax', '')",
  'link.dataset.reimuPhotoswipeReady',
  'link.dataset.reimuPhotoswipeClickReady',
  'window.REIMU_PHOTOSWIPE = { destroy: closePhotoSwipeOverlay };',
  "document.body.classList.add('reimu-photoswipe-on')",
  "document.body.classList.remove('reimu-photoswipe-on')"
]) {
  requireSnippet('PJAX PhotoSwipe runtime contract', snippet, `${files.frontend}\n${files.photoswipeEntry}\n${files.photoswipeModule}`);
}

for (const snippet of [
  'function renderMermaidBlocks()',
  'container.dataset.mermaidReady',
  'div.dataset.mermaidReady = \'pending\';',
  "element.removeAttribute('data-processed')",
  'window.mermaid.run({ nodes: qsa(\'.mermaid\') })',
  "element.dataset.mermaidReady = 'true'",
  'if (config.katex && window.renderMathInElement)',
  'window.renderMathInElement(document.body'
]) {
  requireSnippet('PJAX content enhancer contract', snippet, files.frontend);
}

for (const snippet of [
  'function initCodeCopy()',
  'source.container.dataset.reimuCodeEditorReady',
  'function initYmlEditors()',
  'editor.dataset.ymlReady',
  'function initToc()',
  'button.replaceWith(button.cloneNode(true))',
  'link.replaceWith(link.cloneNode(true))',
  'function initWordPressCommentForm()',
  'form.dataset.ajaxCommentReady',
  'button.dataset.commentLikeReady',
  'button.dataset.commentEditReady',
  'button.dataset.commentDeleteReady',
  'function refreshCommentLoginState()'
]) {
  requireSnippet('PJAX code/comment rebind contract', snippet, files.frontend);
}

for (const snippet of [
  'assets/dist/reimu-search.css',
  'assets/dist/reimu-comments.css',
  'assets/dist/reimu-share.css',
  'assets/dist/reimu-code.css',
  'assets/dist/reimu-photoswipe.css',
  'assets/dist/reimu.js',
  'wp_add_inline_script( \'yneko-reimu-main\', \'window.REIMU_CONFIG=\'',
  "'pjax'",
  "'photoswipe'",
  "'mermaid'",
  "'katex'",
  "'aplayer'"
]) {
  requireSnippet('enqueue/config asset contract', snippet, files.enqueue);
}

for (const snippet of [
  "feature: 'search'",
  "status: 'lazy-runtime'",
  "feature: 'share'",
  "feature: 'photoswipe'",
  "feature: 'comments-profile'",
  "status: 'deferred'",
  "currentLoading: 'main-bundle'",
  'Deferred for v0.2.8 because login/profile/comment runtime code shares mutable config',
  "feature: 'aplayer'",
  "status: 'condition-loaded'",
  "feature: 'mermaid'",
  "feature: 'katex'"
]) {
  requireSnippet('feature loading plan contract', snippet, files.featurePlan);
}

for (const snippet of [
  "feature: 'share'",
  "currentLoading: 'global reimu-share.css plus lazy share JS'",
  'PJAX navigation into posts or virtual pages',
  "feature: 'search'",
  "currentLoading: 'global reimu-search.css plus lazy search JS'",
  "feature: 'comments-profile'",
  "currentLoading: 'global reimu-comments.css'"
]) {
  requireSnippet('PJAX stylesheet availability contract', snippet, files.cssPlan);
}

forbidSnippet('classic runtime compatibility', 'import.meta', `${files.frontend}\n${files.searchEntry}\n${files.shareEntry}\n${files.photoswipeEntry}`);
forbidSnippet('classic runtime compatibility', 'import(', `${files.frontend}\n${files.searchEntry}\n${files.shareEntry}\n${files.photoswipeEntry}`);

if (failures.length) {
  console.error('[pjax-runtime] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log('[pjax-runtime] PJAX, lazy runtime, stylesheet availability, modal, player, content, and comment rebind contracts are present.');
