<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_login() {
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	if ( ! check_ajax_referer( 'yneko_reimu_ajax_login', 'nonce', false ) ) {
		wp_send_json_error(
			array(
				'message'    => esc_html__( '登录信息已过期，请重试。', 'yneko-reimu' ),
				'loginNonce' => wp_create_nonce( 'yneko_reimu_ajax_login' ),
			),
			403
		);
	}

	$email    = isset( $_POST['log'] ) ? strtolower( sanitize_email( wp_unslash( $_POST['log'] ) ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be checked raw.
	$remember = ! empty( $_POST['rememberme'] );
	$two_factor_code = isset( $_POST['two_factor_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['two_factor_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' === $email || ! is_email( $email ) || '' === $password ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请输入邮箱和密码。', 'yneko-reimu' ),
			),
			400
		);
	}

	if ( yneko_reimu_auth_rate_limited( 'login', $email ) ) {
		wp_send_json_error(
			array(
				'message' => yneko_reimu_auth_generic_error_message(),
			),
			429
		);
	}

	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		yneko_reimu_auth_record_failure( 'login', $email );
		wp_send_json_error( array( 'message' => yneko_reimu_auth_generic_error_message() ), 403 );
	}

	if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
		yneko_reimu_auth_record_failure( 'login', $email );
		wp_send_json_error( array( 'message' => yneko_reimu_auth_generic_error_message() ), 403 );
	}

	if ( yneko_reimu_user_2fa_enabled( $user->ID ) ) {
		if ( ! preg_match( '/^\d{6}$/', $two_factor_code ) ) {
			wp_send_json_error(
				array(
					'message'     => esc_html__( '请输入两步验证码。', 'yneko-reimu' ),
					'requires2fa' => true,
				),
				401
			);
		}
		if ( ! yneko_reimu_totp_verify( yneko_reimu_user_2fa_secret( $user->ID ), $two_factor_code ) ) {
			yneko_reimu_auth_record_failure( 'login', $email );
			wp_send_json_error(
				array(
					'message'     => esc_html__( '两步验证码不正确。', 'yneko-reimu' ),
					'requires2fa' => true,
				),
				403
			);
		}
	}

	yneko_reimu_auth_clear_failures( 'login', $email );
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, $remember, is_ssl() );
	do_action( 'wp_login', $user->user_login, $user );

	wp_send_json_success(
		array(
			'message' => esc_html__( '登录成功。', 'yneko-reimu' ),
			'loginNonce' => wp_create_nonce( 'yneko_reimu_ajax_login' ),
		)
	);
}
add_action( 'wp_ajax_nopriv_yneko_reimu_login', 'yneko_reimu_ajax_login' );
