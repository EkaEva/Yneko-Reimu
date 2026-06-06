<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_auth_security_events() {
	$events = get_option( 'yneko_reimu_auth_security_events', array() );
	return is_array( $events ) ? array_values( array_filter( $events, 'is_array' ) ) : array();
}

function yneko_reimu_auth_security_unhandled_count() {
	$count = 0;
	foreach ( yneko_reimu_auth_security_events() as $event ) {
		if ( empty( $event['handled'] ) ) {
			$count++;
		}
	}
	return $count;
}

function yneko_reimu_auth_security_log_event( $type, $context, $rule = array() ) {
	$event  = yneko_reimu_auth_security_event_payload( $type, $context, $rule );
	$events = yneko_reimu_auth_security_events();
	array_unshift( $events, $event );
	$events = array_slice( $events, 0, 100 );
	update_option( 'yneko_reimu_auth_security_events', $events, false );

	if ( function_exists( 'error_log' ) ) {
		error_log( '[Yneko-Reimu auth security] ' . wp_json_encode( $event ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	yneko_reimu_auth_security_maybe_email_alert( $event );
}

function yneko_reimu_auth_security_event_payload( $type, $context, $rule ) {
	$context = is_array( $context ) ? $context : array();
	$rule    = is_array( $rule ) ? $rule : array();

	return array(
		'id'          => substr( md5( wp_generate_uuid4() . microtime( true ) ), 0, 12 ),
		'time'        => time(),
		'type'        => sanitize_key( $type ),
		'scope'       => sanitize_key( $context['scope'] ?? '' ),
		'channel'     => sanitize_key( $context['channel'] ?? '' ),
		'dimension'   => sanitize_key( $rule['dimension'] ?? '' ),
		'period'      => sanitize_key( $rule['period'] ?? '' ),
		'value'       => absint( $rule['value'] ?? 0 ),
		'limit'       => absint( $rule['limit'] ?? 0 ),
		'email_hash'  => sanitize_text_field( $context['email_hash'] ?? '' ),
		'ip_hash'     => sanitize_text_field( $context['ip_hash'] ?? '' ),
		'device_hash' => sanitize_text_field( $context['device_hash'] ?? '' ),
		'handled'     => 0,
	);
}

function yneko_reimu_auth_security_maybe_email_alert( $event ) {
	$settings = yneko_reimu_settings_auth_security();
	if ( '1' !== (string) ( $settings['email_alert_enabled'] ?? '0' ) ) {
		return;
	}
	if ( ! in_array( (string) ( $event['type'] ?? '' ), array( 'blocked', 'mail_failed', 'global_warning' ), true ) ) {
		return;
	}

	$throttle_key = 'yneko_reimu_auth_mail_alert_' . sanitize_key( $event['type'] ?? 'event' );
	if ( get_transient( $throttle_key ) ) {
		return;
	}
	set_transient( $throttle_key, 1, HOUR_IN_SECONDS );

	yneko_reimu_auth_security_send_email_alert( $event );
}

function yneko_reimu_auth_security_send_email_alert( $event ) {
	$admin_email = get_option( 'admin_email' );
	if ( ! is_email( $admin_email ) ) {
		return;
	}

	wp_mail(
		$admin_email,
		wp_specialchars_decode(
			sprintf(
				/* translators: %s: site title. */
				__( '[%s] 认证邮件安全报警', 'yneko-reimu' ),
				get_option( 'blogname' )
			)
		),
		sprintf(
			/* translators: 1: event type, 2: scope, 3: dimension. */
			__( "Yneko-Reimu 捕获到认证邮件风控事件。\n\n类型：%1\$s\n范围：%2\$s\n维度：%3\$s\n请进入 Yneko-Reimu 设置 -> 安全设置 查看详情。", 'yneko-reimu' ),
			(string) ( $event['type'] ?? '' ),
			(string) ( $event['scope'] ?? '' ),
			(string) ( $event['dimension'] ?? '' )
		)
	);
}

function yneko_reimu_auth_security_record_mail_failure( $scope, $email, $channel = 'ajax' ) {
	$context = yneko_reimu_auth_security_context( $scope, $email, $channel );
	yneko_reimu_auth_security_log_event(
		'mail_failed',
		$context,
		array(
			'dimension' => 'mail',
			'period'    => 'request',
			'value'     => 1,
			'limit'     => 0,
		)
	);
}

function yneko_reimu_auth_security_scope_label( $scope ) {
	$labels = array(
		'register'      => array( '注册验证码', 'Registration code' ),
		'lostpassword'  => array( '忘记密码验证码', 'Lost-password code' ),
		'profile_email' => array( '资料邮箱验证码', 'Profile email code' ),
	);
	$row    = $labels[ sanitize_key( $scope ) ] ?? array( $scope, $scope );
	return function_exists( 'yneko_reimu_admin_prefers_zh' ) && yneko_reimu_admin_prefers_zh() ? $row[0] : $row[1];
}

function yneko_reimu_auth_security_event_type_label( $type ) {
	$labels = array(
		'blocked'        => array( '限额阻断', 'Rate blocked' ),
		'global_warning' => array( '全站预算预警', 'Global budget warning' ),
		'mail_failed'    => array( '邮件发送失败', 'Mail failed' ),
	);
	$row    = $labels[ sanitize_key( $type ) ] ?? array( $type, $type );
	return function_exists( 'yneko_reimu_admin_prefers_zh' ) && yneko_reimu_admin_prefers_zh() ? $row[0] : $row[1];
}
