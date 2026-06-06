<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_profile_save() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id      = get_current_user_id();
	$user         = wp_get_current_user();
	$request      = yneko_reimu_profile_save_request( $user );
	$basic_check  = yneko_reimu_profile_save_validate_basics( $request );
	if ( is_wp_error( $basic_check ) ) {
		wp_send_json_error( array( 'message' => $basic_check->get_error_message() ), 400 );
	}
	$general_changed = yneko_reimu_profile_save_has_general_changes( $user_id, $user, $request );
	$prepared_tags = yneko_reimu_profile_save_prepare_tags( $user_id, $request );
	if ( is_wp_error( $prepared_tags ) ) {
		wp_send_json_error(
			array_merge(
				array( 'message' => $prepared_tags->get_error_message() ),
				(array) $prepared_tags->get_error_data()
			),
			400
		);
	}
	$comment_tags          = $prepared_tags['comment_tags'];
	$hidden_special_badges = $prepared_tags['hidden_special_badges'];

	$update = array(
		'ID'           => $user_id,
		'display_name' => $request['display_name'],
		'nickname'     => $request['display_name'],
		'user_url'     => $request['profile_url'],
	);

	$update = yneko_reimu_profile_save_apply_email( $update, $user_id, $user->user_email, $request['new_email'], $request['email_code'] );
	if ( is_wp_error( $update ) ) {
		wp_send_json_error( array( 'message' => $update->get_error_message() ), 400 );
	}

	$password_check = yneko_reimu_profile_save_validate_password( $request['new_password'], $request['new_password_confirm'] );
	if ( is_wp_error( $password_check ) ) {
		wp_send_json_error( array( 'message' => $password_check->get_error_message() ), 400 );
	}

	$avatar_state = yneko_reimu_profile_save_handle_avatar_file( $user_id, $request );
	if ( is_wp_error( $avatar_state ) ) {
		wp_send_json_error( array( 'message' => $avatar_state->get_error_message() ), 400 );
	}
	yneko_reimu_profile_save_apply_avatar( $user_id, $avatar_state );

	$totp_result = yneko_reimu_profile_save_apply_totp( $user_id, $request['totp_enabled'], $request['totp_code'] );
	if ( is_wp_error( $totp_result ) ) {
		wp_send_json_error( array( 'message' => $totp_result->get_error_message() ), 400 );
	}

	$result = wp_update_user( $update );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
	}
	update_user_meta( $user_id, '_yneko_reimu_profile_url_touched', '1' );
	if ( $request['avatar_frame_enabled'] ) {
		delete_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled' );
	} else {
		update_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', '0' );
	}
	$tags_pending = yneko_reimu_profile_save_apply_comment_tags( $user_id, $comment_tags );
	if ( $hidden_special_badges ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges', $hidden_special_badges );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges' );
	}
	update_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', '1' );
	if ( '' !== $request['new_password'] ) {
		wp_set_password( $request['new_password'], $user_id );
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true, is_ssl() );
	}

	wp_send_json_success(
		yneko_reimu_profile_save_payload( $user_id, $redirect, $avatar_state, $comment_tags, $tags_pending, $general_changed )
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_save', 'yneko_reimu_ajax_profile_save' );
