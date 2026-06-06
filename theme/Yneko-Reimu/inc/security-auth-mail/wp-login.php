<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_security_registration_errors( $errors, $sanitized_user_login, $user_email ) {
	unset( $sanitized_user_login );
	if ( ! yneko_reimu_auth_security_enabled_for( 'wp-login' ) || ! is_email( $user_email ) ) {
		return $errors;
	}

	$check = yneko_reimu_auth_security_check( 'register', $user_email, 'wp-login' );
	if ( is_wp_error( $check ) ) {
		$errors->add( 'yneko_reimu_auth_mail_rate_limited', __( '请求过于频繁，请稍后再试。', 'yneko-reimu' ) );
		return $errors;
	}

	yneko_reimu_auth_security_commit( $check );
	return $errors;
}
add_filter( 'registration_errors', 'yneko_reimu_auth_security_registration_errors', 10, 3 );

function yneko_reimu_auth_security_lostpassword_errors( $errors, $user_data ) {
	if ( ! yneko_reimu_auth_security_enabled_for( 'wp-login' ) ) {
		return $errors;
	}

	$email = yneko_reimu_auth_security_lostpassword_email( $user_data );
	if ( '' === $email || ! is_email( $email ) ) {
		return $errors;
	}

	$check = yneko_reimu_auth_security_check( 'lostpassword', $email, 'wp-login' );
	if ( is_wp_error( $check ) ) {
		$errors->add( 'yneko_reimu_auth_mail_rate_limited', __( '请求过于频繁，请稍后再试。', 'yneko-reimu' ) );
		return $errors;
	}

	yneko_reimu_auth_security_commit( $check );
	return $errors;
}
add_filter( 'lostpassword_errors', 'yneko_reimu_auth_security_lostpassword_errors', 10, 2 );

function yneko_reimu_auth_security_lostpassword_email( $user_data ) {
	if ( $user_data instanceof WP_User ) {
		return $user_data->user_email;
	}
	if ( isset( $_POST['user_login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return sanitize_email( wp_unslash( $_POST['user_login'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	return '';
}
