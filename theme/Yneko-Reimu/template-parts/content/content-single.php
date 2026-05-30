<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php echo esc_attr( get_post_field( 'post_name', get_the_ID() ) ); ?>" <?php post_class( 'h-entry article' ); ?> itemscope itemtype="https://schema.org/BlogPosting">
	<div class="article-inner" data-aos="fade-up">
		<div class="article-meta">
			<div class="article-date">
				<span class="article-date-link icon-calendar" data-aos="zoom-in">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></time>
					<time style="display: none;" id="post-update-time" datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>" itemprop="dateModified"><?php echo esc_html( get_the_modified_date( 'Y-m-d' ) ); ?></time>
				</span>
				<?php if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_update_time', false ) ) : ?>
					<span class="article-date-link icon-calendar-plus" data-aos="zoom-in">
						<time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>" itemprop="dateModified"><?php echo esc_html( get_the_modified_date( 'Y-m-d' ) ); ?></time>
					</span>
				<?php endif; ?>
			</div>
		</div>
		<?php $summary = yneko_reimu_get_post_meta( get_the_ID(), '_yneko_reimu_summary', true ); ?>
		<div class="hr-line"></div>
		<div class="e-content article-entry" itemprop="articleBody">
		<?php if ( $summary ) : ?>
			<blockquote class="article-summary" data-aos="zoom-in"><p><?php echo esc_html( $summary ); ?></p></blockquote>
		<?php endif; ?>
		<?php
		get_template_part( 'template-parts/meta/post-outdated' );
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( '页面：', 'yneko-reimu' ),
				'after'  => '</div>',
			)
		);
		?>
		</div>
		<footer class="article-footer">
			<?php
			get_template_part( 'template-parts/meta/post-copyright' );
			get_template_part( 'template-parts/meta/post-terms' );
			?>
			<span class="article-visitor-link" data-aos="zoom-in" aria-label="<?php esc_attr_e( '阅读量', 'yneko-reimu' ); ?>">
				<?php echo yneko_reimu_view_count_text( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
			<?php if ( yneko_reimu_should_show_comments( get_the_ID() ) ) : ?>
				<a class="article-comment-link" data-aos="zoom-in" data-no-pjax href="<?php echo esc_url( get_permalink() . '#comments' ); ?>">
					<?php echo yneko_reimu_comment_count_text( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</footer>
	</div>
	<?php

	if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_post_nav', true ) ) :
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		if ( $prev_post || $next_post ) :
			?>
			<nav id="article-nav" aria-label="<?php esc_attr_e( '文章导航', 'yneko-reimu' ); ?>" data-aos="fade-up">
				<?php if ( $prev_post ) : ?>
					<div class="article-nav-link-wrap left">
						<img data-src="<?php echo esc_url( yneko_reimu_get_post_cover_url( $prev_post->ID ) ); ?>" data-sizes="auto" alt="<?php echo esc_attr( get_the_title( $prev_post ) ); ?>" class="lazyload">
						<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '上一篇：%s', 'yneko-reimu' ), get_the_title( $prev_post ) ) ); ?>" title="<?php echo esc_attr( get_the_title( $prev_post ) ); ?>"></a>
						<div class="article-nav-caption"><?php esc_html_e( '上一篇', 'yneko-reimu' ); ?></div>
						<h3 class="article-nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></h3>
					</div>
				<?php endif; ?>
				<?php if ( $next_post ) : ?>
					<div class="article-nav-link-wrap right">
						<img data-src="<?php echo esc_url( yneko_reimu_get_post_cover_url( $next_post->ID ) ); ?>" data-sizes="auto" alt="<?php echo esc_attr( get_the_title( $next_post ) ); ?>" class="lazyload">
						<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '下一篇：%s', 'yneko-reimu' ), get_the_title( $next_post ) ) ); ?>" title="<?php echo esc_attr( get_the_title( $next_post ) ); ?>"></a>
						<div class="article-nav-caption"><?php esc_html_e( '下一篇', 'yneko-reimu' ); ?></div>
						<h3 class="article-nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></h3>
					</div>
				<?php endif; ?>
			</nav>
			<?php
		endif;
	endif;
	?>
</article>
