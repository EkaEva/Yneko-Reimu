<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_admin_comment_gif_upload_action() {
	if ( empty( $_POST['yneko_reimu_admin_comment_gif_upload'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	check_admin_referer( 'yneko_reimu_admin_comment_gif_upload' );
	yneko_reimu_comment_gif_upload_prepare_media();
	yneko_reimu_comment_gif_upload_finish( yneko_reimu_comment_gif_upload_result( yneko_reimu_comment_gif_upload_source() ) );
}
add_action( 'admin_init', 'yneko_reimu_admin_comment_gif_upload_action' );

function yneko_reimu_admin_add_gif_from_media() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( '权限不足。', 'yneko-reimu' ) ), 403 );
	}

	check_ajax_referer( 'yneko_reimu_admin_add_gif_media', 'nonce' );
	$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
	if ( ! $attachment_id || 'image/gif' !== get_post_mime_type( $attachment_id ) ) {
		wp_send_json_error( array( 'message' => __( '请选择 GIF 文件。', 'yneko-reimu' ) ), 400 );
	}

	yneko_reimu_comment_upload_mark_admin_gif( $attachment_id );

	wp_send_json_success( array( 'message' => __( 'GIF 已加入表情库。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_yneko_reimu_admin_add_gif_media', 'yneko_reimu_admin_add_gif_from_media' );

function yneko_reimu_hide_comment_uploads_from_media_library( $query ) {
	if ( ! is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return;
	}

	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'upload' !== $screen->base ) {
		return;
	}

	if ( ! empty( $_GET['yneko_comment_uploads'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query->set(
			'meta_query',
			array(
				array(
					'key'   => '_yneko_reimu_comment_upload',
					'value' => '1',
				),
			)
		);
		return;
	}

	$meta_query   = (array) $query->get( 'meta_query' );
	$meta_query[] = array(
		'key'     => '_yneko_reimu_comment_upload',
		'compare' => 'NOT EXISTS',
	);
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'yneko_reimu_hide_comment_uploads_from_media_library' );

function yneko_reimu_comment_upload_admin_action() {
	$context = yneko_reimu_comment_upload_admin_request_context();
	if ( ! yneko_reimu_comment_upload_admin_action_is_valid( $context ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	if ( $context['temp'] ) {
		yneko_reimu_comment_upload_admin_temp_action( $context['action'], $context['raw_action'], $context['temp'] );
		return;
	}

	yneko_reimu_comment_upload_admin_attachment_action( $context['action'], $context['id'] );
}
add_action( 'admin_init', 'yneko_reimu_comment_upload_admin_action' );

function yneko_reimu_comment_upload_admin_temp_action( $action, $raw_action, $temp ) {
	$nonce_action = yneko_reimu_comment_temp_upload_nonce_action( $action, $temp );
	if ( in_array( $raw_action, array( 'approve_temp', 'delete_temp' ), true ) ) {
		$nonce_action = yneko_reimu_comment_temp_upload_nonce_action( $raw_action, $temp );
	}
	check_admin_referer( $nonce_action );

	$context = yneko_reimu_comment_upload_admin_temp_context( $temp );
	if ( 'approve' === $action ) {
		yneko_reimu_comment_upload_admin_approve_temp( $context['url'], $context['comment'] );
	} elseif ( 'reject' === $action ) {
		yneko_reimu_comment_upload_admin_reject_temp( $context['url'], $context['comment'] );
	} elseif ( 'delete' === $action ) {
		yneko_reimu_comment_upload_admin_delete_temp( $context );
	}

	yneko_reimu_comment_upload_admin_redirect();
}

function yneko_reimu_comment_upload_admin_approve_temp( $url, $comment ) {
	$promoted = yneko_reimu_comment_promote_upload_url( $url, $comment ? $comment->comment_ID : 0, 'approved' );
	if ( ! $promoted || ! $comment ) {
		return;
	}

	wp_update_comment(
		array(
			'comment_ID'      => absint( $comment->comment_ID ),
			'comment_content' => str_replace( $url, $promoted, $comment->comment_content ),
		)
	);
	delete_comment_meta( $comment->comment_ID, '_yneko_reimu_comment_temp_upload_reviews' );
	if ( '0' === (string) $comment->comment_approved ) {
		yneko_reimu_comment_upload_admin_set_comment_status( $comment->comment_ID, 'approve' );
		if ( ! empty( $comment->user_id ) ) {
			yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'updated', $comment->comment_ID );
		}
	} elseif ( ! empty( $comment->user_id ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'updated', $comment->comment_ID );
	}
}

function yneko_reimu_comment_upload_admin_reject_temp( $url, $comment ) {
	if ( ! $comment ) {
		return;
	}

	yneko_reimu_comment_set_temp_review_status( $comment->comment_ID, $url, 'rejected' );
	if ( '1' === (string) $comment->comment_approved ) {
		yneko_reimu_comment_upload_admin_set_comment_status( $comment->comment_ID, 'hold' );
	}
	if ( ! empty( $comment->user_id ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'rejected', $comment->comment_ID );
	}
}

function yneko_reimu_comment_upload_admin_attachment_action( $action, $id ) {
	check_admin_referer( 'yneko_reimu_comment_upload_' . $action . '_' . $id );

	if ( '1' !== get_post_meta( $id, '_yneko_reimu_comment_upload', true ) ) {
		wp_die( esc_html__( '无效的评论上传附件。', 'yneko-reimu' ), 400 );
	}

	$upload_user_id = absint( get_post_meta( $id, '_yneko_reimu_comment_upload_user_id', true ) );
	$comment_id     = absint( get_post_meta( $id, '_yneko_reimu_comment_upload_comment_id', true ) );
	if ( 'approve' === $action ) {
		yneko_reimu_comment_set_upload_status( $id, 'approved' );
		yneko_reimu_comment_upload_admin_set_comment_status( $comment_id, 'approve' );
	} elseif ( 'revoke' === $action ) {
		yneko_reimu_comment_set_upload_status( $id, 'revoked' );
		yneko_reimu_comment_upload_admin_set_comment_status( $comment_id, 'hold' );
		if ( $upload_user_id ) {
			yneko_reimu_set_user_review_status( $upload_user_id, 'comments', 'pending', $comment_id );
		}
	} elseif ( 'reject' === $action ) {
		yneko_reimu_comment_set_upload_status( $id, 'rejected' );
		yneko_reimu_comment_upload_admin_set_comment_status( $comment_id, 'hold' );
	} elseif ( 'delete' === $action ) {
		yneko_reimu_comment_upload_admin_delete_attachment_from_comment( $id, $comment_id );
		wp_delete_attachment( $id, true );
	}
	if ( $upload_user_id && in_array( $action, array( 'approve', 'reject', 'delete' ), true ) ) {
		yneko_reimu_set_user_review_status( $upload_user_id, 'comments', 'approve' === $action ? 'updated' : 'rejected', $comment_id );
	}

	yneko_reimu_comment_upload_admin_redirect();
}

function yneko_reimu_comment_upload_admin_redirect() {
	wp_safe_redirect( remove_query_arg( array( 'yneko_comment_upload_action', 'attachment_id', 'temp_upload', '_wpnonce' ) ) );
	exit;
}
