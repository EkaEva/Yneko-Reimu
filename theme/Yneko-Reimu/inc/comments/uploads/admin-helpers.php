<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_upload_admin_referer_url() {
	return wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' );
}

function yneko_reimu_comment_gif_upload_redirect( $status, $count = 0 ) {
	$args = array(
		'yneko_comment_gif_upload' => $status,
	);
	if ( $count ) {
		$args['yneko_comment_gif_count'] = $count;
	}

	wp_safe_redirect( add_query_arg( $args, yneko_reimu_comment_upload_admin_referer_url() ) );
	exit;
}

function yneko_reimu_comment_gif_upload_source() {
	// Nonce and capability are checked by yneko_reimu_admin_comment_gif_upload_action().
	if ( empty( $_FILES['yneko_reimu_comment_gif'] ) || ! is_array( $_FILES['yneko_reimu_comment_gif'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		yneko_reimu_comment_gif_upload_redirect( 'empty' );
	}

	return $_FILES['yneko_reimu_comment_gif']; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
}

function yneko_reimu_comment_gif_upload_prepare_media() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
}

function yneko_reimu_comment_gif_upload_handle_file( $file ) {
	if ( UPLOAD_ERR_NO_FILE === absint( $file['error'] ) ) {
		return 'empty';
	}

	$validation = yneko_reimu_comment_upload_validate_file( $file, 'gif', array( 'gif' => 'image/gif' ) );
	if ( is_wp_error( $validation ) ) {
		return 'failed';
	}

	$_FILES['yneko_reimu_comment_gif_single'] = $file;
	$attachment_id                           = media_handle_upload(
		'yneko_reimu_comment_gif_single',
		0,
		array(
			'post_title' => sprintf(
				/* translators: %s: original file name. */
				__( '评论 GIF：%s', 'yneko-reimu' ),
				sanitize_file_name( $file['name'] ?? 'gif' )
			),
		),
		array(
			'test_form' => false,
			'mimes'     => array( 'gif' => 'image/gif' ),
		)
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 'failed';
	}

	yneko_reimu_comment_upload_mark_admin_gif( $attachment_id );
	return 'done';
}

function yneko_reimu_comment_upload_mark_admin_gif( $attachment_id ) {
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', 'gif' );
	yneko_reimu_comment_set_upload_status( $attachment_id, 'approved' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', get_current_user_id() );
}

function yneko_reimu_comment_gif_upload_result( $source ) {
	$names  = is_array( $source['name'] ?? null ) ? $source['name'] : array( $source['name'] ?? '' );
	$result = array(
		'total'  => 0,
		'done'   => 0,
		'failed' => 0,
	);

	add_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );
	foreach ( array_keys( $names ) as $index ) {
		$file   = yneko_reimu_comment_upload_file_from_request( $source, $index );
		$status = yneko_reimu_comment_gif_upload_handle_file( $file );
		if ( 'empty' === $status ) {
			continue;
		}

		++$result['total'];
		if ( 'done' === $status ) {
			++$result['done'];
		} else {
			++$result['failed'];
		}
	}
	unset( $_FILES['yneko_reimu_comment_gif_single'] );
	remove_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );

	return $result;
}

function yneko_reimu_comment_gif_upload_finish( $result ) {
	if ( 0 === $result['total'] ) {
		yneko_reimu_comment_gif_upload_redirect( 'empty' );
	}

	if ( 0 === $result['done'] ) {
		yneko_reimu_comment_gif_upload_redirect( $result['failed'] ? 'invalid' : 'failed' );
	}

	yneko_reimu_comment_gif_upload_redirect( $result['failed'] ? 'partial' : 'success', $result['done'] );
}

function yneko_reimu_comment_upload_admin_normalize_action( $action ) {
	if ( 'approve_temp' === $action ) {
		return 'approve';
	}
	if ( 'delete_temp' === $action ) {
		return 'delete';
	}
	if ( 'remove' === $action ) {
		return 'revoke';
	}

	return $action;
}

function yneko_reimu_comment_upload_admin_request_context() {
	$raw_action = isset( $_GET['yneko_comment_upload_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_upload_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$action     = yneko_reimu_comment_upload_admin_normalize_action( $raw_action );

	return array(
		'raw_action' => $raw_action,
		'action'     => $action,
		'id'         => isset( $_GET['attachment_id'] ) ? absint( $_GET['attachment_id'] ) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'temp'       => isset( $_GET['temp_upload'] ) ? rawurldecode( sanitize_text_field( wp_unslash( $_GET['temp_upload'] ) ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	);
}

function yneko_reimu_comment_upload_admin_action_is_valid( $context ) {
	if ( ! $context['action'] || ( ! $context['id'] && ! $context['temp'] ) ) {
		return false;
	}

	return in_array( $context['action'], array( 'approve', 'reject', 'revoke', 'delete' ), true );
}

function yneko_reimu_comment_upload_admin_temp_context( $temp ) {
	$relative = str_replace( array( '../', '..\\' ), '', $temp );
	$base     = yneko_reimu_comment_temp_upload_base();
	$path     = wp_normalize_path( trailingslashit( $base['path'] ) . $relative );
	$root     = wp_normalize_path( $base['path'] );
	if ( 0 !== strpos( $path, $root ) || ! is_file( $path ) ) {
		wp_die( esc_html__( '无效的评论上传附件。', 'yneko-reimu' ), 400 );
	}

	$url = trailingslashit( $base['url'] ) . str_replace( DIRECTORY_SEPARATOR, '/', $relative );
	return array(
		'path'    => $path,
		'url'     => $url,
		'comment' => yneko_reimu_comment_find_comment_by_temp_url( $url ),
	);
}

function yneko_reimu_comment_upload_admin_delete_temp( $context ) {
	wp_delete_file( $context['path'] );
	if ( ! $context['comment'] ) {
		return;
	}

	yneko_reimu_comment_remove_upload_from_comment( $context['comment']->comment_ID, $context['url'] );
	if ( ! empty( $context['comment']->user_id ) ) {
		yneko_reimu_set_user_review_status( $context['comment']->user_id, 'comments', 'rejected', $context['comment']->comment_ID );
	}
}

function yneko_reimu_comment_upload_admin_set_comment_status( $comment_id, $status ) {
	if ( ! $comment_id ) {
		return;
	}

	$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
	wp_set_comment_status( $comment_id, $status );
	unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
}

function yneko_reimu_comment_upload_admin_delete_attachment_from_comment( $attachment_id, $comment_id ) {
	$attachment_url = wp_get_attachment_url( $attachment_id );
	if ( $comment_id && $attachment_url ) {
		yneko_reimu_comment_remove_upload_from_comment( $comment_id, $attachment_url );
		return;
	}

	if ( ! $attachment_url ) {
		return;
	}

	$comment = yneko_reimu_comment_find_comment_by_temp_url( $attachment_url );
	if ( $comment ) {
		yneko_reimu_comment_remove_upload_from_comment( $comment->comment_ID, $attachment_url );
	}
}
