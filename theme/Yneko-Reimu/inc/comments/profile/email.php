<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_profile_email_code() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user      = wp_get_current_user();
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$new_email = $new_email_input ? $new_email_input : $user->user_email;
	if ( '' === $new_email || ! is_email( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) ), 400 );
	}
	if ( strtolower( $new_email ) === strtolower( $user->user_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '新邮箱地址不要与原邮箱地址重复。', 'yneko-reimu' ) ), 400 );
	}
	if ( email_exists( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) ), 400 );
	}

	$auth_security_context = function_exists( 'yneko_reimu_auth_security_check' ) ? yneko_reimu_auth_security_check( 'profile_email', $new_email, 'ajax' ) : true;
	if ( is_wp_error( $auth_security_context ) ) {
		wp_send_json_error( array( 'message' => $auth_security_context->get_error_message() ), 429 );
	}
	if ( function_exists( 'yneko_reimu_auth_security_commit' ) ) {
		yneko_reimu_auth_security_commit( $auth_security_context );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 邮箱修改验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的邮箱修改验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。', 'yneko-reimu' ),
		$code,
		5
	);
	if ( ! wp_mail( $new_email, wp_specialchars_decode( $title ), $message ) ) {
		if ( function_exists( 'yneko_reimu_auth_security_record_mail_failure' ) ) {
			yneko_reimu_auth_security_record_mail_failure( 'profile_email', $new_email, 'ajax' );
		}
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'profile_email', (string) get_current_user_id(), $new_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
		),
		5 * MINUTE_IN_SECONDS
	);
	wp_send_json_success(
		array(
			'message'      => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ),
			'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_email_code', 'yneko_reimu_ajax_profile_email_code' );
