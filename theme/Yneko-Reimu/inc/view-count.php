<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_get_view_count( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );

	if ( ! $post_id ) {
		return 0;
	}

	return absint( yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_view_count', true ) );
}

function yneko_reimu_view_count_text( $post_id = 0 ) {
	return sprintf(
		/* translators: %s: view count. */
		esc_html__( '%s 阅读量', 'yneko-reimu' ),
		esc_html( number_format_i18n( yneko_reimu_get_view_count( $post_id ) ) )
	);
}

function yneko_reimu_comment_count_text( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	return sprintf(
		/* translators: %s: comment count. */
		esc_html__( '%s 留言', 'yneko-reimu' ),
		esc_html( number_format_i18n( $post_id ? get_comments_number( $post_id ) : 0 ) )
	);
}

function yneko_reimu_should_count_request() {
	if ( is_admin() || is_preview() || is_feed() || is_robots() || wp_doing_ajax() || wp_is_json_request() || is_404() ) {
		return false;
	}

	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return true;
}

function yneko_reimu_should_count_view( $post_id ) {
	if ( ! $post_id || is_admin() || is_preview() || is_feed() || is_robots() || wp_doing_ajax() || wp_is_json_request() ) {
		return false;
	}

	if ( ! is_singular( array( 'post', 'page' ) ) ) {
		return false;
	}

	$post_status = get_post_status( $post_id );
	if ( 'publish' !== $post_status ) {
		return false;
	}

	if ( is_user_logged_in() && current_user_can( 'edit_post', $post_id ) ) {
		return false;
	}

	return true;
}

function yneko_reimu_increment_counter_option( $key ) {
	$count = absint( yneko_reimu_get_option( $key, 0 ) ) + 1;
	update_option( $key, $count, false );

	return $count;
}

function yneko_reimu_count_site_visit() {
	if ( ! yneko_reimu_should_count_request() ) {
		return;
	}

	yneko_reimu_increment_counter_option( 'yneko_reimu_site_pv' );

	$cookie_name = 'yneko_reimu_visitor_id';
	if ( ! empty( $_COOKIE[ $cookie_name ] ) ) {
		return;
	}

	$visitor_id = wp_generate_uuid4();
	$expires    = time() + YEAR_IN_SECONDS;
	$cookie     = array(
		'expires'  => $expires,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'secure'   => is_ssl(),
		'httponly' => true,
		'samesite' => 'Lax',
	);

	if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) {
		$cookie['domain'] = COOKIE_DOMAIN;
	}

	setcookie( $cookie_name, $visitor_id, $cookie );
	$_COOKIE[ $cookie_name ] = $visitor_id;

	yneko_reimu_increment_counter_option( 'yneko_reimu_site_uv' );
}
add_action( 'template_redirect', 'yneko_reimu_count_site_visit', 18 );

function yneko_reimu_count_post_view() {
	$post_id = absint( get_queried_object_id() );

	if ( ! yneko_reimu_should_count_view( $post_id ) ) {
		return;
	}

	$count = yneko_reimu_get_view_count( $post_id );
	update_post_meta( $post_id, '_yneko_reimu_view_count', $count + 1 );
}
add_action( 'template_redirect', 'yneko_reimu_count_post_view', 20 );

function yneko_reimu_get_site_pv() {
	return absint( yneko_reimu_get_option( 'yneko_reimu_site_pv', 0 ) );
}

function yneko_reimu_get_site_uv() {
	return absint( yneko_reimu_get_option( 'yneko_reimu_site_uv', 0 ) );
}
