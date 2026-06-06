<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_user_profile_payload( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$user    = $user_id ? get_userdata( $user_id ) : null;
	if ( ! $user ) {
		return array();
	}

	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	$public_profile_url = $user->user_url ? $user->user_url : '';
	$profile_touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_profile_url_touched', true );
	$profile_url = $public_profile_url ? $public_profile_url : ( $profile_touched ? '' : yneko_reimu_comment_user_github_url( $user_id ) );
	return array(
		'userId'      => $user_id,
		'displayName' => $user->display_name ? $user->display_name : $user->user_login,
		'email'       => $user->user_email,
		'avatarUrl'   => yneko_reimu_user_profile_avatar_url( $user_id ),
		'pendingAvatarUrl' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true ),
		'avatarStatus' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'avatarPending' => 'pending' === (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'reviewStatuses' => yneko_reimu_user_review_status_payload( $user_id ),
		'profileUrl'  => $profile_url,
		'publicProfileUrl' => $public_profile_url,
		'twoFactor'   => yneko_reimu_user_2fa_enabled( $user_id ),
		'avatarUploadEnabled' => '1' === (string) ( $settings['avatar_enabled'] ?? '0' ),
		'avatarReviewEnabled' => '1' === (string) ( $settings['avatar_review'] ?? '0' ),
		'avatarMaxMb' => max( 1, absint( $settings['avatar_max_mb'] ?? 1 ) ),
		'avatarFrameEnabled' => '0' !== (string) get_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', true ),
		'avatarHtml' => yneko_reimu_comment_avatar_for_user_html( $user_id, 56 ),
		'commentBadgesEnabled' => yneko_reimu_comment_badges_enabled(),
		'commentTags' => yneko_reimu_comment_user_tags_payload( $user_id ),
		'pendingCommentTags' => yneko_reimu_comment_user_pending_tags( $user_id ),
		'commentBadgesHtml' => yneko_reimu_comment_user_badges_html( $user_id ),
		'commentTagReviewEnabled' => yneko_reimu_comment_tag_review_enabled(),
	);
}

function yneko_reimu_ajax_profile_get() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请先登录。', 'yneko-reimu' ),
			),
			401
		);
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_user_profile_payload(),
			array(
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_get', 'yneko_reimu_ajax_profile_get' );
