<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div id="content" aria-label="<?php esc_attr_e( '页面内容', 'yneko-reimu' ); ?>" class="sidebar-<?php echo esc_attr( yneko_reimu_sidebar_position() ); ?>">
	<?php get_sidebar(); ?>
	<section id="main" aria-label="<?php esc_attr_e( '主要内容', 'yneko-reimu' ); ?>">
			<?php get_template_part( 'template-parts/content/home-categories' ); ?>
			<?php if ( have_posts() ) : ?>
				<?php
					$reimu_index = 0;
					while ( have_posts() ) :
						the_post();
						get_template_part(
							'template-parts/content/content-card',
							null,
							array(
								'even' => 0 === $reimu_index % 2,
							)
						);
						$reimu_index++;
					endwhile;
					?>
				<?php get_template_part( 'template-parts/components/pagination' ); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content/content-none' ); ?>
			<?php endif; ?>
	</section>
</div>
<?php
get_footer();
