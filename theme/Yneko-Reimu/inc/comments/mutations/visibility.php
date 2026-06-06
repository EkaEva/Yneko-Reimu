<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_current_user_can_manage_comment( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'moderate_comments' ) || current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		return true;
	}

	$user_id = get_current_user_id();
	return $user_id && absint( $comment->user_id ) === absint( $user_id );
}

function yneko_reimu_current_user_can_view_private_comment( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'moderate_comments' ) || current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		return true;
	}

	return absint( $comment->user_id ) && absint( $comment->user_id ) === get_current_user_id();
}

function yneko_reimu_comment_visible_upload_review_status( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	foreach ( yneko_reimu_comment_extract_image_urls( $comment->comment_content ) as $url ) {
		if ( false !== strpos( $url, 'yneko-reimu-comments/tmp/' ) ) {
			$review = yneko_reimu_comment_temp_review_status( $comment->comment_ID, $url );
			return in_array( (string) ( $review['status'] ?? '' ), array( 'pending', 'rejected' ), true ) ? (string) $review['status'] : 'pending';
		}
		$attachment_id = attachment_url_to_postid( $url );
		if ( ! $attachment_id || '1' !== get_post_meta( $attachment_id, '_yneko_reimu_comment_upload', true ) ) {
			continue;
		}
		$status = (string) get_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status', true );
		if ( in_array( $status, array( 'pending', 'revoked', 'rejected' ), true ) ) {
			return $status;
		}
	}

	return '';
}

function yneko_reimu_comment_is_publicly_visible( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return false;
	}

	if ( '1' !== (string) $comment->comment_approved ) {
		return yneko_reimu_current_user_can_view_private_comment( $comment );
	}

	$review_status = yneko_reimu_comment_visible_upload_review_status( $comment );
	if ( in_array( $review_status, array( 'pending', 'revoked', 'rejected' ), true ) ) {
		return yneko_reimu_current_user_can_view_private_comment( $comment );
	}

	return true;
}

function yneko_reimu_get_visible_comments( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return array();
	}

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'status'  => is_user_logged_in() ? 'all' : 'approve',
			'order'   => 'ASC',
			'type'    => 'comment',
		)
	);

	return array_values(
		array_filter(
			$comments,
			static function ( $comment ) {
				return yneko_reimu_comment_is_publicly_visible( $comment );
			}
		)
	);
}
