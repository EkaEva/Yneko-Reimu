<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer id="footer" aria-label="<?php esc_attr_e( '站点页脚', 'yneko-reimu' ); ?>">
	<div style="width:100%;overflow:hidden">
		<div class="footer-line"></div>
	</div>
	<div id="footer-info">
		<div>
			<span class="icon-copyright"></span>
			<?php echo esc_html( ltrim( yneko_reimu_footer_copyright(), "© \t\n\r\0\x0B" ) ); ?>
			<span class="footer-info-sep rotate"></span>
			<a href="https://github.com/EkaEva/Yneko-Reimu" rel="noopener noreferrer" target="_blank"><?php bloginfo( 'name' ); ?></a>
		</div>
		<div>
			<?php
			printf(
				/* translators: 1: WordPress link, 2: Reimu link. */
				wp_kses_post( __( '基于 %1$s，Theme 基于 %2$s 改编', 'yneko-reimu' ) ),
				'<a href="https://wordpress.org/" rel="noopener nofollow noreferrer" target="_blank">WordPress</a>',
				'<a href="https://github.com/D-Sketon/hexo-theme-reimu" rel="noopener nofollow noreferrer" target="_blank">Reimu</a>'
			);
			?>
		</div>
		<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_footer_extra_attribution', '' ) ) : ?>
			<div><?php echo esc_html( yneko_reimu_get_theme_mod( 'yneko_reimu_footer_extra_attribution', '' ) ); ?></div>
		<?php endif; ?>
		<div>
			<span class="icon-brush"></span>
			<?php echo esc_html( yneko_reimu_total_word_count() ); ?>
			&nbsp;|&nbsp;
			<span class="icon-coffee"></span>
			<?php echo esc_html( wp_count_posts( 'post' )->publish ); ?> <?php esc_html_e( '篇文章', 'yneko-reimu' ); ?>
		</div>
		<div>
			<span class="icon-eye"></span>
			<?php esc_html_e( '总访问量', 'yneko-reimu' ); ?>&nbsp;<?php echo yneko_reimu_site_pv_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			&nbsp;|&nbsp;
			<span class="icon-user"></span>
			<?php esc_html_e( '总访客量', 'yneko-reimu' ); ?>&nbsp;<?php echo yneko_reimu_site_uv_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</footer>
