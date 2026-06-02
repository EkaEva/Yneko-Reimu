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
	if ( function_exists( 'yneko_reimu_settings_feature_enabled' ) ) {
		return yneko_reimu_settings_feature_enabled( $key, yneko_reimu_feature_default( $key, $fallback ) );
	}

	return (bool) yneko_reimu_get_theme_mod( $key, yneko_reimu_feature_default( $key, $fallback ) );
}

function yneko_reimu_busuanzi_enabled() {
	return yneko_reimu_feature_enabled( 'yneko_reimu_busuanzi_enable', false );
}

function yneko_reimu_aplayer_enabled( $audio = null ) {
	if ( ! yneko_reimu_feature_enabled( 'yneko_reimu_player_aplayer_enable', false ) ) {
		return false;
	}

	if ( null === $audio ) {
		$audio = function_exists( 'yneko_reimu_settings_music_items' ) ? yneko_reimu_settings_music_items() : array();
	}

	return ! empty( $audio );
}

function yneko_reimu_meting_config() {
	$player = function_exists( 'yneko_reimu_settings_player' ) ? yneko_reimu_settings_player() : array();
	$auto   = trim( (string) ( $player['meting_auto'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_meting_auto', '' ) ) );
	$id     = trim( (string) ( $player['meting_id'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_meting_id', '' ) ) );
	$server = trim( (string) ( $player['meting_server'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_meting_server', '' ) ) );
	$type   = trim( (string) ( $player['meting_type'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_meting_type', '' ) ) );

	if ( '' === $auto && ( '' === $id || '' === $server || '' === $type ) ) {
		return array();
	}

	return array(
		'auto'   => $auto,
		'id'     => $id,
		'server' => $server,
		'type'   => $type,
	);
}

function yneko_reimu_meting_enabled() {
	return yneko_reimu_feature_enabled( 'yneko_reimu_player_meting_enable', false ) && ! empty( yneko_reimu_meting_config() );
}

function yneko_reimu_player_enabled( $audio = null ) {
	return yneko_reimu_aplayer_enabled( $audio ) || yneko_reimu_meting_enabled();
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
