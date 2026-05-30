<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" aria-label="<?php esc_attr_e( '页面内容', 'yneko-reimu' ); ?>" class="sidebar-<?php echo esc_attr( yneko_reimu_sidebar_position( get_queried_object_id() ) ); ?>">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<?php get_sidebar(); ?>
		<section id="main" aria-label="<?php esc_attr_e( '主要内容', 'yneko-reimu' ); ?>">
				<?php get_template_part( 'template-parts/content/content-single' ); ?>
				<?php
				if ( yneko_reimu_should_show_comments( get_the_ID() ) ) {
					comments_template();
				}
				?>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
