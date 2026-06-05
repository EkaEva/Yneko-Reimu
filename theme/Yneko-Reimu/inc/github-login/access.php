<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_current_user_can_access_admin() {
	return current_user_can( 'manage_options' );
}

function yneko_reimu_show_frontend_admin_toolbar() {
	if ( ! yneko_reimu_current_user_can_access_admin() || is_admin() ) {
		return false;
	}

	if ( ! function_exists( 'yneko_reimu_settings_features' ) ) {
		return false;
	}

	$features = yneko_reimu_settings_features();
	return '1' === ( $features['show_admin_toolbar'] ?? '0' );
}

function yneko_reimu_hide_admin_bar_for_comment_users( $show ) {
	if ( is_user_logged_in() && ! yneko_reimu_show_frontend_admin_toolbar() ) {
		return false;
	}

	return $show;
}

add_filter( 'show_admin_bar', 'yneko_reimu_hide_admin_bar_for_comment_users' );

function yneko_reimu_block_comment_users_from_admin() {
	if ( ! is_user_logged_in() || yneko_reimu_current_user_can_access_admin() ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}

add_action( 'admin_init', 'yneko_reimu_block_comment_users_from_admin' );

function yneko_reimu_hide_frontend_plugin_toolbar_notices() {
	if ( ! is_user_logged_in() || yneko_reimu_show_frontend_admin_toolbar() ) {
		return;
	}

	if ( ! yneko_reimu_current_user_can_access_admin() ) {
		return;
	}

	?>
	<style id="yneko-reimu-admin-toolbar-compat">
		#rank-math-analytics-stats-wrapper,
		.rank-math-analytics-stats-wrapper,
		.rank-math-analytics-stats,
		.rank-math-analytics-notice,
		.rank-math-pro-cta,
		#wpadminbar {
			display: none !important;
		}
		html {
			margin-top: 0 !important;
		}
		* html body {
			margin-top: 0 !important;
		}
	</style>
	<?php
}

add_action( 'wp_head', 'yneko_reimu_hide_frontend_plugin_toolbar_notices', 1 );
