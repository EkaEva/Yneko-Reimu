<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'yneko_reimu_panel',
		array(
			'title'       => __( 'Yneko-Reimu 主题设置', 'yneko-reimu' ),
			'description' => __( '控制 Yneko-Reimu 的视觉、文章和社交入口。', 'yneko-reimu' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'yneko_reimu_clone_preset',
		array(
			'title'       => __( 'Reimu 复刻预设', 'yneko-reimu' ),
			'description' => __( '默认按 D-Sketon 演示站首页输出固定导航、语言入口、左侧个人卡和两个首页胶囊。', 'yneko-reimu' ),
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
			'label'       => __( '启用严格复刻侧栏', 'yneko-reimu' ),
			'description' => __( '开启时隐藏 WordPress 小工具区，只保留 Reimu 作者卡、统计、社交和菜单。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_clone_preset',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_clone_tagcloud',
		array(
			'default'           => true,
			'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_clone_tagcloud',
		array(
			'label'       => __( '严格复刻时显示标签云小组件', 'yneko-reimu' ),
			'description' => __( '保持参考站侧栏作者卡下方的 Reimu 标签云区域；不启用 WordPress 原生小工具。', 'yneko-reimu' ),
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

	$wp_customize->add_section(
		'yneko_reimu_visual',
		array(
			'title' => __( '视觉主题', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
		)
	);

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

	$visual_settings = array(
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

	foreach ( $visual_settings as $id => $setting ) {
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

	$visual_booleans = array(
		'yneko_reimu_show_theme_toggle' => array( __( '显示暗色模式切换', 'yneko-reimu' ), true ),
		'yneko_reimu_sticky_nav'        => array( __( '固定导航', 'yneko-reimu' ), true ),
		'yneko_reimu_nav_hide'          => array( __( '导航滚动隐藏', 'yneko-reimu' ), true ),
		'yneko_reimu_show_taichi'       => array( __( '显示太极装饰', 'yneko-reimu' ), true ),
		'yneko_reimu_custom_cursor'     => array( __( '自定义鼠标指针', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_custom_cursor', false ) ),
	);

	foreach ( $visual_booleans as $id => $setting ) {
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

	$wp_customize->add_section(
		'yneko_reimu_images',
		array(
			'title' => __( '横幅与图片', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
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

	$wp_customize->add_section(
		'yneko_reimu_cards',
		array(
			'title' => __( '博客卡片', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_excerpt_length',
		array(
			'default'           => 150,
			'sanitize_callback' => 'yneko_reimu_sanitize_positive_int',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_excerpt_length',
		array(
			'label'       => __( '摘要字数', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_cards',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 40,
				'max'  => 400,
				'step' => 10,
			),
		)
	);

	foreach (
		array(
			'yneko_reimu_show_categories'   => __( '显示分类', 'yneko-reimu' ),
			'yneko_reimu_show_tags'         => __( '显示标签', 'yneko-reimu' ),
			'yneko_reimu_show_comments_num' => __( '显示评论数', 'yneko-reimu' ),
			'yneko_reimu_show_reading_time' => __( '显示阅读时间', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_cards',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_section(
		'yneko_reimu_articles',
		array(
			'title' => __( '文章页', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_show_toc'       => __( '显示 TOC', 'yneko-reimu' ),
			'yneko_reimu_show_copyright' => __( '显示版权框', 'yneko-reimu' ),
			'yneko_reimu_show_outdated'  => __( '显示过期提示', 'yneko-reimu' ),
			'yneko_reimu_show_post_nav'  => __( '显示上一篇/下一篇', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
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

	$wp_customize->add_section(
		'yneko_reimu_social',
		array(
			'title' => __( '社交链接', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_social_github'   => array( 'GitHub URL', '' ),
			'yneko_reimu_social_x'        => array( 'X URL', '' ),
			'yneko_reimu_social_email'    => array( __( 'Email URL 或 mailto:', 'yneko-reimu' ), '' ),
			'yneko_reimu_social_rss'      => array( 'RSS URL', '' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $label[1],
				'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label[0],
				'section' => 'yneko_reimu_social',
				'type'    => 'url',
			)
		);
	}

	$wp_customize->add_section(
		'yneko_reimu_footer',
		array(
			'title' => __( '页脚', 'yneko-reimu' ),
			'panel' => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_section(
		'yneko_reimu_virtual_pages',
		array(
			'title'       => __( '关于与友链', 'yneko-reimu' ),
			'description' => __( '只有在没有同 slug 的真实 WordPress 页面时，主题才会输出这些虚拟页面内容。', 'yneko-reimu' ),
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
		'yneko_reimu_friend_links',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_friend_links',
		array(
			'label'       => __( '友链列表', 'yneko-reimu' ),
			'description' => __( '推荐在“外观 -> Yneko-Reimu 设置”中管理友链。这里保留为旧配置兼容入口。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_virtual_pages',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_sponsor_qr',
		array(
			'default'           => '',
			'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'yneko_reimu_sponsor_qr',
			array(
				'label'   => __( '赞助二维码', 'yneko-reimu' ),
				'section' => 'yneko_reimu_virtual_pages',
			)
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

	$wp_customize->add_section(
		'yneko_reimu_reimu_features',
		array(
			'title'       => __( 'Reimu 扩展功能', 'yneko-reimu' ),
			'description' => __( '扩展默认关闭，填写配置或开启后才加载外部脚本。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_preloader_enable'     => array( __( '加载动画', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_preloader_enable', true ) ),
			'yneko_reimu_top_enable'           => array( __( '回到顶部太极按钮', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_top_enable', true ) ),
			'yneko_reimu_triangle_badge'       => array( __( '右上角 GitHub 三角标', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_triangle_badge', true ) ),
			'yneko_reimu_firework_enable'      => array( __( '鼠标烟花', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_firework_enable', false ) ),
			'yneko_reimu_pjax_enable'          => array( __( 'PJAX 软导航', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_pjax_enable', false ) ),
			'yneko_reimu_busuanzi_enable'      => array( __( '不蒜子统计', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_busuanzi_enable', false ) ),
			'yneko_reimu_player_aplayer_enable'=> array( __( 'APlayer 播放器', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_player_aplayer_enable', false ) ),
			'yneko_reimu_player_meting_enable' => array( __( 'Meting 歌单', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_player_meting_enable', false ) ),
			'yneko_reimu_live2d_widgets_enable'=> array( __( 'Live2D Widgets', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_live2d_widgets_enable', false ) ),
			'yneko_reimu_katex_enable'         => array( __( 'KaTeX 数学公式', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_katex_enable', false ) ),
			'yneko_reimu_photoswipe_enable'    => array( __( 'PhotoSwipe 图片灯箱', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_photoswipe_enable', false ) ),
			'yneko_reimu_mermaid_enable'       => array( __( 'Mermaid 图表', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_mermaid_enable', false ) ),
			'yneko_reimu_algolia_enable'       => array( __( 'Algolia 搜索入口', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_algolia_enable', false ) ),
			'yneko_reimu_generator_search_enable' => array( __( '本地搜索入口', 'yneko-reimu' ), yneko_reimu_feature_default( 'yneko_reimu_generator_search_enable', true ) ),
		) as $id => $setting
	) {
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
				'section' => 'yneko_reimu_reimu_features',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_preloader_text',
		array(
			'default'           => __( '未来有你...', 'yneko-reimu' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_preloader_text',
		array(
			'label'   => __( '加载动画文案', 'yneko-reimu' ),
			'section' => 'yneko_reimu_reimu_features',
			'type'    => 'text',
		)
	);

	$wp_customize->add_section(
		'yneko_reimu_search',
		array(
			'title'       => __( 'Reimu 搜索', 'yneko-reimu' ),
			'description' => __( '默认使用主题自动生成的 /search.json；填写自定义本地 JSON URL 后会覆盖默认地址。搜索优先级：本地 JSON、Algolia、WordPress REST。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_algolia_app_id'     => array( __( 'Algolia App ID', 'yneko-reimu' ), 'text', 'sanitize_text_field' ),
			'yneko_reimu_algolia_api_key'    => array( __( 'Algolia Search API Key', 'yneko-reimu' ), 'text', 'sanitize_text_field' ),
			'yneko_reimu_algolia_index_name' => array( __( 'Algolia Index Name', 'yneko-reimu' ), 'text', 'sanitize_text_field' ),
			'yneko_reimu_local_search_json'  => array( __( '本地搜索 JSON URL', 'yneko-reimu' ), 'url', 'yneko_reimu_sanitize_url_or_empty' ),
		) as $id => $setting
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => $setting[2],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $setting[0],
				'section' => 'yneko_reimu_search',
				'type'    => $setting[1],
			)
		);
	}

	$wp_customize->add_section(
		'yneko_reimu_player',
		array(
			'title'       => __( 'Reimu 播放器', 'yneko-reimu' ),
			'description' => __( '支持 APlayer 音频 JSON 或 Meting 歌单；未启用时不加载外部资源。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_aplayer_audio_json',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_aplayer_audio_json',
		array(
			'label'       => __( 'APlayer audio JSON', 'yneko-reimu' ),
			'description' => __( '推荐在“外观 -> Yneko-Reimu 设置”中管理音乐。这里保留为旧配置兼容入口。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_player',
			'type'        => 'textarea',
		)
	);

	foreach (
		array(
			'yneko_reimu_aplayer_fixed'   => array( __( '固定播放器', 'yneko-reimu' ), false ),
			'yneko_reimu_aplayer_autoplay'=> array( __( '自动播放', 'yneko-reimu' ), false ),
			'yneko_reimu_aplayer_mutex'   => array( __( '播放器互斥', 'yneko-reimu' ), true ),
			'yneko_reimu_aplayer_list_folded' => array( __( '默认折叠播放列表', 'yneko-reimu' ), true ),
		) as $id => $setting
	) {
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
				'section' => 'yneko_reimu_player',
				'type'    => 'checkbox',
			)
		);
	}

	foreach (
		array(
			'yneko_reimu_aplayer_loop'    => array(
				'label'   => __( '循环模式', 'yneko-reimu' ),
				'default' => 'all',
				'choices' => array(
					'all'  => 'all',
					'one'  => 'one',
					'none' => 'none',
				),
			),
			'yneko_reimu_aplayer_order'   => array(
				'label'   => __( '播放顺序', 'yneko-reimu' ),
				'default' => 'list',
				'choices' => array(
					'list'   => 'list',
					'random' => 'random',
				),
			),
			'yneko_reimu_aplayer_preload' => array(
				'label'   => __( '预加载', 'yneko-reimu' ),
				'default' => 'auto',
				'choices' => array(
					'auto'     => 'auto',
					'metadata' => 'metadata',
					'none'     => 'none',
				),
			),
		) as $id => $setting
	) {
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
				'section' => 'yneko_reimu_player',
				'type'    => 'select',
				'choices' => $setting['choices'],
			)
		);
	}

	$wp_customize->add_setting(
		'yneko_reimu_aplayer_volume',
		array(
			'default'           => '0.7',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_aplayer_volume',
		array(
			'label'       => __( '默认音量 0-1', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_player',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 1,
				'step' => 0.1,
			),
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_aplayer_list_max_height',
		array(
			'default'           => '320px',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_aplayer_list_max_height',
		array(
			'label'       => __( '播放列表最大高度', 'yneko-reimu' ),
			'description' => __( '例如 320px。超过高度后列表内部滚动。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_player',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_aplayer_lrc_type',
		array(
			'default'           => '3',
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_aplayer_lrc_type',
		array(
			'label'       => __( '歌词模式', 'yneko-reimu' ),
			'description' => __( 'APlayer 的 lrcType。默认 3 表示读取 audio.lrc 外部歌词文件。', 'yneko-reimu' ),
			'section'     => 'yneko_reimu_player',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 3,
				'step' => 1,
			),
		)
	);

	foreach (
		array(
			'yneko_reimu_meting_id'     => __( 'Meting ID', 'yneko-reimu' ),
			'yneko_reimu_meting_server' => __( 'Meting server', 'yneko-reimu' ),
			'yneko_reimu_meting_type'   => __( 'Meting type', 'yneko-reimu' ),
			'yneko_reimu_meting_auto'   => __( 'Meting auto URL', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_player',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'yneko_reimu_comments_ext',
		array(
			'title'       => __( 'Reimu 评论系统', 'yneko-reimu' ),
			'description' => __( 'WordPress 评论始终可用；第三方评论未启用或未填配置时不会加载。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
			'yneko_reimu_giscus_enable'    => __( 'Giscus', 'yneko-reimu' ),
			'yneko_reimu_utterances_enable'=> __( 'Utterances', 'yneko-reimu' ),
			'yneko_reimu_disqus_enable'    => __( 'Disqus', 'yneko-reimu' ),
			'yneko_reimu_waline_enable'    => __( 'Waline', 'yneko-reimu' ),
			'yneko_reimu_twikoo_enable'    => __( 'Twikoo', 'yneko-reimu' ),
			'yneko_reimu_valine_enable'    => __( 'Valine', 'yneko-reimu' ),
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => false,
				'sanitize_callback' => 'yneko_reimu_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_comments_ext',
				'type'    => 'checkbox',
			)
		);
	}

	foreach (
		array(
			'yneko_reimu_giscus_repo'        => 'Giscus repo',
			'yneko_reimu_giscus_repo_id'     => 'Giscus repo_id',
			'yneko_reimu_giscus_category'    => 'Giscus category',
			'yneko_reimu_giscus_category_id' => 'Giscus category_id',
			'yneko_reimu_utterances_repo'    => 'Utterances repo',
			'yneko_reimu_disqus_shortname'   => 'Disqus shortname',
			'yneko_reimu_waline_server_url'  => 'Waline serverURL',
			'yneko_reimu_twikoo_env_id'      => 'Twikoo envId',
			'yneko_reimu_valine_app_id'      => 'Valine appId',
			'yneko_reimu_valine_app_key'     => 'Valine appKey',
			'yneko_reimu_valine_server_url'  => 'Valine serverURLs',
		) as $id => $label
	) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'yneko_reimu_comments_ext',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'yneko_reimu_vendor',
		array(
			'title'       => __( 'Vendor CDN', 'yneko-reimu' ),
			'description' => __( '用于 Reimu 扩展包的 CDN 前缀，默认与 upstream 演示站一致。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_vendor_cdn_base',
		array(
			'default'           => 'https://npm.webcache.cn',
			'sanitize_callback' => 'yneko_reimu_sanitize_url_or_empty',
		)
	);
	$wp_customize->add_control(
		'yneko_reimu_vendor_cdn_base',
		array(
			'label'   => __( 'CDN 前缀', 'yneko-reimu' ),
			'section' => 'yneko_reimu_vendor',
			'type'    => 'url',
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
			'section'     => 'yneko_reimu_vendor',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'yneko_reimu_customize_register' );
