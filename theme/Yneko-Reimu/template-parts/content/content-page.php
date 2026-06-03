<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="page-<?php echo esc_attr( get_post_field( 'post_name', get_the_ID() ) ); ?>" <?php post_class( 'h-entry article' ); ?>>
	<div class="article-inner" data-aos="fade-up">
		<div class="article-meta">
			<div class="article-date">
				<span class="article-date-link icon-calendar" data-aos="zoom-in">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></time>
					<time style="display:none" id="post-update-time" datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>" itemprop="dateModified"><?php echo esc_html( get_the_modified_date( 'Y-m-d' ) ); ?></time>
				</span>
			</div>
		</div>
		<?php $summary = yneko_reimu_get_post_meta( get_the_ID(), '_yneko_reimu_summary', true ); ?>
		<?php if ( $summary ) : ?>
			<blockquote class="article-summary"><?php echo esc_html( $summary ); ?></blockquote>
		<?php endif; ?>
		<div class="hr-line"></div>
		<div class="e-content article-entry" itemprop="articleBody">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( '页面：', 'yneko-reimu' ),
				'after'  => '</div>',
			)
		);
		?>
		</div>
		<?php echo yneko_reimu_sponsor_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php get_template_part( 'template-parts/meta/post-share' ); ?>
	</div>
</article>
