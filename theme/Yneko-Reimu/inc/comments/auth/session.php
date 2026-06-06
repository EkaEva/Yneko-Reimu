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
