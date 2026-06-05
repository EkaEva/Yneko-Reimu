<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_comment_upload() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( '登录后可上传图片。', 'yneko-reimu' ) ), 403 );
	}

	if ( ! yneko_reimu_comment_upload_enabled() ) {
		wp_send_json_error( array( 'message' => __( '评论图片上传已关闭。', 'yneko-reimu' ) ), 403 );
	}

	check_ajax_referer( 'yneko_reimu_comment_upload', 'nonce' );

	$type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'image';
	$type = 'gif' === $type ? 'gif' : 'image';
	if ( ! yneko_reimu_comment_upload_type_enabled( $type ) ) {
		wp_send_json_error( array( 'message' => 'gif' === $type ? __( '评论 GIF 上传已关闭。', 'yneko-reimu' ) : __( '评论图片上传已关闭。', 'yneko-reimu' ) ), 403 );
	}

	if ( empty( $_FILES['file'] ) || ! is_array( $_FILES['file'] ) ) {
		wp_send_json_error( array( 'message' => __( '请选择要上传的文件。', 'yneko-reimu' ) ), 400 );
	}

	$file = $_FILES['file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$validation = yneko_reimu_comment_upload_validate_file( $file, $type );
	if ( is_wp_error( $validation ) ) {
		wp_send_json_error( array( 'message' => $validation->get_error_message() ), 400 );
	}
	$mime = $validation['mime'];

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$needs_review = yneko_reimu_comment_upload_review_enabled( $type );
	$GLOBALS['yneko_reimu_comment_upload_type'] = $type;
	add_filter( 'upload_dir', $needs_review ? 'yneko_reimu_comment_temp_upload_dir' : 'yneko_reimu_comment_upload_dir' );
	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => yneko_reimu_comment_upload_allowed_mimes(),
		)
	);
	remove_filter( 'upload_dir', $needs_review ? 'yneko_reimu_comment_temp_upload_dir' : 'yneko_reimu_comment_upload_dir' );
	unset( $GLOBALS['yneko_reimu_comment_upload_type'] );

	if ( empty( $upload['url'] ) || ! empty( $upload['error'] ) ) {
		wp_send_json_error( array( 'message' => ! empty( $upload['error'] ) ? $upload['error'] : __( '上传失败。', 'yneko-reimu' ) ), 400 );
	}

	$is_gif = 'image/gif' === $mime;
	$cleanup_key   = wp_generate_password( 24, false, false );
	$attachment_id = 0;
	if ( ! $needs_review ) {
		$attachment_id = yneko_reimu_comment_upload_register_attachment( $upload['file'], $mime, $is_gif ? 'gif' : 'image', $cleanup_key, get_current_user_id() );
	}

	if ( $needs_review ) {
		yneko_reimu_comment_upload_store_cleanup_token( $upload['url'], $cleanup_key, get_current_user_id() );
	}

	wp_send_json_success(
		array(
			'url'     => esc_url_raw( $upload['url'] ),
			'type'    => $is_gif ? 'gif' : 'image',
			'status'  => $needs_review ? 'pending' : 'approved',
			'requiresReview' => $needs_review,
			'cleanupKey' => $cleanup_key,
			'message' => $needs_review ? __( '文件已上传，等待管理员审核。', 'yneko-reimu' ) : __( '已插入评论。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_upload', 'yneko_reimu_ajax_comment_upload' );

function yneko_reimu_ajax_comment_upload_discard() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( '登录后可上传图片。', 'yneko-reimu' ) ), 403 );
	}

	check_ajax_referer( 'yneko_reimu_comment_upload', 'nonce' );

	$url         = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
	$cleanup_key = isset( $_POST['cleanup_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cleanup_key'] ) ) : '';
	if ( ! $url || ! $cleanup_key ) {
		wp_send_json_error( array( 'message' => __( '无效的评论上传附件。', 'yneko-reimu' ) ), 400 );
	}

	if ( yneko_reimu_comment_find_comment_by_temp_url( $url ) ) {
		wp_send_json_error( array( 'message' => __( '文件已被评论使用。', 'yneko-reimu' ) ), 409 );
	}

	$attachment_id = attachment_url_to_postid( $url );
	if ( $attachment_id && '1' === get_post_meta( $attachment_id, '_yneko_reimu_comment_upload', true ) ) {
		if ( absint( get_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', true ) ) !== get_current_user_id() ) {
			wp_send_json_error( array( 'message' => __( '权限不足。', 'yneko-reimu' ) ), 403 );
		}
		if ( $cleanup_key !== (string) get_post_meta( $attachment_id, '_yneko_reimu_comment_upload_cleanup_key', true ) ) {
			wp_send_json_error( array( 'message' => __( '无效的评论上传附件。', 'yneko-reimu' ) ), 400 );
		}
		if ( absint( get_post_meta( $attachment_id, '_yneko_reimu_comment_upload_comment_id', true ) ) ) {
			wp_send_json_error( array( 'message' => __( '文件已被评论使用。', 'yneko-reimu' ) ), 409 );
		}
		wp_delete_attachment( $attachment_id, true );
		wp_send_json_success();
	}

	$temp_base = yneko_reimu_comment_temp_upload_base();
	if ( 0 !== strpos( $url, trailingslashit( $temp_base['url'] ) ) ) {
		wp_send_json_error( array( 'message' => __( '无效的评论上传附件。', 'yneko-reimu' ) ), 400 );
	}

	$cleanup = get_transient( yneko_reimu_comment_upload_cleanup_transient_key( $url ) );
	if ( ! is_array( $cleanup ) || $cleanup_key !== (string) ( $cleanup['key'] ?? '' ) || absint( $cleanup['user'] ?? 0 ) !== get_current_user_id() ) {
		wp_send_json_error( array( 'message' => __( '无效的评论上传附件。', 'yneko-reimu' ) ), 400 );
	}

	$path = yneko_reimu_comment_url_to_path( $url );
	if ( $path && file_exists( $path ) ) {
		wp_delete_file( $path );
	}
	delete_transient( yneko_reimu_comment_upload_cleanup_transient_key( $url ) );
	wp_send_json_success();
}
add_action( 'wp_ajax_yneko_reimu_comment_upload_discard', 'yneko_reimu_ajax_comment_upload_discard' );
