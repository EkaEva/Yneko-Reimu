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
			<?php bloginfo( 'name' ); ?>
		</div>
		<div>
			<?php esc_html_e( '基于', 'yneko-reimu' ); ?>&nbsp;<a href="https://wordpress.org/" rel="noopener nofollow noreferrer" target="_blank">WordPress</a>&nbsp;
			Theme<?php esc_html_e( '基于', 'yneko-reimu' ); ?><a href="https://github.com/D-Sketon/hexo-theme-reimu" rel="noopener nofollow noreferrer" target="_blank">Reimu</a><?php esc_html_e( '改编', 'yneko-reimu' ); ?>
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
			<?php esc_html_e( '总访问量', 'yneko-reimu' ); ?>&nbsp;<?php echo esc_html( number_format_i18n( yneko_reimu_get_site_pv() ) ); ?>
			&nbsp;|&nbsp;
			<span class="icon-user"></span>
			<?php esc_html_e( '总访客量', 'yneko-reimu' ); ?>&nbsp;<?php echo esc_html( number_format_i18n( yneko_reimu_get_site_uv() ) ); ?>
		</div>
	</div>
</footer>
