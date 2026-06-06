<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	yneko_reimu_auth_security_apply_admin_action( $action );

	wp_safe_redirect( admin_url( 'themes.php?page=yneko-reimu-settings#security' ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_auth_security_admin_action' );

function yneko_reimu_auth_security_apply_admin_action( $action ) {
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
}
