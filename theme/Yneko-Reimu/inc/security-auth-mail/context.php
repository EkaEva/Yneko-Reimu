<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_security_client_ip() {
	if ( function_exists( 'yneko_reimu_auth_client_ip' ) ) {
		return yneko_reimu_auth_client_ip();
	}

	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function yneko_reimu_auth_security_hash( $value ) {
	return substr( hash( 'sha256', wp_salt( 'auth' ) . '|' . strtolower( (string) $value ) ), 0, 24 );
}

function yneko_reimu_auth_security_device_cookie_name() {
	return 'yneko_reimu_auth_device';
}

function yneko_reimu_auth_security_device_id() {
	$cookie_name = yneko_reimu_auth_security_device_cookie_name();
	$device      = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	if ( ! preg_match( '/^[a-f0-9]{32}$/', $device ) ) {
		$device = wp_generate_password( 32, false, false );
		$device = strtolower( preg_replace( '/[^a-f0-9]/', '', hash( 'sha256', $device . microtime( true ) ) ) );
		$device = substr( $device, 0, 32 );
	}

	return $device;
}

function yneko_reimu_auth_security_set_device_cookie() {
	if ( headers_sent() ) {
		return;
	}

	$cookie_name = yneko_reimu_auth_security_device_cookie_name();
	$device      = yneko_reimu_auth_security_device_id();
	$expires     = time() + 180 * DAY_IN_SECONDS;
	$args        = array(
		'expires'  => $expires,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
		'secure'   => is_ssl(),
		'httponly' => true,
		'samesite' => 'Lax',
	);

	if ( PHP_VERSION_ID >= 70300 ) {
		setcookie( $cookie_name, $device, $args );
	} else {
		$path = $args['path'] . '; SameSite=Lax';
		setcookie( $cookie_name, $device, $expires, $path, $args['domain'], $args['secure'], true );
	}
	$_COOKIE[ $cookie_name ] = $device;
}

function yneko_reimu_auth_security_context( $scope, $email, $channel = 'ajax' ) {
	$email = strtolower( sanitize_email( $email ) );
	yneko_reimu_auth_security_set_device_cookie();

	return array(
		'scope'       => sanitize_key( $scope ),
		'channel'     => sanitize_key( $channel ),
		'email'       => $email,
		'email_hash'  => yneko_reimu_auth_security_hash( $email ),
		'ip_hash'     => yneko_reimu_auth_security_hash( yneko_reimu_auth_security_client_ip() ),
		'device_hash' => yneko_reimu_auth_security_hash( yneko_reimu_auth_security_device_id() ),
	);
}
