<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="site-search">
	<div class="popup reimu-popup">
		<div class="reimu-search">
			<div class="reimu-search-input-icon"></div>
			<form id="reimu-search-input" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="reimu-search-field-popup"><?php esc_html_e( '搜索', 'yneko-reimu' ); ?></label>
				<input id="reimu-search-field-popup" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( '搜索.....', 'yneko-reimu' ); ?>">
			</form>
			<div class="popup-btn-close" role="button" aria-label="<?php esc_attr_e( '关闭搜索', 'yneko-reimu' ); ?>"></div>
		</div>
		<div class="reimu-results">
			<div id="reimu-stats"><?php esc_html_e( '输入关键词后按回车搜索。', 'yneko-reimu' ); ?></div>
			<div id="reimu-hits"></div>
			<div id="reimu-pagination" class="reimu-pagination"></div>
		</div>
		<img class="reimu-bg" src="<?php echo esc_url( yneko_reimu_get_search_bg_url() ); ?>" loading="lazy" decoding="async" fetchpriority="low" alt="">
	</div>
</div>
