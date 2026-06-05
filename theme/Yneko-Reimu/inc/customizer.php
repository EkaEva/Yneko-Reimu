<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/customizer/panel.php';
require_once get_template_directory() . '/inc/customizer/preset.php';
require_once get_template_directory() . '/inc/customizer/sidebar-widgets.php';
require_once get_template_directory() . '/inc/customizer/visual.php';
require_once get_template_directory() . '/inc/customizer/visual-assets.php';
require_once get_template_directory() . '/inc/customizer/typography-layout.php';
require_once get_template_directory() . '/inc/customizer/images.php';
require_once get_template_directory() . '/inc/customizer/cards.php';
require_once get_template_directory() . '/inc/customizer/articles.php';
require_once get_template_directory() . '/inc/customizer/social.php';
require_once get_template_directory() . '/inc/customizer/footer-virtual.php';

function yneko_reimu_customize_register( $wp_customize ) {
	yneko_reimu_register_customizer_sections( $wp_customize );
}
add_action( 'customize_register', 'yneko_reimu_customize_register' );

function yneko_reimu_register_customizer_sections( $wp_customize ) {
	$reimu_settings_defaults = function_exists( 'yneko_reimu_settings_defaults' ) ? yneko_reimu_settings_defaults() : array();

	yneko_reimu_register_customizer_panel( $wp_customize );
	yneko_reimu_register_customizer_preset_section( $wp_customize );
	yneko_reimu_register_customizer_sidebar_widgets_section( $wp_customize );
	yneko_reimu_register_customizer_visual_section( $wp_customize );
	yneko_reimu_register_customizer_visual_assets_section( $wp_customize );
	yneko_reimu_register_customizer_typography_layout_section( $wp_customize );
	yneko_reimu_register_customizer_images_section( $wp_customize );
	yneko_reimu_register_customizer_cards_section( $wp_customize );
	yneko_reimu_register_customizer_articles_section( $wp_customize );
	yneko_reimu_register_customizer_social_section( $wp_customize, $reimu_settings_defaults );
	yneko_reimu_register_customizer_footer_virtual_sections( $wp_customize );
}
