import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const githubPath = resolve(root, 'theme/Yneko-Reimu/inc/github-login.php');
const githubModulePaths = {
  settings: resolve(root, 'theme/Yneko-Reimu/inc/github-login/settings.php'),
  rendering: resolve(root, 'theme/Yneko-Reimu/inc/github-login/rendering.php'),
  styles: resolve(root, 'theme/Yneko-Reimu/inc/github-login/styles.php'),
  oauth: resolve(root, 'theme/Yneko-Reimu/inc/github-login/oauth.php'),
  users: resolve(root, 'theme/Yneko-Reimu/inc/github-login/users.php'),
  avatars: resolve(root, 'theme/Yneko-Reimu/inc/github-login/avatars.php'),
  access: resolve(root, 'theme/Yneko-Reimu/inc/github-login/access.php'),
  login2fa: resolve(root, 'theme/Yneko-Reimu/inc/github-login/login-2fa.php')
};
const settingsPagePath = resolve(root, 'theme/Yneko-Reimu/inc/settings/page.php');
const settingsPageModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/context.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/tabs.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/general.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/page/submit.php')
];
const settingsPanelsPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/panels.php');
const settingsPanelModulePaths = [
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/users.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/security.php'),
  resolve(root, 'theme/Yneko-Reimu/inc/settings/panels/music.php')
];
const settingsSchemaPaths = [
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
const reimuSourcePath = resolve(root, 'theme/Yneko-Reimu/assets/src/reimu.js');
const commentsRuntimePath = resolve(root, 'theme/Yneko-Reimu/assets/src/reimu/comments-profile.js');

const [
  githubEntry,
  githubSettings,
  githubRendering,
  githubStyles,
  githubOauth,
  githubUsers,
  githubAvatars,
  githubAccess,
  githubLogin2fa,
  settingsPageEntry,
  settingsPanelsEntry,
  reimuEntrySource,
  commentsRuntimeSource,
  ...settingsModules
] = await Promise.all([
  readFile(githubPath, 'utf8'),
  readFile(githubModulePaths.settings, 'utf8'),
  readFile(githubModulePaths.rendering, 'utf8'),
  readFile(githubModulePaths.styles, 'utf8'),
  readFile(githubModulePaths.oauth, 'utf8'),
  readFile(githubModulePaths.users, 'utf8'),
  readFile(githubModulePaths.avatars, 'utf8'),
  readFile(githubModulePaths.access, 'utf8'),
  readFile(githubModulePaths.login2fa, 'utf8'),
  readFile(settingsPagePath, 'utf8'),
  readFile(settingsPanelsPath, 'utf8'),
  readFile(reimuSourcePath, 'utf8'),
  readFile(commentsRuntimePath, 'utf8'),
  ...settingsPageModulePaths.map((path) => readFile(path, 'utf8')),
  ...settingsPanelModulePaths.map((path) => readFile(path, 'utf8')),
  ...settingsSchemaPaths.map((path) => readFile(path, 'utf8'))
]);

const settingsPageModules = settingsModules.slice(0, settingsPageModulePaths.length);
const settingsPanelModules = settingsModules.slice(settingsPageModulePaths.length, settingsPageModulePaths.length + settingsPanelModulePaths.length);
const settingsSchemaModules = settingsModules.slice(settingsPageModulePaths.length + settingsPanelModulePaths.length);
const settingsPage = [settingsPageEntry, ...settingsPageModules].join('\n');
const settingsPanels = [settingsPanelsEntry, ...settingsPanelModules].join('\n');
const settingsSchema = settingsSchemaModules.join('\n');
const reimuSource = `${reimuEntrySource}\n${commentsRuntimeSource}`;

const github = [
  githubEntry,
  githubSettings,
  githubRendering,
  githubStyles,
  githubOauth,
  githubUsers,
  githubAvatars,
  githubAccess,
  githubLogin2fa
].join('\n');

const checks = [
  {
    label: 'OAuth module boundaries',
    source: githubEntry,
    snippets: [
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/settings.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/rendering.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/styles.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/oauth.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/users.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/avatars.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/access.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/login-2fa.php';"
    ]
  },
  {
    label: 'OAuth default option keys',
    source: github,
    snippets: [
      "'client_id'     => ''",
      "'client_secret' => ''",
      "'callback_url'  => ''",
      "'auto_create'   => '1'"
    ]
  },
  {
    label: 'Theme settings OAuth keys',
    source: settingsSchema,
    snippets: [
      "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/defaults.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/normalizers.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/getters.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/settings/schema/compat.php';",
      "'github_oauth'      => array(",
      "'show_admin_toolbar'   => '0'",
      "'client_id'     => ''",
      "'client_secret' => ''",
      "'callback_url'  => ''",
      "'auto_create'   => '0'",
      "function yneko_reimu_settings_github_oauth()",
      "function yneko_reimu_settings_features()",
      "get_option( 'yneko_reimu_github_login_options', array() )",
      "get_option( 'yneko_github_login_options', array() )",
      "function yneko_reimu_merge_github_oauth_fallback( $oauth, $fallback )",
      "foreach ( array( 'client_id', 'client_secret', 'callback_url', 'auto_create' ) as $key )"
    ]
  },
  {
    label: 'Settings panel field names',
    source: `${settingsPage}\n${settingsPanels}`,
    snippets: [
      'data-yneko-settings-panel="github"',
      'name="yneko_reimu_settings[github_oauth][callback_url]"',
      'name="yneko_reimu_settings[github_oauth][client_id]"',
      'name="yneko_reimu_settings[github_oauth][client_secret]"',
      'name="yneko_reimu_settings[github_oauth][auto_create]"',
      'name="yneko_reimu_settings[features][show_admin_toolbar]"',
      "yneko_reimu_settings_group_open( '管理员体验', 'Administrator experience'",
      'yneko_reimu_github_login_bind_url',
      "get_user_meta( get_current_user_id(), '_yneko_reimu_github_login', true )"
    ]
  },
  {
    label: 'Settings page callback fallback',
    source: settingsPage,
    snippets: [
      'yneko_reimu_settings_github_oauth()',
      'yneko_reimu_github_login_callback_url()',
      "add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() )"
    ]
  },
  {
    label: 'Public login action URLs',
    source: github,
    snippets: [
      "add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() )",
      "$args = array( 'action' => 'yneko_reimu_github_login' );",
      "'action' => 'yneko_reimu_github_bind'",
      "'nonce'  => wp_create_nonce( 'yneko_reimu_github_bind' )",
      "check_admin_referer( 'yneko_reimu_github_bind', 'nonce' )",
      "add_action( 'login_form_yneko_reimu_github_login', 'yneko_reimu_github_login_begin' )",
      "add_action( 'login_form_yneko_reimu_github_bind', 'yneko_reimu_github_login_begin_bind' )",
      "add_action( 'login_form_yneko_github_login', 'yneko_reimu_github_login_begin' )",
      "add_action( 'login_form_yneko_reimu_github_callback', 'yneko_reimu_github_login_callback' )",
      "add_action( 'login_form_yneko_github_callback', 'yneko_reimu_github_login_callback' )"
    ]
  },
  {
    label: 'OAuth state and redirect contract',
    source: github,
    snippets: [
      "'yneko_reimu_github_login_state_' . hash( 'sha256', $state )",
      "'redirect_to'  => $redirect_to",
      "'link_user_id' => $bind_current_user ? get_current_user_id() : 0",
      "'mode'         => $bind_current_user ? 'bind' : 'login'",
      "'popup'        => ! empty( $_GET['popup'] ) ? '1' : '0'",
      '10 * MINUTE_IN_SECONDS',
      "wp_validate_redirect( $redirect_to, home_url( '/' ) )",
      'delete_transient( $state_key )'
    ]
  },
  {
    label: 'GitHub authorization and API contract',
    source: github,
    snippets: [
      'https://github.com/login/oauth/authorize?',
      "'scope'        => 'read:user user:email'",
      "'allow_signup' => 'true'",
      'https://github.com/login/oauth/access_token',
      "'Accept' => 'application/json'",
      "'redirect_uri'  => yneko_reimu_github_login_callback_url()",
      'https://api.github.com/user',
      'https://api.github.com/user/emails',
      "'Accept'        => 'application/vnd.github+json'",
      "'User-Agent'    => 'Yneko-WordPress-GitHub-Login'"
    ]
  },
  {
    label: 'OAuth user/meta compatibility',
    source: github,
    snippets: [
      "foreach ( array( '_yneko_reimu_github_id', '_yneko_github_id' ) as $meta_key )",
      "update_user_meta( $user_id, '_yneko_reimu_github_id', $values['id'] )",
      "update_user_meta( $user_id, '_yneko_reimu_github_login', $values['login'] )",
      "update_user_meta( $user_id, '_yneko_reimu_github_url', $values['url'] )",
      "update_user_meta( $user_id, '_yneko_reimu_github_avatar_url', $values['avatar_url'] )",
      "update_user_meta( $user_id, '_yneko_github_id', $values['id'] )",
      "update_user_meta( $user_id, '_yneko_github_login', $values['login'] )",
      "update_user_meta( $user_id, '_yneko_github_url', $values['url'] )",
      "update_user_meta( $user_id, '_yneko_github_avatar_url', $values['avatar_url'] )",
      "get_user_meta( $user_id, '_yneko_reimu_github_avatar_url', true )",
      "get_user_meta( $user_id, '_yneko_github_avatar_url', true )"
    ]
  },
  {
    label: 'OAuth popup/front-end contract',
    source: `${github}\n${reimuSource}`,
    snippets: [
      'data-reimu-github-popup',
      "payload = { type: 'yneko-reimu-github-login', success: true, redirectTo:",
      'window.opener.postMessage',
      "window.localStorage.setItem('yneko-reimu-github-login'",
      "event.key !== 'yneko-reimu-github-login'",
      'handleGithubLoginSuccess',
      "body.getAttribute('data-yneko-reimu-github-login-done') === '1'",
      "data.type !== 'yneko-reimu-github-login'",
      "event.target.closest('[data-reimu-github-popup]')",
      "openAuthPopup(link.href, 'yneko_reimu_github_login', 560, 720)"
    ]
  },
  {
    label: 'Login password visibility style contract',
    source: githubStyles,
    snippets: [
      'function yneko_reimu_github_login_enqueue_styles',
      'function yneko_reimu_github_login_css',
      'function yneko_reimu_github_login_button_css',
      'function yneko_reimu_github_login_layout_css',
      'function yneko_reimu_github_login_button_control_css',
      'function yneko_reimu_github_login_password_css',
      'function yneko_reimu_github_login_footer_css',
      "wp_register_style( 'yneko-reimu-github-login', false, array(), YNEKO_REIMU_VERSION )",
      "wp_enqueue_style( 'yneko-reimu-github-login' )",
      "wp_add_inline_style( 'yneko-reimu-github-login', yneko_reimu_github_login_css() )",
      "add_action( 'wp_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' )",
      "add_action( 'login_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' )",
      'body.login .wp-pwd',
      'position: relative;',
      'display: grid;',
      'body.login .button-secondary:not(.wp-hide-pw)',
      'body.login .wp-pwd .wp-hide-pw.button.button-secondary',
      'body.login .wp-pwd input[type="password"]',
      'body.login .wp-pwd input[type="text"]',
      'margin: 0 !important;',
      'position: relative !important;',
      'grid-area: 1 / 1;',
      'align-self: center !important;',
      'justify-self: end !important;',
      'color: #ff5252 !important;',
      'transform: none !important;',
      'background: transparent !important;',
      'border: 0 !important;',
      'box-shadow: none !important;',
      'body.login .wp-pwd .wp-hide-pw .dashicons',
      'display: none !important;',
      'body.login .wp-pwd .wp-hide-pw::before',
      'password-hidden.svg',
      'password-visible.svg',
      'body.login .wp-pwd .wp-hide-pw:has(.dashicons-hidden)::before'
    ]
  },
  {
    label: 'Front-end admin toolbar compatibility',
    source: `${githubAccess}\n${settingsPage}\n${settingsPanels}\n${settingsSchema}`,
    snippets: [
      'function yneko_reimu_show_frontend_admin_toolbar()',
      "return '1' === ( $features['show_admin_toolbar'] ?? '0' );",
      "add_filter( 'show_admin_bar', 'yneko_reimu_hide_admin_bar_for_comment_users' );",
      'function yneko_reimu_hide_frontend_plugin_toolbar_notices()',
      'add_action( \'wp_head\', \'yneko_reimu_hide_frontend_plugin_toolbar_notices\', 1 );',
      '#rank-math-analytics-stats-wrapper',
      '.rank-math-analytics-stats-wrapper',
      '.rank-math-pro-cta',
      '#wpadminbar',
      'html {',
      'margin-top: 0 !important;',
      "'show_admin_toolbar'   => '0'",
      'name="yneko_reimu_settings[features][show_admin_toolbar]"'
    ]
  },
  {
    label: 'Backend login TOTP contract',
    source: `${githubLogin2fa}\n${githubRendering}\n${githubStyles}`,
    snippets: [
      'function yneko_reimu_login_2fa_field',
      "add_action( 'login_form', 'yneko_reimu_login_2fa_field' );",
      'name="yneko_reimu_login_totp_code"',
      'autocomplete="one-time-code"',
      '认证器验证码或恢复码',
      '如果没有开启二次认证，留空即可',
      'function yneko_reimu_login_2fa_authenticate',
      "add_filter( 'authenticate', 'yneko_reimu_login_2fa_authenticate', 30 );",
      'yneko_reimu_login_2fa_is_wp_login_request',
      'yneko_reimu_user_2fa_enabled',
      'yneko_reimu_user_2fa_secret',
      'yneko_reimu_totp_verify',
      'function yneko_reimu_login_2fa_generate_recovery_codes',
      'function yneko_reimu_login_2fa_store_recovery_codes',
      'function yneko_reimu_login_2fa_consume_recovery_code',
      "return '_yneko_reimu_totp_recovery_codes';",
      'wp_hash_password( $normalized )',
      'wp_check_password( $normalized, $hash, absint( $user_id ) )',
      'unset( $hashes[ $index ] );',
      "'/^\\d{6}$/'",
      '登录信息或二次验证码不正确。',
      'body.login .yneko-reimu-login-totp .description'
    ]
  },
  {
    label: 'OAuth user-facing error messages',
    source: github,
    snippets: [
      'GitHub login is not configured.',
      'Missing GitHub OAuth response.',
      'GitHub login state expired. Please try again.',
      'GitHub did not return an access token.',
      'GitHub API request failed.',
      'GitHub profile is missing required fields.',
      'This GitHub account is already linked to another WordPress account.',
      'No WordPress account is linked to this GitHub account.',
      'This GitHub email already belongs to an existing WordPress account. Please log in normally first, then bind GitHub.'
    ]
  }
];

const failures = [];

for (const check of checks) {
  for (const snippet of check.snippets) {
    if (!check.source.includes(snippet)) {
      failures.push(`${check.label}: missing ${snippet}`);
    }
  }
}

if (failures.length) {
  console.error('[github-oauth] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`[github-oauth] ${checks.length} GitHub OAuth contract groups are present.`);
