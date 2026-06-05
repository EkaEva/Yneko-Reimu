<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_preset_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_clone_preset',
		array(
			'title'       => __( '预设', 'yneko-reimu' ),
			'description' => __( '管理主题内置的导航、侧栏作者卡、首页胶囊和播放器位置。侧栏下方的小组件可在“侧栏小组件”中单独控制。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
			'priority'    => 1,
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_strict_clone',
		array(
			'default'           => true,
			'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_strict_clone',
		array(
			'label'       => __( '使用主题内置侧栏', 'yneko-reimu' ),
			'description' => __( '开启后侧栏由主题自动生成，显示作者卡、站点统计、社交链接和菜单。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_clone_preset',
			'type'        => 'checkbox',
		)
	);

	foreach ( yneko_reimu_default_nav_items() as $key => $item ) {
		$wp_customize->add_setting(
			'yneko_reimu_nav_' . $key . '_label',
			array(
				'default'           => $item['label'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'yneko_reimu_nav_' . $key . '_label',
			array(
				'label'   => sprintf(
					/* translators: %s: nav item label. */
					__( '导航文字：%s', 'yneko-reimu' ),
					$item['label']
				),
				'section' => 'yneko_reimu_clone_preset',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'yneko_reimu_nav_' . $key . '_url',
			array(
				'default'           => $item['url'],
				'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			)
		);
		$wp_customize->add_control(
			'yneko_reimu_nav_' . $key . '_url',
			array(
				'label'   => sprintf(
					/* translators: %s: nav item label. */
					__( '导航链接：%s', 'yneko-reimu' ),
					$item['label']
				),
				'section' => 'yneko_reimu_clone_preset',
				'type'    => 'url',
			)
		);
	}

	for ( $i = 1; $i <= 2; $i++ ) {
		$defaults = yneko_reimu_home_category_capsules();
		$default  = $defaults[ $i - 1 ];

		foreach (
			array(
				'title' => array( __( '首页胶囊标题', 'yneko-reimu' ), 'text', 'sanitize_text_field' ),
				'url'   => array( __( '首页胶囊链接', 'yneko-reimu' ), 'url', 'yneko_reimu_sanitize_url_or_empty' ),
			) as $field => $setting
		) {
			$wp_customize->add_setting(
				'yneko_reimu_home_category_' . $i . '_' . $field,
				array(
					'default'           => $default[ $field ],
					'sanitize_callback' => $setting[2],
				)
			);
			$wp_customize->add_control(
				'yneko_reimu_home_category_' . $i . '_' . $field,
				array(
					'label'   => sprintf(
						/* translators: 1: field label, 2: slot number. */
						__( '%1$s %2$d', 'yneko-reimu' ),
						$setting[0],
						$i
					),
					'section' => 'yneko_reimu_clone_preset',
					'type'    => $setting[1],
				)
			);
		}

		$wp_customize->add_setting(
			'yneko_reimu_home_category_' . $i . '_cover',
			array(
				'default'           => '',
				'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'yneko_reimu_home_category_' . $i . '_cover',
				array(
					'label'   => sprintf(
						/* translators: %d: slot number. */
						__( '首页胶囊封面 %d', 'yneko-reimu' ),
						$i
					),
					'section' => 'yneko_reimu_clone_preset',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_player_position',
		array(
			'default'           => 'before_sidebar',
			'sanitize_callback' => 'yneko_reimu_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_player_position',
		array(
			'label'   => __( '播放器位置', 'yneko-reimu' ),
			'section' => 'yneko_reimu_clone_preset',
			'type'    => 'select',
			'choices' => array(
				'before_sidebar' => __( '侧栏卡片之前', 'yneko-reimu' ),
				'after_sidebar'  => __( '侧栏卡片之后', 'yneko-reimu' ),
				'after_widget'   => __( '小工具之后', 'yneko-reimu' ),
			),
		)
	);
}
