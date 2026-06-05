<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_favicon_append_version( $url ) {
	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . 'yneko-reimu-fallback=1';
}

function yneko_reimu_favicon_mime_type( $url, $attachment_id = 0 ) {
	$mime = $attachment_id ? get_post_mime_type( $attachment_id ) : '';
	if ( $mime ) {
		return $mime;
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	if ( preg_match( '/\.svg$/i', $path ) ) {
		return 'image/svg+xml';
	}
	if ( preg_match( '/\.jpe?g$/i', $path ) ) {
		return 'image/jpeg';
	}
	if ( preg_match( '/\.png$/i', $path ) ) {
		return 'image/png';
	}

	return '';
}

function yneko_reimu_favicon_site_icon() {
	if ( ! has_site_icon() ) {
		return array();
	}

	$site_icon_id = absint( get_option( 'site_icon', 0 ) );
	$url          = $site_icon_id ? wp_get_attachment_url( $site_icon_id ) : get_site_icon_url( 192 );
	if ( ! $url ) {
		return array();
	}

	return array(
		'url'  => $url,
		'type' => yneko_reimu_favicon_mime_type( $url, $site_icon_id ),
	);
}

function yneko_reimu_favicon_fallback() {
	$settings = function_exists( 'yneko_reimu_settings' ) ? yneko_reimu_settings() : array();
	$url      = yneko_reimu_normalize_png_jpeg_url( $settings['favicon_fallback_url'] ?? '' );

	if ( ! $url ) {
		return array();
	}

	return array(
		'url'     => $url,
		'version' => yneko_reimu_favicon_append_version( $url ),
		'type'    => yneko_reimu_favicon_mime_type( $url ),
	);
}

function yneko_reimu_favicon_default() {
	$site_logo = function_exists( 'yneko_reimu_get_site_logo_url' ) ? yneko_reimu_get_site_logo_url() : '';
	$url       = $site_logo ? $site_logo : YNEKO_REIMU_URI . '/assets/images/avatar.svg';

	return array(
		'url'  => $url,
		'type' => yneko_reimu_favicon_mime_type( $url ),
	);
}

function yneko_reimu_favicon_root_url( $path ) {
	return home_url( '/' . ltrim( $path, '/' ) );
}

function yneko_reimu_favicon_can_generate_root_icons( $fallback ) {
	if ( ! $fallback ) {
		return false;
	}

	if ( 'image/png' === ( $fallback['type'] ?? '' ) ) {
		return true;
	}

	return function_exists( 'imagecreatefromstring' ) && function_exists( 'imagepng' );
}

function yneko_reimu_favicon_link( $rel, $url, $type = '', $sizes = '' ) {
	$type_attr  = $type ? ' type="' . esc_attr( $type ) . '"' : '';
	$sizes_attr = $sizes ? ' sizes="' . esc_attr( $sizes ) . '"' : '';

	echo '<link rel="' . esc_attr( $rel ) . '"' . $type_attr . ' href="' . esc_url( $url ) . '"' . $sizes_attr . '>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function yneko_reimu_favicon() {
	$site_icon = yneko_reimu_favicon_site_icon();
	$fallback  = yneko_reimu_favicon_fallback();
	$root_urls = yneko_reimu_favicon_can_generate_root_icons( $fallback );

	if ( $site_icon && $fallback ) {
		remove_action( 'wp_head', 'wp_site_icon', 99 );
		if ( 'image/svg+xml' === $site_icon['type'] ) {
			yneko_reimu_favicon_link( 'icon', $site_icon['url'], $site_icon['type'], 'any' );
		} else {
			yneko_reimu_favicon_link( 'icon', $site_icon['url'], $site_icon['type'], '32x32' );
			yneko_reimu_favicon_link( 'icon', $site_icon['url'], $site_icon['type'], '192x192' );
		}
	} elseif ( ! $site_icon ) {
		$default = yneko_reimu_favicon_default();
		yneko_reimu_favicon_link( 'icon', $default['url'], $default['type'], 'image/svg+xml' === $default['type'] ? 'any' : '' );
	}

	if ( $fallback ) {
		yneko_reimu_favicon_link( 'icon', $fallback['version'], $fallback['type'], '32x32' );
		yneko_reimu_favicon_link( 'icon', $fallback['version'], $fallback['type'], '192x192' );
		if ( $root_urls ) {
			yneko_reimu_favicon_link( 'shortcut icon', yneko_reimu_favicon_root_url( 'favicon.ico' ), 'image/x-icon' );
			yneko_reimu_favicon_link( 'icon', yneko_reimu_favicon_root_url( 'favicon-32x32.png' ), 'image/png', '32x32' );
			yneko_reimu_favicon_link( 'icon', yneko_reimu_favicon_root_url( 'favicon-192x192.png' ), 'image/png', '192x192' );
			yneko_reimu_favicon_link( 'apple-touch-icon', yneko_reimu_favicon_root_url( 'apple-touch-icon.png' ), 'image/png', '180x180' );
		}
	}
}
add_action( 'wp_head', 'yneko_reimu_favicon', 5 );

function yneko_reimu_favicon_request_map() {
	return array(
		'/favicon.ico'            => array(
			'type'  => 'ico',
			'mime'  => 'image/x-icon',
			'sizes' => array( 16, 32, 48, 256 ),
		),
		'/favicon-32x32.png'      => array(
			'type' => 'png',
			'mime' => 'image/png',
			'size' => 32,
		),
		'/favicon-192x192.png'    => array(
			'type' => 'png',
			'mime' => 'image/png',
			'size' => 192,
		),
		'/apple-touch-icon.png'   => array(
			'type' => 'png',
			'mime' => 'image/png',
			'size' => 180,
		),
		'/apple-touch-icon-precomposed.png' => array(
			'type' => 'png',
			'mime' => 'image/png',
			'size' => 180,
		),
	);
}

function yneko_reimu_favicon_current_root_request() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path        = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$home_path   = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home_path   = '/' === $home_path ? '' : untrailingslashit( $home_path );

	if ( $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = substr( $path, strlen( $home_path ) );
	}

	return '/' . ltrim( $path, '/' );
}

function yneko_reimu_favicon_remote_body( $url ) {
	$response = wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 8,
			'redirection' => 3,
		)
	);

	if ( is_wp_error( $response ) ) {
		return '';
	}

	if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	return (string) wp_remote_retrieve_body( $response );
}

function yneko_reimu_favicon_icon_body( $url, $icon ) {
	$source = yneko_reimu_favicon_remote_body( $url );
	if ( '' === $source ) {
		return '';
	}

	if ( 'ico' === $icon['type'] ) {
		return yneko_reimu_favicon_build_ico( $source, $icon['sizes'] ?? array( 16, 32, 48, 256 ) );
	}

	if ( 'png' === $icon['type'] && ! empty( $icon['size'] ) ) {
		return yneko_reimu_favicon_png_from_body( $source, absint( $icon['size'] ) );
	}

	return $source;
}

function yneko_reimu_favicon_png_from_body( $source, $size = 0 ) {
	if ( ! function_exists( 'imagecreatefromstring' ) || ! function_exists( 'imagepng' ) ) {
		return $source;
	}

	$image = imagecreatefromstring( $source );
	if ( ! $image ) {
		return $source;
	}

	$width  = imagesx( $image );
	$height = imagesy( $image );
	$size   = absint( $size );
	if ( $size || $width > 256 || $height > 256 ) {
		$target_width  = $size ? $size : max( 1, (int) round( $width * min( 256 / $width, 256 / $height ) ) );
		$target_height = $size ? $size : max( 1, (int) round( $height * min( 256 / $width, 256 / $height ) ) );
		$target        = imagecreatetruecolor( $target_width, $target_height );
		imagealphablending( $target, false );
		imagesavealpha( $target, true );
		imagecopyresampled( $target, $image, 0, 0, 0, 0, $target_width, $target_height, $width, $height );
		imagedestroy( $image );
		$image = $target;
	}

	ob_start();
	imagepng( $image );
	imagedestroy( $image );
	return (string) ob_get_clean();
}

function yneko_reimu_favicon_build_ico( $source, $sizes ) {
	$sizes   = array_values( array_unique( array_filter( array_map( 'absint', (array) $sizes ) ) ) );
	$sizes   = $sizes ? $sizes : array( 16, 32, 48, 256 );
	$entries = array();
	$images  = array();
	$offset  = 6 + ( count( $sizes ) * 16 );

	foreach ( $sizes as $size ) {
		$size = min( 256, max( 1, $size ) );
		$png  = yneko_reimu_favicon_png_from_body( $source, $size );

		$entries[] = pack( 'CCCCvvVV', 256 === $size ? 0 : $size, 256 === $size ? 0 : $size, 0, 0, 1, 32, strlen( $png ), $offset );
		$images[]  = $png;
		$offset   += strlen( $png );
	}

	return pack( 'vvv', 0, 1, count( $images ) ) . implode( '', $entries ) . implode( '', $images );
}

function yneko_reimu_favicon_serve_root_icon() {
	$request = yneko_reimu_favicon_current_root_request();
	$map     = yneko_reimu_favicon_request_map();

	if ( ! isset( $map[ $request ] ) ) {
		return;
	}

	$fallback = yneko_reimu_favicon_fallback();
	if ( ! yneko_reimu_favicon_can_generate_root_icons( $fallback ) ) {
		return;
	}

	$icon = $map[ $request ];
	$body = yneko_reimu_favicon_icon_body( $fallback['url'], $icon );
	if ( '' === $body ) {
		return;
	}

	status_header( 200 );
	header( 'Content-Type: ' . $icon['mime'] );
	header( 'Cache-Control: public, max-age=' . WEEK_IN_SECONDS );
	header( 'Content-Length: ' . strlen( $body ) );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'template_redirect', 'yneko_reimu_favicon_serve_root_icon', 0 );
add_action( 'do_favicon', 'yneko_reimu_favicon_serve_root_icon', 0 );
