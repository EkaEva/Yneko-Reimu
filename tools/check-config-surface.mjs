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
const settingsSchemaPaths = [
  resolve(themeRoot, 'inc/settings/schema.php'),
  resolve(themeRoot, 'inc/settings/schema/defaults.php'),
  resolve(themeRoot, 'inc/settings/schema/normalizers.php'),
  resolve(themeRoot, 'inc/settings/schema/sanitizers.php'),
  resolve(themeRoot, 'inc/settings/schema/sanitizers/media.php'),
  resolve(themeRoot, 'inc/settings/schema/sanitizers/users.php'),
  resolve(themeRoot, 'inc/settings/schema/sanitizers/groups.php'),
  resolve(themeRoot, 'inc/settings/schema/getters.php'),
  resolve(themeRoot, 'inc/settings/schema/compat.php')
];
const settingsSchema = (await Promise.all(settingsSchemaPaths.map((path) => readFile(path, 'utf8')))).join('\n');
const settingsPagePaths = [
  resolve(themeRoot, 'inc/settings/page.php'),
  resolve(themeRoot, 'inc/settings/page/context.php'),
  resolve(themeRoot, 'inc/settings/page/tabs.php'),
  resolve(themeRoot, 'inc/settings/page/general.php'),
  resolve(themeRoot, 'inc/settings/page/submit.php')
];
const settingsPage = (await Promise.all(settingsPagePaths.map((path) => readFile(path, 'utf8')))).join('\n');
const customizerPaths = [
  resolve(themeRoot, 'inc/customizer.php'),
  resolve(themeRoot, 'inc/customizer/panel.php'),
  resolve(themeRoot, 'inc/customizer/preset.php'),
  resolve(themeRoot, 'inc/customizer/sidebar-widgets.php'),
  resolve(themeRoot, 'inc/customizer/visual.php'),
  resolve(themeRoot, 'inc/customizer/visual-assets.php'),
  resolve(themeRoot, 'inc/customizer/typography-layout.php'),
  resolve(themeRoot, 'inc/customizer/images.php'),
  resolve(themeRoot, 'inc/customizer/cards.php'),
  resolve(themeRoot, 'inc/customizer/articles.php'),
  resolve(themeRoot, 'inc/customizer/restore-defaults.php'),
  resolve(themeRoot, 'inc/customizer/social.php'),
  resolve(themeRoot, 'inc/customizer/footer-virtual.php')
];
const customizer = (await Promise.all(customizerPaths.map((path) => readFile(path, 'utf8')))).join('\n');
const commentsRenderingPaths = [
  resolve(themeRoot, 'inc/comments/rendering.php'),
  resolve(themeRoot, 'inc/comments/rendering/toolbar.php'),
  resolve(themeRoot, 'inc/comments/rendering/identity.php'),
  resolve(themeRoot, 'inc/comments/rendering/environment.php'),
  resolve(themeRoot, 'inc/comments/rendering/markdown.php'),
  resolve(themeRoot, 'inc/comments/rendering/list-helpers.php'),
  resolve(themeRoot, 'inc/comments/rendering/list.php'),
  resolve(themeRoot, 'inc/comments/rendering/external-panels.php'),
  resolve(themeRoot, 'inc/comments/rendering/external.php')
];
const commentsRendering = (await Promise.all(commentsRenderingPaths.map((path) => readFile(path, 'utf8')))).join('\n');
const contentToolPaths = [
  resolve(themeRoot, 'inc/template-tags/content-tools.php'),
  resolve(themeRoot, 'inc/template-tags/content-tools/home-categories.php')
];
const contentTools = (await Promise.all(contentToolPaths.map((path) => readFile(path, 'utf8')))).join('\n');

const files = {
  schema: settingsSchema,
  panels: settingsPanels,
  page: settingsPage,
  svg: await readFile(resolve(themeRoot, 'inc/svg.php'), 'utf8'),
  comments: await readFile(resolve(themeRoot, 'inc/comments.php'), 'utf8'),
  commentsContext: await readFile(resolve(themeRoot, 'inc/comments/context.php'), 'utf8'),
  commentsRendering,
  customizer,
  migrations: await readFile(resolve(themeRoot, 'inc/migrations.php'), 'utf8'),
  contentTools,
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
  'yneko_reimu_cursor_default_url',
  'yneko_reimu_cursor_pointer_url',
  'yneko_reimu_cursor_text_url',
  'yneko_reimu_cursor_progress_url',
  'yneko_reimu_preloader_image_url',
  'yneko_reimu_preloader_text_zh',
  'yneko_reimu_preloader_text_en',
  'yneko_reimu_preloader_image_size',
  'yneko_reimu_preloader_image_rotate',
  'yneko_reimu_top_icon_url',
  'yneko_reimu_sponsor_icon_url',
  'yneko_reimu_font_body',
  'yneko_reimu_font_heading',
  'yneko_reimu_font_code',
  'yneko_reimu_base_font_size',
  'yneko_reimu_article_font_size',
  'yneko_reimu_article_line_height',
  'yneko_reimu_content_max_width',
  'yneko_reimu_article_content_width',
  'yneko_reimu_layout_density',
  'yneko_reimu_card_radius',
  'yneko_reimu_image_radius',
  'yneko_reimu_shadow_strength',
  'yneko_reimu_customizer_reset_groups',
  'yneko_reimu_restore_defaults',
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
