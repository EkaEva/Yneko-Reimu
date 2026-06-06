<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_extensions_panel( $features, $third_party ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="extensions" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '扩展与第三方', 'Extensions and third-party resources' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '这些功能通常会加载额外脚本或连接第三方域名，因此从自定义器移到主设置页集中管理。视觉与布局仍在自定义器中实时预览。', 'These features usually load extra scripts or contact third-party domains, so they are managed here. Visual and layout options remain in the Customizer for live preview.' ); ?>
		<?php yneko_reimu_render_settings_feature_group( $features ); ?>
		<?php yneko_reimu_render_settings_third_party_group( $third_party ); ?>
	</section>
	<?php
}

function yneko_reimu_settings_feature_labels() {
	return array(
		'preloader_enable' => array( '加载动画', 'Loading animation' ),
		'top_enable'       => array( '回到顶部太极按钮', 'Back-to-top Taichi button' ),
		'triangle_badge'   => array( '右上角 GitHub 三角标', 'GitHub corner ribbon' ),
		'firework_enable'  => array( '鼠标烟花', 'Mouse firework' ),
		'pjax_enable'      => array( 'PJAX 软导航', 'PJAX navigation' ),
		'busuanzi_enable'  => array( '不蒜子统计', 'Busuanzi statistics' ),
		'katex_enable'     => array( 'KaTeX 数学公式', 'KaTeX math' ),
		'photoswipe_enable' => array( 'PhotoSwipe 图片灯箱', 'PhotoSwipe lightbox' ),
		'mermaid_enable'    => array( 'Mermaid 图表', 'Mermaid diagrams' ),
		'custom_cursor'     => array( '自定义鼠标指针', 'Custom cursor' ),
	);
}

function yneko_reimu_render_settings_feature_group( $features ) {
	yneko_reimu_settings_group_open( '前台增强', 'Front-end enhancements', '这些开关会改变前台加载、交互或内容增强行为。默认保持轻量。', 'These switches affect front-end loading, interactions, or content enhancements. Defaults stay lightweight.' );
	?>
		<div class="yneko-reimu-checkbox-grid">
			<?php foreach ( yneko_reimu_settings_feature_labels() as $key => $label ) : ?>
				<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( '1', $features[ $key ] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( $label[0], $label[1] ); ?></label>
			<?php endforeach; ?>
		</div>
	<?php
	yneko_reimu_settings_group_close();
}

function yneko_reimu_render_settings_third_party_group( $third_party ) {
	yneko_reimu_settings_group_open( '第三方资源', 'Third-party resources', '启用后可能连接第三方域名；需要隐私优先时可关闭功能或替换为自托管地址。', 'These options may contact third-party domains when enabled. For privacy-first sites, disable them or replace URLs with self-hosted resources.' );
	?>
		<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[third_party][live2d_enable]" value="1" <?php checked( '1', $third_party['live2d_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Live2D Widgets', 'Enable Live2D Widgets' ); ?></label>
		<?php yneko_reimu_render_settings_third_party_url( 'yneko-reimu-live2d-base', 'Live2D Widgets 资源地址', 'Live2D Widgets resource URL', 'live2d_base_url', $third_party ); ?>
		<?php yneko_reimu_render_settings_third_party_url( 'yneko-reimu-live2d-api', 'Live2D 模型 CDN 地址', 'Live2D model CDN URL', 'live2d_api_base_url', $third_party ); ?>
		<div class="yneko-reimu-field">
			<label class="yneko-reimu-field__label" for="yneko-reimu-vendor-cdn"><?php yneko_reimu_admin_bilingual_label( 'Vendor CDN 前缀', 'Vendor CDN base' ); ?></label>
			<input id="yneko-reimu-vendor-cdn" class="regular-text" type="url" name="yneko_reimu_settings[third_party][vendor_cdn_base]" value="<?php echo esc_attr( $third_party['vendor_cdn_base'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '用于 Reimu 扩展包的 CDN 前缀。默认使用 jsDelivr，需要隐私优先时可替换为自托管资源。', 'CDN prefix for Reimu extension packages. The default uses jsDelivr; replace it with self-hosted resources for a privacy-first setup.' ); ?>
		</div>
	<?php
	yneko_reimu_settings_group_close();
}

function yneko_reimu_render_settings_third_party_url( $id, $label_zh, $label_en, $key, $third_party ) {
	?>
	<div class="yneko-reimu-field">
		<label class="yneko-reimu-field__label" for="<?php echo esc_attr( $id ); ?>"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></label>
		<input id="<?php echo esc_attr( $id ); ?>" class="regular-text" type="url" name="yneko_reimu_settings[third_party][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $third_party[ $key ] ); ?>">
	</div>
	<?php
}
