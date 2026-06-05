import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const themeRoot = resolve(root, 'theme/Yneko-Reimu');
const files = {
  entry: await readFile(resolve(themeRoot, 'inc/customizer.php'), 'utf8'),
  panel: await readFile(resolve(themeRoot, 'inc/customizer/panel.php'), 'utf8'),
  preset: await readFile(resolve(themeRoot, 'inc/customizer/preset.php'), 'utf8'),
  sidebarWidgets: await readFile(resolve(themeRoot, 'inc/customizer/sidebar-widgets.php'), 'utf8'),
  visual: await readFile(resolve(themeRoot, 'inc/customizer/visual.php'), 'utf8'),
  visualAssets: await readFile(resolve(themeRoot, 'inc/customizer/visual-assets.php'), 'utf8'),
  typographyLayout: await readFile(resolve(themeRoot, 'inc/customizer/typography-layout.php'), 'utf8'),
  images: await readFile(resolve(themeRoot, 'inc/customizer/images.php'), 'utf8'),
  cards: await readFile(resolve(themeRoot, 'inc/customizer/cards.php'), 'utf8'),
  articles: await readFile(resolve(themeRoot, 'inc/customizer/articles.php'), 'utf8'),
  restoreDefaults: await readFile(resolve(themeRoot, 'inc/customizer/restore-defaults.php'), 'utf8'),
  social: await readFile(resolve(themeRoot, 'inc/customizer/social.php'), 'utf8'),
  footerVirtual: await readFile(resolve(themeRoot, 'inc/customizer/footer-virtual.php'), 'utf8'),
  customizerRestoreJs: await readFile(resolve(themeRoot, 'assets/src/customizer-restore-defaults.js'), 'utf8')
};
const source = Object.values(files).join('\n');
let failed = false;

function requireSnippet(label, snippet) {
  if (!source.includes(snippet)) {
    console.error(`[customizer] Missing ${label}: ${snippet}`);
    failed = true;
  }
}

for (const moduleImport of [
  "require_once get_template_directory() . '/inc/customizer/panel.php';",
  "require_once get_template_directory() . '/inc/customizer/preset.php';",
  "require_once get_template_directory() . '/inc/customizer/sidebar-widgets.php';",
  "require_once get_template_directory() . '/inc/customizer/visual.php';",
  "require_once get_template_directory() . '/inc/customizer/visual-assets.php';",
  "require_once get_template_directory() . '/inc/customizer/typography-layout.php';",
  "require_once get_template_directory() . '/inc/customizer/images.php';",
  "require_once get_template_directory() . '/inc/customizer/cards.php';",
  "require_once get_template_directory() . '/inc/customizer/articles.php';",
  "require_once get_template_directory() . '/inc/customizer/restore-defaults.php';",
  "require_once get_template_directory() . '/inc/customizer/social.php';",
  "require_once get_template_directory() . '/inc/customizer/footer-virtual.php';"
]) {
  requireSnippet('customizer module boundary', moduleImport);
}

for (const snippet of [
  "add_action( 'customize_register', 'yneko_reimu_customize_register' )",
  'function yneko_reimu_customize_register',
  'function yneko_reimu_register_customizer_sections',
  "yneko_reimu_settings_defaults()"
]) {
  requireSnippet('entry contract', snippet);
}

for (const section of [
  'yneko_reimu_panel',
  'yneko_reimu_clone_preset',
  'yneko_reimu_sidebar_widgets',
  'yneko_reimu_visual',
  'yneko_reimu_visual_assets',
  'yneko_reimu_typography_layout',
  'yneko_reimu_images',
  'yneko_reimu_cards',
  'yneko_reimu_articles',
  'yneko_reimu_restore_defaults',
  'yneko_reimu_social',
  'yneko_reimu_footer',
  'yneko_reimu_virtual_pages'
]) {
  requireSnippet('section/panel id', section);
}

for (const setting of [
  'yneko_reimu_strict_clone',
  'yneko_reimu_nav_',
  'yneko_reimu_home_category_',
  'yneko_reimu_player_position',
  'yneko_reimu_sidebar_widget_order',
  'yneko_reimu_accent_color',
  'yneko_reimu_dark_mode_default',
  'yneko_reimu_sidebar_position',
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
  'yneko_reimu_default_banner',
  'yneko_reimu_default_cover',
  'yneko_reimu_default_avatar',
  'yneko_reimu_search_background',
  'yneko_reimu_excerpt_length',
  'yneko_reimu_show_toc',
  'yneko_reimu_show_update_time',
  'yneko_reimu_code_expand_threshold',
  'yneko_reimu_customizer_reset_groups',
  'visual_assets',
  'typography_layout',
  'preview_images',
  'content_display',
  'yneko_reimu_social_share_heading',
  'yneko_reimu_social_sidebar_heading',
  'yneko_reimu_settings[github_url]',
  'yneko_reimu_settings[features][triangle_badge]',
  'yneko_reimu_about_intro',
  'yneko_reimu_footer_copyright',
  'yneko_reimu_footer_start_year',
  'yneko_reimu_footer_extra_attribution'
]) {
  requireSnippet('setting/control contract', setting);
}

for (const sanitizer of [
  'yneko_reimu_sanitize_checkbox',
  'yneko_reimu_sanitize_url_or_empty',
  'yneko_reimu_sanitize_select',
  'yneko_reimu_sanitize_positive_int',
  'yneko_reimu_sanitize_preloader_image_size',
  'yneko_reimu_sanitize_base_font_size',
  'yneko_reimu_sanitize_article_font_size',
  'yneko_reimu_sanitize_article_line_height',
  'yneko_reimu_sanitize_content_max_width',
  'yneko_reimu_sanitize_article_content_width',
  'yneko_reimu_sanitize_radius_px',
  'yneko_reimu_sanitize_customizer_restore_groups',
  'yneko_reimu_sanitize_sidebar_widget_order',
  'yneko_reimu_sanitize_social_url_or_empty',
  'sanitize_text_field',
  'sanitize_textarea_field',
  'sanitize_hex_color'
]) {
  requireSnippet('sanitizer contract', sanitizer);
}

for (const snippet of [
  'function yneko_reimu_customizer_restore_groups',
  'class Yneko_Reimu_Customize_Reset_Control',
  "add_action( 'customize_controls_enqueue_scripts', 'yneko_reimu_customizer_restore_defaults_assets' )",
  "add_action( 'customize_save_after', 'yneko_reimu_customizer_restore_defaults_after_save' )",
  'remove_theme_mod( $setting_id )',
  "wp_enqueue_script(\n\t\t'yneko-reimu-customizer-restore-defaults'",
  'YNEKO_REIMU_CUSTOMIZER_RESTORE',
  'customizer-restore-defaults.js',
  'window.confirm(formatConfirm(group.label || groupId))',
  'control.set(group.settings[settingId])',
  'markGroup(groupId)'
]) {
  requireSnippet('restore defaults contract', snippet);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[customizer] visual preview sections and key Customizer contracts are present.');
}
