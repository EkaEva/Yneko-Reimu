<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_callback( $comment, $args, $depth ) {
	$context = yneko_reimu_comment_item_context( $comment );
	?>
	<li <?php comment_class( 'reimu-comment' . $context['comment_class_extra'] ); ?> id="comment-<?php comment_ID(); ?>" data-comment-time="<?php echo esc_attr( $context['comment_time'] ); ?>" data-comment-id="<?php echo esc_attr( $context['comment_id'] ); ?>" data-comment-user-id="<?php echo esc_attr( absint( $comment->user_id ) ); ?>" data-comment-likes="<?php echo esc_attr( $context['like_count'] ); ?>" data-comment-liked="<?php echo $context['user_liked'] ? '1' : '0'; ?>">
		<article class="reimu-comment__body">
			<?php yneko_reimu_render_comment_item_avatar( $context ); ?>
			<div class="reimu-comment__content">
				<?php
				yneko_reimu_render_comment_item_header( $context );
				yneko_reimu_render_comment_item_review_notice( $context );
				yneko_reimu_render_comment_item_text( $context );
				yneko_reimu_render_comment_item_footer( $context, $args, $depth );
				?>
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
