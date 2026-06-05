<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	$auth_security = isset( $input['auth_security'] ) && is_array( $input['auth_security'] ) ? $input['auth_security'] : array();
	$security = isset( $input['security'] ) && is_array( $input['security'] ) ? $input['security'] : array();
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
		'auth_security'     => function_exists( 'yneko_reimu_sanitize_auth_security_settings' ) ? yneko_reimu_sanitize_auth_security_settings( $auth_security, $defaults['auth_security'] ) : $defaults['auth_security'],
		'security'          => yneko_reimu_sanitize_settings_bool_group(
			$security,
			$defaults['security'],
			array( 'allow_svg_uploads', 'comment_ip_region_lookup' )
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
			array( 'preloader_enable', 'top_enable', 'triangle_badge', 'firework_enable', 'pjax_enable', 'busuanzi_enable', 'katex_enable', 'photoswipe_enable', 'mermaid_enable', 'custom_cursor', 'show_admin_toolbar' )
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
