<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
		'edit'     => '<svg viewBox="0 0 1024 1024" width="20" height="20" aria-hidden="true"><path d="M344.869 558.446a8.338 8.338 0 0 0-1.536 2.925l-49.738 200.412c-2.852 11.703.147 24.283 7.973 33.06a29.33 29.33 0 0 0 29.989 8.558l180.662-54.272c.366 0 .44.22.732.22a7.168 7.168 0 0 0 5.559-2.56l483.255-531.968c14.336-15.8 22.235-37.376 22.235-60.855 0-26.624-10.24-53.102-28.16-72.85l-45.714-50.03A90.917 90.917 0 0 0 884.005 0c-21.212 0-40.814 8.704-55.223 24.503L345.527 556.69c-.439.44-.366 1.17-.732 1.756z m599.99-392.412-53.54 53.395-86.748-87.991 52.81-52.663c8.338-8.485 24.576-7.168 34.157 2.414l50.98 50.907a27.867 27.867 0 0 1 8.34 19.383 22.235 22.235 0 0 1-5.999 14.628zM402.286 577.83l358.765-358.4 80.092 81.334-358.108 357.523-80.75-80.53z m-36.572 153.6 19.968-73.143 53.175 53.175-73.143 19.968z m623.47-344.284a35.474 35.474 0 0 0-34.816 35.84v483.328a45.349 45.349 0 0 1-44.69 45.934H114.322a45.349 45.349 0 0 1-44.617-45.934V117.467a45.349 45.349 0 0 1 44.617-45.933h512.293A35.328 35.328 0 0 0 661.431 35.767 35.401 35.401 0 0 0 626.615 0H108.983C49.006 0 0 50.25 0 112.128v799.744C0 973.751 49.006 1024 109.056 1024h805.961C974.994 1024 1024 973.75 1024 911.872V422.839a35.474 35.474 0 0 0-34.816-35.694z" fill="currentColor"></path></svg>',
		'trash'    => '<svg viewBox="0 0 1024 1024" width="20" height="20" aria-hidden="true"><path d="M814.29 136.567H207.664c-55.752 0-101.274 13.3-101.274 56.776v26.086h808.663v-26.086c.511-42.965-45.011-56.776-100.763-56.776"></path><path d="M723.245 191.808 703.297 51.148c-3.58-27.62-29.667-50.125-57.287-50.125H376.456c-28.132 0-53.707 22.505-57.799 50.126l-19.948 141.17c-3.58 27.621 15.856 22.506 43.477 22.506h337.07c28.133-.511 47.57 4.604 43.989-23.017z m-360.6-28.643L377.99 51.66h265.463l15.344 111.505H362.645zM831.17 282.342H190.785c-36.827 0-64.959 30.177-61.378 67.005l55.24 607.648c3.58 36.827 36.316 67.005 73.655 67.005h505.35c36.828 0 70.074-30.178 73.655-67.005l55.24-607.137c3.58-36.827-24.04-67.516-61.378-67.516zM376.456 953.415H245.514l-43.476-592.816h174.418v592.816z m221.474 0H423.512V360.599H597.93v592.816z m177.487 0H646.01V360.599h172.883l-43.476 592.816z" fill="currentColor"></path></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function yneko_reimu_comment_toolbar( $logged_in_as, $identity = '' ) {
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

	return $identity . '<div class="reimu-comment-toolbar">' .
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

function yneko_reimu_comment_guest_fields_html() {
	$commenter = wp_get_current_commenter();

	return '<div class="reimu-comment-form__fields">' .
		'<p class="comment-form-author"><label class="screen-reader-text" for="author">' . esc_html__( '昵称', 'yneko-reimu' ) . '</label><input id="author" name="author" type="text" placeholder="' . esc_attr__( '昵称', 'yneko-reimu' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"></p>' .
		'<p class="comment-form-email"><label class="screen-reader-text" for="email">' . esc_html__( '邮箱', 'yneko-reimu' ) . '</label><input id="email" name="email" type="email" placeholder="' . esc_attr__( '邮箱', 'yneko-reimu' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"></p>' .
		'<p class="comment-form-url"><label class="screen-reader-text" for="url">' . esc_html__( '网址（可选）', 'yneko-reimu' ) . '</label><input id="url" name="url" type="url" placeholder="' . esc_attr__( '网址（可选）', 'yneko-reimu' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30"></p>' .
	'</div>';
}

function yneko_reimu_comment_login_link_html( $redirect = '' ) {
	$redirect = $redirect ? wp_validate_redirect( $redirect, home_url( '/' ) ) : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	return '<a class="reimu-comment-login-link" href="' . esc_url( wp_login_url( $redirect ) ) . '">' . esc_html__( '登录', 'yneko-reimu' ) . '</a>';
}

function yneko_reimu_comment_content_summary( $content, $word_count = 10 ) {
	$content = str_replace( array( "\r\n", "\r" ), "\n", (string) $content );
	$gif_count = 0;
	$image_count = 0;

	$content_without_media = preg_replace_callback(
		'/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) use ( &$gif_count, &$image_count ) {
			$alt = strtolower( html_entity_decode( $matches[1] ) );
			$url = strtolower( html_entity_decode( $matches[2] ) );
			if ( false !== strpos( $alt, 'gif' ) || preg_match( '/\.gif(?:[?#]|$)/', $url ) ) {
				$gif_count++;
			} else {
				$image_count++;
			}
			return ' ';
		},
		$content
	);

	$text = trim( wp_strip_all_tags( $content_without_media ) );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = $text ? wp_trim_words( $text, absint( $word_count ), '...' ) : '';

	$tokens = array();
	if ( $gif_count ) {
		$tokens[] = '[GIF:' . $gif_count . ']';
	}
	if ( $image_count ) {
		$tokens[] = '[Image:' . $image_count . ']';
	}

	$summary = trim( $text . ( $text && $tokens ? ' ' : '' ) . implode( ' ', $tokens ) );
	return $summary ? $summary : __( '一条评论', 'yneko-reimu' );
}
