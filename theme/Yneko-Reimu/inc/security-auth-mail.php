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
	$scope = $context['scope'];
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
		if ( 'cooldown' === $rule['period'] ) {
			$key = 'yneko_reimu_auth_mail_cooldown_' . $context['scope'] . '_' . $rule['hash'];
			if ( get_transient( $key ) ) {
				yneko_reimu_auth_security_log_event( 'blocked', $context, array_merge( $rule, array( 'value' => 1 ) ) );
				return new WP_Error( 'auth_mail_rate_limited', __( '验证码已发送，请稍后再试。', 'yneko-reimu' ) );
			}
			continue;
		}

		$key   = yneko_reimu_auth_security_counter_key( $context['scope'], $rule['dimension'], $rule['hash'], $rule['period'] );
		$value = yneko_reimu_auth_security_counter_value( $key );
		if ( $value >= $rule['limit'] ) {
			yneko_reimu_auth_security_log_event( 'blocked', $context, array_merge( $rule, array( 'value' => $value ) ) );
			return new WP_Error( 'auth_mail_rate_limited', __( '请求过于频繁，请稍后再试。', 'yneko-reimu' ) );
		}
	}

	return $context;
}

function yneko_reimu_auth_security_commit( $context ) {
	if ( ! is_array( $context ) ) {
		return;
	}

	$settings = yneko_reimu_settings_auth_security();
	foreach ( yneko_reimu_auth_security_rule_checks( $context, $settings ) as $rule ) {
		if ( 'cooldown' === $rule['period'] ) {
			$key = 'yneko_reimu_auth_mail_cooldown_' . $context['scope'] . '_' . $rule['hash'];
			set_transient( $key, 1, max( 10, absint( $rule['ttl'] ?? 60 ) ) );
			continue;
		}

		$key   = yneko_reimu_auth_security_counter_key( $context['scope'], $rule['dimension'], $rule['hash'], $rule['period'] );
		$value = yneko_reimu_auth_security_increment_counter( $key, $rule['period'] );
		if ( 'global' === $rule['dimension'] && 'day' === $rule['period'] ) {
			yneko_reimu_auth_security_maybe_log_global_warning( $context, $rule, $value, $settings );
		}
	}
}

function yneko_reimu_auth_security_maybe_log_global_warning( $context, $rule, $value, $settings ) {
	$limit     = max( 1, absint( $rule['limit'] ?? 100 ) );
	$threshold = max( 50, min( 100, absint( $settings['global_warning_threshold'] ?? 80 ) ) );
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
	$context = is_array( $context ) ? $context : array();
	$rule    = is_array( $rule ) ? $rule : array();
	$event   = array(
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
	$events = yneko_reimu_auth_security_events();
	array_unshift( $events, $event );
	$events = array_slice( $events, 0, 100 );
	update_option( 'yneko_reimu_auth_security_events', $events, false );

	if ( function_exists( 'error_log' ) ) {
		error_log( '[Yneko-Reimu auth security] ' . wp_json_encode( $event ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	yneko_reimu_auth_security_maybe_email_alert( $event );
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
	$row = $labels[ sanitize_key( $scope ) ] ?? array( $scope, $scope );
	return function_exists( 'yneko_reimu_admin_prefers_zh' ) && yneko_reimu_admin_prefers_zh() ? $row[0] : $row[1];
}

function yneko_reimu_auth_security_event_type_label( $type ) {
	$labels = array(
		'blocked'        => array( '限额阻断', 'Rate blocked' ),
		'global_warning' => array( '全站预算预警', 'Global budget warning' ),
		'mail_failed'    => array( '邮件发送失败', 'Mail failed' ),
	);
	$row = $labels[ sanitize_key( $type ) ] ?? array( $type, $type );
	return function_exists( 'yneko_reimu_admin_prefers_zh' ) && yneko_reimu_admin_prefers_zh() ? $row[0] : $row[1];
}

function yneko_reimu_auth_security_admin_action() {
	if ( empty( $_GET['yneko_auth_security_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	$action = sanitize_key( wp_unslash( $_GET['yneko_auth_security_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	check_admin_referer( 'yneko_reimu_auth_security_' . $action );

	if ( 'mark_handled' === $action ) {
		$events = yneko_reimu_auth_security_events();
		foreach ( $events as &$event ) {
			$event['handled'] = 1;
		}
		unset( $event );
		update_option( 'yneko_reimu_auth_security_events', $events, false );
	} elseif ( 'clear' === $action ) {
		delete_option( 'yneko_reimu_auth_security_events' );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=yneko-reimu-settings#security' ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_auth_security_admin_action' );

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

	$email = '';
	if ( $user_data instanceof WP_User ) {
		$email = $user_data->user_email;
	} elseif ( isset( $_POST['user_login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$email = sanitize_email( wp_unslash( $_POST['user_login'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

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
