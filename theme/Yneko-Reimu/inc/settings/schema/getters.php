<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings() {
	$settings = get_option( 'yneko_reimu_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$defaults = yneko_reimu_settings_defaults();

	foreach ( array( 'friends', 'music' ) as $list_key ) {
		if ( array_key_exists( $list_key, $settings ) && is_array( $settings[ $list_key ] ) && ! $settings[ $list_key ] ) {
			$defaults[ $list_key ] = array();
		}
	}

	return array_replace_recursive( $defaults, $settings );
}

function yneko_reimu_setting( $key, $default = '' ) {
	$settings = yneko_reimu_settings();
	return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
}

function yneko_reimu_settings_github_url() {
	$github = yneko_reimu_setting( 'github_url', '' );
	if ( $github ) {
		return esc_url_raw( $github );
	}

	return esc_url_raw( yneko_reimu_get_theme_mod( 'yneko_reimu_social_github', '' ) );
}

function yneko_reimu_settings_sponsor_qr_url() {
	$qr = yneko_reimu_setting( 'sponsor_qr_url', '' );
	if ( $qr ) {
		return esc_url_raw( $qr );
	}

	return esc_url_raw( yneko_reimu_get_theme_mod( 'yneko_reimu_sponsor_qr', '' ) );
}

function yneko_reimu_settings_comment_avatar_url() {
	$avatar = yneko_reimu_setting( 'comment_avatar_url', '' );
	return $avatar ? esc_url_raw( $avatar ) : '';
}

function yneko_reimu_settings_comment_upload() {
	$settings = yneko_reimu_settings();
	$upload   = isset( $settings['comment_upload'] ) && is_array( $settings['comment_upload'] ) ? $settings['comment_upload'] : array();
	if ( '1' === (string) ( $upload['enabled'] ?? '0' ) ) {
		$upload['image_enabled'] = $upload['image_enabled'] ?? '1';
		$upload['gif_enabled']   = $upload['gif_enabled'] ?? '1';
	}

	return wp_parse_args(
		$upload,
		array(
			'enabled'      => '0',
			'image_enabled'=> '0',
			'gif_enabled'  => '0',
			'image_review' => '0',
			'gif_review'   => '0',
			'image_max_mb' => 1,
			'gif_max_mb'   => 3,
			'temp_cleanup_days' => 7,
			'rejected_cleanup_hours' => 24,
			'avatar_enabled'=> '0',
			'avatar_review'=> '0',
			'avatar_max_mb'=> 1,
		)
	);
}

function yneko_reimu_settings_user_badges() {
	$settings = yneko_reimu_settings();
	$defaults = yneko_reimu_settings_defaults();
	$badges   = isset( $settings['user_badges'] ) && is_array( $settings['user_badges'] ) ? $settings['user_badges'] : array();
	return yneko_reimu_sanitize_user_badges_settings( wp_parse_args( $badges, $defaults['user_badges'] ), $defaults['user_badges'] );
}

function yneko_reimu_settings_friend_items() {
	$raw = get_option( 'yneko_reimu_settings', null );
	if ( is_array( $raw ) && array_key_exists( 'friends', $raw ) ) {
		return yneko_reimu_sanitize_friend_items( $raw['friends'] );
	}

	$settings = yneko_reimu_settings();
	$friends  = isset( $settings['friends'] ) && is_array( $settings['friends'] ) ? $settings['friends'] : array();

	if ( $friends ) {
		return yneko_reimu_sanitize_friend_items( $friends );
	}

	return yneko_reimu_default_friend_items();
}

function yneko_reimu_settings_site_friend_info() {
	$settings = yneko_reimu_settings();
	$site     = yneko_reimu_sanitize_site_friend_info( $settings['friend_site'] ?? array() );

	if ( ! $site['image'] ) {
		$site['image'] = yneko_reimu_get_default_avatar_url();
	}

	return $site;
}

function yneko_reimu_settings_music_items() {
	$raw = get_option( 'yneko_reimu_settings', null );
	if ( is_array( $raw ) && array_key_exists( 'music', $raw ) ) {
		return yneko_reimu_sanitize_music_items( $raw['music'] );
	}

	$settings = yneko_reimu_settings();
	$music    = isset( $settings['music'] ) && is_array( $settings['music'] ) ? $settings['music'] : array();

	if ( $music ) {
		return yneko_reimu_sanitize_music_items( $music );
	}

	$legacy = yneko_reimu_json_theme_mod( 'yneko_reimu_aplayer_audio_json', '' );
	return $legacy ? yneko_reimu_sanitize_music_items( $legacy ) : array();
}

function yneko_reimu_settings_github_oauth() {
	$settings = yneko_reimu_settings();
	$oauth    = isset( $settings['github_oauth'] ) && is_array( $settings['github_oauth'] ) ? $settings['github_oauth'] : array();
	$legacy   = get_option( 'yneko_reimu_github_login_options', array() );

	if ( is_array( $legacy ) ) {
		$oauth = yneko_reimu_merge_github_oauth_fallback( $oauth, $legacy );
	}

	if ( empty( $oauth['client_id'] ) || empty( $oauth['client_secret'] ) ) {
		$old_legacy = get_option( 'yneko_github_login_options', array() );
		if ( is_array( $old_legacy ) ) {
			$oauth = yneko_reimu_merge_github_oauth_fallback( $oauth, $old_legacy );
		}
	}

	return wp_parse_args(
		$oauth,
		array(
			'client_id'     => '',
			'client_secret' => '',
			'callback_url'  => '',
			'auto_create'   => '0',
		)
	);
}


function yneko_reimu_settings_group( $group ) {
	$settings = yneko_reimu_settings();
	$defaults = yneko_reimu_settings_defaults();
	$value    = isset( $settings[ $group ] ) && is_array( $settings[ $group ] ) ? $settings[ $group ] : array();
	$default  = isset( $defaults[ $group ] ) && is_array( $defaults[ $group ] ) ? $defaults[ $group ] : array();
	return array_replace_recursive( $default, $value );
}

function yneko_reimu_settings_security() {
	return yneko_reimu_settings_group( 'security' );
}

function yneko_reimu_security_allow_svg_uploads() {
	$security = yneko_reimu_settings_security();
	return '1' === (string) ( $security['allow_svg_uploads'] ?? '1' );
}

function yneko_reimu_security_comment_ip_region_lookup() {
	$security = yneko_reimu_settings_security();
	return '1' === (string) ( $security['comment_ip_region_lookup'] ?? '1' );
}

function yneko_reimu_builtin_page_slugs() {
	return array( 'projects', 'archives', 'about', 'friend' );
}

function yneko_reimu_settings_builtin_pages() {
	return yneko_reimu_settings_group( 'builtin_pages' );
}

function yneko_reimu_builtin_page_enabled( $slug ) {
	$slug = sanitize_key( $slug );
	if ( ! in_array( $slug, yneko_reimu_builtin_page_slugs(), true ) ) {
		return true;
	}

	$pages = yneko_reimu_settings_builtin_pages();
	return '1' === (string) ( $pages[ $slug ] ?? '1' );
}
