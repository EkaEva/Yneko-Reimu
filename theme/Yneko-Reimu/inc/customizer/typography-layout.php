<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_typography_layout_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_typography_layout',
		array(
			'title'       => __( '排版与布局密度', 'yneko-reimu' ),
			'description' => __( '调整字体、阅读宽度、间距密度、圆角和阴影。默认值保持主题现有外观。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$font_choices = array(
		'default'  => __( '跟随主题默认', 'yneko-reimu' ),
		'system'   => __( '系统无衬线', 'yneko-reimu' ),
		'serif'    => __( '系统衬线', 'yneko-reimu' ),
		'rounded'  => __( '圆润中文', 'yneko-reimu' ),
		'mono'     => __( '等宽字体', 'yneko-reimu' ),
		'wenkai'   => __( '霞鹜文楷优先', 'yneko-reimu' ),
		'notoserif'=> __( 'Noto Serif SC 优先', 'yneko-reimu' ),
	);

	foreach (
		array(
			'yneko_reimu_font_body'    => __( '正文字体', 'yneko-reimu' ),
			'yneko_reimu_font_heading' => __( '标题字体', 'yneko-reimu' ),
			'yneko_reimu_font_code'    => __( '代码字体', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => 'default',
				'sanitize_callback' => 'yneko_reimu_sanitize_select',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_typography_layout',
				'type'    => 'select',
				'choices' => $font_choices,
			)
		);
	}

	$number_controls = array(
		'yneko_reimu_base_font_size'       => array( __( '基础字号 px', 'yneko-reimu' ), 16, 14, 20, 'yneko_reimu_sanitize_base_font_size' ),
		'yneko_reimu_article_font_size'    => array( __( '文章字号 px', 'yneko-reimu' ), 16, 14, 22, 'yneko_reimu_sanitize_article_font_size' ),
		'yneko_reimu_article_line_height'  => array( __( '文章行高 %', 'yneko-reimu' ), 167, 140, 220, 'yneko_reimu_sanitize_article_line_height' ),
		'yneko_reimu_content_max_width'    => array( __( '页面内容最大宽度 px', 'yneko-reimu' ), 1550, 960, 1800, 'yneko_reimu_sanitize_content_max_width' ),
		'yneko_reimu_article_content_width'=> array( __( '文章阅读最大宽度 px', 'yneko-reimu' ), 0, 0, 1100, 'yneko_reimu_sanitize_article_content_width' ),
		'yneko_reimu_card_radius'          => array( __( '卡片圆角 px', 'yneko-reimu' ), 12, 0, 32, 'yneko_reimu_sanitize_radius_px' ),
		'yneko_reimu_image_radius'         => array( __( '图片圆角 px', 'yneko-reimu' ), 12, 0, 32, 'yneko_reimu_sanitize_radius_px' ),
	);

	foreach ( $number_controls as $id => $control ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $control[1],
				'sanitize_callback' => $control[4],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $control[0],
				'description' => 'yneko_reimu_article_content_width' === $id ? __( '设为 0 时跟随主题默认文章宽度。', 'yneko-reimu' ) : '',
				'section'     => 'yneko_reimu_typography_layout',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => $control[2],
					'max'  => $control[3],
					'step' => 1,
				),
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_layout_density',
		array(
			'default'           => 'default',
			'sanitize_callback' => 'yneko_reimu_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_layout_density',
		array(
			'label'   => __( '布局密度', 'yneko-reimu' ),
			'section' => 'yneko_reimu_typography_layout',
			'type'    => 'select',
			'choices' => array(
				'comfortable' => __( '舒展', 'yneko-reimu' ),
				'default'     => __( '默认', 'yneko-reimu' ),
				'compact'     => __( '紧凑', 'yneko-reimu' ),
			),
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_shadow_strength',
		array(
			'default'           => 'default',
			'sanitize_callback' => 'yneko_reimu_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_shadow_strength',
		array(
			'label'   => __( '阴影强度', 'yneko-reimu' ),
			'section' => 'yneko_reimu_typography_layout',
			'type'    => 'select',
			'choices' => array(
				'none'    => __( '关闭', 'yneko-reimu' ),
				'soft'    => __( '柔和', 'yneko-reimu' ),
				'default' => __( '默认', 'yneko-reimu' ),
				'strong'  => __( '明显', 'yneko-reimu' ),
			),
		)
	);
}
