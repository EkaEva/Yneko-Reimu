<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="reimu-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="reimu-search-field"><?php esc_html_e( '搜索', 'yneko-reimu' ); ?></label>
	<input id="reimu-search-field" class="reimu-search-form__field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( '搜索文章...', 'yneko-reimu' ); ?>">
	<button class="reimu-search-form__submit" type="submit" aria-label="<?php esc_attr_e( '提交搜索', 'yneko-reimu' ); ?>">⌕</button>
</form>
