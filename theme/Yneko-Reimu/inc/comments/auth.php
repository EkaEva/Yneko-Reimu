<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_login_state() {
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );

	if ( ! is_user_logged_in() ) {
		wp_send_json_success(
			array(
				'loggedIn'       => false,
				'loginUrl'       => wp_login_url( $redirect ),
				'loginHtml'      => yneko_reimu_comment_login_link_html( $redirect ),
				'guestFieldsHtml'=> yneko_reimu_comment_guest_fields_html(),
				'loginModal'     => yneko_reimu_login_modal_html(),
				'commentUploads' => array(
					'enabled'      => yneko_reimu_comment_upload_enabled(),
					'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
					'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
					'isLoggedIn'   => false,
					'nonce'        => '',
				),
			)
		);
	}

	wp_send_json_success(
		array(
			'loggedIn'          => true,
			'identity'          => yneko_reimu_comment_current_user_identity_html( $redirect ),
			'profileModal'      => yneko_reimu_profile_modal_html(),
			'loginModal'        => yneko_reimu_login_modal_html(),
			'commentNonce'      => wp_create_nonce( 'yneko_reimu_submit_comment' ),
			'commentUploadNonce'=> wp_create_nonce( 'yneko_reimu_comment_upload' ),
			'commentUploads'    => array(
				'enabled'      => yneko_reimu_comment_upload_enabled(),
				'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
				'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
				'isLoggedIn'   => true,
				'nonce'        => wp_create_nonce( 'yneko_reimu_comment_upload' ),
			),
			'profileNonce'      => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'       => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			'profile'           => yneko_reimu_user_profile_payload(),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_login_state', 'yneko_reimu_ajax_login_state' );
add_action( 'wp_ajax_nopriv_yneko_reimu_login_state', 'yneko_reimu_ajax_login_state' );

function yneko_reimu_ajax_logout() {
	check_ajax_referer( 'yneko_reimu_ajax_logout', 'nonce' );
	wp_logout();

	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );
	wp_send_json_success(
		array(
			'message'        => esc_html__( '已退出登录。', 'yneko-reimu' ),
			'loginUrl'       => '#reimu-login-modal',
			'loginHtml'      => yneko_reimu_comment_login_link_html( $redirect ),
			'guestFieldsHtml'=> yneko_reimu_comment_guest_fields_html(),
			'loginModal'     => yneko_reimu_login_modal_html(),
			'commentUploads' => array(
				'enabled'      => yneko_reimu_comment_upload_enabled(),
				'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
				'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
				'isLoggedIn'   => false,
				'nonce'        => '',
			),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_logout', 'yneko_reimu_ajax_logout' );

function yneko_reimu_ajax_login() {
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

	$cooldown_key = yneko_reimu_auth_code_cooldown_transient_key( 'reg', $display_name, $user_email );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码已发送，请稍后再试。', 'yneko-reimu' ) ), 429 );
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
	set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );

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
	delete_transient( yneko_reimu_auth_code_cooldown_transient_key( 'reg', $display_name, $user_email ) );
	wp_new_user_notification( $user_id, null, 'admin' );
	wp_send_json_success( array( 'message' => esc_html__( '注册成功，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_register', 'yneko_reimu_ajax_register' );

function yneko_reimu_ajax_send_lostpassword_code() {
	check_ajax_referer( 'yneko_reimu_ajax_lostpassword_code', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	$identifier = isset( $_POST['user_login'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '';
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入注册邮箱。', 'yneko-reimu' ) ), 400 );
	}

	$success_message = esc_html__( '如果该邮箱已注册，验证码将发送到对应邮箱。', 'yneko-reimu' );
	$cooldown_key    = yneko_reimu_auth_code_cooldown_transient_key( 'lost', $identifier, $identifier );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_success( array( 'message' => $success_message ) );
	}

	$user = yneko_reimu_find_user_for_password_reset( $identifier );
	if ( ! $user ) {
		set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );
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
	set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );

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
	delete_transient( yneko_reimu_auth_code_cooldown_transient_key( 'lost', $identifier, $identifier ) );

	wp_send_json_success( array( 'message' => esc_html__( '密码已重置，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_lostpassword', 'yneko_reimu_ajax_lostpassword' );
