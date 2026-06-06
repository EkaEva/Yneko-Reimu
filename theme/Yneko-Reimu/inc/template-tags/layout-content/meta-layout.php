<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_theme_mod_bool( $name, $default = true ) {
	if ( function_exists( 'yneko_reimu_settings_feature_enabled' ) ) {
		return yneko_reimu_settings_feature_enabled( $name, yneko_reimu_feature_default( $name, $default ) );
	}

	return (bool) yneko_reimu_get_theme_mod( $name, yneko_reimu_feature_default( $name, $default ) );
}

function yneko_reimu_meta_choice( $post_id, $key, $default = 'inherit' ) {
	$value = yneko_reimu_get_post_meta( $post_id, $key, true );
	return $value ? $value : $default;
}

function yneko_reimu_is_meta_enabled( $post_id, $meta_key, $theme_mod_key, $default = true ) {
	$choice = yneko_reimu_meta_choice( $post_id, $meta_key );

	if ( 'show' === $choice ) {
		return true;
	}

	if ( 'hide' === $choice ) {
		return false;
	}

	return yneko_reimu_theme_mod_bool( $theme_mod_key, $default );
}

function yneko_reimu_sidebar_position( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : ( is_singular() ? absint( get_queried_object_id() ) : 0 );
	$choice  = $post_id ? yneko_reimu_meta_choice( $post_id, '_yneko_reimu_sidebar', 'inherit' ) : 'inherit';

	if ( in_array( $choice, array( 'left', 'right', 'disabled' ), true ) ) {
		return $choice;
	}

	$position = yneko_reimu_get_theme_mod( 'yneko_reimu_sidebar_position', 'left' );
	return in_array( $position, array( 'left', 'right', 'disabled' ), true ) ? $position : 'left';
}

function yneko_reimu_should_show_sidebar( $post_id = 0 ) {
	return 'disabled' !== yneko_reimu_sidebar_position( $post_id );
}

function yneko_reimu_should_show_toc( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return is_singular( 'post' ) && yneko_reimu_is_meta_enabled( $post_id, '_yneko_reimu_toc', 'yneko_reimu_show_toc', true );
}

function yneko_reimu_special_page_slug( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : ( is_singular( 'page' ) ? absint( get_queried_object_id() ) : 0 );

	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return '';
	}

	$slug = get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, array( 'about', 'projects', 'archives', 'friend' ), true ) ) {
		return '';
	}

	return function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ? '' : $slug;
}

function yneko_reimu_force_disabled_builtin_page_404() {
	if ( is_admin() || ! is_singular( 'page' ) ) {
		return;
	}

	$post_id = absint( get_queried_object_id() );
	if ( ! $post_id ) {
		return;
	}

	$slug = get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, array( 'about', 'projects', 'archives', 'friend' ), true ) || ! function_exists( 'yneko_reimu_builtin_page_enabled' ) || yneko_reimu_builtin_page_enabled( $slug ) ) {
		return;
	}

	global $wp_query;
	if ( $wp_query instanceof WP_Query && method_exists( $wp_query, 'set_404' ) ) {
		$wp_query->set_404();
	}
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'yneko_reimu_force_disabled_builtin_page_404', 0 );
