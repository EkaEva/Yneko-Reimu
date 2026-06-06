<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_code_transient_key( $scope, $identifier, $email ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_code_' . hash( 'sha256', strtolower( $identifier ) . '|' . strtolower( $email ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_code_cooldown_transient_key( $scope, $identifier, $email ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_cooldown_' . hash( 'sha256', strtolower( $identifier ) . '|' . strtolower( $email ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function yneko_reimu_auth_generic_error_message() {
	return esc_html__( '登录失败，请检查账号和密码。', 'yneko-reimu' );
}

function yneko_reimu_auth_rate_key( $scope, $identifier ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_fail_' . hash( 'sha256', strtolower( (string) $identifier ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_rate_limited( $scope, $identifier ) {
	$data = get_transient( yneko_reimu_auth_rate_key( $scope, $identifier ) );
	return is_array( $data ) && absint( $data['count'] ?? 0 ) >= 5;
}

function yneko_reimu_auth_record_failure( $scope, $identifier ) {
	$key  = yneko_reimu_auth_rate_key( $scope, $identifier );
	$data = get_transient( $key );
	$data = is_array( $data ) ? $data : array( 'count' => 0 );
	$data['count'] = absint( $data['count'] ?? 0 ) + 1;
	set_transient( $key, $data, 15 * MINUTE_IN_SECONDS );
}

function yneko_reimu_auth_clear_failures( $scope, $identifier ) {
	delete_transient( yneko_reimu_auth_rate_key( $scope, $identifier ) );
}

function yneko_reimu_generate_unique_login_from_email( $email ) {
	$base = sanitize_user( current( explode( '@', $email ) ), true );
	if ( '' === $base ) {
		$base = 'user';
	}

	$user_login = $base;
	$suffix     = 1;
	while ( username_exists( $user_login ) ) {
		$suffix++;
		$user_login = $base . $suffix;
	}

	return $user_login;
}

function yneko_reimu_find_user_for_password_reset( $identifier ) {
	$identifier = trim( (string) $identifier );
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		return null;
	}

	return get_user_by( 'email', $identifier );
}

function yneko_reimu_validate_registration_fields( $display_name, $user_email, $user_password = '', $check_password = false ) {
	$errors = new WP_Error();
	$name   = trim( wp_strip_all_tags( (string) $display_name ) );

	if ( '' === $name ) {
		$errors->add( 'invalid_display_name', __( '请输入有效的昵称。', 'yneko-reimu' ) );
	} elseif ( mb_strlen( $name ) > 50 ) {
		$errors->add( 'display_name_too_long', __( '昵称不能超过 50 个字符。', 'yneko-reimu' ) );
	}

	if ( '' === $user_email || ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '请输入有效的邮箱地址。', 'yneko-reimu' ) );
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '该邮箱已被注册。', 'yneko-reimu' ) );
	}

	if ( $check_password && strlen( $user_password ) < 8 ) {
		$errors->add( 'weak_password', __( '密码至少需要 8 个字符。', 'yneko-reimu' ) );
	}

	return $errors;
}
