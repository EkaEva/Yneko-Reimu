<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_cards_section( $wp_customize ) {

	$wp_customize->add_section(
		'yneko_reimu_cards',
		array(
			'title'       => __( '博客卡片', 'yneko-reimu' ),
			'description' => __( '控制首页与归档卡片的可见信息，方便在预览中检查密度和排版。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_excerpt_length',
		array(
			'default'           => 150,
			'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_excerpt_length',
		array(
			'label'       => __( '摘要字数', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_cards',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 40,
				'max'  => 400,
				'step' => 10,
			),
		)
	);

	foreach (
		array(
			'yneko_reimu_show_categories'   => __( '显示分类', 'yneko-reimu' ),
			'yneko_reimu_show_tags'         => __( '显示标签', 'yneko-reimu' ),
			'yneko_reimu_show_comments_num' => __( '显示评论数', 'yneko-reimu' ),
			'yneko_reimu_show_reading_time' => __( '显示阅读时间', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_cards',
				'type'    => 'checkbox',
			)
		);
	}
}
