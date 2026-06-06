<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_security_defaults() {
	return array(
		'enabled'                  => '1',
		'protect_ajax'             => '1',
		'protect_wp_login'         => '1',
		'email_hour_limit'         => 3,
		'email_day_limit'          => 8,
		'ip_hour_limit'            => 10,
		'ip_day_limit'             => 30,
		'device_hour_limit'        => 5,
		'device_day_limit'         => 15,
		'global_day_limit'         => 100,
		'cooldown_seconds'         => 60,
		'global_warning_threshold' => 80,
		'email_alert_enabled'      => '0',
	);
}

function yneko_reimu_sanitize_auth_security_settings( $input, $defaults = null ) {
	$defaults = is_array( $defaults ) ? $defaults : yneko_reimu_auth_security_defaults();
	$input    = is_array( $input ) ? $input : array();

	return array(
		'enabled'                  => ! empty( $input['enabled'] ) ? '1' : '0',
		'protect_ajax'             => ! empty( $input['protect_ajax'] ) ? '1' : '0',
		'protect_wp_login'         => ! empty( $input['protect_wp_login'] ) ? '1' : '0',
		'email_hour_limit'         => max( 1, min( 1000, absint( $input['email_hour_limit'] ?? $defaults['email_hour_limit'] ) ) ),
		'email_day_limit'          => max( 1, min( 5000, absint( $input['email_day_limit'] ?? $defaults['email_day_limit'] ) ) ),
		'ip_hour_limit'            => max( 1, min( 5000, absint( $input['ip_hour_limit'] ?? $defaults['ip_hour_limit'] ) ) ),
		'ip_day_limit'             => max( 1, min( 20000, absint( $input['ip_day_limit'] ?? $defaults['ip_day_limit'] ) ) ),
		'device_hour_limit'        => max( 1, min( 5000, absint( $input['device_hour_limit'] ?? $defaults['device_hour_limit'] ) ) ),
		'device_day_limit'         => max( 1, min( 20000, absint( $input['device_day_limit'] ?? $defaults['device_day_limit'] ) ) ),
		'global_day_limit'         => max( 1, min( 100000, absint( $input['global_day_limit'] ?? $defaults['global_day_limit'] ) ) ),
		'cooldown_seconds'         => max( 10, min( 3600, absint( $input['cooldown_seconds'] ?? $defaults['cooldown_seconds'] ) ) ),
		'global_warning_threshold' => max( 50, min( 100, absint( $input['global_warning_threshold'] ?? $defaults['global_warning_threshold'] ) ) ),
		'email_alert_enabled'      => ! empty( $input['email_alert_enabled'] ) ? '1' : '0',
	);
}

function yneko_reimu_settings_auth_security() {
	if ( function_exists( 'yneko_reimu_settings_group' ) ) {
		return yneko_reimu_sanitize_auth_security_settings( yneko_reimu_settings_group( 'auth_security' ) );
	}

	return yneko_reimu_auth_security_defaults();
}

function yneko_reimu_auth_security_enabled_for( $channel ) {
	$settings = yneko_reimu_settings_auth_security();
	if ( '1' !== (string) ( $settings['enabled'] ?? '1' ) ) {
		return false;
	}

	$key = 'wp-login' === $channel ? 'protect_wp_login' : 'protect_ajax';
	return '1' === (string) ( $settings[ $key ] ?? '1' );
}
