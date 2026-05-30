<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_feature_defaults() {
	return apply_filters(
		'yneko_reimu_feature_defaults',
		array(
			'yneko_reimu_preloader_enable'       => true,
			'yneko_reimu_top_enable'             => true,
			'yneko_reimu_triangle_badge'         => true,
			'yneko_reimu_firework_enable'        => false,
			'yneko_reimu_pjax_enable'            => false,
			'yneko_reimu_busuanzi_enable'        => false,
			'yneko_reimu_player_aplayer_enable'  => false,
			'yneko_reimu_player_meting_enable'   => false,
			'yneko_reimu_live2d_widgets_enable'  => false,
			'yneko_reimu_katex_enable'           => false,
			'yneko_reimu_photoswipe_enable'      => false,
			'yneko_reimu_mermaid_enable'         => false,
			'yneko_reimu_algolia_enable'         => false,
			'yneko_reimu_generator_search_enable' => true,
			'yneko_reimu_custom_cursor'          => false,
		)
	);
}

function yneko_reimu_feature_default( $name, $fallback = false ) {
	$defaults = yneko_reimu_feature_defaults();
	return array_key_exists( $name, $defaults ) ? $defaults[ $name ] : $fallback;
}

function yneko_reimu_feature_enabled( $name, $fallback = false ) {
	$key = 0 === strpos( $name, 'yneko_reimu_' ) ? $name : 'yneko_reimu_' . $name;
	return (bool) yneko_reimu_get_theme_mod( $key, yneko_reimu_feature_default( $key, $fallback ) );
}

function yneko_reimu_asset_strategy() {
	return apply_filters(
		'yneko_reimu_asset_strategy',
		array(
			'font_display'            => true,
			'preload_cursor_images'   => false,
			'preload_cursor_variants' => array( 'lily-normal.png', 'lily-link.png' ),
			'script_strategy'         => 'defer',
		)
	);
}
