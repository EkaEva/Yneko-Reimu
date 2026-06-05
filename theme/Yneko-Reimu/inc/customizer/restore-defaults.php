<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_customizer_restore_groups() {
	return array(
		'visual_assets'     => array(
			'label'       => __( '视觉资产', 'yneko-reimu' ),
			'description' => __( '恢复鼠标指针、加载动画图片与文案、回到顶部和赞助装饰图。', 'yneko-reimu' ),
			'settings'    => array(
				'yneko_reimu_cursor_default_url'        => '',
				'yneko_reimu_cursor_pointer_url'        => '',
				'yneko_reimu_cursor_text_url'           => '',
				'yneko_reimu_cursor_progress_url'       => '',
				'yneko_reimu_preloader_image_url'       => '',
				'yneko_reimu_preloader_text_zh'         => '未来有你...',
				'yneko_reimu_preloader_text_en'         => 'Loading...',
				'yneko_reimu_preloader_image_size'      => 150,
				'yneko_reimu_preloader_image_rotate'    => true,
				'yneko_reimu_top_icon_url'              => '',
				'yneko_reimu_sponsor_icon_url'          => '',
			),
		),
		'typography_layout' => array(
			'label'       => __( '排版与布局密度', 'yneko-reimu' ),
			'description' => __( '恢复字体、字号、行高、内容宽度、密度、圆角和阴影。', 'yneko-reimu' ),
			'settings'    => array(
				'yneko_reimu_font_body'             => 'default',
				'yneko_reimu_font_heading'          => 'default',
				'yneko_reimu_font_code'             => 'default',
				'yneko_reimu_base_font_size'        => 16,
				'yneko_reimu_article_font_size'     => 16,
				'yneko_reimu_article_line_height'   => 167,
				'yneko_reimu_content_max_width'     => 1550,
				'yneko_reimu_article_content_width' => 0,
				'yneko_reimu_layout_density'        => 'default',
				'yneko_reimu_card_radius'           => 12,
				'yneko_reimu_image_radius'          => 12,
				'yneko_reimu_shadow_strength'       => 'default',
			),
		),
		'preview_images'    => array(
			'label'       => __( '预览图片', 'yneko-reimu' ),
			'description' => __( '恢复默认横幅、卡片封面、头像/角色图和搜索背景。', 'yneko-reimu' ),
			'settings'    => array(
				'yneko_reimu_default_banner'    => '',
				'yneko_reimu_default_cover'     => '',
				'yneko_reimu_default_avatar'    => '',
				'yneko_reimu_search_background' => '',
			),
		),
		'content_display'   => array(
			'label'       => __( '内容显示', 'yneko-reimu' ),
			'description' => __( '恢复博客卡片信息、文章页模块、摘要长度、过期天数和代码折叠高度。', 'yneko-reimu' ),
			'settings'    => array(
				'yneko_reimu_excerpt_length'         => 150,
				'yneko_reimu_show_categories'        => true,
				'yneko_reimu_show_tags'              => true,
				'yneko_reimu_show_comments_num'      => true,
				'yneko_reimu_show_reading_time'      => true,
				'yneko_reimu_show_toc'               => true,
				'yneko_reimu_show_copyright'         => true,
				'yneko_reimu_show_outdated'          => true,
				'yneko_reimu_show_post_nav'          => true,
				'yneko_reimu_show_update_time'       => false,
				'yneko_reimu_outdated_days'          => 365,
				'yneko_reimu_code_expand_threshold'  => 420,
			),
		),
	);
}

function yneko_reimu_customizer_restore_group_payload() {
	$payload = array();

	foreach ( yneko_reimu_customizer_restore_groups() as $group_id => $group ) {
		$payload[ $group_id ] = array(
			'label'    => $group['label'],
			'settings' => $group['settings'],
		);
	}

	return $payload;
}

function yneko_reimu_sanitize_customizer_restore_groups( $value ) {
	$allowed = array_keys( yneko_reimu_customizer_restore_groups() );
	$items   = array();

	foreach ( explode( ',', (string) $value ) as $group_id ) {
		$group_id = sanitize_key( trim( $group_id ) );
		if ( in_array( $group_id, $allowed, true ) && ! in_array( $group_id, $items, true ) ) {
			$items[] = $group_id;
		}
	}

	return implode( ',', $items );
}

function yneko_reimu_customizer_restore_defaults_load_control_class() {
	if ( class_exists( 'Yneko_Reimu_Customize_Reset_Control' ) || ! class_exists( 'WP_Customize_Control' ) ) {
		return;
	}

	class Yneko_Reimu_Customize_Reset_Control extends WP_Customize_Control {
		/**
		 * Customizer control type.
		 *
		 * @var string
		 */
		public $type        = 'yneko_reimu_reset_group';

		/**
		 * Reset group key from the restore registry.
		 *
		 * @var string
		 */
		public $group_id    = '';

		/**
		 * Button label.
		 *
		 * @var string
		 */
		public $button_text = '';

		public function render_content() {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<button type="button" class="button yneko-reimu-reset-defaults-button" data-reset-group="<?php echo esc_attr( $this->group_id ); ?>">
				<?php echo esc_html( $this->button_text ? $this->button_text : __( '恢复此组默认值', 'yneko-reimu' ) ); ?>
			</button>
			<?php
		}
	}
}

function yneko_reimu_register_customizer_restore_defaults_section( $wp_customize ) {
	yneko_reimu_customizer_restore_defaults_load_control_class();

	$wp_customize->add_section(
		'yneko_reimu_restore_defaults',
		array(
			'title'       => __( '恢复默认', 'yneko-reimu' ),
			'description' => __( '只恢复视觉预览中的低风险外观项。点击后先更新预览，仍需点击“发布”才会保存。', 'yneko-reimu' ),
			'panel'       => 'yneko_reimu_panel',
		)
	);

	$wp_customize->add_setting(
		'yneko_reimu_customizer_reset_groups',
		array(
			'default'           => '',
			'sanitize_callback' => 'yneko_reimu_sanitize_customizer_restore_groups',
			'transport'         => 'postMessage',
		)
	);

	foreach ( yneko_reimu_customizer_restore_groups() as $group_id => $group ) {
		if ( ! class_exists( 'Yneko_Reimu_Customize_Reset_Control' ) ) {
			continue;
		}

		$control_id = 'yneko_reimu_reset_' . $group_id;
		$wp_customize->add_control(
			new Yneko_Reimu_Customize_Reset_Control(
				$wp_customize,
				$control_id,
				array(
					'label'       => $group['label'],
					'description' => $group['description'],
					'section'     => 'yneko_reimu_restore_defaults',
					'settings'    => array(),
					'group_id'    => $group_id,
					'button_text' => __( '恢复此组默认值', 'yneko-reimu' ),
				)
			)
		);
	}
}

function yneko_reimu_customizer_restore_defaults_assets() {
	$script_path = 'assets/dist/customizer-restore-defaults.js';
	wp_enqueue_script(
		'yneko-reimu-customizer-restore-defaults',
		YNEKO_REIMU_URI . '/' . $script_path,
		array( 'customize-controls' ),
		yneko_reimu_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'yneko-reimu-customizer-restore-defaults',
		'YNEKO_REIMU_CUSTOMIZER_RESTORE',
		array(
			'groups'          => yneko_reimu_customizer_restore_group_payload(),
			'trackingSetting' => 'yneko_reimu_customizer_reset_groups',
			/* translators: %s: Customizer restore group label. */
			'confirmTemplate' => __( '将“%s”恢复为默认值？预览会立即更新，但只有点击“发布”后才会保存。', 'yneko-reimu' ),
			'missingSetting'  => __( '部分设置暂时不可用，请刷新 Customizer 后重试。', 'yneko-reimu' ),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'yneko_reimu_customizer_restore_defaults_assets' );

function yneko_reimu_customizer_restore_defaults_after_save( $wp_customize ) {
	$setting = $wp_customize->get_setting( 'yneko_reimu_customizer_reset_groups' );
	if ( ! $setting ) {
		return;
	}

	$groups = yneko_reimu_sanitize_customizer_restore_groups( $setting->post_value( '' ) );
	if ( '' === $groups ) {
		return;
	}

	$registry = yneko_reimu_customizer_restore_groups();
	foreach ( explode( ',', $groups ) as $group_id ) {
		if ( empty( $registry[ $group_id ]['settings'] ) ) {
			continue;
		}

		foreach ( $registry[ $group_id ]['settings'] as $setting_id => $default ) {
			$customize_setting = $wp_customize->get_setting( $setting_id );
			if ( $customize_setting && $customize_setting->post_value( $default ) !== $default ) {
				continue;
			}

			remove_theme_mod( $setting_id );
		}
	}

	remove_theme_mod( 'yneko_reimu_customizer_reset_groups' );
}
add_action( 'customize_save_after', 'yneko_reimu_customizer_restore_defaults_after_save' );
