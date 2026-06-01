<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_page_id    = get_queried_object_id();
$reimu_page_slug  = yneko_reimu_is_virtual_page() ? yneko_reimu_virtual_page_slug() : yneko_reimu_special_page_slug( $reimu_page_id );
$reimu_comment_id = $reimu_page_id;
if ( 'projects' === $reimu_page_slug && function_exists( 'yneko_reimu_comments_virtual_page_post_id' ) ) {
	$reimu_comment_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
}
$reimu_comment_url = $reimu_page_slug && function_exists( 'yneko_reimu_i18n_virtual_path' )
	? yneko_reimu_i18n_virtual_path( $reimu_page_slug )
	: get_permalink( $reimu_page_id );
?>
<footer class="article-footer reimu-virtual-footer">
	<div class="article-footer-terms"></div>
	<div class="article-footer-stats">
		<span class="article-visitor-link" data-aos="zoom-in" aria-label="<?php esc_attr_e( '阅读量', 'yneko-reimu' ); ?>">
			<?php echo yneko_reimu_view_count_text( $reimu_page_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
		<a class="article-comment-link" data-aos="zoom-in" data-no-pjax href="<?php echo esc_url( $reimu_comment_url . '#comments' ); ?>">
			<?php echo yneko_reimu_comment_count_text( $reimu_comment_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
</footer>
