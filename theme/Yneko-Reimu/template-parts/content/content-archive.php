<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="archive-post" data-aos="fade-up">
	<a class="archive-post-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>"></a>
	<div class="archive-post-date">
		<span><?php echo esc_html( get_the_date( 'm-d' ) ); ?></span>
		<small><?php echo esc_html( get_the_date( 'Y' ) ); ?></small>
	</div>
	<div class="archive-post-content">
		<h2 class="archive-post-title"><?php the_title(); ?></h2>
		<div class="archive-post-excerpt"><?php echo esc_html( yneko_reimu_excerpt( get_the_ID() ) ); ?></div>
	</div>
</div>
