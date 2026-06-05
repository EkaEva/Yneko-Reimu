<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_defaults() {
	return array(
		'site_avatar_url'   => '',
		'author_avatar_url' => '',
		'comment_avatar_url'=> '',
		'favicon_fallback_url' => '',
		'comment_upload'    => array(
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
		),
		'user_badges'       => array(
			'enabled'        => '1',
			'review_enabled' => '0',
			'blocklist'      => '',
			'avatar_frames'  => array(
				'enabled' => '0',
				'frames'  => array(
					'owner'       => yneko_reimu_default_avatar_frame_url(),
					'admin'       => yneko_reimu_default_avatar_frame_url(),
					'editor'      => yneko_reimu_default_avatar_frame_url(),
					'author'      => yneko_reimu_default_avatar_frame_url(),
					'contributor' => yneko_reimu_default_avatar_frame_url(),
					'yko'         => yneko_reimu_default_avatar_frame_url(),
					'subscriber'  => yneko_reimu_default_avatar_frame_url(),
				),
			),
			'special'        => array(
				'owner' => array(
					'enabled' => '1',
					'zh'      => '站长',
					'en'      => 'Owner',
				),
				'admin' => array(
					'enabled' => '1',
					'zh'      => '管理员',
					'en'      => 'Admin',
				),
				'yko'   => array(
					'enabled' => '1',
					'zh'      => 'Yko',
					'en'      => 'Yko',
				),
				'subscriber' => array(
					'enabled' => '1',
					'zh'      => '订阅者',
					'en'      => 'Subscriber',
				),
				'contributor' => array(
					'enabled' => '1',
					'zh'      => '贡献者',
					'en'      => 'Contributor',
				),
				'author' => array(
					'enabled' => '1',
					'zh'      => '作者',
					'en'      => 'Author',
				),
				'editor' => array(
					'enabled' => '1',
					'zh'      => '编辑',
					'en'      => 'Editor',
				),
			),
		),
		'github_url'        => '',
		'friend_site'       => yneko_reimu_default_site_friend_info(),
		'friends'           => yneko_reimu_default_friend_items(),
		'sponsor_qr_url'    => '',
		'github_oauth'      => array(
			'client_id'     => '',
			'client_secret' => '',
			'callback_url'  => '',
			'auto_create'   => '0',
		),
		'auth_security'     => function_exists( 'yneko_reimu_auth_security_defaults' ) ? yneko_reimu_auth_security_defaults() : array(
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
		),
		'security'          => array(
			'allow_svg_uploads'        => '1',
			'comment_ip_region_lookup' => '1',
		),
		'builtin_pages'     => array(
			'projects' => '1',
			'archives' => '1',
			'about'    => '1',
			'friend'   => '1',
		),
		'i18n'              => function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : array(
			'enabled'   => '1',
			'default'   => 'zh_CN',
			'en_prefix' => 'en',
			'zh_label'  => '简体中文',
			'en_label'  => 'English',
		),
		'search'            => array(
			'algolia_enable'       => '0',
			'algolia_app_id'       => '',
			'algolia_api_key'      => '',
			'algolia_index_name'   => '',
			'local_enable'         => '1',
			'local_json_url'       => '',
			'index_full_content'   => '0',
		),
		'features'          => array(
			'preloader_enable'     => '1',
			'top_enable'           => '1',
			'triangle_badge'       => '1',
			'firework_enable'      => '0',
			'pjax_enable'          => '0',
			'busuanzi_enable'      => '0',
			'katex_enable'         => '0',
			'photoswipe_enable'    => '0',
			'mermaid_enable'       => '0',
			'custom_cursor'        => '0',
			'show_admin_toolbar'   => '0',
		),
		'player'            => array(
			'aplayer_enable'       => '0',
			'meting_enable'        => '0',
			'fixed'                => '0',
			'autoplay'             => '0',
			'mutex'                => '1',
			'list_folded'          => '1',
			'loop'                 => 'all',
			'order'                => 'list',
			'preload'              => 'metadata',
			'volume'               => '0.7',
			'list_max_height'      => '320px',
			'lrc_type'             => 3,
			'meting_id'            => '',
			'meting_server'        => '',
			'meting_type'          => '',
			'meting_auto'          => '',
		),
		'third_party'       => array(
			'live2d_enable'        => '0',
			'live2d_base_url'      => 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest',
			'live2d_api_base_url'  => 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/',
			'vendor_cdn_base'      => 'https://cdn.jsdelivr.net/npm',
		),
		'external_comments' => array(
			'giscus_enable'        => '0',
			'giscus_repo'          => '',
			'giscus_repo_id'       => '',
			'giscus_category'      => '',
			'giscus_category_id'   => '',
			'utterances_enable'    => '0',
			'utterances_repo'      => '',
			'disqus_enable'        => '0',
			'disqus_shortname'     => '',
			'waline_enable'        => '0',
			'waline_server_url'    => '',
			'twikoo_enable'        => '0',
			'twikoo_env_id'        => '',
			'valine_enable'        => '0',
			'valine_app_id'        => '',
			'valine_app_key'       => '',
			'valine_server_url'    => '',
		),
		'music'             => array(),
	);
}

function yneko_reimu_default_avatar_frame_url() {
	return function_exists( 'yneko_reimu_asset_uri' ) ? yneko_reimu_asset_uri( 'assets/images/avatar-frame.png' ) : get_template_directory_uri() . '/assets/images/avatar-frame.png';
}

function yneko_reimu_user_badge_base_definitions() {
	return array(
		'owner'       => array(
			'title_zh' => '站长',
			'title_en' => 'Owner',
			'zh'       => '站长',
			'en'       => 'Owner',
			'desc_zh'  => '默认分配给站点第一位管理员。',
			'desc_en'  => 'Assigned to the first administrator by default.',
		),
		'admin'       => array(
			'title_zh' => '管理员',
			'title_en' => 'Admin',
			'zh'       => '管理员',
			'en'       => 'Admin',
			'desc_zh'  => '默认分配给除站长外的管理员。',
			'desc_en'  => 'Assigned to administrators except the site owner.',
		),
		'editor'      => array(
			'title_zh' => '编辑',
			'title_en' => 'Editor',
			'zh'       => '编辑',
			'en'       => 'Editor',
			'desc_zh'  => '默认分配给 WordPress 编辑角色。',
			'desc_en'  => 'Assigned to the WordPress Editor role.',
		),
		'author'      => array(
			'title_zh' => '作者',
			'title_en' => 'Author',
			'zh'       => '作者',
			'en'       => 'Author',
			'desc_zh'  => '默认分配给 WordPress 作者角色。',
			'desc_en'  => 'Assigned to the WordPress Author role.',
		),
		'contributor' => array(
			'title_zh' => '贡献者',
			'title_en' => 'Contributor',
			'zh'       => '贡献者',
			'en'       => 'Contributor',
			'desc_zh'  => '默认分配给 WordPress 贡献者角色。',
			'desc_en'  => 'Assigned to the WordPress Contributor role.',
		),
		'yko'         => array(
			'title_zh' => '会员',
			'title_en' => 'Member',
			'zh'       => 'Yko',
			'en'       => 'Yko',
			'desc_zh'  => '登录用户都会拥有这个基础标签。',
			'desc_en'  => 'Assigned to every logged-in user.',
		),
		'subscriber'  => array(
			'title_zh' => '订阅者',
			'title_en' => 'Subscriber',
			'zh'       => '订阅者',
			'en'       => 'Subscriber',
			'desc_zh'  => '默认分配给 WordPress 订阅者角色。',
			'desc_en'  => 'Assigned to the WordPress Subscriber role.',
		),
	);
}

function yneko_reimu_default_site_friend_info() {
	return array(
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'desc'  => get_bloginfo( 'description' ),
		'image' => '',
	);
}

function yneko_reimu_default_friend_items() {
	return array(
		array(
			'name'  => 'EkaEva',
			'url'   => 'https://github.com/EkaEva',
			'desc'  => __( 'Yneko-Reimu 主题作者', 'yneko-reimu' ),
			'image' => '',
		),
		array(
			'name'  => '拔剑Sketon',
			'url'   => 'https://d-sketon.github.io/',
			'desc'  => __( 'hexo-theme-reimu 原作者', 'yneko-reimu' ),
			'image' => 'https://d-sketon.github.io/avatar/avatar.webp',
		),
		array(
			'name'  => '天羊EdSky',
			'url'   => 'https://space.bilibili.com/16573583',
			'desc'  => __( '莉莉概念光标作者', 'yneko-reimu' ),
			'image' => yneko_reimu_asset_uri( 'assets/images/tianyang-edsky.jpg' ),
		),
	);
}
