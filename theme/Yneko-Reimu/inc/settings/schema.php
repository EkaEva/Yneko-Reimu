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
