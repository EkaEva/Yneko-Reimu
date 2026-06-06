<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_security_bucket_id( $period = 'hour' ) {
	if ( 'day' === $period ) {
		return gmdate( 'Ymd' );
	}

	return gmdate( 'YmdH' );
}

function yneko_reimu_auth_security_counter_key( $scope, $dimension, $hash, $period ) {
	return 'yneko_reimu_auth_mail_' . sanitize_key( $period ) . '_' . sanitize_key( $scope ) . '_' . sanitize_key( $dimension ) . '_' . yneko_reimu_auth_security_bucket_id( $period ) . '_' . $hash;
}

function yneko_reimu_auth_security_counter_ttl( $period ) {
	return 'day' === $period ? 2 * DAY_IN_SECONDS : 2 * HOUR_IN_SECONDS;
}

function yneko_reimu_auth_security_counter_value( $key ) {
	return absint( get_transient( $key ) );
}

function yneko_reimu_auth_security_increment_counter( $key, $period ) {
	$value = yneko_reimu_auth_security_counter_value( $key ) + 1;
	set_transient( $key, $value, yneko_reimu_auth_security_counter_ttl( $period ) );
	return $value;
}

function yneko_reimu_auth_security_rule_checks( $context, $settings ) {
	return array(
		array( 'dimension' => 'email', 'period' => 'hour', 'hash' => $context['email_hash'], 'limit' => absint( $settings['email_hour_limit'] ?? 3 ) ),
		array( 'dimension' => 'email', 'period' => 'day', 'hash' => $context['email_hash'], 'limit' => absint( $settings['email_day_limit'] ?? 8 ) ),
		array( 'dimension' => 'ip', 'period' => 'hour', 'hash' => $context['ip_hash'], 'limit' => absint( $settings['ip_hour_limit'] ?? 10 ) ),
		array( 'dimension' => 'ip', 'period' => 'day', 'hash' => $context['ip_hash'], 'limit' => absint( $settings['ip_day_limit'] ?? 30 ) ),
		array( 'dimension' => 'device', 'period' => 'hour', 'hash' => $context['device_hash'], 'limit' => absint( $settings['device_hour_limit'] ?? 5 ) ),
		array( 'dimension' => 'device', 'period' => 'day', 'hash' => $context['device_hash'], 'limit' => absint( $settings['device_day_limit'] ?? 15 ) ),
		array( 'dimension' => 'global', 'period' => 'day', 'hash' => 'site', 'limit' => absint( $settings['global_day_limit'] ?? 100 ) ),
		array( 'dimension' => 'cooldown', 'period' => 'cooldown', 'hash' => $context['email_hash'] . '_' . $context['ip_hash'] . '_' . $context['device_hash'], 'limit' => 1, 'ttl' => absint( $settings['cooldown_seconds'] ?? 60 ) ),
	);
}

function yneko_reimu_auth_security_check( $scope, $email, $channel = 'ajax' ) {
	if ( ! yneko_reimu_auth_security_enabled_for( $channel ) ) {
		return true;
	}

	$settings = yneko_reimu_settings_auth_security();
	$context  = yneko_reimu_auth_security_context( $scope, $email, $channel );
	foreach ( yneko_reimu_auth_security_rule_checks( $context, $settings ) as $rule ) {
		$blocked = yneko_reimu_auth_security_blocked_rule( $context, $rule );
		if ( is_wp_error( $blocked ) ) {
			return $blocked;
		}
	}

	return $context;
}

function yneko_reimu_auth_security_blocked_rule( $context, $rule ) {
	if ( 'cooldown' === $rule['period'] ) {
		$key = 'yneko_reimu_auth_mail_cooldown_' . $context['scope'] . '_' . $rule['hash'];
		if ( get_transient( $key ) ) {
			yneko_reimu_auth_security_log_event( 'blocked', $context, array_merge( $rule, array( 'value' => 1 ) ) );
			return new WP_Error( 'auth_mail_rate_limited', __( '验证码已发送，请稍后再试。', 'yneko-reimu' ) );
		}
		return true;
	}

	$key   = yneko_reimu_auth_security_counter_key( $context['scope'], $rule['dimension'], $rule['hash'], $rule['period'] );
	$value = yneko_reimu_auth_security_counter_value( $key );
	if ( $value >= $rule['limit'] ) {
		yneko_reimu_auth_security_log_event( 'blocked', $context, array_merge( $rule, array( 'value' => $value ) ) );
		return new WP_Error( 'auth_mail_rate_limited', __( '请求过于频繁，请稍后再试。', 'yneko-reimu' ) );
	}

	return true;
}

function yneko_reimu_auth_security_commit( $context ) {
	if ( ! is_array( $context ) ) {
		return;
	}

	$settings = yneko_reimu_settings_auth_security();
	foreach ( yneko_reimu_auth_security_rule_checks( $context, $settings ) as $rule ) {
		yneko_reimu_auth_security_commit_rule( $context, $rule, $settings );
	}
}

function yneko_reimu_auth_security_commit_rule( $context, $rule, $settings ) {
	if ( 'cooldown' === $rule['period'] ) {
		$key = 'yneko_reimu_auth_mail_cooldown_' . $context['scope'] . '_' . $rule['hash'];
		set_transient( $key, 1, max( 10, absint( $rule['ttl'] ?? 60 ) ) );
		return;
	}

	$key   = yneko_reimu_auth_security_counter_key( $context['scope'], $rule['dimension'], $rule['hash'], $rule['period'] );
	$value = yneko_reimu_auth_security_increment_counter( $key, $rule['period'] );
	if ( 'global' === $rule['dimension'] && 'day' === $rule['period'] ) {
		yneko_reimu_auth_security_maybe_log_global_warning( $context, $rule, $value, $settings );
	}
}

function yneko_reimu_auth_security_maybe_log_global_warning( $context, $rule, $value, $settings ) {
	$limit      = max( 1, absint( $rule['limit'] ?? 100 ) );
	$threshold  = max( 50, min( 100, absint( $settings['global_warning_threshold'] ?? 80 ) ) );
	$warning_at = (int) ceil( $limit * $threshold / 100 );
	if ( $value < $warning_at ) {
		return;
	}

	$flag_key = yneko_reimu_auth_security_counter_key( $context['scope'], 'global_warning', 'site', 'day' );
	if ( get_transient( $flag_key ) ) {
		return;
	}

	set_transient( $flag_key, 1, DAY_IN_SECONDS );
	yneko_reimu_auth_security_log_event(
		'global_warning',
		$context,
		array(
			'dimension' => 'global',
			'period'    => 'day',
			'hash'      => 'site',
			'limit'     => $limit,
			'value'     => $value,
		)
	);
}
