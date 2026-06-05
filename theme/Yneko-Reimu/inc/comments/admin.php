<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_avatar_admin_action() {
	$action  = isset( $_GET['yneko_avatar_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_avatar_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id ) {
		return;
	}
	if ( ! in_array( $action, array( 'approve', 'reject', 'delete' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_avatar_' . $action . '_' . $user_id );

	$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
	$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );

	if ( 'approve' === $action && $pending ) {
		if ( $current && $current !== $pending ) {
			yneko_reimu_delete_upload_by_url( $current );
		}
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	} elseif ( 'reject' === $action && $pending ) {
		yneko_reimu_delete_upload_by_url( $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'rejected' );
	} elseif ( 'delete' === $action ) {
		yneko_reimu_delete_upload_by_url( $current );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
		yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_avatar_action', 'user_id', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_avatar_admin_action' );

function yneko_reimu_user_badge_admin_action() {
	$action  = isset( $_GET['yneko_user_badge_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_user_badge_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$index   = isset( $_GET['tag_index'] ) ? absint( $_GET['tag_index'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id || ! in_array( $action, array( 'approve', 'reject', 'revoke' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_user_badge_' . $action . '_' . $user_id . '_' . $index );

	$active  = yneko_reimu_comment_user_custom_tags( $user_id );
	$pending = yneko_reimu_comment_user_pending_tags( $user_id );

	if ( 'approve' === $action && isset( $pending[ $index ] ) ) {
		$approved_tag = $pending[ $index ];
		$replace_id   = sanitize_key( $approved_tag['old_id'] ?? ( $approved_tag['id'] ?? '' ) );
		$active = array_values(
			array_filter(
				$active,
				static function ( $tag ) use ( $approved_tag, $replace_id ) {
					if ( ! is_array( $tag ) ) {
						return false;
					}
					$tag_id = sanitize_key( $tag['id'] ?? '' );
					if ( $replace_id && $tag_id === $replace_id ) {
						return false;
					}
					return ! yneko_reimu_comment_tags_same_label( $tag['label'] ?? '', $approved_tag['label'] ?? '' );
				}
			)
		);
		unset( $approved_tag['old_id'], $approved_tag['old_label'] );
		$active[] = $approved_tag;
		$active   = yneko_reimu_comment_normalize_tag_list( $active, yneko_reimu_comment_custom_tag_storage_limit() );
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'updated' );
	} elseif ( 'reject' === $action && isset( $pending[ $index ] ) ) {
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'rejected' );
	} elseif ( 'revoke' === $action && isset( $active[ $index ] ) ) {
		unset( $active[ $index ] );
		$active = array_values( $active );
		yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
	}

	if ( $active ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags', $active );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
	}
	if ( $pending ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $pending );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_user_badge_action', 'user_id', 'tag_index', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_user_badge_admin_action' );
