<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_profile_save_request( $user ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- The public profile-save AJAX handler verifies the profile nonce before calling this internal parser.
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';

	$request = array(
		'display_name'          => isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '',
		'profile_url'           => isset( $_POST['profile_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['profile_url'] ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'avatar_url'            => isset( $_POST['avatar_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['avatar_url'] ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'avatar_changed'        => ! empty( $_POST['avatar_changed'] ),
		'new_email'             => $new_email_input ? $new_email_input : $user->user_email,
		'email_code'            => isset( $_POST['email_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['email_code'] ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'new_password'          => isset( $_POST['new_password'] ) ? (string) wp_unslash( $_POST['new_password'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'new_password_confirm'  => isset( $_POST['new_password_confirm'] ) ? (string) wp_unslash( $_POST['new_password_confirm'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'avatar_frame_enabled'  => ! empty( $_POST['avatar_frame_enabled'] ),
		'totp_enabled'          => ! empty( $_POST['totp_enabled'] ),
		'totp_code'             => isset( $_POST['totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['totp_code'] ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'tag_labels'            => isset( $_POST['comment_tag_label'] ) && is_array( $_POST['comment_tag_label'] ) ? wp_unslash( $_POST['comment_tag_label'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'tag_colors'            => isset( $_POST['comment_tag_color'] ) && is_array( $_POST['comment_tag_color'] ) ? wp_unslash( $_POST['comment_tag_color'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'tag_ids'               => isset( $_POST['comment_tag_id'] ) && is_array( $_POST['comment_tag_id'] ) ? wp_unslash( $_POST['comment_tag_id'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'tag_enabled_input'     => isset( $_POST['comment_tag_enabled'] ) && is_array( $_POST['comment_tag_enabled'] ) ? wp_unslash( $_POST['comment_tag_enabled'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'special_enabled_input' => isset( $_POST['comment_special_enabled'] ) && is_array( $_POST['comment_special_enabled'] ) ? wp_unslash( $_POST['comment_special_enabled'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	);
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	return $request;
}

function yneko_reimu_profile_save_validate_basics( $request ) {
	if ( '' === $request['display_name'] || mb_strlen( $request['display_name'] ) > 50 ) {
		return new WP_Error( 'yneko_reimu_profile_display_name', esc_html__( '请输入 1 到 50 个字符的昵称。', 'yneko-reimu' ) );
	}
	if ( '' === $request['new_email'] || ! is_email( $request['new_email'] ) ) {
		return new WP_Error( 'yneko_reimu_profile_email', esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) );
	}
	return true;
}

function yneko_reimu_profile_save_prepare_tags( $user_id, $request ) {
	$comment_tags          = array();
	$hidden_special_badges = array();
	$special_badges        = yneko_reimu_comment_user_special_badges( $user_id );

	foreach ( $special_badges as $special_badge ) {
		$type = sanitize_key( $special_badge['type'] ?? '' );
		if ( $type && empty( $request['special_enabled_input'][ $type ] ) ) {
			$hidden_special_badges[] = $type;
		}
	}

	$special_counts = max( 0, count( $special_badges ) - count( $hidden_special_badges ) );
	$display_limit  = yneko_reimu_comment_badge_display_limit();
	if ( $special_counts > $display_limit ) {
		return new WP_Error(
			'yneko_reimu_profile_tag_limit',
			esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
			array( 'field' => 'comment_tag_label' )
		);
	}

	$custom_capacity     = yneko_reimu_comment_badges_enabled() ? max( 0, $display_limit - $special_counts ) : 0;
	$enabled_custom_count = 0;
	if ( yneko_reimu_comment_badges_enabled() ) {
		foreach ( $request['tag_labels'] as $index => $raw_label ) {
			if ( count( $comment_tags ) >= yneko_reimu_comment_custom_tag_storage_limit() ) {
				break;
			}

			$raw_label = (string) $raw_label;
			$label     = yneko_reimu_sanitize_comment_tag_label( $raw_label );
			if ( '' === $label ) {
				continue;
			}
			if ( mb_strlen( trim( wp_strip_all_tags( $raw_label ) ) ) > 8 ) {
				return new WP_Error( 'yneko_reimu_profile_tag_length', esc_html__( '评论标签最多 8 个字符。', 'yneko-reimu' ) );
			}
			if ( yneko_reimu_comment_tag_label_is_reserved( $label ) ) {
				return new WP_Error(
					'yneko_reimu_profile_tag_reserved',
					esc_html__( '该评论标签为系统保留或屏蔽标签，请换一个。', 'yneko-reimu' ),
					array(
						'field' => 'comment_tag_label',
						'value' => $label,
						'index' => absint( $index ),
					)
				);
			}

			$color   = sanitize_hex_color( $request['tag_colors'][ $index ] ?? '' );
			$enabled = ! empty( $request['tag_enabled_input'][ $index ] ) && $enabled_custom_count < $custom_capacity;
			if ( ! empty( $request['tag_enabled_input'][ $index ] ) && ! $enabled ) {
				return new WP_Error(
					'yneko_reimu_profile_tag_limit',
					esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
					array(
						'field' => 'comment_tag_label',
						'index' => absint( $index ),
					)
				);
			}
			if ( $enabled ) {
				++$enabled_custom_count;
			}

			$comment_tags[] = array(
				'id'      => yneko_reimu_comment_tag_id( $request['tag_ids'][ $index ] ?? '' ),
				'label'   => $label,
				'color'   => $color ? $color : '#ff5252',
				'enabled' => $enabled ? '1' : '0',
			);
		}
	}

	return array(
		'comment_tags'          => $comment_tags,
		'hidden_special_badges' => $hidden_special_badges,
	);
}

function yneko_reimu_profile_save_apply_email( $update, $user_id, $current_email, $new_email, $email_code ) {
	if ( strtolower( $new_email ) === strtolower( $current_email ) ) {
		return $update;
	}

	if ( email_exists( $new_email ) ) {
		return new WP_Error( 'yneko_reimu_profile_email_exists', esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) );
	}

	$code_key  = yneko_reimu_auth_code_transient_key( 'profile_email', (string) $user_id, $new_email );
	$code_data = get_transient( $code_key );
	if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) || ! wp_check_password( $email_code, $code_data['code_hash'] ) ) {
		return new WP_Error( 'yneko_reimu_profile_email_code', esc_html__( '邮箱验证码不正确或已失效。', 'yneko-reimu' ) );
	}

	$update['user_email'] = $new_email;
	delete_transient( $code_key );
	return $update;
}

function yneko_reimu_profile_save_validate_password( $password, $confirm ) {
	if ( '' === $password && '' === $confirm ) {
		return true;
	}
	if ( $password !== $confirm ) {
		return new WP_Error( 'yneko_reimu_profile_password_match', esc_html__( '两次输入的密码不一致。', 'yneko-reimu' ) );
	}
	if ( strlen( $password ) < 8 ) {
		return new WP_Error( 'yneko_reimu_profile_password_length', esc_html__( '密码至少需要 8 个字符。', 'yneko-reimu' ) );
	}
	return true;
}

function yneko_reimu_profile_save_handle_avatar_file( $user_id, $request ) {
	$result = array(
		'avatar_url'     => $request['avatar_url'],
		'avatar_changed' => $request['avatar_changed'],
		'avatar_pending' => false,
	);

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- The public profile-save AJAX handler verifies the profile nonce before calling this internal upload helper.
	if ( isset( $_FILES['avatar_file'] ) && ! empty( $_FILES['avatar_file']['name'] ) ) {
		$result['avatar_changed'] = true;
		$avatar_result = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $avatar_result ) ) {
			return $avatar_result;
		}
		$result['avatar_url']     = $avatar_result['url'];
		$result['avatar_pending'] = ! empty( $avatar_result['pending'] );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	return $result;
}

function yneko_reimu_profile_save_apply_avatar( $user_id, $avatar_state ) {
	if ( empty( $avatar_state['avatar_changed'] ) ) {
		return;
	}

	if ( ! empty( $avatar_state['avatar_url'] ) && empty( $avatar_state['avatar_pending'] ) ) {
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $avatar_state['avatar_url'] );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	} elseif ( empty( $avatar_state['avatar_url'] ) && empty( $avatar_state['avatar_pending'] ) ) {
		delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
	}
}

function yneko_reimu_profile_save_apply_totp( $user_id, $enabled, $code ) {
	if ( $enabled ) {
		$current_secret = yneko_reimu_user_2fa_secret( $user_id );
		$pending_secret = (string) get_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', true );
		$secret         = $current_secret ? $current_secret : $pending_secret;
		if ( '' === $secret ) {
			return new WP_Error( 'yneko_reimu_profile_totp_missing', esc_html__( '请先生成认证器密钥。', 'yneko-reimu' ) );
		}
		if ( ! yneko_reimu_totp_verify( $secret, $code ) ) {
			return new WP_Error( 'yneko_reimu_profile_totp_code', esc_html__( '认证器验证码不正确。', 'yneko-reimu' ) );
		}
		update_user_meta( $user_id, '_yneko_reimu_totp_secret', $secret );
		update_user_meta( $user_id, '_yneko_reimu_totp_enabled', '1' );
		delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
		return true;
	}

	delete_user_meta( $user_id, '_yneko_reimu_totp_enabled' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	return true;
}

function yneko_reimu_profile_save_apply_comment_tags( $user_id, $comment_tags ) {
	$tags_pending = false;
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
		return $tags_pending;
	}

	delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
	delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
	yneko_reimu_clear_user_review_status( $user_id, 'tags' );
	return false;
}

function yneko_reimu_profile_save_message( $avatar_pending, $comment_tags ) {
	if ( $avatar_pending ) {
		return esc_html__( '个人资料已保存，头像审核中。', 'yneko-reimu' );
	}
	if ( yneko_reimu_comment_tag_review_enabled() && ! yneko_reimu_comment_user_can_bypass_tag_review( get_current_user_id() ) && $comment_tags ) {
		return esc_html__( '个人资料已保存，评论标签审核中。', 'yneko-reimu' );
	}
	return esc_html__( '个人资料已保存。', 'yneko-reimu' );
}
