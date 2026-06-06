import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');

const files = {
  entry: await readFile(resolve(themeRoot, 'inc/template-tags.php'), 'utf8'),
  layoutEntry: await readFile(resolve(themeRoot, 'inc/template-tags/layout-content.php'), 'utf8'),
  layoutMeta: await readFile(resolve(themeRoot, 'inc/template-tags/layout-content/meta-layout.php'), 'utf8'),
  layoutTaxonomy: await readFile(resolve(themeRoot, 'inc/template-tags/layout-content/taxonomy-adjacent.php'), 'utf8'),
  layoutMetrics: await readFile(resolve(themeRoot, 'inc/template-tags/layout-content/metrics.php'), 'utf8'),
  layoutArchive: await readFile(resolve(themeRoot, 'inc/template-tags/layout-content/archive-footer.php'), 'utf8'),
  social: await readFile(resolve(themeRoot, 'inc/template-tags/social-share.php'), 'utf8'),
  navigationEntry: await readFile(resolve(themeRoot, 'inc/template-tags/navigation-virtual.php'), 'utf8'),
  navigation: await readFile(resolve(themeRoot, 'inc/template-tags/navigation-virtual/navigation.php'), 'utf8'),
  virtual: await readFile(resolve(themeRoot, 'inc/template-tags/navigation-virtual/virtual.php'), 'utf8'),
  walkers: await readFile(resolve(themeRoot, 'inc/template-tags/navigation-virtual/walkers.php'), 'utf8'),
  contentEntry: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools.php'), 'utf8'),
  homeCategories: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools/home-categories.php'), 'utf8'),
  ymlSponsor: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools/yml-sponsor.php'), 'utf8'),
  friends: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools/friends.php'), 'utf8'),
  githubProjects: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools/github-projects.php'), 'utf8'),
  postStats: await readFile(resolve(themeRoot, 'inc/template-tags/content-tools/post-stats.php'), 'utf8')
};
const layout = [
  files.layoutEntry,
  files.layoutMeta,
  files.layoutTaxonomy,
  files.layoutMetrics,
  files.layoutArchive
].join('\n');
const navigation = [
  files.navigationEntry,
  files.navigation,
  files.virtual,
  files.walkers
].join('\n');
const content = [
  files.contentEntry,
  files.homeCategories,
  files.ymlSponsor,
  files.friends,
  files.githubProjects,
  files.postStats
].join('\n');

const source = Object.values(files).join('\n');
let failed = false;

function fail(message) {
  console.error(`[template-tags] ${message}`);
  failed = true;
}

function requireSnippet(label, snippet, haystack = source) {
  if (!haystack.includes(snippet)) {
    fail(`Missing ${label}: ${snippet}`);
  }
}

for (const moduleImport of [
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/layout-content.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/social-share.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/navigation-virtual.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/layout-content/meta-layout.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/layout-content/taxonomy-adjacent.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/layout-content/metrics.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/layout-content/archive-footer.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/navigation-virtual/navigation.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/navigation-virtual/virtual.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/navigation-virtual/walkers.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools/home-categories.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools/yml-sponsor.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools/friends.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools/github-projects.php';",
  "require_once YNEKO_REIMU_DIR . '/inc/template-tags/content-tools/post-stats.php';"
]) {
  requireSnippet('template-tags module boundary', moduleImport);
}

for (const [label, snippet, haystack] of [
  ['direct access guard in entry', "if ( ! defined( 'ABSPATH' ) )", files.entry],
  ['direct access guard in layout entry', "if ( ! defined( 'ABSPATH' ) )", files.layoutEntry],
  ['direct access guard in layout meta module', "if ( ! defined( 'ABSPATH' ) )", files.layoutMeta],
  ['direct access guard in layout taxonomy module', "if ( ! defined( 'ABSPATH' ) )", files.layoutTaxonomy],
  ['direct access guard in layout metrics module', "if ( ! defined( 'ABSPATH' ) )", files.layoutMetrics],
  ['direct access guard in layout archive module', "if ( ! defined( 'ABSPATH' ) )", files.layoutArchive],
  ['direct access guard in social module', "if ( ! defined( 'ABSPATH' ) )", files.social],
  ['direct access guard in navigation entry', "if ( ! defined( 'ABSPATH' ) )", files.navigationEntry],
  ['direct access guard in navigation module', "if ( ! defined( 'ABSPATH' ) )", files.navigation],
  ['direct access guard in virtual module', "if ( ! defined( 'ABSPATH' ) )", files.virtual],
  ['direct access guard in walker module', "if ( ! defined( 'ABSPATH' ) )", files.walkers],
  ['direct access guard in content entry', "if ( ! defined( 'ABSPATH' ) )", files.contentEntry],
  ['direct access guard in home categories module', "if ( ! defined( 'ABSPATH' ) )", files.homeCategories],
  ['direct access guard in yml sponsor module', "if ( ! defined( 'ABSPATH' ) )", files.ymlSponsor],
  ['direct access guard in friends module', "if ( ! defined( 'ABSPATH' ) )", files.friends],
  ['direct access guard in GitHub projects module', "if ( ! defined( 'ABSPATH' ) )", files.githubProjects],
  ['direct access guard in post stats module', "if ( ! defined( 'ABSPATH' ) )", files.postStats]
]) {
  requireSnippet(label, snippet, haystack);
}

for (const hook of [
  "add_action( 'template_redirect', 'yneko_reimu_force_disabled_builtin_page_404', 0 );",
  "add_filter( 'post_link_category', 'yneko_reimu_post_link_parent_category', 10, 3 );",
  "add_filter( 'wp_nav_menu_objects', 'yneko_reimu_ensure_projects_menu_item', 10, 2 );",
  "add_filter( 'wp_nav_menu_objects', 'yneko_reimu_dedupe_builtin_menu_items', 20, 2 );",
  "add_action( 'wp', 'yneko_reimu_maybe_set_virtual_page', 1 );",
  "add_filter( 'template_include', 'yneko_reimu_virtual_template', 99 );",
  "add_shortcode( 'yneko_reimu_sponsor', 'yneko_reimu_sponsor_shortcode' );"
]) {
  requireSnippet('hook/filter/shortcode contract', hook);
}

for (const functionName of [
  'yneko_reimu_sidebar_position',
  'yneko_reimu_should_show_sidebar',
  'yneko_reimu_should_show_toc',
  'yneko_reimu_should_show_comments',
  'yneko_reimu_reading_time',
  'yneko_reimu_word_count',
  'yneko_reimu_excerpt',
  'yneko_reimu_archive_title',
  'yneko_reimu_archive_description',
  'yneko_reimu_footer_copyright',
  'yneko_reimu_social_definitions',
  'yneko_reimu_share_definitions',
  'yneko_reimu_social_links',
  'yneko_reimu_share_links',
  'yneko_reimu_share_context',
  'yneko_reimu_share_url',
  'yneko_reimu_default_nav_items',
  'yneko_reimu_nav_items',
  'yneko_reimu_nav_localized_url',
  'yneko_reimu_nav_localized_title',
  'yneko_reimu_virtual_pages',
  'yneko_reimu_virtual_page_slug',
  'yneko_reimu_virtual_template',
  'yneko_reimu_home_category_capsules',
  'yneko_reimu_yml_editor',
  'yneko_reimu_sponsor_html',
  'yneko_reimu_friend_items',
  'yneko_reimu_github_projects',
  'yneko_reimu_github_starred_projects',
  'yneko_reimu_render_taichi_svg'
]) {
  requireSnippet('public template helper function', `function ${functionName}`);
}

for (const className of [
  'class Yneko_Reimu_Menu_Walker extends Walker_Nav_Menu',
  'class Yneko_Reimu_Sidebar_Menu_Walker extends Walker_Nav_Menu'
]) {
  requireSnippet('public menu walker class', className, navigation);
}

for (const virtualSlug of [
  "'about'",
  "'friend'",
  "'projects'",
  "'archives'",
  "locate_template( 'virtual-page.php' )"
]) {
  requireSnippet('virtual page contract', virtualSlug, navigation);
}

for (const shareService of [
  "'facebook'",
  "'twitter'",
  "'linkedin'",
  "'reddit'",
  "'weibo'",
  "'qq'",
  "'weixin'",
  "'telegram'"
]) {
  requireSnippet('share service contract', shareService, files.social);
}

for (const socialService of [
  "'github'",
  "'bilibili'",
  "'xiaohongshu'",
  "'email'",
  "'rss'",
  "'twitter'",
  "'telegram'",
  "'youtube'",
  "'zhihu'"
]) {
  requireSnippet('social service contract', socialService, files.social);
}

for (const remoteContract of [
  "'yneko_reimu_github_token'",
  "'yneko_reimu_github_projects_' . md5( strtolower( $username ) )",
  "'yneko_reimu_github_starred_projects_' . md5( strtolower( $username ) )"
]) {
  requireSnippet('GitHub project contract', remoteContract, content);
}

for (const settingKey of [
  'yneko_reimu_sidebar_position',
  'yneko_reimu_show_toc',
  'yneko_reimu_comments',
  'yneko_reimu_social_',
  'yneko_reimu_share_',
  '_enabled',
  'yneko_reimu_nav_',
  '_label',
  'yneko_reimu_home_category_',
  '_title',
  'yneko_reimu_footer_copyright'
]) {
  requireSnippet('theme mod/settings key contract', settingKey);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[template-tags] module boundaries, helper functions, hooks, virtual pages, share/social, and GitHub project contracts are present.');
}
