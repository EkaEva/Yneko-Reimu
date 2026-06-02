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

function yneko_reimu_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) || ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'yneko-reimu',
		array(
			'label' => __( 'Yneko-Reimu', 'yneko-reimu' ),
		)
	);

	register_block_pattern(
		'yneko-reimu/article-intro',
		array(
			'title'      => __( '文章引言卡片', 'yneko-reimu' ),
			'categories' => array( 'yneko-reimu' ),
			'content'    => '<!-- wp:group {"className":"reimu-pattern-intro","layout":{"type":"constrained"}} --><div class="wp-block-group reimu-pattern-intro"><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html__( '在这里写下文章的核心摘要，让读者先抓住这一页最重要的情绪与信息。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'yneko-reimu/two-column-note',
		array(
			'title'      => __( '双栏说明区块', 'yneko-reimu' ),
			'categories' => array( 'yneko-reimu' ),
			'content'    => '<!-- wp:columns {"className":"reimu-pattern-columns"} --><div class="wp-block-columns reimu-pattern-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>' . esc_html__( '重点', 'yneko-reimu' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( '用于放置文章中的重点信息、更新说明或推荐阅读。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>' . esc_html__( '补充', 'yneko-reimu' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( '用于放置上下文、链接或轻量的延伸解释。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
		)
	);
}
add_action( 'init', 'yneko_reimu_register_block_patterns' );
