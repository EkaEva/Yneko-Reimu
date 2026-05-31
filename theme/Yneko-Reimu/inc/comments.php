<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comments_canonical_post_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	if ( ! $post_id ) {
		return 0;
	}

	if ( function_exists( 'yneko_reimu_i18n_source_post_id' ) ) {
		$source_id = yneko_reimu_i18n_source_post_id( $post_id );
		if ( $source_id ) {
			return $source_id;
		}
	}

	return $post_id;
}

function yneko_reimu_comments_current_display_post_id() {
	$post_id = absint( get_queried_object_id() );
	return $post_id ? $post_id : get_the_ID();
}

function yneko_reimu_waline_icon( $name ) {
	$icons = array(
		'markdown' => '<svg width="16" height="16" aria-hidden="true" viewBox="0 0 16 16"><path d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z" fill="currentColor"></path></svg>',
		'emoji'    => '<svg viewBox="0 0 1024 1024" width="24" height="24" aria-hidden="true"><path d="M563.2 463.3 677 540c1.7 1.2 3.7 1.8 5.8 1.8.7 0 1.4-.1 2-.2 2.7-.5 5.1-2.1 6.6-4.4l25.3-37.8c1.5-2.3 2.1-5.1 1.6-7.8s-2.1-5.1-4.4-6.6l-73.6-49.1 73.6-49.1c2.3-1.5 3.9-3.9 4.4-6.6.5-2.7 0-5.5-1.6-7.8l-25.3-37.8a10.1 10.1 0 0 0-6.6-4.4c-.7-.1-1.3-.2-2-.2-2.1 0-4.1.6-5.8 1.8l-113.8 76.6c-9.2 6.2-14.7 16.4-14.7 27.5.1 11 5.5 21.3 14.7 27.4zM387 348.8h-45.5c-5.7 0-10.4 4.7-10.4 10.4v153.3c0 5.7 4.7 10.4 10.4 10.4H387c5.7 0 10.4-4.7 10.4-10.4V359.2c0-5.7-4.7-10.4-10.4-10.4zm333.8 241.3-41-20a10.3 10.3 0 0 0-8.1-.5c-2.6.9-4.8 2.9-5.9 5.4-30.1 64.9-93.1 109.1-164.4 115.2-5.7.5-9.9 5.5-9.5 11.2l3.9 45.5c.5 5.3 5 9.5 10.3 9.5h.9c94.8-8 178.5-66.5 218.6-152.7 2.4-5 .3-11.2-4.8-13.6zm186-186.1c-11.9-42-30.5-81.4-55.2-117.1-24.1-34.9-53.5-65.6-87.5-91.2-33.9-25.6-71.5-45.5-111.6-59.2-41.2-14-84.1-21.1-127.8-21.1h-1.2c-75.4 0-148.8 21.4-212.5 61.7-63.7 40.3-114.3 97.6-146.5 165.8-32.2 68.1-44.3 143.6-35.1 218.4 9.3 74.8 39.4 145 87.3 203.3.1.2.3.3.4.5l36.2 38.4c1.1 1.2 2.5 2.1 3.9 2.6 73.3 66.7 168.2 103.5 267.5 103.5 73.3 0 145.2-20.3 207.7-58.7 37.3-22.9 70.3-51.5 98.1-85 27.1-32.7 48.7-69.5 64.2-109.1 15.5-39.7 24.4-81.3 26.6-123.8 2.4-43.6-2.5-87-14.5-129zm-60.5 181.1c-8.3 37-22.8 72-43 104-19.7 31.1-44.3 58.6-73.1 81.7-28.8 23.1-61 41-95.7 53.4-35.6 12.7-72.9 19.1-110.9 19.1-82.6 0-161.7-30.6-222.8-86.2l-34.1-35.8c-23.9-29.3-42.4-62.2-55.1-97.7-12.4-34.7-18.8-71-19.2-107.9-.4-36.9 5.4-73.3 17.1-108.2 12-35.8 30-69.2 53.4-99.1 31.7-40.4 71.1-72 117.2-94.1 44.5-21.3 94-32.6 143.4-32.6 49.3 0 97 10.8 141.8 32 34.3 16.3 65.3 38.1 92 64.8 26.1 26 47.5 56 63.6 89.2 16.2 33.2 26.6 68.5 31 105.1 4.6 37.5 2.7 75.3-5.6 112.3z" fill="currentColor"></path></svg>',
		'gif'      => '<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.968 10.5H15.968V11.484H17.984V12.984H15.968V15H14.468V9H18.968V10.5V10.5ZM8.984 9C9.26533 9 9.49967 9.09367 9.687 9.281C9.87433 9.46833 9.968 9.70267 9.968 9.984V10.5H6.499V13.5H8.468V12H9.968V14.016C9.968 14.2973 9.87433 14.5317 9.687 14.719C9.49967 14.9063 9.26533 15 8.984 15H5.984C5.70267 15 5.46833 14.9063 5.281 14.719C5.09367 14.5317 5 14.2973 5 14.016V9.985C5 9.70367 5.09367 9.46933 5.281 9.282C5.46833 9.09467 5.70267 9.001 5.984 9.001H8.984V9ZM11.468 9H12.968V15H11.468V9V9Z"></path><path d="M18.5 3H5.75C3.6875 3 2 4.6875 2 6.75V18C2 20.0625 3.6875 21.75 5.75 21.75H18.5C20.5625 21.75 22.25 20.0625 22.25 18V6.75C22.25 4.6875 20.5625 3 18.5 3ZM20.75 18C20.75 19.2375 19.7375 20.25 18.5 20.25H5.75C4.5125 20.25 3.5 19.2375 3.5 18V6.75C3.5 5.5125 4.5125 4.5 5.75 4.5H18.5C19.7375 4.5 20.75 5.5125 20.75 6.75V18Z"></path></svg>',
		'image'    => '<svg viewBox="0 0 1024 1024" width="24" height="24" aria-hidden="true"><path d="M784 112H240c-88 0-160 72-160 160v480c0 88 72 160 160 160h544c88 0 160-72 160-160V272c0-88-72-160-160-160zm96 640c0 52.8-43.2 96-96 96H240c-52.8 0-96-43.2-96-96V272c0-52.8 43.2-96 96-96h544c52.8 0 96 43.2 96 96v480z" fill="currentColor"></path><path d="M352 480c52.8 0 96-43.2 96-96s-43.2-96-96-96-96 43.2-96 96 43.2 96 96 96zm0-128c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32zm462.4 379.2-3.2-3.2-177.6-177.6c-25.6-25.6-65.6-25.6-91.2 0l-80 80-36.8-36.8c-25.6-25.6-65.6-25.6-91.2 0L200 728c-4.8 6.4-8 14.4-8 24 0 17.6 14.4 32 32 32 9.6 0 16-3.2 22.4-9.6L380.8 640l134.4 134.4c6.4 6.4 14.4 9.6 24 9.6 17.6 0 32-14.4 32-32 0-9.6-4.8-17.6-9.6-24l-52.8-52.8 80-80L769.6 776c6.4 4.8 12.8 8 20.8 8 17.6 0 32-14.4 32-32 0-8-3.2-16-8-20.8z" fill="currentColor"></path></svg>',
		'preview'  => '<svg viewBox="0 0 1024 1024" width="24" height="24" aria-hidden="true"><path d="M710.816 654.301c70.323-96.639 61.084-230.578-23.705-314.843-46.098-46.098-107.183-71.109-172.28-71.109-65.008 0-126.092 25.444-172.28 71.109-45.227 46.098-70.756 107.183-70.756 172.106 0 64.923 25.444 126.007 71.194 172.106 46.099 46.098 107.184 71.109 172.28 71.109 51.414 0 100.648-16.212 142.824-47.404l126.53 126.006c7.058 7.06 16.297 10.979 26.406 10.979 10.105 0 19.343-3.919 26.402-10.979 14.467-14.467 14.467-38.172 0-52.723L710.816 654.301zm-315.107-23.265c-65.88-65.88-65.88-172.54 0-238.42 32.069-32.07 74.245-49.149 119.471-49.149 45.227 0 87.407 17.603 119.472 49.149 65.88 65.879 65.88 172.539 0 238.42-63.612 63.178-175.242 63.178-238.943 0zm0 0" fill="currentColor"></path><path d="M703.319 121.603H321.03c-109.8 0-199.469 89.146-199.469 199.38v382.034c0 109.796 89.236 199.38 199.469 199.38h207.397c20.653 0 37.384-16.645 37.384-37.299 0-20.649-16.731-37.296-37.384-37.296H321.03c-68.582 0-124.352-55.77-124.352-124.267V321.421c0-68.496 55.77-124.267 124.352-124.267h382.289c68.582 0 124.352 55.771 124.352 124.267V524.72c0 20.654 16.736 37.299 37.385 37.299 20.654 0 37.384-16.645 37.384-37.299V320.549c-.085-109.8-89.321-198.946-199.121-198.946zm0 0" fill="currentColor"></path></svg>',
		'heart'    => '<svg viewBox="0 0 1024 1024" width="20" height="20" aria-hidden="true"><path d="M512 896 142.9 526.9C52.5 436.5 52.5 289.9 142.9 199.5c90.4-90.4 237-90.4 327.4 0L512 241.2l41.7-41.7c90.4-90.4 237-90.4 327.4 0 90.4 90.4 90.4 237 0 327.4L512 896z" fill="currentColor"></path></svg>',
		'reply'    => '<svg viewBox="0 0 1024 1024" width="20" height="20" aria-hidden="true"><path d="M853.3 128H170.7C100 128 42.7 185.3 42.7 256v384c0 70.7 57.3 128 128 128h86.6v128c0 13 7.9 24.6 19.9 29.6 4 1.7 8.2 2.4 12.4 2.4 8.3 0 16.5-3.2 22.6-9.4L462.6 768h390.7c70.7 0 128-57.3 128-128V256c0-70.7-57.3-128-128-128zm64 512c0 35.3-28.7 64-64 64H436.1L321.3 818.8V704H170.7c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64h682.6c35.3 0 64 28.7 64 64v384z" fill="currentColor"></path></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function yneko_reimu_comment_toolbar( $logged_in_as ) {
	$emoji_items = array(
		'😀', '😄', '😁', '😆', '🤣', '😂', '🙂', '🙃', '😉', '😊', '🥰', '😍',
		'😘', '😋', '😜', '🤪', '🤔', '🫡', '😐', '😶', '😏', '😴', '😭', '🥺',
		'😤', '😡', '🤯', '😱', '😇', '😈', '👀', '💦', '✨', '💫', '🔥', '💯',
		'👍', '👎', '👏', '🙏', '🤝', '💪', '👌', '🤌', '✌️', '🤟', '❤️', '💖',
		'💔', '💡', '🎉', '🎊', '🍀', '🌸', '☕', '🍵', '🍰', '🍉', 'orz', 'w',
		'草', '好耶', '破防了', '笑死', '可以', '贴贴',
	);

	$emoji_buttons = '';
	foreach ( $emoji_items as $emoji ) {
		$emoji_buttons .= '<button type="button" class="reimu-comment-popover-item" data-comment-insert="' . esc_attr( $emoji ) . '">' . esc_html( $emoji ) . '</button>';
	}

	return '<div class="reimu-comment-toolbar">' .
		'<div class="reimu-comment-tools wl-actions">' .
			'<a href="https://guides.github.com/features/mastering-markdown/" class="reimu-comment-tool wl-action reimu-comment-tool--markdown" title="Markdown Guide" aria-label="' . esc_attr__( 'Markdown is supported', 'yneko-reimu' ) . '" target="_blank" rel="noopener noreferrer">' . yneko_reimu_waline_icon( 'markdown' ) . '</a>' .
			'<button type="button" class="reimu-comment-tool wl-action" title="' . esc_attr__( '表情', 'yneko-reimu' ) . '" aria-label="' . esc_attr__( '表情', 'yneko-reimu' ) . '" data-comment-tool="emoji">' . yneko_reimu_waline_icon( 'emoji' ) . '</button>' .
			'<button type="button" class="reimu-comment-tool wl-action" title="' . esc_attr__( '表情包', 'yneko-reimu' ) . '" aria-label="' . esc_attr__( '表情包', 'yneko-reimu' ) . '" data-comment-tool="gif">' . yneko_reimu_waline_icon( 'gif' ) . '</button>' .
			'<button type="button" class="reimu-comment-tool wl-action" title="' . esc_attr__( '上传图片', 'yneko-reimu' ) . '" aria-label="' . esc_attr__( '上传图片', 'yneko-reimu' ) . '" data-comment-tool="image">' . yneko_reimu_waline_icon( 'image' ) . '</button>' .
			'<button type="button" class="reimu-comment-tool wl-action" title="' . esc_attr__( '预览', 'yneko-reimu' ) . '" aria-label="' . esc_attr__( '预览', 'yneko-reimu' ) . '" data-comment-tool="preview">' . yneko_reimu_waline_icon( 'preview' ) . '</button>' .
		'</div>' .
		'<div class="reimu-comment-actions"><span class="reimu-comment-word-count"><span data-comment-word-count>0</span> 字</span><span class="reimu-comment-login">' . $logged_in_as . '</span>%1$s %2$s</div>' .
		'<div class="reimu-comment-popovers">' .
			'<div class="reimu-comment-popover" data-comment-popover="emoji" hidden><div class="reimu-comment-popover-title">' . esc_html__( '表情', 'yneko-reimu' ) . '</div><div class="reimu-comment-emoji-grid">' . $emoji_buttons . '</div></div>' .
			'<div class="reimu-comment-popover" data-comment-popover="gif" hidden><div class="reimu-comment-media-row"><input type="url" data-comment-url-input="gif" placeholder="' . esc_attr__( '插入 GIF 链接', 'yneko-reimu' ) . '"><div class="reimu-comment-media-actions"><button type="button" data-comment-url-insert="gif">' . esc_html__( '插入', 'yneko-reimu' ) . '</button><button type="button" data-comment-upload-button="gif">' . esc_html__( '上传', 'yneko-reimu' ) . '</button><input type="file" accept="image/gif" data-comment-upload-input="gif"></div></div><p class="reimu-comment-upload-login" data-comment-upload-login="gif">' . esc_html__( '登录后可上传 GIF。', 'yneko-reimu' ) . '</p><span class="reimu-comment-upload-status" data-comment-upload-status="gif"></span><div class="reimu-comment-gif-grid" data-comment-gif-library></div></div>' .
			'<div class="reimu-comment-popover" data-comment-popover="image" hidden><div class="reimu-comment-media-row"><input type="url" data-comment-url-input="image" placeholder="' . esc_attr__( '插入图片链接', 'yneko-reimu' ) . '"><div class="reimu-comment-media-actions"><button type="button" data-comment-url-insert="image">' . esc_html__( '插入', 'yneko-reimu' ) . '</button><button type="button" data-comment-upload-button="image">' . esc_html__( '上传', 'yneko-reimu' ) . '</button><input type="file" accept="image/jpeg,image/png,image/webp" data-comment-upload-input="image"></div></div><p class="reimu-comment-upload-login" data-comment-upload-login="image">' . esc_html__( '登录后可上传图片。', 'yneko-reimu' ) . '</p><span class="reimu-comment-upload-status" data-comment-upload-status="image"></span></div>' .
		'</div>' .
	'</div>';
}

function yneko_reimu_login_modal() {
	if ( is_user_logged_in() ) {
		return;
	}
	$show_wp_login = (bool) get_option( 'users_can_register' );
	?>
	<div class="reimu-login-modal" id="reimu-login-modal" aria-hidden="true">
		<div class="reimu-login-modal__mask" data-login-close></div>
		<div class="reimu-login-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-login-title">
			<button type="button" class="reimu-login-modal__close popup-btn-close" data-login-close aria-label="<?php esc_attr_e( '关闭登录窗口', 'yneko-reimu' ); ?>"></button>
			<h2 id="reimu-login-title"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></h2>
			<p class="reimu-login-modal__desc"><?php echo esc_html( $show_wp_login ? __( '使用 WordPress 账号登录后即可评论。', 'yneko-reimu' ) : __( '使用 GitHub 登录后即可评论。', 'yneko-reimu' ) ); ?></p>
			<?php do_action( 'yneko_reimu_login_modal_social' ); ?>
			<?php if ( $show_wp_login ) : ?>
			<form class="reimu-login-form" data-reimu-login-form>
				<p>
					<label for="reimu-login-user"><?php esc_html_e( '用户名或邮箱', 'yneko-reimu' ); ?></label>
					<input id="reimu-login-user" name="log" type="text" autocomplete="username" required>
				</p>
				<p>
					<label for="reimu-login-password"><?php esc_html_e( '密码', 'yneko-reimu' ); ?></label>
					<input id="reimu-login-password" name="pwd" type="password" autocomplete="current-password" required>
				</p>
				<label class="reimu-login-remember">
					<input name="rememberme" type="checkbox" value="forever">
					<span><?php esc_html_e( '记住我', 'yneko-reimu' ); ?></span>
				</label>
				<div class="reimu-login-message" data-login-message role="status" aria-live="polite"></div>
				<div class="reimu-login-actions">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" data-no-pjax><?php esc_html_e( '忘记密码？', 'yneko-reimu' ); ?></a>
					<button type="submit" class="reimu-login-submit"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></button>
				</div>
			</form>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

function yneko_reimu_ajax_login() {
	check_ajax_referer( 'yneko_reimu_ajax_login', 'nonce' );

	$credentials = array(
		'user_login'    => isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '',
		'user_password' => isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '',
		'remember'      => ! empty( $_POST['rememberme'] ),
	);

	if ( '' === $credentials['user_login'] || '' === $credentials['user_password'] ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请输入用户名和密码。', 'yneko-reimu' ),
			),
			400
		);
	}

	$user = wp_signon( $credentials, is_ssl() );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error(
			array(
				'message' => $user->get_error_message(),
			),
			403
		);
	}

	wp_send_json_success(
		array(
			'message' => esc_html__( '登录成功。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_nopriv_yneko_reimu_login', 'yneko_reimu_ajax_login' );

function yneko_reimu_ajax_comment_like() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$liked      = ! empty( $_POST['liked'] );

	if ( ! $comment_id || ! get_comment( $comment_id ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	check_ajax_referer( 'yneko_reimu_comment_like_' . $comment_id, 'nonce' );

	$count = absint( yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_count', true ) );
	$count = $liked ? max( 0, $count - 1 ) : $count + 1;
	update_comment_meta( $comment_id, '_yneko_reimu_like_count', $count );

	wp_send_json_success(
		array(
			'count' => $count,
			'liked' => ! $liked,
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );
add_action( 'wp_ajax_nopriv_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );

function yneko_reimu_render_comment_item_html( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	$previous_comment    = $GLOBALS['comment'] ?? null;
	$previous_display_url = $GLOBALS['yneko_reimu_comment_display_url'] ?? null;
	$GLOBALS['comment'] = $comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$GLOBALS['yneko_reimu_comment_display_url'] = get_permalink( (int) $comment->comment_post_ID );
	ob_start();
	yneko_reimu_comment_callback(
		$comment,
		array(
			'style'      => 'ol',
			'avatar_size'=> 56,
			'max_depth'  => get_option( 'thread_comments_depth' ),
			'type'       => 'comment',
		),
		1
	);
	$html = ob_get_clean();

	if ( null === $previous_comment ) {
		unset( $GLOBALS['comment'] );
	} else {
		$GLOBALS['comment'] = $previous_comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
	if ( null === $previous_display_url ) {
		unset( $GLOBALS['yneko_reimu_comment_display_url'] );
	} else {
		$GLOBALS['yneko_reimu_comment_display_url'] = $previous_display_url;
	}

	return $html;
}

function yneko_reimu_ajax_submit_comment() {
	check_ajax_referer( 'yneko_reimu_submit_comment', 'nonce' );

	$comment_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	unset( $comment_data['action'], $comment_data['nonce'] );
	$comment = wp_handle_comment_submission( $comment_data );
	if ( is_wp_error( $comment ) ) {
		wp_send_json_error(
			array(
				'message' => $comment->get_error_message(),
			),
			400
		);
	}

	$comment = get_comment( $comment->comment_ID );
	if ( ! $comment ) {
		wp_send_json_error(
			array(
				'message' => __( '评论提交失败。', 'yneko-reimu' ),
			),
			400
		);
	}

	$is_approved = '1' === (string) $comment->comment_approved;
	$count       = get_comments_number( (int) $comment->comment_post_ID );
	wp_send_json_success(
		array(
			'comment_id' => absint( $comment->comment_ID ),
			'parent_id'  => absint( $comment->comment_parent ),
			'approved'   => $is_approved,
			'html'       => yneko_reimu_render_comment_item_html( $comment ),
			'count'      => absint( $count ),
			'count_label'=> sprintf(
				/* translators: %s: number of comments. */
				_n( '%s 评论', '%s 评论', $count, 'yneko-reimu' ),
				number_format_i18n( $count )
			),
			'message'    => $is_approved ? __( '评论已发布。', 'yneko-reimu' ) : __( '评论已提交，正在等待审核。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_submit_comment', 'yneko_reimu_ajax_submit_comment' );
add_action( 'wp_ajax_nopriv_yneko_reimu_submit_comment', 'yneko_reimu_ajax_submit_comment' );

function yneko_reimu_comment_upload_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['enabled'] ?? '1' );
}

function yneko_reimu_comment_upload_limits() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return array(
		'image' => max( 1, absint( $settings['image_max_mb'] ?? 1 ) ) * MB_IN_BYTES,
		'gif'   => max( 1, absint( $settings['gif_max_mb'] ?? 1 ) ) * MB_IN_BYTES,
	);
}

function yneko_reimu_comment_upload_dir( $dirs ) {
	$subdir = '/yneko-reimu-comments' . gmdate( '/Y/m' );

	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;

	return $dirs;
}

function yneko_reimu_comment_temp_upload_dir( $dirs ) {
	$subdir = '/yneko-reimu-comments/tmp/' . gmdate( 'Y/m/d' );

	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;

	return $dirs;
}

function yneko_reimu_comment_temp_upload_base() {
	$uploads = wp_get_upload_dir();
	return array(
		'path' => trailingslashit( $uploads['basedir'] ) . 'yneko-reimu-comments/tmp',
		'url'  => trailingslashit( $uploads['baseurl'] ) . 'yneko-reimu-comments/tmp',
	);
}

function yneko_reimu_comment_regular_upload_base() {
	$uploads = wp_get_upload_dir();
	return array(
		'path' => trailingslashit( $uploads['basedir'] ) . 'yneko-reimu-comments',
		'url'  => trailingslashit( $uploads['baseurl'] ) . 'yneko-reimu-comments',
	);
}

function yneko_reimu_comment_url_to_path( $url ) {
	$uploads = wp_get_upload_dir();
	$baseurl = trailingslashit( $uploads['baseurl'] );
	if ( 0 !== strpos( $url, $baseurl ) ) {
		return '';
	}

	$relative = rawurldecode( substr( $url, strlen( $baseurl ) ) );
	$relative = str_replace( array( '../', '..\\' ), '', $relative );
	return trailingslashit( $uploads['basedir'] ) . wp_normalize_path( $relative );
}

function yneko_reimu_comment_extract_image_urls( $content ) {
	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', (string) $content, $matches );
	return empty( $matches[1] ) ? array() : array_values( array_unique( array_map( 'esc_url_raw', $matches[1] ) ) );
}

function yneko_reimu_comment_missing_image_url() {
	return yneko_reimu_asset_uri( 'assets/images/comment-missing.webp' );
}

function yneko_reimu_comment_resolve_image_url( $url ) {
	$url = esc_url_raw( $url );
	if ( false === strpos( $url, 'yneko-reimu-comments/' ) ) {
		return $url;
	}

	$attachment_id = attachment_url_to_postid( $url );
	if ( $attachment_id && '1' === get_post_meta( $attachment_id, '_yneko_reimu_comment_upload', true ) ) {
		$attachment_url = wp_get_attachment_url( $attachment_id );
		return $attachment_url ? esc_url_raw( $attachment_url ) : yneko_reimu_comment_missing_image_url();
	}

	$path = yneko_reimu_comment_url_to_path( $url );
	if ( $path && file_exists( $path ) ) {
		return $url;
	}

	return yneko_reimu_comment_missing_image_url();
}

function yneko_reimu_comment_upload_allowed_mimes() {
	return array(
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'webp'     => 'image/webp',
		'gif'      => 'image/gif',
	);
}

function yneko_reimu_comment_upload_is_gif_attachment( $attachment_id ) {
	return 'image/gif' === get_post_mime_type( $attachment_id );
}

function yneko_reimu_comment_gif_library( $limit = 24, $include_pending = false ) {
	$items = yneko_reimu_comment_upload_library( $limit, 'gif', $include_pending );

	if ( ! $include_pending ) {
		$items = array_values(
			array_filter(
				$items,
				function ( $item ) {
					return 'approved' === $item['status'];
				}
			)
		);
	}

	return $items;
}

function yneko_reimu_comment_upload_library( $limit = 80, $type = 'all', $include_pending = true ) {
	$type       = in_array( $type, array( 'all', 'gif', 'image' ), true ) ? $type : 'all';
	$meta_query = array(
		array(
			'key'   => '_yneko_reimu_comment_upload',
			'value' => '1',
		),
	);

	if ( 'all' !== $type ) {
		$meta_query[] = array(
			'key'   => '_yneko_reimu_comment_upload_type',
			'value' => $type,
		);
	}

	if ( $include_pending ) {
		$meta_query[] = array(
			'key'     => '_yneko_reimu_comment_upload_status',
			'value'   => array( 'approved', 'pending', 'private' ),
			'compare' => 'IN',
		);
	} else {
		$meta_query[] = array(
			'key'   => '_yneko_reimu_comment_upload_status',
			'value' => 'approved',
		);
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'gif' === $type ? 'image/gif' : 'image',
			'posts_per_page' => max( 1, absint( $limit ) ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);

	$items = array();
	foreach ( $query->posts as $attachment ) {
		$url = wp_get_attachment_url( $attachment->ID );
		if ( ! $url ) {
			continue;
		}

		$items[] = array(
			'id'     => absint( $attachment->ID ),
			'url'    => esc_url_raw( $url ),
			'title'  => get_the_title( $attachment ),
			'type'   => get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_type', true ),
			'status' => get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_status', true ),
			'user'   => absint( get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_user_id', true ) ),
			'comment_id' => absint( get_post_meta( $attachment->ID, '_yneko_reimu_comment_upload_comment_id', true ) ),
			'date'   => get_the_date( 'Y-m-d H:i', $attachment ),
		);
	}

	return $items;
}

function yneko_reimu_ajax_comment_upload() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( '登录后可上传图片。', 'yneko-reimu' ) ), 403 );
	}

	if ( ! yneko_reimu_comment_upload_enabled() ) {
		wp_send_json_error( array( 'message' => __( '评论图片上传已关闭。', 'yneko-reimu' ) ), 403 );
	}

	check_ajax_referer( 'yneko_reimu_comment_upload', 'nonce' );

	$type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'image';
	$type = 'gif' === $type ? 'gif' : 'image';

	if ( empty( $_FILES['file'] ) || ! is_array( $_FILES['file'] ) ) {
		wp_send_json_error( array( 'message' => __( '请选择要上传的文件。', 'yneko-reimu' ) ), 400 );
	}

	$file = $_FILES['file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$size = absint( $file['size'] ?? 0 );
	$limits = yneko_reimu_comment_upload_limits();
	if ( $size <= 0 || $size > $limits[ $type ] ) {
		wp_send_json_error( array( 'message' => __( '文件大小超出限制。', 'yneko-reimu' ) ), 400 );
	}

	$check = wp_check_filetype_and_ext( $file['tmp_name'] ?? '', $file['name'] ?? '', yneko_reimu_comment_upload_allowed_mimes() );
	$mime  = $check['type'] ?? '';
	if ( ! $mime || ! in_array( $mime, yneko_reimu_comment_upload_allowed_mimes(), true ) ) {
		wp_send_json_error( array( 'message' => __( '仅支持 JPG、PNG、WebP 和 GIF。', 'yneko-reimu' ) ), 400 );
	}

	if ( 'gif' === $type && 'image/gif' !== $mime ) {
		wp_send_json_error( array( 'message' => __( '请选择 GIF 文件。', 'yneko-reimu' ) ), 400 );
	}

	if ( 'image' === $type && 'image/gif' === $mime ) {
		wp_send_json_error( array( 'message' => __( '图片上传不支持 GIF，请使用 GIF 按钮。', 'yneko-reimu' ) ), 400 );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	add_filter( 'upload_dir', 'yneko_reimu_comment_temp_upload_dir' );
	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => yneko_reimu_comment_upload_allowed_mimes(),
		)
	);
	remove_filter( 'upload_dir', 'yneko_reimu_comment_temp_upload_dir' );

	if ( empty( $upload['url'] ) || ! empty( $upload['error'] ) ) {
		wp_send_json_error( array( 'message' => ! empty( $upload['error'] ) ? $upload['error'] : __( '上传失败。', 'yneko-reimu' ) ), 400 );
	}

	$is_gif = 'image/gif' === $mime;
	wp_send_json_success(
		array(
			'url'     => esc_url_raw( $upload['url'] ),
			'type'    => $is_gif ? 'gif' : 'image',
			'status'  => 'temporary',
			'message' => $is_gif ? '' : __( '图片已上传。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_upload', 'yneko_reimu_ajax_comment_upload' );

function yneko_reimu_comment_promote_upload_url( $url, $comment_id ) {
	$temp_base = yneko_reimu_comment_temp_upload_base();
	if ( 0 !== strpos( $url, trailingslashit( $temp_base['url'] ) ) ) {
		return $url;
	}

	$source_path = yneko_reimu_comment_url_to_path( $url );
	if ( ! $source_path || ! file_exists( $source_path ) ) {
		return '';
	}

	$check = wp_check_filetype_and_ext( $source_path, wp_basename( $source_path ), yneko_reimu_comment_upload_allowed_mimes() );
	$mime  = $check['type'] ?? '';
	if ( ! $mime || ! in_array( $mime, yneko_reimu_comment_upload_allowed_mimes(), true ) ) {
		wp_delete_file( $source_path );
		return '';
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$file = array(
		'name'     => wp_basename( $source_path ),
		'type'     => $mime,
		'tmp_name' => $source_path,
		'error'    => UPLOAD_ERR_OK,
		'size'     => filesize( $source_path ),
	);

	add_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );
	$_FILES['yneko_reimu_comment_promote_file'] = $file;
	$attachment_id                             = media_handle_sideload(
		$_FILES['yneko_reimu_comment_promote_file'],
		0,
		sprintf(
			/* translators: %s: original file name. */
			__( '评论上传：%s', 'yneko-reimu' ),
			sanitize_file_name( $file['name'] )
		),
		array(
			'test_form' => false,
			'mimes'     => yneko_reimu_comment_upload_allowed_mimes(),
		)
	);
	unset( $_FILES['yneko_reimu_comment_promote_file'] );
	remove_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );

	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_file( $source_path );
		return '';
	}

	$is_gif = 'image/gif' === get_post_mime_type( $attachment_id );
	$comment = get_comment( $comment_id );
	$user_id = $comment ? absint( $comment->user_id ) : 0;
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', $is_gif ? 'gif' : 'image' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status', $is_gif ? 'pending' : 'private' );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', $user_id );
	update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_comment_id', absint( $comment_id ) );

	return wp_get_attachment_url( $attachment_id );
}

function yneko_reimu_promote_comment_uploads( $comment_id, $comment_approved, $commentdata ) {
	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	if ( '' === $content || false === strpos( $content, 'yneko-reimu-comments/tmp/' ) ) {
		return;
	}

	if ( ! in_array( (string) $comment_approved, array( '1', 'approve' ), true ) ) {
		return;
	}

	$updated = $content;
	foreach ( yneko_reimu_comment_extract_image_urls( $content ) as $url ) {
		$promoted = yneko_reimu_comment_promote_upload_url( $url, $comment_id );
		if ( $promoted ) {
			$updated = str_replace( $url, $promoted, $updated );
		}
	}

	if ( $updated !== $content ) {
		wp_update_comment(
			array(
				'comment_ID'      => absint( $comment_id ),
				'comment_content' => $updated,
			)
		);
	}
}
add_action( 'comment_post', 'yneko_reimu_promote_comment_uploads', 10, 3 );

function yneko_reimu_promote_uploads_when_comment_approved( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
		return;
	}

	yneko_reimu_promote_comment_uploads( $comment->comment_ID, '1', array( 'comment_content' => $comment->comment_content ) );
}
add_action( 'wp_set_comment_status', 'yneko_reimu_promote_uploads_when_comment_approved', 10, 1 );

function yneko_reimu_comment_delete_temp_uploads_from_content( $content ) {
	foreach ( yneko_reimu_comment_extract_image_urls( $content ) as $url ) {
		$temp_base = yneko_reimu_comment_temp_upload_base();
		if ( 0 !== strpos( $url, trailingslashit( $temp_base['url'] ) ) ) {
			continue;
		}

		$path = yneko_reimu_comment_url_to_path( $url );
		if ( $path && file_exists( $path ) ) {
			wp_delete_file( $path );
		}
	}
}

function yneko_reimu_cleanup_comment_temp_uploads_on_status( $comment_id, $comment_status ) {
	if ( in_array( (string) $comment_status, array( 'approve', '1' ), true ) ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( $comment ) {
		yneko_reimu_comment_delete_temp_uploads_from_content( $comment->comment_content );
	}
}
add_action( 'wp_set_comment_status', 'yneko_reimu_cleanup_comment_temp_uploads_on_status', 10, 2 );

function yneko_reimu_cleanup_comment_temp_uploads_on_delete( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( $comment ) {
		yneko_reimu_comment_delete_temp_uploads_from_content( $comment->comment_content );
	}
}
add_action( 'delete_comment', 'yneko_reimu_cleanup_comment_temp_uploads_on_delete' );

function yneko_reimu_cleanup_expired_comment_temp_uploads() {
	$base = yneko_reimu_comment_temp_upload_base();
	$root = wp_normalize_path( $base['path'] );
	if ( ! is_dir( $root ) ) {
		return;
	}

	$now      = time();
	$max_age  = DAY_IN_SECONDS;
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	foreach ( $iterator as $item ) {
		$path = wp_normalize_path( $item->getPathname() );
		if ( 0 !== strpos( $path, $root ) ) {
			continue;
		}
		if ( $item->isFile() && ( $now - $item->getMTime() ) > $max_age ) {
			wp_delete_file( $path );
		} elseif ( $item->isDir() ) {
			@rmdir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
	}
}
add_action( 'yneko_reimu_cleanup_comment_temp_uploads', 'yneko_reimu_cleanup_expired_comment_temp_uploads' );

function yneko_reimu_schedule_comment_temp_cleanup() {
	if ( ! wp_next_scheduled( 'yneko_reimu_cleanup_comment_temp_uploads' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'yneko_reimu_cleanup_comment_temp_uploads' );
	}
}
add_action( 'init', 'yneko_reimu_schedule_comment_temp_cleanup' );

function yneko_reimu_comment_public_gif_urls() {
	$urls = array();
	foreach ( yneko_reimu_comment_gif_library( 200, false ) as $item ) {
		if ( ! empty( $item['url'] ) ) {
			$urls[] = esc_url_raw( $item['url'] );
		}
	}
	return array_values( array_unique( $urls ) );
}

function yneko_reimu_comment_is_public_gif_only( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return false;
	}

	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', $content, $matches );
	if ( empty( $matches[1] ) || 1 !== count( $matches[1] ) ) {
		return false;
	}

	$remaining = trim( preg_replace( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', '', $content ) );
	if ( '' !== $remaining ) {
		return false;
	}

	return in_array( esc_url_raw( $matches[1][0] ), yneko_reimu_comment_public_gif_urls(), true );
}

function yneko_reimu_comment_content_kind( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return '';
	}

	preg_match_all( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', $content, $matches );
	$urls = empty( $matches[1] ) ? array() : $matches[1];
	$remaining = trim( preg_replace( '/!\[[^\]]*\]\((https?:\/\/[^)\s]+)\)/i', '', $content ) );

	if ( 1 === count( $urls ) && '' === $remaining ) {
		return preg_match( '/\.gif(?:\?.*)?$/i', (string) $urls[0] ) ? 'gif' : 'image';
	}

	if ( empty( $urls ) && '' !== trim( wp_strip_all_tags( $content ) ) ) {
		return 'text';
	}

	return '';
}

function yneko_reimu_comment_identity_key( $commentdata ) {
	if ( ! empty( $commentdata['user_ID'] ) ) {
		return 'user:' . absint( $commentdata['user_ID'] );
	}
	if ( ! empty( $commentdata['comment_author_email'] ) ) {
		return 'mail:' . strtolower( sanitize_email( $commentdata['comment_author_email'] ) );
	}
	if ( ! empty( $commentdata['comment_author_IP'] ) ) {
		return 'ip:' . md5( (string) $commentdata['comment_author_IP'] );
	}
	return '';
}

function yneko_reimu_prevent_duplicate_simple_comment( $commentdata ) {
	$kind = yneko_reimu_comment_content_kind( $commentdata['comment_content'] ?? '' );
	if ( ! in_array( $kind, array( 'gif', 'image', 'text' ), true ) ) {
		return $commentdata;
	}

	$key = yneko_reimu_comment_identity_key( $commentdata );
	if ( '' === $key ) {
		return $commentdata;
	}

	$hash = md5( trim( (string) $commentdata['comment_content'] ) );
	$flag = 'yneko_reimu_simple_comment_' . md5( $key . '|' . $kind . '|' . $hash );
	if ( get_transient( $flag ) ) {
		wp_die( esc_html__( '请不要重复发送相同的评论。', 'yneko-reimu' ), 409 );
	}

	set_transient( $flag, 1, HOUR_IN_SECONDS );
	return $commentdata;
}
add_filter( 'preprocess_comment', 'yneko_reimu_prevent_duplicate_simple_comment', 5 );

function yneko_reimu_approve_public_gif_only_comment( $approved, $commentdata ) {
	if ( yneko_reimu_comment_is_public_gif_only( $commentdata['comment_content'] ?? '' ) ) {
		return 1;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'yneko_reimu_approve_public_gif_only_comment', 10, 2 );

function yneko_reimu_admin_comment_gif_upload_action() {
	if ( empty( $_POST['yneko_reimu_admin_comment_gif_upload'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	check_admin_referer( 'yneko_reimu_admin_comment_gif_upload' );

	if ( empty( $_FILES['yneko_reimu_comment_gif'] ) || ! is_array( $_FILES['yneko_reimu_comment_gif'] ) ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', 'empty', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$source = $_FILES['yneko_reimu_comment_gif']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$names  = is_array( $source['name'] ?? null ) ? $source['name'] : array( $source['name'] ?? '' );
	$limits = yneko_reimu_comment_upload_limits();
	$total  = 0;
	$done   = 0;
	$failed = 0;

	add_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );
	foreach ( array_keys( $names ) as $index ) {
		$file = array(
			'name'     => is_array( $source['name'] ?? null ) ? ( $source['name'][ $index ] ?? '' ) : ( $source['name'] ?? '' ),
			'type'     => is_array( $source['type'] ?? null ) ? ( $source['type'][ $index ] ?? '' ) : ( $source['type'] ?? '' ),
			'tmp_name' => is_array( $source['tmp_name'] ?? null ) ? ( $source['tmp_name'][ $index ] ?? '' ) : ( $source['tmp_name'] ?? '' ),
			'error'    => is_array( $source['error'] ?? null ) ? ( $source['error'][ $index ] ?? UPLOAD_ERR_NO_FILE ) : ( $source['error'] ?? UPLOAD_ERR_NO_FILE ),
			'size'     => is_array( $source['size'] ?? null ) ? ( $source['size'][ $index ] ?? 0 ) : ( $source['size'] ?? 0 ),
		);

		if ( UPLOAD_ERR_NO_FILE === absint( $file['error'] ) ) {
			continue;
		}

		++$total;
		$size  = absint( $file['size'] ?? 0 );
		$check = wp_check_filetype_and_ext( $file['tmp_name'] ?? '', $file['name'] ?? '', array( 'gif' => 'image/gif' ) );
		if ( UPLOAD_ERR_OK !== absint( $file['error'] ) || $size <= 0 || $size > $limits['gif'] || 'image/gif' !== ( $check['type'] ?? '' ) ) {
			++$failed;
			continue;
		}

		$_FILES['yneko_reimu_comment_gif_single'] = $file;
		$attachment_id                           = media_handle_upload(
			'yneko_reimu_comment_gif_single',
			0,
			array(
				'post_title' => sprintf(
					/* translators: %s: original file name. */
					__( '评论 GIF：%s', 'yneko-reimu' ),
					sanitize_file_name( $file['name'] ?? 'gif' )
				),
			),
			array(
				'test_form' => false,
				'mimes'     => array( 'gif' => 'image/gif' ),
			)
		);

		if ( is_wp_error( $attachment_id ) ) {
			++$failed;
			continue;
		}

		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload', '1' );
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_type', 'gif' );
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status', 'approved' );
		update_post_meta( $attachment_id, '_yneko_reimu_comment_upload_user_id', get_current_user_id() );
		++$done;
	}
	unset( $_FILES['yneko_reimu_comment_gif_single'] );
	remove_filter( 'upload_dir', 'yneko_reimu_comment_upload_dir' );

	if ( 0 === $total ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', 'empty', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	if ( 0 === $done ) {
		wp_safe_redirect( add_query_arg( 'yneko_comment_gif_upload', $failed ? 'invalid' : 'failed', wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ) );
		exit;
	}

	$status = $failed ? 'partial' : 'success';
	wp_safe_redirect(
		add_query_arg(
			array(
				'yneko_comment_gif_upload' => $status,
				'yneko_comment_gif_count'  => $done,
			),
			wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=yneko-reimu-settings#comments' )
		)
	);
	exit;
}
add_action( 'admin_init', 'yneko_reimu_admin_comment_gif_upload_action' );

function yneko_reimu_hide_comment_uploads_from_media_library( $query ) {
	if ( ! is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return;
	}

	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'upload' !== $screen->base ) {
		return;
	}

	if ( ! empty( $_GET['yneko_comment_uploads'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query->set(
			'meta_query',
			array(
				array(
					'key'   => '_yneko_reimu_comment_upload',
					'value' => '1',
				),
			)
		);
		return;
	}

	$meta_query = (array) $query->get( 'meta_query' );
	$meta_query[] = array(
		'key'     => '_yneko_reimu_comment_upload',
		'compare' => 'NOT EXISTS',
	);
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'yneko_reimu_hide_comment_uploads_from_media_library' );

function yneko_reimu_comment_upload_admin_action() {
	$action = isset( $_GET['yneko_comment_upload_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_upload_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$id     = isset( $_GET['attachment_id'] ) ? absint( $_GET['attachment_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $id ) {
		return;
	}

	if ( ! in_array( $action, array( 'approve', 'remove', 'delete' ), true ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}

	check_admin_referer( 'yneko_reimu_comment_upload_' . $action . '_' . $id );

	if ( '1' !== get_post_meta( $id, '_yneko_reimu_comment_upload', true ) ) {
		wp_die( esc_html__( '无效的评论上传附件。', 'yneko-reimu' ), 400 );
	}

	if ( 'approve' === $action ) {
		update_post_meta( $id, '_yneko_reimu_comment_upload_status', 'approved' );
	} elseif ( 'remove' === $action ) {
		update_post_meta( $id, '_yneko_reimu_comment_upload_status', 'private' );
	} elseif ( 'delete' === $action ) {
		wp_delete_attachment( $id, true );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_comment_upload_action', 'attachment_id', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_comment_upload_admin_action' );

function yneko_reimu_get_comment_avatar( $comment, $size = 56 ) {
	$default = function_exists( 'yneko_reimu_get_default_comment_avatar_url' ) ? yneko_reimu_get_default_comment_avatar_url() : '';

	if ( ! $default ) {
		return get_avatar( $comment, $size );
	}

	if ( empty( $comment->user_id ) ) {
		$author = get_comment_author( $comment );
		return sprintf(
			'<img alt="%1$s" src="%2$s" class="avatar avatar-%3$d photo reimu-comment-default-avatar" height="%3$d" width="%3$d" loading="lazy" decoding="async">',
			esc_attr( $author ),
			esc_url( $default ),
			absint( $size )
		);
	}

	return get_avatar( $comment, $size, $default );
}

function yneko_reimu_comment_region_from_ip( $ip ) {
	$ip = trim( (string) $ip );

	if ( '' === $ip ) {
		return '';
	}

	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		return '';
	}

	$cache_key = 'reimu_comment_region_' . md5( $ip );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return (string) $cached;
	}

	$region   = '';
	$response = wp_remote_get(
		'https://ipwho.is/' . rawurlencode( $ip ) . '?lang=zh-CN',
		array(
			'timeout'     => 2,
			'redirection' => 1,
			'user-agent'  => 'Yneko-Reimu/' . ( defined( 'YNEKO_REIMU_VERSION' ) ? YNEKO_REIMU_VERSION : '1.0' ),
		)
	);

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $data ) && ! empty( $data['success'] ) ) {
			$country_code = isset( $data['country_code'] ) ? strtoupper( (string) $data['country_code'] ) : '';
			$connection   = isset( $data['connection'] ) && is_array( $data['connection'] ) ? $data['connection'] : array();
			if ( 'CN' === $country_code ) {
				$region = ! empty( $data['region'] ) ? (string) $data['region'] : ( ! empty( $data['city'] ) ? (string) $data['city'] : '' );
				$region = preg_replace( '/(省|市|自治区|壮族自治区|回族自治区|维吾尔自治区)$/u', '', $region );
			}
			if ( '' === $region && ! empty( $connection['org'] ) ) {
				$region = (string) $connection['org'];
			}
			if ( '' === $region && ! empty( $connection['isp'] ) ) {
				$region = (string) $connection['isp'];
			}
			if ( '' === $region ) {
				$region = ! empty( $data['country'] ) ? (string) $data['country'] : '';
			}
		}
	}

	$region = trim( wp_strip_all_tags( $region ) );
	$region = mb_substr( $region, 0, 24 );
	set_transient( $cache_key, $region, 30 * DAY_IN_SECONDS );

	return $region;
}

function yneko_reimu_comment_agent_badges( $agent, $ip = '' ) {
	$agent   = (string) $agent;
	$badges  = array();
	$region  = yneko_reimu_comment_region_from_ip( $ip );
	$browser = '';
	$system  = '';

	if ( $region ) {
		$badges[] = $region;
	}

	if ( preg_match( '/Edg(?:e|A|iOS)?\/([0-9.]+)/i', $agent, $matches ) ) {
		$browser = 'Edge' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Chrome\/([0-9.]+)/i', $agent, $matches ) && false === stripos( $agent, 'Chromium' ) ) {
		$browser = 'Chrome' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Firefox\/([0-9.]+)/i', $agent, $matches ) ) {
		$browser = 'Firefox' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Version\/([0-9.]+).*Safari/i', $agent, $matches ) ) {
		$browser = 'Safari' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/MSIE\s([0-9.]+)|Trident\/.*rv:([0-9.]+)/i', $agent, $matches ) ) {
		$version  = ! empty( $matches[1] ) ? $matches[1] : $matches[2];
		$browser = 'IE' . yneko_reimu_comment_major_minor_version( $version );
	}

	if ( preg_match( '/Windows NT 10/i', $agent ) ) {
		$system = 'Windows 11';
	} elseif ( preg_match( '/Windows NT 6\.3/i', $agent ) ) {
		$system = 'Windows 8.1';
	} elseif ( preg_match( '/Windows NT 6\.1/i', $agent ) ) {
		$system = 'Windows 7';
	} elseif ( preg_match( '/Android\s?([0-9.]*)/i', $agent, $matches ) ) {
		$system = 'Android' . ( ! empty( $matches[1] ) ? ' ' . yneko_reimu_comment_major_minor_version( $matches[1] ) : '' );
	} elseif ( preg_match( '/iPhone|iPad|iPod/i', $agent ) ) {
		$system = 'iOS';
	} elseif ( preg_match( '/Mac OS X\s?([0-9_\.]*)/i', $agent, $matches ) ) {
		$system = 'macOS' . ( ! empty( $matches[1] ) ? ' ' . yneko_reimu_comment_major_minor_version( str_replace( '_', '.', $matches[1] ) ) : '' );
	} elseif ( preg_match( '/Linux/i', $agent ) ) {
		$system = 'Linux';
	}

	if ( $browser ) {
		$badges[] = $browser;
	}

	if ( $system ) {
		$badges[] = $system;
	}

	$badges = array_values( array_unique( array_filter( $badges ) ) );

	if ( empty( $badges ) ) {
		return '';
	}

	$html = '<span class="reimu-comment__badges" aria-label="' . esc_attr__( '评论环境信息', 'yneko-reimu' ) . '">';
	foreach ( $badges as $badge ) {
		$html .= '<span class="reimu-comment__badge">' . esc_html( $badge ) . '</span>';
	}
	$html .= '</span>';

	return $html;
}

function yneko_reimu_comment_major_minor_version( $version ) {
	$parts = preg_split( '/[._]/', (string) $version );
	$major = isset( $parts[0] ) && '' !== $parts[0] ? preg_replace( '/\D+/', '', $parts[0] ) : '';
	$minor = isset( $parts[1] ) && '' !== $parts[1] ? preg_replace( '/\D+/', '', $parts[1] ) : '0';

	if ( '' === $major ) {
		return '';
	}

	return $major . '.' . ( '' === $minor ? '0' : $minor );
}

function yneko_reimu_render_comment_markdown( $text ) {
	$text        = str_replace( array( "\r\n", "\r" ), "\n", (string) $text );
	$code_blocks = array();

	$text = preg_replace_callback(
		'/```(?:[a-z0-9_-]+)?\n?([\s\S]*?)```/i',
		function ( $matches ) use ( &$code_blocks ) {
			$key                 = '@@REIMU_COMMENT_CODE_' . count( $code_blocks ) . '@@';
			$code_blocks[ $key ] = '<pre><code>' . esc_html( trim( $matches[1], "\n" ) ) . '</code></pre>';
			return "\n" . $key . "\n";
		},
		$text
	);

	$html = esc_html( $text );
	$html = preg_replace_callback(
		'/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) {
			$url = yneko_reimu_comment_resolve_image_url( html_entity_decode( $matches[2] ) );
			return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( html_entity_decode( $matches[1] ) ) . '" loading="lazy" decoding="async">';
		},
		$html
	);
	$html = preg_replace_callback(
		'/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) {
			return '<a href="' . esc_url( html_entity_decode( $matches[2] ) ) . '" rel="nofollow noopener noreferrer" target="_blank">' . esc_html( html_entity_decode( $matches[1] ) ) . '</a>';
		},
		$html
	);
	$html = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $html );
	$html = preg_replace( '/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $html );
	$html = wpautop( $html );

	foreach ( $code_blocks as $key => $block ) {
		$html = str_replace( '<p>' . esc_html( $key ) . '</p>', $block, $html );
		$html = str_replace( esc_html( $key ), $block, $html );
	}

	return wp_kses_post( $html );
}

function yneko_reimu_comment_callback( $comment, $args, $depth ) {
	$comment_time = $comment->comment_date_gmt ? mysql2date( 'U', $comment->comment_date_gmt, false ) : mysql2date( 'U', $comment->comment_date, false );
	$comment_id   = get_comment_ID();
	$like_count   = absint( yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_count', true ) );
	$badges       = yneko_reimu_comment_agent_badges( $comment->comment_agent, $comment->comment_author_IP );
	$comment_link = get_comment_link( $comment );
	if ( ! empty( $GLOBALS['yneko_reimu_comment_display_url'] ) ) {
		$comment_link = untrailingslashit( (string) $GLOBALS['yneko_reimu_comment_display_url'] ) . '#comment-' . absint( $comment_id );
	}
	?>
	<li <?php comment_class( 'reimu-comment' ); ?> id="comment-<?php comment_ID(); ?>" data-comment-time="<?php echo esc_attr( $comment_time ); ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>">
		<article class="reimu-comment__body">
			<a class="reimu-comment__avatar" href="<?php echo esc_url( $comment_link ); ?>" aria-hidden="true" tabindex="-1"><?php echo yneko_reimu_get_comment_avatar( $comment, 56 ); ?></a>
			<div class="reimu-comment__content">
				<header class="reimu-comment__meta">
					<span class="reimu-comment__headline">
						<span class="reimu-comment__author"><?php comment_author_link(); ?></span>
						<a class="reimu-comment__date" href="<?php echo esc_url( $comment_link ); ?>">
							<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'Y-m-d', $comment ) ); ?></time>
						</a>
					</span>
					<?php echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</header>
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( '评论正在等待审核。', 'yneko-reimu' ); ?></p>
				<?php endif; ?>
				<div class="comment-text wl-content"><?php echo yneko_reimu_render_comment_markdown( $comment->comment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div class="reimu-comment__footer" aria-label="<?php esc_attr_e( '评论操作', 'yneko-reimu' ); ?>">
					<button type="button" class="reimu-comment-like" data-comment-like="<?php echo esc_attr( $comment_id ); ?>" data-like-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_like_' . $comment_id ) ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( '点赞', 'yneko-reimu' ); ?>">
						<?php echo yneko_reimu_waline_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span data-like-count><?php echo esc_html( $like_count ); ?></span>
					</button>
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'reply_text' => yneko_reimu_waline_icon( 'reply' ) . '<span>' . esc_html__( '回复', 'yneko-reimu' ) . '</span>',
								'before'     => '',
								'after'      => '',
							)
						)
					);
					?>
					<?php edit_comment_link( __( '编辑', 'yneko-reimu' ), '<span class="reimu-comment__edit">', '</span>' ); ?>
				</div>
			</div>
		</article>
	<?php
}

function yneko_reimu_comment_field_order( $fields ) {
	$ordered = array();

	foreach ( array( 'author', 'email', 'url', 'comment', 'cookies' ) as $key ) {
		if ( isset( $fields[ $key ] ) ) {
			$ordered[ $key ] = $fields[ $key ];
		}
	}

	foreach ( $fields as $key => $field ) {
		if ( ! isset( $ordered[ $key ] ) ) {
			$ordered[ $key ] = $field;
		}
	}

	return $ordered;
}
add_filter( 'comment_form_fields', 'yneko_reimu_comment_field_order' );

function yneko_reimu_external_comment_systems() {
	$systems = array();

	if (
		yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_enable', false )
		&& yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_repo', '' )
		&& yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_repo_id', '' )
		&& yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_category', '' )
		&& yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_category_id', '' )
	) {
		$systems['giscus'] = array(
			'label'       => 'giscus',
			'repo'        => yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_repo', '' ),
			'repo_id'     => yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_repo_id', '' ),
			'category'    => yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_category', 'General' ),
			'category_id' => yneko_reimu_get_theme_mod( 'yneko_reimu_giscus_category_id', '' ),
		);
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_utterances_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_utterances_repo', '' ) ) {
		$systems['utterances'] = array(
			'label' => 'utterances',
			'repo'  => yneko_reimu_get_theme_mod( 'yneko_reimu_utterances_repo', '' ),
		);
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_disqus_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_disqus_shortname', '' ) ) {
		$systems['disqus'] = array(
			'label'     => 'disqus',
			'shortname' => yneko_reimu_get_theme_mod( 'yneko_reimu_disqus_shortname', '' ),
		);
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_waline_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_waline_server_url', '' ) ) {
		$systems['waline'] = array(
			'label'      => 'waline',
			'server_url' => yneko_reimu_get_theme_mod( 'yneko_reimu_waline_server_url', '' ),
		);
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_twikoo_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_twikoo_env_id', '' ) ) {
		$systems['twikoo'] = array(
			'label'  => 'twikoo',
			'env_id' => yneko_reimu_get_theme_mod( 'yneko_reimu_twikoo_env_id', '' ),
		);
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_valine_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_id', '' ) && yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_key', '' ) ) {
		$systems['valine'] = array(
			'label'      => 'valine',
			'app_id'     => yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_id', '' ),
			'app_key'    => yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_key', '' ),
			'server_url' => yneko_reimu_get_theme_mod( 'yneko_reimu_valine_server_url', '' ),
		);
	}

	return $systems;
}

function yneko_reimu_render_external_comment_panel( $key, $config ) {
	$post_url = get_permalink();
	$post_id  = 'post-' . get_the_ID();
	$locale   = str_replace( '_', '-', get_locale() );
	$lang     = in_array( $locale, array( 'zh-CN', 'zh-HK', 'zh-TW' ), true ) ? $locale : substr( $locale, 0, 2 );

	switch ( $key ) {
		case 'giscus':
			?>
			<div class="comment giscus-comment" id="giscus-comment" data-aos="fade-up"></div>
			<script src="https://giscus.app/client.js"
				data-repo="<?php echo esc_attr( $config['repo'] ); ?>"
				data-repo-id="<?php echo esc_attr( $config['repo_id'] ); ?>"
				data-category="<?php echo esc_attr( $config['category'] ); ?>"
				data-category-id="<?php echo esc_attr( $config['category_id'] ); ?>"
				data-mapping="pathname"
				data-strict="0"
				data-reactions-enabled="1"
				data-emit-metadata="0"
				data-input-position="bottom"
				data-theme="preferred_color_scheme"
				data-lang="<?php echo esc_attr( $lang ); ?>"
				crossorigin="anonymous"
				async></script>
			<?php
			break;

		case 'utterances':
			?>
			<div class="comment utterances-comment" id="utterances-comment"></div>
			<script src="https://utteranc.es/client.js"
				repo="<?php echo esc_attr( $config['repo'] ); ?>"
				issue-term="pathname"
				theme="preferred-color-scheme"
				crossorigin="anonymous"
				async></script>
			<?php
			break;

		case 'disqus':
			?>
			<div class="comment disqus-comment"><div id="disqus_thread"></div></div>
			<script>
				var disqus_config = function () {
					this.page.url = <?php echo wp_json_encode( $post_url ); ?>;
					this.page.identifier = <?php echo wp_json_encode( $post_id ); ?>;
				};
				(function() {
					var d = document, s = d.createElement('script');
					s.src = 'https://' + <?php echo wp_json_encode( $config['shortname'] ); ?> + '.disqus.com/embed.js';
					s.setAttribute('data-timestamp', +new Date());
					(d.head || d.body).appendChild(s);
				})();
			</script>
			<?php
			break;

		case 'waline':
			?>
			<div class="comment waline-comment" id="waline-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.Waline) {
						window.Waline.init({ el: '#waline-comment', serverURL: <?php echo wp_json_encode( esc_url_raw( $config['server_url'] ) ); ?>, path: location.pathname });
					}
				});
			</script>
			<?php
			break;

		case 'twikoo':
			?>
			<div class="comment twikoo-comment" id="twikoo-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.twikoo) {
						window.twikoo.init({ envId: <?php echo wp_json_encode( $config['env_id'] ); ?>, el: '#twikoo-comment', path: location.pathname });
					}
				});
			</script>
			<?php
			break;

		case 'valine':
			?>
			<div class="comment valine-comment" id="valine-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.Valine) {
						new window.Valine({
							el: '#valine-comment',
							appId: <?php echo wp_json_encode( $config['app_id'] ); ?>,
							appKey: <?php echo wp_json_encode( $config['app_key'] ); ?>,
							serverURLs: <?php echo wp_json_encode( $config['server_url'] ); ?> || undefined,
							path: location.pathname
						});
					}
				});
			</script>
			<?php
			break;
	}
}
