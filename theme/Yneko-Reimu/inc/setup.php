<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_setup() {
	load_theme_textdomain( 'yneko-reimu', YNEKO_REIMU_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'block-template-parts' );
	add_editor_style( 'assets/dist/reimu.css' );
	add_editor_style( 'assets/dist/reimu-editor.css' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 96,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'yneko-reimu' ),
			'mobile'  => __( 'Mobile Menu', 'yneko-reimu' ),
			'footer'  => __( 'Footer Menu', 'yneko-reimu' ),
			'social'  => __( 'Social Menu', 'yneko-reimu' ),
		)
	);

	add_image_size( 'reimu-card', 900, 560, true );
	add_image_size( 'yneko-reimu-banner', 1920, 720, true );
}
add_action( 'after_setup_theme', 'yneko_reimu_setup' );

function yneko_reimu_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'yneko_reimu_content_width', 860 );
}
add_action( 'after_setup_theme', 'yneko_reimu_content_width', 0 );
