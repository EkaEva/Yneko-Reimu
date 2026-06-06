import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const files = {
  entry: await readFile(resolve(themeRoot, 'inc/i18n.php'), 'utf8'),
  settings: await readFile(resolve(themeRoot, 'inc/i18n/settings.php'), 'utf8'),
  urls: await readFile(resolve(themeRoot, 'inc/i18n/urls.php'), 'utf8'),
  posts: await readFile(resolve(themeRoot, 'inc/i18n/posts.php'), 'utf8'),
  requests: await readFile(resolve(themeRoot, 'inc/i18n/requests.php'), 'utf8'),
  queries: await readFile(resolve(themeRoot, 'inc/i18n/queries.php'), 'utf8')
};

const source = Object.values(files).join('\n');
let failed = false;

function fail(message) {
  console.error(`[i18n-contract] ${message}`);
  failed = true;
}

function requireSnippet(label, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    fail(`Missing ${label}: ${snippet}`);
  }
}

for (const moduleImport of [
  "require_once YNEKO_REIMU_DIR . '/inc/i18n/settings.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/i18n/urls.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/i18n/posts.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/i18n/requests.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/i18n/queries.php';"
]) {
  requireSnippet('i18n module boundary', moduleImport, files.entry);
}

for (const [label, haystack] of Object.entries(files)) {
  requireSnippet(`direct access guard in ${label}`, "if ( ! defined( 'ABSPATH' ) )", haystack);
}

for (const functionName of [
  'yneko_reimu_i18n_defaults',
  'yneko_reimu_i18n_settings',
  'yneko_reimu_i18n_enabled',
  'yneko_reimu_i18n_default_language',
  'yneko_reimu_i18n_languages',
  'yneko_reimu_i18n_language_exists',
  'yneko_reimu_i18n_normalize_language',
  'yneko_reimu_i18n_url_prefix',
  'yneko_reimu_i18n_request_path',
  'yneko_reimu_i18n_current_language',
  'yneko_reimu_i18n_is_english_request',
  'yneko_reimu_i18n_frontend_text',
  'yneko_reimu_i18n_relative_without_prefix',
  'yneko_reimu_i18n_prefixed_url',
  'yneko_reimu_i18n_home_url',
  'yneko_reimu_i18n_localize_url',
  'yneko_reimu_i18n_post_language',
  'yneko_reimu_i18n_translation_id',
  'yneko_reimu_i18n_source_post_id',
  'yneko_reimu_i18n_get_translation_id',
  'yneko_reimu_i18n_post_url',
  'yneko_reimu_i18n_display_post_for_language',
  'yneko_reimu_i18n_find_post_by_en_path',
  'yneko_reimu_i18n_rewrite_rules',
  'yneko_reimu_i18n_detect_en_path',
  'yneko_reimu_i18n_query_vars',
  'yneko_reimu_i18n_parse_request_fallback',
  'yneko_reimu_i18n_resolve_en_request',
  'yneko_reimu_i18n_force_404_status',
  'yneko_reimu_i18n_force_404_template',
  'yneko_reimu_i18n_language_meta_query',
  'yneko_reimu_i18n_apply_language_query_args',
  'yneko_reimu_i18n_filter_main_query',
  'yneko_reimu_i18n_rest_post_query',
  'yneko_reimu_i18n_filter_sticky_posts',
  'yneko_reimu_i18n_virtual_path',
  'yneko_reimu_i18n_switch_url',
  'yneko_reimu_i18n_options'
]) {
  requireSnippet('public i18n function', `function ${functionName}`);
}

for (const hook of [
  "add_filter( 'locale', 'yneko_reimu_i18n_filter_locale', 20 );",
  "add_filter( 'determine_locale', 'yneko_reimu_i18n_filter_locale', 20 );",
  "add_action( 'after_setup_theme', 'yneko_reimu_i18n_load_frontend_textdomain', 20 );",
  "add_filter( 'language_attributes', 'yneko_reimu_i18n_language_attributes', 20 );",
  "add_filter( 'post_link', 'yneko_reimu_i18n_filter_post_link', 20, 2 );",
  "add_filter( 'page_link', 'yneko_reimu_i18n_filter_page_link', 20, 2 );",
  "add_action( 'init', 'yneko_reimu_i18n_rewrite_rules', 5 );",
  "add_filter( 'query_vars', 'yneko_reimu_i18n_query_vars' );",
  "add_action( 'parse_request', 'yneko_reimu_i18n_parse_request_fallback', 1 );",
  "add_action( 'pre_get_posts', 'yneko_reimu_i18n_resolve_en_request', 1 );",
  "add_action( 'template_redirect', 'yneko_reimu_i18n_force_404_status', 0 );",
  "add_filter( 'template_include', 'yneko_reimu_i18n_force_404_template', 100 );",
  "add_action( 'pre_get_posts', 'yneko_reimu_i18n_filter_main_query', 20 );",
  "add_filter( 'rest_post_query', 'yneko_reimu_i18n_rest_post_query', 20, 2 );",
  "add_filter( 'option_sticky_posts', 'yneko_reimu_i18n_filter_sticky_posts', 20 );",
  "add_filter( 'term_link', 'yneko_reimu_i18n_filter_term_link', 20 );",
  "add_filter( 'author_link', 'yneko_reimu_i18n_filter_term_link', 20 );",
  "add_action( 'after_switch_theme', 'yneko_reimu_i18n_flush_rewrite_rules' );",
  "add_action( 'admin_init', 'yneko_reimu_i18n_maybe_flush_rewrite_rules' );",
  "add_action( 'wp_loaded', 'yneko_reimu_i18n_maybe_flush_rewrite_rules', 20 );"
]) {
  requireSnippet('i18n hook contract', hook);
}

for (const contract of [
  "'_yneko_reimu_language'",
  "'_yneko_reimu_translation_id'",
  "'yneko_reimu_lang'",
  "'yneko_reimu_en_path'",
  "'yneko_reimu_force_404'",
  "'yneko_reimu_i18n_rewrite_state'",
  "'en_US'",
  "'zh_CN'",
  "'search.json'",
  "'about', 'projects', 'archives', 'friend'",
  "add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/(.+?)/?$'",
  "wp_parse_url( home_url( '/' )",
  "wp_make_link_relative",
  "user_trailingslashit",
  "status_header( 404 )",
  "flush_rewrite_rules()"
]) {
  requireSnippet('i18n behavior contract', contract);
}

const dependencyFiles = {
  'inc/seo-compat.php': await readFile(resolve(themeRoot, 'inc/seo-compat.php'), 'utf8'),
  'inc/search-index.php': await readFile(resolve(themeRoot, 'inc/search-index.php'), 'utf8'),
  'inc/post-meta.php and inc/post-meta/*.php': (
    await Promise.all([
      'inc/post-meta.php',
      'inc/post-meta/register.php',
      'inc/post-meta/admin.php',
      'inc/post-meta/save.php'
    ].map((relativeFile) => readFile(resolve(themeRoot, relativeFile), 'utf8')))
  ).join('\n'),
  'inc/template-tags/navigation-virtual.php and inc/template-tags/navigation-virtual/*.php': (
    await Promise.all([
      'inc/template-tags/navigation-virtual.php',
      'inc/template-tags/navigation-virtual/navigation.php',
      'inc/template-tags/navigation-virtual/virtual.php',
      'inc/template-tags/navigation-virtual/walkers.php'
    ].map((relativeFile) => readFile(resolve(themeRoot, relativeFile), 'utf8')))
  ).join('\n')
};

for (const [relativeFile, dependencies] of Object.entries({
  'inc/seo-compat.php': [
    'yneko_reimu_i18n_enabled',
    'yneko_reimu_i18n_current_language',
    'yneko_reimu_i18n_post_url',
    'yneko_reimu_i18n_hreflang_links'
  ],
  'inc/search-index.php': [
    'yneko_reimu_i18n_language_exists',
    'yneko_reimu_i18n_current_language',
    'yneko_reimu_i18n_prefixed_url',
    'yneko_reimu_i18n_language_meta_query'
  ],
  'inc/post-meta.php and inc/post-meta/*.php': [
    'yneko_reimu_i18n_normalize_language',
    'yneko_reimu_i18n_post_language',
    'yneko_reimu_i18n_translation_id',
    'yneko_reimu_i18n_languages'
  ],
  'inc/template-tags/navigation-virtual.php and inc/template-tags/navigation-virtual/*.php': [
    'yneko_reimu_i18n_is_english_request',
    'yneko_reimu_i18n_home_url',
    'yneko_reimu_i18n_virtual_path',
    'yneko_reimu_i18n_localize_url'
  ]
})) {
  const content = dependencyFiles[relativeFile];
  for (const dependency of dependencies) {
    requireSnippet(`i18n dependency in ${relativeFile}`, dependency, content);
  }
}

if (failed) {
  console.error('[i18n-contract] Contract check failed.');
  process.exit(1);
}

console.log('[i18n-contract] module boundaries, URL/routing, query, meta, and dependency contracts are present.');
