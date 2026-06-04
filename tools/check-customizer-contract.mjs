import { readFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const customizerPath = resolve(root, 'theme/Yneko-Reimu/inc/customizer.php');
const source = await readFile(customizerPath, 'utf8');
let failed = false;

function requireSnippet(label, snippet) {
  if (!source.includes(snippet)) {
    console.error(`[customizer] Missing ${label}: ${snippet}`);
    failed = true;
  }
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
  'yneko_reimu_images',
  'yneko_reimu_cards',
  'yneko_reimu_articles',
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
  'yneko_reimu_default_banner',
  'yneko_reimu_default_cover',
  'yneko_reimu_default_avatar',
  'yneko_reimu_search_background',
  'yneko_reimu_excerpt_length',
  'yneko_reimu_show_toc',
  'yneko_reimu_show_update_time',
  'yneko_reimu_code_expand_threshold',
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
  'yneko_reimu_sanitize_sidebar_widget_order',
  'yneko_reimu_sanitize_social_url_or_empty',
  'sanitize_text_field',
  'sanitize_textarea_field',
  'sanitize_hex_color'
]) {
  requireSnippet('sanitizer contract', sanitizer);
}

if (failed) {
  process.exitCode = 1;
} else {
  console.log('[customizer] visual preview sections and key Customizer contracts are present.');
}
