<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_page_id = get_queried_object_id();
$reimu_comments = $reimu_page_id ? get_comments_number( $reimu_page_id ) : 0;
?>
<footer class="article-footer reimu-virtual-footer">
	<span class="article-visitor-link" data-aos="zoom-in" aria-label="<?php esc_attr_e( '阅读量', 'yneko-reimu' ); ?>">
		<?php echo yneko_reimu_view_count_text( $reimu_page_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</span>
	<a class="article-comment-link" data-aos="zoom-in" data-no-pjax href="<?php echo esc_url( get_permalink( $reimu_page_id ) . '#comments' ); ?>">
		<?php
		printf(
			/* translators: %s: comment count. */
			esc_html__( '%s 留言', 'yneko-reimu' ),
			esc_html( number_format_i18n( $reimu_comments ) )
		);
		?>
	</a>
</footer>
