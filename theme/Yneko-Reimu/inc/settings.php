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

function yneko_reimu_normalize_settings_url( $url ) {
	$url = trim( (string) $url );
	return '' === $url ? '' : esc_url_raw( $url );
}

function yneko_reimu_normalize_png_webp_url( $url ) {
	$url = yneko_reimu_normalize_settings_url( $url );
	if ( '' === $url ) {
		return '';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	if ( ! preg_match( '/\.(?:png|webp)$/i', $path ) ) {
		return '';
	}

	return $url;
}

function yneko_reimu_normalize_png_jpeg_url( $url ) {
	$url = yneko_reimu_normalize_settings_url( $url );
	if ( '' === $url ) {
		return '';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	if ( ! preg_match( '/\.(?:png|jpe?g)$/i', $path ) ) {
		return '';
	}

	return $url;
}

function yneko_reimu_normalize_avatar_frame_url( $url ) {
	$url = yneko_reimu_normalize_settings_url( $url );
	if ( '' === $url ) {
		return '';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	if ( ! preg_match( '/\.(?:png|webp|avif)$/i', $path ) ) {
		return '';
	}

	return $url;
}

function yneko_reimu_sanitize_site_friend_info( $item ) {
	$item     = is_array( $item ) ? $item : array();
	$defaults = yneko_reimu_default_site_friend_info();
	$name     = sanitize_text_field( $item['name'] ?? $defaults['name'] );
	$url      = yneko_reimu_normalize_settings_url( $item['url'] ?? $defaults['url'] );
	$desc     = sanitize_text_field( $item['desc'] ?? $defaults['desc'] );
	$image    = yneko_reimu_normalize_png_webp_url( $item['image'] ?? '' );

	return array(
		'name'  => '' !== $name ? $name : $defaults['name'],
		'url'   => $url ? $url : $defaults['url'],
		'desc'  => $desc,
		'image' => $image,
	);
}

function yneko_reimu_sanitize_friend_items( $items ) {
	$clean = array();
	$seen  = array();

	if ( ! is_array( $items ) ) {
		return $clean;
	}

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$name = sanitize_text_field( $item['name'] ?? '' );
		$url  = yneko_reimu_normalize_settings_url( $item['url'] ?? '' );
		$desc = sanitize_text_field( $item['desc'] ?? '' );
		$image = yneko_reimu_normalize_settings_url( $item['image'] ?? '' );

		if ( '' === $name || '' === $url ) {
			continue;
		}

		$key = strtolower( untrailingslashit( $url ) );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$clean[] = array(
			'name'  => $name,
			'url'   => $url,
			'desc'  => $desc,
			'image' => $image,
		);
	}

	return $clean;
}

function yneko_reimu_sanitize_music_items( $items ) {
	$clean = array();

	if ( ! is_array( $items ) ) {
		return $clean;
	}

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$name   = sanitize_text_field( $item['name'] ?? '' );
		$artist = sanitize_text_field( $item['artist'] ?? '' );
		$url    = yneko_reimu_normalize_settings_url( $item['url'] ?? '' );
		$cover  = yneko_reimu_normalize_settings_url( $item['cover'] ?? '' );
		$lrc    = yneko_reimu_normalize_settings_url( $item['lrc'] ?? '' );
		$theme  = sanitize_hex_color( $item['theme'] ?? '' );

		if ( '' === $name || '' === $url ) {
			continue;
		}

		$clean[] = array(
			'name'   => $name,
			'artist' => $artist,
			'url'    => $url,
			'cover'  => $cover,
			'lrc'    => $lrc,
			'theme'  => $theme ? $theme : '#ff5252',
		);
	}

	return $clean;
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

function yneko_reimu_settings_theme_mod_bool( $key, $default = false ) {
	return yneko_reimu_get_theme_mod( $key, $default ) ? '1' : '0';
}

function yneko_reimu_settings_theme_mod_text( $key, $default = '' ) {
	return (string) yneko_reimu_get_theme_mod( $key, $default );
}

function yneko_reimu_sanitize_user_badge_label( $label ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	$label = preg_replace( '/[\r\n\t]+/u', ' ', $label );
	$label = preg_replace( '/\s{2,}/u', ' ', $label );
	return mb_substr( trim( $label ), 0, 12 );
}

function yneko_reimu_sanitize_user_badges_settings( $input, $defaults ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array(
		'enabled'        => ! empty( $input['enabled'] ) ? '1' : '0',
		'review_enabled' => ! empty( $input['review_enabled'] ) ? '1' : '0',
		'blocklist'      => yneko_reimu_sanitize_user_badge_blocklist( $input['blocklist'] ?? '' ),
		'avatar_frames'  => array(
			'enabled' => ! empty( $input['avatar_frames']['enabled'] ) ? '1' : '0',
			'frames'  => array(),
		),
		'special'        => array(),
	);

	$special_input = isset( $input['special'] ) && is_array( $input['special'] ) ? $input['special'] : array();
	$frame_input = isset( $input['avatar_frames']['frames'] ) && is_array( $input['avatar_frames']['frames'] ) ? $input['avatar_frames']['frames'] : array();
	foreach ( array_keys( yneko_reimu_user_badge_base_definitions() ) as $key ) {
		$definition = yneko_reimu_user_badge_base_definitions()[ $key ];
		$default    = $defaults['special'][ $key ] ?? array(
			'enabled' => '1',
			'zh'      => $definition['zh'],
			'en'      => $definition['en'],
		);
		$row     = isset( $special_input[ $key ] ) && is_array( $special_input[ $key ] ) ? $special_input[ $key ] : array();
		$zh      = yneko_reimu_sanitize_user_badge_label( $row['zh'] ?? $default['zh'] );
		$en      = yneko_reimu_sanitize_user_badge_label( $row['en'] ?? $default['en'] );
		if ( '' === $zh && '' === $en ) {
			$zh = $default['zh'];
			$en = $default['en'];
		}
		$clean['special'][ $key ] = array(
			'enabled' => ! empty( $row['enabled'] ) ? '1' : '0',
			'zh'      => $zh,
			'en'      => $en,
		);
		$frame_url = yneko_reimu_normalize_avatar_frame_url( $frame_input[ $key ] ?? ( $defaults['avatar_frames']['frames'][ $key ] ?? yneko_reimu_default_avatar_frame_url() ) );
		$clean['avatar_frames']['frames'][ $key ] = $frame_url ? $frame_url : yneko_reimu_default_avatar_frame_url();
	}

	return $clean;
}

function yneko_reimu_sanitize_user_badge_blocklist( $value ) {
	$items = preg_split( '#/+#u', (string) $value );
	$clean = array();
	foreach ( $items as $item ) {
		$item = yneko_reimu_sanitize_user_badge_label( $item );
		if ( '' !== $item ) {
			$clean[] = $item;
		}
	}
	return implode( '/', array_values( array_unique( $clean ) ) );
}

function yneko_reimu_sanitize_settings( $input ) {
	$defaults = yneko_reimu_settings_defaults();
	$input    = is_array( $input ) ? $input : array();
	$oauth    = isset( $input['github_oauth'] ) && is_array( $input['github_oauth'] ) ? $input['github_oauth'] : array();
	$upload   = isset( $input['comment_upload'] ) && is_array( $input['comment_upload'] ) ? $input['comment_upload'] : array();
	$user_badges = isset( $input['user_badges'] ) && is_array( $input['user_badges'] ) ? $input['user_badges'] : array();
	$i18n     = isset( $input['i18n'] ) && is_array( $input['i18n'] ) ? $input['i18n'] : array();
	$search   = isset( $input['search'] ) && is_array( $input['search'] ) ? $input['search'] : array();
	$features = isset( $input['features'] ) && is_array( $input['features'] ) ? $input['features'] : array();
	$player   = isset( $input['player'] ) && is_array( $input['player'] ) ? $input['player'] : array();
	$third_party = isset( $input['third_party'] ) && is_array( $input['third_party'] ) ? $input['third_party'] : array();
	$external_comments = isset( $input['external_comments'] ) && is_array( $input['external_comments'] ) ? $input['external_comments'] : array();
	$i18n_default = function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : $defaults['i18n'];
	$i18n_default_language = isset( $i18n['default'] ) && 'en_US' === $i18n['default'] ? 'en_US' : 'zh_CN';
	$i18n_prefix = trim( sanitize_title( $i18n['en_prefix'] ?? $i18n_default['en_prefix'] ), '/' );
	$player_loop = in_array( $player['loop'] ?? 'all', array( 'all', 'one', 'none' ), true ) ? $player['loop'] : 'all';
	$player_order = in_array( $player['order'] ?? 'list', array( 'list', 'random' ), true ) ? $player['order'] : 'list';
	$player_preload = in_array( $player['preload'] ?? 'metadata', array( 'auto', 'metadata', 'none' ), true ) ? $player['preload'] : 'metadata';
	$player_volume = (float) ( $player['volume'] ?? '0.7' );
	$player_volume = max( 0, min( 1, $player_volume ) );
	$list_max_height = sanitize_text_field( $player['list_max_height'] ?? '320px' );
	$list_max_height = '' !== $list_max_height ? $list_max_height : '320px';
	$current = get_option( 'yneko_reimu_settings', array() );
	$current = is_array( $current ) ? $current : array();

	return array(
		'site_avatar_url'   => array_key_exists( 'site_avatar_url', $input ) ? yneko_reimu_normalize_settings_url( $input['site_avatar_url'] ) : yneko_reimu_normalize_settings_url( $current['site_avatar_url'] ?? '' ),
		'author_avatar_url' => array_key_exists( 'author_avatar_url', $input ) ? yneko_reimu_normalize_settings_url( $input['author_avatar_url'] ) : yneko_reimu_normalize_settings_url( $current['author_avatar_url'] ?? '' ),
		'comment_avatar_url'=> yneko_reimu_normalize_settings_url( $input['comment_avatar_url'] ?? '' ),
		'favicon_fallback_url' => yneko_reimu_normalize_png_jpeg_url( $input['favicon_fallback_url'] ?? '' ),
		'comment_upload'    => array(
			'enabled'      => ( ! empty( $upload['image_enabled'] ) || ! empty( $upload['gif_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
			'image_enabled'=> ( ! empty( $upload['image_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
			'gif_enabled'  => ( ! empty( $upload['gif_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
			'image_review' => ! empty( $upload['image_review'] ) ? '1' : '0',
			'gif_review'   => ! empty( $upload['gif_review'] ) ? '1' : '0',
			'image_max_mb' => max( 1, min( 20, absint( $upload['image_max_mb'] ?? 1 ) ) ),
			'gif_max_mb'   => max( 1, min( 30, absint( $upload['gif_max_mb'] ?? 3 ) ) ),
			'temp_cleanup_days' => max( 1, min( 30, absint( $upload['temp_cleanup_days'] ?? 7 ) ) ),
			'rejected_cleanup_hours' => max( 1, min( 168, absint( $upload['rejected_cleanup_hours'] ?? 24 ) ) ),
			'avatar_enabled'=> ! empty( $upload['avatar_enabled'] ) ? '1' : '0',
			'avatar_review'=> ! empty( $upload['avatar_review'] ) ? '1' : '0',
			'avatar_max_mb'=> max( 1, min( 10, absint( $upload['avatar_max_mb'] ?? 1 ) ) ),
		),
		'user_badges'       => yneko_reimu_sanitize_user_badges_settings( $user_badges, $defaults['user_badges'] ),
		'github_url'        => yneko_reimu_normalize_settings_url( $input['github_url'] ?? '' ),
		'friend_site'       => yneko_reimu_sanitize_site_friend_info( $input['friend_site'] ?? array() ),
		'friends'           => yneko_reimu_sanitize_friend_items( array_key_exists( 'friends', $input ) ? $input['friends'] : array() ),
		'sponsor_qr_url'    => yneko_reimu_normalize_settings_url( $input['sponsor_qr_url'] ?? '' ),
		'github_oauth'      => array(
			'client_id'     => sanitize_text_field( $oauth['client_id'] ?? '' ),
			'client_secret' => sanitize_text_field( $oauth['client_secret'] ?? '' ),
			'callback_url'  => yneko_reimu_normalize_settings_url( $oauth['callback_url'] ?? '' ),
			'auto_create'   => ! empty( $oauth['auto_create'] ) ? '1' : '0',
		),
		'builtin_pages'     => yneko_reimu_sanitize_settings_bool_group(
			isset( $input['builtin_pages'] ) && is_array( $input['builtin_pages'] ) && isset( $input['builtin_pages']['_present'] )
				? $input['builtin_pages']
				: ( isset( $current['builtin_pages'] ) && is_array( $current['builtin_pages'] ) ? $current['builtin_pages'] : $defaults['builtin_pages'] ),
			$defaults['builtin_pages'],
			array( 'projects', 'archives', 'about', 'friend' )
		),
		'i18n'              => array(
			'enabled'   => ! empty( $i18n['enabled'] ) ? '1' : '0',
			'default'   => $i18n_default_language,
			'en_prefix' => $i18n_prefix ? $i18n_prefix : 'en',
			'zh_label'  => sanitize_text_field( $i18n['zh_label'] ?? $i18n_default['zh_label'] ),
			'en_label'  => sanitize_text_field( $i18n['en_label'] ?? $i18n_default['en_label'] ),
		),
		'search'            => array(
			'algolia_enable'     => ! empty( $search['algolia_enable'] ) ? '1' : '0',
			'algolia_app_id'     => sanitize_text_field( $search['algolia_app_id'] ?? '' ),
			'algolia_api_key'    => sanitize_text_field( $search['algolia_api_key'] ?? '' ),
			'algolia_index_name' => sanitize_text_field( $search['algolia_index_name'] ?? '' ),
			'local_enable'       => ! empty( $search['local_enable'] ) ? '1' : '0',
			'local_json_url'     => yneko_reimu_normalize_settings_url( $search['local_json_url'] ?? '' ),
			'index_full_content' => ! empty( $search['index_full_content'] ) ? '1' : '0',
		),
		'features'          => yneko_reimu_sanitize_settings_bool_group(
			$features,
			$defaults['features'],
			array( 'preloader_enable', 'top_enable', 'triangle_badge', 'firework_enable', 'pjax_enable', 'busuanzi_enable', 'katex_enable', 'photoswipe_enable', 'mermaid_enable', 'custom_cursor' )
		),
		'player'            => array(
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
		),
		'third_party'       => array(
			'live2d_enable'       => ! empty( $third_party['live2d_enable'] ) ? '1' : '0',
			'live2d_base_url'     => yneko_reimu_normalize_settings_url( $third_party['live2d_base_url'] ?? $defaults['third_party']['live2d_base_url'] ),
			'live2d_api_base_url' => yneko_reimu_normalize_settings_url( $third_party['live2d_api_base_url'] ?? $defaults['third_party']['live2d_api_base_url'] ),
			'vendor_cdn_base'     => yneko_reimu_normalize_settings_url( $third_party['vendor_cdn_base'] ?? $defaults['third_party']['vendor_cdn_base'] ),
		),
		'external_comments' => array(
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
		),
		'music'             => yneko_reimu_sanitize_music_items( array_key_exists( 'music', $input ) ? $input['music'] : array() ),
	);
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

function yneko_reimu_settings_group( $group ) {
	$settings = yneko_reimu_settings();
	$defaults = yneko_reimu_settings_defaults();
	$value    = isset( $settings[ $group ] ) && is_array( $settings[ $group ] ) ? $settings[ $group ] : array();
	$default  = isset( $defaults[ $group ] ) && is_array( $defaults[ $group ] ) ? $defaults[ $group ] : array();
	return array_replace_recursive( $default, $value );
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

function yneko_reimu_register_settings() {
	register_setting(
		'yneko_reimu_settings',
		'yneko_reimu_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'yneko_reimu_sanitize_settings',
			'default'           => yneko_reimu_settings_defaults(),
		)
	);
}
add_action( 'admin_init', 'yneko_reimu_register_settings' );

function yneko_reimu_cleanup_blocked_user_badges_after_settings_save( $old_value, $value, $option ) {
	unset( $old_value, $option );
	if ( empty( $value['user_badges'] ) || ! function_exists( 'yneko_reimu_comment_normalize_tag_list' ) ) {
		return;
	}

	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_comment_tags',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	foreach ( $users as $user_id ) {
		foreach ( array( '_yneko_reimu_comment_tags', '_yneko_reimu_comment_tags_pending' ) as $meta_key ) {
			$current = get_user_meta( absint( $user_id ), $meta_key, true );
			$limit   = function_exists( 'yneko_reimu_comment_custom_tag_storage_limit' ) ? yneko_reimu_comment_custom_tag_storage_limit() : 5;
			$clean   = yneko_reimu_comment_normalize_tag_list( $current, $limit );
			if ( $clean ) {
				update_user_meta( absint( $user_id ), $meta_key, $clean );
			} else {
				delete_user_meta( absint( $user_id ), $meta_key );
			}
		}
	}
}
add_action( 'updated_option_yneko_reimu_settings', 'yneko_reimu_cleanup_blocked_user_badges_after_settings_save', 10, 3 );

function yneko_reimu_register_settings_page() {
	add_theme_page(
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		'manage_options',
		'yneko-reimu-settings',
		'yneko_reimu_render_settings_page'
	);
}
add_action( 'admin_menu', 'yneko_reimu_register_settings_page' );

function yneko_reimu_admin_menu_badge_markup( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return ' <span class="update-plugins yneko-reimu-menu-badge count-' . esc_attr( $count ) . '"><span class="plugin-count">' . esc_html( number_format_i18n( $count ) ) . '</span></span>';
}

function yneko_reimu_add_admin_menu_review_badges() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $menu, $submenu;
	$counts = yneko_reimu_admin_review_badge_counts();
	$total  = absint( ( $counts['comments'] ?? 0 ) + ( $counts['users'] ?? 0 ) );
	if ( ! $total ) {
		return;
	}

	foreach ( $menu as &$item ) {
		if ( ! isset( $item[2] ) || 'themes.php' !== $item[2] ) {
			continue;
		}
		$item[0] = preg_replace( '#\s*<span class="update-plugins yneko-reimu-menu-badge.*?</span></span>#', '', (string) $item[0] );
		$item[0] .= yneko_reimu_admin_menu_badge_markup( $total );
		break;
	}
	unset( $item );

	if ( empty( $submenu['themes.php'] ) || ! is_array( $submenu['themes.php'] ) ) {
		return;
	}
	foreach ( $submenu['themes.php'] as &$subitem ) {
		if ( ! isset( $subitem[2] ) || 'yneko-reimu-settings' !== $subitem[2] ) {
			continue;
		}
		$subitem[0] = preg_replace( '#\s*<span class="update-plugins yneko-reimu-menu-badge.*?</span></span>#', '', (string) $subitem[0] );
		$subitem[0] .= yneko_reimu_admin_menu_badge_markup( $total );
		break;
	}
	unset( $subitem );
}
add_action( 'admin_menu', 'yneko_reimu_add_admin_menu_review_badges', 99 );

function yneko_reimu_admin_media_field( $name, $value, $label, $accept = '' ) {
	?>
	<div class="yneko-reimu-media-field">
		<input type="url" class="regular-text yneko-reimu-media-url" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $accept ? ' data-accept="' . esc_attr( $accept ) . '"' : ''; ?>>
		<button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( $label ); ?></button>
	</div>
	<?php
}

function yneko_reimu_admin_bilingual_text( $zh, $en, $tag = 'span' ) {
	$tag = in_array( $tag, array( 'span', 'p', 'div', 'button' ), true ) ? $tag : 'span';
	$text = yneko_reimu_admin_prefers_zh() ? $zh : $en;
	return sprintf(
		'<%1$s class="yneko-reimu-admin-text">%2$s</%1$s>',
		tag_escape( $tag ),
		esc_html( $text )
	);
}

function yneko_reimu_admin_prefers_zh() {
	$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	return 0 === strpos( strtolower( str_replace( '-', '_', (string) $locale ) ), 'zh_' );
}

function yneko_reimu_admin_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_description( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en, 'p' ) );
}

function yneko_reimu_admin_bilingual_heading( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_button_text( $zh, $en ) {
	return yneko_reimu_admin_bilingual_text( $zh, $en );
}

function yneko_reimu_admin_badge( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return '<span class="yneko-reimu-admin-badge" aria-label="' . esc_attr(
		sprintf(
			/* translators: %d: pending review item count. */
			__( '%d 个待处理项目', 'yneko-reimu' ),
			$count
		)
	) . '">' . esc_html( (string) $count ) . '</span>';
}

function yneko_reimu_admin_count_pending_comment_uploads( $type = 'all' ) {
	if ( ! function_exists( 'yneko_reimu_comment_pending_temp_uploads' ) || ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return 0;
	}

	$type = in_array( $type, array( 'all', 'image', 'gif' ), true ) ? $type : 'all';
	$count = 0;
	foreach ( yneko_reimu_comment_pending_temp_uploads( 300 ) as $item ) {
		$item_type = 'gif' === ( $item['type'] ?? '' ) ? 'gif' : 'image';
		if ( 'all' === $type || $type === $item_type ) {
			$count++;
		}
	}
	foreach ( yneko_reimu_comment_upload_library( 300, $type, true ) as $item ) {
		if ( 'pending' === (string) ( $item['status'] ?? '' ) ) {
			$count++;
		}
	}

	return $count;
}

function yneko_reimu_admin_count_pending_avatars() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_avatar_pending_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	return count( $users );
}

function yneko_reimu_admin_count_pending_user_badges() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	$count = 0;
	foreach ( $users as $user_id ) {
		$tags = get_user_meta( absint( $user_id ), '_yneko_reimu_comment_tags_pending', true );
		if ( is_array( $tags ) ) {
			$count += count( array_filter( $tags, 'is_array' ) );
		}
	}
	return $count;
}

function yneko_reimu_admin_review_badge_counts() {
	$upload = yneko_reimu_settings_comment_upload();
	$badges = yneko_reimu_settings_user_badges();
	$counts = array(
		'comment_images' => '1' === (string) ( $upload['image_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'image' ) : 0,
		'comment_gifs'   => '1' === (string) ( $upload['gif_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'gif' ) : 0,
		'avatars'        => '1' === (string) ( $upload['avatar_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_avatars() : 0,
		'user_badges'    => '1' === (string) ( $badges['review_enabled'] ?? '0' ) ? yneko_reimu_admin_count_pending_user_badges() : 0,
	);
	$counts['comments'] = $counts['comment_images'] + $counts['comment_gifs'];
	$counts['users']    = $counts['avatars'] + $counts['user_badges'];
	return $counts;
}

function yneko_reimu_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = yneko_reimu_settings();
	$oauth    = yneko_reimu_settings_github_oauth();
	$builtin_pages = yneko_reimu_settings_builtin_pages();
	$i18n     = isset( $settings['i18n'] ) && is_array( $settings['i18n'] ) ? wp_parse_args( $settings['i18n'], yneko_reimu_i18n_defaults() ) : yneko_reimu_i18n_defaults();
	$search   = yneko_reimu_settings_search();
	$features = yneko_reimu_settings_features();
	$player   = yneko_reimu_settings_player();
	$third_party = yneko_reimu_settings_third_party();
	$external_comments = yneko_reimu_settings_external_comments();
	$review_badges = yneko_reimu_admin_review_badge_counts();
	$callback = function_exists( 'yneko_reimu_github_login_callback_url' ) ? yneko_reimu_github_login_callback_url() : add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() );
	?>
	<div class="wrap yneko-reimu-settings-page">
		<h1><?php esc_html_e( 'Yneko-Reimu 设置', 'yneko-reimu' ); ?></h1>
		<?php yneko_reimu_admin_bilingual_description( '这些内容保存在 WordPress 数据库中，不会写入主题源码或主题包。', 'These settings are stored in the WordPress database and are never written into the theme source or release package.' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'yneko_reimu_settings' ); ?>
			<nav class="nav-tab-wrapper yneko-reimu-settings-tabs" aria-label="<?php esc_attr_e( 'Yneko-Reimu 设置分类', 'yneko-reimu' ); ?>">
				<button type="button" class="nav-tab nav-tab-active" data-yneko-settings-tab="general"><?php yneko_reimu_admin_bilingual_label( '常规设置', 'General' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="github"><?php yneko_reimu_admin_bilingual_label( 'GitHub 登录设置', 'GitHub login' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="i18n"><?php yneko_reimu_admin_bilingual_label( '多语言设置', 'Multilingual' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="comments"><?php yneko_reimu_admin_bilingual_label( '评论设置', 'Comments' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['comments'] ?? 0 ) ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="users"><?php yneko_reimu_admin_bilingual_label( '用户设置', 'Users' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['users'] ?? 0 ) ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="search"><?php yneko_reimu_admin_bilingual_label( '搜索设置', 'Search' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="extensions"><?php yneko_reimu_admin_bilingual_label( '扩展与第三方', 'Extensions' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="external-comments"><?php yneko_reimu_admin_bilingual_label( '外部评论', 'External comments' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="friends"><?php yneko_reimu_admin_bilingual_label( '友链设置', 'Friend links' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="music"><?php yneko_reimu_admin_bilingual_label( '曲目设置', 'Music' ); ?></button>
			</nav>

			<section class="yneko-reimu-settings-panel is-active" data-yneko-settings-panel="general">
				<h2><?php yneko_reimu_admin_bilingual_heading( '常规设置', 'General settings' ); ?></h2>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '视觉预览工作台', 'Visual preview workspace' ); ?></th>
					<td>
						<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '打开 WordPress 自定义', 'Open WordPress Customizer' ); ?></a>
						<?php yneko_reimu_admin_bilingual_description( '站点图标、Logo、作者头像、横幅、封面、搜索背景、强调色、侧栏、导航、首页胶囊和页脚文字保留在自定义器中，方便使用右侧实时预览。', 'Site icon, logo, author avatar, banners, covers, search background, accent color, sidebar, navigation, home capsules, and footer text remain in the Customizer for live preview.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '第三方资源提示', 'Third-party resources' ); ?></th>
					<td>
						<?php yneko_reimu_admin_bilingual_description( '启用 Google Fonts、GA、Cloudflare RUM、jsDelivr、APlayer、mouse-firework、Live2D、Algolia、Busuanzi 等功能后，前台可能连接对应第三方域名。需要隐私优先时，请关闭相关扩展，或在“扩展与第三方”中把 Vendor CDN / Live2D 地址替换为自托管资源。', 'When Google Fonts, GA, Cloudflare RUM, jsDelivr, APlayer, mouse-firework, Live2D, Algolia, Busuanzi, or similar features are enabled, the front end may contact those third-party domains. For a privacy-first setup, disable those extensions or replace Vendor CDN / Live2D URLs with self-hosted resources in Extensions.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '内置页面开关', 'Built-in pages' ); ?></th>
					<td>
						<input type="hidden" name="yneko_reimu_settings[builtin_pages][_present]" value="1">
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][projects]" value="1" <?php checked( '1', $builtin_pages['projects'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '项目页', 'Projects page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][archives]" value="1" <?php checked( '1', $builtin_pages['archives'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '归档页', 'Archives page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][about]" value="1" <?php checked( '1', $builtin_pages['about'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '关于页', 'About page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][friend]" value="1" <?php checked( '1', $builtin_pages['friend'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '友链页', 'Friends page' ); ?></label>
						<?php yneko_reimu_admin_bilingual_description( '关闭后会从主题默认导航和菜单中的对应内置链接移除，并让对应内置路径返回 404；不会影响 WordPress 原生分类、标签、日期等归档页。', 'Disabled pages are removed from the theme default navigation and matching built-in menu links, and their built-in paths return 404. Native WordPress category, tag, date, and other archives are not affected.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Favicon / Apple Touch 兜底图', 'Favicon / Apple Touch fallback' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[favicon_fallback_url]', $settings['favicon_fallback_url'], yneko_reimu_admin_bilingual_text( '选择 PNG/JPG', 'Choose PNG/JPG' ), 'image/png,image/jpeg' ); ?>
						<?php yneko_reimu_admin_bilingual_description( '站点图标和 Logo 仍可使用 SVG；这里建议额外设置一张 512×512 的 PNG/JPG，用于不稳定支持 SVG favicon 或 apple-touch-icon 的浏览器、移动端和聊天软件预览。此项不会影响 Rank Math 的 og:image。', 'The site icon and logo can still use SVG. Add a square 512x512 PNG/JPG here as a fallback for browsers, mobile devices, and chat previews that do not reliably support SVG favicon or apple-touch-icon. This does not affect Rank Math og:image.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-github-url"><?php yneko_reimu_admin_bilingual_label( 'GitHub 主页链接', 'GitHub profile URL' ); ?></label></th>
					<td>
						<input id="yneko-reimu-github-url" class="regular-text" type="url" name="yneko_reimu_settings[github_url]" value="<?php echo esc_attr( $settings['github_url'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。', 'Used by the GitHub corner ribbon, sidebar GitHub link, and project-page repository source.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '赞助二维码', 'Sponsor QR code' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[sponsor_qr_url]', $settings['sponsor_qr_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
						<?php yneko_reimu_admin_bilingual_description( '用于底部赞助入口。留空时不会显示赞助二维码。', 'Used by the footer sponsor entry. If empty, the sponsor QR code is hidden.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="i18n" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '多语言设置', 'Multilingual settings' ); ?></h2>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '启用语言切换', 'Enable language switcher' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[i18n][enabled]" value="1" <?php checked( '1', $i18n['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示前台语言切换入口', 'Show the front-end language switcher' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '默认语言', 'Default language' ); ?></th>
					<td>
						<select name="yneko_reimu_settings[i18n][default]">
							<option value="zh_CN" <?php selected( $i18n['default'], 'zh_CN' ); ?>>简体中文 / Simplified Chinese</option>
							<option value="en_US" <?php selected( $i18n['default'], 'en_US' ); ?>>English / 英文</option>
						</select>
						<?php yneko_reimu_admin_bilingual_description( '默认建议保持简体中文，中文内容继续使用站点原始地址。', 'Keeping Simplified Chinese as the default is recommended; Chinese content keeps the original site URLs.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-en-prefix"><?php yneko_reimu_admin_bilingual_label( '英文路径前缀', 'English URL prefix' ); ?></label></th>
					<td>
						<input id="yneko-reimu-en-prefix" class="regular-text" type="text" name="yneko_reimu_settings[i18n][en_prefix]" value="<?php echo esc_attr( $i18n['en_prefix'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '例如 en 会让英文内容使用 /en/ 开头的地址。', 'For example, en makes English content use URLs starting with /en/.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-zh-label"><?php yneko_reimu_admin_bilingual_label( '中文显示名', 'Chinese label' ); ?></label></th>
					<td>
						<input id="yneko-reimu-zh-label" class="regular-text" type="text" name="yneko_reimu_settings[i18n][zh_label]" value="<?php echo esc_attr( $i18n['zh_label'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '显示在前台语言切换菜单中的中文名称。', 'The Chinese language name shown in the front-end language switcher.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-en-label"><?php yneko_reimu_admin_bilingual_label( '英文显示名', 'English label' ); ?></label></th>
					<td>
						<input id="yneko-reimu-en-label" class="regular-text" type="text" name="yneko_reimu_settings[i18n][en_label]" value="<?php echo esc_attr( $i18n['en_label'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '显示在前台语言切换菜单中的英文名称。', 'The English language name shown in the front-end language switcher.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="github" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( 'GitHub 登录设置', 'GitHub login settings' ); ?></h2>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="yneko-reimu-callback-url"><?php yneko_reimu_admin_bilingual_label( '回调地址', 'Callback URL' ); ?></label></th>
					<td>
						<input id="yneko-reimu-callback-url" class="regular-text" type="url" name="yneko_reimu_settings[github_oauth][callback_url]" value="<?php echo esc_attr( $oauth['callback_url'] ); ?>" placeholder="<?php echo esc_attr( $callback ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '留空时自动使用下方默认地址；如果站点经过反向代理、固定域名或特殊登录路径，可在这里覆盖。GitHub OAuth App 中的 Authorization callback URL 需要与最终地址完全一致。', 'Leave empty to use the default URL below. Override it when the site uses a reverse proxy, fixed public domain, or custom login path. The Authorization callback URL in GitHub OAuth App must match the final URL exactly.' ); ?>
						<p class="description"><code><?php echo esc_html( $callback ); ?></code></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-id"><?php yneko_reimu_admin_bilingual_label( '客户端 ID', 'Client ID' ); ?></label></th>
					<td>
						<input id="yneko-reimu-client-id" class="regular-text" type="text" name="yneko_reimu_settings[github_oauth][client_id]" value="<?php echo esc_attr( $oauth['client_id'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '填写 GitHub OAuth App 提供的 Client ID。留空时前台不显示 GitHub 登录按钮。', 'Enter the Client ID from your GitHub OAuth App. If empty, the GitHub login button is hidden on the front end.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-secret"><?php yneko_reimu_admin_bilingual_label( '客户端密钥', 'Client Secret' ); ?></label></th>
					<td>
						<input id="yneko-reimu-client-secret" class="regular-text" type="password" autocomplete="off" name="yneko_reimu_settings[github_oauth][client_secret]" value="<?php echo esc_attr( $oauth['client_secret'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '密钥只保存在 WordPress 数据库中，不会写入主题源码或发布包。', 'The secret is stored only in the WordPress database and is never written into the theme source or release package.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自动创建用户', 'Auto-create users' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[github_oauth][auto_create]" value="1" <?php checked( '1', $oauth['auto_create'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许 GitHub 登录自动创建 WordPress 用户', 'Allow GitHub login to create WordPress users automatically' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '绑定当前账户', 'Bind current account' ); ?></th>
					<td>
						<?php
						$github_bind_url   = function_exists( 'yneko_reimu_github_login_bind_url' ) ? yneko_reimu_github_login_bind_url( admin_url( 'themes.php?page=yneko-reimu-settings' ) ) : '';
						$github_bound_name = get_user_meta( get_current_user_id(), '_yneko_reimu_github_login', true );
						?>
						<?php if ( $github_bound_name ) : ?>
							<p class="description">
								<?php
								printf(
									/* translators: %s: GitHub username. */
									esc_html__( '当前账户已绑定 GitHub：%s', 'yneko-reimu' ),
									esc_html( $github_bound_name )
								);
								?>
							</p>
						<?php endif; ?>
						<?php if ( $github_bind_url ) : ?>
							<p><a class="button" href="<?php echo esc_url( $github_bind_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '绑定/重新绑定 GitHub', 'Bind/Rebind GitHub' ); ?></a></p>
						<?php endif; ?>
						<?php yneko_reimu_admin_bilingual_description( '普通 GitHub 登录不会绑定当前 WordPress 用户；只有点击这里的绑定按钮才会把授权的 GitHub 账号绑定到当前账户。', 'Normal GitHub login never binds the current WordPress user. Only this binding button links the authorized GitHub account to the current account.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="comments" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '评论设置', 'Comment settings' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '游客评论头像', 'Guest comment avatar' ); ?></th>
						<td>
							<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[comment_avatar_url]', $settings['comment_avatar_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
							<?php yneko_reimu_admin_bilingual_description( '用于未登录访客评论的默认头像。留空时使用 One User Avatar 的全站默认头像，再留空则使用作者头像。', 'Default avatar for logged-out commenters. If empty, One User Avatar site default is used first, then the author avatar.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '评论图片上传', 'Comment image uploads' ); ?></th>
						<td>
							<?php $comment_upload = yneko_reimu_settings_comment_upload(); ?>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_enabled]" value="1" <?php checked( '1', $comment_upload['image_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传图片', 'Allow logged-in users to upload images' ); ?></label>
								&nbsp;
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_review]" value="1" <?php checked( '1', $comment_upload['image_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '图片人工审核', 'Review uploaded images' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '图片上限 MB', 'Image max MB' ); ?> <input class="small-text" type="number" min="1" max="20" name="yneko_reimu_settings[comment_upload][image_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['image_max_mb'] ) ); ?>"></label>
							</p>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_enabled]" value="1" <?php checked( '1', $comment_upload['gif_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传 GIF', 'Allow logged-in users to upload GIFs' ); ?></label>
								&nbsp;
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_review]" value="1" <?php checked( '1', $comment_upload['gif_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( 'GIF 人工审核', 'Review uploaded GIFs' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( 'GIF 上限 MB', 'GIF max MB' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][gif_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['gif_max_mb'] ) ); ?>"></label>
							</p>
							<p>
								<label><?php yneko_reimu_admin_bilingual_label( '临时文件清理天数', 'Temporary file cleanup days' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][temp_cleanup_days]" value="<?php echo esc_attr( absint( $comment_upload['temp_cleanup_days'] ) ); ?>"></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '驳回后文件清理时间（小时）', 'Rejected file cleanup hours' ); ?> <input class="small-text" type="number" min="1" max="168" name="yneko_reimu_settings[comment_upload][rejected_cleanup_hours]" value="<?php echo esc_attr( absint( $comment_upload['rejected_cleanup_hours'] ?? 24 ) ); ?>"></label>
							</p>
							<?php yneko_reimu_admin_bilingual_description( '未启用某类上传时，评论区对应上传按钮会隐藏。启用人工审核后，文件先留在临时目录并出现在下方待审核列表；批准后评论中的图片/GIF 才会生效。', 'When a type is disabled, its upload button is hidden in comments. With review enabled, uploads stay in the temporary folder and appear in the pending list below; approved files are then applied to comments.' ); ?>
						</td>
					</tr>
				</table>

				<h2><?php yneko_reimu_admin_bilingual_heading( '评论上传管理', 'Comment upload manager' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '登录用户上传的图片和 GIF 会集中显示在这里。待审核文件需要批准后才会在评论中生效；GIF 批准后也会出现在评论区 GIF 面板中。', 'Images and GIFs uploaded by logged-in users are listed here. Pending files must be approved before they work in comments; approved GIFs also appear in the comment GIF picker.' ); ?>
				<?php yneko_reimu_render_admin_comment_gif_upload(); ?>
				<?php yneko_reimu_render_comment_upload_admin(); ?>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="users" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户设置', 'User settings' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '用户标签及头像框', 'User badges and avatar frames' ); ?></th>
						<td>
							<?php $user_badges = yneko_reimu_settings_user_badges(); ?>
							<?php $avatar_frames = isset( $user_badges['avatar_frames'] ) && is_array( $user_badges['avatar_frames'] ) ? $user_badges['avatar_frames'] : array(); ?>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][enabled]" value="1" <?php checked( '1', $user_badges['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区用户标签', 'Enable comment user badges' ); ?></label></p>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][review_enabled]" value="1" <?php checked( '1', $user_badges['review_enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户标签审核', 'Review user custom badges' ); ?></label></p>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][avatar_frames][enabled]" value="1" <?php checked( '1', $avatar_frames['enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区头像框', 'Enable comment avatar frames' ); ?></label></p>
							<?php yneko_reimu_admin_bilingual_description( '关闭总开关后，除站长/管理员等特殊身份外，普通用户自定义标签不会显示，个人资料中也不显示标签设置区。开启审核后，非管理员的新自定义标签需要在下方批准后才会显示。', 'When the main switch is off, ordinary custom badges are hidden except special identity badges, and profile badge settings are hidden. With review enabled, new custom badges from non-admin users require approval below before they display.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '特殊标签及用户头像框管理', 'Special badges and avatar frames' ); ?></th>
						<td>
							<div class="yneko-reimu-special-badge-table">
								<?php
								foreach ( yneko_reimu_user_badge_base_definitions() as $badge_key => $definition ) :
									$row = $user_badges['special'][ $badge_key ] ?? array( 'enabled' => '1', 'zh' => $definition['zh'], 'en' => $definition['en'] );
									$frame_url = $avatar_frames['frames'][ $badge_key ] ?? yneko_reimu_default_avatar_frame_url();
									?>
									<div class="yneko-reimu-special-badge-row">
										<label><input type="checkbox" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][enabled]" value="1" <?php checked( '1', $row['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( $definition['title_zh'], $definition['title_en'] ); ?></label>
										<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][zh]" value="<?php echo esc_attr( $row['zh'] ); ?>" placeholder="<?php echo esc_attr( $definition['zh'] ); ?>">
										<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][en]" value="<?php echo esc_attr( $row['en'] ); ?>" placeholder="<?php echo esc_attr( $definition['en'] ); ?>">
										<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[user_badges][avatar_frames][frames][' . $badge_key . ']', $frame_url, yneko_reimu_admin_bilingual_text( '选择头像框', 'Choose frame' ), 'image/png,image/webp,image/avif' ); ?>
										<span class="description"><?php echo esc_html( yneko_reimu_admin_prefers_zh() ? $definition['desc_zh'] : $definition['desc_en'] ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
							<?php yneko_reimu_admin_bilingual_description( '七种基础特殊标签按“站长 > 管理员 > 编辑 > 作者 > 贡献者 > 会员 > 订阅者”排序。标签原名和当前显示名都会作为保留词。头像框支持 PNG、WebP、AVIF；用户同时拥有多个特殊标签时，按这个顺序使用第一个可用头像框。', 'The seven base special badges are ordered by Owner > Admin > Editor > Author > Contributor > Member > Subscriber. Original and current labels are reserved. Avatar frames support PNG, WebP, and AVIF; when a user has multiple special badges, the first available frame in this order is used.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自定义标签屏蔽词', 'Custom badge blocklist' ); ?></th>
						<td>
							<input class="regular-text" type="text" name="yneko_reimu_settings[user_badges][blocklist]" value="<?php echo esc_attr( $user_badges['blocklist'] ?? '' ); ?>" placeholder="<?php esc_attr_e( '广告/官方/测试', 'yneko-reimu' ); ?>">
							<?php yneko_reimu_admin_bilingual_description( '用 / 分隔。保存后，匹配屏蔽词或保留词的旧自定义标签会自动停止显示，用户也不能再次设置。', 'Separate words with /. After saving, old custom badges matching blocked or reserved words stop displaying automatically, and users cannot set them again.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '用户头像上传', 'User avatar uploads' ); ?></th>
						<td>
							<?php $comment_upload = yneko_reimu_settings_comment_upload(); ?>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_enabled]" value="1" <?php checked( '1', $comment_upload['avatar_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许用户上传个人头像', 'Allow users to upload profile avatars' ); ?></label>
							</p>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_review]" value="1" <?php checked( '1', $comment_upload['avatar_review'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户头像审核', 'Review user avatars' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '头像上限 MB', 'Avatar max MB' ); ?> <input class="small-text" type="number" min="1" max="10" name="yneko_reimu_settings[comment_upload][avatar_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['avatar_max_mb'] ) ); ?>"></label>
							</p>
							<?php yneko_reimu_admin_bilingual_description( '未开启上传时，用户仍可填写头像图片链接。开启审核后，新上传头像先进入临时目录，批准后才会应用。', 'When upload is disabled, users can still use an avatar image URL. When review is enabled, new uploads go to a temporary directory and apply only after approval.' ); ?>
						</td>
					</tr>
				</table>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户标签审核', 'User badge review' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['user_badges'] ?? 0 ) ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '这里会列出已有和待审核的用户自定义标签。即使未开启审核，也可以在这里单独撤销某个用户的标签。', 'This lists existing and pending user custom badges. Even when review is disabled, you can revoke individual user badges here.' ); ?>
				<?php yneko_reimu_render_user_badge_admin(); ?>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户头像管理', 'User avatar manager' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['avatars'] ?? 0 ) ); ?></h2>
				<?php yneko_reimu_render_user_avatar_admin(); ?>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="search" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '搜索设置', 'Search settings' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '搜索不依赖实时预览，因此统一在这里管理。优先级：Algolia 配置完整时优先；否则使用本地 JSON；再否则回退 WordPress REST。', 'Search does not need live preview, so it is managed here. Priority: Algolia when fully configured, then local JSON, then WordPress REST.' ); ?>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索入口', 'Search providers' ); ?></th>
					<td>
						<label><input type="checkbox" name="yneko_reimu_settings[search][algolia_enable]" value="1" <?php checked( '1', $search['algolia_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Algolia 搜索入口', 'Enable Algolia search' ); ?></label><br>
						<label><input type="checkbox" name="yneko_reimu_settings[search][local_enable]" value="1" <?php checked( '1', $search['local_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用本地搜索入口', 'Enable local search' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-algolia-app-id">Algolia App ID</label></th>
					<td><input id="yneko-reimu-algolia-app-id" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_app_id]" value="<?php echo esc_attr( $search['algolia_app_id'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-algolia-api-key">Algolia Search API Key</label></th>
					<td>
						<input id="yneko-reimu-algolia-api-key" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_api_key]" value="<?php echo esc_attr( $search['algolia_api_key'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '填写 Search-Only API Key，不要填写 Admin API Key。', 'Enter the Search-Only API Key, not an Admin API Key.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-algolia-index-name">Algolia Index Name</label></th>
					<td><input id="yneko-reimu-algolia-index-name" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_index_name]" value="<?php echo esc_attr( $search['algolia_index_name'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-local-json"><?php yneko_reimu_admin_bilingual_label( '本地搜索 JSON URL', 'Local search JSON URL' ); ?></label></th>
					<td>
						<input id="yneko-reimu-local-json" class="regular-text" type="url" name="yneko_reimu_settings[search][local_json_url]" value="<?php echo esc_attr( $search['local_json_url'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '留空时使用主题自动生成的 /search.json。', 'Leave empty to use the theme-generated /search.json.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索索引内容', 'Search index content' ); ?></th>
					<td>
						<label><input type="checkbox" name="yneko_reimu_settings[search][index_full_content]" value="1" <?php checked( '1', $search['index_full_content'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '搜索索引包含全文', 'Include full content in search index' ); ?></label>
						<?php yneko_reimu_admin_bilingual_description( '默认关闭，仅输出标题、摘要、分类、标签和 URL。开启后 /search.json 会公开文章纯文本全文。', 'Disabled by default; only title, excerpt, categories, tags, and URL are output. When enabled, /search.json exposes plain-text post content.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="extensions" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '扩展与第三方', 'Extensions and third-party resources' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '这些功能通常会加载额外脚本或连接第三方域名，因此从自定义器移到主设置页集中管理。视觉与布局仍在自定义器中实时预览。', 'These features usually load extra scripts or contact third-party domains, so they are managed here. Visual and layout options remain in the Customizer for live preview.' ); ?>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '主题扩展', 'Theme extensions' ); ?></th>
					<td>
						<?php
						$feature_labels = array(
							'preloader_enable' => array( '加载动画', 'Loading animation' ),
							'top_enable' => array( '回到顶部太极按钮', 'Back-to-top Taichi button' ),
							'triangle_badge' => array( '右上角 GitHub 三角标', 'GitHub corner ribbon' ),
							'firework_enable' => array( '鼠标烟花', 'Mouse firework' ),
							'pjax_enable' => array( 'PJAX 软导航', 'PJAX navigation' ),
							'busuanzi_enable' => array( '不蒜子统计', 'Busuanzi statistics' ),
							'katex_enable' => array( 'KaTeX 数学公式', 'KaTeX math' ),
							'photoswipe_enable' => array( 'PhotoSwipe 图片灯箱', 'PhotoSwipe lightbox' ),
							'mermaid_enable' => array( 'Mermaid 图表', 'Mermaid diagrams' ),
							'custom_cursor' => array( '自定义鼠标指针', 'Custom cursor' ),
						);
						?>
						<?php foreach ( $feature_labels as $key => $label ) : ?>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( '1', $features[ $key ] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( $label[0], $label[1] ); ?></label><br>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Live2D Widgets', 'Live2D Widgets' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[third_party][live2d_enable]" value="1" <?php checked( '1', $third_party['live2d_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Live2D Widgets', 'Enable Live2D Widgets' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-live2d-base"><?php yneko_reimu_admin_bilingual_label( 'Live2D Widgets 资源地址', 'Live2D Widgets resource URL' ); ?></label></th>
					<td><input id="yneko-reimu-live2d-base" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_base_url]" value="<?php echo esc_attr( $third_party['live2d_base_url'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-live2d-api"><?php yneko_reimu_admin_bilingual_label( 'Live2D 模型 CDN 地址', 'Live2D model CDN URL' ); ?></label></th>
					<td><input id="yneko-reimu-live2d-api" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_api_base_url]" value="<?php echo esc_attr( $third_party['live2d_api_base_url'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-vendor-cdn"><?php yneko_reimu_admin_bilingual_label( 'Vendor CDN 前缀', 'Vendor CDN base' ); ?></label></th>
					<td>
						<input id="yneko-reimu-vendor-cdn" class="regular-text" type="url" name="yneko_reimu_settings[third_party][vendor_cdn_base]" value="<?php echo esc_attr( $third_party['vendor_cdn_base'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '用于 Reimu 扩展包的 CDN 前缀。默认使用 jsDelivr，需要隐私优先时可替换为自托管资源。', 'CDN prefix for Reimu extension packages. The default uses jsDelivr; replace it with self-hosted resources for a privacy-first setup.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="external-comments" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '外部评论', 'External comments' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( 'WordPress 评论始终可用；第三方评论未启用或未填配置时不会加载。', 'WordPress comments are always available. Third-party comments load only when enabled and configured.' ); ?>
				<table class="form-table" role="presentation">
				<?php
				$external_comment_fields = array(
					'giscus' => array( 'Giscus', array( 'repo', 'repo_id', 'category', 'category_id' ) ),
					'utterances' => array( 'Utterances', array( 'repo' ) ),
					'disqus' => array( 'Disqus', array( 'shortname' ) ),
					'waline' => array( 'Waline', array( 'server_url' ) ),
					'twikoo' => array( 'Twikoo', array( 'env_id' ) ),
					'valine' => array( 'Valine', array( 'app_id', 'app_key', 'server_url' ) ),
				);
				?>
				<?php foreach ( $external_comment_fields as $prefix => $meta ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $meta[0] ); ?></th>
						<td>
							<label><input type="checkbox" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix ); ?>_enable]" value="1" <?php checked( '1', $external_comments[ $prefix . '_enable' ] ?? '0' ); ?>> <?php echo esc_html( $meta[0] ); ?></label>
							<?php foreach ( $meta[1] as $field ) : ?>
								<p><label><?php echo esc_html( $meta[0] . ' ' . $field ); ?><br><input class="regular-text" type="text" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix . '_' . $field ); ?>]" value="<?php echo esc_attr( $external_comments[ $prefix . '_' . $field ] ?? '' ); ?>"></label></p>
							<?php endforeach; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="friends" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '友链设置', 'Friend link settings' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '用于友链页面的卡片列表，支持名称、链接、描述和头像。', 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.' ); ?>
				<?php $site_friend = yneko_reimu_sanitize_site_friend_info( $settings['friend_site'] ?? array() ); ?>
				<h3><?php yneko_reimu_admin_bilingual_heading( '本站友链信息', 'Site friend-link info' ); ?></h3>
				<?php yneko_reimu_admin_bilingual_description( '用于友链页“本站信息”代码块。未配置图片时，将依次使用站点头像、作者头像和主题内置头像。', 'Used by the Site info code block on the friend-links page. When image is empty, the site avatar, author avatar, and bundled theme avatar are used in order.' ); ?>
				<table class="form-table yneko-reimu-site-friend-table" role="presentation">
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '名称', 'Name' ); ?></th>
						<td><input type="text" class="regular-text" name="yneko_reimu_settings[friend_site][name]" value="<?php echo esc_attr( $site_friend['name'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '链接', 'URL' ); ?></th>
						<td><input type="url" class="regular-text" name="yneko_reimu_settings[friend_site][url]" value="<?php echo esc_attr( $site_friend['url'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '描述', 'Description' ); ?></th>
						<td><input type="text" class="regular-text" name="yneko_reimu_settings[friend_site][desc]" value="<?php echo esc_attr( $site_friend['desc'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Image', 'Image' ); ?></th>
						<td>
							<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[friend_site][image]', $site_friend['image'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ), 'image/png,image/webp' ); ?>
							<?php yneko_reimu_admin_bilingual_description( '仅建议使用 WebP 或 PNG，推荐正方形 512×512，体积控制在 200KB 以内。', 'Use WebP or PNG. A square 512x512 image under 200KB is recommended.' ); ?>
						</td>
					</tr>
				</table>
				<div class="yneko-reimu-repeatable" data-repeatable="friends">
					<div class="yneko-reimu-repeatable-list">
						<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
							<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增友链', 'Add friend' ) ); ?></button>
				</div>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="music" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '曲目设置', 'Music track settings' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '播放器启用、播放行为、Meting 歌单和媒体库曲目统一在这里管理。播放器位置保留在自定义器中，方便观察侧栏布局。', 'Player enablement, playback behavior, Meting playlists, and Media Library tracks are managed here. Player position remains in the Customizer so sidebar layout can be previewed.' ); ?>
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放器入口', 'Player providers' ); ?></th>
					<td>
						<label><input type="checkbox" name="yneko_reimu_settings[player][aplayer_enable]" value="1" <?php checked( '1', $player['aplayer_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 APlayer 媒体库曲目', 'Enable APlayer Media Library tracks' ); ?></label><br>
						<label><input type="checkbox" name="yneko_reimu_settings[player][meting_enable]" value="1" <?php checked( '1', $player['meting_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Meting 歌单', 'Enable Meting playlist' ); ?></label>
						<?php yneko_reimu_admin_bilingual_description( 'APlayer 需要至少一首曲目；Meting 需要 auto URL，或同时填写 ID、server、type。配置不完整时前台不会输出空播放器。', 'APlayer needs at least one track. Meting needs an auto URL, or ID, server, and type together. Incomplete configuration does not render an empty player.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放行为', 'Playback behavior' ); ?></th>
					<td>
						<label><input type="checkbox" name="yneko_reimu_settings[player][fixed]" value="1" <?php checked( '1', $player['fixed'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '固定播放器', 'Fixed player' ); ?></label><br>
						<label><input type="checkbox" name="yneko_reimu_settings[player][autoplay]" value="1" <?php checked( '1', $player['autoplay'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '自动播放', 'Autoplay' ); ?></label><br>
						<label><input type="checkbox" name="yneko_reimu_settings[player][mutex]" value="1" <?php checked( '1', $player['mutex'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '播放器互斥', 'Mutex' ); ?></label><br>
						<label><input type="checkbox" name="yneko_reimu_settings[player][list_folded]" value="1" <?php checked( '1', $player['list_folded'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '默认折叠播放列表', 'Fold playlist by default' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放参数', 'Playback options' ); ?></th>
					<td>
						<label><?php yneko_reimu_admin_bilingual_label( '循环模式', 'Loop' ); ?>
							<select name="yneko_reimu_settings[player][loop]">
								<option value="all" <?php selected( $player['loop'], 'all' ); ?>>all</option>
								<option value="one" <?php selected( $player['loop'], 'one' ); ?>>one</option>
								<option value="none" <?php selected( $player['loop'], 'none' ); ?>>none</option>
							</select>
						</label>
						&nbsp;
						<label><?php yneko_reimu_admin_bilingual_label( '播放顺序', 'Order' ); ?>
							<select name="yneko_reimu_settings[player][order]">
								<option value="list" <?php selected( $player['order'], 'list' ); ?>>list</option>
								<option value="random" <?php selected( $player['order'], 'random' ); ?>>random</option>
							</select>
						</label>
						&nbsp;
						<label><?php yneko_reimu_admin_bilingual_label( '预加载', 'Preload' ); ?>
							<select name="yneko_reimu_settings[player][preload]">
								<option value="metadata" <?php selected( $player['preload'], 'metadata' ); ?>>metadata</option>
								<option value="none" <?php selected( $player['preload'], 'none' ); ?>>none</option>
								<option value="auto" <?php selected( $player['preload'], 'auto' ); ?>>auto</option>
							</select>
						</label>
						<p>
							<label><?php yneko_reimu_admin_bilingual_label( '默认音量 0-1', 'Volume 0-1' ); ?> <input class="small-text" type="number" min="0" max="1" step="0.1" name="yneko_reimu_settings[player][volume]" value="<?php echo esc_attr( $player['volume'] ); ?>"></label>
							&nbsp;
							<label><?php yneko_reimu_admin_bilingual_label( '歌词模式', 'LRC type' ); ?> <input class="small-text" type="number" min="0" max="3" step="1" name="yneko_reimu_settings[player][lrc_type]" value="<?php echo esc_attr( absint( $player['lrc_type'] ) ); ?>"></label>
							&nbsp;
							<label><?php yneko_reimu_admin_bilingual_label( '列表最大高度', 'List max height' ); ?> <input type="text" name="yneko_reimu_settings[player][list_max_height]" value="<?php echo esc_attr( $player['list_max_height'] ); ?>" placeholder="320px"></label>
						</p>
						<?php yneko_reimu_admin_bilingual_description( '预加载默认 metadata，避免首屏过早下载完整音频。隐私/性能优先时可选 none。', 'Preload defaults to metadata to avoid downloading full audio during first paint. Use none for a privacy/performance-first setup.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Meting 配置', 'Meting configuration' ); ?></th>
					<td>
						<p><label>Meting auto URL<br><input class="regular-text" type="url" name="yneko_reimu_settings[player][meting_auto]" value="<?php echo esc_attr( $player['meting_auto'] ); ?>"></label></p>
						<p>
							<label>Meting ID <input type="text" name="yneko_reimu_settings[player][meting_id]" value="<?php echo esc_attr( $player['meting_id'] ); ?>"></label>
							&nbsp;
							<label>server <input type="text" name="yneko_reimu_settings[player][meting_server]" value="<?php echo esc_attr( $player['meting_server'] ); ?>" placeholder="netease"></label>
							&nbsp;
							<label>type <input type="text" name="yneko_reimu_settings[player][meting_type]" value="<?php echo esc_attr( $player['meting_type'] ); ?>" placeholder="playlist"></label>
						</p>
						<?php yneko_reimu_admin_bilingual_description( '填写 auto URL 后可不填 ID/server/type。', 'When auto URL is filled, ID/server/type can stay empty.' ); ?>
					</td>
				</tr>
				</table>
				<h3><?php yneko_reimu_admin_bilingual_heading( '媒体库曲目', 'Media Library tracks' ); ?></h3>
				<?php yneko_reimu_admin_bilingual_description( '未配置曲目且未配置 Meting 时，前台不会加载音乐播放器。', 'If neither tracks nor Meting are configured, the front-end music player is not loaded.' ); ?>
				<div class="yneko-reimu-repeatable" data-repeatable="music">
					<div class="yneko-reimu-repeatable-list">
						<?php foreach ( yneko_reimu_sanitize_music_items( $settings['music'] ) as $index => $track ) : ?>
							<?php yneko_reimu_render_music_row( $index, $track ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button yneko-reimu-add-row" data-template="music"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增曲目', 'Add track' ) ); ?></button>
				</div>
			</section>

			<div class="yneko-reimu-floating-submit">
				<span class="yneko-reimu-floating-submit__hint"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_text( '切换标签页不会保存修改。', 'Switching tabs does not save changes.' ) ); ?></span>
				<button type="submit" class="button button-primary yneko-reimu-submit-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '保存设置', 'Save settings' ) ); ?></button>
			</div>
		</form>
		<form id="yneko-reimu-admin-gif-upload-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ); ?>">
			<?php wp_nonce_field( 'yneko_reimu_admin_comment_gif_upload' ); ?>
			<input type="hidden" name="yneko_reimu_admin_comment_gif_upload" value="1">
		</form>
	</div>
	<?php
}

function yneko_reimu_render_admin_comment_gif_upload() {
	$status = isset( $_GET['yneko_comment_gif_upload'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_gif_upload'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$count  = isset( $_GET['yneko_comment_gif_count'] ) ? absint( $_GET['yneko_comment_gif_count'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $status ) {
		$messages = array(
			'success' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF 并加入表情库。', '已上传 %d 个 GIF 并加入表情库。', $count, 'yneko-reimu' ),
				$count
			) : __( 'GIF 已上传并加入表情库。', 'yneko-reimu' ),
			'partial' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF，部分文件未成功。', '已上传 %d 个 GIF，部分文件未成功。', $count, 'yneko-reimu' ),
				$count
			) : __( '部分 GIF 上传失败。', 'yneko-reimu' ),
			'empty'   => __( '请选择要上传的 GIF。', 'yneko-reimu' ),
			'invalid' => __( '仅支持未超出大小限制的 GIF 文件。', 'yneko-reimu' ),
			'failed'  => __( 'GIF 上传失败。', 'yneko-reimu' ),
		);
		$class = in_array( $status, array( 'success', 'partial' ), true ) ? 'notice notice-success inline' : 'notice notice-error inline';
		echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $messages[ $status ] ?? $messages['failed'] ) . '</p></div>';
	}
	?>
	<div class="yneko-reimu-admin-gif-upload">
		<input id="yneko-reimu-admin-gif-file" form="yneko-reimu-admin-gif-upload-form" type="file" name="yneko_reimu_comment_gif[]" accept="image/gif" multiple hidden>
		<button type="button" class="button button-primary yneko-reimu-admin-gif-pick" data-yneko-admin-gif-pick><?php yneko_reimu_admin_bilingual_label( '上传本地 GIF 并入库', 'Upload local GIFs' ); ?></button>
		<button type="button" class="button yneko-reimu-admin-gif-media" data-yneko-admin-gif-media data-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_admin_add_gif_media' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '从媒体库加入 GIF', 'Add GIF from Media Library' ); ?></button>
	</div>
	<?php
}

function yneko_reimu_render_friend_row( $index, $friend = array() ) {
	$friend = wp_parse_args(
		$friend,
		array(
			'name'  => '',
			'url'   => '',
			'desc'  => '',
			'image' => '',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-heading" data-row-label="friend">
			<span class="yneko-reimu-row-number"></span>
		</div>
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-friend">
			<label><?php yneko_reimu_admin_bilingual_label( '名称', 'Name' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $friend['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '链接', 'URL' ); ?><input type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $friend['url'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '描述', 'Description' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][desc]" value="<?php echo esc_attr( $friend['desc'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '头像', 'Avatar' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][image]" value="<?php echo esc_attr( $friend['image'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_music_row( $index, $track = array() ) {
	$track = wp_parse_args(
		$track,
		array(
			'name'   => '',
			'artist' => '',
			'url'    => '',
			'cover'  => '',
			'lrc'    => '',
			'theme'  => '#ff5252',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-heading" data-row-label="music">
			<span class="yneko-reimu-row-number"></span>
		</div>
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-music">
			<label><?php yneko_reimu_admin_bilingual_label( '歌名', 'Track title' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $track['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '作者', 'Artist' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][artist]" value="<?php echo esc_attr( $track['artist'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '音频', 'Audio' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $track['url'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '封面', 'Cover' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][cover]" value="<?php echo esc_attr( $track['cover'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '歌词 LRC', 'Lyrics LRC' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][lrc]" value="<?php echo esc_attr( $track['lrc'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '主题色', 'Theme color' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][theme]" value="<?php echo esc_attr( $track['theme'] ); ?>"></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_comment_upload_admin() {
	if ( ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return;
	}

	$review_badges = yneko_reimu_admin_review_badge_counts();
	$items = array_merge( yneko_reimu_comment_pending_temp_uploads( 80 ), yneko_reimu_comment_upload_library( 80, 'all', true ) );
	if ( ! $items ) {
		echo '<p class="description">' . esc_html__( '暂无评论图片或 GIF 上传。', 'yneko-reimu' ) . '</p>';
		return;
	}
	$groups = array(
		'admin_gif'  => array(
			'title' => __( '后台上传的 GIF', 'yneko-reimu' ),
			'items' => array(),
		),
		'user_gif'   => array(
			'title' => __( '用户评论 GIF', 'yneko-reimu' ),
			'badge' => $review_badges['comment_gifs'] ?? 0,
			'items' => array(),
		),
		'user_image' => array(
			'title' => __( '用户评论图片', 'yneko-reimu' ),
			'badge' => $review_badges['comment_images'] ?? 0,
			'items' => array(),
		),
	);
	foreach ( $items as $item ) {
		if ( 'gif' === $item['type'] && empty( $item['comment_id'] ) ) {
			$groups['admin_gif']['items'][] = $item;
		} elseif ( 'gif' === $item['type'] ) {
			$groups['user_gif']['items'][] = $item;
		} else {
			$groups['user_image']['items'][] = $item;
		}
	}
	?>
	<?php foreach ( $groups as $group ) : ?>
		<div class="yneko-reimu-upload-admin-section">
			<h3><?php echo esc_html( $group['title'] ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $group['badge'] ?? 0 ) ); ?></h3>
			<?php if ( empty( $group['items'] ) ) : ?>
				<p class="description"><?php esc_html_e( '暂无可选...', 'yneko-reimu' ); ?></p>
				<?php continue; ?>
			<?php endif; ?>
			<div class="yneko-reimu-upload-admin-grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<?php
					$id     = $item['id'];
					$is_temp = is_string( $id ) && 0 === strpos( $id, 'temp:' );
					$type   = 'gif' === $item['type'] ? 'gif' : 'image';
					$status = in_array( (string) $item['status'], array( 'approved', 'pending', 'revoked', 'rejected' ), true ) ? (string) $item['status'] : 'pending';
					$user   = $item['user'] ? get_user_by( 'id', $item['user'] ) : null;
					$label  = 'gif' === $type ? __( 'GIF', 'yneko-reimu' ) : __( '图片', 'yneko-reimu' );
					$temp_relative = $is_temp ? rawurldecode( substr( $id, 5 ) ) : '';
					?>
					<div class="yneko-reimu-upload-admin-card">
						<img src="<?php echo esc_url( $item['url'] ); ?>" alt="">
						<div class="yneko-reimu-upload-admin-meta">
							<strong>
								<?php
								if ( 'rejected' === $status ) {
									esc_html_e( '审核未通过', 'yneko-reimu' );
								} elseif ( 'revoked' === $status ) {
									esc_html_e( '已撤销', 'yneko-reimu' );
								} elseif ( 'pending' === $status ) {
									echo 'gif' === $type ? esc_html__( 'GIF 待审核', 'yneko-reimu' ) : esc_html__( '图片待审核', 'yneko-reimu' );
								} else {
									echo 'gif' === $type ? esc_html__( 'GIF 已通过', 'yneko-reimu' ) : esc_html__( '图片已通过', 'yneko-reimu' );
								}
								?>
							</strong>
							<span><?php echo esc_html( $label ); ?></span>
							<span><?php echo esc_html( $user ? $user->display_name : __( '未知用户', 'yneko-reimu' ) ); ?></span>
							<span><?php echo esc_html( $item['date'] ); ?></span>
						</div>
						<div class="yneko-reimu-upload-admin-actions">
							<?php if ( 'pending' === $status || 'revoked' === $status ) : ?>
								<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( $is_temp ? array( 'yneko_comment_upload_action' => 'approve', 'temp_upload' => $temp_relative ) : array( 'yneko_comment_upload_action' => 'approve', 'attachment_id' => absint( $id ) ) ), $is_temp ? yneko_reimu_comment_temp_upload_nonce_action( 'approve', $temp_relative ) : 'yneko_reimu_comment_upload_approve_' . absint( $id ) ) ); ?>"><?php esc_html_e( '通过', 'yneko-reimu' ); ?></a>
							<?php endif; ?>
							<?php if ( 'pending' === $status ) : ?>
								<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( $is_temp ? array( 'yneko_comment_upload_action' => 'reject', 'temp_upload' => $temp_relative ) : array( 'yneko_comment_upload_action' => 'reject', 'attachment_id' => absint( $id ) ) ), $is_temp ? yneko_reimu_comment_temp_upload_nonce_action( 'reject', $temp_relative ) : 'yneko_reimu_comment_upload_reject_' . absint( $id ) ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
							<?php elseif ( 'approved' === $status ) : ?>
								<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_comment_upload_action' => 'revoke', 'attachment_id' => absint( $id ) ) ), 'yneko_reimu_comment_upload_revoke_' . absint( $id ) ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
							<?php endif; ?>
							<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( $is_temp ? array( 'yneko_comment_upload_action' => 'delete', 'temp_upload' => $temp_relative ) : array( 'yneko_comment_upload_action' => 'delete', 'attachment_id' => absint( $id ) ) ), $is_temp ? yneko_reimu_comment_temp_upload_nonce_action( 'delete', $temp_relative ) : 'yneko_reimu_comment_upload_delete_' . absint( $id ) ) ); ?>"><?php esc_html_e( '删除', 'yneko-reimu' ); ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	<?php
}

function yneko_reimu_render_user_avatar_admin() {
	$users = get_users(
		array(
			'number'     => 120,
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_avatar_url',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_avatar_pending_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	if ( ! $users ) {
		echo '<p class="description">' . esc_html__( '暂无用户上传头像。', 'yneko-reimu' ) . '</p>';
		return;
	}
	?>
	<div class="yneko-reimu-upload-admin-grid yneko-reimu-user-avatar-grid">
		<?php foreach ( $users as $user ) : ?>
			<?php
			$user_id = absint( $user->ID );
			$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
			$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
			$status  = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true );
			$shown   = $pending ? $pending : $current;
			if ( ! $shown ) {
				continue;
			}
			?>
			<div class="yneko-reimu-upload-admin-card">
				<img src="<?php echo esc_url( $shown ); ?>" alt="">
				<div class="yneko-reimu-upload-admin-meta">
					<strong><?php echo esc_html( $user->display_name ? $user->display_name : $user->user_login ); ?></strong>
					<span><?php echo esc_html( $user->user_email ); ?></span>
					<span><?php echo 'pending' === $status ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '已应用头像', 'yneko-reimu' ); ?></span>
				</div>
				<div class="yneko-reimu-upload-admin-actions">
					<?php if ( $pending ) : ?>
						<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'approve', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_approve_' . $user_id ) ); ?>"><?php esc_html_e( '批准头像', 'yneko-reimu' ); ?></a>
						<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'reject', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_reject_' . $user_id ) ); ?>"><?php esc_html_e( '驳回并删除', 'yneko-reimu' ); ?></a>
					<?php endif; ?>
					<?php if ( $current ) : ?>
						<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'delete', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_delete_' . $user_id ) ); ?>"><?php esc_html_e( '删除头像', 'yneko-reimu' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_render_user_badge_admin() {
	$users = get_users(
		array(
			'number'     => 200,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_comment_tags',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	if ( ! $users ) {
		echo '<p class="description">' . esc_html__( '暂无用户自定义标签。', 'yneko-reimu' ) . '</p>';
		return;
	}
	?>
	<div class="yneko-reimu-user-badge-admin">
		<?php foreach ( $users as $user ) : ?>
			<?php
			$user_id = absint( $user->ID );
			$active  = function_exists( 'yneko_reimu_comment_user_custom_tags' ) ? yneko_reimu_comment_user_custom_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags', true );
			$pending = function_exists( 'yneko_reimu_comment_user_pending_tags' ) ? yneko_reimu_comment_user_pending_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', true );
			$active  = is_array( $active ) ? array_values( $active ) : array();
			$pending = is_array( $pending ) ? array_values( $pending ) : array();
			if ( ! $active && ! $pending ) {
				continue;
			}
			?>
			<div class="yneko-reimu-user-badge-card">
				<div class="yneko-reimu-user-badge-card__user">
					<strong><?php echo esc_html( $user->display_name ? $user->display_name : $user->user_login ); ?></strong>
					<span><?php echo esc_html( $user->user_email ); ?></span>
				</div>
				<div class="yneko-reimu-user-badge-card__tags">
					<?php foreach ( array( 'pending' => $pending, 'active' => $active ) as $status => $tags ) : ?>
						<?php foreach ( $tags as $index => $tag ) : ?>
							<?php
							if ( ! is_array( $tag ) ) {
								continue;
							}
							$label = function_exists( 'yneko_reimu_sanitize_comment_tag_label' ) ? yneko_reimu_sanitize_comment_tag_label( $tag['label'] ?? '' ) : sanitize_text_field( $tag['label'] ?? '' );
							$color = sanitize_hex_color( $tag['color'] ?? '' );
							if ( '' === $label ) {
								continue;
							}
							?>
							<div class="yneko-reimu-user-badge-item">
								<span class="yneko-reimu-user-badge-pill" style="<?php echo esc_attr( $color ? '--badge-color:' . $color . ';' : '' ); ?>"><?php echo esc_html( $label ); ?></span>
								<span class="description"><?php echo '0' === (string) ( $tag['enabled'] ?? '1' ) ? esc_html__( '未启用', 'yneko-reimu' ) : ( 'pending' === $status ? esc_html__( '待审核', 'yneko-reimu' ) : esc_html__( '已通过', 'yneko-reimu' ) ); ?></span>
								<?php if ( 'pending' === $status && ! empty( $tag['old_label'] ) ) : ?>
									<?php
									$old_label_text = sprintf(
										/* translators: %s: previous user badge label. */
										__( '原标签：%s', 'yneko-reimu' ),
										$tag['old_label']
									);
									?>
									<span class="description"><?php echo esc_html( $old_label_text ); ?></span>
								<?php endif; ?>
								<div class="yneko-reimu-user-badge-actions">
									<?php if ( 'pending' === $status ) : ?>
										<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'approve', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_approve_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '批准', 'yneko-reimu' ); ?></a>
										<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'reject', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_reject_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
									<?php else : ?>
										<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'revoke', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_revoke_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_enqueue_settings_admin_assets( $hook ) {
	if ( 'appearance_page_yneko-reimu-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_register_style( 'yneko-reimu-admin-settings', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-admin-settings' );
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-settings-page{padding-bottom:96px}.yneko-reimu-settings-tabs{display:flex;flex-wrap:wrap;gap:0;margin-top:20px}.yneko-reimu-settings-tabs .nav-tab{display:inline-flex;align-items:center;min-height:40px;margin-left:0;margin-right:6px;padding:8px 15px;background:#f0f0f1;border-bottom:1px solid #c3c4c7;color:#1d2327;cursor:pointer}.yneko-reimu-settings-tabs .nav-tab-active{background:#fff;border-bottom-color:#fff;color:#2271b1}.yneko-reimu-settings-panel{max-width:1280px;padding-top:4px}.yneko-reimu-settings-panel[hidden]{display:none!important}.yneko-reimu-settings-panel h2:first-child{margin-top:24px}.yneko-reimu-floating-submit{position:fixed;z-index:20;right:20px;bottom:0;left:180px;display:flex;align-items:center;justify-content:flex-end;gap:16px;min-height:64px;padding:12px 24px;background:rgba(240,240,241,.94);border-top:1px solid #dcdcde;box-shadow:0 -8px 24px rgba(0,0,0,.08);backdrop-filter:saturate(140%) blur(8px)}.folded .yneko-reimu-floating-submit{left:56px}.yneko-reimu-floating-submit__hint{color:#646970}.yneko-reimu-settings-page h2{margin-top:32px}.yneko-reimu-admin-text{line-height:1.35}.description.yneko-reimu-admin-text,.yneko-reimu-admin-text.description,.yneko-reimu-settings-page p.yneko-reimu-admin-text{display:block;margin:6px 0 0;color:#646970}.yneko-reimu-settings-page .button .yneko-reimu-admin-text{vertical-align:middle}.yneko-reimu-submit-button .yneko-reimu-admin-text{color:#fff}.yneko-reimu-admin-gif-upload{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin:14px 0 18px;padding:12px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-admin-gif-upload label{display:flex;flex-direction:column;gap:6px;font-weight:600}.yneko-reimu-special-badge-table{display:flex;flex-direction:column;gap:10px;max-width:760px}.yneko-reimu-special-badge-row{display:grid;grid-template-columns:130px minmax(120px,1fr) minmax(120px,1fr);gap:8px;align-items:center;padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-special-badge-row-extra{grid-template-columns:90px minmax(110px,.8fr) minmax(120px,1fr) minmax(120px,1fr)}.yneko-reimu-special-badge-row input[type=text]{width:100%}.yneko-reimu-media-field,.yneko-reimu-inline-media{display:flex;gap:8px;align-items:center}.yneko-reimu-inline-media input{flex:1;min-width:0}.yneko-reimu-repeatable-row{margin:14px 0;padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-row-heading{display:flex;align-items:center;margin:-2px 0 14px}.yneko-reimu-row-number{display:inline-flex;align-items:center;min-height:24px;padding:3px 10px;border-radius:999px;background:#f6f7f7;color:#1d2327;font-weight:600}.yneko-reimu-row-grid{display:grid;gap:12px}.yneko-reimu-row-grid-friend{grid-template-columns:repeat(4,minmax(0,1fr))}.yneko-reimu-row-grid-music{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-row-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.yneko-reimu-row-grid input{width:100%}.yneko-reimu-row-actions{display:flex;gap:8px;margin-top:12px}.yneko-reimu-upload-admin-section{margin-top:22px}.yneko-reimu-upload-admin-section h3{margin:0 0 10px}.yneko-reimu-upload-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-top:14px}.yneko-reimu-upload-admin-card{padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-upload-admin-card img{display:block;width:100%;aspect-ratio:1;object-fit:cover;border-radius:6px;background:#f6f7f7}.yneko-reimu-upload-admin-meta{display:flex;flex-direction:column;gap:3px;margin:9px 0;color:#646970;font-size:12px}.yneko-reimu-upload-admin-meta strong{color:#1d2327}.yneko-reimu-upload-admin-actions{display:flex;flex-wrap:wrap;gap:6px}@media(max-width:960px){.yneko-reimu-row-grid-friend,.yneko-reimu-row-grid-music{grid-template-columns:1fr}.yneko-reimu-floating-submit{left:0;right:0;justify-content:space-between;padding:10px 14px}}@media(max-width:782px){.yneko-reimu-settings-tabs .nav-tab{flex:1 1 150px;margin-right:4px}.yneko-reimu-floating-submit{min-height:74px}.yneko-reimu-floating-submit__hint{display:none}}'
	);
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-admin-gif-upload{align-items:center;padding:16px;background:linear-gradient(180deg,#fff,#fbfbfc)}.yneko-reimu-admin-gif-upload .button{display:inline-flex;align-items:center;justify-content:center;min-height:34px;border-radius:6px;font-weight:600}.yneko-reimu-admin-gif-pick:before{content:"+";margin-right:6px;font-weight:700}.yneko-reimu-admin-gif-media:before{content:"";width:14px;height:14px;margin-right:6px;border:2px solid currentColor;border-radius:3px;box-sizing:border-box}.yneko-reimu-admin-gif-upload .button.is-loading{pointer-events:none;opacity:.72}.yneko-reimu-upload-admin-actions .button{border-radius:5px}.yneko-reimu-upload-admin-actions .button-link-delete{color:#b32d2e}'
	);
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-special-badge-table{max-width:1180px}.yneko-reimu-special-badge-row{display:grid;grid-template-columns:130px minmax(110px,.8fr) minmax(110px,.8fr) minmax(260px,1.3fr);gap:8px;align-items:center;padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-special-badge-row .description{grid-column:1/-1;margin:0;color:#646970}.yneko-reimu-special-badge-row .yneko-reimu-inline-media{min-width:0}.yneko-reimu-user-badge-admin{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;margin-top:14px}.yneko-reimu-user-badge-card{padding:14px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-user-badge-card__user{display:flex;flex-direction:column;gap:3px;margin-bottom:10px}.yneko-reimu-user-badge-card__user span{color:#646970}.yneko-reimu-user-badge-card__tags{display:flex;flex-direction:column;gap:8px}.yneko-reimu-user-badge-item{display:grid;grid-template-columns:auto 1fr auto;gap:8px;align-items:center;padding:8px;border:1px solid #f0f0f1;border-radius:8px;background:#fbfbfc}.yneko-reimu-user-badge-pill{display:inline-flex;align-items:center;width:max-content;max-width:120px;padding:3px 8px;border-radius:999px;color:var(--badge-color,#2271b1);background:color-mix(in srgb,var(--badge-color,#2271b1) 10%,#fff);border:1px solid color-mix(in srgb,var(--badge-color,#2271b1) 24%,#fff);font-size:12px;font-weight:700}.yneko-reimu-user-badge-actions{display:flex;gap:5px}.yneko-reimu-settings-tabs .nav-tab{position:relative;gap:7px}.yneko-reimu-settings-page h2,.yneko-reimu-settings-page th{position:relative}.yneko-reimu-admin-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;margin-left:7px;padding:0 6px;border-radius:999px;background:#d63638;color:#fff;font-size:11px;font-weight:700;line-height:18px;box-shadow:0 0 0 2px #fff}.nav-tab .yneko-reimu-admin-badge{position:absolute;top:-8px;right:-8px;margin-left:0}@media(max-width:960px){.yneko-reimu-special-badge-row,.yneko-reimu-user-badge-item{grid-template-columns:1fr}}'
	);

	wp_register_script( 'yneko-reimu-admin-settings', false, array( 'jquery' ), YNEKO_REIMU_VERSION, true );
	wp_enqueue_script( 'yneko-reimu-admin-settings' );
	$admin_i18n = array(
		'locale'          => yneko_reimu_admin_prefers_zh() ? 'zh' : 'en',
		'mediaTitle'      => array( 'zh' => '选择媒体', 'en' => 'Select media' ),
		'useMedia'        => array( 'zh' => '使用此媒体', 'en' => 'Use this media' ),
		'invalidImage'    => array( 'zh' => '请选择此字段允许的图片格式。', 'en' => 'Please choose an image format allowed by this field.' ),
		'choose'          => array( 'zh' => '选择', 'en' => 'Choose' ),
		'remove'          => array( 'zh' => '删除', 'en' => 'Remove' ),
		'deleteUpload'    => array( 'zh' => '确定删除这个评论上传文件吗？', 'en' => 'Delete this comment upload file?' ),
		'adminGifTitle'   => array( 'zh' => '选择 GIF', 'en' => 'Select GIF' ),
		'adminGifUse'     => array( 'zh' => '加入表情库', 'en' => 'Add to library' ),
		'adminGifInvalid' => array( 'zh' => '请选择 GIF 文件。', 'en' => 'Please select a GIF file.' ),
		'adminGifAdded'   => array( 'zh' => 'GIF 已加入表情库。', 'en' => 'GIF added to the library.' ),
		'adminGifFailed'  => array( 'zh' => 'GIF 入库失败。', 'en' => 'Failed to add GIF.' ),
		'name'            => array( 'zh' => '名称', 'en' => 'Name' ),
		'url'             => array( 'zh' => '链接', 'en' => 'URL' ),
		'description'     => array( 'zh' => '描述', 'en' => 'Description' ),
		'avatar'          => array( 'zh' => '头像', 'en' => 'Avatar' ),
		'trackTitle'      => array( 'zh' => '歌名', 'en' => 'Track title' ),
		'artist'          => array( 'zh' => '作者', 'en' => 'Artist' ),
		'audio'           => array( 'zh' => '音频', 'en' => 'Audio' ),
		'cover'           => array( 'zh' => '封面', 'en' => 'Cover' ),
		'lyrics'          => array( 'zh' => '歌词 LRC', 'en' => 'Lyrics LRC' ),
		'themeColor'      => array( 'zh' => '主题色', 'en' => 'Theme color' ),
		'friendItem'      => array( 'zh' => '友链', 'en' => 'Friend' ),
		'musicItem'       => array( 'zh' => '曲目', 'en' => 'Track' ),
	);
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		'window.YNEKO_REIMU_ADMIN_I18N=' . wp_json_encode( $admin_i18n ) . ';' .
		"(function(){var labels=window.YNEKO_REIMU_ADMIN_I18N||{};var locale=labels.locale==='zh'?'zh':'en';var counters={friend:Date.now(),music:Date.now()+1000};function esc(value){return String(value||'').replace(/[&<>\"']/g,function(chr){return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',\"'\":'&#039;'}[chr];});}function plain(key,zh,en){var item=labels[key]||{};return item[locale]||(locale==='zh'?zh:en);}function labelText(key,zh,en){return '<span class=\"yneko-reimu-admin-text\">'+esc(plain(key,zh,en))+'</span>';}function fieldLabel(key,zh,en,control){return '<label>'+labelText(key,zh,en)+control+'</label>';}function rowHeading(type){return '<div class=\"yneko-reimu-row-heading\" data-row-label=\"'+type+'\"><span class=\"yneko-reimu-row-number\"></span></div>';}function rowTitle(type,index){var key=type==='music'?'musicItem':'friendItem';var fallbackZh=type==='music'?'曲目':'友链';var fallbackEn=type==='music'?'Track':'Friend';return '<span class=\"yneko-reimu-admin-text\">'+esc(plain(key,fallbackZh,fallbackEn))+' #'+index+'</span>';}function isAccepted(input,url){var accept=(input&&input.dataset?input.dataset.accept:'')||'';if(!accept){return true;}var allowed=[];if(/image\\/png/.test(accept)){allowed.push('png');}if(/image\\/webp/.test(accept)){allowed.push('webp');}if(/image\\/avif/.test(accept)){allowed.push('avif');}if(/image\\/jpe?g/.test(accept)){allowed.push('jpe?g');}if(!allowed.length){return true;}return new RegExp('\\\\.('+allowed.join('|')+')(?:[?#].*)?$','i').test(url||'');}function activateTab(name){var tabs=document.querySelectorAll('[data-yneko-settings-tab]');var panels=document.querySelectorAll('[data-yneko-settings-panel]');var exists=false;tabs.forEach(function(tab){if(tab.getAttribute('data-yneko-settings-tab')===name){exists=true;}});if(!exists){name='general';}tabs.forEach(function(tab){var active=tab.getAttribute('data-yneko-settings-tab')===name;tab.classList.toggle('nav-tab-active',active);tab.setAttribute('aria-selected',active?'true':'false');});panels.forEach(function(panel){var active=panel.getAttribute('data-yneko-settings-panel')===name;panel.hidden=!active;panel.classList.toggle('is-active',active);});try{window.localStorage.setItem('ynekoReimuSettingsTab',name);}catch(error){}if(window.location.hash!=='#'+name){try{history.replaceState(null,'','#'+name);}catch(error){}}}function initTabs(){var initial=(window.location.hash||'').replace(/^#/,'');if(!initial){try{initial=window.localStorage.getItem('ynekoReimuSettingsTab')||'';}catch(error){}}activateTab(initial||'general');document.querySelectorAll('[data-yneko-settings-tab]').forEach(function(tab){tab.addEventListener('click',function(event){event.preventDefault();activateTab(tab.getAttribute('data-yneko-settings-tab')||'general');});});window.addEventListener('hashchange',function(){activateTab((window.location.hash||'').replace(/^#/,''));});}function refreshNumbers(root){(root||document).querySelectorAll('.yneko-reimu-repeatable').forEach(function(section){var type=section.dataset.repeatable==='music'?'music':'friend';section.querySelectorAll('.yneko-reimu-repeatable-row').forEach(function(row,index){var heading=row.querySelector('.yneko-reimu-row-heading');if(!heading){heading=document.createElement('div');heading.className='yneko-reimu-row-heading';heading.setAttribute('data-row-label',type);heading.innerHTML='<span class=\"yneko-reimu-row-number\"></span>';row.insertBefore(heading,row.firstChild);}var number=heading.querySelector('.yneko-reimu-row-number');if(number){number.innerHTML=rowTitle(type,index+1);}});});}function media(button){var field=button.closest('.yneko-reimu-inline-media')||button.closest('.yneko-reimu-media-field');var input=field?field.querySelector('.yneko-reimu-media-url'):null;if(!input||!window.wp||!wp.media){return;}var accept=(input.dataset&&input.dataset.accept)||'';var frame=wp.media({title:plain('mediaTitle','选择媒体','Select media'),button:{text:plain('useMedia','使用此媒体','Use this media')},library:accept?{type:accept.split(',')}:undefined,multiple:false});frame.on('select',function(){var attachment=frame.state().get('selection').first().toJSON();var url=attachment.url||'';if(!isAccepted(input,url)){window.alert(plain('invalidImage','请选择此字段允许的图片格式。','Please choose an image format allowed by this field.'));return;}input.value=url;input.dispatchEvent(new Event('change',{bubbles:true}));});frame.open();}function pickButton(){return '<button type=\"button\" class=\"button yneko-reimu-media-button\">'+labelText('choose','选择','Choose')+'</button>';}function mediaInput(name){return '<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"'+name+'\">'+pickButton()+'</span>';}function friendTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\">'+rowHeading('friend')+'<div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-friend\">'+fieldLabel('name','名称','Name','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][name]\">')+fieldLabel('url','链接','URL','<input type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][url]\">')+fieldLabel('description','描述','Description','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][desc]\">')+fieldLabel('avatar','头像','Avatar','<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][image]\">'+pickButton()+'</span>')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+labelText('remove','删除','Remove')+'</button></div></div>';}function musicTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\">'+rowHeading('music')+'<div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-music\">'+fieldLabel('trackTitle','歌名','Track title','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][name]\">')+fieldLabel('artist','作者','Artist','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][artist]\">')+fieldLabel('audio','音频','Audio',mediaInput('yneko_reimu_settings[music]['+i+'][url]'))+fieldLabel('cover','封面','Cover',mediaInput('yneko_reimu_settings[music]['+i+'][cover]'))+fieldLabel('lyrics','歌词 LRC','Lyrics LRC',mediaInput('yneko_reimu_settings[music]['+i+'][lrc]'))+fieldLabel('themeColor','主题色','Theme color','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][theme]\" value=\"#ff5252\">')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+labelText('remove','删除','Remove')+'</button></div></div>';}document.addEventListener('change',function(event){var input=event.target&&event.target.matches&&event.target.matches('.yneko-reimu-media-url[data-accept]')?event.target:null;if(input&&input.value&&!isAccepted(input,input.value)){window.alert(plain('invalidImage','请选择此字段允许的图片格式。','Please choose an image format allowed by this field.'));input.value='';}});document.addEventListener('click',function(event){var target=event.target;if(target.closest('[data-yneko-upload-delete]')&&!window.confirm(plain('deleteUpload','确定删除这个评论上传文件吗？','Delete this comment upload file?'))){event.preventDefault();return;}if(target.closest('.yneko-reimu-media-button')){event.preventDefault();media(target.closest('.yneko-reimu-media-button'));}if(target.closest('.yneko-reimu-remove-row')){event.preventDefault();var repeatable=target.closest('.yneko-reimu-repeatable');target.closest('.yneko-reimu-repeatable-row').remove();refreshNumbers(repeatable||document);}var add=target.closest('.yneko-reimu-add-row');if(add){event.preventDefault();var type=add.dataset.template;var repeatable=add.closest('.yneko-reimu-repeatable');var list=repeatable.querySelector('.yneko-reimu-repeatable-list');var i=counters[type]++;list.insertAdjacentHTML('beforeend',type==='friend'?friendTemplate(i):musicTemplate(i));refreshNumbers(repeatable);}});initTabs();refreshNumbers();}());"
	);
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		"(function(){var labels=window.YNEKO_REIMU_ADMIN_I18N||{};var locale=labels.locale==='zh'?'zh':'en';function plain(key,zh,en){var item=labels[key]||{};return item[locale]||(locale==='zh'?zh:en);}function setLoading(button,loading){if(!button){return;}button.classList.toggle('is-loading',!!loading);button.disabled=!!loading;}var file=document.getElementById('yneko-reimu-admin-gif-file');var form=document.getElementById('yneko-reimu-admin-gif-upload-form');document.addEventListener('click',function(event){var pick=event.target&&event.target.closest?event.target.closest('[data-yneko-admin-gif-pick]'):null;if(pick){event.preventDefault();if(file){file.click();}return;}var mediaButton=event.target&&event.target.closest?event.target.closest('[data-yneko-admin-gif-media]'):null;if(!mediaButton){return;}event.preventDefault();if(!window.wp||!wp.media){return;}var frame=wp.media({title:plain('adminGifTitle','选择 GIF','Select GIF'),button:{text:plain('adminGifUse','加入表情库','Add to library')},library:{type:'image/gif'},multiple:false});frame.on('select',function(){var attachment=frame.state().get('selection').first().toJSON();if(!attachment||attachment.mime!=='image/gif'){window.alert(plain('adminGifInvalid','请选择 GIF 文件。','Please select a GIF file.'));return;}var data=new FormData();data.append('action','yneko_reimu_admin_add_gif_media');data.append('nonce',mediaButton.getAttribute('data-nonce')||'');data.append('attachment_id',attachment.id||'');setLoading(mediaButton,true);fetch(window.ajaxurl,{method:'POST',credentials:'same-origin',body:data}).then(function(response){return response.json().catch(function(){return {success:false,data:{message:plain('adminGifFailed','GIF 入库失败。','Failed to add GIF.')}};});}).then(function(payload){if(!payload||!payload.success){window.alert(payload&&payload.data&&payload.data.message?payload.data.message:plain('adminGifFailed','GIF 入库失败。','Failed to add GIF.'));setLoading(mediaButton,false);return;}window.location.href=window.location.href.replace(/#.*$/,'')+'#comments';window.location.reload();}).catch(function(){window.alert(plain('adminGifFailed','GIF 入库失败。','Failed to add GIF.'));setLoading(mediaButton,false);});});frame.open();});if(file&&form){file.addEventListener('change',function(){if(file.files&&file.files.length){form.submit();}});}}());"
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_enqueue_settings_admin_assets' );
