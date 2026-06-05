<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_enqueue_main_runtime( $asset_strategy, $main_script_deps, $config ) {
	wp_enqueue_script( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.js', array_values( array_unique( $main_script_deps ) ), yneko_reimu_asset_version( 'assets/dist/reimu.js' ), true );
	if ( function_exists( 'wp_script_add_data' ) && ! empty( $asset_strategy['script_strategy'] ) ) {
		wp_script_add_data( 'yneko-reimu-main', 'strategy', sanitize_key( $asset_strategy['script_strategy'] ) );
	}
	wp_add_inline_script( 'yneko-reimu-main', 'window.REIMU_CONFIG=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

function yneko_reimu_enqueue_assets() {
	$asset_strategy    = yneko_reimu_asset_strategy();
	$aplayer_audio     = yneko_reimu_normalize_aplayer_audio( yneko_reimu_settings_music_items() );
	$enable_aplayer    = yneko_reimu_player_enabled( $aplayer_audio );
	$current_language  = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : get_locale();
	$search_settings   = function_exists( 'yneko_reimu_settings_search' ) ? yneko_reimu_settings_search() : array();
	$player_settings   = function_exists( 'yneko_reimu_settings_player' ) ? yneko_reimu_settings_player() : array();
	$external_comments = function_exists( 'yneko_reimu_settings_external_comments' ) ? yneko_reimu_settings_external_comments() : array();

	yneko_reimu_enqueue_theme_styles( $asset_strategy, $enable_aplayer );

	$search_result    = yneko_reimu_build_search_config( $current_language, $search_settings );
	$config           = yneko_reimu_build_frontend_config( $current_language, $search_result['search'], $aplayer_audio, $player_settings );
	$main_script_deps = array_merge(
		$search_result['deps'],
		yneko_reimu_enqueue_optional_vendor_assets( $enable_aplayer, $external_comments )
	);

	yneko_reimu_enqueue_main_runtime( $asset_strategy, $main_script_deps, $config );
}
add_action( 'wp_enqueue_scripts', 'yneko_reimu_enqueue_assets' );
