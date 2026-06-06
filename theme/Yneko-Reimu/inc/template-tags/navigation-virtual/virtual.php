<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_virtual_pages() {
	$pages = array(
		'about'    => array( 'title' => __( '关于', 'yneko-reimu' ), 'description' => __( '关于这个站点与作者。', 'yneko-reimu' ) ),
		'projects' => array( 'title' => __( '项目', 'yneko-reimu' ), 'description' => __( 'GitHub 项目与作品。', 'yneko-reimu' ) ),
		'archives' => array( 'title' => __( '归档', 'yneko-reimu' ), 'description' => __( '按时间整理全部文章。', 'yneko-reimu' ) ),
		'friend'   => array( 'title' => __( '友链', 'yneko-reimu' ), 'description' => __( '朋友们的站点入口。', 'yneko-reimu' ) ),
	);

	foreach ( array_keys( $pages ) as $slug ) {
		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) {
			unset( $pages[ $slug ] );
		}
	}

	return $pages;
}

function yneko_reimu_detect_virtual_page_slug() {
	$path = yneko_reimu_virtual_request_path();
	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return '';
	}

	$pages = yneko_reimu_virtual_pages();
	return isset( $pages[ $path ] ) ? $path : '';
}

function yneko_reimu_virtual_request_path() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( rawurldecode( $path ), '/' );

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	}

	return function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ? yneko_reimu_i18n_relative_without_prefix( $path ) : $path;
}

function yneko_reimu_maybe_set_virtual_page() {
	if ( ! is_404() ) {
		return;
	}

	global $wp_query;
	if ( $wp_query instanceof WP_Query && $wp_query->get( 'yneko_reimu_force_404' ) ) {
		return;
	}

	$slug          = yneko_reimu_detect_virtual_page_slug();
	$existing_page = $slug ? get_page_by_path( $slug, OBJECT, 'page' ) : null;
	if ( ! $slug || ( $existing_page && 'publish' === get_post_status( $existing_page ) ) ) {
		return;
	}

	$pages = yneko_reimu_virtual_pages();
	$GLOBALS['yneko_reimu_virtual_page'] = array_merge( array( 'slug' => $slug ), $pages[ $slug ] );

	global $wp_query;
	if ( $wp_query ) {
		$wp_query->is_404 = false;
	}

	status_header( 200 );
}
add_action( 'wp', 'yneko_reimu_maybe_set_virtual_page', 1 );

function yneko_reimu_virtual_page() {
	return isset( $GLOBALS['yneko_reimu_virtual_page'] ) && is_array( $GLOBALS['yneko_reimu_virtual_page'] )
		? $GLOBALS['yneko_reimu_virtual_page']
		: array();
}

function yneko_reimu_is_virtual_page( $slug = '' ) {
	$page = yneko_reimu_virtual_page();
	if ( ! $page ) {
		return false;
	}

	return '' === $slug || $slug === $page['slug'];
}

function yneko_reimu_virtual_page_slug() {
	$page = yneko_reimu_virtual_page();
	return $page ? $page['slug'] : '';
}

function yneko_reimu_virtual_template( $template ) {
	if ( ! yneko_reimu_is_virtual_page() ) {
		return $template;
	}

	$virtual_template = locate_template( 'virtual-page.php' );
	return $virtual_template ? $virtual_template : $template;
}
add_filter( 'template_include', 'yneko_reimu_virtual_template', 99 );
