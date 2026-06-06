<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_current_user_identity( $redirect_post_id = 0 ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$redirect = $redirect_post_id ? get_permalink( $redirect_post_id ) : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	return yneko_reimu_comment_current_user_identity_html( $redirect );
}

function yneko_reimu_comment_current_user_identity_html( $redirect = '', $extra_statuses = array() ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$user         = wp_get_current_user();
	$user_id      = absint( $user->ID );
	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	$profile_url  = yneko_reimu_comment_user_profile_url( $user_id );
	$redirect     = $redirect ? wp_validate_redirect( $redirect, home_url( '/' ) ) : home_url( '/' );
	$logout_url   = wp_logout_url( $redirect );
	$avatar       = yneko_reimu_comment_avatar_for_user_html( $user_id, 56 );
	$name_html    = esc_html( $display_name );
	$status_html  = yneko_reimu_user_review_primary_status_html( $user_id, $extra_statuses );

	if ( $profile_url ) {
		$name_html = '<a class="reimu-comment-current-user__name" href="' . esc_url( $profile_url ) . '" target="_blank" rel="noopener noreferrer nofollow">' . esc_html( $display_name ) . '</a>';
	} else {
		$name_html = '<span class="reimu-comment-current-user__name">' . esc_html( $display_name ) . '</span>';
	}

	return '<div class="reimu-comment-current-user">' .
		'<div class="reimu-comment-current-user__avatar-wrap">' .
			'<button type="button" class="reimu-comment-current-user__avatar" data-reimu-profile-open aria-label="' . esc_attr__( '编辑个人资料', 'yneko-reimu' ) . '">' . $avatar . '</button>' .
			'<a class="reimu-comment-current-user__logout" href="' . esc_url( $logout_url ) . '" data-reimu-ajax-logout data-no-pjax aria-label="' . esc_attr__( '退出登录', 'yneko-reimu' ) . '"></a>' .
		'</div>' .
		$name_html .
		$status_html .
		'<a class="reimu-comment-current-user__logout-text" href="' . esc_url( $logout_url ) . '" data-reimu-ajax-logout data-no-pjax aria-label="' . esc_attr__( '退出登录', 'yneko-reimu' ) . '">' . esc_html__( '退出', 'yneko-reimu' ) . '</a>' .
	'</div>';
}

function yneko_reimu_comment_author_link_html( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	$author = get_comment_author( $comment );
	$url    = '';

	if ( ! empty( $comment->user_id ) ) {
		$url = yneko_reimu_comment_user_profile_url( $comment->user_id );
	}

	if ( ! $url ) {
		$url = get_comment_author_url( $comment );
	}

	if ( $url ) {
		return '<a class="reimu-comment__author-link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer nofollow">' . esc_html( $author ) . '</a>';
	}

	return '<span class="reimu-comment__author-name">' . esc_html( $author ) . '</span>';
}

function yneko_reimu_get_comment_avatar( $comment, $size = 56 ) {
	$default = function_exists( 'yneko_reimu_get_default_comment_avatar_url' ) ? yneko_reimu_get_default_comment_avatar_url() : '';

	if ( ! $default ) {
		return get_avatar( $comment, $size );
	}

	if ( empty( $comment->user_id ) ) {
		$author = get_comment_author( $comment );
		return sprintf(
			'<img alt="%1$s" src="%2$s" class="avatar avatar-%3$d photo reimu-comment-default-avatar" height="%3$d" width="%3$d" loading="lazy" decoding="async">',
			esc_attr( $author ),
			esc_url( $default ),
			absint( $size )
		);
	}

	return yneko_reimu_comment_avatar_with_frame( get_avatar( $comment, $size, $default ), absint( $comment->user_id ), 'reimu-avatar-frame--comment' );
}
