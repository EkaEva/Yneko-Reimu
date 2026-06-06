<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_sidebar_widgets_section( $wp_customize ) {
	yneko_reimu_customizer_add_sidebar_widgets_section( $wp_customize );
	yneko_reimu_customizer_add_sidebar_widget_toggles( $wp_customize );
	yneko_reimu_customizer_add_sidebar_widget_order_control( $wp_customize );
	yneko_reimu_customizer_add_sidebar_widget_limit_controls( $wp_customize );
}

function yneko_reimu_customizer_add_sidebar_widgets_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_sidebar_widgets',
		array(
			'title'       => __( '侧栏小组件', 'yneko-reimu' ),
			'description' => __( '控制侧栏作者卡下方的主题内置小组件。标签云默认开启，其它默认关闭；搜索入口已在顶部导航中提供。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
			'priority'    => 2,
		)
	);
}

function yneko_reimu_customizer_sidebar_widget_toggles() {
	return array(
		'tagcloud'        => array( __( '标签云', 'yneko-reimu' ), true ),
		'projects'        => array( __( '项目', 'yneko-reimu' ), false ),
		'recent_posts'    => array( __( '近期文章', 'yneko-reimu' ), false ),
		'recent_comments' => array( __( '近期评论', 'yneko-reimu' ), false ),
		'archives'        => array( __( '归档', 'yneko-reimu' ), false ),
		'categories'      => array( __( '分类', 'yneko-reimu' ), false ),
	);
}

function yneko_reimu_customizer_add_sidebar_widget_toggles( $wp_customize ) {
	foreach ( yneko_reimu_customizer_sidebar_widget_toggles() as $key => $setting ) {
		$wp_customize->add_setting(
			'yneko_reimu_sidebar_widget_' . $key,
			array(
				'default'           => $setting[1],
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'yneko_reimu_sidebar_widget_' . $key,
			array(
				'label'   => $setting[0],
				'section' => 'yneko_reimu_sidebar_widgets',
				'type'    => 'checkbox',
			)
		);
	}
}

function yneko_reimu_customizer_add_sidebar_widget_order_control( $wp_customize ) {
	$wp_customize->add_setting(
		'yneko_reimu_sidebar_widget_order',
		array(
			'default'           => 'tagcloud,projects,recent_posts,categories,archives,recent_comments',
			'sanitize_callback' => 'yneko_reimu_sanitize_sidebar_widget_order',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_sidebar_widget_order',
		array(
			'label'       => __( '小组件排序', 'yneko-reimu' ),
			'description' => __( '使用英文逗号分隔：tagcloud, projects, recent_posts, categories, archives, recent_comments。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_sidebar_widgets',
			'type'        => 'text',
		)
	);
}

function yneko_reimu_customizer_sidebar_widget_limits() {
	return array(
		'projects'        => array( __( '项目数量', 'yneko-reimu' ), 5 ),
		'recent_posts'    => array( __( '近期文章数量', 'yneko-reimu' ), 5 ),
		'recent_comments' => array( __( '近期评论数量', 'yneko-reimu' ), 5 ),
		'archives'        => array( __( '归档数量', 'yneko-reimu' ), 8 ),
		'categories'      => array( __( '分类数量', 'yneko-reimu' ), 8 ),
	);
}

function yneko_reimu_customizer_add_sidebar_widget_limit_controls( $wp_customize ) {
	foreach ( yneko_reimu_customizer_sidebar_widget_limits() as $key => $setting ) {
		$wp_customize->add_setting(
			'yneko_reimu_sidebar_widget_' . $key . '_limit',
			array(
				'default'           => $setting[1],
				'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
			)
		);
		$wp_customize->add_control(
			'yneko_reimu_sidebar_widget_' . $key . '_limit',
			array(
				'label'       => $setting[0],
				'section'     => 'yneko_reimu_sidebar_widgets',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 20,
					'step' => 1,
				),
			)
		);
	}
}
