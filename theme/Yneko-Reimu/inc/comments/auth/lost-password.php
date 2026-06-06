<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_send_lostpassword_code() {
	check_ajax_referer( 'yneko_reimu_ajax_lostpassword_code', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	$identifier = isset( $_POST['user_login'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '';
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入注册邮箱。', 'yneko-reimu' ) ), 400 );
	}

	$success_message = esc_html__( '如果该邮箱已注册，验证码将发送到对应邮箱。', 'yneko-reimu' );
	$auth_security_context = function_exists( 'yneko_reimu_auth_security_check' ) ? yneko_reimu_auth_security_check( 'lostpassword', $identifier, 'ajax' ) : true;
	if ( is_wp_error( $auth_security_context ) ) {
		wp_send_json_success( array( 'message' => $success_message ) );
	}
	if ( function_exists( 'yneko_reimu_auth_security_commit' ) ) {
		yneko_reimu_auth_security_commit( $auth_security_context );
	}

	$user = yneko_reimu_find_user_for_password_reset( $identifier );
	if ( ! $user ) {
		wp_send_json_success( array( 'message' => $success_message ) );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 密码重置验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的密码重置验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请立即检查账号安全。', 'yneko-reimu' ),
		$code,
		5
	);

	if ( ! wp_mail( $user->user_email, wp_specialchars_decode( $title ), $message ) ) {
		if ( function_exists( 'yneko_reimu_auth_security_record_mail_failure' ) ) {
			yneko_reimu_auth_security_record_mail_failure( 'lostpassword', $identifier, 'ajax' );
		}
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'lost', $identifier, $user->user_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
			'user_id'   => absint( $user->ID ),
		),
		5 * MINUTE_IN_SECONDS
	);

	wp_send_json_success( array( 'message' => $success_message ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_lostpassword_code', 'yneko_reimu_ajax_send_lostpassword_code' );

function yneko_reimu_ajax_lostpassword() {
	check_ajax_referer( 'yneko_reimu_ajax_lostpassword', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	$identifier    = isset( $_POST['user_login'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '';
	$user_password = isset( $_POST['user_password'] ) ? (string) wp_unslash( $_POST['user_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be reset raw.
	$verify_code   = isset( $_POST['verify_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['verify_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入注册邮箱。', 'yneko-reimu' ) ), 400 );
	}
	if ( strlen( $user_password ) < 8 ) {
		wp_send_json_error( array( 'message' => esc_html__( '密码至少需要 8 个字符。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! preg_match( '/^\d{6}$/', $verify_code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 6 位邮箱验证码。', 'yneko-reimu' ) ), 400 );
	}

	$user = yneko_reimu_find_user_for_password_reset( $identifier );
	if ( ! $user ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
	}

	$code_key  = yneko_reimu_auth_code_transient_key( 'lost', $identifier, $user->user_email );
	$code_data = get_transient( $code_key );
	if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) || absint( $code_data['user_id'] ?? 0 ) !== absint( $user->ID ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
	}

	$attempts = isset( $code_data['attempts'] ) ? absint( $code_data['attempts'] ) : 0;
	if ( $attempts >= 5 ) {
		delete_transient( $code_key );
		wp_send_json_error( array( 'message' => esc_html__( '验证码错误次数过多，请重新获取。', 'yneko-reimu' ) ), 429 );
	}

	if ( ! wp_check_password( $verify_code, $code_data['code_hash'] ) ) {
		$code_data['attempts'] = $attempts + 1;
		set_transient( $code_key, $code_data, 5 * MINUTE_IN_SECONDS );
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确。', 'yneko-reimu' ) ), 400 );
	}

	wp_set_password( $user_password, $user->ID );
	delete_transient( $code_key );

	wp_send_json_success( array( 'message' => esc_html__( '密码已重置，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_lostpassword', 'yneko_reimu_ajax_lostpassword' );
