<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_group_input( $input, $key ) {
	return isset( $input[ $key ] ) && is_array( $input[ $key ] ) ? $input[ $key ] : array();
}

function yneko_reimu_sanitize_settings_bool_group( $input, $defaults, $keys ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array();
	foreach ( $keys as $key ) {
		$clean[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
	}
	foreach ( $defaults as $key => $value ) {
		if ( ! array_key_exists( $key, $clean ) ) {
			$clean[ $key ] = is_array( $value ) ? $value : (string) $value;
		}
	}
	return $clean;
}

function yneko_reimu_sanitize_github_oauth_settings( $oauth ) {
	$oauth = is_array( $oauth ) ? $oauth : array();

	return array(
		'client_id'     => sanitize_text_field( $oauth['client_id'] ?? '' ),
		'client_secret' => sanitize_text_field( $oauth['client_secret'] ?? '' ),
		'callback_url'  => yneko_reimu_normalize_settings_url( $oauth['callback_url'] ?? '' ),
		'auto_create'   => ! empty( $oauth['auto_create'] ) ? '1' : '0',
	);
}

function yneko_reimu_sanitize_i18n_settings( $i18n, $defaults ) {
	$i18n                  = is_array( $i18n ) ? $i18n : array();
	$i18n_default          = function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : $defaults;
	$i18n_default_language = isset( $i18n['default'] ) && 'en_US' === $i18n['default'] ? 'en_US' : 'zh_CN';
	$i18n_prefix           = trim( sanitize_title( $i18n['en_prefix'] ?? $i18n_default['en_prefix'] ), '/' );

	return array(
		'enabled'   => ! empty( $i18n['enabled'] ) ? '1' : '0',
		'default'   => $i18n_default_language,
		'en_prefix' => $i18n_prefix ? $i18n_prefix : 'en',
		'zh_label'  => sanitize_text_field( $i18n['zh_label'] ?? $i18n_default['zh_label'] ),
		'en_label'  => sanitize_text_field( $i18n['en_label'] ?? $i18n_default['en_label'] ),
	);
}

function yneko_reimu_sanitize_search_settings( $search ) {
	$search = is_array( $search ) ? $search : array();

	return array(
		'algolia_enable'     => ! empty( $search['algolia_enable'] ) ? '1' : '0',
		'algolia_app_id'     => sanitize_text_field( $search['algolia_app_id'] ?? '' ),
		'algolia_api_key'    => sanitize_text_field( $search['algolia_api_key'] ?? '' ),
		'algolia_index_name' => sanitize_text_field( $search['algolia_index_name'] ?? '' ),
		'local_enable'       => ! empty( $search['local_enable'] ) ? '1' : '0',
		'local_json_url'     => yneko_reimu_normalize_settings_url( $search['local_json_url'] ?? '' ),
		'index_full_content' => ! empty( $search['index_full_content'] ) ? '1' : '0',
	);
}

function yneko_reimu_sanitize_player_settings( $player ) {
	$player          = is_array( $player ) ? $player : array();
	$player_loop     = in_array( $player['loop'] ?? 'all', array( 'all', 'one', 'none' ), true ) ? $player['loop'] : 'all';
	$player_order    = in_array( $player['order'] ?? 'list', array( 'list', 'random' ), true ) ? $player['order'] : 'list';
	$player_preload  = in_array( $player['preload'] ?? 'metadata', array( 'auto', 'metadata', 'none' ), true ) ? $player['preload'] : 'metadata';
	$player_volume   = (float) ( $player['volume'] ?? '0.7' );
	$player_volume   = max( 0, min( 1, $player_volume ) );
	$list_max_height = sanitize_text_field( $player['list_max_height'] ?? '320px' );
	$list_max_height = '' !== $list_max_height ? $list_max_height : '320px';

	return array(
		'aplayer_enable'  => ! empty( $player['aplayer_enable'] ) ? '1' : '0',
		'meting_enable'   => ! empty( $player['meting_enable'] ) ? '1' : '0',
		'fixed'           => ! empty( $player['fixed'] ) ? '1' : '0',
		'autoplay'        => ! empty( $player['autoplay'] ) ? '1' : '0',
		'mutex'           => ! empty( $player['mutex'] ) ? '1' : '0',
		'list_folded'     => ! empty( $player['list_folded'] ) ? '1' : '0',
		'loop'            => $player_loop,
		'order'           => $player_order,
		'preload'         => $player_preload,
		'volume'          => (string) $player_volume,
		'list_max_height' => $list_max_height,
		'lrc_type'        => max( 0, min( 3, absint( $player['lrc_type'] ?? 3 ) ) ),
		'meting_id'       => sanitize_text_field( $player['meting_id'] ?? '' ),
		'meting_server'   => sanitize_text_field( $player['meting_server'] ?? '' ),
		'meting_type'     => sanitize_text_field( $player['meting_type'] ?? '' ),
		'meting_auto'     => yneko_reimu_normalize_settings_url( $player['meting_auto'] ?? '' ),
	);
}

function yneko_reimu_sanitize_third_party_settings( $third_party, $defaults ) {
	$third_party = is_array( $third_party ) ? $third_party : array();

	return array(
		'live2d_enable'       => ! empty( $third_party['live2d_enable'] ) ? '1' : '0',
		'live2d_base_url'     => yneko_reimu_normalize_settings_url( $third_party['live2d_base_url'] ?? $defaults['live2d_base_url'] ),
		'live2d_api_base_url' => yneko_reimu_normalize_settings_url( $third_party['live2d_api_base_url'] ?? $defaults['live2d_api_base_url'] ),
		'vendor_cdn_base'     => yneko_reimu_normalize_settings_url( $third_party['vendor_cdn_base'] ?? $defaults['vendor_cdn_base'] ),
	);
}

function yneko_reimu_sanitize_external_comments_settings( $external_comments ) {
	$external_comments = is_array( $external_comments ) ? $external_comments : array();

	return array(
		'giscus_enable'      => ! empty( $external_comments['giscus_enable'] ) ? '1' : '0',
		'giscus_repo'        => sanitize_text_field( $external_comments['giscus_repo'] ?? '' ),
		'giscus_repo_id'     => sanitize_text_field( $external_comments['giscus_repo_id'] ?? '' ),
		'giscus_category'    => sanitize_text_field( $external_comments['giscus_category'] ?? '' ),
		'giscus_category_id' => sanitize_text_field( $external_comments['giscus_category_id'] ?? '' ),
		'utterances_enable'  => ! empty( $external_comments['utterances_enable'] ) ? '1' : '0',
		'utterances_repo'    => sanitize_text_field( $external_comments['utterances_repo'] ?? '' ),
		'disqus_enable'      => ! empty( $external_comments['disqus_enable'] ) ? '1' : '0',
		'disqus_shortname'   => sanitize_text_field( $external_comments['disqus_shortname'] ?? '' ),
		'waline_enable'      => ! empty( $external_comments['waline_enable'] ) ? '1' : '0',
		'waline_server_url'  => yneko_reimu_normalize_settings_url( $external_comments['waline_server_url'] ?? '' ),
		'twikoo_enable'      => ! empty( $external_comments['twikoo_enable'] ) ? '1' : '0',
		'twikoo_env_id'      => sanitize_text_field( $external_comments['twikoo_env_id'] ?? '' ),
		'valine_enable'      => ! empty( $external_comments['valine_enable'] ) ? '1' : '0',
		'valine_app_id'      => sanitize_text_field( $external_comments['valine_app_id'] ?? '' ),
		'valine_app_key'     => sanitize_text_field( $external_comments['valine_app_key'] ?? '' ),
		'valine_server_url'  => yneko_reimu_normalize_settings_url( $external_comments['valine_server_url'] ?? '' ),
	);
}
