<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" aria-label="<?php esc_attr_e( '页面内容', 'yneko-reimu' ); ?>" class="sidebar-<?php echo esc_attr( yneko_reimu_sidebar_position() ); ?>">
	<?php get_sidebar(); ?>
	<section id="main" aria-label="<?php esc_attr_e( '主要内容', 'yneko-reimu' ); ?>">
		<?php if ( have_posts() ) : ?>
			<div class="archives-outer-wrap" data-aos="fade-up">
				<?php $reimu_current_year = ''; ?>
				<?php while ( have_posts() ) : ?>
					<?php
					the_post();
					$reimu_year = get_the_date( 'Y' );
					if ( $reimu_year !== $reimu_current_year ) :
						if ( '' !== $reimu_current_year ) :
							?>
								</ul>
							</section>
							<?php
						endif;
						$reimu_current_year = $reimu_year;
						?>
						<section class="archives-wrap" data-aos="fade-up">
							<div class="archive-year-wrap">
								<a href="<?php echo esc_url( get_year_link( (int) $reimu_year ) ); ?>" class="archive-year"><?php echo esc_html( $reimu_year ); ?></a>
							</div>
							<ul>
					<?php endif; ?>
								<li class="archive-article">
									<div class="archive-article-date">
										<span>
											<time class="dt-published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date( 'm-d' ) ); ?></time>
										</span>
									</div>
									<a class="archive-article-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</li>
				<?php endwhile; ?>
					</ul>
				</section>
			</div>
			<?php get_template_part( 'template-parts/components/pagination' ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content-none' ); ?>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
