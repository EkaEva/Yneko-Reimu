<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_i18n_relative_without_prefix( $path ) {
	$path   = trim( (string) $path, '/' );
	$prefix = yneko_reimu_i18n_url_prefix();

	if ( $path === $prefix ) {
		return '';
	}

	if ( 0 === strpos( $path, $prefix . '/' ) ) {
		return trim( substr( $path, strlen( $prefix ) ), '/' );
	}

	return $path;
}

function yneko_reimu_i18n_prefixed_url( $relative = '' ) {
	$prefix   = yneko_reimu_i18n_url_prefix();
	$relative = trim( (string) $relative, '/' );
	$path     = $prefix . ( $relative ? '/' . $relative : '' );
	return home_url( user_trailingslashit( $path ) );
}

function yneko_reimu_i18n_home_url( $language = '' ) {
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url() : home_url( '/' );
}

function yneko_reimu_i18n_localize_url( $url, $language = '' ) {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return $url;
	}

	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	$url      = (string) $url;

	if ( '' === $url || '#' === $url || preg_match( '#^(mailto|tel):#i', $url ) ) {
		return $url;
	}

	$home = wp_parse_url( home_url( '/' ) );
	$raw  = wp_parse_url( $url );
	if ( ! is_array( $raw ) ) {
		return $url;
	}

	if ( isset( $raw['host'], $home['host'] ) && strtolower( $raw['host'] ) !== strtolower( $home['host'] ) ) {
		return $url;
	}

	$path      = isset( $raw['path'] ) ? trim( rawurldecode( $raw['path'] ), '/' ) : '';
	$home_path = isset( $home['path'] ) ? trim( rawurldecode( $home['path'] ), '/' ) : '';
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	} elseif ( $home_path === $path ) {
		$path = '';
	}

	$path = yneko_reimu_i18n_relative_without_prefix( $path );
	$base = 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $path ) : home_url( user_trailingslashit( $path ) );

	if ( ! empty( $raw['query'] ) ) {
		$base .= '?' . $raw['query'];
	}
	if ( ! empty( $raw['fragment'] ) ) {
		$base .= '#' . $raw['fragment'];
	}

	return $base;
}

function yneko_reimu_i18n_virtual_path( $slug, $language = '' ) {
	$slug     = trim( (string) $slug, '/' );
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $slug ) : home_url( user_trailingslashit( $slug ) );
}

function yneko_reimu_i18n_switch_url( $language ) {
	$language = yneko_reimu_i18n_language_exists( $language ) ? $language : 'zh_CN';

	$special_slug = function_exists( 'yneko_reimu_special_page_slug' ) ? yneko_reimu_special_page_slug() : '';
	if ( function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page() ) {
		return yneko_reimu_i18n_virtual_path( yneko_reimu_virtual_page_slug(), $language );
	}
	if ( $special_slug ) {
		return yneko_reimu_i18n_virtual_path( $special_slug, $language );
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$target  = yneko_reimu_i18n_get_translation_id( $post_id, $language );
		return $target ? yneko_reimu_i18n_post_url( $target, $language ) : yneko_reimu_i18n_post_url( $post_id, $language );
	}

	$path = yneko_reimu_i18n_request_path();
	$path = yneko_reimu_i18n_relative_without_prefix( $path );

	if ( '' === $path ) {
		return yneko_reimu_i18n_home_url( $language );
	}

	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $path ) : home_url( user_trailingslashit( $path ) );
}

function yneko_reimu_i18n_options() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return array();
	}

	$current = yneko_reimu_i18n_current_language();
	$options = array();

	foreach ( yneko_reimu_i18n_languages() as $language ) {
		$options[] = array(
			'code'     => $language['code'],
			'label'    => $language['label'],
			'url'      => yneko_reimu_i18n_switch_url( $language['code'] ),
			'selected' => $language['code'] === $current,
		);
	}

	return $options;
}

function yneko_reimu_i18n_filter_term_link( $url ) {
	return yneko_reimu_i18n_enabled() && 'en_US' === yneko_reimu_i18n_current_language() ? yneko_reimu_i18n_localize_url( $url, 'en_US' ) : $url;
}
add_filter( 'term_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'author_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'year_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'month_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'day_link', 'yneko_reimu_i18n_filter_term_link', 20 );
