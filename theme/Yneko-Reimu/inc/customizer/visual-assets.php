<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_visual_assets_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_visual_assets',
		array(
			'title'       => __( '视觉资产', 'yneko-reimu' ),
			'description' => __( '替换光标、加载动画和小装饰图片。功能启用开关仍在“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$cursor_controls = array(
		'yneko_reimu_cursor_default_url'  => __( '默认鼠标指针', 'yneko-reimu' ),
		'yneko_reimu_cursor_pointer_url'  => __( '链接/按钮鼠标指针', 'yneko-reimu' ),
		'yneko_reimu_cursor_text_url'     => __( '文本输入鼠标指针', 'yneko-reimu' ),
		'yneko_reimu_cursor_progress_url' => __( '加载/忙碌鼠标指针', 'yneko-reimu' ),
	);

	foreach ( $cursor_controls as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$id,
				array(
					'label'       => $label,
					'description' => __( '留空时使用主题内置 Lily 指针。建议使用 PNG/WebP，尺寸不超过 64×64。', 'yneko-reimu' ),
					'section'     => 'yneko_reimu_visual_assets',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_preloader_image_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'yneko_reimu_preloader_image_url',
			array(
				'label'       => __( '加载动画中间图片', 'yneko-reimu' ),
				'description' => __( '留空时使用主题内置太极图。', 'yneko-reimu' ),
				'section'     => 'yneko_reimu_visual_assets',
			)
		)
	);

	foreach (
		array(
			'yneko_reimu_preloader_text_zh' => array( __( '加载动画中文文案', 'yneko-reimu' ), '未来有你...' ),
			'yneko_reimu_preloader_text_en' => array( __( '加载动画英文文案', 'yneko-reimu' ), 'Loading...' ),
		) as $id => $meta
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $meta[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $meta[0],
				'section' => 'yneko_reimu_visual_assets',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_preloader_image_size',
		array(
			'default'           => 150,
			'sanitize_callback' => 'yneko_reimu_sanitize_preloader_image_size',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_preloader_image_size',
		array(
			'label'       => __( '加载动画图片尺寸 px', 'yneko-reimu' ),
			'description' => __( '限制在 48 到 320 像素之间，避免遮挡移动端内容。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_visual_assets',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 48,
				'max'  => 320,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_preloader_image_rotate',
		array(
			'default'           => true,
			'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_preloader_image_rotate',
		array(
			'label'   => __( '加载动画图片旋转', 'yneko-reimu' ),
			'section' => 'yneko_reimu_visual_assets',
			'type'    => 'checkbox',
		)
	);

	foreach (
		array(
			'yneko_reimu_top_icon_url'     => __( '回到顶部装饰图', 'yneko-reimu' ),
			'yneko_reimu_sponsor_icon_url' => __( '赞助按钮装饰图', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$id,
				array(
					'label'       => $label,
					'description' => __( '留空时使用主题内置太极图。', 'yneko-reimu' ),
					'section'     => 'yneko_reimu_visual_assets',
				)
			)
		);
	}
}
