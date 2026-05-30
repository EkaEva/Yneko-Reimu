<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_asset_uri( $path ) {
	return YNEKO_REIMU_URI . '/' . ltrim( $path, '/' );
}

function yneko_reimu_get_default_banner_url() {
	$custom = yneko_reimu_get_theme_mod( 'yneko_reimu_default_banner', '' );
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	if ( file_exists( YNEKO_REIMU_DIR . '/assets/images/banner.png' ) ) {
		return yneko_reimu_asset_uri( 'assets/images/banner.png' );
	}

	return '';
}

function yneko_reimu_get_banner_srcset( $banner = '' ) {
	$src = $banner ? esc_url_raw( $banner ) : yneko_reimu_get_default_banner_url();

	return array(
		array(
			'media' => '(max-width: 479px)',
			'src'   => $src,
		),
		array(
			'media' => '(max-width: 799px)',
			'src'   => $src,
		),
		array(
			'media' => '(min-width: 800px)',
			'src'   => $src,
		),
	);
}

function yneko_reimu_get_default_cover_url() {
	$custom = yneko_reimu_get_theme_mod( 'yneko_reimu_default_cover', '' );
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	return yneko_reimu_get_default_banner_url();
}

function yneko_reimu_get_default_avatar_url() {
	$settings_avatar = yneko_reimu_setting( 'author_avatar_url', '' );
	if ( $settings_avatar ) {
		return esc_url_raw( $settings_avatar );
	}

	$custom = yneko_reimu_get_theme_mod( 'yneko_reimu_default_avatar', '' );
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	$site_logo = yneko_reimu_get_site_logo_url();
	if ( $site_logo ) {
		return $site_logo;
	}

	$site_logo = yneko_reimu_get_site_logo_url();
	return $site_logo ? $site_logo : yneko_reimu_asset_uri( 'assets/images/avatar.svg' );
}

function yneko_reimu_get_default_comment_avatar_url() {
	$settings_avatar = function_exists( 'yneko_reimu_settings_comment_avatar_url' ) ? yneko_reimu_settings_comment_avatar_url() : '';
	if ( $settings_avatar ) {
		return esc_url_raw( $settings_avatar );
	}

	$plugin_avatar_id = absint( get_option( 'avatar_default_wp_user_avatar', 0 ) );
	if ( $plugin_avatar_id ) {
		$plugin_avatar = wp_get_attachment_image_url( $plugin_avatar_id, 'thumbnail' );
		if ( $plugin_avatar ) {
			return esc_url_raw( $plugin_avatar );
		}
	}

	return yneko_reimu_get_default_avatar_url();
}

function yneko_reimu_get_site_logo_url() {
	$settings_logo = yneko_reimu_setting( 'site_avatar_url', '' );
	if ( $settings_logo ) {
		return esc_url_raw( $settings_logo );
	}

	$custom_logo_id = absint( yneko_reimu_get_theme_mod( 'custom_logo', 0 ) );
	if ( $custom_logo_id ) {
		$custom_logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $custom_logo ) {
			return esc_url_raw( $custom_logo );
		}
	}

	$site_icon_id = absint( get_option( 'site_icon', 0 ) );
	if ( $site_icon_id ) {
		$site_icon = wp_get_attachment_image_url( $site_icon_id, 'full' );
		if ( $site_icon ) {
			return esc_url_raw( $site_icon );
		}
	}

	if ( file_exists( YNEKO_REIMU_DIR . '/assets/images/avatar.svg' ) ) {
		return yneko_reimu_asset_uri( 'assets/images/avatar.svg' );
	}

	if ( file_exists( YNEKO_REIMU_DIR . '/assets/images/logo.svg' ) ) {
		return yneko_reimu_asset_uri( 'assets/images/logo.svg' );
	}

	return '';
}

function yneko_reimu_get_search_bg_url() {
	$custom = yneko_reimu_get_theme_mod( 'yneko_reimu_search_background', '' );
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	if ( file_exists( YNEKO_REIMU_DIR . '/assets/images/search-bg.png' ) ) {
		return yneko_reimu_asset_uri( 'assets/images/search-bg.png' );
	}

	return yneko_reimu_get_default_avatar_url();
}

function yneko_reimu_get_sponsor_qr_url() {
	$custom = yneko_reimu_settings_sponsor_qr_url();
	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	return '';
}

function yneko_reimu_get_reimu_image_url() {
	$site_logo = yneko_reimu_get_site_logo_url();
	return $site_logo ? $site_logo : yneko_reimu_asset_uri( 'assets/images/avatar.svg' );
}

function yneko_reimu_get_cover_pool() {
	$cover_dir = YNEKO_REIMU_DIR . '/assets/images/covers';
	$cover_uri = YNEKO_REIMU_URI . '/assets/images/covers';

	if ( ! is_dir( $cover_dir ) ) {
		return array();
	}

	$files = array();
	foreach ( array( 'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif' ) as $extension ) {
		$matches = glob( $cover_dir . '/*.' . $extension );
		if ( $matches ) {
			$files = array_merge( $files, $matches );
		}
	}

	if ( ! $files ) {
		return array();
	}

	natcasesort( $files );

	return array_values(
		array_map(
			static function ( $file ) use ( $cover_dir, $cover_uri ) {
				$relative = str_replace( '\\', '/', substr( $file, strlen( $cover_dir ) + 1 ) );
				return $cover_uri . '/' . $relative;
			},
			$files
		)
	);
}

function yneko_reimu_get_post_banner_url( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$custom  = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_banner_url', true );

	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	if ( has_post_thumbnail( $post_id ) ) {
		$image = get_the_post_thumbnail_url( $post_id, 'reimu-hero' );
		if ( $image ) {
			return esc_url_raw( $image );
		}
	}

	return yneko_reimu_get_default_banner_url();
}

function yneko_reimu_get_post_cover_url( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$custom  = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_cover_url', true );

	if ( $custom ) {
		return esc_url_raw( $custom );
	}

	if ( has_post_thumbnail( $post_id ) ) {
		$image = get_the_post_thumbnail_url( $post_id, 'reimu-card' );
		if ( $image ) {
			return esc_url_raw( $image );
		}
	}

	return yneko_reimu_get_default_cover_url();
}

function yneko_reimu_background_style( $url ) {
	if ( ! $url ) {
		return '';
	}

	return 'background-image: linear-gradient(90deg, rgba(255,255,255,.9), rgba(255,255,255,.18)), url(' . esc_url( $url ) . ');';
}
