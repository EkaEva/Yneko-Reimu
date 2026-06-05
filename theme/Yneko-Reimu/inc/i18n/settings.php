<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_i18n_defaults() {
	return array(
		'enabled'       => '1',
		'default'       => 'zh_CN',
		'en_prefix'     => 'en',
		'zh_label'      => '简体中文',
		'en_label'      => 'English',
	);
}

function yneko_reimu_i18n_settings() {
	$options  = get_option( 'yneko_reimu_settings', array() );
	$options  = is_array( $options ) ? $options : array();
	$settings = isset( $options['i18n'] ) && is_array( $options['i18n'] ) ? $options['i18n'] : array();
	return wp_parse_args( $settings, yneko_reimu_i18n_defaults() );
}

function yneko_reimu_i18n_enabled() {
	$settings = yneko_reimu_i18n_settings();
	return ! empty( $settings['enabled'] );
}

function yneko_reimu_i18n_default_language() {
	$settings = yneko_reimu_i18n_settings();
	return 'en_US' === $settings['default'] ? 'en_US' : 'zh_CN';
}

function yneko_reimu_i18n_languages() {
	$settings = yneko_reimu_i18n_settings();
	return array(
		'zh_CN' => array(
			'code'   => 'zh_CN',
			'slug'   => '',
			'label'  => $settings['zh_label'] ? $settings['zh_label'] : '简体中文',
			'locale' => 'zh_CN',
		),
		'en_US' => array(
			'code'   => 'en_US',
			'slug'   => trim( sanitize_title( $settings['en_prefix'] ? $settings['en_prefix'] : 'en' ), '/' ),
			'label'  => $settings['en_label'] ? $settings['en_label'] : 'English',
			'locale' => 'en_US',
		),
	);
}

function yneko_reimu_i18n_language_exists( $language ) {
	return isset( yneko_reimu_i18n_languages()[ $language ] );
}

function yneko_reimu_i18n_normalize_language( $language ) {
	$language = (string) $language;
	$map      = array(
		'zh_CN' => 'zh_CN',
		'zh_cn' => 'zh_CN',
		'zh-cn' => 'zh_CN',
		'en_US' => 'en_US',
		'en_us' => 'en_US',
		'en-us' => 'en_US',
	);
	return isset( $map[ $language ] ) ? $map[ $language ] : '';
}

function yneko_reimu_i18n_url_prefix() {
	$languages = yneko_reimu_i18n_languages();
	return $languages['en_US']['slug'] ? $languages['en_US']['slug'] : 'en';
}

function yneko_reimu_i18n_request_path() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( rawurldecode( $path ), '/' );

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	} elseif ( $home_path === $path ) {
		$path = '';
	}

	return $path;
}

function yneko_reimu_i18n_current_language() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return yneko_reimu_i18n_default_language();
	}

	if ( isset( $GLOBALS['yneko_reimu_current_language'] ) ) {
		return $GLOBALS['yneko_reimu_current_language'];
	}

	$prefix = yneko_reimu_i18n_url_prefix();
	$path   = yneko_reimu_i18n_request_path();
	$lang   = ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ? 'en_US' : 'zh_CN';

	$GLOBALS['yneko_reimu_current_language'] = $lang;
	return $lang;
}

function yneko_reimu_i18n_is_english_request() {
	return 'en_US' === yneko_reimu_i18n_current_language();
}

function yneko_reimu_i18n_frontend_text( $text ) {
	if ( ! yneko_reimu_i18n_enabled() || ( is_admin() && ! wp_doing_ajax() ) || 'en_US' !== yneko_reimu_i18n_current_language() ) {
		return $text;
	}

	$translations = array(
		'置顶' => 'Sticky',
		'本站信息' => 'Site info',
		'申请方法' => 'How to apply',
		'小伙伴们' => 'Friends',
		'添加本站后，在本页留言，格式如下' => 'After adding this site, leave a comment on this page in the following format.',
		'没有文章' => 'No posts',
		'字'   => 'words',
		'篇文章' => 'posts',
	);

	return isset( $translations[ $text ] ) ? $translations[ $text ] : $text;
}

function yneko_reimu_i18n_filter_locale( $locale ) {
	if ( is_admin() || wp_doing_ajax() ) {
		return $locale;
	}

	return yneko_reimu_i18n_enabled() ? yneko_reimu_i18n_current_language() : $locale;
}
add_filter( 'locale', 'yneko_reimu_i18n_filter_locale', 20 );
add_filter( 'determine_locale', 'yneko_reimu_i18n_filter_locale', 20 );

function yneko_reimu_i18n_load_frontend_textdomain() {
	if ( is_admin() || wp_doing_ajax() || ! yneko_reimu_i18n_enabled() || 'en_US' !== yneko_reimu_i18n_current_language() ) {
		return;
	}

	$mofile = YNEKO_REIMU_DIR . '/languages/en_US.mo';
	if ( file_exists( $mofile ) ) {
		load_textdomain( 'yneko-reimu', $mofile, 'en_US' );
	}
}
add_action( 'after_setup_theme', 'yneko_reimu_i18n_load_frontend_textdomain', 20 );

function yneko_reimu_i18n_language_attributes( $output ) {
	if ( ! yneko_reimu_i18n_enabled() || is_admin() ) {
		return $output;
	}

	$lang = 'en_US' === yneko_reimu_i18n_current_language() ? 'en-US' : 'zh-CN';
	return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $lang ) . '"', $output );
}
add_filter( 'language_attributes', 'yneko_reimu_i18n_language_attributes', 20 );
