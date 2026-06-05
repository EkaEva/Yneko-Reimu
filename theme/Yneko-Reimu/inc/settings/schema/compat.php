<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_merge_github_oauth_fallback( $oauth, $fallback ) {
	$oauth    = is_array( $oauth ) ? $oauth : array();
	$fallback = is_array( $fallback ) ? $fallback : array();

	foreach ( array( 'client_id', 'client_secret', 'callback_url', 'auto_create' ) as $key ) {
		if ( empty( $oauth[ $key ] ) && isset( $fallback[ $key ] ) && '' !== $fallback[ $key ] ) {
			$oauth[ $key ] = $fallback[ $key ];
		}
	}

	return $oauth;
}

function yneko_reimu_settings_raw_has_group( $group ) {
	$raw = get_option( 'yneko_reimu_settings', null );
	return is_array( $raw ) && array_key_exists( $group, $raw ) && is_array( $raw[ $group ] );
}

function yneko_reimu_settings_feature_enabled( $theme_mod_key, $fallback = false ) {
	$map = array(
		'yneko_reimu_preloader_enable'       => 'preloader_enable',
		'yneko_reimu_top_enable'             => 'top_enable',
		'yneko_reimu_triangle_badge'         => 'triangle_badge',
		'yneko_reimu_firework_enable'        => 'firework_enable',
		'yneko_reimu_pjax_enable'            => 'pjax_enable',
		'yneko_reimu_busuanzi_enable'        => 'busuanzi_enable',
		'yneko_reimu_katex_enable'           => 'katex_enable',
		'yneko_reimu_photoswipe_enable'      => 'photoswipe_enable',
		'yneko_reimu_mermaid_enable'         => 'mermaid_enable',
		'yneko_reimu_custom_cursor'          => 'custom_cursor',
		'yneko_reimu_algolia_enable'         => array( 'search', 'algolia_enable' ),
		'yneko_reimu_generator_search_enable'=> array( 'search', 'local_enable' ),
		'yneko_reimu_player_aplayer_enable'  => array( 'player', 'aplayer_enable' ),
		'yneko_reimu_player_meting_enable'   => array( 'player', 'meting_enable' ),
		'yneko_reimu_live2d_widgets_enable'  => array( 'third_party', 'live2d_enable' ),
	);

	if ( ! isset( $map[ $theme_mod_key ] ) ) {
		return (bool) yneko_reimu_get_theme_mod( $theme_mod_key, $fallback );
	}

	$target = $map[ $theme_mod_key ];
	$group  = is_array( $target ) ? $target[0] : 'features';
	$key    = is_array( $target ) ? $target[1] : $target;
	if ( yneko_reimu_settings_raw_has_group( $group ) ) {
		$settings = yneko_reimu_settings_group( $group );
		return '1' === (string) ( $settings[ $key ] ?? '0' );
	}

	return (bool) yneko_reimu_get_theme_mod( $theme_mod_key, $fallback );
}

function yneko_reimu_settings_search() {
	$settings = yneko_reimu_settings_group( 'search' );
	if ( ! yneko_reimu_settings_raw_has_group( 'search' ) ) {
		$settings['algolia_enable']     = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_algolia_enable', false );
		$settings['algolia_app_id']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_algolia_app_id', '' );
		$settings['algolia_api_key']    = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_algolia_api_key', '' );
		$settings['algolia_index_name'] = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_algolia_index_name', '' );
		$settings['local_enable']       = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_generator_search_enable', true );
		$settings['local_json_url']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_local_search_json', '' );
		$settings['index_full_content'] = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_search_index_full_content', false );
	}
	return $settings;
}

function yneko_reimu_settings_features() {
	$settings = yneko_reimu_settings_group( 'features' );
	if ( ! yneko_reimu_settings_raw_has_group( 'features' ) ) {
		foreach (
			array(
				'preloader_enable' => array( 'yneko_reimu_preloader_enable', true ),
				'top_enable'       => array( 'yneko_reimu_top_enable', true ),
				'triangle_badge'   => array( 'yneko_reimu_triangle_badge', true ),
				'firework_enable'  => array( 'yneko_reimu_firework_enable', false ),
				'pjax_enable'      => array( 'yneko_reimu_pjax_enable', false ),
				'busuanzi_enable'  => array( 'yneko_reimu_busuanzi_enable', false ),
				'katex_enable'     => array( 'yneko_reimu_katex_enable', false ),
				'photoswipe_enable'=> array( 'yneko_reimu_photoswipe_enable', false ),
				'mermaid_enable'   => array( 'yneko_reimu_mermaid_enable', false ),
				'custom_cursor'    => array( 'yneko_reimu_custom_cursor', false ),
				'show_admin_toolbar' => array( 'yneko_reimu_show_admin_toolbar', false ),
			) as $key => $legacy
		) {
			$settings[ $key ] = yneko_reimu_settings_theme_mod_bool( $legacy[0], $legacy[1] );
		}
	}
	return $settings;
}

function yneko_reimu_settings_player() {
	$settings = yneko_reimu_settings_group( 'player' );
	if ( ! yneko_reimu_settings_raw_has_group( 'player' ) ) {
		$settings['aplayer_enable']  = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_player_aplayer_enable', false );
		$settings['meting_enable']   = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_player_meting_enable', false );
		$settings['fixed']           = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_aplayer_fixed', false );
		$settings['autoplay']        = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_aplayer_autoplay', false );
		$settings['mutex']           = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_aplayer_mutex', true );
		$settings['list_folded']     = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_aplayer_list_folded', true );
		$settings['loop']            = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_aplayer_loop', 'all' );
		$settings['order']           = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_aplayer_order', 'list' );
		$settings['preload']         = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_aplayer_preload', 'metadata' );
		$settings['volume']          = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_aplayer_volume', '0.7' );
		$settings['list_max_height'] = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_aplayer_list_max_height', '320px' );
		$settings['lrc_type']        = absint( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_lrc_type', 3 ) );
		$settings['meting_id']       = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_meting_id', '' );
		$settings['meting_server']   = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_meting_server', '' );
		$settings['meting_type']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_meting_type', '' );
		$settings['meting_auto']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_meting_auto', '' );
	}
	return $settings;
}

function yneko_reimu_settings_third_party() {
	$settings = yneko_reimu_settings_group( 'third_party' );
	if ( ! yneko_reimu_settings_raw_has_group( 'third_party' ) ) {
		$settings['live2d_enable']       = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_live2d_widgets_enable', false );
		$settings['live2d_base_url']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_live2d_base_url', $settings['live2d_base_url'] );
		$settings['live2d_api_base_url'] = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_live2d_api_base_url', $settings['live2d_api_base_url'] );
		$settings['vendor_cdn_base']     = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_vendor_cdn_base', $settings['vendor_cdn_base'] );
	}
	return $settings;
}

function yneko_reimu_settings_external_comments() {
	$settings = yneko_reimu_settings_group( 'external_comments' );
	if ( ! yneko_reimu_settings_raw_has_group( 'external_comments' ) ) {
		foreach (
			array(
				'giscus_enable' => false,
				'utterances_enable' => false,
				'disqus_enable' => false,
				'waline_enable' => false,
				'twikoo_enable' => false,
				'valine_enable' => false,
			) as $key => $default
		) {
			$settings[ $key ] = yneko_reimu_settings_theme_mod_bool( 'yneko_reimu_' . $key, $default );
		}
		foreach (
			array(
				'giscus_repo',
				'giscus_repo_id',
				'giscus_category',
				'giscus_category_id',
				'utterances_repo',
				'disqus_shortname',
				'waline_server_url',
				'twikoo_env_id',
				'valine_app_id',
				'valine_app_key',
				'valine_server_url',
			) as $key
		) {
			$settings[ $key ] = yneko_reimu_settings_theme_mod_text( 'yneko_reimu_' . $key, $settings[ $key ] ?? '' );
		}
	}
	return $settings;
}
