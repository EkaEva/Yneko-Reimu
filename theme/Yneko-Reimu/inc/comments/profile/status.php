<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
