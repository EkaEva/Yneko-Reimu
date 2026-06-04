import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const githubPath = resolve(root, 'theme/Yneko-Reimu/inc/github-login.php');
const githubModulePaths = {
  settings: resolve(root, 'theme/Yneko-Reimu/inc/github-login/settings.php'),
  rendering: resolve(root, 'theme/Yneko-Reimu/inc/github-login/rendering.php'),
  oauth: resolve(root, 'theme/Yneko-Reimu/inc/github-login/oauth.php'),
  users: resolve(root, 'theme/Yneko-Reimu/inc/github-login/users.php'),
  avatars: resolve(root, 'theme/Yneko-Reimu/inc/github-login/avatars.php'),
  access: resolve(root, 'theme/Yneko-Reimu/inc/github-login/access.php')
};
const settingsPagePath = resolve(root, 'theme/Yneko-Reimu/inc/settings/page.php');
const settingsPanelsPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/panels.php');
const settingsSchemaPath = resolve(root, 'theme/Yneko-Reimu/inc/settings/schema.php');
const reimuSourcePath = resolve(root, 'theme/Yneko-Reimu/assets/src/reimu.js');

const [
  githubEntry,
  githubSettings,
  githubRendering,
  githubOauth,
  githubUsers,
  githubAvatars,
  githubAccess,
  settingsPage,
  settingsPanels,
  settingsSchema,
  reimuSource
] = await Promise.all([
  readFile(githubPath, 'utf8'),
  readFile(githubModulePaths.settings, 'utf8'),
  readFile(githubModulePaths.rendering, 'utf8'),
  readFile(githubModulePaths.oauth, 'utf8'),
  readFile(githubModulePaths.users, 'utf8'),
  readFile(githubModulePaths.avatars, 'utf8'),
  readFile(githubModulePaths.access, 'utf8'),
  readFile(settingsPagePath, 'utf8'),
  readFile(settingsPanelsPath, 'utf8'),
  readFile(settingsSchemaPath, 'utf8'),
  readFile(reimuSourcePath, 'utf8')
]);

const github = [
  githubEntry,
  githubSettings,
  githubRendering,
  githubOauth,
  githubUsers,
  githubAvatars,
  githubAccess
].join('\n');

const checks = [
  {
    label: 'OAuth module boundaries',
    source: githubEntry,
    snippets: [
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/settings.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/rendering.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/oauth.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/users.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/avatars.php';",
      "require_once YNEKO_REIMU_DIR . '/inc/github-login/access.php';"
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
      "'github_oauth'      => array(",
      "'client_id'     => ''",
      "'client_secret' => ''",
      "'callback_url'  => ''",
      "'auto_create'   => '0'",
      "function yneko_reimu_settings_github_oauth()",
      "get_option( 'yneko_reimu_github_login_options', array() )",
      "get_option( 'yneko_github_login_options', array() )",
      "function yneko_reimu_merge_github_oauth_fallback( $oauth, $fallback )",
      "foreach ( array( 'client_id', 'client_secret', 'callback_url', 'auto_create' ) as $key )"
    ]
  },
  {
    label: 'Settings panel field names',
    source: settingsPanels,
    snippets: [
      'data-yneko-settings-panel="github"',
      'name="yneko_reimu_settings[github_oauth][callback_url]"',
      'name="yneko_reimu_settings[github_oauth][client_id]"',
      'name="yneko_reimu_settings[github_oauth][client_secret]"',
      'name="yneko_reimu_settings[github_oauth][auto_create]"',
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
      "data.type !== 'yneko-reimu-github-login'",
      "event.target.closest('[data-reimu-github-popup]')",
      "openAuthPopup(link.href, 'yneko_reimu_github_login', 560, 720)"
    ]
  },
  {
    label: 'Login password visibility style contract',
    source: githubRendering,
    snippets: [
      'body.login .wp-pwd',
      'position: relative;',
      'display: grid;',
      'body.login .button-secondary:not(.wp-hide-pw)',
      'body.login .wp-pwd .wp-hide-pw.button.button-secondary',
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
