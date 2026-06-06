<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_editor_asset_version( $relative_path ) {
	$path = YNEKO_REIMU_DIR . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $path ) ) {
		return YNEKO_REIMU_VERSION . '.' . filemtime( $path );
	}

	return YNEKO_REIMU_VERSION;
}

function yneko_reimu_register_editor_blocks() {
	yneko_reimu_register_editor_block_patterns();
	yneko_reimu_register_editor_block_styles();
}
add_action( 'init', 'yneko_reimu_register_editor_blocks' );

function yneko_reimu_register_editor_block_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) || ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'yneko-reimu',
		array(
			'label' => __( 'Yneko-Reimu', 'yneko-reimu' ),
		)
	);

	$patterns = array(
		'yneko-reimu/article-intro'      => yneko_reimu_editor_pattern_article_intro(),
		'yneko-reimu/two-column-note'   => yneko_reimu_editor_pattern_two_column_note(),
		'yneko-reimu/settings-table'    => yneko_reimu_editor_pattern_settings_table(),
		'yneko-reimu/code-window'       => yneko_reimu_editor_pattern_code_window(),
		'yneko-reimu/tip-notice'        => yneko_reimu_editor_pattern_notice( 'tip' ),
		'yneko-reimu/info-notice'       => yneko_reimu_editor_pattern_notice( 'info' ),
		'yneko-reimu/warning-notice'    => yneko_reimu_editor_pattern_notice( 'warning' ),
		'yneko-reimu/technical-note'    => yneko_reimu_editor_pattern_technical_note(),
	);

	foreach ( $patterns as $name => $pattern ) {
		register_block_pattern( $name, $pattern );
	}
}

function yneko_reimu_register_editor_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	register_block_style(
		'core/table',
		array(
			'name'  => 'reimu-field-table',
			'label' => __( 'Reimu 设置说明表格', 'yneko-reimu' ),
		)
	);

	register_block_style(
		'core/code',
		array(
			'name'  => 'reimu-code-window',
			'label' => __( 'Reimu 代码窗口', 'yneko-reimu' ),
		)
	);

	foreach ( yneko_reimu_editor_notice_types() as $type => $notice ) {
		register_block_style(
			'core/group',
			array(
				'name'  => 'reimu-notice-' . $type,
				'label' => $notice['label'],
			)
		);
	}
}

function yneko_reimu_enqueue_block_editor_assets() {
	wp_enqueue_style(
		'yneko-reimu-editor',
		YNEKO_REIMU_URI . '/assets/dist/reimu-editor.css',
		array(),
		yneko_reimu_editor_asset_version( 'assets/dist/reimu-editor.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'yneko_reimu_enqueue_block_editor_assets' );

function yneko_reimu_editor_pattern_article_intro() {
	return array(
		'title'      => __( '文章引言卡片', 'yneko-reimu' ),
		'categories' => array( 'yneko-reimu' ),
		'content'    => '<!-- wp:group {"className":"reimu-pattern-intro","layout":{"type":"constrained"}} --><div class="wp-block-group reimu-pattern-intro"><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html__( '在这里写下文章的核心摘要，让读者先抓住这一页最重要的情绪与信息。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
	);
}

function yneko_reimu_editor_pattern_two_column_note() {
	return array(
		'title'      => __( '双栏说明区块', 'yneko-reimu' ),
		'categories' => array( 'yneko-reimu' ),
		'content'    => '<!-- wp:columns {"className":"reimu-pattern-columns"} --><div class="wp-block-columns reimu-pattern-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>' . esc_html__( '重点', 'yneko-reimu' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( '用于放置文章中的重点信息、更新说明或推荐阅读。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3>' . esc_html__( '补充', 'yneko-reimu' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( '用于放置上下文、链接或轻量的延伸解释。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
	);
}

function yneko_reimu_editor_pattern_settings_table() {
	return array(
		'title'       => __( '设置说明表格', 'yneko-reimu' ),
		'description' => __( '用于整理设置项、默认值、说明和影响范围。', 'yneko-reimu' ),
		'categories'  => array( 'yneko-reimu' ),
		'content'     => '<!-- wp:group {"className":"yneko-guide-table-wrap","layout":{"type":"constrained"}} --><div class="wp-block-group yneko-guide-table-wrap"><!-- wp:table {"className":"is-style-reimu-field-table yneko-guide-field-table"} --><figure class="wp-block-table is-style-reimu-field-table yneko-guide-field-table"><table><thead><tr><th>' . esc_html__( '设置项', 'yneko-reimu' ) . '</th><th>' . esc_html__( '默认值', 'yneko-reimu' ) . '</th><th>' . esc_html__( '说明', 'yneko-reimu' ) . '</th><th>' . esc_html__( '影响范围', 'yneko-reimu' ) . '</th></tr></thead><tbody><tr><td><code>feature.enabled</code></td><td><code>false</code></td><td>' . esc_html__( '说明这个选项解决什么问题。', 'yneko-reimu' ) . '</td><td>' . esc_html__( '仅在满足页面上下文时加载。', 'yneko-reimu' ) . '</td></tr><tr><td><code>style.mode</code></td><td><code>auto</code></td><td>' . esc_html__( '说明推荐值和可选值。', 'yneko-reimu' ) . '</td><td>' . esc_html__( '影响前台展示，不改变历史内容。', 'yneko-reimu' ) . '</td></tr></tbody></table></figure><!-- /wp:table --></div><!-- /wp:group -->',
	);
}

function yneko_reimu_editor_pattern_code_window() {
	return array(
		'title'       => __( '代码窗口', 'yneko-reimu' ),
		'description' => __( '用于插入会在前台增强为 Reimu 代码窗口的原生代码块。', 'yneko-reimu' ),
		'categories'  => array( 'yneko-reimu' ),
		'content'     => '<!-- wp:code {"className":"is-style-reimu-code-window language-bash"} --><pre class="wp-block-code is-style-reimu-code-window language-bash"><code># ' . esc_html__( '示例命令', 'yneko-reimu' ) . "\n" . 'npm run build</code></pre><!-- /wp:code -->',
	);
}

function yneko_reimu_editor_pattern_notice( $type ) {
	$notices = yneko_reimu_editor_notice_types();
	$notice  = isset( $notices[ $type ] ) ? $notices[ $type ] : $notices['info'];

	return array(
		'title'       => $notice['label'],
		'description' => __( '用于插入主题化提示、说明或警告信息。', 'yneko-reimu' ),
		'categories'  => array( 'yneko-reimu' ),
		'content'     => '<!-- wp:group {"className":"' . esc_attr( $notice['class'] ) . '","layout":{"type":"constrained"}} --><div class="wp-block-group ' . esc_attr( $notice['class'] ) . '"><!-- wp:paragraph {"className":"custom-block-title"} --><p class="custom-block-title">' . esc_html( $notice['label'] ) . '</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>' . esc_html__( '在这里写下需要读者特别留意的说明。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
	);
}

function yneko_reimu_editor_pattern_technical_note() {
	$notice = yneko_reimu_editor_pattern_notice( 'info' );
	$table  = yneko_reimu_editor_pattern_settings_table();
	$code   = yneko_reimu_editor_pattern_code_window();

	return array(
		'title'       => __( '技术笔记段落结构', 'yneko-reimu' ),
		'description' => __( '用于快速搭建包含摘要、提示、表格和代码窗口的说明型文章段落。', 'yneko-reimu' ),
		'categories'  => array( 'yneko-reimu' ),
		'content'     => '<!-- wp:heading {"level":2} --><h2>' . esc_html__( '实现思路', 'yneko-reimu' ) . '</h2><!-- /wp:heading --><!-- wp:paragraph {"className":"article-summary"} --><p class="article-summary">' . esc_html__( '先用一两句话说明问题、结论和适用范围。', 'yneko-reimu' ) . '</p><!-- /wp:paragraph -->' . $notice['content'] . $table['content'] . $code['content'],
	);
}

function yneko_reimu_editor_notice_label( $type ) {
	$notices = yneko_reimu_editor_notice_types();

	return isset( $notices[ $type ] ) ? $notices[ $type ]['label'] : __( '说明块', 'yneko-reimu' );
}

function yneko_reimu_editor_notice_types() {
	return array(
		'tip'     => array(
			'label' => __( 'TIP 提示块', 'yneko-reimu' ),
			'class' => 'custom-block tip is-style-reimu-notice-tip',
		),
		'info'    => array(
			'label' => __( 'INFO 说明块', 'yneko-reimu' ),
			'class' => 'custom-block info is-style-reimu-notice-info',
		),
		'warning' => array(
			'label' => __( 'WARNING 警告块', 'yneko-reimu' ),
			'class' => 'custom-block warning is-style-reimu-notice-warning',
		),
	);
}
