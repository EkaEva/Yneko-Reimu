<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_panel( $wp_customize ) {
	$wp_customize->add_panel(
		'yneko_reimu_panel',
		array(
			'title'       => __( 'Yneko-Reimu 视觉预览', 'yneko-reimu' ),
			'description' => __( '这里作为视觉预览工作台，保留能通过右侧预览判断效果的项目；搜索、音乐、友链、OAuth 和第三方服务请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'priority'    => 30,
		)
	);
}
