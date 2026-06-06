<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_ajax_edit_comment() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;
	if ( ! $comment ) {
		wp_send_json_error( array( 'message' => __( '评论不存在。', 'yneko-reimu' ) ), 404 );
	}

	check_ajax_referer( 'yneko_reimu_comment_manage_' . $comment_id, 'nonce' );
	if ( ! yneko_reimu_current_user_can_manage_comment( $comment ) ) {
		wp_send_json_error( array( 'message' => __( '你不能编辑这条评论。', 'yneko-reimu' ) ), 403 );
	}

	$content = isset( $_POST['comment'] ) ? trim( (string) wp_unslash( $_POST['comment'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Comment content is passed to wp_update_comment and rendered through the theme sanitizer.
	if ( '' === $content ) {
		wp_send_json_error( array( 'message' => __( '评论内容不能为空。', 'yneko-reimu' ) ), 400 );
	}
	if ( yneko_reimu_comment_media_count( $content ) > 1 ) {
		wp_send_json_error( array( 'message' => __( '一条评论最多只能添加一张图片或一个 GIF。', 'yneko-reimu' ) ), 400 );
	}

	$result = wp_update_comment(
		array(
			'comment_ID'      => $comment_id,
			'comment_content' => $content,
		),
		true
	);
	if ( is_wp_error( $result ) || ! $result ) {
		wp_send_json_error( array( 'message' => is_wp_error( $result ) ? $result->get_error_message() : __( '评论更新失败。', 'yneko-reimu' ) ), 400 );
	}

	$comment = get_comment( $comment_id );
	wp_send_json_success(
		array(
			'comment_id' => $comment_id,
			'html'       => yneko_reimu_render_comment_markdown( $comment->comment_content ),
			'raw'        => $comment->comment_content,
			'message'    => __( '评论已更新。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_edit_comment', 'yneko_reimu_ajax_edit_comment' );

function yneko_reimu_ajax_delete_comment() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;
	if ( ! $comment ) {
		wp_send_json_error( array( 'message' => __( '评论不存在。', 'yneko-reimu' ) ), 404 );
	}

	check_ajax_referer( 'yneko_reimu_comment_manage_' . $comment_id, 'nonce' );
	if ( ! yneko_reimu_current_user_can_manage_comment( $comment ) ) {
		wp_send_json_error( array( 'message' => __( '你不能删除这条评论。', 'yneko-reimu' ) ), 403 );
	}

	$post_id = absint( $comment->comment_post_ID );
	if ( ! wp_delete_comment( $comment_id, true ) ) {
		wp_send_json_error( array( 'message' => __( '评论删除失败。', 'yneko-reimu' ) ), 400 );
	}

	$count = get_comments_number( $post_id );
	wp_send_json_success(
		array(
			'comment_id' => $comment_id,
			'count'      => absint( $count ),
			'count_label'=> sprintf(
				/* translators: %s: number of comments. */
				_n( '%s 评论', '%s 评论', $count, 'yneko-reimu' ),
				number_format_i18n( $count )
			),
			'message'    => __( '评论已删除。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_delete_comment', 'yneko_reimu_ajax_delete_comment' );
