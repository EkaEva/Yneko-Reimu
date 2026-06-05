<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_promote_upload_url( $url, $comment_id, $status = 'pending' ) {
	$temp_base = yneko_reimu_comment_temp_upload_base();
	if ( 0 !== strpos( $url, trailingslashit( $temp_base['url'] ) ) ) {
		return $url;
	}

	$source_path = yneko_reimu_comment_url_to_path( $url );
	if ( ! $source_path || ! file_exists( $source_path ) ) {
		return '';
	}

	$check = wp_check_filetype_and_ext( $source_path, wp_basename( $source_path ), yneko_reimu_comment_upload_allowed_mimes() );
	$mime  = $check['type'] ?? '';
	if ( ! $mime || ! in_array( $mime, yneko_reimu_comment_upload_allowed_mimes(), true ) ) {
		wp_delete_file( $source_path );
		return '';
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$file = array(
		'name'     => wp_basename( $source_path ),
		'type'     => $mime,
		'tmp_name' => $source_path,
		'error'    => UPLOAD_ERR_OK,
		'size'     => filesize( $source_path ),
	);

	add_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );
	$attachment_id = media_handle_sideload(
		$file,
		0,
		sprintf(
			/* translators: %s: original file name. */
			__( '评论上传：%s', 'yneko-reimu' ),
			sanitize_file_name( $file['name'] )
		),
		array(
			'test_form' => false,
			'mimes'     => yneko_reimu_comment_upload_allowed_mimes(),
		)
	);
	remove_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );

	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_file( $source_path );
		return '';
	}

	$comment = get_comment( $comment_id );
	$user_id = $comment ? absint( $comment->user_id ) : 0;
	$is_gif  = 'image/gif' === get_post_mime_type( $attachment_id );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', $is_gif ? 'gif' : 'image' );
	yneko_reimu_comment_set_upload_status( $attachment_id, in_array( $status, array( 'pending', 'approved' ), true ) ? $status : 'pending' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', $user_id );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_comment_id', absint( $comment_id ) );

	return wp_get_attachment_url( $attachment_id );
}

function yneko_reimu_promote_comment_uploads( $comment_id, $comment_approved, $commentdata ) {
	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	if ( '' === $content || false === strpos( $content, 'yneko-reimu-comments/tmp/' ) ) {
		return;
	}

	if ( ! in_array( (string) $comment_approved, array( '1', 'approve' ), true ) ) {
		return;
	}

	$updated = $content;
	foreach ( yneko_reimu_comment_extract_image_urls( $content ) as $url ) {
		$path = yneko_reimu_comment_url_to_path( $url );
		$check = $path && file_exists( $path ) ? wp_check_filetype_and_ext( $path, wp_basename( $path ), yneko_reimu_comment_upload_allowed_mimes() ) : array();
		$type = ( isset( $check['type'] ) && 'image/gif' === $check['type'] ) ? 'gif' : 'image';
		if ( yneko_reimu_comment_upload_review_enabled( $type ) ) {
			continue;
		}
		$promoted = yneko_reimu_comment_promote_upload_url( $url, $comment_id, 'approved' );
		if ( $promoted ) {
			$updated = str_replace( $url, $promoted, $updated );
		}
	}

	if ( $updated !== $content ) {
		wp_update_comment(
			array(
				'comment_ID'      => absint( $comment_id ),
				'comment_content' => $updated,
			)
		);
	}
}
add_action( 'comment_post', 'yneko_reimu_promote_comment_uploads', 10, 3 );

function yneko_reimu_promote_uploads_when_comment_approved( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
		return;
	}

	yneko_reimu_promote_comment_uploads( $comment->comment_ID, '1', array( 'comment_content' => $comment->comment_content ) );
}
add_action( 'wp_set_comment_status', 'yneko_reimu_promote_uploads_when_comment_approved', 10, 1 );

function yneko_reimu_comment_delete_temp_uploads_from_content( $content ) {
	foreach ( yneko_reimu_comment_extract_image_urls( $content ) as $url ) {
		$temp_base = yneko_reimu_comment_temp_upload_base();
		if ( 0 !== strpos( $url, trailingslashit( $temp_base['url'] ) ) ) {
			continue;
		}

		$path = yneko_reimu_comment_url_to_path( $url );
		if ( $path && file_exists( $path ) ) {
			wp_delete_file( $path );
		}
	}
}

function yneko_reimu_cleanup_comment_temp_uploads_on_status( $comment_id, $comment_status ) {
	unset( $comment_id, $comment_status );
}
add_action( 'wp_set_comment_status', 'yneko_reimu_cleanup_comment_temp_uploads_on_status', 10, 2 );

function yneko_reimu_cleanup_comment_temp_uploads_on_delete( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( $comment ) {
		yneko_reimu_comment_delete_temp_uploads_from_content( $comment->comment_content );
	}
}
add_action( 'delete_comment', 'yneko_reimu_cleanup_comment_temp_uploads_on_delete' );

function yneko_reimu_cleanup_expired_comment_temp_uploads() {
	$base = yneko_reimu_comment_temp_upload_base();
	$root = wp_normalize_path( $base['path'] );
	if ( ! is_dir( $root ) ) {
		return;
	}

	$now      = time();
	$max_age  = yneko_reimu_comment_temp_cleanup_days() * DAY_IN_SECONDS;
	$rejected_max_age = yneko_reimu_comment_rejected_cleanup_hours() * HOUR_IN_SECONDS;
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ( $iterator as $item ) {
		$path = wp_normalize_path( $item->getPathname() );
		if ( 0 !== strpos( $path, $root ) ) {
			continue;
		}
		if ( $item->isFile() ) {
			$relative = ltrim( str_replace( $root, '', $path ), '/' );
			$url = trailingslashit( $base['url'] ) . str_replace( DIRECTORY_SEPARATOR, '/', $relative );
			$comment = yneko_reimu_comment_find_comment_by_temp_url( $url );
			$review  = $comment ? yneko_reimu_comment_temp_review_status( $comment->comment_ID, $url ) : array();
			$status  = (string) ( $review['status'] ?? 'pending' );
			$review_time = absint( $review['time'] ?? $item->getMTime() );
			if ( 'rejected' === $status ) {
				if ( ( $now - $review_time ) > $rejected_max_age ) {
					if ( $comment ) {
						yneko_reimu_comment_remove_upload_from_comment( $comment->comment_ID, $url );
					}
					wp_delete_file( $path );
				}
			} elseif ( ( $now - $item->getMTime() ) > $max_age ) {
				if ( $comment ) {
					yneko_reimu_comment_remove_upload_from_comment( $comment->comment_ID, $url );
				}
				wp_delete_file( $path );
			}
		} elseif ( $item->isDir() ) {
			@rmdir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
	}

	foreach ( yneko_reimu_comment_upload_library( 300, 'all', true ) as $item ) {
		if ( 'rejected' !== (string) ( $item['status'] ?? '' ) || empty( $item['id'] ) || ! is_numeric( $item['id'] ) ) {
			continue;
		}
		$status_time = absint( get_post_meta( absint( $item['id'] ), '_yneko_reimu_comment_upload_status_time', true ) );
		if ( ! $status_time ) {
			$status_time = get_post_time( 'U', true, absint( $item['id'] ) );
		}
		if ( $status_time && ( $now - $status_time ) > $rejected_max_age ) {
			$attachment_id  = absint( $item['id'] );
			$attachment_url = wp_get_attachment_url( $attachment_id );
			$comment_id     = absint( $item['comment_id'] ?? 0 );
			if ( $comment_id && $attachment_url ) {
				yneko_reimu_comment_remove_upload_from_comment( $comment_id, $attachment_url );
			}
			wp_delete_attachment( $attachment_id, true );
		}
	}
}
add_action( 'yneko_reimu_cleanup_comment_temp_uploads', 'yneko_reimu_cleanup_expired_comment_temp_uploads' );

function yneko_reimu_schedule_comment_temp_cleanup() {
	if ( ! wp_next_scheduled( 'yneko_reimu_cleanup_comment_temp_uploads' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'yneko_reimu_cleanup_comment_temp_uploads' );
	}
}
add_action( 'init', 'yneko_reimu_schedule_comment_temp_cleanup' );
