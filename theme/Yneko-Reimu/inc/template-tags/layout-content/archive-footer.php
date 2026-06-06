<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_archive_title() {
	if ( yneko_reimu_is_virtual_page() ) {
		$page = yneko_reimu_virtual_page();
		return $page['title'];
	}

	$special_title = yneko_reimu_special_page_archive_value( 'title' );
	if ( '' !== $special_title ) {
		return $special_title;
	}

	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query. */
			esc_html__( '搜索：%s', 'yneko-reimu' ),
			get_search_query()
		);
	}

	if ( is_404() ) {
		return esc_html__( '404（´◔ ₃ ◔`)', 'yneko-reimu' );
	}

	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_title() );
	}

	if ( is_home() && ! is_front_page() ) {
		return get_the_title( get_option( 'page_for_posts' ) );
	}

	return get_bloginfo( 'name' );
}

function yneko_reimu_archive_description() {
	if ( yneko_reimu_is_virtual_page() ) {
		$page = yneko_reimu_virtual_page();
		return $page['description'];
	}

	$special_description = yneko_reimu_special_page_archive_value( 'description' );
	if ( '' !== $special_description ) {
		return $special_description;
	}

	if ( is_search() ) {
		return esc_html__( '以下是与你输入关键词相关的文章。', 'yneko-reimu' );
	}

	if ( is_404() ) {
		return esc_html__( '少年，你迷路了吗？', 'yneko-reimu' );
	}

	if ( is_archive() ) {
		return get_the_archive_description();
	}

	return get_bloginfo( 'description' );
}

function yneko_reimu_special_page_archive_value( $key ) {
	$special_slug = yneko_reimu_special_page_slug();
	$pages        = $special_slug ? yneko_reimu_virtual_pages() : array();

	return isset( $pages[ $special_slug ][ $key ] ) ? $pages[ $special_slug ][ $key ] : '';
}

function yneko_reimu_footer_copyright() {
	$text  = yneko_reimu_get_theme_mod( 'yneko_reimu_footer_copyright', '' );
	$start = absint( yneko_reimu_get_theme_mod( 'yneko_reimu_footer_start_year', gmdate( 'Y' ) ) );
	$year  = absint( gmdate( 'Y' ) );
	$range = $start && $start < $year ? $start . '-' . $year : (string) $year;

	if ( ! $text ) {
		$text = sprintf(
			/* translators: %s: site name. */
			__( '© %s. Powered by WordPress.', 'yneko-reimu' ),
			get_bloginfo( 'name' )
		);
	}

	return str_replace( '{year}', $range, $text );
}
