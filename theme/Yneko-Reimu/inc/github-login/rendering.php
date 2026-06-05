<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_login_render_button( $redirect_to = '' ) {
	if ( ! yneko_reimu_github_login_is_configured() ) {
		return '';
	}

	$url = yneko_reimu_github_login_start_url( $redirect_to ? $redirect_to : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) );
	$url = add_query_arg( 'popup', '1', $url );

	return sprintf(
		'<a class="yneko-reimu-button" href="%1$s" data-reimu-github-popup data-no-pjax><span class="yneko-reimu-icon" aria-hidden="true">%2$s</span><span>%3$s</span></a>',
		esc_url( $url ),
		yneko_reimu_github_login_icon(),
		esc_html__( '使用 GitHub 登录', 'yneko-reimu' )
	);
}

function yneko_reimu_github_login_render_reimu_button() {
	$redirect_to = is_singular() ? get_permalink() : home_url( '/' );
	$button      = yneko_reimu_github_login_render_button( $redirect_to );
	$divider     = '<div class="reimu-login-divider"><span>' . esc_html__( '或使用 GitHub 登录', 'yneko-reimu' ) . '</span></div>';

	if ( ! $button ) {
		return;
	}

	echo '<div class="reimu-login-social">' . $divider . $button . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action( 'yneko_reimu_login_modal_social', 'yneko_reimu_github_login_render_reimu_button' );

add_action( 'reimu_wp_login_modal_social', 'yneko_reimu_github_login_render_reimu_button' );

function yneko_reimu_github_login_icon() {
	return '<svg viewBox="0 0 16 16" width="18" height="18" fill="currentColor" focusable="false" aria-hidden="true"><path d="M8 0C3.58 0 0 3.64 0 8.13c0 3.59 2.29 6.63 5.47 7.71.4.08.55-.18.55-.39 0-.19-.01-.83-.01-1.5-2.01.38-2.53-.5-2.69-.96-.09-.24-.48-.96-.82-1.15-.28-.15-.68-.53-.01-.54.63-.01 1.08.59 1.23.84.72 1.23 1.87.88 2.33.67.07-.53.28-.88.51-1.08-1.78-.21-3.64-.91-3.64-4.03 0-.89.31-1.62.82-2.19-.08-.21-.36-1.04.08-2.16 0 0 .67-.22 2.2.84A7.5 7.5 0 0 1 8 3.92c.68 0 1.36.09 2 .27 1.53-1.06 2.2-.84 2.2-.84.44 1.12.16 1.95.08 2.16.51.57.82 1.3.82 2.19 0 3.13-1.87 3.82-3.65 4.03.29.25.54.74.54 1.5 0 1.08-.01 1.95-.01 2.22 0 .21.15.47.55.39A8.03 8.03 0 0 0 16 8.13C16 3.64 12.42 0 8 0Z"></path></svg>';
}
