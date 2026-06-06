<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_update_comment_review_status_for_user( $comment_id, $status ) {
	if ( ! empty( $GLOBALS['yneko_reimu_suppress_comment_review_status'] ) ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || empty( $comment->user_id ) ) {
		return;
	}

	$status = sanitize_key( $status );
	if ( in_array( $status, array( 'approve', 'approved', '1' ), true ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'updated', $comment->comment_ID );
	} elseif ( in_array( $status, array( 'hold', 'unapprove', 'unapproved', '0', 'spam', 'trash', 'delete' ), true ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'rejected', $comment->comment_ID );
	}
}

function yneko_reimu_comment_status_changed( $comment_id, $comment_status = '' ) {
	yneko_reimu_update_comment_review_status_for_user( $comment_id, $comment_status );
}
add_action( 'wp_set_comment_status', 'yneko_reimu_comment_status_changed', 10, 2 );
