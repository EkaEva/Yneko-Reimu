<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! yneko_reimu_theme_mod_bool( 'yneko_reimu_show_theme_toggle', true ) ) {
	return;
}
?>
<button id="theme-toggle" class="nav-icon reimu-theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e( '切换明暗模式', 'yneko-reimu' ); ?>">
	<span id="nav-sun-btn" aria-hidden="true"></span>
	<span id="nav-moon-btn" aria-hidden="true"></span>
	<span id="nav-circle-half-stroke-btn" aria-hidden="true"></span>
</button>
