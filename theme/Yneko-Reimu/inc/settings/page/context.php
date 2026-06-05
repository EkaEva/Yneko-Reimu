<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_page_context() {
	$settings = yneko_reimu_settings();

	return array(
		'settings'             => $settings,
		'oauth'                => yneko_reimu_settings_github_oauth(),
		'builtin_pages'        => yneko_reimu_settings_builtin_pages(),
		'i18n'                 => isset( $settings['i18n'] ) && is_array( $settings['i18n'] ) ? wp_parse_args( $settings['i18n'], yneko_reimu_i18n_defaults() ) : yneko_reimu_i18n_defaults(),
		'search'               => yneko_reimu_settings_search(),
		'features'             => yneko_reimu_settings_features(),
		'player'               => yneko_reimu_settings_player(),
		'third_party'          => yneko_reimu_settings_third_party(),
		'external_comments'    => yneko_reimu_settings_external_comments(),
		'auth_security'        => function_exists( 'yneko_reimu_settings_auth_security' ) ? yneko_reimu_settings_auth_security() : array(),
		'security'             => function_exists( 'yneko_reimu_settings_security' ) ? yneko_reimu_settings_security() : array(),
		'review_badges'        => yneko_reimu_admin_review_badge_counts(),
		'callback'             => function_exists( 'yneko_reimu_github_login_callback_url' ) ? yneko_reimu_github_login_callback_url() : add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() ),
		'admin_totp'           => yneko_reimu_admin_current_user_totp_payload(),
		'admin_totp_available' => function_exists( 'yneko_reimu_user_2fa_enabled' ) && function_exists( 'yneko_reimu_totp_generate_secret' ),
	);
}
