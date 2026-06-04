<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_like_cookie_name() {
	return 'yneko_reimu_comment_like_id';
}

function yneko_reimu_comment_like_guest_token() {
	$cookie_name = yneko_reimu_comment_like_cookie_name();
	$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	if ( preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return strtolower( $token );
	}

	$token   = str_replace( '-', '', wp_generate_uuid4() ) . str_replace( '-', '', wp_generate_uuid4() );
	$expires = time() + YEAR_IN_SECONDS;
	$cookie  = array(
		'expires'  => $expires,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'secure'   => is_ssl(),
		'httponly' => true,
		'samesite' => 'Lax',
	);
	if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) {
		$cookie['domain'] = COOKIE_DOMAIN;
	}

	setcookie( $cookie_name, $token, $cookie );
	$_COOKIE[ $cookie_name ] = $token;

	return $token;
}

function yneko_reimu_comment_like_actor_key( $create_guest = true ) {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		return 'user:' . absint( $user_id );
	}

	$token = $create_guest ? yneko_reimu_comment_like_guest_token() : '';
	if ( ! $token ) {
		$cookie_name = yneko_reimu_comment_like_cookie_name();
		$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	}
	if ( ! preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return '';
	}

	return 'guest:' . hash_hmac( 'sha256', $token, wp_salt( 'nonce' ) );
}

function yneko_reimu_comment_like_registry( $comment_id ) {
	$registry = yneko_reimu_get_comment_meta( absint( $comment_id ), '_yneko_reimu_like_registry', true );
	return is_array( $registry ) ? $registry : array();
}

function yneko_reimu_comment_like_baseline( $comment_id ) {
	$comment_id = absint( $comment_id );
	$baseline   = yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_baseline', true );
	if ( '' !== $baseline && null !== $baseline ) {
		return absint( $baseline );
	}

	$current        = absint( yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_count', true ) );
	$stored_registry = yneko_reimu_comment_like_registry( $comment_id );
	$baseline       = max( 0, $current - count( array_filter( $stored_registry ) ) );
	update_comment_meta( $comment_id, '_yneko_reimu_like_baseline', $baseline );

	return absint( $baseline );
}

function yneko_reimu_comment_like_count_from_registry( $comment_id, $registry = null ) {
	$registry = is_array( $registry ) ? $registry : yneko_reimu_comment_like_registry( $comment_id );
	return yneko_reimu_comment_like_baseline( $comment_id ) + count( array_filter( $registry ) );
}

function yneko_reimu_comment_user_liked( $comment_id ) {
	$comment_id = absint( $comment_id );
	if ( ! $comment_id ) {
		return false;
	}

	$actor_key = yneko_reimu_comment_like_actor_key( false );
	if ( ! $actor_key ) {
		return false;
	}

	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	return ! empty( $registry[ $actor_key ] );
}

function yneko_reimu_ajax_comment_like() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;

	if ( ! $comment ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	check_ajax_referer( 'yneko_reimu_comment_like_' . $comment_id, 'nonce' );

	if ( ! yneko_reimu_comment_is_publicly_visible( $comment ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	$actor_key = yneko_reimu_comment_like_actor_key();
	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	$next_liked = empty( $registry[ $actor_key ] );
	if ( $next_liked ) {
		$registry[ $actor_key ] = time();
	} else {
		unset( $registry[ $actor_key ] );
	}

	$count = yneko_reimu_comment_like_count_from_registry( $comment_id, $registry );
	if ( empty( $registry ) ) {
		delete_comment_meta( $comment_id, '_yneko_reimu_like_registry' );
	} else {
		update_comment_meta( $comment_id, '_yneko_reimu_like_registry', $registry );
	}
	update_comment_meta( $comment_id, '_yneko_reimu_like_count', $count );

	wp_send_json_success(
		array(
			'count' => $count,
			'liked' => $next_liked,
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );
add_action( 'wp_ajax_nopriv_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );

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
