<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/media.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/users.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/schema/sanitizers/groups.php';

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

function yneko_reimu_sanitize_settings( $input ) {
	$defaults = yneko_reimu_settings_defaults();
	$input    = is_array( $input ) ? $input : array();
	$current = get_option( 'yneko_reimu_settings', array() );
	$current = is_array( $current ) ? $current : array();

	return array(
		'site_avatar_url'   => array_key_exists( 'site_avatar_url', $input ) ? yneko_reimu_normalize_settings_url( $input['site_avatar_url'] ) : yneko_reimu_normalize_settings_url( $current['site_avatar_url'] ?? '' ),
		'author_avatar_url' => array_key_exists( 'author_avatar_url', $input ) ? yneko_reimu_normalize_settings_url( $input['author_avatar_url'] ) : yneko_reimu_normalize_settings_url( $current['author_avatar_url'] ?? '' ),
		'comment_avatar_url'=> yneko_reimu_normalize_settings_url( $input['comment_avatar_url'] ?? '' ),
		'favicon_fallback_url' => yneko_reimu_normalize_png_jpeg_url( $input['favicon_fallback_url'] ?? '' ),
		'comment_upload'    => yneko_reimu_sanitize_comment_upload_settings( yneko_reimu_settings_group_input( $input, 'comment_upload' ) ),
		'user_badges'       => yneko_reimu_sanitize_user_badges_settings( yneko_reimu_settings_group_input( $input, 'user_badges' ), $defaults['user_badges'] ),
		'github_url'        => yneko_reimu_normalize_settings_url( $input['github_url'] ?? '' ),
		'friend_site'       => yneko_reimu_sanitize_site_friend_info( $input['friend_site'] ?? array() ),
		'friends'           => yneko_reimu_sanitize_friend_items( array_key_exists( 'friends', $input ) ? $input['friends'] : array() ),
		'sponsor_qr_url'    => yneko_reimu_normalize_settings_url( $input['sponsor_qr_url'] ?? '' ),
		'github_oauth'      => yneko_reimu_sanitize_github_oauth_settings( yneko_reimu_settings_group_input( $input, 'github_oauth' ) ),
		'auth_security'     => function_exists( 'yneko_reimu_sanitize_auth_security_settings' ) ? yneko_reimu_sanitize_auth_security_settings( yneko_reimu_settings_group_input( $input, 'auth_security' ), $defaults['auth_security'] ) : $defaults['auth_security'],
		'security'          => yneko_reimu_sanitize_settings_bool_group(
			yneko_reimu_settings_group_input( $input, 'security' ),
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
		'i18n'              => yneko_reimu_sanitize_i18n_settings( yneko_reimu_settings_group_input( $input, 'i18n' ), $defaults['i18n'] ),
		'search'            => yneko_reimu_sanitize_search_settings( yneko_reimu_settings_group_input( $input, 'search' ) ),
		'features'          => yneko_reimu_sanitize_settings_bool_group(
			yneko_reimu_settings_group_input( $input, 'features' ),
			$defaults['features'],
			array( 'preloader_enable', 'top_enable', 'triangle_badge', 'firework_enable', 'pjax_enable', 'busuanzi_enable', 'katex_enable', 'photoswipe_enable', 'mermaid_enable', 'custom_cursor', 'show_admin_toolbar' )
		),
		'player'            => yneko_reimu_sanitize_player_settings( yneko_reimu_settings_group_input( $input, 'player' ) ),
		'third_party'       => yneko_reimu_sanitize_third_party_settings( yneko_reimu_settings_group_input( $input, 'third_party' ), $defaults['third_party'] ),
		'external_comments' => yneko_reimu_sanitize_external_comments_settings( yneko_reimu_settings_group_input( $input, 'external_comments' ) ),
		'music'             => yneko_reimu_sanitize_music_items( array_key_exists( 'music', $input ) ? $input['music'] : array() ),
	);
}
