<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_enqueue_theme_styles( $asset_strategy, $enable_aplayer ) {
	wp_enqueue_style( 'yneko-reimu-theme', get_stylesheet_uri(), array(), YNEKO_REIMU_VERSION );
	if ( ! empty( $asset_strategy['font_display'] ) ) {
		wp_enqueue_style( 'yneko-reimu-fonts', 'https://fonts.googleapis.com/css?family=Mulish:400,400italic,700,700italic|Noto+Serif+SC:400,400italic,700,700italic&display=swap', array(), null );
	}
	wp_enqueue_style( 'yneko-reimu-loader', YNEKO_REIMU_URI . '/assets/dist/loader.css', array( 'yneko-reimu-theme' ), yneko_reimu_asset_version( 'assets/dist/loader.css' ) );
	if ( $enable_aplayer ) {
		wp_enqueue_style( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.css' ), array(), '1.10.1' );
		wp_enqueue_style( 'yneko-reimu-player', YNEKO_REIMU_URI . '/assets/dist/reimu-player.css', array( 'yneko-reimu-aplayer' ), yneko_reimu_asset_version( 'assets/dist/reimu-player.css' ) );
	}
	$main_style_deps = array( 'yneko-reimu-theme', 'yneko-reimu-loader' );
	if ( ! empty( $asset_strategy['font_display'] ) ) {
		$main_style_deps[] = 'yneko-reimu-fonts';
	}
	if ( $enable_aplayer ) {
		$main_style_deps[] = 'yneko-reimu-aplayer';
		$main_style_deps[] = 'yneko-reimu-player';
	}
	wp_enqueue_style( 'yneko-reimu-search', YNEKO_REIMU_URI . '/assets/dist/reimu-search.css', $main_style_deps, yneko_reimu_asset_version( 'assets/dist/reimu-search.css' ) );
	$main_style_deps[] = 'yneko-reimu-search';
	wp_enqueue_style( 'yneko-reimu-comments', YNEKO_REIMU_URI . '/assets/dist/reimu-comments.css', $main_style_deps, yneko_reimu_asset_version( 'assets/dist/reimu-comments.css' ) );
	$main_style_deps[] = 'yneko-reimu-comments';
	wp_enqueue_style( 'yneko-reimu-share', YNEKO_REIMU_URI . '/assets/dist/reimu-share.css', $main_style_deps, yneko_reimu_asset_version( 'assets/dist/reimu-share.css' ) );
	$main_style_deps[] = 'yneko-reimu-share';
	wp_enqueue_style( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.css', $main_style_deps, yneko_reimu_asset_version( 'assets/dist/reimu.css' ) );
	if ( yneko_reimu_should_enqueue_code_styles() ) {
		wp_enqueue_style( 'yneko-reimu-code', YNEKO_REIMU_URI . '/assets/dist/reimu-code.css', array( 'yneko-reimu-main' ), yneko_reimu_asset_version( 'assets/dist/reimu-code.css' ) );
	}

	$accent = sanitize_hex_color( yneko_reimu_get_theme_mod( 'yneko_reimu_accent_color', '#ff5252' ) );
	$accent = $accent ? $accent : '#ff5252';
	wp_add_inline_style( 'yneko-reimu-main', ':root{--red-1:' . esc_html( $accent ) . ';--color-link:' . esc_html( $accent ) . ';}' );
	wp_add_inline_style( 'yneko-reimu-main', yneko_reimu_visual_asset_variables_css() );
	wp_add_inline_style( 'yneko-reimu-main', yneko_reimu_cursor_variables_css() );
	if ( ! yneko_reimu_get_theme_mod( 'yneko_reimu_sticky_nav', true ) ) {
		wp_add_inline_style( 'yneko-reimu-main', '#header-nav{position:absolute;}' );
	}
}

function yneko_reimu_should_enqueue_code_styles() {
	if ( is_singular() ) {
		return true;
	}

	return function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page();
}
