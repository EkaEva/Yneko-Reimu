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
