<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_archive_query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
	)
);
?>
<div class="archives-outer-wrap" data-aos="fade-up">
	<?php
	$reimu_tags = get_tags(
		array(
			'hide_empty' => true,
			'number'     => 40,
		)
	);
	if ( $reimu_tags ) :
		?>
		<div class="tag-wrap">
			<?php foreach ( $reimu_tags as $reimu_tag ) : ?>
				<div class="archives-tag-list-item" data-aos="fade-up">
					<a class="archives-tag-list-link" href="<?php echo esc_url( get_tag_link( $reimu_tag ) ); ?>"><?php echo esc_html( $reimu_tag->name ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php
	$reimu_categories = get_categories(
		array(
			'hide_empty' => true,
			'number'     => 40,
		)
	);
	if ( $reimu_categories ) :
		?>
		<div class="category-wrap">
			<?php foreach ( $reimu_categories as $reimu_category ) : ?>
				<div class="archives-category-list-item" data-aos="fade-up">
					<a class="archives-category-list-link" href="<?php echo esc_url( get_category_link( $reimu_category ) ); ?>"><?php echo esc_html( $reimu_category->name ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $reimu_archive_query->have_posts() ) : ?>
		<?php $reimu_current_year = ''; ?>
		<?php while ( $reimu_archive_query->have_posts() ) : ?>
			<?php
			$reimu_archive_query->the_post();
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
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<section class="reimu-empty reimu-virtual-empty">
			<h2><?php esc_html_e( '还没有文章', 'yneko-reimu' ); ?></h2>
			<p><?php esc_html_e( '发布文章后，这里会按年份生成归档列表。', 'yneko-reimu' ); ?></p>
		</section>
	<?php endif; ?>
</div>
