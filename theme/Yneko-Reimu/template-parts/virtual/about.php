<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_about_post_id = is_singular( 'page' ) ? get_the_ID() : 0;

if ( $reimu_about_post_id && function_exists( 'yneko_reimu_i18n_is_english_request' ) && yneko_reimu_i18n_is_english_request() && function_exists( 'yneko_reimu_i18n_get_translation_id' ) ) {
	$reimu_about_translation_id = yneko_reimu_i18n_get_translation_id( $reimu_about_post_id, 'en_US' );
	if ( $reimu_about_translation_id && $reimu_about_translation_id !== $reimu_about_post_id ) {
		$reimu_about_post_id = $reimu_about_translation_id;
	}
}

$reimu_about_content = $reimu_about_post_id ? trim( (string) get_post_field( 'post_content', $reimu_about_post_id ) ) : '';
?>
<article id="page-about" class="h-entry article reimu-virtual-page">
	<div class="article-inner" data-aos="fade-up">
		<div class="article-meta">
			<div class="article-date">
				<span class="article-date-link icon-calendar" data-aos="zoom-in">
					<time datetime="<?php echo esc_attr( gmdate( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( gmdate( 'Y-m-d' ) ); ?></time>
					<time style="display:none" id="post-update-time"><?php echo esc_html( gmdate( 'Y-m-d' ) ); ?></time>
				</span>
			</div>
		</div>
		<div class="hr-line"></div>
		<div class="e-content article-entry" itemprop="articleBody">
			<?php yneko_reimu_render_heatmap(); ?>
			<?php if ( $reimu_about_post_id && '' !== $reimu_about_content ) : ?>
				<?php
				$reimu_about_post = get_post( $reimu_about_post_id );
				echo apply_filters( 'the_content', $reimu_about_post ? $reimu_about_post->post_content : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( '页面：', 'yneko-reimu' ),
						'after'  => '</div>',
					)
				);
				?>
			<?php else : ?>
				<div class="reimu-empty reimu-virtual-empty">
					<h2><?php esc_html_e( '关于页待编辑', 'yneko-reimu' ); ?></h2>
					<p><?php esc_html_e( '创建 slug 为 about 的 WordPress 页面后，这里会自动显示你的页面正文。', 'yneko-reimu' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php echo yneko_reimu_sponsor_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php get_template_part( 'template-parts/meta/virtual-page-footer' ); ?>
	</div>
</article>
