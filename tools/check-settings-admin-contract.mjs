import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const pagePath = resolve(root, 'theme/Yneko-Reimu/inc/settings/page.php');
const panelsPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/panels.php');
const renderersPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers.php');

const page = await readFile(pagePath, 'utf8');
const panels = await readFile(panelsPath, 'utf8');
const renderers = await readFile(renderersPath, 'utf8');

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
  'id="yneko-reimu-admin-gif-upload-form"',
  'wp_nonce_field( \'yneko_reimu_admin_comment_gif_upload\' )',
  'yneko_reimu_admin_review_badge_counts()'
];

const requiredPanelSnippets = [
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
  'name="yneko_reimu_settings[user_badges][enabled]"',
  'name="yneko_reimu_settings[user_badges][review_enabled]"',
  'name="yneko_reimu_settings[user_badges][blocklist]"',
  'name="yneko_reimu_settings[features][',
  'name="yneko_reimu_settings[third_party][',
  'name="yneko_reimu_settings[external_comments][',
  'name="yneko_reimu_settings[friend_site][name]"',
  'yneko_reimu_sanitize_friend_items( $settings[\'friends\'] )',
  'name="yneko_reimu_settings[player][aplayer_enable]"',
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

if (failures.length) {
  console.error('[settings-admin] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[settings-admin] ${tabs.length} tabs, ${tabs.length} panels, and key settings/admin review contracts are present.`);
