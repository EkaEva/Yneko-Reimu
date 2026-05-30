<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! yneko_reimu_is_meta_enabled( get_the_ID(), '_yneko_reimu_copyright', 'yneko_reimu_show_copyright', true ) ) {
	return;
}
?>
<blockquote class="article-copyright">
	<p>
		<strong><span class="icon icon-user"></span><?php esc_html_e( '本文作者：', 'yneko-reimu' ); ?></strong><?php the_author(); ?>
	</p>
	<p>
		<strong><span class="icon icon-link"></span><?php esc_html_e( '本文链接：', 'yneko-reimu' ); ?></strong><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_permalink() ); ?></a>
	</p>
	<p>
		<strong><span class="icon icon-copyright"></span><?php esc_html_e( '本文版权：', 'yneko-reimu' ); ?></strong>
		<?php
		printf(
			/* translators: %s: license link. */
			wp_kses_post( __( '本博客所有文章除特别声明外，均采用 %s 许可协议。转载请注明出处！', 'yneko-reimu' ) ),
			'<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" rel="nofollow noopener noreferrer" target="_blank"><span class="icon-creative-commons"></span>BY-NC-SA</a>'
		);
		?>
	</p>
	<span class="article-copyright-bg icon-creative-commons"></span>
</blockquote>
