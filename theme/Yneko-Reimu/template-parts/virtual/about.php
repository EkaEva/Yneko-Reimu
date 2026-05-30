<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
			<div class="reimu-empty reimu-virtual-empty">
				<h2><?php esc_html_e( '关于页待编辑', 'yneko-reimu' ); ?></h2>
				<p><?php esc_html_e( '创建 slug 为 about 的 WordPress 页面后，这里会自动显示你的页面正文。', 'yneko-reimu' ); ?></p>
			</div>
		</div>
		<?php echo yneko_reimu_sponsor_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</article>
