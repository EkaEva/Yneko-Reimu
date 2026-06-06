<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_profile_avatar_upload() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id = get_current_user_id();
	$result  = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'message'      => $result->get_error_message(),
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			),
			400
		);
	}

	$pending = ! empty( $result['pending'] );
	wp_send_json_success(
		array_merge(
			array(
				'message'      => $pending ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '头像已更新', 'yneko-reimu' ),
				'avatarUploadUrl' => $result['url'],
				'avatarUploadPending' => $pending,
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
				'identity'     => yneko_reimu_comment_current_user_identity_html( $redirect ),
			),
			yneko_reimu_user_profile_payload( $user_id )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_avatar_upload', 'yneko_reimu_ajax_profile_avatar_upload' );
