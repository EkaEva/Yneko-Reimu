<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_enqueue_optional_vendor_assets( $enable_aplayer, $external_comments ) {
	$main_script_deps = array();

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_firework_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-firework', yneko_reimu_vendor_url( 'mouse-firework@0.2.0/dist/index.umd.js' ), array(), '0.2.0', true );
		$main_script_deps[] = 'yneko-reimu-firework';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_busuanzi_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-busuanzi', yneko_reimu_vendor_url( 'busuanzi@2.3.0/bsz.pure.mini.js' ), array(), '2.3.0', true );
	}

	if ( $enable_aplayer ) {
		wp_enqueue_script( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.js' ), array(), '1.10.1', true );
		$main_script_deps[] = 'yneko-reimu-aplayer';
	}

	if ( yneko_reimu_meting_enabled() ) {
		wp_enqueue_script( 'yneko-reimu-meting', yneko_reimu_vendor_url( 'meting@2.0.1/dist/Meting.min.js' ), array( 'yneko-reimu-aplayer' ), '2.0.1', true );
		$main_script_deps[] = 'yneko-reimu-meting';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_live2d_widgets_enable', false ) ) {
		$live2d_base = yneko_reimu_live2d_base_url();
		wp_enqueue_style( 'yneko-reimu-live2d', $live2d_base . 'waifu.css', array(), '0.9.0' );
		wp_add_inline_style(
			'yneko-reimu-live2d',
			'#waifu{left:auto!important;right:0!important;z-index:99}#waifu-toggle{left:auto!important;right:0!important;margin-left:0!important;margin-right:-100px;padding:5px 5px 5px 2px;transition:margin-right 1s}#waifu-toggle.waifu-toggle-active{margin-right:-50px!important}#waifu-toggle.waifu-toggle-active:hover{margin-right:-30px!important}#waifu-tips{right:0;margin:-30px 20px 0 0}#waifu-tool{right:auto!important;left:-10px!important}'
		);
		wp_enqueue_script( 'yneko-reimu-live2d-core', $live2d_base . 'live2d.min.js', array(), '0.9.0', true );
		wp_enqueue_script( 'yneko-reimu-live2d-widget', $live2d_base . 'waifu-tips.js', array( 'yneko-reimu-live2d-core' ), '0.9.0', true );
		wp_add_inline_script(
			'yneko-reimu-live2d-widget',
			'window.addEventListener("load",function(){if(window.innerWidth<768||typeof window.initWidget!=="function"){return;}window.initWidget({waifuPath:"' . esc_js( $live2d_base . 'waifu-tips.json' ) . '",cdnPath:"' . esc_js( yneko_reimu_live2d_api_base_url() ) . '",tools:["hitokoto","switch-model","switch-texture","photo","info","quit"]});});'
		);
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_katex_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.css' ), array(), '0.16.24' );
		wp_enqueue_script( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.js' ), array(), '0.16.24', true );
		wp_enqueue_script( 'yneko-reimu-katex-auto', yneko_reimu_vendor_url( 'katex@0.16.24/dist/contrib/auto-render.min.js' ), array( 'yneko-reimu-katex' ), '0.16.24', true );
		$main_script_deps[] = 'yneko-reimu-katex-auto';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_photoswipe_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-photoswipe', yneko_reimu_vendor_url( 'photoswipe@5.4.4/dist/photoswipe.css' ), array(), '5.4.4' );
		wp_enqueue_style( 'yneko-reimu-photoswipe-enhance', YNEKO_REIMU_URI . '/assets/dist/reimu-photoswipe.css', array( 'yneko-reimu-photoswipe' ), yneko_reimu_asset_version( 'assets/dist/reimu-photoswipe.css' ) );
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_mermaid_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-mermaid', yneko_reimu_vendor_url( 'mermaid@11.12.0/dist/mermaid.min.js' ), array(), '11.12.0', true );
		$main_script_deps[] = 'yneko-reimu-mermaid';
	}

	if ( ! empty( $external_comments['waline_enable'] ) && '1' === (string) $external_comments['waline_enable'] && ! empty( $external_comments['waline_server_url'] ) ) {
		wp_enqueue_style( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.css' ), array(), '2.15.8' );
		wp_enqueue_script( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.js' ), array(), '2.15.8', true );
	}

	if ( ! empty( $external_comments['twikoo_enable'] ) && '1' === (string) $external_comments['twikoo_enable'] && ! empty( $external_comments['twikoo_env_id'] ) ) {
		wp_enqueue_script( 'yneko-reimu-twikoo', yneko_reimu_vendor_url( 'twikoo@1.6.42/dist/twikoo.all.min.js' ), array(), '1.6.42', true );
	}

	if ( ! empty( $external_comments['valine_enable'] ) && '1' === (string) $external_comments['valine_enable'] && ! empty( $external_comments['valine_app_id'] ) && ! empty( $external_comments['valine_app_key'] ) ) {
		wp_enqueue_script( 'yneko-reimu-valine', yneko_reimu_vendor_url( 'valine@1.5.3/dist/Valine.min.js' ), array(), '1.5.3', true );
	}

	return $main_script_deps;
}
