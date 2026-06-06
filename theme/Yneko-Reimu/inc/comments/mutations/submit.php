<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_comment_item_html( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	$previous_comment    = $GLOBALS['comment'] ?? null;
	$previous_display_url = $GLOBALS['yneko_reimu_comment_display_url'] ?? null;
	$GLOBALS['comment'] = $comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$GLOBALS['yneko_reimu_comment_display_url'] = get_permalink( (int) $comment->comment_post_ID );
	ob_start();
	yneko_reimu_comment_callback(
		$comment,
		array(
			'style'      => 'ol',
			'avatar_size'=> 56,
			'max_depth'  => get_option( 'thread_comments_depth' ),
			'type'       => 'comment',
		),
		1
	);
	$html = ob_get_clean();

	if ( null === $previous_comment ) {
		unset( $GLOBALS['comment'] );
	} else {
		$GLOBALS['comment'] = $previous_comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
	if ( null === $previous_display_url ) {
		unset( $GLOBALS['yneko_reimu_comment_display_url'] );
	} else {
		$GLOBALS['yneko_reimu_comment_display_url'] = $previous_display_url;
	}

	return $html;
}

function yneko_reimu_ajax_submit_comment() {
	check_ajax_referer( 'yneko_reimu_submit_comment', 'nonce' );

	$comment_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	unset( $comment_data['action'], $comment_data['nonce'] );
	$comment = wp_handle_comment_submission( $comment_data );
	if ( is_wp_error( $comment ) ) {
		$status = 400;
		$data   = $comment->get_error_data();
		if ( is_numeric( $data ) ) {
			$status = absint( $data );
		} elseif ( is_array( $data ) && isset( $data['status'] ) ) {
			$status = absint( $data['status'] );
		}
		if ( $status < 400 || $status > 599 ) {
			$status = 400;
		}
		wp_send_json_error(
			array(
				'message' => $comment->get_error_message(),
				'code'    => $comment->get_error_code(),
			),
			$status
		);
	}

	$comment = get_comment( $comment->comment_ID );
	if ( ! $comment ) {
		wp_send_json_error(
			array(
				'message' => __( '评论提交失败。', 'yneko-reimu' ),
			),
			400
		);
	}

	$is_approved = '1' === (string) $comment->comment_approved;
	if ( ! $is_approved && is_user_logged_in() && absint( $comment->user_id ) === get_current_user_id() ) {
		yneko_reimu_increment_user_review_status_count( get_current_user_id(), 'comments', 'pending', $comment->comment_ID );
	}
	foreach ( yneko_reimu_comment_extract_image_urls( $comment->comment_content ) as $url ) {
		if ( false !== strpos( $url, 'yneko-reimu-comments/tmp/' ) ) {
			yneko_reimu_comment_set_temp_review_status( $comment->comment_ID, $url, 'pending' );
		}
	}
	$count       = count( yneko_reimu_get_visible_comments( (int) $comment->comment_post_ID ) );
	wp_send_json_success(
		array(
			'comment_id' => absint( $comment->comment_ID ),
			'parent_id'  => absint( $comment->comment_parent ),
			'approved'   => $is_approved,
			'html'       => yneko_reimu_render_comment_item_html( $comment ),
			'count'      => absint( $count ),
			'count_label'=> sprintf(
				/* translators: %s: number of comments. */
				_n( '%s 评论', '%s 评论', $count, 'yneko-reimu' ),
				number_format_i18n( $count )
			),
			'message'    => $is_approved ? __( '评论已发布。', 'yneko-reimu' ) : __( '评论已提交，正在等待审核。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_submit_comment', 'yneko_reimu_ajax_submit_comment' );
add_action( 'wp_ajax_nopriv_yneko_reimu_submit_comment', 'yneko_reimu_ajax_submit_comment' );
