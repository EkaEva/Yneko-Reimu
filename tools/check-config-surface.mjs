import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const files = {
  schema: await readFile(resolve(themeRoot, 'inc/settings/schema.php'), 'utf8'),
  panels: await readFile(resolve(themeRoot, 'inc/settings/panels.php'), 'utf8'),
  page: await readFile(resolve(themeRoot, 'inc/settings/page.php'), 'utf8'),
  svg: await readFile(resolve(themeRoot, 'inc/svg.php'), 'utf8'),
  comments: await readFile(resolve(themeRoot, 'inc/comments.php'), 'utf8'),
  commentsRendering: await readFile(resolve(themeRoot, 'inc/comments/rendering.php'), 'utf8'),
  customizer: await readFile(resolve(themeRoot, 'inc/customizer.php'), 'utf8'),
  migrations: await readFile(resolve(themeRoot, 'inc/migrations.php'), 'utf8'),
  contentTools: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools.php'), 'utf8'),
  hooks: await readFile(resolve(root, 'docs/hooks.md'), 'utf8'),
  development: await readFile(resolve(root, 'docs/development.md'), 'utf8')
};

const source = Object.values(files).join('\n');
const failures = [];

function requireSnippet(category, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    failures.push(`${category}: missing ${snippet}`);
  }
}

const coveredAdminUi = [
  {
    key: 'security.allow_svg_uploads',
    snippets: [
      "'allow_svg_uploads'        => '1'",
      "array( 'allow_svg_uploads', 'comment_ip_region_lookup' )",
      'name="yneko_reimu_settings[security][allow_svg_uploads]"',
      'function yneko_reimu_security_allow_svg_uploads',
      'yneko_reimu_security_allow_svg_uploads()',
      'yneko_reimu_allow_svg_uploads'
    ]
  },
  {
    key: 'security.comment_ip_region_lookup',
    snippets: [
      "'comment_ip_region_lookup' => '1'",
      "array( 'allow_svg_uploads', 'comment_ip_region_lookup' )",
      'name="yneko_reimu_settings[security][comment_ip_region_lookup]"',
      'function yneko_reimu_security_comment_ip_region_lookup',
      'function yneko_reimu_comment_ip_region_lookup_enabled',
      'yneko_reimu_comment_ip_region_lookup_enabled()',
      'https://ipwho.is/'
    ]
  }
];

const coveredCustomizer = [
  'yneko_reimu_show_update_time',
  "yneko_reimu_share_' . $key . '_enabled",
  "yneko_reimu_social_' . $key . '_enabled",
  'yneko_reimu_settings[github_url]',
  'yneko_reimu_sidebar_position',
  'yneko_reimu_home_category_'
];

const developerExtensions = [
  'yneko_reimu_feature_defaults',
  'yneko_reimu_asset_strategy',
  'yneko_reimu_security_headers',
  'yneko_reimu_schema_enabled',
  'yneko_reimu_schema_graph',
  'yneko_reimu_content_width',
  'yneko_reimu_allow_svg_uploads',
  'yneko_reimu_virtual_pages'
];

const internalCompat = [
  'yneko_reimu_projects_comment_post_id',
  'yneko_reimu_github_login_options',
  'yneko_github_login_options',
  'yneko_reimu_friend_links',
  'yneko_reimu_aplayer_audio_json',
  'yneko_reimu_settings_raw_has_group'
];

for (const item of coveredAdminUi) {
  for (const snippet of item.snippets) {
    requireSnippet(`covered/admin UI ${item.key}`, snippet);
  }
}

for (const key of coveredCustomizer) {
  requireSnippet(`covered/customizer ${key}`, key, files.customizer);
}

for (const hook of developerExtensions) {
  requireSnippet(`developer extension ${hook}`, hook, files.hooks);
}

for (const marker of internalCompat) {
  requireSnippet(`internal/compat ${marker}`, marker, source);
}

const securityPanelStart = files.panels.indexOf("yneko_reimu_settings_group_open( '媒体与隐私', 'Media and privacy'");
const securityAlertsStart = files.panels.indexOf("yneko_reimu_settings_group_open( '安全报警', 'Security alerts'");
if (-1 === securityPanelStart || -1 === securityAlertsStart || securityAlertsStart <= securityPanelStart) {
  failures.push('covered/admin UI: Media and privacy group must appear before Security alerts in the Security panel.');
}

const regionFunctionStart = files.commentsRendering.indexOf('function yneko_reimu_comment_region_from_ip');
const remoteCallStart = files.commentsRendering.indexOf("wp_remote_get(\n\t\t'https://ipwho.is/'");
const guardCallStart = files.commentsRendering.indexOf('yneko_reimu_comment_ip_region_lookup_enabled()', regionFunctionStart);
if (-1 === regionFunctionStart || -1 === remoteCallStart || -1 === guardCallStart || !(regionFunctionStart < guardCallStart && guardCallStart < remoteCallStart)) {
  failures.push('covered/admin UI security.comment_ip_region_lookup: ipwho.is request must be guarded before wp_remote_get().');
}

const categories = {
  'covered/admin UI': coveredAdminUi.map((item) => item.key),
  'covered/customizer': coveredCustomizer,
  'developer extension': developerExtensions,
  'internal/compat': internalCompat
};

if (failures.length) {
  console.error('[config-surface] Contract check failed:');
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log('[config-surface] Configurable surface audit categories:');
for (const [category, items] of Object.entries(categories)) {
  console.log(`- ${category}: ${items.join(', ')}`);
}
