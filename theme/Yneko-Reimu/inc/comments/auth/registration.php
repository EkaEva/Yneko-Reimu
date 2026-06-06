<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_send_register_code() {
	check_ajax_referer( 'yneko_reimu_ajax_register_code', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '当前未开放注册。', 'yneko-reimu' ) ), 403 );
	}

	$display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$user_email   = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$errors       = yneko_reimu_validate_registration_fields( $display_name, $user_email );

	if ( $errors->has_errors() ) {
		wp_send_json_error( array( 'message' => $errors->get_error_message() ), 400 );
	}

	$auth_security_context = function_exists( 'yneko_reimu_auth_security_check' ) ? yneko_reimu_auth_security_check( 'register', $user_email, 'ajax' ) : true;
	if ( is_wp_error( $auth_security_context ) ) {
		wp_send_json_error( array( 'message' => $auth_security_context->get_error_message() ), 429 );
	}
	if ( function_exists( 'yneko_reimu_auth_security_commit' ) ) {
		yneko_reimu_auth_security_commit( $auth_security_context );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 注册验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的注册验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请忽略这封邮件。', 'yneko-reimu' ),
		$code,
		5
	);

	if ( ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
		if ( function_exists( 'yneko_reimu_auth_security_record_mail_failure' ) ) {
			yneko_reimu_auth_security_record_mail_failure( 'register', $user_email, 'ajax' );
		}
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'reg', $display_name, $user_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
		),
		5 * MINUTE_IN_SECONDS
	);

	wp_send_json_success( array( 'message' => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_register_code', 'yneko_reimu_ajax_send_register_code' );

function yneko_reimu_ajax_register() {
	check_ajax_referer( 'yneko_reimu_ajax_register', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '当前未开放注册。', 'yneko-reimu' ) ), 403 );
	}

	$display_name  = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$user_email    = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$user_password = isset( $_POST['user_password'] ) ? (string) wp_unslash( $_POST['user_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be created raw.
	$verify_code   = isset( $_POST['verify_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['verify_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$errors        = yneko_reimu_validate_registration_fields( $display_name, $user_email, $user_password, true );

	if ( $errors->has_errors() ) {
		wp_send_json_error( array( 'message' => $errors->get_error_message() ), 400 );
	}
	if ( ! preg_match( '/^\d{6}$/', $verify_code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 6 位邮箱验证码。', 'yneko-reimu' ) ), 400 );
	}

	$code_key  = yneko_reimu_auth_code_transient_key( 'reg', $display_name, $user_email );
	$code_data = get_transient( $code_key );
	if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码已失效，请重新获取。', 'yneko-reimu' ) ), 400 );
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

	$user_login = yneko_reimu_generate_unique_login_from_email( $user_email );
	$user_id = wp_create_user( $user_login, $user_password, $user_email );
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ), 400 );
	}
	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $display_name,
			'nickname'     => $display_name,
		)
	);

	delete_transient( $code_key );
	wp_new_user_notification( $user_id, null, 'admin' );
	wp_send_json_success( array( 'message' => esc_html__( '注册成功，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_register', 'yneko_reimu_ajax_register' );
