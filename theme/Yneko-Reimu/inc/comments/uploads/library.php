<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
