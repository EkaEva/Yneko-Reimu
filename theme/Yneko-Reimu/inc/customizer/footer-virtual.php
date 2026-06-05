<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_footer_virtual_sections( $wp_customize ) {

	$wp_customize->add_section(
		'yneko_reimu_footer',
		array(
			'title'       => __( '页脚', 'yneko-reimu' ),
			'description' => __( '页脚文本适合通过右侧预览确认；赞助二维码请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_section(
		'yneko_reimu_virtual_pages',
		array(
			'title'       => __( '关于与友链', 'yneko-reimu' ),
			'description' => __( '这里只保留可预览的虚拟页面文案；友链列表请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_about_intro',
		array(
			'default'           => __( '这里记录项目、学习笔记和日常灵感。', 'yneko-reimu' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_about_intro',
		array(
			'label'   => __( '关于页简介', 'yneko-reimu' ),
			'section' => 'yneko_reimu_virtual_pages',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_footer_copyright',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_footer_copyright',
		array(
			'label'       => __( '版权文本', 'yneko-reimu' ),
			'description' => __( '可使用 {year} 作为年份占位。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_footer',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_footer_start_year',
		array(
			'default'           => gmdate( 'Y' ),
			'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_footer_start_year',
		array(
			'label'   => __( '起始年份', 'yneko-reimu' ),
			'section' => 'yneko_reimu_footer',
			'type'    => 'number',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_footer_extra_attribution',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_footer_extra_attribution',
		array(
			'label'       => __( '页脚额外署名', 'yneko-reimu' ),
			'description' => __( '主题会始终保留 WordPress 与 hexo-theme-reimu/MIT 署名。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_footer',
			'type'        => 'text',
		)
	);
}
