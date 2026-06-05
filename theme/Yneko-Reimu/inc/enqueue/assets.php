<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_vendor_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['vendor_cdn_base'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_vendor_cdn_base', 'https://cdn.jsdelivr.net/npm' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) : 'https://cdn.jsdelivr.net/npm';
}

function yneko_reimu_vendor_url( $package_path ) {
	return yneko_reimu_vendor_base_url() . '/' . ltrim( $package_path, '/' );
}

function yneko_reimu_asset_version( $relative_path ) {
	$path = YNEKO_REIMU_DIR . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $path ) ) {
		return YNEKO_REIMU_VERSION . '.' . filemtime( $path );
	}

	return YNEKO_REIMU_VERSION;
}

function yneko_reimu_default_aplayer_audio_json() {
	return '[]';
}

function yneko_reimu_json_theme_mod( $key, $default = '' ) {
	$value = trim( (string) yneko_reimu_get_theme_mod( $key, $default ) );

	if ( '' === $value ) {
		return null;
	}

	$decoded = json_decode( $value, true );
	return is_array( $decoded ) ? $decoded : null;
}

function yneko_reimu_normalize_aplayer_audio( $audio ) {
	if ( ! is_array( $audio ) ) {
		return $audio;
	}

	return $audio;
}

function yneko_reimu_live2d_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['live2d_base_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_live2d_base_url', 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) . '/' : 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest/';
}

function yneko_reimu_live2d_api_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['live2d_api_base_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_live2d_api_base_url', 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) . '/' : 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/';
}
