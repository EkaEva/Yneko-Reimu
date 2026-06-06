<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_like_cookie_name() {
	return 'yneko_reimu_comment_like_id';
}

function yneko_reimu_comment_like_guest_token() {
	$cookie_name = yneko_reimu_comment_like_cookie_name();
	$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	if ( preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return strtolower( $token );
	}

	$token   = str_replace( '-', '', wp_generate_uuid4() ) . str_replace( '-', '', wp_generate_uuid4() );
	$expires = time() + YEAR_IN_SECONDS;
	$cookie  = array(
		'expires'  => $expires,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'secure'   => is_ssl(),
		'httponly' => true,
		'samesite' => 'Lax',
	);
	if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) {
		$cookie['domain'] = COOKIE_DOMAIN;
	}

	setcookie( $cookie_name, $token, $cookie );
	$_COOKIE[ $cookie_name ] = $token;

	return $token;
}

function yneko_reimu_comment_like_actor_key( $create_guest = true ) {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		return 'user:' . absint( $user_id );
	}

	$token = $create_guest ? yneko_reimu_comment_like_guest_token() : '';
	if ( ! $token ) {
		$cookie_name = yneko_reimu_comment_like_cookie_name();
		$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	}
	if ( ! preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return '';
	}

	return 'guest:' . hash_hmac( 'sha256', $token, wp_salt( 'nonce' ) );
}

function yneko_reimu_comment_like_registry( $comment_id ) {
	$registry = yneko_reimu_get_comment_meta( absint( $comment_id ), '_yneko_reimu_like_registry', true );
	return is_array( $registry ) ? $registry : array();
}

function yneko_reimu_comment_like_baseline( $comment_id ) {
	$comment_id = absint( $comment_id );
	$baseline   = yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_baseline', true );
	if ( '' !== $baseline && null !== $baseline ) {
		return absint( $baseline );
	}

	$current        = absint( yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_count', true ) );
	$stored_registry = yneko_reimu_comment_like_registry( $comment_id );
	$baseline       = max( 0, $current - count( array_filter( $stored_registry ) ) );
	update_comment_meta( $comment_id, '_yneko_reimu_like_baseline', $baseline );

	return absint( $baseline );
}

function yneko_reimu_comment_like_count_from_registry( $comment_id, $registry = null ) {
	$registry = is_array( $registry ) ? $registry : yneko_reimu_comment_like_registry( $comment_id );
	return yneko_reimu_comment_like_baseline( $comment_id ) + count( array_filter( $registry ) );
}

function yneko_reimu_comment_user_liked( $comment_id ) {
	$comment_id = absint( $comment_id );
	if ( ! $comment_id ) {
		return false;
	}

	$actor_key = yneko_reimu_comment_like_actor_key( false );
	if ( ! $actor_key ) {
		return false;
	}

	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	return ! empty( $registry[ $actor_key ] );
}

function yneko_reimu_ajax_comment_like() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;

	if ( ! $comment ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	check_ajax_referer( 'yneko_reimu_comment_like_' . $comment_id, 'nonce' );

	if ( ! yneko_reimu_comment_is_publicly_visible( $comment ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	$actor_key = yneko_reimu_comment_like_actor_key();
	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	$next_liked = empty( $registry[ $actor_key ] );
	if ( $next_liked ) {
		$registry[ $actor_key ] = time();
	} else {
		unset( $registry[ $actor_key ] );
	}

	$count = yneko_reimu_comment_like_count_from_registry( $comment_id, $registry );
	if ( empty( $registry ) ) {
		delete_comment_meta( $comment_id, '_yneko_reimu_like_registry' );
	} else {
		update_comment_meta( $comment_id, '_yneko_reimu_like_registry', $registry );
	}
	update_comment_meta( $comment_id, '_yneko_reimu_like_count', $count );

	wp_send_json_success(
		array(
			'count' => $count,
			'liked' => $next_liked,
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );
add_action( 'wp_ajax_nopriv_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );
