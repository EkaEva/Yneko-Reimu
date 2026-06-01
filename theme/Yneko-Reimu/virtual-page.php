<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_virtual_slug = yneko_reimu_virtual_page_slug();

get_header();
?>
<div id="content" aria-label="<?php esc_attr_e( '页面内容', 'yneko-reimu' ); ?>" class="sidebar-<?php echo esc_attr( yneko_reimu_sidebar_position() ); ?>">
	<?php get_sidebar(); ?>
	<section id="main" aria-label="<?php esc_attr_e( '主要内容', 'yneko-reimu' ); ?>">
		<?php
		if ( $reimu_virtual_slug ) {
			get_template_part( 'template-parts/virtual/' . $reimu_virtual_slug );
		} else {
			get_template_part( 'template-parts/content/content-none' );
		}
		?>
		<?php
		if ( 'projects' === $reimu_virtual_slug && function_exists( 'yneko_reimu_should_show_comments' ) && yneko_reimu_should_show_comments( yneko_reimu_comments_virtual_page_post_id( 'projects' ) ) ) {
			comments_template();
		}
		?>
	</section>
</div>
<?php
get_footer();
