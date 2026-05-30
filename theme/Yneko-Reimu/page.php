<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div id="content" aria-label="<?php esc_attr_e( '页面内容', 'yneko-reimu' ); ?>" class="sidebar-<?php echo esc_attr( yneko_reimu_sidebar_position( get_queried_object_id() ) ); ?>">
	<?php
	while ( have_posts() ) :
		the_post();
		$reimu_special_page_slug = yneko_reimu_special_page_slug( get_the_ID() );
		?>
		<?php get_sidebar(); ?>
		<section id="main" aria-label="<?php esc_attr_e( '主要内容', 'yneko-reimu' ); ?>">
				<?php
				if ( 'about' === $reimu_special_page_slug ) {
					get_template_part( 'template-parts/virtual/about' );
				} elseif ( 'archives' === $reimu_special_page_slug ) {
					get_template_part( 'template-parts/virtual/archives' );
				} elseif ( in_array( $reimu_special_page_slug, array( 'projects', 'friend' ), true ) ) {
					get_template_part( 'template-parts/virtual/' . $reimu_special_page_slug );
				} else {
					get_template_part( 'template-parts/content/content-page' );
				}
				?>
				<?php
				if ( ( ! $reimu_special_page_slug || in_array( $reimu_special_page_slug, array( 'about', 'friend' ), true ) ) && yneko_reimu_should_show_comments( get_the_ID() ) ) {
					comments_template();
				}
				?>
		</section>
	<?php endwhile; ?>
</div>
<?php
get_footer();
