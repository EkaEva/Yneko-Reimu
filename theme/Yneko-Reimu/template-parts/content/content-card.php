<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
$even      = isset( $args['even'] ) ? (bool) $args['even'] : true;
$side      = $even ? 'left' : 'right';
$cover_url = yneko_reimu_get_post_cover_url( get_the_ID() );
?>
<div class="post-wrapper">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-wrap ' . $side ); ?> data-aos="fade-up">
		<a class="post-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>"></a>
		<?php if ( yneko_reimu_post_is_sticky( get_the_ID() ) ) : ?>
			<div class="post-sticky"><?php echo esc_html( function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '置顶' ) : __( '置顶', 'yneko-reimu' ) ); ?></div>
		<?php endif; ?>
		<div class="post-cover <?php echo esc_attr( $side ); ?>">
			<img src="<?php echo esc_url( $cover_url ); ?>" data-src="<?php echo esc_url( $cover_url ); ?>" data-sizes="auto" alt="<?php the_title_attribute(); ?>" class="lazyload" loading="lazy" decoding="async" width="900" height="560">
		</div>
		<div class="post-info">
			<?php get_template_part( 'template-parts/meta/post-meta' ); ?>
			<h2 class="post-title"><?php the_title(); ?></h2>
			<div class="post-article"><?php echo esc_html( yneko_reimu_excerpt( get_the_ID() ) ); ?></div>
		</div>
	</article>
</div>
