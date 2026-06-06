<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_home_category_default_capsules() {
	return array(
		1 => array(
			'title' => __( 'Yneko', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'yneko' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'yneko' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
		2 => array(
			'title' => __( '学习笔记', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'study-notes' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'study-notes' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
	);
}

function yneko_reimu_home_category_legacy_urls() {
	return array(
		home_url( '/category/hexo/' ),
		home_url( '/category/project/' ),
		home_url( '/category/项目/' ),
		home_url( '/category/%e9%a1%b9%e7%9b%ae/' ),
		home_url( '/project/' ),
		home_url( '/项目/' ),
	);
}

function yneko_reimu_home_category_legacy_covers() {
	return array(
		yneko_reimu_asset_uri( 'assets/images/banner.png' ),
		yneko_reimu_asset_uri( 'assets/images/banner.webp' ),
		yneko_reimu_get_default_banner_url(),
	);
}

function yneko_reimu_home_category_title_value( $index, $default ) {
	$title = trim( (string) yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_title', $default['title'] ) );

	if ( 1 === $index && ( in_array( strtolower( $title ), array( 'hexo', 'project' ), true ) || '项目' === $title ) ) {
		return $default['title'];
	}

	return '' === $title ? $default['title'] : $title;
}

function yneko_reimu_home_category_url_value( $index, $default ) {
	$url = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_url', $default['url'] );

	if ( 1 === $index && in_array( yneko_reimu_normalize_theme_url( $url ), yneko_reimu_home_category_legacy_urls(), true ) ) {
		$url = $default['url'];
	}

	return yneko_reimu_normalize_theme_url( $url, $default['url'] );
}

function yneko_reimu_home_category_cover_value( $index, $default ) {
	$cover = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_cover', '' );

	if ( '' === $cover ) {
		$cover = $default['cover'];
	}

	if ( in_array( yneko_reimu_normalize_theme_url( $cover ), yneko_reimu_home_category_legacy_covers(), true ) ) {
		$cover = $default['cover'];
	}

	return esc_url_raw( $cover );
}

function yneko_reimu_home_category_capsule_item( $index, $default ) {
	return array(
		'title' => yneko_reimu_home_category_title_value( $index, $default ),
		'url'   => yneko_reimu_home_category_url_value( $index, $default ),
		'count' => $default['count'],
		'cover' => yneko_reimu_home_category_cover_value( $index, $default ),
	);
}

function yneko_reimu_home_category_capsules() {
	$items = array();

	foreach ( yneko_reimu_home_category_default_capsules() as $index => $default ) {
		$items[] = yneko_reimu_home_category_capsule_item( $index, $default );
	}

	return $items;
}
