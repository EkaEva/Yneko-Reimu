import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const settingsPanelPaths = [
  resolve(themeRoot, 'inc/settings/panels.php'),
  resolve(themeRoot, 'inc/settings/panels/users.php'),
  resolve(themeRoot, 'inc/settings/panels/security.php'),
  resolve(themeRoot, 'inc/settings/panels/music.php')
];
const settingsPanels = (await Promise.all(settingsPanelPaths.map((path) => readFile(path, 'utf8')))).join('\n');

const files = {
  functions: await readFile(resolve(themeRoot, 'functions.php'), 'utf8'),
  security: await readFile(resolve(themeRoot, 'inc/security-auth-mail.php'), 'utf8'),
  schema: await readFile(resolve(themeRoot, 'inc/settings/schema.php'), 'utf8'),
  page: await readFile(resolve(themeRoot, 'inc/settings/page.php'), 'utf8'),
  panels: settingsPanels,
  admin: await readFile(resolve(themeRoot, 'inc/settings/admin.php'), 'utf8'),
  auth: await readFile(resolve(themeRoot, 'inc/comments/auth.php'), 'utf8'),
  profile: await readFile(resolve(themeRoot, 'inc/comments/profile.php'), 'utf8')
};

const source = Object.values(files).join('\n');
let failed = false;

function fail(message) {
  console.error(`[auth-security] ${message}`);
  failed = true;
}

function requireSnippet(label, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    fail(`Missing ${label}: ${snippet}`);
  }
}

for (const snippet of [
  "require_once YNEKO_REIMU_DIR . '/inc/security-auth-mail.php';",
  'function yneko_reimu_auth_security_defaults',
  'function yneko_reimu_sanitize_auth_security_settings',
  'function yneko_reimu_settings_auth_security',
  'function yneko_reimu_auth_security_check',
  'function yneko_reimu_auth_security_commit',
  'function yneko_reimu_auth_security_record_mail_failure',
  'function yneko_reimu_auth_security_log_event',
  'function yneko_reimu_auth_security_unhandled_count'
]) {
  requireSnippet('auth security helper boundary', snippet);
}

for (const defaultSnippet of [
  "'enabled'                  => '1'",
  "'protect_ajax'             => '1'",
  "'protect_wp_login'         => '1'",
  "'email_hour_limit'         => 3",
  "'email_day_limit'          => 8",
  "'ip_hour_limit'            => 10",
  "'ip_day_limit'             => 30",
  "'device_hour_limit'        => 5",
  "'device_day_limit'         => 15",
  "'global_day_limit'         => 100",
  "'cooldown_seconds'         => 60",
  "'global_warning_threshold' => 80",
  "'email_alert_enabled'      => '0'"
]) {
  requireSnippet('auth security default limit', defaultSnippet, `${files.security}\n${files.schema}`);
}

for (const deviceSnippet of [
  "return 'yneko_reimu_auth_device';",
  '180 * DAY_IN_SECONDS',
  "'samesite' => 'Lax'",
  "'httponly' => true",
  "'secure'   => is_ssl()",
  'setcookie( $cookie_name, $device, $args )',
  "setcookie( $cookie_name, $device, $expires, $path, $args['domain'], $args['secure'], true )"
]) {
  requireSnippet('privacy-friendly device cookie', deviceSnippet, files.security);
}

for (const counterSnippet of [
  "'email', 'period' => 'hour'",
  "'email', 'period' => 'day'",
  "'ip', 'period' => 'hour'",
  "'ip', 'period' => 'day'",
  "'device', 'period' => 'hour'",
  "'device', 'period' => 'day'",
  "'global', 'period' => 'day'",
  "'cooldown', 'period' => 'cooldown'",
  'set_transient( $key, $value, yneko_reimu_auth_security_counter_ttl( $period ) )',
  'set_transient( $key, 1, max( 10, absint( $rule[\'ttl\'] ?? 60 ) ) )'
]) {
  requireSnippet('auth security transient counter', counterSnippet, files.security);
}

for (const logSnippet of [
  "get_option( 'yneko_reimu_auth_security_events', array() )",
  'array_slice( $events, 0, 100 )',
  "'handled'     => 0",
  "'email_hash'",
  "'ip_hash'",
  "'device_hash'",
  "error_log( '[Yneko-Reimu auth security] '",
  "delete_option( 'yneko_reimu_auth_security_events' )"
]) {
  requireSnippet('auth security event log', logSnippet, files.security);
}

for (const hookSnippet of [
  "add_filter( 'registration_errors', 'yneko_reimu_auth_security_registration_errors', 10, 3 )",
  "add_filter( 'lostpassword_errors', 'yneko_reimu_auth_security_lostpassword_errors', 10, 2 )",
  "yneko_reimu_auth_security_check( 'register', $user_email, 'wp-login' )",
  "yneko_reimu_auth_security_check( 'lostpassword', $email, 'wp-login' )"
]) {
  requireSnippet('native wp-login auth email guard', hookSnippet, files.security);
}

for (const ajaxSnippet of [
  "yneko_reimu_auth_security_check( 'register', $user_email, 'ajax' )",
  "yneko_reimu_auth_security_check( 'lostpassword', $identifier, 'ajax' )",
  "yneko_reimu_auth_security_check( 'profile_email', $new_email, 'ajax' )",
  "yneko_reimu_auth_security_record_mail_failure( 'register', $user_email, 'ajax' )",
  "yneko_reimu_auth_security_record_mail_failure( 'lostpassword', $identifier, 'ajax' )",
  "yneko_reimu_auth_security_record_mail_failure( 'profile_email', $new_email, 'ajax' )"
]) {
  requireSnippet('front-end AJAX auth email guard', ajaxSnippet, `${files.auth}\n${files.profile}`);
}

for (const uiSnippet of [
  'data-yneko-settings-tab="security"',
  'data-yneko-settings-panel="security"',
  'name="yneko_reimu_settings[auth_security][enabled]"',
  'name="yneko_reimu_settings[auth_security][email_hour_limit]"',
  'name="yneko_reimu_settings[auth_security][global_day_limit]"',
  'yneko-reimu-security-alert-card',
  'yneko_reimu_admin_badge( $review_badges[\'security\'] ?? 0 )',
  '$counts[\'security\'] = function_exists( \'yneko_reimu_auth_security_unhandled_count\' ) ? yneko_reimu_auth_security_unhandled_count() : 0'
]) {
  requireSnippet('settings auth security UI', uiSnippet);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[auth-security] settings, counters, device cookie, alerts, and mail guard contracts are present.');
}
