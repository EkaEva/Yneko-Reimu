import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const files = {
  enqueue: await readFile(resolve(themeRoot, 'inc/enqueue.php'), 'utf8'),
  comments: await readFile(resolve(themeRoot, 'inc/comments.php'), 'utf8'),
  uploads: await readFile(resolve(themeRoot, 'inc/comments/uploads.php'), 'utf8'),
  modals: await readFile(resolve(themeRoot, 'inc/comments/modals.php'), 'utf8'),
  auth: await readFile(resolve(themeRoot, 'inc/comments/auth.php'), 'utf8'),
  profile: await readFile(resolve(themeRoot, 'inc/comments/profile.php'), 'utf8'),
  mutations: await readFile(resolve(themeRoot, 'inc/comments/mutations.php'), 'utf8'),
  rendering: await readFile(resolve(themeRoot, 'inc/comments/rendering.php'), 'utf8'),
  frontend: await readFile(resolve(themeRoot, 'assets/src/reimu.js'), 'utf8'),
  commentMedia: await readFile(resolve(themeRoot, 'assets/src/reimu/comment-media.js'), 'utf8'),
  commentTools: await readFile(resolve(themeRoot, 'assets/src/reimu/comment-tools.js'), 'utf8'),
  commentList: await readFile(resolve(themeRoot, 'assets/src/reimu/comment-list.js'), 'utf8'),
  profileForm: await readFile(resolve(themeRoot, 'assets/src/reimu/profile-form.js'), 'utf8'),
  profileStatus: await readFile(resolve(themeRoot, 'assets/src/reimu/profile-status.js'), 'utf8'),
  adapterCss: await readFile(resolve(themeRoot, 'assets/src/yneko-reimu-adapter.css'), 'utf8'),
  commentsCss: await readFile(resolve(themeRoot, 'assets/src/reimu-comments.css'), 'utf8')
};

const source = Object.values(files).join('\n');
let failed = false;

function fail(message) {
  console.error(`[comments-profile] ${message}`);
  failed = true;
}

function requireSnippet(label, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    fail(`Missing ${label}: ${snippet}`);
  }
}

function requirePair(label, left, right, haystack = source) {
  requireSnippet(`${label} source`, left, haystack);
  requireSnippet(`${label} target`, right, haystack);
}

for (const moduleImport of [
  "import { createCommentList } from './reimu/comment-list.js';",
  "import { createCommentMedia } from './reimu/comment-media.js';",
  "import { createCommentTools } from './reimu/comment-tools.js';",
  "import { createProfileFormUi } from './reimu/profile-form.js';",
  "import { createProfileStatusUi } from './reimu/profile-status.js';"
]) {
  requireSnippet('source module boundary', moduleImport, files.frontend);
}

for (const phpModule of [
  "require_once YNEKO_REIMU_DIR . '/inc/comments/uploads.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/comments/modals.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/comments/auth.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/comments/profile.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/comments/mutations.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/comments/rendering.php';"
]) {
  requireSnippet('comments PHP module boundary', phpModule, files.comments);
}

for (const globalContract of [
  'window.REIMU_CONFIG',
  'window.ReimuWP',
  'init: init'
]) {
  requireSnippet('front-end global contract', globalContract, files.frontend);
}

for (const configKey of [
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
  "'enabled'",
  "'imageEnabled'",
  "'gifEnabled'",
  "'isLoggedIn'",
  "'gifs'",
  "'comments'"
]) {
  requireSnippet('REIMU_CONFIG comments/profile key', configKey, files.enqueue);
}

const ajaxContracts = [
  ['yneko_reimu_login_state', 'yneko_reimu_ajax_login_state', 'both'],
  ['yneko_reimu_logout', 'yneko_reimu_ajax_logout', 'auth'],
  ['yneko_reimu_login', 'yneko_reimu_ajax_login', 'guest'],
  ['yneko_reimu_profile_get', 'yneko_reimu_ajax_profile_get', 'auth'],
  ['yneko_reimu_profile_status_ack', 'yneko_reimu_ajax_profile_status_ack', 'auth'],
  ['yneko_reimu_profile_email_code', 'yneko_reimu_ajax_profile_email_code', 'auth'],
  ['yneko_reimu_profile_totp_generate', 'yneko_reimu_ajax_profile_totp_generate', 'auth'],
  ['yneko_reimu_profile_avatar_upload', 'yneko_reimu_ajax_profile_avatar_upload', 'auth'],
  ['yneko_reimu_profile_save', 'yneko_reimu_ajax_profile_save', 'auth'],
  ['yneko_reimu_register_code', 'yneko_reimu_ajax_send_register_code', 'guest'],
  ['yneko_reimu_register', 'yneko_reimu_ajax_register', 'guest'],
  ['yneko_reimu_lostpassword_code', 'yneko_reimu_ajax_send_lostpassword_code', 'guest'],
  ['yneko_reimu_lostpassword', 'yneko_reimu_ajax_lostpassword', 'guest'],
  ['yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like', 'both'],
  ['yneko_reimu_edit_comment', 'yneko_reimu_ajax_edit_comment', 'auth'],
  ['yneko_reimu_delete_comment', 'yneko_reimu_ajax_delete_comment', 'auth'],
  ['yneko_reimu_submit_comment', 'yneko_reimu_ajax_submit_comment', 'both'],
  ['yneko_reimu_comment_upload', 'yneko_reimu_ajax_comment_upload', 'auth-upload'],
  ['yneko_reimu_comment_upload_discard', 'yneko_reimu_ajax_comment_upload_discard', 'auth-upload']
];

function phpSourceForAction(action, exposure) {
  if (exposure === 'auth-upload') {
    return files.uploads;
  }
  if ([
    'yneko_reimu_login_state',
    'yneko_reimu_logout',
    'yneko_reimu_login',
    'yneko_reimu_register_code',
    'yneko_reimu_register',
    'yneko_reimu_lostpassword_code',
    'yneko_reimu_lostpassword'
  ].includes(action)) {
    return files.auth;
  }
  if ([
    'yneko_reimu_profile_get',
    'yneko_reimu_profile_status_ack',
    'yneko_reimu_profile_email_code',
    'yneko_reimu_profile_totp_generate',
    'yneko_reimu_profile_avatar_upload',
    'yneko_reimu_profile_save'
  ].includes(action)) {
    return files.profile;
  }
  if ([
    'yneko_reimu_comment_like',
    'yneko_reimu_edit_comment',
    'yneko_reimu_delete_comment',
    'yneko_reimu_submit_comment'
  ].includes(action)) {
    return files.mutations;
  }
  return files.comments;
}

for (const [action, callback, exposure] of ajaxContracts) {
  const phpSource = phpSourceForAction(action, exposure);
  requireSnippet(`${action} PHP function`, `function ${callback}`, phpSource);
  if (action === 'yneko_reimu_profile_avatar_upload') {
    requireSnippet(`${action} profile-save file fallback`, "yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file']", files.profile);
  } else {
    requireSnippet(`${action} front-end action`, `'${action}'`, source);
  }

  if (exposure === 'both') {
    requireSnippet(`${action} authenticated AJAX hook`, `add_action( 'wp_ajax_${action}', '${callback}' )`, phpSource);
    requireSnippet(`${action} guest AJAX hook`, `add_action( 'wp_ajax_nopriv_${action}', '${callback}' )`, phpSource);
  } else if (exposure === 'guest') {
    requireSnippet(`${action} guest AJAX hook`, `add_action( 'wp_ajax_nopriv_${action}', '${callback}' )`, phpSource);
  } else {
    requireSnippet(`${action} authenticated AJAX hook`, `add_action( 'wp_ajax_${action}', '${callback}' )`, phpSource);
  }
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
  requireSnippet('nonce created for front end', `wp_create_nonce( '${nonce}' )`, source);
  requireSnippet('nonce verified by handler', `check_ajax_referer( '${nonce}', 'nonce'`, source);
}

for (const dynamicNonce of [
  'yneko_reimu_comment_like_',
  'yneko_reimu_comment_manage_'
]) {
  requireSnippet('dynamic nonce created in comment markup', `wp_create_nonce( '${dynamicNonce}`, files.rendering);
  requireSnippet('dynamic nonce verified in handler', `check_ajax_referer( '${dynamicNonce}`, files.mutations);
}

for (const renderingContract of [
  'function yneko_reimu_comment_callback',
  'function yneko_reimu_render_comment_markdown',
  'function yneko_reimu_get_comment_avatar',
  'function yneko_reimu_external_comment_systems',
  'function yneko_reimu_render_external_comment_panel',
  "add_filter( 'comment_form_fields', 'yneko_reimu_comment_field_order' )",
  'data-comment-raw',
  'data-comment-like',
  'data-comment-manage-nonce',
  'data-like-nonce',
  'giscus-comment',
  'utterances-comment',
  'disqus_thread',
  'waline-comment',
  'twikoo-comment',
  'valine-comment'
]) {
  requireSnippet('comments rendering contract', renderingContract, files.rendering);
}

for (const payloadKey of [
  "'loggedIn'",
  "'loginHtml'",
  "'guestFieldsHtml'",
  "'loginModal'",
  "'profileModal'",
  "'commentNonce'",
  "'commentUploadNonce'",
  "'profileNonce'",
  "'logoutNonce'",
  "'profile'"
]) {
  requireSnippet('login-state refresh payload key', payloadKey, files.auth);
}

for (const selector of [
  '.reimu-confirm-modal',
  '[data-reimu-confirm-ok]',
  '[data-reimu-confirm-cancel]',
  '#reimu-login-modal',
  '[data-reimu-login-form]',
  '[data-reimu-register-form]',
  '[data-reimu-lost-form]',
  '[data-login-2fa]',
  '[name="two_factor_code"]',
  '[data-register-code-send]',
  '[data-lost-code-send]',
  '#reimu-profile-modal',
  '[data-reimu-profile-form]',
  '[data-profile-avatar-preview]',
  '[data-profile-avatar-upload]',
  '[data-profile-avatar-file]',
  '[data-profile-avatar-changed]',
  '[data-profile-tag-list]',
  '[data-profile-add-tag]',
  '[name="totp_enabled"]',
  '[data-comment-upload-row]',
  '[data-comment-upload-login]',
  '[data-comment-upload-button]',
  '[data-comment-upload-input]',
  '[data-comment-upload-status]',
  '[data-comment-preview-panel]',
  '#reimu-comment-list',
  '[data-comment-like]',
  '[data-comment-edit]',
  '[data-comment-delete]',
  'data-comment-manage-nonce',
  'data-like-nonce',
  '[data-reimu-ajax-logout]',
  '[data-reimu-profile-open]',
  '[data-profile-inline-status-list]'
]) {
  requireSnippet('DOM selector contract', selector);
}

for (const formField of [
  "formData.append('action', 'yneko_reimu_submit_comment')",
  "formData.append('nonce', config.comments.nonce || '')",
  "formData.append('action', 'yneko_reimu_comment_upload')",
  "formData.append('nonce', state.uploads.nonce || '')",
  "formData.append('action', 'yneko_reimu_comment_upload_discard')",
  "formData.append('cleanup_key', item.cleanupKey || '')",
  "formData.append('action', 'yneko_reimu_comment_like')",
  "formData.append('nonce', button.getAttribute('data-like-nonce') || '')",
  "formData.append('action', 'yneko_reimu_edit_comment')",
  "formData.append('action', 'yneko_reimu_delete_comment')",
  "formData.append('nonce', button.getAttribute('data-comment-manage-nonce') || '')"
]) {
  requireSnippet('front-end form payload contract', formField);
}

for (const functionName of [
  'requestThemeConfirm',
  'refreshCommentLoginState',
  'applyCommentLoggedInState',
  'applyCommentLoggedOutState',
  'startProfileStatusPolling',
  'postProfileAction',
  'ackProfileStatuses',
  'initWordPressCommentForm',
  'initCommentUploadRows',
  'initCommentOwnerActions',
  'initCommentLikes'
]) {
  requireSnippet('front-end comments/profile flow function', `function ${functionName}`, files.frontend);
}

for (const moduleExport of [
  'export function createCommentMedia',
  'export function createCommentTools',
  'export function createCommentList',
  'export function createProfileFormUi',
  'export function createProfileStatusUi'
]) {
  requireSnippet('comments/profile source module export', moduleExport);
}

for (const cssSelector of [
  '.reimu-confirm-modal',
  '.reimu-confirm-modal__dialog',
  '.reimu-confirm-modal__ok',
  '.reimu-login-modal',
  '.reimu-profile-modal',
  '.reimu-profile-form',
  '.reimu-profile-tag-row',
  '#comments',
  '.reimu-comment-form',
  '.reimu-comment-toolbar',
  '.reimu-comment-popover',
  '.reimu-comment-upload-row',
  '.reimu-comment-preview-panel',
  '.reimu-comment-preview-content',
  '.comment-text img',
  '.reimu-comment-current-user',
  '.reimu-comment-current-user__statuses',
  '.reimu-comment-like',
  '.reimu-comment-edit-form',
  '.reimu-comment-pending'
]) {
  requireSnippet('comments/profile CSS selector', cssSelector, `${files.adapterCss}\n${files.commentsCss}`);
}

requirePair('comment upload endpoint and nonce', "formData.append('action', 'yneko_reimu_comment_upload')", "check_ajax_referer( 'yneko_reimu_comment_upload', 'nonce' )", source);
requirePair('comment submit endpoint and nonce', "formData.append('action', 'yneko_reimu_submit_comment')", "check_ajax_referer( 'yneko_reimu_submit_comment', 'nonce' )", source);
requirePair('profile save endpoint and nonce', "postProfileAction('yneko_reimu_profile_save'", "check_ajax_referer( 'yneko_reimu_profile', 'nonce' )", source);

if (files.frontend.includes('window.confirm')) {
  fail('Front-end comments runtime should use the theme confirm dialog instead of window.confirm.');
}
if (files.commentMedia.includes('window.confirm')) {
  fail('Comment media replacement should use the injected theme confirm dialog instead of window.confirm.');
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[comments-profile] AJAX actions, nonces, config keys, DOM selectors, and CSS anchors are present.');
}
