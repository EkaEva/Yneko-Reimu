<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! yneko_reimu_feature_enabled( 'preloader_enable', false ) ) {
	return;
}
?>
<div id="loader">
	<div class="loading-bg loading-left-bg"></div>
	<div class="loading-bg loading-right-bg"></div>
	<div class="spinner-box">
		<div class="loading-taichi rotate">
			<?php yneko_reimu_render_taichi_svg( 150 ); ?>
		</div>
		<?php
		$reimu_loader_text = yneko_reimu_get_theme_mod( 'yneko_reimu_preloader_text', __( '未来有你...', 'yneko-reimu' ) );
		if ( __( '少女祈祷中...', 'yneko-reimu' ) === $reimu_loader_text || '少女祈祷中...' === $reimu_loader_text ) {
			$reimu_loader_text = __( '未来有你...', 'yneko-reimu' );
		}
		?>
		<div class="loading-word"><?php echo esc_html( $reimu_loader_text ); ?></div>
	</div>
</div>
<div id="copy-tooltip"></div>
<div id="heatmap-tooltip"></div>
