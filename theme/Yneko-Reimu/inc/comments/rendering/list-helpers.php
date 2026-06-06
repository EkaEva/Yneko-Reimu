<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_item_context( $comment ) {
	$comment_id    = get_comment_ID();
	$review_status = yneko_reimu_comment_visible_upload_review_status( $comment );

	return array(
		'comment'                  => $comment,
		'comment_id'               => $comment_id,
		'comment_time'             => $comment->comment_date_gmt ? mysql2date( 'U', $comment->comment_date_gmt, false ) : mysql2date( 'U', $comment->comment_date, false ),
		'like_count'               => yneko_reimu_comment_like_count_from_registry( $comment_id ),
		'user_liked'               => yneko_reimu_comment_user_liked( $comment_id ),
		'badges'                   => yneko_reimu_comment_agent_badges( $comment->comment_agent, $comment->comment_author_IP ),
		'user_badges'              => ! empty( $comment->user_id ) ? yneko_reimu_comment_user_badges_html( $comment->user_id ) : '',
		'is_logged_in_commenter'   => ! empty( $comment->user_id ),
		'comment_link'             => yneko_reimu_comment_item_link( $comment, $comment_id ),
		'can_manage'               => yneko_reimu_current_user_can_manage_comment( $comment ),
		'review_label'             => yneko_reimu_comment_media_review_label( $comment ),
		'review_status'            => $review_status,
		'comment_class_extra'      => 'rejected' === $review_status ? ' reimu-comment-rejected' : '',
		'avatar_logged_in_class'   => ! empty( $comment->user_id ) ? ' reimu-comment__avatar--logged-in' : '',
		'review_notice_extra_class' => 'rejected' === $review_status ? ' is-rejected' : '',
	);
}

function yneko_reimu_comment_item_link( $comment, $comment_id ) {
	if ( ! empty( $GLOBALS['yneko_reimu_comment_display_url'] ) ) {
		return untrailingslashit( (string) $GLOBALS['yneko_reimu_comment_display_url'] ) . '#comment-' . absint( $comment_id );
	}

	return get_comment_link( $comment );
}

function yneko_reimu_render_comment_item_avatar( $context ) {
	$comment = $context['comment'];
	?>
	<a class="reimu-comment__avatar<?php echo esc_attr( $context['avatar_logged_in_class'] ); ?>" href="<?php echo esc_url( $context['comment_link'] ); ?>" aria-hidden="true" tabindex="-1"><?php echo yneko_reimu_get_comment_avatar( $comment, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	<?php
}

function yneko_reimu_render_comment_item_header( $context ) {
	$comment = $context['comment'];
	?>
	<header class="reimu-comment__meta">
		<span class="reimu-comment__headline">
			<span class="reimu-comment__author"><?php echo yneko_reimu_comment_author_link_html( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php echo $context['user_badges']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<a class="reimu-comment__date" href="<?php echo esc_url( $context['comment_link'] ); ?>">
				<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'Y-m-d', $comment ) ); ?></time>
			</a>
		</span>
		<?php echo $context['badges']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</header>
	<?php
}

function yneko_reimu_render_comment_item_review_notice( $context ) {
	if ( ! $context['review_label'] ) {
		return;
	}
	?>
	<p class="comment-awaiting-moderation<?php echo esc_attr( $context['review_notice_extra_class'] ); ?>"><?php echo esc_html( $context['review_label'] ); ?></p>
	<?php
}

function yneko_reimu_render_comment_item_text( $context ) {
	$comment = $context['comment'];
	?>
	<div class="comment-text wl-content" data-comment-raw="<?php echo esc_attr( $comment->comment_content ); ?>"><?php echo yneko_reimu_render_comment_markdown( $comment->comment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php
}

function yneko_reimu_render_comment_owner_actions( $context ) {
	if ( ! $context['can_manage'] ) {
		return;
	}
	$comment_id = $context['comment_id'];
	$nonce      = wp_create_nonce( 'yneko_reimu_comment_manage_' . $comment_id );
	?>
	<button type="button" class="reimu-comment-owner-action" data-comment-edit="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( $nonce ); ?>" aria-label="<?php esc_attr_e( '编辑评论', 'yneko-reimu' ); ?>">
		<?php echo yneko_reimu_waline_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>
	<button type="button" class="reimu-comment-owner-action reimu-comment-owner-action--delete" data-comment-delete="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( $nonce ); ?>" aria-label="<?php esc_attr_e( '删除评论', 'yneko-reimu' ); ?>">
		<?php echo yneko_reimu_waline_icon( 'trash' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>
	<?php
}

function yneko_reimu_render_comment_like_action( $context ) {
	?>
	<button type="button" class="reimu-comment-like<?php echo $context['user_liked'] ? ' liked' : ''; ?>" data-comment-like="<?php echo esc_attr( $context['comment_id'] ); ?>" data-like-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_like_' . $context['comment_id'] ) ); ?>" aria-pressed="<?php echo $context['user_liked'] ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( '点赞', 'yneko-reimu' ); ?>">
		<?php echo yneko_reimu_waline_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span data-like-count><?php echo esc_html( $context['like_count'] ); ?></span>
	</button>
	<?php
}

function yneko_reimu_render_comment_reply_action( $args, $depth ) {
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
}

function yneko_reimu_render_comment_item_footer( $context, $args, $depth ) {
	?>
	<div class="reimu-comment__footer" aria-label="<?php esc_attr_e( '评论操作', 'yneko-reimu' ); ?>">
		<?php
		yneko_reimu_render_comment_owner_actions( $context );
		yneko_reimu_render_comment_like_action( $context );
		yneko_reimu_render_comment_reply_action( $args, $depth );
		?>
	</div>
	<?php
}
