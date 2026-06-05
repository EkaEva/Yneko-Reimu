<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_images_section( $wp_customize ) {

	$wp_customize->add_section(
		'yneko_reimu_images',
		array(
			'title'       => __( '横幅与图片', 'yneko-reimu' ),
			'description' => __( '横幅、封面、作者头像和搜索背景会立即体现在预览中；评论头像审核等用户数据请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_default_banner'    => __( '默认横幅图片', 'yneko-reimu' ),
			'yneko_reimu_default_cover'     => __( '默认卡片封面', 'yneko-reimu' ),
			'yneko_reimu_default_avatar'    => __( '默认头像/角色图', 'yneko-reimu' ),
			'yneko_reimu_search_background' => __( '搜索弹窗背景图', 'yneko-reimu' ),
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
					'label'   => $label,
					'section' => 'yneko_reimu_images',
				)
			)
		);
	}
}
