<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

function yneko_reimu_settings_theme_mod_bool( $key, $default = false ) {
	return yneko_reimu_get_theme_mod( $key, $default ) ? '1' : '0';
}

function yneko_reimu_settings_theme_mod_text( $key, $default = '' ) {
	return (string) yneko_reimu_get_theme_mod( $key, $default );
}
