<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = yneko_reimu_home_category_capsules();
?>
<div class="post-categories-wrapper" data-aos="fade-up">
	<?php foreach ( $categories as $category ) : ?>
		<div class="post-categories-wrap">
			<a class="post-link" href="<?php echo esc_url( $category['url'] ); ?>" aria-label="<?php echo esc_attr( $category['title'] ); ?>" title="<?php echo esc_attr( $category['title'] ); ?>"></a>
			<div class="post-categories-cover">
				<img src="<?php echo esc_url( $category['cover'] ); ?>" data-src="<?php echo esc_url( $category['cover'] ); ?>" data-sizes="auto" alt="<?php echo esc_attr( $category['title'] ); ?>" class="lazyload">
				<h2><?php echo esc_html( $category['title'] ); ?></h2>
				<h3><?php echo esc_html( $category['count'] ); ?></h3>
			</div>
		</div>
	<?php endforeach; ?>
</div>
