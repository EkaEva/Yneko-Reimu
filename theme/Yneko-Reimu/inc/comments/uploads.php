<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_upload_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['enabled'] ?? '0' ) || '1' === (string) ( $settings['image_enabled'] ?? '0' ) || '1' === (string) ( $settings['gif_enabled'] ?? '0' );
}

function yneko_reimu_comment_upload_type_enabled( $type ) {
	$type     = 'gif' === $type ? 'gif' : 'image';
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings[ $type . '_enabled' ] ?? '0' );
}

function yneko_reimu_comment_upload_review_enabled( $type ) {
	$type     = 'gif' === $type ? 'gif' : 'image';
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings[ $type . '_review' ] ?? '0' );
}

function yneko_reimu_comment_upload_limits() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return array(
		'image' => max( 1, absint( $settings['image_max_mb'] ?? 1 ) ) * MB_IN_BYTES,
		'gif'   => max( 1, absint( $settings['gif_max_mb'] ?? 1 ) ) * MB_IN_BYTES,
	);
}

function yneko_reimu_comment_upload_dir( $dirs ) {
	$subdir = '/yneko-reimu-comments' . gmdate( '/Y/m' );

	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;

	return $dirs;
}

function yneko_reimu_comment_temp_upload_dir( $dirs ) {
	$type   = ! empty( $GLOBALS['yneko_reimu_comment_upload_type'] ) && 'gif' === $GLOBALS['yneko_reimu_comment_upload_type'] ? 'gif' : 'image';
	$subdir = '/yneko-reimu-comments/tmp/' . $type . gmdate( '/Y/m/d' );

	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;

	return $dirs;
}

function yneko_reimu_comment_temp_upload_base() {
	$uploads = wp_get_upload_dir();
	return array(
		'path' => trailingslashit( $uploads['basedir'] ) . 'yneko-reimu-comments/tmp',
		'url'  => trailingslashit( $uploads['baseurl'] ) . 'yneko-reimu-comments/tmp',
	);
}

function yneko_reimu_comment_temp_cleanup_days() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return max( 1, min( 30, absint( $settings['temp_cleanup_days'] ?? 7 ) ) );
}

function yneko_reimu_comment_rejected_cleanup_hours() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return max( 1, min( 168, absint( $settings['rejected_cleanup_hours'] ?? 24 ) ) );
}

function yneko_reimu_comment_regular_upload_base() {
	$uploads = wp_get_upload_dir();
	return array(
		'path' => trailingslashit( $uploads['basedir'] ) . 'yneko-reimu-comments',
		'url'  => trailingslashit( $uploads['baseurl'] ) . 'yneko-reimu-comments',
	);
}

function yneko_reimu_comment_url_to_path( $url ) {
	$uploads = wp_get_upload_dir();
	$baseurl = trailingslashit( $uploads['baseurl'] );
	if ( 0 !== strpos( $url, $baseurl ) ) {
		return '';
	}

	$relative = rawurldecode( substr( $url, strlen( $baseurl ) ) );
	$relative = str_replace( array( '../', '..\\' ), '', $relative );
	return trailingslashit( $uploads['basedir'] ) . wp_normalize_path( $relative );
}

function yneko_reimu_comment_extract_image_urls( $content ) {
	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', (string) $content, $matches );
	return empty( $matches[1] ) ? array() : array_values( array_unique( array_map( 'esc_url_raw', $matches[1] ) ) );
}

function yneko_reimu_comment_media_count( $content ) {
	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', (string) $content, $matches );
	return empty( $matches[0] ) ? 0 : count( $matches[0] );
}

function yneko_reimu_comment_normalize_media_url( $url ) {
	return esc_url_raw( html_entity_decode( (string) $url, ENT_QUOTES ) );
}

function yneko_reimu_comment_remove_temp_review_status( $comment_id, $url ) {
	$comment_id = absint( $comment_id );
	$url        = esc_url_raw( $url );
	if ( ! $comment_id || ! $url ) {
		return;
	}

	$meta = yneko_reimu_comment_temp_review_meta( $comment_id );
	if ( ! isset( $meta[ $url ] ) ) {
		return;
	}

	unset( $meta[ $url ] );
	if ( empty( $meta ) ) {
		delete_comment_meta( $comment_id, '_yneko_reimu_comment_temp_upload_reviews' );
	} else {
		update_comment_meta( $comment_id, '_yneko_reimu_comment_temp_upload_reviews', $meta );
	}
}

function yneko_reimu_comment_remove_media_url_from_content( $content, $url ) {
	$target  = yneko_reimu_comment_normalize_media_url( $url );
	$changed = false;
	if ( '' === $target ) {
		return (string) $content;
	}

	$updated = preg_replace_callback(
		'/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) use ( $target, &$changed ) {
			if ( $target === yneko_reimu_comment_normalize_media_url( $matches[1] ?? '' ) ) {
				$changed = true;
				return "\n";
			}
			return $matches[0];
		},
		(string) $content
	);

	if ( ! $changed ) {
		return (string) $content;
	}

	$updated = preg_replace( "/[ \t]*\n[ \t]*/", "\n", (string) $updated );
	$updated = preg_replace( "/\n{3,}/", "\n\n", (string) $updated );
	return trim( (string) $updated );
}

function yneko_reimu_comment_remove_upload_from_comment( $comment_id, $url ) {
	$comment = get_comment( absint( $comment_id ) );
	if ( ! $comment ) {
		return false;
	}

	$updated = yneko_reimu_comment_remove_media_url_from_content( $comment->comment_content, $url );
	if ( $updated === (string) $comment->comment_content ) {
		return false;
	}

	yneko_reimu_comment_remove_temp_review_status( $comment->comment_ID, $url );
	if ( '' === trim( wp_strip_all_tags( $updated ) ) ) {
		return wp_delete_comment( $comment->comment_ID, true ) ? 'deleted' : false;
	}

	$result = wp_update_comment(
		array(
			'comment_ID'      => absint( $comment->comment_ID ),
			'comment_content' => $updated,
		),
		true
	);

	return ( is_wp_error( $result ) || ! $result ) ? false : 'updated';
}

function yneko_reimu_comment_missing_image_url() {
	return yneko_reimu_asset_uri( 'assets/images/comment-missing.webp' );
}

function yneko_reimu_comment_resolve_image_url( $url ) {
	$url = esc_url_raw( $url );
	if ( false === strpos( $url, 'yneko-reimu-comments/' ) ) {
		return $url;
	}

	$attachment_id = attachment_url_to_postid( $url );
	if ( $attachment_id && '1' === get_post_meta( $attachment_id, '_yneko_reimu_comment_upload', true ) ) {
		$attachment_url = wp_get_attachment_url( $attachment_id );
		return $attachment_url ? esc_url_raw( $attachment_url ) : yneko_reimu_comment_missing_image_url();
	}

	$path = yneko_reimu_comment_url_to_path( $url );
	if ( $path && file_exists( $path ) ) {
		return $url;
	}

	return yneko_reimu_comment_missing_image_url();
}

function yneko_reimu_comment_media_review_label( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}
	$status = yneko_reimu_comment_visible_upload_review_status( $comment );
	if ( 'rejected' === $status ) {
		return __( '审核未通过', 'yneko-reimu' );
	}
	if ( in_array( $status, array( 'pending', 'revoked' ), true ) || ( '0' === (string) $comment->comment_approved && ! $status ) ) {
		return __( '评论正在等待审核。', 'yneko-reimu' );
	}
	return '';
}

function yneko_reimu_comment_upload_allowed_mimes() {
	return array(
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'webp'     => 'image/webp',
		'gif'      => 'image/gif',
	);
}

function yneko_reimu_comment_temp_upload_nonce_action( $action, $relative ) {
	return 'yneko_reimu_comment_upload_' . sanitize_key( $action ) . '_temp_' . md5( wp_normalize_path( rawurldecode( (string) $relative ) ) );
}

function yneko_reimu_comment_temp_review_meta( $comment_id ) {
	$meta = get_comment_meta( absint( $comment_id ), '_yneko_reimu_comment_temp_upload_reviews', true );
	return is_array( $meta ) ? $meta : array();
}

function yneko_reimu_comment_set_temp_review_status( $comment_id, $url, $status ) {
	$comment_id = absint( $comment_id );
	$url        = esc_url_raw( $url );
	$status     = sanitize_key( $status );
	if ( ! $comment_id || ! $url || ! in_array( $status, array( 'pending', 'rejected' ), true ) ) {
		return;
	}

	$meta         = yneko_reimu_comment_temp_review_meta( $comment_id );
	$meta[ $url ] = array(
		'status' => $status,
		'time'   => time(),
	);
	update_comment_meta( $comment_id, '_yneko_reimu_comment_temp_upload_reviews', $meta );
}

function yneko_reimu_comment_temp_review_status( $comment_id, $url ) {
	$meta = yneko_reimu_comment_temp_review_meta( $comment_id );
	$url  = esc_url_raw( $url );
	return isset( $meta[ $url ] ) && is_array( $meta[ $url ] ) ? $meta[ $url ] : array();
}

function yneko_reimu_comment_set_upload_status( $attachment_id, $status ) {
	$attachment_id = absint( $attachment_id );
	$status        = sanitize_key( $status );
	if ( ! $attachment_id || ! in_array( $status, array( 'pending', 'approved', 'revoked', 'rejected', 'private' ), true ) ) {
		return;
	}

	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status', $status );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status_time', time() );
}

function yneko_reimu_comment_upload_is_gif_attachment( $attachment_id ) {
	return 'image/gif' === get_post_mime_type( $attachment_id );
}

function yneko_reimu_comment_gif_library( $limit = 24, $include_pending = false ) {
	$items = yneko_reimu_comment_upload_library( $limit, 'gif', $include_pending );

	if ( ! $include_pending ) {
		$items = array_values(
			array_filter(
				$items,
				function ( $item ) {
					return 'approved' === $item['status'];
				}
			)
		);
	}

	return $items;
}

function yneko_reimu_comment_upload_library( $limit = 80, $type = 'all', $include_pending = true ) {
	$type       = in_array( $type, array( 'all', 'gif', 'image' ), true ) ? $type : 'all';
	$meta_query = array(
		array(
			'key'   => '_yneko_reimu_comment_upload',
			'value' => '1',
		),
	);

	if ( 'all' !== $type ) {
		$meta_query[] = array(
			'key'   => '_yneko_reimu_comment_upload_type',
			'value' => $type,
		);
	}

	if ( $include_pending ) {
		$meta_query[] = array(
			'key'     => '_yneko_reimu_comment_upload_status',
			'value'   => array( 'approved', 'pending', 'private', 'revoked', 'rejected' ),
			'compare' => 'IN',
		);
	} else {
		$meta_query[] = array(
			'key'   => '_yneko_reimu_comment_upload_status',
			'value' => 'approved',
		);
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'gif' === $type ? 'image/gif' : 'image',
			'posts_per_page' => max( 1, absint( $limit ) ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);

	$items = array();
	foreach ( $query->posts as $attachment ) {
		$url = wp_get_attachment_url( $attachment->ID );
		if ( ! $url ) {
			continue;
		}

		$items[] = array(
			'id'     => absint( $attachment->ID ),
			'url'    => esc_url_raw( $url ),
			'title'  => get_the_title( $attachment ),
			'type'   => get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_type', true ),
			'status' => get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_status', true ),
			'user'   => absint( get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_user_id', true ) ),
			'comment_id' => absint( get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_comment_id', true ) ),
			'date'   => get_the_date( 'Y-m-d H:i', $attachment ),
		);
	}

	return $items;
}

function yneko_reimu_comment_pending_temp_uploads( $limit = 80 ) {
	$base = yneko_reimu_comment_temp_upload_base();
	$root = wp_normalize_path( $base['path'] );
	if ( ! is_dir( $root ) ) {
		return array();
	}

	$uploads = array();
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $iterator as $item ) {
		if ( ! $item->isFile() ) {
			continue;
		}
		$path = wp_normalize_path( $item->getPathname() );
		if ( 0 !== strpos( $path, $root ) ) {
			continue;
		}
		$check = wp_check_filetype_and_ext( $path, wp_basename( $path ), yneko_reimu_comment_upload_allowed_mimes() );
		$mime  = $check['type'] ?? '';
		if ( ! $mime ) {
			continue;
		}
		$relative = ltrim( str_replace( $root, '', $path ), '/' );
		$url = trailingslashit( $base['url'] ) . str_replace( DIRECTORY_SEPARATOR, '/', $relative );
		$comment = yneko_reimu_comment_find_comment_by_temp_url( $url );
		$review  = $comment ? yneko_reimu_comment_temp_review_status( $comment->comment_ID, $url ) : array();
		$status  = in_array( (string) ( $review['status'] ?? '' ), array( 'pending', 'rejected' ), true ) ? (string) $review['status'] : 'pending';
		$uploads[] = array(
			'id'     => 'temp:' . rawurlencode( $relative ),
			'url'    => esc_url_raw( $url ),
			'path'   => $path,
			'title'  => wp_basename( $path ),
			'type'   => 'image/gif' === $mime ? 'gif' : 'image',
			'status' => $status,
			'user'   => $comment ? absint( $comment->user_id ) : 0,
			'comment_id' => $comment ? absint( $comment->comment_ID ) : 0,
			'date'   => date_i18n( 'Y-m-d H:i', filemtime( $path ) ),
		);
		if ( count( $uploads ) >= absint( $limit ) ) {
			break;
		}
	}

	return $uploads;
}

function yneko_reimu_comment_find_comment_by_temp_url( $url ) {
	global $wpdb;

	$url = esc_url_raw( $url );
	if ( '' === $url ) {
		return null;
	}

	$comment_id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			"SELECT comment_ID FROM {$wpdb->comments} WHERE comment_content LIKE %s ORDER BY comment_ID DESC LIMIT 1",
			'%' . $wpdb->esc_like( $url ) . '%'
		)
	);

	return $comment_id ? get_comment( absint( $comment_id ) ) : null;
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

function yneko_reimu_comment_public_gif_urls() {
	$urls = array();
	foreach ( yneko_reimu_comment_gif_library( 200, false ) as $item ) {
		if ( ! empty( $item['url'] ) ) {
			$urls[] = esc_url_raw( $item['url'] );
		}
	}
	return array_values( array_unique( $urls ) );
}

function yneko_reimu_comment_is_public_gif_only( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return false;
	}

	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', $content, $matches );
	if ( empty( $matches[1] ) || 1 !== count( $matches[1] ) ) {
		return false;
	}

	$remaining = trim( preg_replace( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', '', $content ) );
	if ( '' !== $remaining ) {
		return false;
	}

	return in_array( esc_url_raw( $matches[1][0] ), yneko_reimu_comment_public_gif_urls(), true );
}

function yneko_reimu_comment_content_kind( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return '';
	}

	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', $content, $matches );
	$urls = empty( $matches[1] ) ? array() : $matches[1];
	$remaining = trim( preg_replace( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', '', $content ) );

	if ( 1 === count( $urls ) && '' === $remaining ) {
		return preg_match( '/\.gif(?:\?.*)?$/i', (string) $urls[0] ) ? 'gif' : 'image';
	}

	if ( empty( $urls ) && '' !== trim( wp_strip_all_tags( $content ) ) ) {
		return 'text';
	}

	return '';
}

function yneko_reimu_comment_identity_key( $commentdata ) {
	if ( ! empty( $commentdata['user_ID'] ) ) {
		return 'user:' . absint( $commentdata['user_ID'] );
	}
	if ( ! empty( $commentdata['comment_author_email'] ) ) {
		return 'mail:' . strtolower( sanitize_email( $commentdata['comment_author_email'] ) );
	}
	if ( ! empty( $commentdata['comment_author_IP'] ) ) {
		return 'ip:' . md5( (string) $commentdata['comment_author_IP'] );
	}
	return '';
}

function yneko_reimu_prevent_duplicate_simple_comment( $commentdata ) {
	$kind = yneko_reimu_comment_content_kind( $commentdata['comment_content'] ?? '' );
	if ( ! in_array( $kind, array( 'gif', 'image', 'text' ), true ) ) {
		return $commentdata;
	}

	$key = yneko_reimu_comment_identity_key( $commentdata );
	if ( '' === $key ) {
		return $commentdata;
	}

	$hash = md5( trim( (string) $commentdata['comment_content'] ) );
	$flag = 'yneko_reimu_simple_comment_' . md5( $key . '|' . $kind . '|' . $hash );
	if ( get_transient( $flag ) ) {
		return new WP_Error(
			'yneko_reimu_duplicate_simple_comment',
			__( '请不要重复发送相同的评论。', 'yneko-reimu' ),
			array( 'status' => 409 )
		);
	}

	set_transient( $flag, 1, HOUR_IN_SECONDS );
	return $commentdata;
}
add_filter( 'preprocess_comment', 'yneko_reimu_prevent_duplicate_simple_comment', 5 );

function yneko_reimu_limit_comment_media_count( $approved, $commentdata ) {
	if ( yneko_reimu_comment_media_count( $commentdata['comment_content'] ?? '' ) <= 1 ) {
		return $approved;
	}

	return new WP_Error(
		'yneko_reimu_comment_media_limit',
		__( '一条评论最多只能添加一张图片或一个 GIF。', 'yneko-reimu' ),
		array( 'status' => 400 )
	);
}
add_filter( 'pre_comment_approved', 'yneko_reimu_limit_comment_media_count', 5, 2 );

function yneko_reimu_approve_public_gif_only_comment( $approved, $commentdata ) {
	if ( yneko_reimu_comment_is_public_gif_only( $commentdata['comment_content'] ?? '' ) ) {
		return 1;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'yneko_reimu_approve_public_gif_only_comment', 10, 2 );
