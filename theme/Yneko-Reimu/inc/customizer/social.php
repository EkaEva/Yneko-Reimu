<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_customizer_social_section( $wp_customize, $reimu_settings_defaults ) {
	yneko_reimu_register_customizer_social_base_section( $wp_customize );
	yneko_reimu_register_customizer_share_heading( $wp_customize );
	yneko_reimu_register_customizer_share_controls( $wp_customize );
	yneko_reimu_register_customizer_social_sidebar_heading( $wp_customize );
	yneko_reimu_register_customizer_social_sidebar_controls( $wp_customize, $reimu_settings_defaults );
}

function yneko_reimu_register_customizer_social_base_section( $wp_customize ) {
	$wp_customize->add_section(
		'yneko_reimu_social',
		array(
			'title'       => __( '分享与社交链接', 'yneko-reimu' ),
			'description' => __( '上方管理文章分享按钮；下方管理侧栏社交图标。链接可以先填写，只有启用后才会在前台显示。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);
}

function yneko_reimu_register_customizer_share_heading( $wp_customize ) {
	$wp_customize->add_setting(
		'yneko_reimu_social_share_heading',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'yneko_reimu_social_share_heading',
			array(
				'label'       => __( '文章分享链接', 'yneko-reimu' ),
				'description' => __( '默认启用 QQ 和微信，其它分享按钮默认关闭。', 'yneko-reimu' ),
				'section'     => 'yneko_reimu_social',
				'type'        => 'hidden',
			)
		)
	);
}

function yneko_reimu_register_customizer_share_controls( $wp_customize ) {
	foreach ( yneko_reimu_share_definitions() as $key => $item ) {
		$id = 'yneko_reimu_share_' . $key . '_enabled';
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => in_array( $key, array( 'qq', 'weixin' ), true ),
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => sprintf(
					/* translators: %s: share service label. */
					__( '启用文章分享：%s', 'yneko-reimu' ),
					$item['label']
				),
				'section' => 'yneko_reimu_social',
				'type'    => 'checkbox',
			)
		);
	}
}

function yneko_reimu_register_customizer_social_sidebar_heading( $wp_customize ) {
	$wp_customize->add_setting(
		'yneko_reimu_social_sidebar_heading',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'yneko_reimu_social_sidebar_heading',
			array(
				'label'       => __( '侧栏社交图标', 'yneko-reimu' ),
				'description' => __( '默认只启用 GitHub。其它社交链接可先填写保存，启用后才会显示。', 'yneko-reimu' ),
				'section'     => 'yneko_reimu_social',
				'type'        => 'hidden',
			)
		)
	);
}

function yneko_reimu_register_customizer_social_sidebar_controls( $wp_customize, $reimu_settings_defaults ) {
	foreach ( yneko_reimu_social_definitions() as $key => $item ) {
		$enabled_id = 'yneko_reimu_social_' . $key . '_enabled';
		$wp_customize->add_setting(
			$enabled_id,
			array(
				'default'           => 'github' === $key,
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$enabled_id,
			array(
				'label'   => sprintf(
					/* translators: %s: social service label. */
					__( '启用侧栏社交：%s', 'yneko-reimu' ),
					$item['label']
				),
				'section' => 'yneko_reimu_social',
				'type'    => 'checkbox',
			)
		);

		if ( 'github' === $key ) {
			yneko_reimu_register_customizer_github_social_controls( $wp_customize, $reimu_settings_defaults );
			continue;
		}

		yneko_reimu_register_customizer_social_url_control( $wp_customize, $key, $item );
	}
}

function yneko_reimu_register_customizer_github_social_controls( $wp_customize, $reimu_settings_defaults ) {
	$wp_customize->add_setting(
		'yneko_reimu_settings[github_url]',
		array(
			'default'           => $reimu_settings_defaults['github_url'] ?? '',
			'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			'type'              => 'option',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_settings[github_url]',
		array(
			'label'       => __( 'GitHub 链接', 'yneko-reimu' ),
			'description' => __( '同时用于顶部 GitHub 三角标、侧栏 GitHub 图标和项目页拉取来源。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_social',
			'type'        => 'url',
		)
	);
	$wp_customize->add_setting(
		'yneko_reimu_settings[features][triangle_badge]',
		array(
			'default'           => $reimu_settings_defaults['features']['triangle_badge'] ?? '1',
			'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			'type'              => 'option',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_settings[features][triangle_badge]',
		array(
			'label'       => __( '显示右上角 GitHub 三角标', 'yneko-reimu' ),
			'description' => __( '关闭后，页面右上角不再显示 GitHub 三角标。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_social',
			'type'        => 'checkbox',
		)
	);
}

function yneko_reimu_register_customizer_social_url_control( $wp_customize, $key, $item ) {
	$url_id = 'twitter' === $key ? 'yneko_reimu_social_x' : 'yneko_reimu_social_' . $key;
	$wp_customize->add_setting(
		$url_id,
		array(
			'default'           => '',
			'sanitize_callback' => 'yneko_reimu_sanitize_social_url_or_empty',
		)
	);
	$wp_customize->add_control(
		$url_id,
		array(
			'label'       => sprintf(
				/* translators: %s: social service label. */
				__( '%s 链接', 'yneko-reimu' ),
				$item['label']
			),
			'description' => $item['placeholder'],
			'section'     => 'yneko_reimu_social',
			'type'        => 'email' === $key ? 'text' : 'url',
		)
	);
}
