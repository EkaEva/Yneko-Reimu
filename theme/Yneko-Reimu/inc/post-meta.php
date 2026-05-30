<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_rest_meta() {
	$text_meta = array(
		'_yneko_reimu_banner_url',
		'_yneko_reimu_cover_url',
		'_yneko_reimu_summary',
		'_yneko_reimu_keywords',
	);

	$choice_meta = array(
		'_yneko_reimu_sidebar',
		'_yneko_reimu_toc',
		'_yneko_reimu_copyright',
		'_yneko_reimu_outdated',
		'_yneko_reimu_comments',
		'_yneko_reimu_sticky',
	);

	foreach ( array( 'post', 'page' ) as $post_type ) {
		foreach ( $text_meta as $meta_key ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_textarea_field',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}

		register_post_meta(
			$post_type,
			'_yneko_reimu_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			$post_type,
			'_yneko_reimu_translation_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		foreach ( $choice_meta as $meta_key ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_key',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'yneko_reimu_register_rest_meta' );

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

	$selects = array(
		'_yneko_reimu_sidebar'   => array(
			'label'   => __( '侧边栏', 'yneko-reimu' ),
			'choices' => array(
				'inherit'  => __( '跟随全局', 'yneko-reimu' ),
				'right'    => __( '右侧', 'yneko-reimu' ),
				'left'     => __( '左侧', 'yneko-reimu' ),
				'disabled' => __( '关闭', 'yneko-reimu' ),
			),
		),
		'_yneko_reimu_toc'       => array(
			'label'   => __( 'TOC', 'yneko-reimu' ),
			'choices' => array(
				'inherit' => __( '跟随全局', 'yneko-reimu' ),
				'show'    => __( '显示', 'yneko-reimu' ),
				'hide'    => __( '隐藏', 'yneko-reimu' ),
			),
		),
		'_yneko_reimu_copyright' => array(
			'label'   => __( '版权框', 'yneko-reimu' ),
			'choices' => array(
				'inherit' => __( '跟随全局', 'yneko-reimu' ),
				'show'    => __( '显示', 'yneko-reimu' ),
				'hide'    => __( '隐藏', 'yneko-reimu' ),
			),
		),
		'_yneko_reimu_outdated'  => array(
			'label'   => __( '过期提示', 'yneko-reimu' ),
			'choices' => array(
				'inherit' => __( '跟随全局', 'yneko-reimu' ),
				'show'    => __( '显示', 'yneko-reimu' ),
				'hide'    => __( '隐藏', 'yneko-reimu' ),
			),
		),
		'_yneko_reimu_comments'  => array(
			'label'   => __( '评论区', 'yneko-reimu' ),
			'choices' => array(
				'inherit' => __( '跟随文章设置', 'yneko-reimu' ),
				'show'    => __( '显示', 'yneko-reimu' ),
				'hide'    => __( '隐藏', 'yneko-reimu' ),
			),
		),
		'_yneko_reimu_sticky'    => array(
			'label'   => __( '置顶标识', 'yneko-reimu' ),
			'choices' => array(
				'inherit' => __( '跟随 WordPress 置顶', 'yneko-reimu' ),
				'show'    => __( '显示', 'yneko-reimu' ),
				'hide'    => __( '隐藏', 'yneko-reimu' ),
			),
		),
	);
	?>
	<p>
		<label for="yneko-reimu-banner-url"><?php esc_html_e( '自定义 Banner URL', 'yneko-reimu' ); ?></label>
		<input class="widefat" type="url" id="yneko-reimu-banner-url" name="yneko_reimu_banner_url" value="<?php echo esc_attr( yneko_reimu_get_post_meta( $post->ID, '_yneko_reimu_banner_url', true ) ); ?>">
	</p>
	<p>
		<label for="yneko-reimu-cover-url"><?php esc_html_e( '自定义封面 URL', 'yneko-reimu' ); ?></label>
		<input class="widefat" type="url" id="yneko-reimu-cover-url" name="yneko_reimu_cover_url" value="<?php echo esc_attr( yneko_reimu_get_post_meta( $post->ID, '_yneko_reimu_cover_url', true ) ); ?>">
	</p>
	<p>
		<label for="yneko-reimu-summary"><?php esc_html_e( '文章摘要/副标题', 'yneko-reimu' ); ?></label>
		<textarea class="widefat" id="yneko-reimu-summary" name="yneko_reimu_summary" rows="3"><?php echo esc_textarea( yneko_reimu_get_post_meta( $post->ID, '_yneko_reimu_summary', true ) ); ?></textarea>
	</p>
	<p>
		<label for="yneko-reimu-keywords"><?php esc_html_e( '关键词', 'yneko-reimu' ); ?></label>
		<input class="widefat" type="text" id="yneko-reimu-keywords" name="yneko_reimu_keywords" value="<?php echo esc_attr( yneko_reimu_get_post_meta( $post->ID, '_yneko_reimu_keywords', true ) ); ?>">
	</p>
	<?php yneko_reimu_render_language_meta_fields( $post ); ?>
	<?php foreach ( $selects as $key => $config ) : ?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $config['label'] ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $key ); ?>" name="yneko_reimu_meta[<?php echo esc_attr( $key ); ?>]">
				<?php $current = yneko_reimu_meta_choice( $post->ID, $key ); ?>
				<?php
				if ( '_yneko_reimu_sticky' === $key ) {
					$current = '1' === $current ? 'show' : ( '0' === $current ? 'hide' : 'inherit' );
				}
				?>
				<?php foreach ( $config['choices'] as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php endforeach; ?>
	<?php
}

function yneko_reimu_post_meta_bilingual_text( $zh, $en ) {
	return '<span class="yneko-reimu-post-meta-bi"><span>' . esc_html( $zh ) . '</span><small>' . esc_html( $en ) . '</small></span>';
}

function yneko_reimu_post_meta_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_post_meta_bilingual_text( $zh, $en ) );
}

function yneko_reimu_render_language_meta_fields( $post ) {
	$current_language = function_exists( 'yneko_reimu_i18n_post_language' ) ? yneko_reimu_i18n_post_language( $post->ID ) : 'zh_CN';
	$translation_id   = function_exists( 'yneko_reimu_i18n_translation_id' ) ? yneko_reimu_i18n_translation_id( $post->ID ) : 0;
	$languages        = function_exists( 'yneko_reimu_i18n_languages' ) ? yneko_reimu_i18n_languages() : array(
		'zh_CN' => array( 'label' => '简体中文' ),
		'en_US' => array( 'label' => 'English' ),
	);
	$posts = get_posts(
		array(
			'post_type'      => $post->post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page' => 200,
			'post__not_in'   => array( $post->ID ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>
	<hr>
	<p>
		<label for="yneko-reimu-language"><?php yneko_reimu_post_meta_bilingual_label( '内容语言', 'Content language' ); ?></label>
		<select class="widefat" id="yneko-reimu-language" name="yneko_reimu_language">
			<?php foreach ( $languages as $code => $language ) : ?>
				<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $current_language, $code ); ?>><?php echo esc_html( $language['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="yneko-reimu-translation-id"><?php yneko_reimu_post_meta_bilingual_label( '对应翻译文章/页面', 'Linked translation post/page' ); ?></label>
		<select class="widefat" id="yneko-reimu-translation-id" name="yneko_reimu_translation_id">
			<option value="0"><?php esc_html_e( '无对应内容', 'yneko-reimu' ); ?></option>
			<?php foreach ( $posts as $candidate ) : ?>
				<?php
				$candidate_language = function_exists( 'yneko_reimu_i18n_post_language' ) ? yneko_reimu_i18n_post_language( $candidate->ID ) : 'zh_CN';
				$candidate_label    = isset( $languages[ $candidate_language ] ) ? $languages[ $candidate_language ]['label'] : $candidate_language;
				?>
				<option value="<?php echo esc_attr( $candidate->ID ); ?>" <?php selected( $translation_id, $candidate->ID ); ?>>
					<?php echo esc_html( sprintf( '[%1$s] %2$s', $candidate_label, get_the_title( $candidate ) ? get_the_title( $candidate ) : __( '(无标题)', 'yneko-reimu' ) ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<span class="description">
			<?php echo wp_kses_post( yneko_reimu_post_meta_bilingual_text( '用于前台语言切换。保存后主题会自动同步对方文章的对应关系。', 'Used by the front-end language switcher. The reverse relation is synced after saving.' ) ); ?>
		</span>
	</p>
	<?php
}

function yneko_reimu_save_post_options( $post_id ) {
	if ( ! isset( $_POST['yneko_reimu_post_options_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yneko_reimu_post_options_nonce'] ) ), 'yneko_reimu_save_post_options' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$url_fields = array(
		'_yneko_reimu_banner_url' => 'yneko_reimu_banner_url',
		'_yneko_reimu_cover_url'  => 'yneko_reimu_cover_url',
	);

	foreach ( $url_fields as $meta_key => $field_name ) {
		$value = isset( $_POST[ $field_name ] ) ? esc_url_raw( wp_unslash( $_POST[ $field_name ] ) ) : '';
		if ( $value ) {
			update_post_meta( $post_id, $meta_key, $value );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	$text_fields = array(
		'_yneko_reimu_summary'  => 'yneko_reimu_summary',
		'_yneko_reimu_keywords' => 'yneko_reimu_keywords',
	);

	foreach ( $text_fields as $meta_key => $field_name ) {
		$value = isset( $_POST[ $field_name ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $field_name ] ) ) : '';
		if ( $value ) {
			update_post_meta( $post_id, $meta_key, $value );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}

	$language = isset( $_POST['yneko_reimu_language'] ) ? sanitize_text_field( wp_unslash( $_POST['yneko_reimu_language'] ) ) : 'zh_CN';
	if ( ! function_exists( 'yneko_reimu_i18n_language_exists' ) || ! yneko_reimu_i18n_language_exists( $language ) ) {
		$language = 'zh_CN';
	}
	update_post_meta( $post_id, '_yneko_reimu_language', $language );

	$translation_id = isset( $_POST['yneko_reimu_translation_id'] ) ? absint( wp_unslash( $_POST['yneko_reimu_translation_id'] ) ) : 0;
	yneko_reimu_save_translation_link( $post_id, $translation_id );

	$allowed = array(
		'_yneko_reimu_sidebar'   => array( 'inherit', 'right', 'left', 'disabled' ),
		'_yneko_reimu_toc'       => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_copyright' => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_outdated'  => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_comments'  => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_sticky'    => array( 'inherit', 'show', 'hide' ),
	);
	$values  = isset( $_POST['yneko_reimu_meta'] ) && is_array( $_POST['yneko_reimu_meta'] ) ? wp_unslash( $_POST['yneko_reimu_meta'] ) : array();

	foreach ( $allowed as $meta_key => $choices ) {
		$value = isset( $values[ $meta_key ] ) ? sanitize_key( $values[ $meta_key ] ) : 'inherit';
		$value = in_array( $value, $choices, true ) ? $value : 'inherit';

		if ( '_yneko_reimu_sticky' === $meta_key ) {
			if ( 'show' === $value ) {
				update_post_meta( $post_id, '_yneko_reimu_sticky', '1' );
			} elseif ( 'hide' === $value ) {
				update_post_meta( $post_id, '_yneko_reimu_sticky', '0' );
			} else {
				delete_post_meta( $post_id, '_yneko_reimu_sticky' );
			}
			continue;
		}

		if ( 'inherit' === $value ) {
			delete_post_meta( $post_id, $meta_key );
		} else {
			update_post_meta( $post_id, $meta_key, $value );
		}
	}
}
add_action( 'save_post', 'yneko_reimu_save_post_options' );

function yneko_reimu_save_translation_link( $post_id, $translation_id ) {
	$post_id        = absint( $post_id );
	$translation_id = absint( $translation_id );

	if ( ! $post_id ) {
		return;
	}

	$old_translation = absint( get_post_meta( $post_id, '_yneko_reimu_translation_id', true ) );

	if ( $old_translation && $old_translation !== $translation_id && absint( get_post_meta( $old_translation, '_yneko_reimu_translation_id', true ) ) === $post_id ) {
		delete_post_meta( $old_translation, '_yneko_reimu_translation_id' );
	}

	if ( ! $translation_id || $translation_id === $post_id || get_post_type( $translation_id ) !== get_post_type( $post_id ) ) {
		delete_post_meta( $post_id, '_yneko_reimu_translation_id' );
		return;
	}

	update_post_meta( $post_id, '_yneko_reimu_translation_id', $translation_id );
	update_post_meta( $translation_id, '_yneko_reimu_translation_id', $post_id );

	if ( function_exists( 'yneko_reimu_i18n_post_language' ) && yneko_reimu_i18n_post_language( $translation_id ) === yneko_reimu_i18n_post_language( $post_id ) ) {
		$opposite = 'zh_CN' === yneko_reimu_i18n_post_language( $post_id ) ? 'en_US' : 'zh_CN';
		update_post_meta( $translation_id, '_yneko_reimu_language', $opposite );
	}
}
