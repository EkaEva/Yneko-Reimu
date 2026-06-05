import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const pagePath = resolve(root, 'theme/Yneko-Reimu/inc/settings/page.php');
const panelsPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/panels.php');
const renderersPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers.php');
const adminPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/admin.php');
const adminJsPath = resolve(root, 'theme/Yneko-Reimu/assets/src/admin-settings.js');

const page = await readFile(pagePath, 'utf8');
const panels = await readFile(panelsPath, 'utf8');
const renderers = await readFile(renderersPath, 'utf8');
const admin = await readFile(adminPath, 'utf8');
const adminJs = await readFile(adminJsPath, 'utf8');

const tabs = [
  'general',
  'github',
  'i18n',
  'comments',
  'users',
  'search',
  'extensions',
  'external-comments',
  'friends',
  'music'
];

const panelFunctions = new Map([
  ['github', 'yneko_reimu_render_settings_github_panel'],
  ['i18n', 'yneko_reimu_render_settings_i18n_panel'],
  ['comments', 'yneko_reimu_render_settings_comments_panel'],
  ['users', 'yneko_reimu_render_settings_users_panel'],
  ['search', 'yneko_reimu_render_settings_search_panel'],
  ['extensions', 'yneko_reimu_render_settings_extensions_panel'],
  ['external-comments', 'yneko_reimu_render_settings_external_comments_panel'],
  ['friends', 'yneko_reimu_render_settings_friends_panel'],
  ['music', 'yneko_reimu_render_settings_music_panel']
]);

const requiredPageSnippets = [
  'settings_fields( \'yneko_reimu_settings\' )',
  'data-yneko-settings-panel="general"',
  'yneko_reimu_settings_group_open( \'管理员体验\', \'Administrator experience\'',
  'name="yneko_reimu_settings[features][show_admin_toolbar]"',
  'yneko_reimu_settings_group_open( \'账号安全\', \'Account security\'',
  'data-yneko-admin-totp',
  'data-yneko-admin-totp-generate',
  'data-yneko-admin-totp-enable',
  'data-yneko-admin-totp-disable',
  'id="yneko-reimu-admin-gif-upload-form"',
  'wp_nonce_field( \'yneko_reimu_admin_comment_gif_upload\' )',
  'yneko_reimu_admin_review_badge_counts()'
];

const requiredPanelSnippets = [
  'function yneko_reimu_settings_group_open',
  'function yneko_reimu_settings_field_open',
  'class="yneko-reimu-settings-group"',
  'class="yneko-reimu-checkbox-grid"',
  'class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3"',
  'class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-2"',
  'name="yneko_reimu_settings[github_oauth][callback_url]"',
  'name="yneko_reimu_settings[github_oauth][client_id]"',
  'name="yneko_reimu_settings[github_oauth][client_secret]"',
  'name="yneko_reimu_settings[i18n][enabled]"',
  'name="yneko_reimu_settings[i18n][default]"',
  'name="yneko_reimu_settings[search][algolia_enable]"',
  'name="yneko_reimu_settings[search][index_full_content]"',
  'yneko_reimu_settings[comment_avatar_url]',
  'name="yneko_reimu_settings[comment_upload][image_enabled]"',
  'name="yneko_reimu_settings[comment_upload][gif_enabled]"',
  'name="yneko_reimu_settings[comment_upload][avatar_enabled]"',
  'name="yneko_reimu_settings[comment_upload][avatar_max_mb]"',
  'name="yneko_reimu_settings[user_badges][enabled]"',
  'name="yneko_reimu_settings[user_badges][review_enabled]"',
  'name="yneko_reimu_settings[user_badges][blocklist]"',
  'name="yneko_reimu_settings[features][',
  'name="yneko_reimu_settings[third_party][',
  'name="yneko_reimu_settings[external_comments][',
  'name="yneko_reimu_settings[friend_site][name]"',
  'yneko_reimu_sanitize_friend_items( $settings[\'friends\'] )',
  'name="yneko_reimu_settings[player][aplayer_enable]"',
  'name="yneko_reimu_settings[player][meting_auto]"',
  'yneko_reimu_sanitize_music_items( $settings[\'music\'] )',
  'yneko_reimu_render_admin_comment_gif_upload()',
  'yneko_reimu_render_comment_upload_admin()',
  'yneko_reimu_render_user_badge_admin()',
  'yneko_reimu_render_user_avatar_admin()',
  'yneko_reimu_admin_badge( $review_badges[\'user_badges\'] ?? 0 )',
  'yneko_reimu_admin_badge( $review_badges[\'avatars\'] ?? 0 )'
];

const requiredRendererSnippets = [
  'name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][name]"',
  'name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][url]"',
  'name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][image]"',
  'name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][name]"',
  'name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][url]"',
  'name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][cover]"'
];

const requiredAdminStyleSnippets = [
  '.yneko-reimu-checkbox-line{display:inline-flex',
  'white-space:nowrap',
  '.yneko-reimu-checkbox-line .yneko-reimu-admin-text{display:inline',
  '.yneko-reimu-settings-subgrid label.yneko-reimu-checkbox-line{display:inline-flex',
  '.yneko-reimu-special-badge-table{max-width:100%;box-sizing:border-box}',
  '.yneko-reimu-special-badge-row{display:grid;grid-template-columns:110px minmax(0,1fr) minmax(0,1fr) minmax(0,1.35fr)',
  '.yneko-reimu-special-badge-row{grid-template-columns:120px minmax(0,1fr) minmax(0,1fr)}',
  '.yneko-reimu-special-badge-row .yneko-reimu-media-field{grid-column:2/4}',
  '.yneko-reimu-special-badge-row .yneko-reimu-media-field,.yneko-reimu-special-badge-row .yneko-reimu-inline-media{min-width:0;width:100%;max-width:100%;box-sizing:border-box}',
  '.yneko-reimu-media-field input,.yneko-reimu-inline-media input{flex:1 1 auto;min-width:0;max-width:100%}',
  '.yneko-reimu-admin-totp{display:flex;flex-direction:column;gap:12px',
  '.yneko-reimu-admin-totp-setup{display:grid;grid-template-columns:150px minmax(0,1fr)',
  '.yneko-reimu-admin-totp-status.is-enabled'
];

const requiredAdminPhpSnippets = [
  'function yneko_reimu_admin_current_user_totp_payload',
  'function yneko_reimu_ajax_admin_totp_generate',
  'function yneko_reimu_ajax_admin_totp_enable',
  'function yneko_reimu_ajax_admin_totp_disable',
  'wp_ajax_yneko_reimu_admin_totp_generate',
  'wp_ajax_yneko_reimu_admin_totp_enable',
  'wp_ajax_yneko_reimu_admin_totp_disable',
  'check_ajax_referer( \'yneko_reimu_admin_totp\', \'nonce\' )',
  'current_user_can( \'manage_options\' )',
  '_yneko_reimu_totp_pending_secret',
  '_yneko_reimu_totp_secret',
  '_yneko_reimu_totp_enabled'
];

const requiredAdminJsSnippets = [
  'function initAdminTotp()',
  'function postAdminTotp(root, action, extra)',
  'function loadQrCode(src)',
  'data-yneko-admin-totp',
  'yneko_reimu_admin_totp_generate',
  'yneko_reimu_admin_totp_enable',
  'yneko_reimu_admin_totp_disable',
  'data-yneko-admin-qrcode',
  'QRCode.toDataURL',
  'initAdminTotp();'
];

function countOccurrences(haystack, needle) {
  return haystack.split(needle).length - 1;
}

const failures = [];

for (const tab of tabs) {
  const tabNeedle = `data-yneko-settings-tab="${tab}"`;
  const panelNeedle = `data-yneko-settings-panel="${tab}"`;
  const tabCount = countOccurrences(page, tabNeedle);
  const panelCount = countOccurrences(tab === 'general' ? page : panels, panelNeedle);

  if (tabCount !== 1) {
    failures.push(`Expected exactly one settings tab ${tabNeedle}; found ${tabCount}.`);
  }

  if (panelCount !== 1) {
    failures.push(`Expected exactly one settings panel ${panelNeedle}; found ${panelCount}.`);
  }
}

for (const [panel, functionName] of panelFunctions) {
  const functionDeclaration = `function ${functionName}`;
  const functionCall = `${functionName}(`;
  const declarationCount = countOccurrences(panels, functionDeclaration);
  const pageCallCount = countOccurrences(page, functionCall);

  if (declarationCount !== 1) {
    failures.push(`Expected one ${functionName} declaration for ${panel}; found ${declarationCount}.`);
  }

  if (pageCallCount !== 1) {
    failures.push(`Expected one ${functionName} call from settings page for ${panel}; found ${pageCallCount}.`);
  }
}

for (const snippet of requiredPageSnippets) {
  if (!page.includes(snippet)) {
    failures.push(`Missing required settings page snippet: ${snippet}`);
  }
}

for (const snippet of requiredPanelSnippets) {
  if (!panels.includes(snippet)) {
    failures.push(`Missing required settings panel snippet: ${snippet}`);
  }
}

for (const snippet of requiredRendererSnippets) {
  if (!renderers.includes(snippet)) {
    failures.push(`Missing required settings renderer snippet: ${snippet}`);
  }
}

for (const snippet of requiredAdminStyleSnippets) {
  if (!admin.includes(snippet)) {
    failures.push(`Missing required settings admin style snippet: ${snippet}`);
  }
}

for (const snippet of requiredAdminPhpSnippets) {
  if (!admin.includes(snippet)) {
    failures.push(`Missing required settings admin PHP snippet: ${snippet}`);
  }
}

for (const snippet of requiredAdminJsSnippets) {
  if (!adminJs.includes(snippet)) {
    failures.push(`Missing required settings admin JS snippet: ${snippet}`);
  }
}

const extensionsStart = panels.indexOf('function yneko_reimu_render_settings_extensions_panel');
const externalCommentsStart = panels.indexOf('function yneko_reimu_render_settings_external_comments_panel');
if (-1 === extensionsStart || -1 === externalCommentsStart || externalCommentsStart <= extensionsStart) {
  failures.push('Could not locate the Extensions panel boundaries.');
} else {
  const extensionsPanel = panels.slice(extensionsStart, externalCommentsStart);
  if (extensionsPanel.includes('show_admin_toolbar')) {
    failures.push('show_admin_toolbar must stay in the General panel, not the Extensions feature loop.');
  }
}

if (failures.length) {
  console.error('[settings-admin] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[settings-admin] ${tabs.length} tabs, ${tabs.length} panels, and key settings/admin review contracts are present.`);
