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
		<?php
		$reimu_loader_image  = function_exists( 'yneko_reimu_visual_asset_url' ) ? yneko_reimu_visual_asset_url( 'yneko_reimu_preloader_image_url', '' ) : '';
		$reimu_loader_rotate = yneko_reimu_theme_mod_bool( 'yneko_reimu_preloader_image_rotate', true );
		$reimu_loader_texts  = function_exists( 'yneko_reimu_preloader_texts' ) ? yneko_reimu_preloader_texts() : array( 'zh_CN' => __( '未来有你...', 'yneko-reimu' ), 'en_US' => 'Loading...' );
		$reimu_language      = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : 'zh_CN';
		$reimu_loader_text   = $reimu_loader_texts[ $reimu_language ] ?? $reimu_loader_texts['zh_CN'];
		$reimu_loader_size   = function_exists( 'yneko_reimu_preloader_image_size' ) ? yneko_reimu_preloader_image_size() : 150;
		?>
		<div class="loading-taichi<?php echo $reimu_loader_rotate ? ' rotate' : ''; ?>">
			<?php if ( $reimu_loader_image ) : ?>
				<img src="<?php echo esc_url( $reimu_loader_image ); ?>" alt="" width="<?php echo esc_attr( $reimu_loader_size ); ?>" height="<?php echo esc_attr( $reimu_loader_size ); ?>" decoding="async">
			<?php else : ?>
				<?php yneko_reimu_render_taichi_svg( $reimu_loader_size ); ?>
			<?php endif; ?>
		</div>
		<div class="loading-word"><?php echo esc_html( $reimu_loader_text ); ?></div>
	</div>
</div>
<div id="copy-tooltip"></div>
<div id="heatmap-tooltip"></div>
