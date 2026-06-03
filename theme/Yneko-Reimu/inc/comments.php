<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once YNEKO_REIMU_DIR . '/inc/comments/uploads.php';

function yneko_reimu_comments_canonical_post_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
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
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
	return $post_id ? $post_id : get_the_ID();
}

function yneko_reimu_comments_virtual_page_post_id( $slug ) {
	$slug = sanitize_title( $slug );
	if ( ! $slug ) {
		return 0;
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		return absint( $page->ID );
	}

	if ( 'projects' === $slug ) {
		$carrier_id = absint( get_option( 'yneko_reimu_projects_comment_post_id' ) );
		$carrier    = $carrier_id ? get_post( $carrier_id ) : null;
		if ( $carrier && 'trash' !== get_post_status( $carrier ) ) {
			return $carrier_id;
		}

		$existing_carrier = get_page_by_path( 'yneko-reimu-projects-comments', OBJECT, 'page' );
		if ( $existing_carrier && 'trash' !== get_post_status( $existing_carrier ) ) {
			$carrier_id = absint( $existing_carrier->ID );
		} else {
			$carrier_id = wp_insert_post(
				array(
					'post_title'     => 'Yneko Reimu Projects Comments',
					'post_name'      => 'yneko-reimu-projects-comments',
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_content'   => '',
					'comment_status' => 'open',
					'ping_status'    => 'closed',
				),
				true
			);
			$carrier_id = is_wp_error( $carrier_id ) ? 0 : absint( $carrier_id );
		}

		if ( $carrier_id ) {
			update_option( 'yneko_reimu_projects_comment_post_id', $carrier_id, false );
			return $carrier_id;
		}
	}

	$fallback_id = absint( get_option( 'page_on_front' ) );
	if ( ! $fallback_id ) {
		$fallback_id = absint( get_option( 'page_for_posts' ) );
	}

	return $fallback_id;
}

function yneko_reimu_default_open_projects_comments( $post_id, $post, $update ) {
	if ( $update || 'page' !== $post->post_type || 'projects' !== $post->post_name ) {
		return;
	}

	if ( 'open' === $post->comment_status ) {
		return;
	}

	remove_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10 );
	wp_update_post(
		array(
			'ID'             => absint( $post_id ),
			'comment_status' => 'open',
		)
	);
	add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );
}
add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );

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

function yneko_reimu_ajax_language_from_url( $url ) {
	if ( ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() || ! function_exists( 'yneko_reimu_i18n_url_prefix' ) ) {
		return '';
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path = trim( is_string( $path ) ? $path : '', '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home && ( $path === $home || 0 === strpos( $path, $home . '/' ) ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}

	$prefix = trim( (string) yneko_reimu_i18n_url_prefix(), '/' );
	return ( $prefix && ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ) ? 'en_US' : 'zh_CN';
}

function yneko_reimu_ajax_set_language_from_redirect( $redirect ) {
	$language = yneko_reimu_ajax_language_from_url( $redirect );
	if ( ! $language || ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() ) {
		return;
	}

	$GLOBALS['yneko_reimu_current_language'] = $language;
	if ( 'en_US' === $language ) {
		$mofile = YNEKO_REIMU_DIR . '/languages/en_US.mo';
		if ( file_exists( $mofile ) ) {
			unload_textdomain( 'yneko-reimu' );
			load_textdomain( 'yneko-reimu', $mofile, 'en_US' );
		}
	}
}

function yneko_reimu_comment_user_profile_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( $user && ! empty( $user->user_url ) ) {
		return esc_url_raw( $user->user_url );
	}

	return '';
}

function yneko_reimu_comment_user_github_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	foreach ( array( '_yneko_reimu_github_url', '_yneko_github_url' ) as $meta_key ) {
		$url = get_user_meta( $user_id, $meta_key, true );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	return '';
}

function yneko_reimu_comment_tag_reserved_labels() {
	$labels = array(
		'站长',
		'管理员',
		'管理員',
		'博主',
		'作者',
		'编辑',
		'訂閱者',
		'订阅者',
		'贡献者',
		'貢獻者',
		'版主',
		'官方',
		'会员',
		'會員',
		'admin',
		'administrator',
		'owner',
		'webmaster',
		'blogger',
		'author',
		'editor',
		'subscriber',
		'contributor',
		'moderator',
		'official',
		'member',
		'yko',
	);

	if ( function_exists( 'yneko_reimu_settings_user_badges' ) ) {
		$config = yneko_reimu_settings_user_badges();
		if ( ! empty( $config['special'] ) && is_array( $config['special'] ) ) {
			foreach ( $config['special'] as $key => $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				if ( function_exists( 'yneko_reimu_user_badge_base_definitions' ) ) {
					$definitions = yneko_reimu_user_badge_base_definitions();
					if ( isset( $definitions[ $key ] ) ) {
						$labels[] = $definitions[ $key ]['title_zh'] ?? '';
						$labels[] = $definitions[ $key ]['title_en'] ?? '';
						$labels[] = $definitions[ $key ]['zh'] ?? '';
						$labels[] = $definitions[ $key ]['en'] ?? '';
					}
				}
				foreach ( array( 'zh', 'en' ) as $lang_key ) {
					if ( ! empty( $row[ $lang_key ] ) ) {
						$labels[] = $row[ $lang_key ];
					}
				}
			}
		}
		if ( ! empty( $config['blocklist'] ) ) {
			foreach ( preg_split( '#/+#u', (string) $config['blocklist'] ) as $blocked ) {
				$labels[] = $blocked;
			}
		}
	}

	return array_values( array_unique( array_map( static function ( $label ) {
		return trim( mb_strtolower( wp_strip_all_tags( (string) $label ) ) );
	}, $labels ) ) );
}

function yneko_reimu_comment_tag_label_is_reserved( $label ) {
	$label = trim( mb_strtolower( wp_strip_all_tags( (string) $label ) ) );
	if ( '' === $label ) {
		return false;
	}

	return in_array( $label, yneko_reimu_comment_tag_reserved_labels(), true );
}

function yneko_reimu_comment_tag_review_enabled() {
	$config = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	return '1' === (string) ( $config['review_enabled'] ?? '0' );
}

function yneko_reimu_comment_user_can_bypass_tag_review( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	return $user_id && ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'moderate_comments' ) );
}

function yneko_reimu_comment_custom_tag_storage_limit() {
	return 5;
}

function yneko_reimu_comment_badge_display_limit() {
	return 2;
}

function yneko_reimu_comment_tag_id( $id = '' ) {
	$id = sanitize_key( (string) $id );
	if ( '' !== $id ) {
		return $id;
	}

	return 'tag_' . substr( md5( wp_generate_uuid4() . '|' . microtime( true ) ), 0, 16 );
}

function yneko_reimu_comment_normalize_tag_list( $stored, $limit = null ) {
	if ( null === $limit ) {
		$limit = yneko_reimu_comment_custom_tag_storage_limit();
	}
	$stored = is_array( $stored ) ? $stored : array();
	$tags   = array();
	foreach ( $stored as $tag ) {
		if ( count( $tags ) >= $limit || ! is_array( $tag ) ) {
			break;
		}

		$label = yneko_reimu_sanitize_comment_tag_label( $tag['label'] ?? '' );
		if ( '' === $label || yneko_reimu_comment_tag_label_is_reserved( $label ) ) {
			continue;
		}

		$color = sanitize_hex_color( $tag['color'] ?? '' );
		$clean_tag = array(
			'id'      => yneko_reimu_comment_tag_id( $tag['id'] ?? '' ),
			'label'   => $label,
			'color'   => $color ? $color : '#3b82f6',
			'enabled' => '0' === (string) ( $tag['enabled'] ?? '1' ) ? '0' : '1',
		);
		if ( ! empty( $tag['old_id'] ) ) {
			$clean_tag['old_id'] = sanitize_key( $tag['old_id'] );
		}
		if ( ! empty( $tag['old_label'] ) ) {
			$clean_tag['old_label'] = yneko_reimu_sanitize_comment_tag_label( $tag['old_label'] );
		}
		$tags[] = $clean_tag;
	}
	return $tags;
}

function yneko_reimu_comment_user_pending_tags( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$stored = get_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', true );
	if ( ! is_array( $stored ) ) {
		return array();
	}

	$normalized = yneko_reimu_comment_normalize_tag_list( $stored, yneko_reimu_comment_custom_tag_storage_limit() );
	if ( $normalized !== $stored ) {
		if ( $normalized ) {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $normalized );
		} else {
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
		}
	}

	return $normalized;
}

function yneko_reimu_comment_tag_map_by_id( $tags ) {
	$map = array();
	foreach ( is_array( $tags ) ? $tags : array() as $tag ) {
		if ( ! is_array( $tag ) || empty( $tag['id'] ) ) {
			continue;
		}
		$map[ sanitize_key( $tag['id'] ) ] = $tag;
	}
	return $map;
}

function yneko_reimu_comment_tags_same_label( $left, $right ) {
	return mb_strtolower( (string) $left ) === mb_strtolower( (string) $right );
}

function yneko_reimu_comment_prepare_reviewed_tags( $current_tags, $submitted_tags ) {
	$current_tags  = yneko_reimu_comment_normalize_tag_list( $current_tags, yneko_reimu_comment_custom_tag_storage_limit() );
	$submitted_tags = yneko_reimu_comment_normalize_tag_list( $submitted_tags, yneko_reimu_comment_custom_tag_storage_limit() );
	$current_map   = yneko_reimu_comment_tag_map_by_id( $current_tags );
	$active        = array();
	$pending       = array();

	foreach ( $submitted_tags as $submitted ) {
		$id      = sanitize_key( $submitted['id'] ?? '' );
		$current = $id && isset( $current_map[ $id ] ) ? $current_map[ $id ] : null;
		if ( $current ) {
			if ( yneko_reimu_comment_tags_same_label( $current['label'] ?? '', $submitted['label'] ?? '' ) ) {
				$current['color']   = $submitted['color'];
				$current['enabled'] = $submitted['enabled'];
				$active[] = $current;
			} else {
				$active[] = $current;
				$pending[] = array_merge(
					$submitted,
					array(
						'id'      => $id,
						'old_id'  => $id,
						'old_label' => $current['label'],
					)
				);
			}
			continue;
		}

		$pending[] = $submitted;
	}

	return array(
		'active'  => yneko_reimu_comment_normalize_tag_list( $active, yneko_reimu_comment_custom_tag_storage_limit() ),
		'pending' => yneko_reimu_comment_normalize_tag_list( $pending, yneko_reimu_comment_custom_tag_storage_limit() ),
	);
}

function yneko_reimu_sanitize_comment_tag_label( $label ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	$label = preg_replace( '/[\r\n\t]+/u', ' ', $label );
	$label = preg_replace( '/\s{2,}/u', ' ', $label );
	$label = trim( $label );
	if ( '' === $label ) {
		return '';
	}

	return mb_substr( $label, 0, 8 );
}

function yneko_reimu_comment_user_custom_tags( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$stored = get_user_meta( $user_id, '_yneko_reimu_comment_tags', true );
	if ( ! is_array( $stored ) ) {
		return array();
	}

	$normalized = yneko_reimu_comment_normalize_tag_list( $stored, yneko_reimu_comment_custom_tag_storage_limit() );
	if ( $normalized !== $stored ) {
		if ( $normalized ) {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags', $normalized );
		} else {
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
		}
	}

	return $normalized;
}

function yneko_reimu_comment_badges_enabled() {
	$config = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	return '0' !== (string) ( $config['enabled'] ?? '1' );
}

function yneko_reimu_comment_badge_label_for_language( $row, $fallback ) {
	$row = is_array( $row ) ? $row : array();
	$language = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : get_locale();
	$primary  = ( 0 === strpos( (string) $language, 'en' ) ) ? 'en' : 'zh';
	$label    = trim( (string) ( $row[ $primary ] ?? '' ) );
	if ( '' === $label ) {
		$label = trim( (string) ( $row[ 'en' === $primary ? 'zh' : 'en' ] ?? '' ) );
	}
	return '' !== $label ? $label : $fallback;
}

function yneko_reimu_comment_site_owner_user_id() {
	$users = get_users(
		array(
			'role'    => 'administrator',
			'orderby' => 'ID',
			'order'   => 'ASC',
			'number'  => 1,
			'fields'  => 'ID',
		)
	);
	return ! empty( $users[0] ) ? absint( $users[0] ) : 0;
}

function yneko_reimu_comment_special_badge_priority() {
	return array( 'owner', 'admin', 'editor', 'author', 'contributor', 'yko', 'subscriber' );
}

function yneko_reimu_comment_user_special_badge_types( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$user  = get_userdata( $user_id );
	$roles = $user ? array_map( 'sanitize_key', (array) $user->roles ) : array();
	$types = array();
	if ( yneko_reimu_comment_site_owner_user_id() === $user_id ) {
		$types = yneko_reimu_comment_special_badge_priority();
	} else {
		if ( in_array( 'administrator', $roles, true ) ) {
			$types[] = 'admin';
		} elseif ( in_array( 'editor', $roles, true ) ) {
			$types[] = 'editor';
		} elseif ( in_array( 'author', $roles, true ) ) {
			$types[] = 'author';
		} elseif ( in_array( 'contributor', $roles, true ) ) {
			$types[] = 'contributor';
		} elseif ( in_array( 'subscriber', $roles, true ) ) {
			$types[] = 'subscriber';
		}
		$types[] = 'yko';
	}

	$types = array_values( array_unique( $types ) );
	$priority = array_flip( yneko_reimu_comment_special_badge_priority() );
	usort(
		$types,
		static function ( $a, $b ) use ( $priority ) {
			return ( $priority[ $a ] ?? 99 ) <=> ( $priority[ $b ] ?? 99 );
		}
	);
	return $types;
}

function yneko_reimu_comment_user_special_badges( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$config  = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	$special = isset( $config['special'] ) && is_array( $config['special'] ) ? $config['special'] : array();
	$types   = yneko_reimu_comment_user_special_badge_types( $user_id );

	$fallbacks = array(
		'owner'       => __( '站长', 'yneko-reimu' ),
		'admin'       => __( '管理员', 'yneko-reimu' ),
		'yko'         => 'Yko',
		'subscriber'  => __( '订阅者', 'yneko-reimu' ),
		'contributor' => __( '贡献者', 'yneko-reimu' ),
		'author'      => __( '作者', 'yneko-reimu' ),
		'editor'      => __( '编辑', 'yneko-reimu' ),
	);

	$badges = array();
	foreach ( yneko_reimu_comment_special_badge_priority() as $type ) {
		if ( ! in_array( $type, $types, true ) ) {
			continue;
		}
		$row = isset( $special[ $type ] ) && is_array( $special[ $type ] ) ? $special[ $type ] : array();
		if ( '0' === (string) ( $row['enabled'] ?? '1' ) ) {
			continue;
		}
		if ( ! yneko_reimu_comment_badges_enabled() && ! in_array( $type, array( 'owner', 'admin' ), true ) ) {
			continue;
		}
		$badges[] = array(
			'type'  => $type,
			'label' => yneko_reimu_comment_badge_label_for_language( $row, $fallbacks[ $type ] ?? $type ),
		);
	}

	return $badges;
}

function yneko_reimu_comment_user_special_badge( $user_id ) {
	$badges = yneko_reimu_comment_user_special_badges( $user_id );
	return $badges ? $badges[0] : array();
}

function yneko_reimu_comment_user_hidden_special_badges( $user_id ) {
	$hidden = get_user_meta( absint( $user_id ), '_yneko_reimu_comment_hidden_special_badges', true );
	return is_array( $hidden ) ? array_map( 'sanitize_key', $hidden ) : array();
}

function yneko_reimu_comment_user_tags_payload( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$payload = array();
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	$hidden  = yneko_reimu_comment_user_hidden_special_badges( $user_id );
	$touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', true );
	foreach ( $special_badges as $index => $special ) {
		$enabled = $touched ? ! in_array( $special['type'], $hidden, true ) : 0 === $index;
		$payload[] = array(
			'type'    => 'special',
			'key'     => $special['type'],
			'label'   => $special['label'],
			'color'   => '',
			'enabled' => $enabled ? '1' : '0',
		);
	}

	$enabled_special_count = count(
		array_filter(
			$payload,
			static function ( $tag ) {
				return is_array( $tag ) && 'special' === ( $tag['type'] ?? '' ) && '0' !== (string) ( $tag['enabled'] ?? '1' );
			}
		)
	);
	$display_limit = yneko_reimu_comment_badge_display_limit();
	$custom_slots  = max( 0, $display_limit - $enabled_special_count );
	if ( ! yneko_reimu_comment_badges_enabled() ) {
		$custom_slots = 0;
	}
	$enabled_custom_count = 0;
	foreach ( yneko_reimu_comment_user_custom_tags( $user_id ) as $tag ) {
		$enabled = '0' !== (string) ( $tag['enabled'] ?? '1' ) && $enabled_custom_count < $custom_slots;
		if ( $enabled ) {
			$enabled_custom_count++;
		}
		$payload[] = array(
			'type' => 'custom',
			'id'   => $tag['id'],
			'key'  => '',
			'label' => $tag['label'],
			'color' => $tag['color'],
			'enabled' => $enabled ? '1' : '0',
		);
	}

	return $payload;
}

function yneko_reimu_comment_user_badges_html( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}

	$badges = array();
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	$hidden  = yneko_reimu_comment_user_hidden_special_badges( $user_id );
	$touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', true );
	foreach ( $special_badges as $index => $special ) {
		$enabled = $touched ? ! in_array( $special['type'], $hidden, true ) : 0 === $index;
		if ( ! $enabled ) {
			continue;
		}
		$badges[] = array(
			'label' => $special['label'],
			'class' => 'reimu-comment-user-tag--' . sanitize_html_class( $special['type'] ),
			'style' => '',
		);
	}

	$display_limit = yneko_reimu_comment_badge_display_limit();
	if ( yneko_reimu_comment_badges_enabled() ) {
		foreach ( yneko_reimu_comment_user_custom_tags( $user_id ) as $tag ) {
			if ( count( $badges ) >= $display_limit ) {
				break;
			}
			if ( '0' === (string) ( $tag['enabled'] ?? '1' ) ) {
				continue;
			}
			$badges[] = array(
				'label' => $tag['label'],
				'class' => 'reimu-comment-user-tag--custom',
				'style' => '--reimu-comment-tag-color:' . $tag['color'] . ';',
			);
		}
	}

	$badges = array_slice( $badges, 0, $display_limit );

	if ( empty( $badges ) ) {
		return '';
	}

	$html = '<span class="reimu-comment-user-tags" aria-label="' . esc_attr__( '用户标签', 'yneko-reimu' ) . '">';
	foreach ( $badges as $badge ) {
		$html .= '<span class="reimu-comment-user-tag ' . esc_attr( $badge['class'] ) . '"';
		if ( ! empty( $badge['style'] ) ) {
			$html .= ' style="' . esc_attr( $badge['style'] ) . '"';
		}
		$html .= '>' . esc_html( $badge['label'] ) . '</span>';
	}
	$html .= '</span>';

	return $html;
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

function yneko_reimu_normalize_user_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	if ( ! preg_match( '#^[a-z][a-z0-9+.-]*://#i', $url ) && preg_match( '#^[^\s/@]+\.[^\s]+#', $url ) ) {
		$url = 'https://' . $url;
	}

	return esc_url_raw( $url );
}

function yneko_reimu_user_avatar_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$custom = get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
	return $custom ? esc_url_raw( $custom ) : '';
}

function yneko_reimu_user_profile_avatar_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$custom = yneko_reimu_user_avatar_url( $user_id );
	if ( $custom ) {
		return $custom;
	}

	foreach ( array( '_yneko_reimu_github_avatar_url', '_yneko_github_avatar_url' ) as $meta_key ) {
		$avatar = get_user_meta( $user_id, $meta_key, true );
		if ( $avatar ) {
			return esc_url_raw( $avatar );
		}
	}

	return '';
}

function yneko_reimu_user_review_status_meta_key( $type ) {
	$type = sanitize_key( $type );
	if ( ! in_array( $type, array( 'avatar', 'tags', 'comments' ), true ) ) {
		return '';
	}

	return 'avatar' === $type ? '_yneko_reimu_avatar_status' : '_yneko_reimu_' . $type . '_status';
}

function yneko_reimu_set_user_review_status( $user_id, $type, $status, $comment_id = 0 ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	$status  = sanitize_key( $status );
	if ( ! $user_id || ! $key || ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
		return;
	}

	update_user_meta( $user_id, $key, $status );
	update_user_meta( $user_id, $key . '_time', time() );
	if ( $comment_id ) {
		update_user_meta( $user_id, $key . '_comment_id', absint( $comment_id ) );
	}
}

function yneko_reimu_increment_user_review_status_count( $user_id, $type, $status, $comment_id = 0 ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	$status  = sanitize_key( $status );
	if ( ! $user_id || ! $key || ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
		return;
	}

	$current_status = (string) get_user_meta( $user_id, $key, true );
	$count_key      = $key . '_count';
	$count          = absint( get_user_meta( $user_id, $count_key, true ) );
	update_user_meta( $user_id, $key, $status );
	update_user_meta( $user_id, $key . '_time', time() );
	update_user_meta( $user_id, $count_key, $status === $current_status ? max( 1, $count + 1 ) : 1 );
	if ( $comment_id ) {
		update_user_meta( $user_id, $key . '_comment_id', absint( $comment_id ) );
	}
}

function yneko_reimu_clear_user_review_status( $user_id, $type ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	if ( ! $user_id || ! $key ) {
		return;
	}

	delete_user_meta( $user_id, $key );
	delete_user_meta( $user_id, $key . '_time' );
	delete_user_meta( $user_id, $key . '_comment_id' );
	delete_user_meta( $user_id, $key . '_count' );
}

function yneko_reimu_user_pending_comment_review_count( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return 0;
	}

	$comment_ids = array();
	foreach ( get_comments( array( 'user_id' => $user_id, 'status' => 'hold', 'fields' => 'ids', 'number' => 300 ) ) as $comment_id ) {
		$comment_ids[ absint( $comment_id ) ] = true;
	}
	foreach ( yneko_reimu_comment_upload_library( 300, 'all', true ) as $item ) {
		if ( absint( $item['user'] ?? 0 ) !== $user_id ) {
			continue;
		}
		if ( in_array( (string) ( $item['status'] ?? '' ), array( 'pending', 'revoked' ), true ) ) {
			$comment_id = absint( $item['comment_id'] ?? 0 );
			if ( $comment_id ) {
				$comment_ids[ $comment_id ] = true;
			}
		}
	}
	foreach ( yneko_reimu_comment_pending_temp_uploads( 300 ) as $item ) {
		if ( absint( $item['user'] ?? 0 ) === $user_id ) {
			$comment_id = absint( $item['comment_id'] ?? 0 );
			if ( $comment_id ) {
				$comment_ids[ $comment_id ] = true;
			}
		}
	}
	return count( $comment_ids );
}

function yneko_reimu_user_review_status_payload( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$payload = array();
	foreach ( array( 'avatar', 'tags', 'comments' ) as $type ) {
		$key    = yneko_reimu_user_review_status_meta_key( $type );
		$status = $key ? (string) get_user_meta( $user_id, $key, true ) : '';
		if ( ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
			continue;
		}
		$payload[ $type ] = array(
			'status'    => $status,
			'timestamp' => absint( get_user_meta( $user_id, $key . '_time', true ) ),
			'commentId' => absint( get_user_meta( $user_id, $key . '_comment_id', true ) ),
			'count'     => absint( get_user_meta( $user_id, $key . '_count', true ) ),
		);
		if ( 'pending' === $status && 'tags' === $type ) {
			$payload[ $type ]['count'] = count( yneko_reimu_comment_user_pending_tags( $user_id ) );
		} elseif ( 'pending' === $status && 'comments' === $type ) {
			$payload[ $type ]['count'] = max( 1, yneko_reimu_user_pending_comment_review_count( $user_id ) );
		} elseif ( empty( $payload[ $type ]['count'] ) ) {
			$payload[ $type ]['count'] = 1;
		}
	}

	return $payload;
}

function yneko_reimu_user_review_primary_status_html( $user_id ) {
	$statuses = yneko_reimu_user_review_status_payload( $user_id );
	$priority = array( 'avatar', 'tags', 'comments' );
	$html     = '';
	foreach ( $priority as $type ) {
		if ( empty( $statuses[ $type ]['status'] ) ) {
			continue;
		}
		$status = (string) $statuses[ $type ]['status'];
		$label  = '';
		$class  = 'reimu-comment-current-user__status';
		if ( 'avatar' === $type ) {
			$label = 'pending' === $status ? __( '头像审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '头像审核不通过', 'yneko-reimu' ) : __( '头像已更新', 'yneko-reimu' ) );
		} elseif ( 'tags' === $type ) {
			$label = 'pending' === $status ? __( '标签审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '标签审核不通过', 'yneko-reimu' ) : __( '标签已更新', 'yneko-reimu' ) );
		} else {
			$label = 'pending' === $status ? __( '评论审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '评论审核不通过', 'yneko-reimu' ) : __( '评论已更新', 'yneko-reimu' ) );
		}
		if ( 'rejected' === $status ) {
			$class .= ' is-error';
		} elseif ( 'updated' === $status ) {
			$class .= ' is-success';
		} else {
			$class .= ' is-pending';
		}
		$count = absint( $statuses[ $type ]['count'] ?? 0 );
		$html .= '<span class="' . esc_attr( $class ) . '" data-profile-inline-status data-profile-status-kind="' . esc_attr( $type ) . '" data-profile-status-state="' . esc_attr( $status ) . '">' . esc_html( $label );
		if ( 'pending' === $status && $count > 1 && in_array( $type, array( 'tags', 'comments' ), true ) ) {
			$html .= '<b class="reimu-comment-current-user__status-count">' . esc_html( (string) $count ) . '</b>';
		}
		$html .= '</span>';
	}

	return $html ? '<span class="reimu-comment-current-user__statuses" data-profile-inline-status-list>' . $html . '</span>' : '';
}

function yneko_reimu_handle_profile_avatar_upload( $user_id, $file ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return new WP_Error( 'invalid_user', __( '请先登录。', 'yneko-reimu' ) );
	}
	if ( ! yneko_reimu_avatar_upload_enabled() ) {
		return new WP_Error( 'avatar_upload_disabled', __( '当前未开启头像上传。', 'yneko-reimu' ) );
	}
	if ( empty( $file['name'] ) ) {
		return new WP_Error( 'avatar_file_missing', __( '请选择头像文件。', 'yneko-reimu' ) );
	}
	if ( ! empty( $file['size'] ) && absint( $file['size'] ) > yneko_reimu_avatar_upload_limit() ) {
		return new WP_Error( 'avatar_too_large', __( '头像文件超过大小限制。', 'yneko-reimu' ) );
	}

	$allowed_mimes = array( 'image/jpeg', 'image/png', 'image/webp' );
	$file_type     = wp_check_filetype_and_ext( $file['tmp_name'] ?? '', $file['name'] ?? '' );
	$mime_type     = isset( $file_type['type'] ) ? (string) $file_type['type'] : '';
	if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
		return new WP_Error( 'avatar_invalid_type', __( '头像仅支持 JPG、PNG 或 WebP。', 'yneko-reimu' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( yneko_reimu_avatar_review_enabled() ) {
		$GLOBALS['yneko_reimu_avatar_upload_pending'] = true;
	}
	add_filter( 'upload_dir', 'yneko_reimu_avatar_upload_dir' );
	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => array(
				'jpg|jpeg' => 'image/jpeg',
				'png'      => 'image/png',
				'webp'     => 'image/webp',
			),
		)
	);
	remove_filter( 'upload_dir', 'yneko_reimu_avatar_upload_dir' );
	unset( $GLOBALS['yneko_reimu_avatar_upload_pending'] );

	if ( empty( $upload['url'] ) || ! empty( $upload['error'] ) ) {
		return new WP_Error( 'avatar_upload_failed', ! empty( $upload['error'] ) ? $upload['error'] : __( '头像上传失败。', 'yneko-reimu' ) );
	}

	$avatar_url = esc_url_raw( $upload['url'] );
	$pending    = yneko_reimu_avatar_review_enabled();
	if ( $pending ) {
		update_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', $avatar_url );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'pending' );
	} else {
		$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
		if ( $current && $current !== $avatar_url ) {
			yneko_reimu_delete_upload_by_url( $current );
		}
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $avatar_url );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	}

	return array(
		'url'     => $avatar_url,
		'pending' => $pending,
	);
}

function yneko_reimu_comment_avatar_frame_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id || ! function_exists( 'yneko_reimu_settings_user_badges' ) ) {
		return '';
	}

	if ( '0' === (string) get_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', true ) ) {
		return '';
	}

	$config = yneko_reimu_settings_user_badges();
	$frames = isset( $config['avatar_frames'] ) && is_array( $config['avatar_frames'] ) ? $config['avatar_frames'] : array();
	if ( '1' !== (string) ( $frames['enabled'] ?? '0' ) ) {
		return '';
	}
	$frame_urls = isset( $frames['frames'] ) && is_array( $frames['frames'] ) ? $frames['frames'] : array();
	foreach ( yneko_reimu_comment_user_special_badge_types( $user_id ) as $type ) {
		$url = isset( $frame_urls[ $type ] ) ? esc_url_raw( $frame_urls[ $type ] ) : '';
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

function yneko_reimu_comment_avatar_with_frame( $avatar_html, $user_id, $class = '' ) {
	$frame_url = yneko_reimu_comment_avatar_frame_url( $user_id );
	if ( ! $frame_url ) {
		return $avatar_html;
	}

	$class = trim( 'reimu-avatar-frame ' . $class );
	return '<span class="' . esc_attr( $class ) . '" style="--reimu-avatar-frame:url(' . esc_url( $frame_url ) . ');">' . $avatar_html . '</span>';
}

function yneko_reimu_comment_avatar_for_user_html( $user_id, $size = 56 ) {
	$user_id = absint( $user_id );
	$user = $user_id ? get_userdata( $user_id ) : null;
	if ( ! $user ) {
		return '';
	}

	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	$avatar_url   = yneko_reimu_user_avatar_url( $user_id );
	$avatar       = $avatar_url ? '<img alt="' . esc_attr( $display_name ) . '" src="' . esc_url( $avatar_url ) . '" class="avatar avatar-' . absint( $size ) . ' photo yneko-user-avatar" height="' . absint( $size ) . '" width="' . absint( $size ) . '" loading="lazy" decoding="async">' : get_avatar( $user_id, $size, '', $display_name );
	return yneko_reimu_comment_avatar_with_frame( $avatar, $user_id, 'reimu-avatar-frame--current' );
}

function yneko_reimu_avatar_upload_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['avatar_enabled'] ?? '0' );
}

function yneko_reimu_avatar_review_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['avatar_review'] ?? '0' );
}

function yneko_reimu_avatar_upload_limit() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return max( 1, absint( $settings['avatar_max_mb'] ?? 1 ) ) * MB_IN_BYTES;
}

function yneko_reimu_avatar_upload_dir( $dirs ) {
	$pending = ! empty( $GLOBALS['yneko_reimu_avatar_upload_pending'] );
	$subdir  = ( $pending ? '/yneko-reimu-avatars-pending' : '/yneko-reimu-avatars' ) . gmdate( '/Y/m' );
	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;
	return $dirs;
}

function yneko_reimu_comment_current_user_identity( $redirect_post_id = 0 ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$redirect = $redirect_post_id ? get_permalink( $redirect_post_id ) : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	return yneko_reimu_comment_current_user_identity_html( $redirect );
}

function yneko_reimu_comment_current_user_identity_html( $redirect = '' ) {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$user         = wp_get_current_user();
	$user_id      = absint( $user->ID );
	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	$profile_url  = yneko_reimu_comment_user_profile_url( $user_id );
	$redirect     = $redirect ? wp_validate_redirect( $redirect, home_url( '/' ) ) : home_url( '/' );
	$logout_url   = wp_logout_url( $redirect );
	$avatar       = yneko_reimu_comment_avatar_for_user_html( $user_id, 56 );
	$name_html    = esc_html( $display_name );
	$status_html  = yneko_reimu_user_review_primary_status_html( $user_id );

	if ( $profile_url ) {
		$name_html = '<a class="reimu-comment-current-user__name" href="' . esc_url( $profile_url ) . '" target="_blank" rel="noopener noreferrer nofollow">' . esc_html( $display_name ) . '</a>';
	} else {
		$name_html = '<span class="reimu-comment-current-user__name">' . esc_html( $display_name ) . '</span>';
	}

	return '<div class="reimu-comment-current-user">' .
		'<div class="reimu-comment-current-user__avatar-wrap">' .
			'<button type="button" class="reimu-comment-current-user__avatar" data-reimu-profile-open aria-label="' . esc_attr__( '编辑个人资料', 'yneko-reimu' ) . '">' . $avatar . '</button>' .
			'<a class="reimu-comment-current-user__logout" href="' . esc_url( $logout_url ) . '" data-reimu-ajax-logout data-no-pjax aria-label="' . esc_attr__( '退出登录', 'yneko-reimu' ) . '"></a>' .
		'</div>' .
		$name_html .
		$status_html .
		'<a class="reimu-comment-current-user__logout-text" href="' . esc_url( $logout_url ) . '" data-reimu-ajax-logout data-no-pjax aria-label="' . esc_attr__( '退出登录', 'yneko-reimu' ) . '">' . esc_html__( '退出', 'yneko-reimu' ) . '</a>' .
	'</div>';
}

function yneko_reimu_user_profile_payload( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$user    = $user_id ? get_userdata( $user_id ) : null;
	if ( ! $user ) {
		return array();
	}

	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	$public_profile_url = $user->user_url ? $user->user_url : '';
	$profile_touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_profile_url_touched', true );
	$profile_url = $public_profile_url ? $public_profile_url : ( $profile_touched ? '' : yneko_reimu_comment_user_github_url( $user_id ) );
	return array(
		'userId'      => $user_id,
		'displayName' => $user->display_name ? $user->display_name : $user->user_login,
		'email'       => $user->user_email,
		'avatarUrl'   => yneko_reimu_user_profile_avatar_url( $user_id ),
		'pendingAvatarUrl' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true ),
		'avatarStatus' => (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'avatarPending' => 'pending' === (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true ),
		'reviewStatuses' => yneko_reimu_user_review_status_payload( $user_id ),
		'profileUrl'  => $profile_url,
		'publicProfileUrl' => $public_profile_url,
		'twoFactor'   => yneko_reimu_user_2fa_enabled( $user_id ),
		'avatarUploadEnabled' => '1' === (string) ( $settings['avatar_enabled'] ?? '0' ),
		'avatarReviewEnabled' => '1' === (string) ( $settings['avatar_review'] ?? '0' ),
		'avatarMaxMb' => max( 1, absint( $settings['avatar_max_mb'] ?? 1 ) ),
		'avatarFrameEnabled' => '0' !== (string) get_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', true ),
		'avatarHtml' => yneko_reimu_comment_avatar_for_user_html( $user_id, 56 ),
		'commentBadgesEnabled' => yneko_reimu_comment_badges_enabled(),
		'commentTags' => yneko_reimu_comment_user_tags_payload( $user_id ),
		'pendingCommentTags' => yneko_reimu_comment_user_pending_tags( $user_id ),
		'commentBadgesHtml' => yneko_reimu_comment_user_badges_html( $user_id ),
		'commentTagReviewEnabled' => yneko_reimu_comment_tag_review_enabled(),
	);
}

function yneko_reimu_ajax_login_state() {
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );

	if ( ! is_user_logged_in() ) {
		wp_send_json_success(
			array(
				'loggedIn'       => false,
				'loginUrl'       => wp_login_url( $redirect ),
				'loginHtml'      => yneko_reimu_comment_login_link_html( $redirect ),
				'guestFieldsHtml'=> yneko_reimu_comment_guest_fields_html(),
				'loginModal'     => yneko_reimu_login_modal_html(),
				'commentUploads' => array(
					'enabled'      => yneko_reimu_comment_upload_enabled(),
					'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
					'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
					'isLoggedIn'   => false,
					'nonce'        => '',
				),
			)
		);
	}

	wp_send_json_success(
		array(
			'loggedIn'          => true,
			'identity'          => yneko_reimu_comment_current_user_identity_html( $redirect ),
			'profileModal'      => yneko_reimu_profile_modal_html(),
			'loginModal'        => yneko_reimu_login_modal_html(),
			'commentNonce'      => wp_create_nonce( 'yneko_reimu_submit_comment' ),
			'commentUploadNonce'=> wp_create_nonce( 'yneko_reimu_comment_upload' ),
			'commentUploads'    => array(
				'enabled'      => yneko_reimu_comment_upload_enabled(),
				'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
				'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
				'isLoggedIn'   => true,
				'nonce'        => wp_create_nonce( 'yneko_reimu_comment_upload' ),
			),
			'profileNonce'      => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'       => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			'profile'           => yneko_reimu_user_profile_payload(),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_login_state', 'yneko_reimu_ajax_login_state' );
add_action( 'wp_ajax_nopriv_yneko_reimu_login_state', 'yneko_reimu_ajax_login_state' );

function yneko_reimu_ajax_logout() {
	check_ajax_referer( 'yneko_reimu_ajax_logout', 'nonce' );
	wp_logout();

	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );
	wp_send_json_success(
		array(
			'message'        => esc_html__( '已退出登录。', 'yneko-reimu' ),
			'loginUrl'       => '#reimu-login-modal',
			'loginHtml'      => yneko_reimu_comment_login_link_html( $redirect ),
			'guestFieldsHtml'=> yneko_reimu_comment_guest_fields_html(),
			'loginModal'     => yneko_reimu_login_modal_html(),
			'commentUploads' => array(
				'enabled'      => yneko_reimu_comment_upload_enabled(),
				'imageEnabled' => yneko_reimu_comment_upload_type_enabled( 'image' ),
				'gifEnabled'   => yneko_reimu_comment_upload_type_enabled( 'gif' ),
				'isLoggedIn'   => false,
				'nonce'        => '',
			),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_logout', 'yneko_reimu_ajax_logout' );

function yneko_reimu_comment_author_link_html( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	$author = get_comment_author( $comment );
	$url    = '';

	if ( ! empty( $comment->user_id ) ) {
		$url = yneko_reimu_comment_user_profile_url( $comment->user_id );
	}

	if ( ! $url ) {
		$url = get_comment_author_url( $comment );
	}

	if ( $url ) {
		return '<a class="reimu-comment__author-link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer nofollow">' . esc_html( $author ) . '</a>';
	}

	return '<span class="reimu-comment__author-name">' . esc_html( $author ) . '</span>';
}

function yneko_reimu_login_modal() {
	if ( is_user_logged_in() ) {
		yneko_reimu_profile_modal();
		return;
	}
	echo yneko_reimu_login_modal_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function yneko_reimu_login_modal_html() {
	if ( is_user_logged_in() ) {
		return '';
	}
	$allow_registration = (bool) get_option( 'users_can_register' );
	ob_start();
	?>
	<div class="reimu-login-modal" id="reimu-login-modal" aria-hidden="true">
		<div class="reimu-login-modal__mask" data-login-close></div>
		<div class="reimu-login-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-login-title">
			<button type="button" class="reimu-login-modal__close popup-btn-close" data-login-close aria-label="<?php esc_attr_e( '关闭登录窗口', 'yneko-reimu' ); ?>"></button>
			<h2 id="reimu-login-title"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></h2>
			<p class="reimu-login-modal__desc" hidden></p>
			<form class="reimu-login-form reimu-login-panel is-active" data-reimu-login-form data-login-panel="login">
				<p>
					<label for="reimu-login-user"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
					<input id="reimu-login-user" name="log" type="email" autocomplete="email" required>
				</p>
				<p>
					<label for="reimu-login-password"><?php esc_html_e( '密码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-password-row"><input id="reimu-login-password" name="pwd" type="password" autocomplete="current-password" required><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
				</p>
				<p class="reimu-login-2fa" data-login-2fa hidden>
					<label for="reimu-login-2fa"><?php esc_html_e( '两步验证码', 'yneko-reimu' ); ?></label>
					<input id="reimu-login-2fa" name="two_factor_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
				</p>
				<label class="reimu-login-remember">
					<input name="rememberme" type="checkbox" value="forever">
					<span><?php esc_html_e( '记住我', 'yneko-reimu' ); ?></span>
				</label>
				<div class="reimu-login-message" data-login-message role="status" aria-live="polite"></div>
				<div class="reimu-login-actions">
					<button class="reimu-login-help-link" type="button" data-login-panel-trigger="lost"><?php esc_html_e( '忘记密码？', 'yneko-reimu' ); ?></button>
					<?php if ( $allow_registration ) : ?>
						<button class="reimu-login-register-button" type="button" data-login-panel-trigger="register"><?php esc_html_e( '注册', 'yneko-reimu' ); ?></button>
					<?php endif; ?>
					<button type="submit" class="reimu-login-submit"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></button>
				</div>
			</form>
			<?php if ( $allow_registration ) : ?>
				<form class="reimu-login-form reimu-login-panel" data-reimu-register-form data-login-panel="register" data-loading-text="<?php esc_attr_e( '注册中...', 'yneko-reimu' ); ?>" hidden>
					<p>
						<label for="reimu-register-display-name"><?php esc_html_e( '昵称', 'yneko-reimu' ); ?></label>
						<input id="reimu-register-display-name" name="display_name" type="text" autocomplete="nickname" required>
					</p>
					<p>
						<label for="reimu-register-email"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
						<input id="reimu-register-email" name="user_email" type="email" autocomplete="email" required>
					</p>
					<p>
						<label for="reimu-register-password"><?php esc_html_e( '密码', 'yneko-reimu' ); ?></label>
						<span class="reimu-login-password-row"><input id="reimu-register-password" name="user_password" type="password" autocomplete="new-password" minlength="8" required><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
					</p>
					<p>
						<label for="reimu-register-code"><?php esc_html_e( '邮箱验证码', 'yneko-reimu' ); ?></label>
						<span class="reimu-login-code-row">
							<input id="reimu-register-code" name="verify_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
							<button class="reimu-login-code-button" type="button" data-register-code-send><?php esc_html_e( '发送验证码', 'yneko-reimu' ); ?></button>
						</span>
					</p>
					<p class="reimu-login-note"><?php esc_html_e( '验证码会发送到您的邮箱，5 分钟内有效。', 'yneko-reimu' ); ?></p>
					<div class="reimu-login-message" data-register-message role="status" aria-live="polite"></div>
					<div class="reimu-login-actions">
						<button class="reimu-login-help-link" type="button" data-login-panel-trigger="login"><?php esc_html_e( '返回登录', 'yneko-reimu' ); ?></button>
						<button type="submit" class="reimu-login-submit"><?php esc_html_e( '注册', 'yneko-reimu' ); ?></button>
					</div>
				</form>
			<?php endif; ?>
			<form class="reimu-login-form reimu-login-panel" data-reimu-lost-form data-login-panel="lost" data-loading-text="<?php esc_attr_e( '重置中...', 'yneko-reimu' ); ?>" hidden>
				<p>
					<label for="reimu-lost-user"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
					<input id="reimu-lost-user" name="user_login" type="email" autocomplete="email" required>
				</p>
				<p>
					<label for="reimu-lost-password"><?php esc_html_e( '新密码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-password-row"><input id="reimu-lost-password" name="user_password" type="password" autocomplete="new-password" minlength="8" required><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
				</p>
				<p>
					<label for="reimu-lost-code"><?php esc_html_e( '邮箱验证码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-code-row">
						<input id="reimu-lost-code" name="verify_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
						<button class="reimu-login-code-button" type="button" data-lost-code-send><?php esc_html_e( '发送验证码', 'yneko-reimu' ); ?></button>
					</span>
				</p>
				<p class="reimu-login-note"><?php esc_html_e( '验证码会发送到账号邮箱，5 分钟内有效。', 'yneko-reimu' ); ?></p>
				<div class="reimu-login-message" data-lost-message role="status" aria-live="polite"></div>
				<div class="reimu-login-actions">
					<button class="reimu-login-help-link" type="button" data-login-panel-trigger="login"><?php esc_html_e( '返回登录', 'yneko-reimu' ); ?></button>
					<button type="submit" class="reimu-login-submit"><?php esc_html_e( '重置密码', 'yneko-reimu' ); ?></button>
				</div>
			</form>
			<?php do_action( 'yneko_reimu_login_modal_social' ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function yneko_reimu_profile_modal() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	echo yneko_reimu_profile_modal_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function yneko_reimu_profile_modal_html() {
	if ( ! is_user_logged_in() ) {
		return '';
	}
	$profile = yneko_reimu_user_profile_payload();
	$comment_tags = isset( $profile['commentTags'] ) && is_array( $profile['commentTags'] ) ? $profile['commentTags'] : yneko_reimu_comment_user_tags_payload( get_current_user_id() );
	$special_tags = array_values(
		array_filter(
			$comment_tags,
			static function ( $tag ) {
				return is_array( $tag ) && 'special' === ( $tag['type'] ?? '' ) && ! empty( $tag['key'] );
			}
		)
	);
	$enabled_special_count = count(
		array_filter(
			$special_tags,
			static function ( $tag ) {
				return is_array( $tag ) && '0' !== (string) ( $tag['enabled'] ?? '1' );
			}
		)
	);
	$custom_tag_slots = max( 0, yneko_reimu_comment_badge_display_limit() - $enabled_special_count );
	$custom_tags = array_values(
		array_filter(
			$comment_tags,
			static function ( $tag ) {
				return is_array( $tag ) && 'custom' === ( $tag['type'] ?? '' ) && '' !== ( $tag['label'] ?? '' );
			}
		)
	);
	ob_start();
	?>
	<div class="reimu-profile-modal" id="reimu-profile-modal" aria-hidden="true" data-avatar-max-mb="<?php echo esc_attr( $profile['avatarMaxMb'] ); ?>">
		<div class="reimu-profile-modal__mask" data-profile-close></div>
		<div class="reimu-profile-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-profile-title">
			<button type="button" class="reimu-login-modal__close popup-btn-close" data-profile-close aria-label="<?php esc_attr_e( '关闭个人资料窗口', 'yneko-reimu' ); ?>"></button>
			<h2 id="reimu-profile-title"><?php esc_html_e( '个人资料', 'yneko-reimu' ); ?></h2>
			<form class="reimu-profile-form" data-reimu-profile-form>
				<div class="reimu-profile-avatar-preview"><img data-profile-avatar-preview src="<?php echo esc_url( $profile['avatarUrl'] ? $profile['avatarUrl'] : get_avatar_url( get_current_user_id(), array( 'size' => 96 ) ) ); ?>" alt=""></div>
				<p class="reimu-profile-avatar-field">
					<label for="reimu-profile-avatar-url" class="reimu-profile-avatar-label">
						<span><?php esc_html_e( '头像链接', 'yneko-reimu' ); ?></span>
						<small data-profile-avatar-hint role="status" aria-live="polite"></small>
					</label>
					<span class="reimu-profile-avatar-row">
						<input id="reimu-profile-avatar-url" name="avatar_url" type="url" value="<?php echo esc_attr( $profile['avatarUrl'] ); ?>">
						<input name="avatar_changed" type="hidden" value="0" data-profile-avatar-changed>
						<?php if ( ! empty( $profile['avatarUploadEnabled'] ) ) : ?>
							<button class="reimu-profile-avatar-upload" type="button" data-profile-avatar-upload><?php esc_html_e( '上传', 'yneko-reimu' ); ?></button>
							<input id="reimu-profile-avatar-file" name="avatar_file" type="file" accept="image/jpeg,image/png,image/webp" data-profile-avatar-file hidden>
						<?php endif; ?>
					</span>
				</p>
				<p>
					<label for="reimu-profile-display-name"><?php esc_html_e( '昵称', 'yneko-reimu' ); ?></label>
					<input id="reimu-profile-display-name" name="display_name" type="text" value="<?php echo esc_attr( $profile['displayName'] ); ?>" required>
				</p>
				<p>
					<label for="reimu-profile-url"><?php esc_html_e( '个人主页', 'yneko-reimu' ); ?></label>
					<input id="reimu-profile-url" name="profile_url" type="text" inputmode="url" value="<?php echo esc_attr( $profile['profileUrl'] ); ?>">
				</p>
				<p class="reimu-profile-avatar-frame-toggle">
					<label class="reimu-login-remember"><input name="avatar_frame_enabled" type="checkbox" value="1" <?php checked( ! empty( $profile['avatarFrameEnabled'] ) ); ?>><span><?php esc_html_e( '显示我的评论头像框', 'yneko-reimu' ); ?></span></label>
				</p>
				<?php if ( ! empty( $profile['commentBadgesEnabled'] ) ) : ?>
					<div class="reimu-profile-tags">
						<div class="reimu-profile-tags__header">
							<span><?php esc_html_e( '评论标签', 'yneko-reimu' ); ?></span>
						<small><?php esc_html_e( '最多添加 5 个自定义标签；特殊标签和已勾选的自定义标签合计最多 2 个。自定义标签最多 8 个字符，保留标签不可自行设置。', 'yneko-reimu' ); ?></small>
					</div>
					<div class="reimu-profile-tags__message" data-profile-tags-message role="status" aria-live="polite" hidden></div>
					<?php if ( $special_tags ) : ?>
						<div class="reimu-profile-special-tag-list" data-profile-special-tag-list>
							<?php foreach ( $special_tags as $special_tag ) : ?>
								<label class="reimu-profile-special-tag-toggle">
									<input type="checkbox" name="comment_special_enabled[<?php echo esc_attr( $special_tag['key'] ); ?>]" value="1" <?php checked( '1', $special_tag['enabled'] ?? '1' ); ?>>
									<span><?php echo esc_html( $special_tag['label'] ); ?></span>
									<small><?php esc_html_e( '特殊标签', 'yneko-reimu' ); ?></small>
								</label>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="reimu-profile-tag-list" data-profile-tag-list data-max-tags="<?php echo esc_attr( $custom_tag_slots ); ?>" data-storage-limit="<?php echo esc_attr( yneko_reimu_comment_custom_tag_storage_limit() ); ?>" data-existing-tags="<?php echo esc_attr( wp_json_encode( $custom_tags ) ); ?>"></div>
					<button type="button" class="reimu-profile-add-tag" data-profile-add-tag><?php esc_html_e( '+ 新增自定义标签', 'yneko-reimu' ); ?></button>
				</div>
				<?php endif; ?>
				<p>
					<label for="reimu-profile-email"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
					<output id="reimu-profile-email" class="reimu-profile-frozen-field" aria-readonly="true" aria-disabled="true" data-profile-current-email-display><?php echo esc_html( $profile['email'] ); ?></output>
					<input name="current_email" type="hidden" value="<?php echo esc_attr( $profile['email'] ); ?>" data-profile-current-email>
				</p>
				<p>
					<label for="reimu-profile-new-email"><?php esc_html_e( '新邮箱', 'yneko-reimu' ); ?></label>
					<input id="reimu-profile-new-email" name="user_email" type="email" autocomplete="email" data-profile-new-email>
				</p>
				<p>
					<label for="reimu-profile-email-code"><?php esc_html_e( '新邮箱验证码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-code-row">
						<input id="reimu-profile-email-code" name="email_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
						<button class="reimu-login-code-button" type="button" data-profile-email-code-send><?php esc_html_e( '发送验证码', 'yneko-reimu' ); ?></button>
					</span>
				</p>
				<?php if ( ! empty( $profile['avatarPending'] ) ) : ?>
					<p class="reimu-profile-avatar-status" data-profile-avatar-status><?php esc_html_e( '头像审核中', 'yneko-reimu' ); ?></p>
				<?php else : ?>
					<p class="reimu-profile-avatar-status" data-profile-avatar-status hidden></p>
				<?php endif; ?>
				<p>
					<label for="reimu-profile-password"><?php esc_html_e( '新密码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-password-row"><input id="reimu-profile-password" name="new_password" type="password" autocomplete="new-password" minlength="8"><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
				</p>
				<p>
					<label for="reimu-profile-password-confirm"><?php esc_html_e( '确认新密码', 'yneko-reimu' ); ?></label>
					<span class="reimu-login-password-row"><input id="reimu-profile-password-confirm" name="new_password_confirm" type="password" autocomplete="new-password" minlength="8"><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
				</p>
				<div class="reimu-profile-2fa">
					<label class="reimu-login-remember"><input name="totp_enabled" type="checkbox" value="1" <?php checked( ! empty( $profile['twoFactor'] ) ); ?> data-profile-2fa-toggle><span><?php esc_html_e( '开启认证器两步验证', 'yneko-reimu' ); ?></span></label>
					<div class="reimu-profile-2fa-setup" data-profile-2fa-setup hidden>
						<button class="reimu-login-code-button" type="button" data-profile-2fa-generate><?php esc_html_e( '生成密钥', 'yneko-reimu' ); ?></button>
						<div class="reimu-profile-2fa-secret" data-profile-2fa-secret></div>
						<img data-profile-2fa-qr alt="" hidden>
						<p>
							<label for="reimu-profile-2fa-code"><?php esc_html_e( '认证器验证码', 'yneko-reimu' ); ?></label>
							<input id="reimu-profile-2fa-code" name="totp_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
						</p>
					</div>
				</div>
				<div class="reimu-login-message" data-profile-message role="status" aria-live="polite"></div>
				<div class="reimu-login-actions">
					<button class="reimu-login-help-link" type="button" data-profile-close><?php esc_html_e( '取消', 'yneko-reimu' ); ?></button>
					<button type="submit" class="reimu-login-submit"><?php esc_html_e( '保存', 'yneko-reimu' ); ?></button>
				</div>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function yneko_reimu_ajax_login() {
	if ( ! check_ajax_referer( 'yneko_reimu_ajax_login', 'nonce', false ) ) {
		wp_send_json_error(
			array(
				'message'    => esc_html__( '登录信息已过期，请重试。', 'yneko-reimu' ),
				'loginNonce' => wp_create_nonce( 'yneko_reimu_ajax_login' ),
			),
			403
		);
	}

	$email    = isset( $_POST['log'] ) ? strtolower( sanitize_email( wp_unslash( $_POST['log'] ) ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be checked raw.
	$remember = ! empty( $_POST['rememberme'] );
	$two_factor_code = isset( $_POST['two_factor_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['two_factor_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' === $email || ! is_email( $email ) || '' === $password ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请输入邮箱和密码。', 'yneko-reimu' ),
			),
			400
		);
	}

	if ( yneko_reimu_auth_rate_limited( 'login', $email ) ) {
		wp_send_json_error(
			array(
				'message' => yneko_reimu_auth_generic_error_message(),
			),
			429
		);
	}

	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		yneko_reimu_auth_record_failure( 'login', $email );
		wp_send_json_error( array( 'message' => yneko_reimu_auth_generic_error_message() ), 403 );
	}

	if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
		yneko_reimu_auth_record_failure( 'login', $email );
		wp_send_json_error( array( 'message' => yneko_reimu_auth_generic_error_message() ), 403 );
	}

	if ( yneko_reimu_user_2fa_enabled( $user->ID ) ) {
		if ( ! preg_match( '/^\d{6}$/', $two_factor_code ) ) {
			wp_send_json_error(
				array(
					'message'     => esc_html__( '请输入两步验证码。', 'yneko-reimu' ),
					'requires2fa' => true,
				),
				401
			);
		}
		if ( ! yneko_reimu_totp_verify( yneko_reimu_user_2fa_secret( $user->ID ), $two_factor_code ) ) {
			yneko_reimu_auth_record_failure( 'login', $email );
			wp_send_json_error(
				array(
					'message'     => esc_html__( '两步验证码不正确。', 'yneko-reimu' ),
					'requires2fa' => true,
				),
				403
			);
		}
	}

	yneko_reimu_auth_clear_failures( 'login', $email );
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, $remember, is_ssl() );
	do_action( 'wp_login', $user->user_login, $user );

	wp_send_json_success(
		array(
			'message' => esc_html__( '登录成功。', 'yneko-reimu' ),
			'loginNonce' => wp_create_nonce( 'yneko_reimu_ajax_login' ),
		)
	);
}
add_action( 'wp_ajax_nopriv_yneko_reimu_login', 'yneko_reimu_ajax_login' );

function yneko_reimu_user_2fa_secret( $user_id ) {
	return (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_secret', true );
}

function yneko_reimu_user_2fa_enabled( $user_id ) {
	return '1' === (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_enabled', true ) && '' !== yneko_reimu_user_2fa_secret( $user_id );
}

function yneko_reimu_totp_base32_chars() {
	return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
}

function yneko_reimu_totp_generate_secret( $length = 20 ) {
	$chars  = yneko_reimu_totp_base32_chars();
	$secret = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$secret .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
	}
	return $secret;
}

function yneko_reimu_totp_base32_decode( $secret ) {
	$secret = strtoupper( preg_replace( '/[^A-Z2-7]/', '', (string) $secret ) );
	$chars  = yneko_reimu_totp_base32_chars();
	$bits   = '';
	$bytes  = '';

	for ( $i = 0; $i < strlen( $secret ); $i++ ) {
		$index = strpos( $chars, $secret[ $i ] );
		if ( false === $index ) {
			continue;
		}
		$bits .= str_pad( decbin( $index ), 5, '0', STR_PAD_LEFT );
	}

	for ( $i = 0; $i + 8 <= strlen( $bits ); $i += 8 ) {
		$bytes .= chr( bindec( substr( $bits, $i, 8 ) ) );
	}

	return $bytes;
}

function yneko_reimu_totp_code( $secret, $time_slice = null ) {
	$time_slice = null === $time_slice ? floor( time() / 30 ) : (int) $time_slice;
	$key        = yneko_reimu_totp_base32_decode( $secret );
	if ( '' === $key ) {
		return '';
	}

	$counter = pack( 'N*', 0 ) . pack( 'N*', $time_slice );
	$hash    = hash_hmac( 'sha1', $counter, $key, true );
	$offset  = ord( substr( $hash, -1 ) ) & 0x0f;
	$value   = unpack( 'N', substr( $hash, $offset, 4 ) )[1] & 0x7fffffff;
	return str_pad( (string) ( $value % 1000000 ), 6, '0', STR_PAD_LEFT );
}

function yneko_reimu_totp_verify( $secret, $code ) {
	$code = preg_replace( '/\D+/', '', (string) $code );
	if ( ! preg_match( '/^\d{6}$/', $code ) || '' === $secret ) {
		return false;
	}

	$slice = floor( time() / 30 );
	for ( $i = -1; $i <= 1; $i++ ) {
		if ( hash_equals( yneko_reimu_totp_code( $secret, $slice + $i ), $code ) ) {
			return true;
		}
	}
	return false;
}

function yneko_reimu_totp_uri( $user_id, $secret ) {
	$user  = get_userdata( absint( $user_id ) );
	$email = $user ? $user->user_email : '';
	$label = rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . ':' . $email );
	return 'otpauth://totp/' . $label . '?secret=' . rawurlencode( $secret ) . '&issuer=' . rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) . '&algorithm=SHA1&digits=6&period=30';
}

function yneko_reimu_ajax_profile_get() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '请先登录。', 'yneko-reimu' ),
			),
			401
		);
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_user_profile_payload(),
			array(
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_get', 'yneko_reimu_ajax_profile_get' );

function yneko_reimu_ajax_profile_status_ack() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$types = isset( $_POST['types'] ) && is_array( $_POST['types'] ) ? array_map( 'sanitize_key', wp_unslash( $_POST['types'] ) ) : array();
	foreach ( $types as $type ) {
		$key = yneko_reimu_user_review_status_meta_key( $type );
		if ( ! $key ) {
			continue;
		}
		$status = (string) get_user_meta( get_current_user_id(), $key, true );
		if ( in_array( $status, array( 'updated', 'rejected' ), true ) ) {
			yneko_reimu_clear_user_review_status( get_current_user_id(), $type );
		}
	}

	wp_send_json_success(
		array(
			'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_status_ack', 'yneko_reimu_ajax_profile_status_ack' );

function yneko_reimu_ajax_profile_email_code() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user      = wp_get_current_user();
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$new_email = $new_email_input ? $new_email_input : $user->user_email;
	if ( '' === $new_email || ! is_email( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) ), 400 );
	}
	if ( strtolower( $new_email ) === strtolower( $user->user_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '新邮箱地址不要与原邮箱地址重复。', 'yneko-reimu' ) ), 400 );
	}
	if ( email_exists( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) ), 400 );
	}

	$cooldown_key = yneko_reimu_auth_code_cooldown_transient_key( 'profile_email', (string) get_current_user_id(), $new_email );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码已发送，请稍后再试。', 'yneko-reimu' ) ), 429 );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 邮箱修改验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的邮箱修改验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。', 'yneko-reimu' ),
		$code,
		5
	);
	if ( ! wp_mail( $new_email, wp_specialchars_decode( $title ), $message ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'profile_email', (string) get_current_user_id(), $new_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
		),
		5 * MINUTE_IN_SECONDS
	);
	set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );
	wp_send_json_success(
		array(
			'message'      => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ),
			'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_email_code', 'yneko_reimu_ajax_profile_email_code' );

function yneko_reimu_ajax_profile_totp_generate() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$secret = yneko_reimu_totp_generate_secret();
	update_user_meta( get_current_user_id(), '_yneko_reimu_totp_pending_secret', $secret );
	wp_send_json_success(
		array(
			'secret' => $secret,
			'uri'    => yneko_reimu_totp_uri( get_current_user_id(), $secret ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_totp_generate', 'yneko_reimu_ajax_profile_totp_generate' );

function yneko_reimu_ajax_profile_avatar_upload() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	yneko_reimu_ajax_set_language_from_redirect( $redirect );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id = get_current_user_id();
	$result  = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'message'      => $result->get_error_message(),
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			),
			400
		);
	}

	$pending = ! empty( $result['pending'] );
	wp_send_json_success(
		array_merge(
			array(
				'message'      => $pending ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '头像已更新', 'yneko-reimu' ),
				'avatarUploadUrl' => $result['url'],
				'avatarUploadPending' => $pending,
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce'  => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
				'identity'     => yneko_reimu_comment_current_user_identity_html( $redirect ),
			),
			yneko_reimu_user_profile_payload( $user_id )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_avatar_upload', 'yneko_reimu_ajax_profile_avatar_upload' );

function yneko_reimu_ajax_profile_save() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$user_id      = get_current_user_id();
	$user         = wp_get_current_user();
	$display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$profile_url  = isset( $_POST['profile_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['profile_url'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$avatar_url   = isset( $_POST['avatar_url'] ) ? yneko_reimu_normalize_user_url( wp_unslash( $_POST['avatar_url'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$avatar_changed = ! empty( $_POST['avatar_changed'] );
	$new_email_input = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$new_email    = $new_email_input ? $new_email_input : $user->user_email;
	$email_code   = isset( $_POST['email_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['email_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$new_password = isset( $_POST['new_password'] ) ? (string) wp_unslash( $_POST['new_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be set raw.
	$new_password_confirm = isset( $_POST['new_password_confirm'] ) ? (string) wp_unslash( $_POST['new_password_confirm'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be compared raw.
	$avatar_frame_enabled = ! empty( $_POST['avatar_frame_enabled'] );
	$totp_enabled = ! empty( $_POST['totp_enabled'] );
	$totp_code    = isset( $_POST['totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_labels   = isset( $_POST['comment_tag_label'] ) && is_array( $_POST['comment_tag_label'] ) ? wp_unslash( $_POST['comment_tag_label'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_colors   = isset( $_POST['comment_tag_color'] ) && is_array( $_POST['comment_tag_color'] ) ? wp_unslash( $_POST['comment_tag_color'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_ids      = isset( $_POST['comment_tag_id'] ) && is_array( $_POST['comment_tag_id'] ) ? wp_unslash( $_POST['comment_tag_id'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$tag_enabled_input = isset( $_POST['comment_tag_enabled'] ) && is_array( $_POST['comment_tag_enabled'] ) ? wp_unslash( $_POST['comment_tag_enabled'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$special_enabled_input = isset( $_POST['comment_special_enabled'] ) && is_array( $_POST['comment_special_enabled'] ) ? wp_unslash( $_POST['comment_special_enabled'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' === $display_name || mb_strlen( $display_name ) > 50 ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 1 到 50 个字符的昵称。', 'yneko-reimu' ) ), 400 );
	}
	if ( '' === $new_email || ! is_email( $new_email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入有效的邮箱地址。', 'yneko-reimu' ) ), 400 );
	}

	$comment_tags = array();
	$hidden_special_badges = array();
	$avatar_pending = false;
	$tags_pending = false;
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	foreach ( $special_badges as $index => $special_badge ) {
		$type = sanitize_key( $special_badge['type'] ?? '' );
		if ( ! $type ) {
			continue;
		}
		$enabled = ! empty( $special_enabled_input[ $type ] );
		if ( ! $enabled ) {
			$hidden_special_badges[] = $type;
		}
	}
	$special_counts = max( 0, count( $special_badges ) - count( $hidden_special_badges ) );
	$display_limit   = yneko_reimu_comment_badge_display_limit();
	if ( $special_counts > $display_limit ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
				'field'   => 'comment_tag_label',
			),
			400
		);
	}
	$custom_capacity = yneko_reimu_comment_badges_enabled() ? max( 0, $display_limit - $special_counts ) : 0;
	$enabled_custom_count = 0;
	if ( yneko_reimu_comment_badges_enabled() ) {
		foreach ( $tag_labels as $index => $raw_label ) {
			if ( count( $comment_tags ) >= yneko_reimu_comment_custom_tag_storage_limit() ) {
				break;
			}
			$raw_label = isset( $tag_labels[ $index ] ) ? (string) $tag_labels[ $index ] : '';
			$label     = yneko_reimu_sanitize_comment_tag_label( $raw_label );
			if ( '' === $label ) {
				continue;
			}
			if ( mb_strlen( trim( wp_strip_all_tags( $raw_label ) ) ) > 8 ) {
				wp_send_json_error( array( 'message' => esc_html__( '评论标签最多 8 个字符。', 'yneko-reimu' ) ), 400 );
			}
			if ( yneko_reimu_comment_tag_label_is_reserved( $label ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( '该评论标签为系统保留或屏蔽标签，请换一个。', 'yneko-reimu' ),
						'field'   => 'comment_tag_label',
						'value'   => $label,
						'index'   => absint( $index ),
					),
					400
				);
			}
			$color = sanitize_hex_color( $tag_colors[ $index ] ?? '' );
			$enabled = ! empty( $tag_enabled_input[ $index ] ) && $enabled_custom_count < $custom_capacity;
			if ( ! empty( $tag_enabled_input[ $index ] ) && ! $enabled ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
						'field'   => 'comment_tag_label',
						'index'   => absint( $index ),
					),
					400
				);
			}
			if ( $enabled ) {
				$enabled_custom_count++;
			}
			$comment_tags[] = array(
				'id'      => yneko_reimu_comment_tag_id( $tag_ids[ $index ] ?? '' ),
				'label'   => $label,
				'color'   => $color ? $color : '#ff5252',
				'enabled' => $enabled ? '1' : '0',
			);
		}
	}

	$update = array(
		'ID'           => $user_id,
		'display_name' => $display_name,
		'nickname'     => $display_name,
		'user_url'     => $profile_url,
	);

	if ( strtolower( $new_email ) !== strtolower( $user->user_email ) ) {
		if ( email_exists( $new_email ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '该邮箱已被注册。', 'yneko-reimu' ) ), 400 );
		}
		$code_key = yneko_reimu_auth_code_transient_key( 'profile_email', (string) $user_id, $new_email );
		$code_data = get_transient( $code_key );
		if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) || ! wp_check_password( $email_code, $code_data['code_hash'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '邮箱验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
		}
		$update['user_email'] = $new_email;
		delete_transient( $code_key );
		delete_transient( yneko_reimu_auth_code_cooldown_transient_key( 'profile_email', (string) $user_id, $new_email ) );
	}

	if ( '' !== $new_password || '' !== $new_password_confirm ) {
		if ( $new_password !== $new_password_confirm ) {
			wp_send_json_error( array( 'message' => esc_html__( '两次输入的密码不一致。', 'yneko-reimu' ) ), 400 );
		}
		if ( strlen( $new_password ) < 8 ) {
			wp_send_json_error( array( 'message' => esc_html__( '密码至少需要 8 个字符。', 'yneko-reimu' ) ), 400 );
		}
	}

	if ( isset( $_FILES['avatar_file'] ) && ! empty( $_FILES['avatar_file']['name'] ) ) {
		$avatar_changed = true;
		$avatar_result = yneko_reimu_handle_profile_avatar_upload( $user_id, $_FILES['avatar_file'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $avatar_result ) ) {
			wp_send_json_error( array( 'message' => $avatar_result->get_error_message() ), 400 );
		}
		$avatar_url     = $avatar_result['url'];
		$avatar_pending = ! empty( $avatar_result['pending'] );
	}

	if ( $avatar_changed ) {
		if ( $avatar_url && ! $avatar_pending ) {
			update_user_meta( $user_id, '_yneko_reimu_avatar_url', $avatar_url );
			delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
			yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
		} elseif ( ! $avatar_url && ! $avatar_pending ) {
			delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
			delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
			yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
		}
	}

	if ( $totp_enabled ) {
		$current_secret = yneko_reimu_user_2fa_secret( $user_id );
		$pending_secret = (string) get_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', true );
		$secret = $current_secret ? $current_secret : $pending_secret;
		if ( '' === $secret ) {
			wp_send_json_error( array( 'message' => esc_html__( '请先生成认证器密钥。', 'yneko-reimu' ) ), 400 );
		}
		if ( ! yneko_reimu_totp_verify( $secret, $totp_code ) ) {
			wp_send_json_error( array( 'message' => esc_html__( '认证器验证码不正确。', 'yneko-reimu' ) ), 400 );
		}
		update_user_meta( $user_id, '_yneko_reimu_totp_secret', $secret );
		update_user_meta( $user_id, '_yneko_reimu_totp_enabled', '1' );
		delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_totp_enabled' );
		delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	}

	$result = wp_update_user( $update );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
	}
	update_user_meta( $user_id, '_yneko_reimu_profile_url_touched', '1' );
	if ( $avatar_frame_enabled ) {
		delete_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled' );
	} else {
		update_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', '0' );
	}
	if ( $comment_tags ) {
		if ( yneko_reimu_comment_tag_review_enabled() && ! yneko_reimu_comment_user_can_bypass_tag_review( $user_id ) ) {
			$reviewed = yneko_reimu_comment_prepare_reviewed_tags( yneko_reimu_comment_user_custom_tags( $user_id ), $comment_tags );
			if ( $reviewed['active'] ) {
				update_user_meta( $user_id, '_yneko_reimu_comment_tags', $reviewed['active'] );
			} else {
				delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
			}
			if ( $reviewed['pending'] ) {
				update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $reviewed['pending'] );
				yneko_reimu_set_user_review_status( $user_id, 'tags', 'pending' );
				$tags_pending = true;
			} else {
				delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
				yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
			}
		} else {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags', $comment_tags );
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
			yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
		}
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
		yneko_reimu_clear_user_review_status( $user_id, 'tags' );
	}
	if ( $hidden_special_badges ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges', $hidden_special_badges );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_hidden_special_badges' );
	}
	update_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', '1' );
	if ( '' !== $new_password ) {
		wp_set_password( $new_password, $user_id );
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true, is_ssl() );
	}

	wp_send_json_success(
		array_merge(
			array(
				'message' => $avatar_pending ? esc_html__( '个人资料已保存，头像审核中。', 'yneko-reimu' ) : ( yneko_reimu_comment_tag_review_enabled() && ! yneko_reimu_comment_user_can_bypass_tag_review( $user_id ) && $comment_tags ? esc_html__( '个人资料已保存，评论标签审核中。', 'yneko-reimu' ) : esc_html__( '个人资料已保存。', 'yneko-reimu' ) ),
				'profileNonce' => wp_create_nonce( 'yneko_reimu_profile' ),
				'logoutNonce' => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
				'identity' => yneko_reimu_comment_current_user_identity_html( $redirect ),
				'tagsPending' => $tags_pending,
			),
			yneko_reimu_user_profile_payload( $user_id )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_save', 'yneko_reimu_ajax_profile_save' );

function yneko_reimu_auth_code_transient_key( $scope, $identifier, $email ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_code_' . hash( 'sha256', strtolower( $identifier ) . '|' . strtolower( $email ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_code_cooldown_transient_key( $scope, $identifier, $email ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_cooldown_' . hash( 'sha256', strtolower( $identifier ) . '|' . strtolower( $email ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function yneko_reimu_auth_generic_error_message() {
	return esc_html__( '登录失败，请检查账号和密码。', 'yneko-reimu' );
}

function yneko_reimu_auth_rate_key( $scope, $identifier ) {
	return 'yneko_reimu_' . sanitize_key( $scope ) . '_fail_' . hash( 'sha256', strtolower( (string) $identifier ) . '|' . yneko_reimu_auth_client_ip() );
}

function yneko_reimu_auth_rate_limited( $scope, $identifier ) {
	$data = get_transient( yneko_reimu_auth_rate_key( $scope, $identifier ) );
	return is_array( $data ) && absint( $data['count'] ?? 0 ) >= 5;
}

function yneko_reimu_auth_record_failure( $scope, $identifier ) {
	$key  = yneko_reimu_auth_rate_key( $scope, $identifier );
	$data = get_transient( $key );
	$data = is_array( $data ) ? $data : array( 'count' => 0 );
	$data['count'] = absint( $data['count'] ?? 0 ) + 1;
	set_transient( $key, $data, 15 * MINUTE_IN_SECONDS );
}

function yneko_reimu_auth_clear_failures( $scope, $identifier ) {
	delete_transient( yneko_reimu_auth_rate_key( $scope, $identifier ) );
}

function yneko_reimu_generate_unique_login_from_email( $email ) {
	$base = sanitize_user( current( explode( '@', $email ) ), true );
	if ( '' === $base ) {
		$base = 'user';
	}

	$user_login = $base;
	$suffix     = 1;
	while ( username_exists( $user_login ) ) {
		$suffix++;
		$user_login = $base . $suffix;
	}

	return $user_login;
}

function yneko_reimu_find_user_for_password_reset( $identifier ) {
	$identifier = trim( (string) $identifier );
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		return null;
	}

	return get_user_by( 'email', $identifier );
}

function yneko_reimu_validate_registration_fields( $display_name, $user_email, $user_password = '', $check_password = false ) {
	$errors = new WP_Error();
	$name   = trim( wp_strip_all_tags( (string) $display_name ) );

	if ( '' === $name ) {
		$errors->add( 'invalid_display_name', __( '请输入有效的昵称。', 'yneko-reimu' ) );
	} elseif ( mb_strlen( $name ) > 50 ) {
		$errors->add( 'display_name_too_long', __( '昵称不能超过 50 个字符。', 'yneko-reimu' ) );
	}

	if ( '' === $user_email || ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '请输入有效的邮箱地址。', 'yneko-reimu' ) );
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '该邮箱已被注册。', 'yneko-reimu' ) );
	}

	if ( $check_password && strlen( $user_password ) < 8 ) {
		$errors->add( 'weak_password', __( '密码至少需要 8 个字符。', 'yneko-reimu' ) );
	}

	return $errors;
}

function yneko_reimu_ajax_send_register_code() {
	check_ajax_referer( 'yneko_reimu_ajax_register_code', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '当前未开放注册。', 'yneko-reimu' ) ), 403 );
	}

	$display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$user_email   = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$errors       = yneko_reimu_validate_registration_fields( $display_name, $user_email );

	if ( $errors->has_errors() ) {
		wp_send_json_error( array( 'message' => $errors->get_error_message() ), 400 );
	}

	$cooldown_key = yneko_reimu_auth_code_cooldown_transient_key( 'reg', $display_name, $user_email );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码已发送，请稍后再试。', 'yneko-reimu' ) ), 429 );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 注册验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的注册验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请忽略这封邮件。', 'yneko-reimu' ),
		$code,
		5
	);

	if ( ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'reg', $display_name, $user_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
		),
		5 * MINUTE_IN_SECONDS
	);
	set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );

	wp_send_json_success( array( 'message' => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_register_code', 'yneko_reimu_ajax_send_register_code' );

function yneko_reimu_ajax_register() {
	check_ajax_referer( 'yneko_reimu_ajax_register', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '当前未开放注册。', 'yneko-reimu' ) ), 403 );
	}

	$display_name  = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$user_email    = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$user_password = isset( $_POST['user_password'] ) ? (string) wp_unslash( $_POST['user_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be created raw.
	$verify_code   = isset( $_POST['verify_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['verify_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$errors        = yneko_reimu_validate_registration_fields( $display_name, $user_email, $user_password, true );

	if ( $errors->has_errors() ) {
		wp_send_json_error( array( 'message' => $errors->get_error_message() ), 400 );
	}
	if ( ! preg_match( '/^\d{6}$/', $verify_code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 6 位邮箱验证码。', 'yneko-reimu' ) ), 400 );
	}

	$code_key  = yneko_reimu_auth_code_transient_key( 'reg', $display_name, $user_email );
	$code_data = get_transient( $code_key );
	if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码已失效，请重新获取。', 'yneko-reimu' ) ), 400 );
	}

	$attempts = isset( $code_data['attempts'] ) ? absint( $code_data['attempts'] ) : 0;
	if ( $attempts >= 5 ) {
		delete_transient( $code_key );
		wp_send_json_error( array( 'message' => esc_html__( '验证码错误次数过多，请重新获取。', 'yneko-reimu' ) ), 429 );
	}

	if ( ! wp_check_password( $verify_code, $code_data['code_hash'] ) ) {
		$code_data['attempts'] = $attempts + 1;
		set_transient( $code_key, $code_data, 5 * MINUTE_IN_SECONDS );
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确。', 'yneko-reimu' ) ), 400 );
	}

	$user_login = yneko_reimu_generate_unique_login_from_email( $user_email );
	$user_id = wp_create_user( $user_login, $user_password, $user_email );
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ), 400 );
	}
	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $display_name,
			'nickname'     => $display_name,
		)
	);

	delete_transient( $code_key );
	delete_transient( yneko_reimu_auth_code_cooldown_transient_key( 'reg', $display_name, $user_email ) );
	wp_new_user_notification( $user_id, null, 'admin' );
	wp_send_json_success( array( 'message' => esc_html__( '注册成功，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_register', 'yneko_reimu_ajax_register' );

function yneko_reimu_ajax_send_lostpassword_code() {
	check_ajax_referer( 'yneko_reimu_ajax_lostpassword_code', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	$identifier = isset( $_POST['user_login'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '';
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入注册邮箱。', 'yneko-reimu' ) ), 400 );
	}

	$success_message = esc_html__( '如果该邮箱已注册，验证码将发送到对应邮箱。', 'yneko-reimu' );
	$cooldown_key    = yneko_reimu_auth_code_cooldown_transient_key( 'lost', $identifier, $identifier );
	if ( get_transient( $cooldown_key ) ) {
		wp_send_json_success( array( 'message' => $success_message ) );
	}

	$user = yneko_reimu_find_user_for_password_reset( $identifier );
	if ( ! $user ) {
		set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );
		wp_send_json_success( array( 'message' => $success_message ) );
	}

	$code  = (string) random_int( 100000, 999999 );
	$title = sprintf(
		/* translators: %s: site title. */
		__( '[%s] 密码重置验证码', 'yneko-reimu' ),
		wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
	);
	$message = sprintf(
		/* translators: 1: verification code, 2: expiry minutes. */
		__( '您的密码重置验证码是：%1$s', 'yneko-reimu' ) . "\n\n" . __( '该验证码将在 %2$d 分钟后失效。如果这不是您本人操作，请立即检查账号安全。', 'yneko-reimu' ),
		$code,
		5
	);

	if ( ! wp_mail( $user->user_email, wp_specialchars_decode( $title ), $message ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码邮件发送失败，请稍后重试。', 'yneko-reimu' ) ), 500 );
	}

	set_transient(
		yneko_reimu_auth_code_transient_key( 'lost', $identifier, $user->user_email ),
		array(
			'code_hash' => wp_hash_password( $code ),
			'attempts'  => 0,
			'user_id'   => absint( $user->ID ),
		),
		5 * MINUTE_IN_SECONDS
	);
	set_transient( $cooldown_key, 1, MINUTE_IN_SECONDS );

	wp_send_json_success( array( 'message' => $success_message ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_lostpassword_code', 'yneko_reimu_ajax_send_lostpassword_code' );

function yneko_reimu_ajax_lostpassword() {
	check_ajax_referer( 'yneko_reimu_ajax_lostpassword', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );

	$identifier    = isset( $_POST['user_login'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '';
	$user_password = isset( $_POST['user_password'] ) ? (string) wp_unslash( $_POST['user_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Passwords must be reset raw.
	$verify_code   = isset( $_POST['verify_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['verify_code'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( '' === $identifier || ! is_email( $identifier ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入注册邮箱。', 'yneko-reimu' ) ), 400 );
	}
	if ( strlen( $user_password ) < 8 ) {
		wp_send_json_error( array( 'message' => esc_html__( '密码至少需要 8 个字符。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! preg_match( '/^\d{6}$/', $verify_code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请输入 6 位邮箱验证码。', 'yneko-reimu' ) ), 400 );
	}

	$user = yneko_reimu_find_user_for_password_reset( $identifier );
	if ( ! $user ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
	}

	$code_key  = yneko_reimu_auth_code_transient_key( 'lost', $identifier, $user->user_email );
	$code_data = get_transient( $code_key );
	if ( ! is_array( $code_data ) || empty( $code_data['code_hash'] ) || absint( $code_data['user_id'] ?? 0 ) !== absint( $user->ID ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确或已失效。', 'yneko-reimu' ) ), 400 );
	}

	$attempts = isset( $code_data['attempts'] ) ? absint( $code_data['attempts'] ) : 0;
	if ( $attempts >= 5 ) {
		delete_transient( $code_key );
		wp_send_json_error( array( 'message' => esc_html__( '验证码错误次数过多，请重新获取。', 'yneko-reimu' ) ), 429 );
	}

	if ( ! wp_check_password( $verify_code, $code_data['code_hash'] ) ) {
		$code_data['attempts'] = $attempts + 1;
		set_transient( $code_key, $code_data, 5 * MINUTE_IN_SECONDS );
		wp_send_json_error( array( 'message' => esc_html__( '验证码不正确。', 'yneko-reimu' ) ), 400 );
	}

	wp_set_password( $user_password, $user->ID );
	delete_transient( $code_key );
	delete_transient( yneko_reimu_auth_code_cooldown_transient_key( 'lost', $identifier, $identifier ) );

	wp_send_json_success( array( 'message' => esc_html__( '密码已重置，请返回登录。', 'yneko-reimu' ) ) );
}
add_action( 'wp_ajax_nopriv_yneko_reimu_lostpassword', 'yneko_reimu_ajax_lostpassword' );

function yneko_reimu_comment_like_cookie_name() {
	return 'yneko_reimu_comment_like_id';
}

function yneko_reimu_comment_like_guest_token() {
	$cookie_name = yneko_reimu_comment_like_cookie_name();
	$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	if ( preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return strtolower( $token );
	}

	$token   = str_replace( '-', '', wp_generate_uuid4() ) . str_replace( '-', '', wp_generate_uuid4() );
	$expires = time() + YEAR_IN_SECONDS;
	$cookie  = array(
		'expires'  => $expires,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'secure'   => is_ssl(),
		'httponly' => true,
		'samesite' => 'Lax',
	);
	if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) {
		$cookie['domain'] = COOKIE_DOMAIN;
	}

	setcookie( $cookie_name, $token, $cookie );
	$_COOKIE[ $cookie_name ] = $token;

	return $token;
}

function yneko_reimu_comment_like_actor_key( $create_guest = true ) {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		return 'user:' . absint( $user_id );
	}

	$token = $create_guest ? yneko_reimu_comment_like_guest_token() : '';
	if ( ! $token ) {
		$cookie_name = yneko_reimu_comment_like_cookie_name();
		$token       = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	}
	if ( ! preg_match( '/^[a-f0-9]{32,64}$/i', $token ) ) {
		return '';
	}

	return 'guest:' . hash_hmac( 'sha256', $token, wp_salt( 'nonce' ) );
}

function yneko_reimu_comment_like_registry( $comment_id ) {
	$registry = yneko_reimu_get_comment_meta( absint( $comment_id ), '_yneko_reimu_like_registry', true );
	return is_array( $registry ) ? $registry : array();
}

function yneko_reimu_comment_like_baseline( $comment_id ) {
	$comment_id = absint( $comment_id );
	$baseline   = yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_baseline', true );
	if ( '' !== $baseline && null !== $baseline ) {
		return absint( $baseline );
	}

	$current        = absint( yneko_reimu_get_comment_meta( $comment_id, '_yneko_reimu_like_count', true ) );
	$stored_registry = yneko_reimu_comment_like_registry( $comment_id );
	$baseline       = max( 0, $current - count( array_filter( $stored_registry ) ) );
	update_comment_meta( $comment_id, '_yneko_reimu_like_baseline', $baseline );

	return absint( $baseline );
}

function yneko_reimu_comment_like_count_from_registry( $comment_id, $registry = null ) {
	$registry = is_array( $registry ) ? $registry : yneko_reimu_comment_like_registry( $comment_id );
	return yneko_reimu_comment_like_baseline( $comment_id ) + count( array_filter( $registry ) );
}

function yneko_reimu_comment_user_liked( $comment_id ) {
	$comment_id = absint( $comment_id );
	if ( ! $comment_id ) {
		return false;
	}

	$actor_key = yneko_reimu_comment_like_actor_key( false );
	if ( ! $actor_key ) {
		return false;
	}

	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	return ! empty( $registry[ $actor_key ] );
}

function yneko_reimu_ajax_comment_like() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;

	if ( ! $comment ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	check_ajax_referer( 'yneko_reimu_comment_like_' . $comment_id, 'nonce' );

	if ( ! yneko_reimu_comment_is_publicly_visible( $comment ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( '评论不存在。', 'yneko-reimu' ),
			),
			404
		);
	}

	$actor_key = yneko_reimu_comment_like_actor_key();
	$registry  = yneko_reimu_comment_like_registry( $comment_id );
	$next_liked = empty( $registry[ $actor_key ] );
	if ( $next_liked ) {
		$registry[ $actor_key ] = time();
	} else {
		unset( $registry[ $actor_key ] );
	}

	$count = yneko_reimu_comment_like_count_from_registry( $comment_id, $registry );
	if ( empty( $registry ) ) {
		delete_comment_meta( $comment_id, '_yneko_reimu_like_registry' );
	} else {
		update_comment_meta( $comment_id, '_yneko_reimu_like_registry', $registry );
	}
	update_comment_meta( $comment_id, '_yneko_reimu_like_count', $count );

	wp_send_json_success(
		array(
			'count' => $count,
			'liked' => $next_liked,
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );
add_action( 'wp_ajax_nopriv_yneko_reimu_comment_like', 'yneko_reimu_ajax_comment_like' );

function yneko_reimu_current_user_can_manage_comment( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'moderate_comments' ) || current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		return true;
	}

	$user_id = get_current_user_id();
	return $user_id && absint( $comment->user_id ) === absint( $user_id );
}

function yneko_reimu_current_user_can_view_private_comment( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment || ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'moderate_comments' ) || current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		return true;
	}

	return absint( $comment->user_id ) && absint( $comment->user_id ) === get_current_user_id();
}

function yneko_reimu_comment_visible_upload_review_status( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '';
	}

	foreach ( yneko_reimu_comment_extract_image_urls( $comment->comment_content ) as $url ) {
		if ( false !== strpos( $url, 'yneko-reimu-comments/tmp/' ) ) {
			$review = yneko_reimu_comment_temp_review_status( $comment->comment_ID, $url );
			return in_array( (string) ( $review['status'] ?? '' ), array( 'pending', 'rejected' ), true ) ? (string) $review['status'] : 'pending';
		}
		$attachment_id = attachment_url_to_postid( $url );
		if ( ! $attachment_id || '1' !== get_post_meta( $attachment_id, '_yneko_reimu_comment_upload', true ) ) {
			continue;
		}
		$status = (string) get_post_meta( $attachment_id, '_yneko_reimu_comment_upload_status', true );
		if ( in_array( $status, array( 'pending', 'revoked', 'rejected' ), true ) ) {
			return $status;
		}
	}

	return '';
}

function yneko_reimu_comment_is_publicly_visible( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return false;
	}

	if ( '1' !== (string) $comment->comment_approved ) {
		return yneko_reimu_current_user_can_view_private_comment( $comment );
	}

	$review_status = yneko_reimu_comment_visible_upload_review_status( $comment );
	if ( in_array( $review_status, array( 'pending', 'revoked', 'rejected' ), true ) ) {
		return yneko_reimu_current_user_can_view_private_comment( $comment );
	}

	return true;
}

function yneko_reimu_get_visible_comments( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return array();
	}

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'status'  => is_user_logged_in() ? 'all' : 'approve',
			'order'   => 'ASC',
			'type'    => 'comment',
		)
	);

	return array_values(
		array_filter(
			$comments,
			static function ( $comment ) {
				return yneko_reimu_comment_is_publicly_visible( $comment );
			}
		)
	);
}

function yneko_reimu_ajax_edit_comment() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;
	if ( ! $comment ) {
		wp_send_json_error( array( 'message' => __( '评论不存在。', 'yneko-reimu' ) ), 404 );
	}

	check_ajax_referer( 'yneko_reimu_comment_manage_' . $comment_id, 'nonce' );
	if ( ! yneko_reimu_current_user_can_manage_comment( $comment ) ) {
		wp_send_json_error( array( 'message' => __( '你不能编辑这条评论。', 'yneko-reimu' ) ), 403 );
	}

	$content = isset( $_POST['comment'] ) ? trim( (string) wp_unslash( $_POST['comment'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Comment content is passed to wp_update_comment and rendered through the theme sanitizer.
	if ( '' === $content ) {
		wp_send_json_error( array( 'message' => __( '评论内容不能为空。', 'yneko-reimu' ) ), 400 );
	}
	if ( yneko_reimu_comment_media_count( $content ) > 1 ) {
		wp_send_json_error( array( 'message' => __( '一条评论最多只能添加一张图片或一个 GIF。', 'yneko-reimu' ) ), 400 );
	}

	$result = wp_update_comment(
		array(
			'comment_ID'      => $comment_id,
			'comment_content' => $content,
		),
		true
	);
	if ( is_wp_error( $result ) || ! $result ) {
		wp_send_json_error( array( 'message' => is_wp_error( $result ) ? $result->get_error_message() : __( '评论更新失败。', 'yneko-reimu' ) ), 400 );
	}

	$comment = get_comment( $comment_id );
	wp_send_json_success(
		array(
			'comment_id' => $comment_id,
			'html'       => yneko_reimu_render_comment_markdown( $comment->comment_content ),
			'raw'        => $comment->comment_content,
			'message'    => __( '评论已更新。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_edit_comment', 'yneko_reimu_ajax_edit_comment' );

function yneko_reimu_ajax_delete_comment() {
	$comment_id = isset( $_POST['comment_id'] ) ? absint( wp_unslash( $_POST['comment_id'] ) ) : 0;
	$comment    = $comment_id ? get_comment( $comment_id ) : null;
	if ( ! $comment ) {
		wp_send_json_error( array( 'message' => __( '评论不存在。', 'yneko-reimu' ) ), 404 );
	}

	check_ajax_referer( 'yneko_reimu_comment_manage_' . $comment_id, 'nonce' );
	if ( ! yneko_reimu_current_user_can_manage_comment( $comment ) ) {
		wp_send_json_error( array( 'message' => __( '你不能删除这条评论。', 'yneko-reimu' ) ), 403 );
	}

	$post_id = absint( $comment->comment_post_ID );
	if ( ! wp_delete_comment( $comment_id, true ) ) {
		wp_send_json_error( array( 'message' => __( '评论删除失败。', 'yneko-reimu' ) ), 400 );
	}

	$count = get_comments_number( $post_id );
	wp_send_json_success(
		array(
			'comment_id' => $comment_id,
			'count'      => absint( $count ),
			'count_label'=> sprintf(
				/* translators: %s: number of comments. */
				_n( '%s 评论', '%s 评论', $count, 'yneko-reimu' ),
				number_format_i18n( $count )
			),
			'message'    => __( '评论已删除。', 'yneko-reimu' ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_delete_comment', 'yneko_reimu_ajax_delete_comment' );

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
		$status = 400;
		$data   = $comment->get_error_data();
		if ( is_numeric( $data ) ) {
			$status = absint( $data );
		} elseif ( is_array( $data ) && isset( $data['status'] ) ) {
			$status = absint( $data['status'] );
		}
		if ( $status < 400 || $status > 599 ) {
			$status = 400;
		}
		wp_send_json_error(
			array(
				'message' => $comment->get_error_message(),
				'code'    => $comment->get_error_code(),
			),
			$status
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
	if ( ! $is_approved && is_user_logged_in() && absint( $comment->user_id ) === get_current_user_id() ) {
		yneko_reimu_increment_user_review_status_count( get_current_user_id(), 'comments', 'pending', $comment->comment_ID );
	}
	foreach ( yneko_reimu_comment_extract_image_urls( $comment->comment_content ) as $url ) {
		if ( false !== strpos( $url, 'yneko-reimu-comments/tmp/' ) ) {
			yneko_reimu_comment_set_temp_review_status( $comment->comment_ID, $url, 'pending' );
		}
	}
	$count       = count( yneko_reimu_get_visible_comments( (int) $comment->comment_post_ID ) );
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

function yneko_reimu_update_comment_review_status_for_user( $comment_id, $status ) {
	if ( ! empty( $GLOBALS['yneko_reimu_suppress_comment_review_status'] ) ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || empty( $comment->user_id ) ) {
		return;
	}

	$status = sanitize_key( $status );
	if ( in_array( $status, array( 'approve', 'approved', '1' ), true ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'updated', $comment->comment_ID );
	} elseif ( in_array( $status, array( 'hold', 'unapprove', 'unapproved', '0', 'spam', 'trash', 'delete' ), true ) ) {
		yneko_reimu_set_user_review_status( $comment->user_id, 'comments', 'rejected', $comment->comment_ID );
	}
}

function yneko_reimu_comment_status_changed( $comment_id, $comment_status = '' ) {
	yneko_reimu_update_comment_review_status_for_user( $comment_id, $comment_status );
}
add_action( 'wp_set_comment_status', 'yneko_reimu_comment_status_changed', 10, 2 );

function yneko_reimu_hold_comment_with_pending_uploads( $approved, $commentdata ) {
	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	if ( false !== strpos( $content, 'yneko-reimu-comments/tmp/' ) ) {
		return 0;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'yneko_reimu_hold_comment_with_pending_uploads', 10, 2 );


function yneko_reimu_delete_upload_by_url( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return;
	}
	$uploads = wp_get_upload_dir();
	if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) || 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return;
	}
	$path = $uploads['basedir'] . str_replace( '/', DIRECTORY_SEPARATOR, substr( $url, strlen( $uploads['baseurl'] ) ) );
	if ( is_file( $path ) ) {
		wp_delete_file( $path );
	}
}

function yneko_reimu_avatar_admin_action() {
	$action  = isset( $_GET['yneko_avatar_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_avatar_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id ) {
		return;
	}
	if ( ! in_array( $action, array( 'approve', 'reject', 'delete' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_avatar_' . $action . '_' . $user_id );

	$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
	$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );

	if ( 'approve' === $action && $pending ) {
		if ( $current && $current !== $pending ) {
			yneko_reimu_delete_upload_by_url( $current );
		}
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	} elseif ( 'reject' === $action && $pending ) {
		yneko_reimu_delete_upload_by_url( $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'rejected' );
	} elseif ( 'delete' === $action ) {
		yneko_reimu_delete_upload_by_url( $current );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
		yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_avatar_action', 'user_id', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_avatar_admin_action' );

function yneko_reimu_user_badge_admin_action() {
	$action  = isset( $_GET['yneko_user_badge_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_user_badge_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$index   = isset( $_GET['tag_index'] ) ? absint( $_GET['tag_index'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id || ! in_array( $action, array( 'approve', 'reject', 'revoke' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_user_badge_' . $action . '_' . $user_id . '_' . $index );

	$active  = yneko_reimu_comment_user_custom_tags( $user_id );
	$pending = yneko_reimu_comment_user_pending_tags( $user_id );

	if ( 'approve' === $action && isset( $pending[ $index ] ) ) {
		$approved_tag = $pending[ $index ];
		$replace_id   = sanitize_key( $approved_tag['old_id'] ?? ( $approved_tag['id'] ?? '' ) );
		$active = array_values(
			array_filter(
				$active,
				static function ( $tag ) use ( $approved_tag, $replace_id ) {
					if ( ! is_array( $tag ) ) {
						return false;
					}
					$tag_id = sanitize_key( $tag['id'] ?? '' );
					if ( $replace_id && $tag_id === $replace_id ) {
						return false;
					}
					return ! yneko_reimu_comment_tags_same_label( $tag['label'] ?? '', $approved_tag['label'] ?? '' );
				}
			)
		);
		unset( $approved_tag['old_id'], $approved_tag['old_label'] );
		$active[] = $approved_tag;
		$active   = yneko_reimu_comment_normalize_tag_list( $active, yneko_reimu_comment_custom_tag_storage_limit() );
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'updated' );
	} elseif ( 'reject' === $action && isset( $pending[ $index ] ) ) {
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'rejected' );
	} elseif ( 'revoke' === $action && isset( $active[ $index ] ) ) {
		unset( $active[ $index ] );
		$active = array_values( $active );
		yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
	}

	if ( $active ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags', $active );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
	}
	if ( $pending ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $pending );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_user_badge_action', 'user_id', 'tag_index', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_user_badge_admin_action' );

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

	return yneko_reimu_comment_avatar_with_frame( get_avatar( $comment, $size, $default ), absint( $comment->user_id ), 'reimu-avatar-frame--comment' );
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
		'/```\s*(?:[a-z0-9_-]+)?[^\n]*\n?([\s\S]*?)```/i',
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
	$like_count   = yneko_reimu_comment_like_count_from_registry( $comment_id );
	$user_liked   = yneko_reimu_comment_user_liked( $comment_id );
	$badges       = yneko_reimu_comment_agent_badges( $comment->comment_agent, $comment->comment_author_IP );
	$user_badges  = ! empty( $comment->user_id ) ? yneko_reimu_comment_user_badges_html( $comment->user_id ) : '';
	$is_logged_in_commenter = ! empty( $comment->user_id );
	$comment_link = get_comment_link( $comment );
	$can_manage   = yneko_reimu_current_user_can_manage_comment( $comment );
	$review_label = yneko_reimu_comment_media_review_label( $comment );
	$review_status = yneko_reimu_comment_visible_upload_review_status( $comment );
	if ( ! empty( $GLOBALS['yneko_reimu_comment_display_url'] ) ) {
		$comment_link = untrailingslashit( (string) $GLOBALS['yneko_reimu_comment_display_url'] ) . '#comment-' . absint( $comment_id );
	}
	?>
	<li <?php comment_class( 'reimu-comment' . ( 'rejected' === $review_status ? ' reimu-comment-rejected' : '' ) ); ?> id="comment-<?php comment_ID(); ?>" data-comment-time="<?php echo esc_attr( $comment_time ); ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-comment-user-id="<?php echo esc_attr( absint( $comment->user_id ) ); ?>" data-comment-likes="<?php echo esc_attr( $like_count ); ?>" data-comment-liked="<?php echo $user_liked ? '1' : '0'; ?>">
		<article class="reimu-comment__body">
			<a class="reimu-comment__avatar<?php echo $is_logged_in_commenter ? ' reimu-comment__avatar--logged-in' : ''; ?>" href="<?php echo esc_url( $comment_link ); ?>" aria-hidden="true" tabindex="-1"><?php echo yneko_reimu_get_comment_avatar( $comment, 56 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<div class="reimu-comment__content">
				<header class="reimu-comment__meta">
					<span class="reimu-comment__headline">
						<span class="reimu-comment__author"><?php echo yneko_reimu_comment_author_link_html( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php echo $user_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="reimu-comment__date" href="<?php echo esc_url( $comment_link ); ?>">
							<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( get_comment_date( 'Y-m-d', $comment ) ); ?></time>
						</a>
					</span>
					<?php echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</header>
				<?php if ( $review_label ) : ?>
					<p class="comment-awaiting-moderation<?php echo 'rejected' === $review_status ? ' is-rejected' : ''; ?>"><?php echo esc_html( $review_label ); ?></p>
				<?php endif; ?>
				<div class="comment-text wl-content" data-comment-raw="<?php echo esc_attr( $comment->comment_content ); ?>"><?php echo yneko_reimu_render_comment_markdown( $comment->comment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div class="reimu-comment__footer" aria-label="<?php esc_attr_e( '评论操作', 'yneko-reimu' ); ?>">
					<?php if ( $can_manage ) : ?>
						<button type="button" class="reimu-comment-owner-action" data-comment-edit="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_manage_' . $comment_id ) ); ?>" aria-label="<?php esc_attr_e( '编辑评论', 'yneko-reimu' ); ?>">
							<?php echo yneko_reimu_waline_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" class="reimu-comment-owner-action reimu-comment-owner-action--delete" data-comment-delete="<?php echo esc_attr( $comment_id ); ?>" data-comment-manage-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_manage_' . $comment_id ) ); ?>" aria-label="<?php esc_attr_e( '删除评论', 'yneko-reimu' ); ?>">
							<?php echo yneko_reimu_waline_icon( 'trash' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					<?php endif; ?>
					<button type="button" class="reimu-comment-like<?php echo $user_liked ? ' liked' : ''; ?>" data-comment-like="<?php echo esc_attr( $comment_id ); ?>" data-like-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_comment_like_' . $comment_id ) ); ?>" aria-pressed="<?php echo $user_liked ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( '点赞', 'yneko-reimu' ); ?>">
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
	$config  = function_exists( 'yneko_reimu_settings_external_comments' ) ? yneko_reimu_settings_external_comments() : array();

	if (
		! empty( $config['giscus_enable'] ) && '1' === (string) $config['giscus_enable']
		&& ! empty( $config['giscus_repo'] )
		&& ! empty( $config['giscus_repo_id'] )
		&& ! empty( $config['giscus_category'] )
		&& ! empty( $config['giscus_category_id'] )
	) {
		$systems['giscus'] = array(
			'label'       => 'giscus',
			'repo'        => $config['giscus_repo'],
			'repo_id'     => $config['giscus_repo_id'],
			'category'    => $config['giscus_category'] ? $config['giscus_category'] : 'General',
			'category_id' => $config['giscus_category_id'],
		);
	}

	if ( ! empty( $config['utterances_enable'] ) && '1' === (string) $config['utterances_enable'] && ! empty( $config['utterances_repo'] ) ) {
		$systems['utterances'] = array(
			'label' => 'utterances',
			'repo'  => $config['utterances_repo'],
		);
	}

	if ( ! empty( $config['disqus_enable'] ) && '1' === (string) $config['disqus_enable'] && ! empty( $config['disqus_shortname'] ) ) {
		$systems['disqus'] = array(
			'label'     => 'disqus',
			'shortname' => $config['disqus_shortname'],
		);
	}

	if ( ! empty( $config['waline_enable'] ) && '1' === (string) $config['waline_enable'] && ! empty( $config['waline_server_url'] ) ) {
		$systems['waline'] = array(
			'label'      => 'waline',
			'server_url' => $config['waline_server_url'],
		);
	}

	if ( ! empty( $config['twikoo_enable'] ) && '1' === (string) $config['twikoo_enable'] && ! empty( $config['twikoo_env_id'] ) ) {
		$systems['twikoo'] = array(
			'label'  => 'twikoo',
			'env_id' => $config['twikoo_env_id'],
		);
	}

	if ( ! empty( $config['valine_enable'] ) && '1' === (string) $config['valine_enable'] && ! empty( $config['valine_app_id'] ) && ! empty( $config['valine_app_key'] ) ) {
		$systems['valine'] = array(
			'label'      => 'valine',
			'app_id'     => $config['valine_app_id'],
			'app_key'    => $config['valine_app_key'],
			'server_url' => $config['valine_server_url'],
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
