<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

$reimu_external_comments = yneko_reimu_external_comment_systems();
$reimu_display_post_id   = function_exists( 'yneko_reimu_comments_current_display_post_id' ) ? yneko_reimu_comments_current_display_post_id() : get_the_ID();
$reimu_canonical_post_id = function_exists( 'yneko_reimu_comments_canonical_post_id' ) ? yneko_reimu_comments_canonical_post_id( $reimu_display_post_id ) : $reimu_display_post_id;
$reimu_comments          = $reimu_canonical_post_id ? get_comments(
	array(
		'post_id' => $reimu_canonical_post_id,
		'status'  => 'approve',
		'order'   => 'ASC',
	)
) : array();
$reimu_show_wp_comments  = comments_open( $reimu_canonical_post_id ) || ! empty( $reimu_comments ) || get_comments_number( $reimu_canonical_post_id );
$reimu_has_selector      = ! empty( $reimu_external_comments );
$reimu_comment_count     = get_comments_number( $reimu_canonical_post_id );
$reimu_comment_open      = comments_open( $reimu_canonical_post_id );
?>
<section id="comments" data-aos="fade-up">
	<div class="comment-header">
		<h2 class="comment-title"><?php esc_html_e( '说些什么吧！', 'yneko-reimu' ); ?></h2>

		<?php if ( $reimu_has_selector ) : ?>
			<div class="comment-selector">
				<div class="comment-selector-wrap">
					<?php foreach ( $reimu_external_comments as $key => $config ) : ?>
						<button class="selector-item" type="button" data-selector="<?php echo esc_attr( $key ); ?>"><span><?php echo esc_html( $config['label'] ); ?></span></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="comment-content">
	<?php if ( $reimu_show_wp_comments ) : ?>
		<div id="comment-panel-wordpress" class="comment-panel comment wordpress-comment<?php echo $reimu_has_selector ? '' : ' active'; ?>" data-aos="fade-up">
			<?php
			if ( $reimu_comment_open ) {
				$reimu_logged_in_as = '';
				$reimu_identity     = '';
				if ( is_user_logged_in() ) {
					$reimu_identity = function_exists( 'yneko_reimu_comment_current_user_identity' ) ? yneko_reimu_comment_current_user_identity( $reimu_display_post_id ) : '';
				} else {
					$reimu_logged_in_as = '<a class="reimu-comment-login-link" href="' . esc_url( wp_login_url( get_permalink( $reimu_display_post_id ) ) ) . '">' . esc_html__( '登录', 'yneko-reimu' ) . '</a>';
				}
				$reimu_logged_in_as = str_replace( '%', '%%', $reimu_logged_in_as );
				$reimu_identity     = str_replace( '%', '%%', $reimu_identity );

				comment_form(
					array(
						'class_form'            => 'reimu-comment-form' . ( is_user_logged_in() ? ' reimu-comment-form--logged-in' : '' ),
						'class_submit'          => 'submit reimu-comment-submit',
						'cancel_reply_before'   => '',
						'cancel_reply_after'    => '',
						'cancel_reply_link'     => __( '取消回复', 'yneko-reimu' ),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'logged_in_as'          => '',
						'comment_post_ID'       => $reimu_canonical_post_id,
						'title_reply'           => '',
						'title_reply_before'    => '',
						'title_reply_after'     => '',
						'label_submit'          => __( '提交', 'yneko-reimu' ),
						'format'                => 'xhtml',
						'submit_button'         => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
						'submit_field'          => yneko_reimu_comment_toolbar( $reimu_logged_in_as, $reimu_identity ),
						'fields'                => array(
							'author' => '<div class="reimu-comment-form__fields"><p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( '昵称', 'yneko-reimu' ) . '</label><input id="author" name="author" type="text" placeholder="' . esc_attr__( '昵称', 'yneko-reimu' ) . '" value="' . esc_attr( wp_get_current_commenter()['comment_author'] ) . '" size="30"></p>',
							'email'  => '<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( '邮箱', 'yneko-reimu' ) . '</label><input id="email" name="email" type="email" placeholder="' . esc_attr__( '邮箱', 'yneko-reimu' ) . '" value="' . esc_attr( wp_get_current_commenter()['comment_author_email'] ) . '" size="30"></p>',
							'url'    => '<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( '网址（可选）', 'yneko-reimu' ) . '</label><input id="url" name="url" type="url" placeholder="' . esc_attr__( '网址（可选）', 'yneko-reimu' ) . '" value="' . esc_attr( wp_get_current_commenter()['comment_author_url'] ) . '" size="30"></p></div>',
							'cookies' => '<input type="hidden" name="wp-comment-cookies-consent" value="yes">',
						),
						'comment_field'         => '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . esc_html__( '评论', 'yneko-reimu' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" placeholder="' . esc_attr__( '欢迎评论', 'yneko-reimu' ) . '" required></textarea><input type="hidden" name="redirect_to" value="' . esc_url( get_permalink( $reimu_display_post_id ) . '#comments' ) . '"></p><div class="reimu-comment-preview-panel" data-comment-preview-panel hidden><h4>' . esc_html__( '预览:', 'yneko-reimu' ) . '</h4><div class="reimu-comment-preview-content wl-content"></div></div>',
					),
					$reimu_canonical_post_id
				);
			} elseif ( $reimu_comment_count ) {
				?>
				<p class="no-comments"><?php esc_html_e( '评论已关闭。', 'yneko-reimu' ); ?></p>
				<?php
			}
			?>

			<div class="reimu-comment-list-header">
				<h3 class="reimu-comment-count">
					<?php
					printf(
						/* translators: %s: number of comments. */
						esc_html( _n( '%s 评论', '%s 评论', $reimu_comment_count, 'yneko-reimu' ) ),
						esc_html( number_format_i18n( $reimu_comment_count ) )
					);
					?>
				</h3>
				<div class="reimu-comment-order" aria-label="<?php esc_attr_e( '评论排序', 'yneko-reimu' ); ?>">
					<button type="button" class="active" data-comment-sort="asc"><?php esc_html_e( '按正序', 'yneko-reimu' ); ?></button>
					<button type="button" data-comment-sort="desc"><?php esc_html_e( '按倒序', 'yneko-reimu' ); ?></button>
					<button type="button" data-comment-sort="hot"><?php esc_html_e( '按热度', 'yneko-reimu' ); ?></button>
				</div>
			</div>

			<ol id="reimu-comment-list" class="reimu-comment-list" data-reimu-loadmore-root data-reimu-loadmore-batch="12"<?php echo $reimu_comments ? '' : ' hidden'; ?>>
			<?php if ( $reimu_comments ) : ?>
					<?php
					$GLOBALS['yneko_reimu_comment_display_url'] = get_permalink( $reimu_display_post_id );
					wp_list_comments(
						array(
							'style'       => 'ol',
							'short_ping'  => true,
							'avatar_size' => 56,
							'callback'    => 'yneko_reimu_comment_callback',
							'per_page'    => 0,
							'post_id'     => $reimu_canonical_post_id,
						),
						$reimu_comments
					);
					unset( $GLOBALS['yneko_reimu_comment_display_url'] );
					?>
			<?php endif; ?>
			</ol>
				<div class="reimu-load-more-wrap reimu-comment-load-more-wrap"<?php echo $reimu_comment_count > 12 ? '' : ' hidden'; ?>>
					<button type="button" class="reimu-load-more" data-reimu-loadmore-target="#reimu-comment-list" data-label-more="<?php esc_attr_e( '加载更多...', 'yneko-reimu' ); ?>"><?php esc_html_e( '加载更多...', 'yneko-reimu' ); ?></button>
				</div>
			<?php if ( $reimu_comments ) : ?>
				<?php the_comments_navigation(); ?>
			<?php else : ?>
				<p class="reimu-comment-empty"><?php esc_html_e( '还没有评论，来抢一张小板凳吧。', 'yneko-reimu' ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php foreach ( $reimu_external_comments as $key => $config ) : ?>
		<div id="comment-panel-<?php echo esc_attr( $key ); ?>" class="comment-panel">
			<?php yneko_reimu_render_external_comment_panel( $key, $config ); ?>
		</div>
	<?php endforeach; ?>
	</div>
</section>
