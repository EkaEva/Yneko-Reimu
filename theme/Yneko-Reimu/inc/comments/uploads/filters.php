<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

function yneko_reimu_hold_comment_with_pending_uploads( $approved, $commentdata ) {
	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	if ( false !== strpos( $content, 'yneko-reimu-comments/tmp/' ) ) {
		return 0;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'yneko_reimu_hold_comment_with_pending_uploads', 10, 2 );
