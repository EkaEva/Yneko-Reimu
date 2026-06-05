<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_articles_section( $wp_customize ) {

	$wp_customize->add_section(
		'yneko_reimu_articles',
		array(
			'title'       => __( '文章页', 'yneko-reimu' ),
			'description' => __( '控制文章页可见模块。评论上传、外部评论和登录服务请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_show_toc'         => array( __( '显示 TOC', 'yneko-reimu' ), true ),
			'yneko_reimu_show_copyright'   => array( __( '显示版权框', 'yneko-reimu' ), true ),
			'yneko_reimu_show_outdated'    => array( __( '显示过期提示', 'yneko-reimu' ), true ),
			'yneko_reimu_show_post_nav'    => array( __( '显示上一篇/下一篇', 'yneko-reimu' ), true ),
			'yneko_reimu_show_update_time' => array( __( '显示更新时间', 'yneko-reimu' ), false ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $label[1],
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label[0],
				'section' => 'yneko_reimu_articles',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_outdated_days',
		array(
			'default'           => 365,
			'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_outdated_days',
		array(
			'label'       => __( '过期天数阈值', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_articles',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 30,
				'step' => 30,
			),
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_code_expand_threshold',
		array(
			'default'           => 420,
			'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_code_expand_threshold',
		array(
			'label'       => __( '代码块折叠高度', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_articles',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 120,
				'step' => 20,
			),
		)
	);
}
