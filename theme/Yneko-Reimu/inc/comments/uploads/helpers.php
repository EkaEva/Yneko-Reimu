<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_upload_cleanup_transient_key( $url ) {
	return 'yneko_reimu_comment_upload_cleanup_' . md5( esc_url_raw( $url ) );
}

function yneko_reimu_comment_upload_store_cleanup_token( $url, $cleanup_key, $user_id ) {
	set_transient(
		yneko_reimu_comment_upload_cleanup_transient_key( $url ),
		array(
			'key'  => (string) $cleanup_key,
			'user' => absint( $user_id ),
		),
		DAY_IN_SECONDS
	);
}

function yneko_reimu_comment_upload_file_from_request( $source, $index = null ) {
	if ( null === $index ) {
		return is_array( $source ) ? $source : array();
	}

	return array(
		'name'     => is_array( $source['name'] ?? null ) ? ( $source['name'][ $index ] ?? '' ) : ( $source['name'] ?? '' ),
		'type'     => is_array( $source['type'] ?? null ) ? ( $source['type'][ $index ] ?? '' ) : ( $source['type'] ?? '' ),
		'tmp_name' => is_array( $source['tmp_name'] ?? null ) ? ( $source['tmp_name'][ $index ] ?? '' ) : ( $source['tmp_name'] ?? '' ),
		'error'    => is_array( $source['error'] ?? null ) ? ( $source['error'][ $index ] ?? UPLOAD_ERR_NO_FILE ) : ( $source['error'] ?? UPLOAD_ERR_NO_FILE ),
		'size'     => is_array( $source['size'] ?? null ) ? ( $source['size'][ $index ] ?? 0 ) : ( $source['size'] ?? 0 ),
	);
}

function yneko_reimu_comment_upload_validate_file( $file, $type, $mimes = null ) {
	$type   = 'gif' === $type ? 'gif' : 'image';
	$mimes  = is_array( $mimes ) ? $mimes : yneko_reimu_comment_upload_allowed_mimes();
	$limits = yneko_reimu_comment_upload_limits();

	if ( ! is_array( $file ) || UPLOAD_ERR_OK !== absint( $file['error'] ?? UPLOAD_ERR_OK ) ) {
		return new WP_Error( 'yneko_reimu_comment_upload_file_error', __( '上传失败。', 'yneko-reimu' ) );
	}

	$size = absint( $file['size'] ?? 0 );
	if ( $size <= 0 || $size > $limits[ $type ] ) {
		return new WP_Error( 'yneko_reimu_comment_upload_size', __( '文件大小超出限制。', 'yneko-reimu' ) );
	}

	$check = wp_check_filetype_and_ext( $file['tmp_name'] ?? '', $file['name'] ?? '', $mimes );
	$mime  = $check['type'] ?? '';
	if ( ! $mime || ! in_array( $mime, $mimes, true ) ) {
		return new WP_Error( 'yneko_reimu_comment_upload_mime', __( '仅支持 JPG、PNG、WebP 和 GIF。', 'yneko-reimu' ) );
	}

	if ( 'gif' === $type && 'image/gif' !== $mime ) {
		return new WP_Error( 'yneko_reimu_comment_upload_not_gif', __( '请选择 GIF 文件。', 'yneko-reimu' ) );
	}

	if ( 'image' === $type && 'image/gif' === $mime ) {
		return new WP_Error( 'yneko_reimu_comment_upload_gif_as_image', __( '图片上传不支持 GIF，请使用 GIF 按钮。', 'yneko-reimu' ) );
	}

	return array(
		'mime' => $mime,
		'type' => 'image/gif' === $mime ? 'gif' : 'image',
	);
}

function yneko_reimu_comment_upload_register_attachment( $file_path, $mime, $type, $cleanup_key, $user_id, $status = 'approved', $comment_id = 0 ) {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attachment = array(
		'post_mime_type' => $mime,
		'post_title'     => sanitize_file_name( wp_basename( $file_path ) ),
		'post_status'    => 'inherit',
	);
	$attachment_id = wp_insert_attachment( $attachment, $file_path );
	if ( ! $attachment_id ) {
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file_path ) );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', 'gif' === $type ? 'gif' : 'image' );
	yneko_reimu_comment_set_upload_status( $attachment_id, $status );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', absint( $user_id ) );
	if ( $cleanup_key ) {
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_cleanup_key', (string) $cleanup_key );
	}
	if ( $comment_id ) {
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_comment_id', absint( $comment_id ) );
	}

	return absint( $attachment_id );
}
