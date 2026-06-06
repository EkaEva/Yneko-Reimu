<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_defaults() {
	return array(
		'site_avatar_url'      => '',
		'author_avatar_url'    => '',
		'comment_avatar_url'   => '',
		'favicon_fallback_url' => '',
		'comment_upload'       => yneko_reimu_settings_comment_upload_defaults(),
		'user_badges'          => yneko_reimu_settings_user_badges_defaults(),
		'github_url'           => '',
		'friend_site'          => yneko_reimu_default_site_friend_info(),
		'friends'              => yneko_reimu_default_friend_items(),
		'sponsor_qr_url'       => '',
		'github_oauth'         => yneko_reimu_settings_github_oauth_defaults(),
		'auth_security'        => function_exists( 'yneko_reimu_auth_security_defaults' ) ? yneko_reimu_auth_security_defaults() : yneko_reimu_settings_auth_security_fallback_defaults(),
		'security'             => yneko_reimu_settings_security_defaults(),
		'builtin_pages'        => yneko_reimu_settings_builtin_pages_defaults(),
		'updates'              => yneko_reimu_settings_update_defaults(),
		'i18n'                 => function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : yneko_reimu_settings_i18n_fallback_defaults(),
		'search'               => yneko_reimu_settings_search_defaults(),
		'features'             => yneko_reimu_settings_feature_defaults(),
		'player'               => yneko_reimu_settings_player_defaults(),
		'third_party'          => yneko_reimu_settings_third_party_defaults(),
		'external_comments'    => yneko_reimu_settings_external_comments_defaults(),
		'music'                => array(),
	);
}

function yneko_reimu_settings_comment_upload_defaults() {
	return array(
		'enabled'                => '0',
		'image_enabled'          => '0',
		'gif_enabled'            => '0',
		'image_review'           => '0',
		'gif_review'             => '0',
		'image_max_mb'           => 1,
		'gif_max_mb'             => 3,
		'temp_cleanup_days'      => 7,
		'rejected_cleanup_hours' => 24,
		'avatar_enabled'         => '0',
		'avatar_review'          => '0',
		'avatar_max_mb'          => 1,
	);
}

function yneko_reimu_settings_github_oauth_defaults() {
	return array(
		'client_id'     => '',
		'client_secret' => '',
		'callback_url'  => '',
		'auto_create'   => '0',
	);
}

function yneko_reimu_settings_auth_security_fallback_defaults() {
	return array(
		'enabled'                  => '1',
		'protect_ajax'             => '1',
		'protect_wp_login'         => '1',
		'email_hour_limit'         => 3,
		'email_day_limit'          => 8,
		'ip_hour_limit'            => 10,
		'ip_day_limit'             => 30,
		'device_hour_limit'        => 5,
		'device_day_limit'         => 15,
		'global_day_limit'         => 100,
		'cooldown_seconds'         => 60,
		'global_warning_threshold' => 80,
		'email_alert_enabled'      => '0',
	);
}

function yneko_reimu_settings_security_defaults() {
	return array(
		'allow_svg_uploads'        => '1',
		'comment_ip_region_lookup' => '1',
	);
}

function yneko_reimu_settings_builtin_pages_defaults() {
	return array(
		'projects' => '1',
		'archives' => '1',
		'about'    => '1',
		'friend'   => '1',
	);
}

function yneko_reimu_settings_update_defaults() {
	return array(
		'github_release_check' => '1',
		'cache_minutes'        => 360,
	);
}

function yneko_reimu_settings_i18n_fallback_defaults() {
	return array(
		'enabled'   => '1',
		'default'   => 'zh_CN',
		'en_prefix' => 'en',
		'zh_label'  => '简体中文',
		'en_label'  => 'English',
	);
}

function yneko_reimu_settings_search_defaults() {
	return array(
		'algolia_enable'     => '0',
		'algolia_app_id'     => '',
		'algolia_api_key'    => '',
		'algolia_index_name' => '',
		'local_enable'       => '1',
		'local_json_url'     => '',
		'index_full_content' => '0',
	);
}

function yneko_reimu_settings_feature_defaults() {
	return array(
		'preloader_enable'   => '1',
		'top_enable'         => '1',
		'triangle_badge'     => '1',
		'firework_enable'    => '0',
		'pjax_enable'        => '0',
		'busuanzi_enable'    => '0',
		'katex_enable'       => '0',
		'photoswipe_enable'  => '0',
		'mermaid_enable'     => '0',
		'custom_cursor'      => '0',
		'show_admin_toolbar' => '0',
	);
}

function yneko_reimu_settings_player_defaults() {
	return array(
		'aplayer_enable'  => '0',
		'meting_enable'   => '0',
		'fixed'           => '0',
		'autoplay'        => '0',
		'mutex'           => '1',
		'list_folded'     => '1',
		'loop'            => 'all',
		'order'           => 'list',
		'preload'         => 'metadata',
		'volume'          => '0.7',
		'list_max_height' => '320px',
		'lrc_type'        => 3,
		'meting_id'       => '',
		'meting_server'   => '',
		'meting_type'     => '',
		'meting_auto'     => '',
	);
}

function yneko_reimu_settings_third_party_defaults() {
	return array(
		'live2d_enable'       => '0',
		'live2d_base_url'     => 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest',
		'live2d_api_base_url' => 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/',
		'vendor_cdn_base'     => 'https://cdn.jsdelivr.net/npm',
	);
}

function yneko_reimu_settings_external_comments_defaults() {
	return array(
		'giscus_enable'     => '0',
		'giscus_repo'       => '',
		'giscus_repo_id'    => '',
		'giscus_category'   => '',
		'giscus_category_id'=> '',
		'utterances_enable' => '0',
		'utterances_repo'   => '',
		'disqus_enable'     => '0',
		'disqus_shortname'  => '',
		'waline_enable'     => '0',
		'waline_server_url' => '',
		'twikoo_enable'     => '0',
		'twikoo_env_id'     => '',
		'valine_enable'     => '0',
		'valine_app_id'     => '',
		'valine_app_key'    => '',
		'valine_server_url' => '',
	);
}
