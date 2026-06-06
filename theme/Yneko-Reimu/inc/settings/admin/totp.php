<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_admin_current_user_totp_payload() {
	$user_id = get_current_user_id();
	return array(
		'enabled'       => $user_id && function_exists( 'yneko_reimu_user_2fa_enabled' ) ? yneko_reimu_user_2fa_enabled( $user_id ) : false,
		'nonce'         => wp_create_nonce( 'yneko_reimu_admin_totp' ),
		'recoveryCount' => $user_id && function_exists( 'yneko_reimu_login_2fa_recovery_code_count' ) ? yneko_reimu_login_2fa_recovery_code_count( $user_id ) : 0,
	);
}

function yneko_reimu_admin_totp_verify_request() {
	check_ajax_referer( 'yneko_reimu_admin_totp', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) || ! get_current_user_id() ) {
		wp_send_json_error( array( 'message' => esc_html__( '权限不足。', 'yneko-reimu' ) ), 403 );
	}
	if ( ! function_exists( 'yneko_reimu_totp_generate_secret' ) || ! function_exists( 'yneko_reimu_totp_verify' ) || ! function_exists( 'yneko_reimu_totp_uri' ) || ! function_exists( 'yneko_reimu_user_2fa_secret' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '二次认证模块尚未加载。', 'yneko-reimu' ) ), 500 );
	}
}

function yneko_reimu_ajax_admin_totp_generate() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	$secret  = yneko_reimu_totp_generate_secret();
	update_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', $secret );

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'secret'  => $secret,
				'uri'     => yneko_reimu_totp_uri( $user_id, $secret ),
				'message' => esc_html__( '请用认证器扫码，并输入 6 位验证码后启用。', 'yneko-reimu' ),
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_generate', 'yneko_reimu_ajax_admin_totp_generate' );

function yneko_reimu_ajax_admin_totp_enable() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	$code    = isset( $_POST['totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is checked by yneko_reimu_admin_totp_verify_request(); regex keeps only digits.
	$secret  = (string) get_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', true );
	if ( '' === $secret ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先生成认证器密钥。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! yneko_reimu_totp_verify( $secret, $code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '认证器验证码不正确。', 'yneko-reimu' ) ), 400 );
	}

	update_user_meta( $user_id, '_yneko_reimu_totp_secret', $secret );
	update_user_meta( $user_id, '_yneko_reimu_totp_enabled', '1' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );

	$recovery_codes = array();
	if ( function_exists( 'yneko_reimu_login_2fa_generate_recovery_codes' ) && function_exists( 'yneko_reimu_login_2fa_store_recovery_codes' ) ) {
		$recovery_codes = yneko_reimu_login_2fa_generate_recovery_codes();
		yneko_reimu_login_2fa_store_recovery_codes( $user_id, $recovery_codes );
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'message'       => esc_html__( '二次认证已开启。请立即保存这些一次性恢复码，它们只会显示这一次。', 'yneko-reimu' ),
				'recoveryCodes' => $recovery_codes,
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_enable', 'yneko_reimu_ajax_admin_totp_enable' );

function yneko_reimu_ajax_admin_totp_recovery_generate() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	if ( ! function_exists( 'yneko_reimu_user_2fa_enabled' ) || ! yneko_reimu_user_2fa_enabled( $user_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先开启二次认证。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! function_exists( 'yneko_reimu_login_2fa_generate_recovery_codes' ) || ! function_exists( 'yneko_reimu_login_2fa_store_recovery_codes' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '恢复码模块尚未加载。', 'yneko-reimu' ) ), 500 );
	}

	$recovery_codes = yneko_reimu_login_2fa_generate_recovery_codes();
	yneko_reimu_login_2fa_store_recovery_codes( $user_id, $recovery_codes );

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'message'       => esc_html__( '新的恢复码已生成，旧恢复码已失效。请立即保存这些恢复码。', 'yneko-reimu' ),
				'recoveryCodes' => $recovery_codes,
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_recovery_generate', 'yneko_reimu_ajax_admin_totp_recovery_generate' );

function yneko_reimu_ajax_admin_totp_disable() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	delete_user_meta( $user_id, '_yneko_reimu_totp_enabled' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_secret' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	if ( function_exists( 'yneko_reimu_login_2fa_clear_recovery_codes' ) ) {
		yneko_reimu_login_2fa_clear_recovery_codes( $user_id );
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array( 'message' => esc_html__( '二次认证已关闭。', 'yneko-reimu' ) )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_disable', 'yneko_reimu_ajax_admin_totp_disable' );
