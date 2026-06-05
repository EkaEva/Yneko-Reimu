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

	if ( empty( $_FILES['yneko_reimu_comment_gif'] ) || ! is_array( $_FILES['yneko_reimu_comment_gif'] ) ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', 'empty', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$source = $_FILES['yneko_reimu_comment_gif']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$names  = is_array( $source['name'] ?? null ) ? $source['name'] : array( $source['name'] ?? '' );
	$total  = 0;
	$done   = 0;
	$failed = 0;

	add_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );
	foreach ( array_keys( $names ) as $index ) {
		$file = yneko_reimu_comment_upload_file_from_request( $source, $index );

		if ( UPLOAD_ERR_NO_FILE === absint( $file['error'] ) ) {
			continue;
		}

		++$total;
		$validation = yneko_reimu_comment_upload_validate_file( $file, 'gif', array( 'gif' => 'image/gif' ) );
		if ( is_wp_error( $validation ) ) {
			++$failed;
			continue;
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
			++$failed;
			continue;
		}

		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', 'gif' );
		yneko_reimu_comment_set_upload_status( $attachment_id, 'approved' );
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', get_current_user_id() );
		++$done;
	}
	unset( $_FILES['yneko_reimu_comment_gif_single'] );
	remove_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );

	if ( 0 === $total ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', 'empty', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	if ( 0 === $done ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', $failed ? 'invalid' : 'failed', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	$status = $failed ? 'partial' : 'success';
	wp_safe_redirect(
		add_query_arg(
			array(
				'yneko_comment_gif_upload' => $status,
				'yneko_comment_gif_count'  => $done,
			),
			wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' )
		)
	);
	exit;
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

	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', 'gif' );
	yneko_reimu_comment_set_upload_status( $attachment_id, 'approved' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', get_current_user_id() );

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
	$action     = isset( $_GET['yneko_comment_upload_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_upload_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$raw_action = $action;
	$id         = isset( $_GET['attachment_id'] ) ? absint( $_GET['attachment_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$temp       = isset( $_GET['temp_upload'] ) ? rawurldecode( sanitize_text_field( wp_unslash( $_GET['temp_upload'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ( ! $id && ! $temp ) ) {
		return;
	}

	if ( 'approve_temp' === $action ) {
		$action = 'approve';
	} elseif ( 'delete_temp' === $action ) {
		$action = 'delete';
	} elseif ( 'remove' === $action ) {
		$action = 'revoke';
	}

	if ( ! in_array( $action, array( 'approve', 'reject', 'revoke', 'delete' ), true ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	if ( $temp ) {
		yneko_reimu_comment_upload_admin_temp_action( $action, $raw_action, $temp );
		return;
	}

	yneko_reimu_comment_upload_admin_attachment_action( $action, $id );
}
add_action( 'admin_init', 'yneko_reimu_comment_upload_admin_action' );

function yneko_reimu_comment_upload_admin_temp_action( $action, $raw_action, $temp ) {
	$nonce_action = yneko_reimu_comment_temp_upload_nonce_action( $action, $temp );
	if ( in_array( $raw_action, array( 'approve_temp', 'delete_temp' ), true ) ) {
		$nonce_action = yneko_reimu_comment_temp_upload_nonce_action( $raw_action, $temp );
	}
	check_admin_referer( $nonce_action );

	$relative = str_replace( array( '../', '..\\' ), '', $temp );
	$base     = yneko_reimu_comment_temp_upload_base();
	$path     = wp_normalize_path( trailingslashit( $base['path'] ) . $relative );
	$root     = wp_normalize_path( $base['path'] );
	if ( 0 !== strpos( $path, $root ) || ! is_file( $path ) ) {
		wp_die( esc_html__( '无效的评论上传附件。', 'yneko-reimu' ), 400 );
	}

	$url     = trailingslashit( $base['url'] ) . str_replace( DIRECTORY_SEPARATOR, '/', $relative );
	$comment = yneko_reimu_comment_find_comment_by_temp_url( $url );
	if ( 'approve' === $action ) {
		yneko_reimu_comment_upload_admin_approve_temp( $url, $comment );
	} elseif ( 'reject' === $action ) {
		yneko_reimu_comment_upload_admin_reject_temp( $url, $comment );
	} elseif ( 'delete' === $action ) {
		wp_delete_file( $path );
		if ( $comment ) {
			yneko_reimu_comment_remove_upload_from_comment( $comment->comment_ID, $url );
			if ( ! empty( $comment->user_id ) ) {
				yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'rejected', $comment->comment_ID );
			}
		}
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
		$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
		wp_set_comment_status( $comment->comment_ID, 'approve' );
		unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
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
		$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
		wp_set_comment_status( $comment->comment_ID, 'hold' );
		unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
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
		if ( $comment_id ) {
			$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
			wp_set_comment_status( $comment_id, 'approve' );
			unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
		}
	} elseif ( 'revoke' === $action ) {
		yneko_reimu_comment_set_upload_status( $id, 'revoked' );
		if ( $comment_id ) {
			$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
			wp_set_comment_status( $comment_id, 'hold' );
			unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
		}
		if ( $upload_user_id ) {
			yneko_reimu_set_user_review_status( $upload_user_id, 'comments', 'pending', $comment_id );
		}
	} elseif ( 'reject' === $action ) {
		yneko_reimu_comment_set_upload_status( $id, 'rejected' );
		if ( $comment_id ) {
			$GLOBALS['yneko_reimu_suppress_comment_review_status'] = true;
			wp_set_comment_status( $comment_id, 'hold' );
			unset( $GLOBALS['yneko_reimu_suppress_comment_review_status'] );
		}
	} elseif ( 'delete' === $action ) {
		$attachment_url = wp_get_attachment_url( $id );
		if ( $comment_id && $attachment_url ) {
			yneko_reimu_comment_remove_upload_from_comment( $comment_id, $attachment_url );
		} elseif ( $attachment_url ) {
			$comment = yneko_reimu_comment_find_comment_by_temp_url( $attachment_url );
			if ( $comment ) {
				yneko_reimu_comment_remove_upload_from_comment( $comment->comment_ID, $attachment_url );
			}
		}
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
