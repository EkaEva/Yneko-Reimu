<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_user_profile_payload( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$user    = $user_id ? get_userdata( $user_id ) : null;
	if ( ! $user ) {
		return array();
	}

	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	$public_profile_url = $user->user_url ? $user->user_url : '';
	$profile_touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_profile_url_touched', true );
	$profile_url = $public_profile_url ? $public_profile_url : ( $profile_touched ? '' : yneko_reimu_comment_user_github_url( $user_id ) );
	return array(
		'userId'      => $user_id,
		'displayName' => $user->display_name ? $user->display_name : $user->user_login,
		'email'       => $user->user_email,
		'avatarUrl'   => yneko_reimu_user_profile_avatar_url( $user_id ),
		'pendingAvatarUrl' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true ),
		'avatarStatus' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'avatarPending' => 'pending' === (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'reviewStatuses' => yneko_reimu_user_review_status_payload( $user_id ),
		'profileUrl'  => $profile_url,
		'publicProfileUrl' => $public_profile_url,
		'twoFactor'   => yneko_reimu_user_2fa_enabled( $user_id ),
		'avatarUploadEnabled' => '1' === (string) ( $settings['avatar_enabled'] ?? '0' ),
		'avatarReviewEnabled' => '1' === (string) ( $settings['avatar_review'] ?? '0' ),
		'avatarMaxMb' => max( 1, absint( $settings['avatar_max_mb'] ?? 1 ) ),
		'avatarFrameEnabled' => '0' !== (string) get_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', true ),
		'avatarHtml' => yneko_reimu_comment_avatar_for_user_html( $user_id, 56 ),
		'commentBadgesEnabled' => yneko_reimu_comment_badges_enabled(),
		'commentTags' => yneko_reimu_comment_user_tags_payload( $user_id ),
		'pendingCommentTags' => yneko_reimu_comment_user_pending_tags( $user_id ),
		'commentBadgesHtml' => yneko_reimu_comment_user_badges_html( $user_id ),
		'commentTagReviewEnabled' => yneko_reimu_comment_tag_review_enabled(),
	);
}

function yneko_reimu_user_2fa_secret( $user_id ) {
	return (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_secret', true );
}

function yneko_reimu_user_2fa_enabled( $user_id ) {
	return '1' === (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_enabled', true ) && '' !== yneko_reimu_user_2fa_secret( $user_id );
}

function yneko_reimu_totp_base32_chars() {
	return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
}

function yneko_reimu_totp_generate_secret( $length = 20 ) {
	$chars  = yneko_reimu_totp_base32_chars();
	$secret = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$secret .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
	}
	return $secret;
}

function yneko_reimu_totp_base32_decode( $secret ) {
	$secret = strtoupper( preg_replace( '/[^A-Z2-7]/', '', (string) $secret ) );
	$chars  = yneko_reimu_totp_base32_chars();
	$bits   = '';
	$bytes  = '';

	for ( $i = 0; $i < strlen( $secret ); $i++ ) {
		$index = strpos( $chars, $secret[ $i ] );
		if ( false === $index ) {
			continue;
		}
		$bits .= str_pad( decbin( $index ), 5, '0', STR_PAD_LEFT );
	}

	for ( $i = 0; $i + 8 <= strlen( $bits ); $i += 8 ) {
		$bytes .= chr( bindec( substr( $bits, $i, 8 ) ) );
	}

	return $bytes;
}

function yneko_reimu_totp_code( $secret, $time_slice = null ) {
	$time_slice = null === $time_slice ? floor( time() / 30 ) : (int) $time_slice;
	$key        = yneko_reimu_totp_base32_decode( $secret );
	if ( '' === $key ) {
		return '';
	}

	$counter = pack( 'N*', 0 ) . pack( 'N*', $time_slice );
	$hash    = hash_hmac( 'sha1', $counter, $key, true );
	$offset  = ord( substr( $hash, -1 ) ) & 0x0f;
	$value   = unpack( 'N', substr( $hash, $offset, 4 ) )[1] & 0x7fffffff;
	return str_pad( (string) ( $value % 1000000 ), 6, '0', STR_PAD_LEFT );
}

function yneko_reimu_totp_verify( $secret, $code ) {
	$code = preg_replace( '/\D+/', '', (string) $code );
	if ( ! preg_match( '/^\d{6}$/', $code ) || '' === $secret ) {
		return false;
	}

	$slice = floor( time() / 30 );
	for ( $i = -1; $i <= 1; $i++ ) {
		if ( hash_equals( yneko_reimu_totp_code( $secret, $slice + $i ), $code ) ) {
			return true;
		}
	}
	return false;
}

function yneko_reimu_totp_uri( $user_id, $secret ) {
	$user  = get_userdata( absint( $user_id ) );
	$email = $user ? $user->user_email : '';
	$label = rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . ':' . $email );
	return 'otpauth://totp/' . $label . '?secret=' . rawurlencode( $secret ) . '&issuer=' . rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) . '&algorithm=SHA1&digits=6&period=30';
}

function yneko_reimu_ajax_profile_get() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请先登录。', 'yneko-reimu' ),
			),
			401
		);
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_user_profile_payload(),
			array(
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_get', 'yneko_reimu_ajax_profile_get' );

function yneko_reimu_ajax_profile_status_ack() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$types = isset( $_POST['types'] ) && is_array( $_POST['types'] ) ? array_map( 'sanitize_key', wp_unslash( $_POST['types'] ) ) : array();
	foreach ( $types as $type ) {
		$key = yneko_reimu_user_review_status_meta_key( $type );
		if ( ! $key ) {
			continue;
		}
		$status = (string) get_user_meta( get_current_user_id(), $key, true );
		if ( in_array( $status, array( 'updated', 'rejected' ), true ) ) {
			yneko_reimu_clear_user_review_status( get_current_user_id(), $type );
		}
	}

	wp_send_json_success(
		array(
			'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_status_ack', 'yneko_reimu_ajax_profile_status_ack' );

function yneko_reimu_ajax_profile_email_code() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user      = wp_get_current_user();
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$new_email = $new_email_input ? $new_email_input : $user->user_email;
	if ( '' === $new_email || ! is_email( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) ), 400 );
	}
	if ( strtolower( $new_email ) === strtolower( $user->user_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '新邮箱地址不要与原邮箱地址重复。', 'yneko-reimu' ) ), 400 );
	}
	if ( email_exists( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) ), 400 );
	}

	$auth_security_context = function_exists( 'yneko_reimu_auth_security_check' ) ? yneko_reimu_auth_security_check( 'profile_email', $new_email, 'ajax' ) : true;
	if ( is_wp_error( $auth_security_context ) ) {
		wp_send_json_error( array( 'message' => $auth_security_context->get_error_message() ), 429 );
	}
	if ( function_exists( 'yneko_reimu_auth_security_commit' ) ) {
		yneko_reimu_auth_security_commit( $auth_security_context );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 邮箱修改验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的邮箱修改验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。', 'yneko-reimu' ),
		$code,
		5
	);
	if ( ! wp_mail( $new_email, wp_specialchars_decode( $title ), $message ) ) {
		if ( function_exists( 'yneko_reimu_auth_security_record_mail_failure' ) ) {
			yneko_reimu_auth_security_record_mail_failure( 'profile_email', $new_email, 'ajax' );
		}
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'profile_email', (string) get_current_user_id(), $new_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
		),
		5 * MINUTE_IN_SECONDS
	);
	wp_send_json_success(
		array(
			'message'      => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ),
			'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_email_code', 'yneko_reimu_ajax_profile_email_code' );

function yneko_reimu_ajax_profile_totp_generate() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$secret = yneko_reimu_totp_generate_secret();
	update_user_meta( get_current_user_id(), '_yneko_reimu_totp_pending_secret', $secret );
	wp_send_json_success(
		array(
			'secret' => $secret,
			'uri'    => yneko_reimu_totp_uri( get_current_user_id(), $secret ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_totp_generate', 'yneko_reimu_ajax_profile_totp_generate' );

function yneko_reimu_ajax_profile_avatar_upload() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id = get_current_user_id();
	$result  = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'message'      => $result->get_error_message(),
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			),
			400
		);
	}

	$pending = ! empty( $result['pending'] );
	wp_send_json_success(
		array_merge(
			array(
				'message'      => $pending ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '头像已更新', 'yneko-reimu' ),
				'avatarUploadUrl' => $result['url'],
				'avatarUploadPending' => $pending,
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
				'identity'     => yneko_reimu_comment_current_user_identity_html( $redirect ),
			),
			yneko_reimu_user_profile_payload( $user_id )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_avatar_upload', 'yneko_reimu_ajax_profile_avatar_upload' );

function yneko_reimu_ajax_profile_save() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id      = get_current_user_id();
	$user         = wp_get_current_user();
	$display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$profile_url  = isset( $_POST['profile_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['profile_url'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$avatar_url   = isset( $_POST['avatar_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['avatar_url'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$avatar_changed = ! empty( $_POST['avatar_changed'] );
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$new_email    = $new_email_input ? $new_email_input : $user->user_email;
	$email_code   = isset( $_POST['email_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['email_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$new_password = isset( $_POST['new_password'] ) ? (string) wp_unslash( $_POST['new_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be set raw.
	$new_password_confirm = isset( $_POST['new_password_confirm'] ) ? (string) wp_unslash( $_POST['new_password_confirm'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be compared raw.
	$avatar_frame_enabled = ! empty( $_POST['avatar_frame_enabled'] );
	$totp_enabled = ! empty( $_POST['totp_enabled'] );
	$totp_code    = isset( $_POST['totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_labels   = isset( $_POST['comment_tag_label'] ) && is_array( $_POST['comment_tag_label'] ) ? wp_unslash( $_POST['comment_tag_label'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_colors   = isset( $_POST['comment_tag_color'] ) && is_array( $_POST['comment_tag_color'] ) ? wp_unslash( $_POST['comment_tag_color'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_ids      = isset( $_POST['comment_tag_id'] ) && is_array( $_POST['comment_tag_id'] ) ? wp_unslash( $_POST['comment_tag_id'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_enabled_input = isset( $_POST['comment_tag_enabled'] ) && is_array( $_POST['comment_tag_enabled'] ) ? wp_unslash( $_POST['comment_tag_enabled'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$special_enabled_input = isset( $_POST['comment_special_enabled'] ) && is_array( $_POST['comment_special_enabled'] ) ? wp_unslash( $_POST['comment_special_enabled'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' === $display_name || mb_strlen( $display_name ) > 50 ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 1 到 50 个字符的昵称。', 'yneko-reimu' ) ), 400 );
	}
	if ( '' === $new_email || ! is_email( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) ), 400 );
	}

	$comment_tags = array();
	$hidden_special_badges = array();
	$avatar_pending = false;
	$tags_pending = false;
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	foreach ( $special_badges as $index => $special_badge ) {
		$type = sanitize_key( $special_badge['type'] ?? '' );
		if ( ! $type ) {
			continue;
		}
		$enabled = ! empty( $special_enabled_input[ $type ] );
		if ( ! $enabled ) {
			$hidden_special_badges[] = $type;
		}
	}
	$special_counts = max( 0, count( $special_badges ) - count( $hidden_special_badges ) );
	$display_limit   = yneko_reimu_comment_badge_display_limit();
	if ( $special_counts > $display_limit ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
				'field'   => 'comment_tag_label',
			),
			400
		);
	}
	$custom_capacity = yneko_reimu_comment_badges_enabled() ? max( 0, $display_limit - $special_counts ) : 0;
	$enabled_custom_count = 0;
	if ( yneko_reimu_comment_badges_enabled() ) {
		foreach ( $tag_labels as $index => $raw_label ) {
			if ( count( $comment_tags ) >= yneko_reimu_comment_custom_tag_storage_limit() ) {
				break;
			}
			$raw_label = isset( $tag_labels[ $index ] ) ? (string) $tag_labels[ $index ] : '';
			$label     = yneko_reimu_sanitize_comment_tag_label( $raw_label );
			if ( '' === $label ) {
				continue;
			}
			if ( mb_strlen( trim( wp_strip_all_tags( $raw_label ) ) ) > 8 ) {
				wp_send_json_error( array( 'message' => esc_html__( '评论标签最多 8 个字符。', 'yneko-reimu' ) ), 400 );
			}
			if ( yneko_reimu_comment_tag_label_is_reserved( $label ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( '该评论标签为系统保留或屏蔽标签，请换一个。', 'yneko-reimu' ),
						'field'   => 'comment_tag_label',
						'value'   => $label,
						'index'   => absint( $index ),
					),
					400
				);
			}
			$color = sanitize_hex_color( $tag_colors[ $index ] ?? '' );
			$enabled = ! empty( $tag_enabled_input[ $index ] ) && $enabled_custom_count < $custom_capacity;
			if ( ! empty( $tag_enabled_input[ $index ] ) && ! $enabled ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
						'field'   => 'comment_tag_label',
						'index'   => absint( $index ),
					),
					400
				);
			}
			if ( $enabled ) {
				$enabled_custom_count++;
			}
			$comment_tags[] = array(
				'id'      => yneko_reimu_comment_tag_id( $tag_ids[ $index ] ?? '' ),
				'label'   => $label,
				'color'   => $color ? $color : '#ff5252',
				'enabled' => $enabled ? '1' : '0',
			);
		}
	}

	$update = array(
		'ID'           => $user_id,
		'display_name' => $display_name,
		'nickname'     => $display_name,
		'user_url'     => $profile_url,
	);

	if ( strtolower( $new_email ) !== strtolower( $user->user_email ) ) {
		if ( email_exists( $new_email ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) ), 400 );
		}
		$code_key = yneko_reimu_auth_code_transient_key( 'profile_email', (string) $user_id, $new_email );
		$code_data = get_transient( $code_key );
		if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) || ! wp_check_password( $email_code, $code_data['code_hash'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '邮箱验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
		}
		$update['user_email'] = $new_email;
		delete_transient( $code_key );
	}

	if ( '' !== $new_password || '' !== $new_password_confirm ) {
		if ( $new_password !== $new_password_confirm ) {
			wp_send_json_error( array( 'message' => esc_html__( '两次输入的密码不一致。', 'yneko-reimu' ) ), 400 );
		}
		if ( strlen( $new_password ) < 8 ) {
			wp_send_json_error( array( 'message' => esc_html__( '密码至少需要 8 个字符。', 'yneko-reimu' ) ), 400 );
		}
	}

	if ( isset( $_FILES['avatar_file'] ) && ! empty( $_FILES['avatar_file']['name'] ) ) {
		$avatar_changed = true;
		$avatar_result = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $avatar_result ) ) {
			wp_send_json_error( array( 'message' => $avatar_result->get_error_message() ), 400 );
		}
		$avatar_url     = $avatar_result['url'];
		$avatar_pending = ! empty( $avatar_result['pending'] );
	}

	if ( $avatar_changed ) {
		if ( $avatar_url && ! $avatar_pending ) {
			update_user_meta( $user_id, '_yneko_reimu_avatar_url', $avatar_url );
			delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
			yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
		} elseif ( ! $avatar_url && ! $avatar_pending ) {
			delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
			delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
			yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
		}
	}

	if ( $totp_enabled ) {
		$current_secret = yneko_reimu_user_2fa_secret( $user_id );
		$pending_secret = (string) get_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', true );
		$secret = $current_secret ? $current_secret : $pending_secret;
		if ( '' === $secret ) {
			wp_send_json_error( array( 'message' => esc_html__( '请先生成认证器密钥。', 'yneko-reimu' ) ), 400 );
		}
		if ( ! yneko_reimu_totp_verify( $secret, $totp_code ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '认证器验证码不正确。', 'yneko-reimu' ) ), 400 );
		}
		update_user_meta( $user_id, '_yneko_reimu_totp_secret', $secret );
		update_user_meta( $user_id, '_yneko_reimu_totp_enabled', '1' );
		delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_totp_enabled' );
		delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	}

	$result = wp_update_user( $update );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
	}
	update_user_meta( $user_id, '_yneko_reimu_profile_url_touched', '1' );
	if ( $avatar_frame_enabled ) {
		delete_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled' );
	} else {
		update_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', '0' );
	}
	if ( $comment_tags ) {
		if ( yneko_reimu_comment_tag_review_enabled() && ! yneko_reimu_comment_user_can_bypass_tag_review( $user_id ) ) {
			$reviewed = yneko_reimu_comment_prepare_reviewed_tags( yneko_reimu_comment_user_custom_tags( $user_id ), $comment_tags );
			if ( $reviewed['active'] ) {
				update_user_meta( $user_id, '_yneko_reimu_comment_tags', $reviewed['active'] );
			} else {
				delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
			}
			if ( $reviewed['pending'] ) {
				update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $reviewed['pending'] );
				yneko_reimu_set_user_review_status( $user_id, 'tags', 'pending' );
				$tags_pending = true;
			} else {
				delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
				yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
			}
		} else {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags', $comment_tags );
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
			yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
		}
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
		yneko_reimu_clear_user_review_status( $user_id, 'tags' );
	}
	if ( $hidden_special_badges ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges', $hidden_special_badges );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges' );
	}
	update_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', '1' );
	if ( '' !== $new_password ) {
		wp_set_password( $new_password, $user_id );
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true, is_ssl() );
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_user_profile_payload( $user_id ),
			array(
				'message' => $avatar_pending ? esc_html__( '个人资料已保存，头像审核中。', 'yneko-reimu' ) : ( yneko_reimu_comment_tag_review_enabled() && ! yneko_reimu_comment_user_can_bypass_tag_review( $user_id ) && $comment_tags ? esc_html__( '个人资料已保存，评论标签审核中。', 'yneko-reimu' ) : esc_html__( '个人资料已保存。', 'yneko-reimu' ) ),
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce' => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
				'identity' => yneko_reimu_comment_current_user_identity_html( $redirect ),
				'tagsPending' => $tags_pending,
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_save', 'yneko_reimu_ajax_profile_save' );
