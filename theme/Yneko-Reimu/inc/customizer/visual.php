<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_visual_section( $wp_customize ) {
	yneko_reimu_customizer_add_visual_section( $wp_customize );
	yneko_reimu_customizer_add_accent_color_control( $wp_customize );
	yneko_reimu_customizer_add_visual_select_controls( $wp_customize );
	yneko_reimu_customizer_add_visual_boolean_controls( $wp_customize );
}

function yneko_reimu_customizer_add_visual_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_visual',
		array(
			'title'       => __( '视觉主题', 'yneko-reimu' ),
			'description' => __( '颜色、暗色模式和导航布局适合在这里实时预览；第三方脚本与装饰扩展请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);
}

function yneko_reimu_customizer_add_accent_color_control( $wp_customize ) {
	$wp_customize->add_setting(
		'yneko_reimu_accent_color',
		array(
			'default'           => '#ff5252',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'yneko_reimu_accent_color',
			array(
				'label'   => __( '强调色', 'yneko-reimu' ),
				'section' => 'yneko_reimu_visual',
			)
		)
	);
}

function yneko_reimu_customizer_visual_select_controls() {
	return array(
		'yneko_reimu_dark_mode_default' => array(
			'label'   => __( '暗色模式默认值', 'yneko-reimu' ),
			'type'    => 'select',
			'default' => 'auto',
			'choices' => array(
				'auto'  => __( '跟随系统', 'yneko-reimu' ),
				'light' => __( '浅色', 'yneko-reimu' ),
				'dark'  => __( '暗色', 'yneko-reimu' ),
			),
		),
		'yneko_reimu_sidebar_position'  => array(
			'label'   => __( '侧边栏位置', 'yneko-reimu' ),
			'type'    => 'select',
			'default' => 'left',
			'choices' => array(
				'left'     => __( '左侧', 'yneko-reimu' ),
				'right'    => __( '右侧', 'yneko-reimu' ),
				'disabled' => __( '关闭', 'yneko-reimu' ),
			),
		),
	);
}

function yneko_reimu_customizer_add_visual_select_controls( $wp_customize ) {
	foreach ( yneko_reimu_customizer_visual_select_controls() as $id => $setting ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $setting['default'],
				'sanitize_callback' => 'yneko_reimu_sanitize_select',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $setting['label'],
				'section' => 'yneko_reimu_visual',
				'type'    => $setting['type'],
				'choices' => $setting['choices'],
			)
		);
	}
}

function yneko_reimu_customizer_visual_boolean_controls() {
	return array(
		'yneko_reimu_show_theme_toggle' => array( __( '显示暗色模式切换', 'yneko-reimu' ), true ),
		'yneko_reimu_sticky_nav'        => array( __( '固定导航', 'yneko-reimu' ), true ),
		'yneko_reimu_nav_hide'          => array( __( '导航滚动隐藏', 'yneko-reimu' ), true ),
		'yneko_reimu_show_taichi'       => array( __( '显示太极装饰', 'yneko-reimu' ), true ),
	);
}

function yneko_reimu_customizer_add_visual_boolean_controls( $wp_customize ) {
	foreach ( yneko_reimu_customizer_visual_boolean_controls() as $id => $setting ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $setting[1],
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $setting[0],
				'section' => 'yneko_reimu_visual',
				'type'    => 'checkbox',
			)
		);
	}
}
