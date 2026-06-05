<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_callback( $comment, $args, $depth ) {
	$comment_time = $comment->comment_date_gmt ? mysql2date( 'U', $comment->comment_date_gmt, false ) : mysql2date( 'U', $comment->comment_date, false );
	$comment_id   = get_comment_ID();
	$like_count   = yneko_reimu_comment_like_count_from_registry( $comment_id );
	$user_liked   = yneko_reimu_comment_user_liked( $comment_id );
	$badges       = yneko_reimu_comment_agent_badges( $comment->comment_agent, $comment->comment_author_IP );
	$user_badges  = ! empty( $comment->user_id ) ? yneko_reimu_comment_user_badges_html( $comment->user_id ) : '';
	$is_logged_in_commenter = ! empty( $comment->user_id );
	$comment_link = get_comment_link( $comment );
	$can_manage   = yneko_reimu_current_user_can_manage_comment( $comment );
	$review_label = yneko_reimu_comment_media_review_label( $comment );
	$review_status = yneko_reimu_comment_visible_upload_review_status( $comment );
	if ( ! empty( $GLOBALS['yneko_reimu_comment_display_url'] ) ) {
		$comment_link = untrailingslashit( (string) $GLOBALS['yneko_reimu_comment_display_url'] ) . '#comment-' . absint( $comment_id );
	}
	?>
	<li <?php comment_class( 'reimu-comment' . ( 'rejected' === $review_status ? ' reimu-comment-rejected' : '' ) ); ?> id="comment-<?php comment_ID(); ?>" data-comment-time="<?php echo esc_attr( $comment_time ); ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-comment-user-id="<?php echo esc_attr( absint( $comment->user_id ) ); ?>" data-comment-likes="<?php echo esc_attr( $like_count ); ?>" data-comment-liked="<?php echo $user_liked ? '1' : '0'; ?>">
		<article class="reimu-comment__body">
			<a class="reimu-comment__avatar<?php echo $is_logged_in_commenter ? ' reimu-comment__avatar--logged-in' : ''; ?>" href="<?php echo esc_url( $comment_link ); ?>" aria-hidden="true" tabindex="-1"><?php echo yneko_reimu_get_comment_avatar( $comment, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<div class="reimu-comment__content">
				<header class="reimu-comment__meta">
					<span class="reimu-comment__headline">
						<span class="reimu-comment__author"><?php echo yneko_reimu_comment_author_link_html( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php echo $user_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="reimu-comment__date" href="<?php echo esc_url( $comment_link ); ?>">
							<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'Y-m-d', $comment ) ); ?></time>
						</a>
					</span>
					<?php echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</header>
				<?php if ( $review_label ) : ?>
					<p class="comment-awaiting-moderation<?php echo 'rejected' === $review_status ? ' is-rejected' : ''; ?>"><?php echo esc_html( $review_label ); ?></p>
				<?php endif; ?>
				<div class="comment-text wl-content" data-comment-raw="<?php echo esc_attr( $comment->comment_content ); ?>"><?php echo yneko_reimu_render_comment_markdown( $comment->comment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div class="reimu-comment__footer" aria-label="<?php esc_attr_e( '评论操作', 'yneko-reimu' ); ?>">
					<?php if ( $can_manage ) : ?>
						<button type="button" class="reimu-comment-owner-action" data-comment-edit="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_manage_' . $comment_id ) ); ?>" aria-label="<?php esc_attr_e( '编辑评论', 'yneko-reimu' ); ?>">
							<?php echo yneko_reimu_waline_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" class="reimu-comment-owner-action reimu-comment-owner-action--delete" data-comment-delete="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_manage_' . $comment_id ) ); ?>" aria-label="<?php esc_attr_e( '删除评论', 'yneko-reimu' ); ?>">
							<?php echo yneko_reimu_waline_icon( 'trash' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					<?php endif; ?>
					<button type="button" class="reimu-comment-like<?php echo $user_liked ? ' liked' : ''; ?>" data-comment-like="<?php echo esc_attr( $comment_id ); ?>" data-like-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_like_' . $comment_id ) ); ?>" aria-pressed="<?php echo $user_liked ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( '点赞', 'yneko-reimu' ); ?>">
						<?php echo yneko_reimu_waline_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span data-like-count><?php echo esc_html( $like_count ); ?></span>
					</button>
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'reply_text' => yneko_reimu_waline_icon( 'reply' ) . '<span>' . esc_html__( '回复', 'yneko-reimu' ) . '</span>',
								'before'     => '',
								'after'      => '',
							)
						)
					);
					?>
				</div>
			</div>
		</article>
	<?php
}

function yneko_reimu_comment_field_order( $fields ) {
	$ordered = array();

	foreach ( array( 'author', 'email', 'url', 'comment', 'cookies' ) as $key ) {
		if ( isset( $fields[ $key ] ) ) {
			$ordered[ $key ] = $fields[ $key ];
		}
	}

	foreach ( $fields as $key => $field ) {
		if ( ! isset( $ordered[ $key ] ) ) {
			$ordered[ $key ] = $field;
		}
	}

	return $ordered;
}
add_filter( 'comment_form_fields', 'yneko_reimu_comment_field_order' );
