<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_add_post_meta_boxes() {
	add_meta_box(
		'yneko-reimu-options',
		__( 'Reimu 设置', 'yneko-reimu' ),
		'yneko_reimu_render_post_options_meta_box',
		array( 'post', 'page' ),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'yneko_reimu_add_post_meta_boxes' );

function yneko_reimu_post_meta_admin_style( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_register_style( 'yneko-reimu-post-meta-admin', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-post-meta-admin' );
	wp_add_inline_style(
		'yneko-reimu-post-meta-admin',
		'.yneko-reimu-post-meta-bi{display:inline-flex;flex-direction:column;gap:2px;line-height:1.35}.yneko-reimu-post-meta-bi small{font-size:11px;color:#646970;font-weight:400}.misc-pub-section .yneko-reimu-post-meta-bi{display:inline-flex}'
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_post_meta_admin_style' );

function yneko_reimu_render_post_options_meta_box( $post ) {
	wp_nonce_field( 'yneko_reimu_save_post_options', 'yneko_reimu_post_options_nonce' );
	yneko_reimu_render_post_meta_text_fields( $post );
	yneko_reimu_render_language_meta_fields( $post );
	yneko_reimu_render_post_meta_choice_fields( $post );
}

function yneko_reimu_post_meta_text_fields() {
	return array(
		'_yneko_reimu_banner_url' => array( 'yneko-reimu-banner-url', 'yneko_reimu_banner_url', 'url', __( '自定义 Banner URL', 'yneko-reimu' ) ),
		'_yneko_reimu_cover_url'  => array( 'yneko-reimu-cover-url', 'yneko_reimu_cover_url', 'url', __( '自定义封面 URL', 'yneko-reimu' ) ),
		'_yneko_reimu_summary'    => array( 'yneko-reimu-summary', 'yneko_reimu_summary', 'textarea', __( '文章摘要/副标题', 'yneko-reimu' ) ),
		'_yneko_reimu_keywords'   => array( 'yneko-reimu-keywords', 'yneko_reimu_keywords', 'text', __( '关键词', 'yneko-reimu' ) ),
	);
}

function yneko_reimu_render_post_meta_text_fields( $post ) {
	foreach ( yneko_reimu_post_meta_text_fields() as $meta_key => $field ) {
		yneko_reimu_render_post_meta_text_field( $post, $meta_key, $field );
	}
}

function yneko_reimu_render_post_meta_text_field( $post, $meta_key, $field ) {
	list( $id, $name, $type, $label ) = $field;
	$value = yneko_reimu_get_post_meta( $post->ID, $meta_key, true );
	?>
	<p>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea class="widefat" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input class="widefat" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php endif; ?>
	</p>
	<?php
}

function yneko_reimu_post_meta_selects() {
	return array(
		'_yneko_reimu_sidebar'   => array(
			'label'   => __( '侧边栏', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随全局', 'yneko-reimu' ), 'right' => __( '右侧', 'yneko-reimu' ), 'left' => __( '左侧', 'yneko-reimu' ), 'disabled' => __( '关闭', 'yneko-reimu' ) ),
		),
		'_yneko_reimu_toc'       => array(
			'label'   => __( 'TOC', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随全局', 'yneko-reimu' ), 'show' => __( '显示', 'yneko-reimu' ), 'hide' => __( '隐藏', 'yneko-reimu' ) ),
		),
		'_yneko_reimu_copyright' => array(
			'label'   => __( '版权框', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随全局', 'yneko-reimu' ), 'show' => __( '显示', 'yneko-reimu' ), 'hide' => __( '隐藏', 'yneko-reimu' ) ),
		),
		'_yneko_reimu_outdated'  => array(
			'label'   => __( '过期提示', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随全局', 'yneko-reimu' ), 'show' => __( '显示', 'yneko-reimu' ), 'hide' => __( '隐藏', 'yneko-reimu' ) ),
		),
		'_yneko_reimu_comments'  => array(
			'label'   => __( '评论区', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随文章设置', 'yneko-reimu' ), 'show' => __( '显示', 'yneko-reimu' ), 'hide' => __( '隐藏', 'yneko-reimu' ) ),
		),
		'_yneko_reimu_sticky'    => array(
			'label'   => __( '置顶标识', 'yneko-reimu' ),
			'choices' => array( 'inherit' => __( '跟随 WordPress 置顶', 'yneko-reimu' ), 'show' => __( '显示', 'yneko-reimu' ), 'hide' => __( '隐藏', 'yneko-reimu' ) ),
		),
	);
}

function yneko_reimu_render_post_meta_choice_fields( $post ) {
	foreach ( yneko_reimu_post_meta_selects() as $key => $config ) {
		yneko_reimu_render_post_meta_choice_field( $post, $key, $config );
	}
}

function yneko_reimu_render_post_meta_choice_field( $post, $key, $config ) {
	$current = yneko_reimu_post_meta_select_value( $post->ID, $key );
	?>
	<p>
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $config['label'] ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $key ); ?>" name="yneko_reimu_meta[<?php echo esc_attr( $key ); ?>]">
			<?php foreach ( $config['choices'] as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

function yneko_reimu_post_meta_select_value( $post_id, $key ) {
	$current = yneko_reimu_meta_choice( $post_id, $key );
	if ( '_yneko_reimu_sticky' !== $key ) {
		return $current;
	}

	return '1' === $current ? 'show' : ( '0' === $current ? 'hide' : 'inherit' );
}

function yneko_reimu_post_meta_bilingual_text( $zh, $en ) {
	return '<span class="yneko-reimu-post-meta-bi"><span>' . esc_html( $zh ) . '</span><small>' . esc_html( $en ) . '</small></span>';
}

function yneko_reimu_post_meta_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_post_meta_bilingual_text( $zh, $en ) );
}

function yneko_reimu_render_language_meta_fields( $post ) {
	$context = yneko_reimu_language_meta_context( $post );
	?>
	<hr>
	<p>
		<label for="yneko-reimu-language"><?php yneko_reimu_post_meta_bilingual_label( '内容语言', 'Content language' ); ?></label>
		<select class="widefat" id="yneko-reimu-language" name="yneko_reimu_language">
			<?php foreach ( $context['languages'] as $code => $language ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $context['current_language'], $code ); ?>><?php echo esc_html( $language['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php yneko_reimu_render_translation_meta_field( $context ); ?>
	<?php
}

function yneko_reimu_language_meta_context( $post ) {
	return array(
		'current_language' => function_exists( 'yneko_reimu_i18n_post_language' ) ? yneko_reimu_i18n_post_language( $post->ID ) : 'zh_CN',
		'translation_id'   => function_exists( 'yneko_reimu_i18n_translation_id' ) ? yneko_reimu_i18n_translation_id( $post->ID ) : 0,
		'languages'        => function_exists( 'yneko_reimu_i18n_languages' ) ? yneko_reimu_i18n_languages() : array( 'zh_CN' => array( 'label' => '简体中文' ), 'en_US' => array( 'label' => 'English' ) ),
		'posts'            => yneko_reimu_language_meta_candidates( $post ),
	);
}

function yneko_reimu_language_meta_candidates( $post ) {
	return get_posts(
		array(
			'post_type'      => $post->post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page' => 200,
			'post__not_in'   => array( $post->ID ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

function yneko_reimu_render_translation_meta_field( $context ) {
	?>
	<p>
		<label for="yneko-reimu-translation-id"><?php yneko_reimu_post_meta_bilingual_label( '对应翻译文章/页面', 'Linked translation post/page' ); ?></label>
		<select class="widefat" id="yneko-reimu-translation-id" name="yneko_reimu_translation_id">
			<option value="0"><?php esc_html_e( '无对应内容', 'yneko-reimu' ); ?></option>
			<?php foreach ( $context['posts'] as $candidate ) : ?>
				<?php echo wp_kses( yneko_reimu_translation_meta_option_html( $candidate, $context ), array( 'option' => array( 'value' => true, 'selected' => true ) ) ); ?>
			<?php endforeach; ?>
		</select>
		<span class="description">
			<?php echo wp_kses_post( yneko_reimu_post_meta_bilingual_text( '用于前台语言切换。保存后主题会自动同步对方文章的对应关系。', 'Used by the front-end language switcher. The reverse relation is synced after saving.' ) ); ?>
		</span>
	</p>
	<?php
}

function yneko_reimu_translation_meta_option_html( $candidate, $context ) {
	$candidate_language = function_exists( 'yneko_reimu_i18n_post_language' ) ? yneko_reimu_i18n_post_language( $candidate->ID ) : 'zh_CN';
	$candidate_label    = isset( $context['languages'][ $candidate_language ] ) ? $context['languages'][ $candidate_language ]['label'] : $candidate_language;
	$title              = get_the_title( $candidate ) ? get_the_title( $candidate ) : __( '(无标题)', 'yneko-reimu' );

	return sprintf(
		'<option value="%1$s" %2$s>%3$s</option>',
		esc_attr( $candidate->ID ),
		selected( $context['translation_id'], $candidate->ID, false ),
		esc_html( sprintf( '[%1$s] %2$s', $candidate_label, $title ) )
	);
}
