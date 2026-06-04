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

function yneko_reimu_github_login_enqueue_styles() {
	$login_logo = get_site_icon_url( 96 );
	if ( ! $login_logo ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$login_logo     = $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'thumbnail' ) : '';
	}
	if ( ! $login_logo ) {
		$login_logo = YNEKO_REIMU_URI . '/assets/images/avatar.svg';
	}
	$password_hidden_icon  = YNEKO_REIMU_URI . '/assets/images/icons/password-hidden.svg';
	$password_visible_icon = YNEKO_REIMU_URI . '/assets/images/icons/password-visible.svg';
	$css = '
		.reimu-login-social { margin: 0; }
		.yneko-reimu-button {
			display: inline-flex;
			width: 100%;
			min-height: 42px;
			box-sizing: border-box;
			align-items: center;
			justify-content: center;
			gap: 10px;
			padding: 0 18px;
			color: #fff !important;
			background: #24292f;
			border: 0;
			border-radius: 8px;
			box-shadow: 0 8px 18px rgba(36, 41, 47, .18);
			font-weight: 700;
			text-decoration: none !important;
			transition: opacity .2s ease, transform .2s ease, box-shadow .2s ease;
		}
		.yneko-reimu-button:hover {
			opacity: .92;
			transform: translateY(-1px);
			box-shadow: 0 10px 22px rgba(36, 41, 47, .24);
		}
		.yneko-reimu-icon { display: inline-flex; }
		.reimu-login-divider {
			display: flex;
			align-items: center;
			gap: 12px;
			margin: 0 0 10px;
			color: #999;
			font-size: 13px;
		}
		.reimu-login-divider:before,
		.reimu-login-divider:after {
			content: "";
			height: 1px;
			flex: 1;
			background: rgba(255, 82, 82, .18);
		}
		html,
		body.login {
			min-height: 100%;
			background: #eee !important;
		}
		body.login {
			display: grid;
			min-height: 100vh;
			place-items: center;
			margin: 0;
			padding: 32px 16px;
			box-sizing: border-box;
		}
		body.login #login {
			width: min(560px, 100%);
			padding: 0;
			box-sizing: border-box;
		}
		body.login h1 a {
			width: 68px;
			height: 68px;
			margin: 0 auto 18px;
			background-image: url("' . esc_url( $login_logo ) . '") !important;
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			border-radius: 50%;
			filter: drop-shadow(0 8px 18px rgba(255, 82, 82, .12));
		}
		body.login #loginform,
		body.login #lostpasswordform,
		body.login #registerform {
			margin-top: 0;
			padding: 34px 44px 30px;
			background: #fff;
			border: 1px solid rgba(255, 82, 82, .36);
			border-radius: 12px;
			box-shadow: 0 22px 60px rgba(0, 0, 0, .12);
		}
		body.login form p {
			margin: 0 0 18px;
		}
		body.login form p.submit {
			display: flex;
			justify-content: flex-end;
			margin: 24px 0 0;
			padding: 0;
		}
		body.login .user-pass-wrap {
			margin-bottom: 18px;
		}
		body.login label {
			color: #ff5252;
			font-weight: 700;
		}
		body.login form .input,
		body.login input[type="text"],
		body.login input[type="email"],
		body.login input[type="password"] {
			width: 100%;
			min-height: 42px;
			margin-top: 8px;
			padding: 0 14px;
			box-sizing: border-box;
			color: #444;
			background: #f5f5f5;
			border: 1px solid rgba(255, 82, 82, .34);
			border-radius: 8px;
			box-shadow: none;
			font-size: 16px;
		}
		body.login form .input:focus,
		body.login input[type="text"]:focus,
		body.login input[type="email"]:focus,
		body.login input[type="password"]:focus {
			border-color: rgba(255, 82, 82, .72);
			box-shadow: 0 0 0 3px rgba(255, 82, 82, .10);
			outline: none;
		}
		body.login .button:not(.wp-hide-pw),
		body.login .button-secondary:not(.wp-hide-pw),
		body.login .button-primary:not(.wp-hide-pw),
		body.login .wp-generate-pw:not(.wp-hide-pw),
		body.login .wp-cancel-pw:not(.wp-hide-pw),
		body.login .reset-pass-submit .button {
			display: inline-flex !important;
			min-height: 40px;
			align-items: center;
			justify-content: center;
			padding: 0 22px;
			border-radius: 8px;
			font-size: 14px;
			font-weight: 700;
			line-height: 40px;
			text-decoration: none;
			box-shadow: none;
		}
		body.login .button-primary {
			min-height: 40px;
			padding: 0 22px;
			background: #1fbf75;
			border: 0;
			border-radius: 8px;
			box-shadow: 0 8px 18px rgba(31, 191, 117, .18);
			font-weight: 700;
			line-height: 40px;
		}
		body.login .button-secondary:not(.wp-hide-pw),
		body.login .wp-generate-pw:not(.wp-hide-pw),
		body.login .wp-cancel-pw:not(.wp-hide-pw) {
			color: #2f64ff;
			background: #fff;
			border: 1px solid rgba(47, 100, 255, .38);
		}
		body.login .reset-pass-submit {
			display: flex;
			justify-content: flex-end;
			gap: 10px;
			margin-top: 24px;
		}
		body.login .wp-pwd {
			position: relative;
			display: grid;
			margin-top: 8px;
		}
		body.login .wp-pwd input[type="password"],
		body.login .wp-pwd input[type="text"] {
			grid-area: 1 / 1;
			margin-top: 0;
			padding-right: 54px;
		}
		body.login .wp-pwd .wp-hide-pw,
		body.login .wp-pwd .wp-hide-pw.button,
		body.login .wp-pwd .wp-hide-pw.button.button-secondary,
		body.login .wp-pwd .wp-hide-pw.button-secondary {
			position: relative !important;
			top: auto !important;
			right: auto !important;
			bottom: auto !important;
			display: inline-flex !important;
			grid-area: 1 / 1;
			appearance: none !important;
			width: 30px !important;
			min-width: 0 !important;
			height: 30px !important;
			min-height: 0 !important;
			align-items: center !important;
			align-self: center !important;
			justify-content: center !important;
			justify-self: end !important;
			margin: 0 10px 0 0 !important;
			padding: 0 !important;
			color: #ff5252 !important;
			background: transparent !important;
			background-image: none !important;
			border: 0 !important;
			border-color: transparent !important;
			border-radius: 0 !important;
			box-shadow: none !important;
			line-height: 1 !important;
			text-decoration: none !important;
			transform: none !important;
		}
		body.login .wp-pwd .wp-hide-pw .dashicons {
			display: none !important;
		}
		body.login .wp-pwd .wp-hide-pw::before {
			display: block;
			width: 19px;
			height: 19px;
			content: "";
			background-color: currentColor;
			mask: url("' . esc_url( $password_hidden_icon ) . '") center / contain no-repeat;
			-webkit-mask: url("' . esc_url( $password_hidden_icon ) . '") center / contain no-repeat;
		}
		body.login .wp-pwd .wp-hide-pw[aria-pressed="true"]::before {
			mask-image: url("' . esc_url( $password_visible_icon ) . '");
			-webkit-mask-image: url("' . esc_url( $password_visible_icon ) . '");
		}
		body.login .wp-pwd .wp-hide-pw:has(.dashicons-hidden)::before {
			mask-image: url("' . esc_url( $password_visible_icon ) . '");
			-webkit-mask-image: url("' . esc_url( $password_visible_icon ) . '");
		}
		body.login .button-primary:hover,
		body.login .button-primary:focus {
			background: #1fbf75;
			box-shadow: 0 10px 22px rgba(31, 191, 117, .24);
			opacity: .92;
		}
		body.login .button-secondary:not(.wp-hide-pw):hover,
		body.login .button-secondary:not(.wp-hide-pw):focus,
		body.login .wp-generate-pw:not(.wp-hide-pw):hover,
		body.login .wp-generate-pw:not(.wp-hide-pw):focus,
		body.login .wp-cancel-pw:not(.wp-hide-pw):hover,
		body.login .wp-cancel-pw:not(.wp-hide-pw):focus {
			color: #2f64ff;
			background: #fff;
			border-color: rgba(47, 100, 255, .55);
			box-shadow: 0 10px 22px rgba(47, 100, 255, .12);
			opacity: .92;
		}
		body.login .wp-pwd .wp-hide-pw:hover,
		body.login .wp-pwd .wp-hide-pw:focus {
			color: #ff5252 !important;
			background: transparent !important;
			border: 0 !important;
			box-shadow: none !important;
			opacity: 1;
		}
		body.login .wp-pwd .wp-hide-pw:focus {
			outline: 0 !important;
		}
		body.login .wp-pwd .wp-hide-pw:focus-visible {
			outline: 0 !important;
		}
		body.login #nav,
		body.login #backtoblog {
			margin: 16px 0 0;
			text-align: center;
		}
		body.login #nav a,
		body.login #backtoblog a,
		body.login .privacy-policy-page-link a {
			color: #ff5252;
			text-decoration: none;
		}
		body.login .message,
		body.login .notice,
		body.login #login_error {
			width: 100%;
			margin: 0 0 18px;
			padding: 14px 18px;
			box-sizing: border-box;
			background: #fff;
			border-left-color: #ff5252;
			border-radius: 8px;
		}
		body.login .language-switcher {
			width: min(560px, 100%);
			margin: 18px auto 0;
			padding: 0;
			text-align: center;
		}
		body.login .language-switcher form {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			margin: 0;
			padding: 0;
			background: transparent;
			border: 0;
			box-shadow: none;
		}
		body.login .language-switcher select {
			min-height: 38px;
			border-color: rgba(255, 82, 82, .28);
			border-radius: 8px;
		}
		body.login .language-switcher .button {
			min-height: 38px;
			border-color: rgba(255, 82, 82, .28);
			border-radius: 8px;
			color: #ff5252;
			background: #fff;
		}
		body.login .privacy-policy-page-link {
			width: min(560px, 100%);
			margin: 14px auto 0;
			text-align: center;
		}
		@media screen and (max-width: 560px) {
			body.login {
				padding: 20px 12px;
			}
			body.login #loginform,
			body.login #lostpasswordform,
			body.login #registerform {
				padding: 28px 22px 24px;
			}
		}
	';

	wp_register_style( 'yneko-reimu-github-login', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-github-login' );
	wp_add_inline_style( 'yneko-reimu-github-login', $css );
}

add_action( 'wp_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' );

add_action( 'login_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' );
