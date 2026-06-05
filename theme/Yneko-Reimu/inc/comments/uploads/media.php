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
