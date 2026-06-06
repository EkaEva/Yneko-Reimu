import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const pagePath = resolve(root, 'theme/Yneko-Reimu/inc/settings/page.php');
const pageModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/context.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/tabs.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/general.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/submit.php')
];
const panelsPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/panels.php');
const panelModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/common.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/i18n.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/github.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/comments.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/search.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/friends.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/extensions.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/external-comments.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/users.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/security.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/music.php')
];
const renderersPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers.php');
const rendererModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers/admin-gif.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers/repeatable.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers/comment-upload.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers/user-avatar.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/renderers/user-badges.php')
];
const adminPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/admin.php');
const adminModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/admin/menu.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/admin/ui.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/admin/totp.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/admin/review-counts.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/admin/assets.php')
];
const adminJsPath = resolve(root, 'theme/Yneko-Reimu/assets/src/admin-settings.js');
const login2faPath = resolve(root, 'theme/Yneko-Reimu/inc/github-login/login-2fa.php');
const schemaPaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/defaults.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/normalizers.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/sanitizers.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/sanitizers/media.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/sanitizers/users.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/sanitizers/groups.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/getters.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/schema/compat.php')
];
const securityPath = resolve(root, 'theme/Yneko-Reimu/inc/security-auth-mail.php');

const pageEntry = await readFile(pagePath, 'utf8');
const pageModules = await Promise.all(pageModulePaths.map((path) => readFile(path, 'utf8')));
const page = [pageEntry, ...pageModules].join('\n');
const panelsEntry = await readFile(panelsPath, 'utf8');
const panelModules = await Promise.all(panelModulePaths.map((path) => readFile(path, 'utf8')));
const panels = [panelsEntry, ...panelModules].join('\n');
const renderersEntry = await readFile(renderersPath, 'utf8');
const rendererModules = await Promise.all(rendererModulePaths.map((path) => readFile(path, 'utf8')));
const renderers = [renderersEntry, ...rendererModules].join('\n');
const adminEntry = await readFile(adminPath, 'utf8');
const adminModules = await Promise.all(adminModulePaths.map((path) => readFile(path, 'utf8')));
const admin = [adminEntry, ...adminModules].join('\n');
const adminJs = await readFile(adminJsPath, 'utf8');
const login2fa = await readFile(login2faPath, 'utf8');
const schema = (await Promise.all(schemaPaths.map((path) => readFile(path, 'utf8')))).join('\n');
const security = await readFile(securityPath, 'utf8');

const tabs = [
  'general',
  'github',
  'i18n',
  'comments',
  'users',
  'security',
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
  ['security', 'yneko_reimu_render_settings_security_panel'],
  ['search', 'yneko_reimu_render_settings_search_panel'],
  ['extensions', 'yneko_reimu_render_settings_extensions_panel'],
  ['external-comments', 'yneko_reimu_render_settings_external_comments_panel'],
  ['friends', 'yneko_reimu_render_settings_friends_panel'],
  ['music', 'yneko_reimu_render_settings_music_panel']
]);

const requiredPageSnippets = [
  "require_once YNEKO_REIMU_DIR . '/inc/settings/page/context.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/page/tabs.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/page/general.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/page/submit.php';",
  'function yneko_reimu_render_settings_page',
  'function yneko_reimu_settings_page_context',
  'function yneko_reimu_render_settings_nav_tabs',
  'function yneko_reimu_render_settings_general_panel',
  'function yneko_reimu_render_settings_floating_submit',
  'function yneko_reimu_render_settings_hidden_upload_form',
  'settings_fields( \'yneko_reimu_settings\' )',
  'data-yneko-settings-panel="general"',
  'data-yneko-settings-tab="security"',
  'yneko_reimu_settings_group_open( \'管理员体验\', \'Administrator experience\'',
  'name="yneko_reimu_settings[features][show_admin_toolbar]"',
  'yneko_reimu_settings_group_open( \'账号安全\', \'Account security\'',
  'data-yneko-admin-totp',
  'data-yneko-admin-totp-generate',
  'data-yneko-admin-totp-toggle',
  'data-yneko-admin-totp-recovery',
  'data-yneko-admin-totp-recovery-generate',
  'data-yneko-admin-totp-recovery-copy',
  "'security'             => function_exists( 'yneko_reimu_settings_security' ) ? yneko_reimu_settings_security() : array(),",
  "yneko_reimu_render_settings_security_panel( $context['auth_security'], $context['security'], $context['review_badges'] )",
  'id="yneko-reimu-admin-gif-upload-form"',
  'wp_nonce_field( \'yneko_reimu_admin_comment_gif_upload\' )',
  'yneko_reimu_admin_review_badge_counts()'
];

const requiredPanelSnippets = [
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/common.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/github.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/comments.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/extensions.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/external-comments.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/users.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/security.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/panels/music.php';",
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
  'name="yneko_reimu_settings[auth_security][enabled]"',
  'name="yneko_reimu_settings[auth_security][protect_ajax]"',
  'name="yneko_reimu_settings[auth_security][protect_wp_login]"',
  'name="yneko_reimu_settings[auth_security][email_hour_limit]"',
  'name="yneko_reimu_settings[auth_security][email_day_limit]"',
  'name="yneko_reimu_settings[auth_security][ip_hour_limit]"',
  'name="yneko_reimu_settings[auth_security][ip_day_limit]"',
  'name="yneko_reimu_settings[auth_security][device_hour_limit]"',
  'name="yneko_reimu_settings[auth_security][device_day_limit]"',
  'name="yneko_reimu_settings[auth_security][global_day_limit]"',
  'name="yneko_reimu_settings[auth_security][cooldown_seconds]"',
  'name="yneko_reimu_settings[auth_security][global_warning_threshold]"',
  'name="yneko_reimu_settings[auth_security][email_alert_enabled]"',
  'yneko_reimu_settings_group_open( \'媒体与隐私\', \'Media and privacy\'',
  'name="yneko_reimu_settings[security][allow_svg_uploads]"',
  'name="yneko_reimu_settings[security][comment_ip_region_lookup]"',
  'yneko_reimu_auth_security_events()',
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
  "require_once YNEKO_REIMU_DIR . '/inc/settings/renderers/admin-gif.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/renderers/repeatable.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/renderers/comment-upload.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/renderers/user-avatar.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/renderers/user-badges.php';",
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
  '.yneko-reimu-admin-totp-recovery{display:flex;flex-direction:column;gap:10px',
  '.yneko-reimu-admin-totp-recovery__codes',
  '.yneko-reimu-admin-totp-status.is-enabled',
  '.yneko-reimu-security-alert-card.is-unhandled',
  '.yneko-reimu-security-alert-list',
  '.yneko-reimu-security-alert-actions'
];

const requiredAdminPhpSnippets = [
  "require_once YNEKO_REIMU_DIR . '/inc/settings/admin/menu.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/admin/ui.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/admin/totp.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/admin/review-counts.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/admin/assets.php';",
  'function yneko_reimu_admin_current_user_totp_payload',
  'function yneko_reimu_ajax_admin_totp_generate',
  'function yneko_reimu_ajax_admin_totp_enable',
  'function yneko_reimu_ajax_admin_totp_recovery_generate',
  'function yneko_reimu_ajax_admin_totp_disable',
  'wp_ajax_yneko_reimu_admin_totp_generate',
  'wp_ajax_yneko_reimu_admin_totp_enable',
  'wp_ajax_yneko_reimu_admin_totp_recovery_generate',
  'wp_ajax_yneko_reimu_admin_totp_disable',
  'check_ajax_referer( \'yneko_reimu_admin_totp\', \'nonce\' )',
  'current_user_can( \'manage_options\' )',
  '_yneko_reimu_totp_pending_secret',
  '_yneko_reimu_totp_secret',
  '_yneko_reimu_totp_enabled',
  '_yneko_reimu_totp_recovery_codes'
];

const requiredSchemaSnippets = [
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/defaults.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/normalizers.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/media.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/users.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/groups.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/getters.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/compat.php';",
  "'auth_security'",
  "'security'          => array(",
  "'allow_svg_uploads'        => '1'",
  "'comment_ip_region_lookup' => '1'",
  "'email_hour_limit'         => 3",
  "'email_day_limit'          => 8",
  "'ip_hour_limit'            => 10",
  "'ip_day_limit'             => 30",
  "'device_hour_limit'        => 5",
  "'device_day_limit'         => 15",
  "'global_day_limit'         => 100",
  "'cooldown_seconds'         => 60",
  "'global_warning_threshold' => 80",
  'function yneko_reimu_sanitize_settings',
  'function yneko_reimu_sanitize_comment_upload_settings',
  'function yneko_reimu_sanitize_github_oauth_settings',
  'function yneko_reimu_sanitize_i18n_settings',
  'function yneko_reimu_sanitize_search_settings',
  'function yneko_reimu_sanitize_player_settings',
  'function yneko_reimu_sanitize_third_party_settings',
  'function yneko_reimu_sanitize_external_comments_settings',
  "yneko_reimu_sanitize_auth_security_settings( yneko_reimu_settings_group_input( $input, 'auth_security' ), $defaults['auth_security'] )",
  'yneko_reimu_sanitize_settings_bool_group(',
  "array( 'allow_svg_uploads', 'comment_ip_region_lookup' )",
  'function yneko_reimu_settings_security',
  'function yneko_reimu_security_allow_svg_uploads',
  'function yneko_reimu_security_comment_ip_region_lookup'
];

const requiredSecuritySnippets = [
  'function yneko_reimu_auth_security_defaults',
  'function yneko_reimu_auth_security_unhandled_count',
  'function yneko_reimu_auth_security_admin_action',
  'yneko_reimu_auth_security_events',
  "'mark_handled' === $action",
  "'clear' === $action"
];

const requiredAdminJsSnippets = [
  'function initAdminTotp()',
  'function postAdminTotp(root, action, extra)',
  'function loadQrCode(src)',
  'data-yneko-admin-totp',
  'yneko_reimu_admin_totp_generate',
  'yneko_reimu_admin_totp_enable',
  'yneko_reimu_admin_totp_recovery_generate',
  'yneko_reimu_admin_totp_disable',
  'data-yneko-admin-totp-toggle',
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
  const tabCount = countOccurrences(page, tabNeedle)
    + (page.includes('data-yneko-settings-tab="<?php echo esc_attr( $slug ); ?>"') && page.includes(`array( '${tab}',`) ? 1 : 0);
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
  const tabFallback = snippet.match(/^data-yneko-settings-tab="([^"]+)"$/);
  if (tabFallback && page.includes('data-yneko-settings-tab="<?php echo esc_attr( $slug ); ?>"') && page.includes(`array( '${tabFallback[1]}',`)) {
    continue;
  }
  if (!page.includes(snippet)) {
    failures.push(`Missing required settings page snippet: ${snippet}`);
  }
}

for (const snippet of requiredPanelSnippets) {
  const fallback = snippet.match(/^name="yneko_reimu_settings\[auth_security\]\[([^\]]+)\]"$/);
  if (fallback && panels.includes(`array( '${fallback[1]}',`)) {
    continue;
  }
  const commentUploadFallback = snippet.match(/^name="yneko_reimu_settings\[comment_upload\]\[(image|gif)_enabled\]"$/);
  if (commentUploadFallback && panels.includes("name=\"yneko_reimu_settings[comment_upload][<?php echo esc_attr( $type ); ?>_enabled]\"") && panels.includes(`yneko_reimu_render_settings_comment_upload_group( '${'image' === commentUploadFallback[1] ? '图片上传' : 'GIF 上传'}`)) {
    continue;
  }
  const friendSiteFallback = snippet.match(/^name="yneko_reimu_settings\[friend_site\]\[([^\]]+)\]"$/);
  if (friendSiteFallback && panels.includes("name=\"yneko_reimu_settings[friend_site][<?php echo esc_attr( $key ); ?>]\"") && panels.includes(`'${friendSiteFallback[1]}'`)) {
    continue;
  }
  if (!panels.includes(snippet)) {
    failures.push(`Missing required settings panel snippet: ${snippet}`);
  }
}

for (const snippet of requiredRendererSnippets) {
  const repeatableFallback = snippet.match(/^name="yneko_reimu_settings\[(friends|music)\]\[<\?php echo esc_attr\( \$index \); \?>\]\[([^\]]+)\]"$/);
  if (repeatableFallback && renderers.includes("name=\"yneko_reimu_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]\"") && renderers.includes(`'${repeatableFallback[1]}', $index, '${repeatableFallback[2]}'`)) {
    continue;
  }
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
  if (!`${admin}\n${login2fa}`.includes(snippet)) {
    failures.push(`Missing required settings admin PHP snippet: ${snippet}`);
  }
}

for (const snippet of requiredSchemaSnippets) {
  if (!schema.includes(snippet)) {
    failures.push(`Missing required auth-security schema snippet: ${snippet}`);
  }
}

for (const snippet of requiredSecuritySnippets) {
  if (!security.includes(snippet)) {
    failures.push(`Missing required auth-security helper snippet: ${snippet}`);
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

console.log(`[settings-admin] ${tabs.length} tabs, ${tabs.length} panels, and key settings/admin review/security contracts are present.`);
