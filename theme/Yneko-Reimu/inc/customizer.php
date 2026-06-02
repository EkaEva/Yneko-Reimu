<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'yneko_reimu_panel',
		array(
			'title'       => __( 'Yneko-Reimu 视觉预览', 'yneko-reimu' ),
			'description' => __( '这里作为视觉预览工作台，保留能通过右侧预览判断效果的项目；搜索、音乐、友链、OAuth 和第三方服务请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'priority'    => 30,
		)
	);

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

	$wp_customize->add_section(
		'yneko_reimu_sidebar_widgets',
		array(
			'title'       => __( '侧栏小组件', 'yneko-reimu' ),
			'description' => __( '控制侧栏作者卡下方的主题内置小组件。标签云默认开启，其它默认关闭；搜索入口已在顶部导航中提供。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
			'priority'    => 2,
		)
	);

	$sidebar_widget_toggles = array(
		'tagcloud'        => array( __( '标签云', 'yneko-reimu' ), true ),
		'projects'        => array( __( '项目', 'yneko-reimu' ), false ),
		'recent_posts'    => array( __( '近期文章', 'yneko-reimu' ), false ),
		'recent_comments' => array( __( '近期评论', 'yneko-reimu' ), false ),
		'archives'        => array( __( '归档', 'yneko-reimu' ), false ),
		'categories'      => array( __( '分类', 'yneko-reimu' ), false ),
	);
	foreach ( $sidebar_widget_toggles as $key => $setting ) {
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

	foreach (
		array(
			'projects'        => array( __( '项目数量', 'yneko-reimu' ), 5 ),
			'recent_posts'    => array( __( '近期文章数量', 'yneko-reimu' ), 5 ),
			'recent_comments' => array( __( '近期评论数量', 'yneko-reimu' ), 5 ),
			'archives'        => array( __( '归档数量', 'yneko-reimu' ), 8 ),
			'categories'      => array( __( '分类数量', 'yneko-reimu' ), 8 ),
		) as $key => $setting
	) {
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

	$wp_customize->add_section(
		'yneko_reimu_visual',
		array(
			'title'       => __( '视觉主题', 'yneko-reimu' ),
			'description' => __( '颜色、暗色模式和导航布局适合在这里实时预览；第三方脚本与装饰扩展请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
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

	$wp_customize->add_section(
		'yneko_reimu_cards',
		array(
			'title'       => __( '博客卡片', 'yneko-reimu' ),
			'description' => __( '控制首页与归档卡片的可见信息，方便在预览中检查密度和排版。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
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
			'title'       => __( '文章页', 'yneko-reimu' ),
			'description' => __( '控制文章页可见模块。评论上传、外部评论和登录服务请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
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
			'title'       => __( '社交链接', 'yneko-reimu' ),
			'description' => __( '这里只保留可直接预览的社交展示链接；GitHub 主页和 OAuth 请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	foreach (
		array(
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
			'title'       => __( '页脚', 'yneko-reimu' ),
			'description' => __( '页脚文本适合通过右侧预览确认；赞助二维码请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_section(
		'yneko_reimu_virtual_pages',
		array(
			'title'       => __( '关于与友链', 'yneko-reimu' ),
			'description' => __( '这里只保留可预览的虚拟页面文案；友链列表请到“外观 -> Yneko-Reimu 设置”。', 'yneko-reimu' ),
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
			'section'     => 'yneko_reimu_footer',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'yneko_reimu_customize_register' );
