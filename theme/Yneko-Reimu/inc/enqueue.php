<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_vendor_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['vendor_cdn_base'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_vendor_cdn_base', 'https://cdn.jsdelivr.net/npm' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) : 'https://cdn.jsdelivr.net/npm';
}

function yneko_reimu_vendor_url( $package_path ) {
	return yneko_reimu_vendor_base_url() . '/' . ltrim( $package_path, '/' );
}

function yneko_reimu_asset_version( $relative_path ) {
	$path = YNEKO_REIMU_DIR . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $path ) ) {
		return YNEKO_REIMU_VERSION . '.' . filemtime( $path );
	}

	return YNEKO_REIMU_VERSION;
}

function yneko_reimu_default_aplayer_audio_json() {
	return '[]';
}

function yneko_reimu_json_theme_mod( $key, $default = '' ) {
	$value = trim( (string) yneko_reimu_get_theme_mod( $key, $default ) );

	if ( '' === $value ) {
		return null;
	}

	$decoded = json_decode( $value, true );
	return is_array( $decoded ) ? $decoded : null;
}

function yneko_reimu_normalize_aplayer_audio( $audio ) {
	if ( ! is_array( $audio ) ) {
		return $audio;
	}

	return $audio;
}

function yneko_reimu_live2d_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['live2d_base_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_live2d_base_url', 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) . '/' : 'https://fastly.jsdelivr.net/gh/stevenjoezhang/live2d-widget@latest/';
}

function yneko_reimu_live2d_api_base_url() {
	$third_party = function_exists( 'yneko_reimu_settings_third_party' ) ? yneko_reimu_settings_third_party() : array();
	$base = $third_party['live2d_api_base_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_live2d_api_base_url', 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) . '/' : 'https://fastly.jsdelivr.net/gh/fghrsh/live2d_api/';
}

function yneko_reimu_cursor_variables_css() {
	if ( ! yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false ) ) {
		return ':root{--cursor-default:auto;--cursor-pointer:pointer;--cursor-text:text;--cursor-busy:wait;--cursor-progress:progress;--cursor-not-allowed:not-allowed;--cursor-help:help;--cursor-move:move;--cursor-grab:grab;--cursor-grabbing:grabbing;--cursor-zoom-in:zoom-in;--cursor-crosshair:crosshair;--cursor-ew-resize:ew-resize;--cursor-ns-resize:ns-resize;--cursor-nwse-resize:nwse-resize;--cursor-nesw-resize:nesw-resize;--cursor-alias:alias;}';
	}

	$base = YNEKO_REIMU_URI . '/assets/images/cursor/';
	return ':root{' .
		'--cursor-default:url(' . esc_url( $base . 'lily-normal.png' ) . '), auto;' .
		'--cursor-pointer:url(' . esc_url( $base . 'lily-link.png' ) . '), pointer;' .
		'--cursor-text:url(' . esc_url( $base . 'lily-text.png' ) . '), text;' .
		'--cursor-busy:url(' . esc_url( $base . 'lily-busy.png' ) . '), wait;' .
		'--cursor-progress:url(' . esc_url( $base . 'lily-work.png' ) . '), progress;' .
		'--cursor-not-allowed:url(' . esc_url( $base . 'lily-unavailable.png' ) . '), not-allowed;' .
		'--cursor-help:url(' . esc_url( $base . 'lily-help.png' ) . '), help;' .
		'--cursor-move:url(' . esc_url( $base . 'lily-move.png' ) . '), move;' .
		'--cursor-grab:url(' . esc_url( $base . 'lily-hand.png' ) . '), grab;' .
		'--cursor-grabbing:url(' . esc_url( $base . 'lily-hand.png' ) . '), grabbing;' .
		'--cursor-zoom-in:url(' . esc_url( $base . 'lily-help.png' ) . '), zoom-in;' .
		'--cursor-crosshair:url(' . esc_url( $base . 'lily-cross.png' ) . '), crosshair;' .
		'--cursor-ew-resize:url(' . esc_url( $base . 'lily-resize-ew.png' ) . '), ew-resize;' .
		'--cursor-ns-resize:url(' . esc_url( $base . 'lily-resize-ns.png' ) . '), ns-resize;' .
		'--cursor-nwse-resize:url(' . esc_url( $base . 'lily-resize-nwse.png' ) . '), nwse-resize;' .
		'--cursor-nesw-resize:url(' . esc_url( $base . 'lily-resize-nesw.png' ) . '), nesw-resize;' .
		'--cursor-alias:url(' . esc_url( $base . 'lily-alternate.png' ) . '), alias;' .
	'}';
}

function yneko_reimu_critical_cursor() {
	$cursor_base = YNEKO_REIMU_URI . '/assets/images/cursor/';
	$strategy    = yneko_reimu_asset_strategy();
	?>
	<?php if ( yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false ) && ! empty( $strategy['preload_cursor_images'] ) ) : ?>
		<?php foreach ( (array) $strategy['preload_cursor_variants'] as $cursor_file ) : ?>
			<link rel="preload" as="image" href="<?php echo esc_url( $cursor_base . basename( $cursor_file ) ); ?>">
		<?php endforeach; ?>
	<?php endif; ?>
	<style id="yneko-reimu-critical-cursor">
		<?php echo yneko_reimu_cursor_variables_css(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		html,
		body,
		#container,
		#wrap {
			cursor: var(--cursor-default) !important;
		}
		a,
		button,
		[role="button"] {
			cursor: var(--cursor-pointer) !important;
		}
		input,
		textarea {
			cursor: var(--cursor-text) !important;
		}
		input[type="button"],
		input[type="submit"],
		input[type="reset"],
		input[type="checkbox"],
		input[type="radio"] {
			cursor: var(--cursor-pointer) !important;
		}
		html.reimu-page-loading,
		html.reimu-page-loading *,
		body.reimu-page-loading,
		body.reimu-page-loading *,
		#loader,
		#loader * {
			cursor: var(--cursor-progress) !important;
		}
	</style>
	<script>
		(function(){var d=document.documentElement;function setLoading(){d.classList.add('reimu-page-loading');if(document.body){document.body.classList.add('reimu-page-loading');}}function shouldLoad(e){if(e.defaultPrevented||e.metaKey||e.ctrlKey||e.shiftKey||e.altKey||(typeof e.button==='number'&&e.button>0)){return false;}var a=e.target&&e.target.closest?e.target.closest('a[href]'):null;if(!a||a.target||a.hasAttribute('download')||a.origin!==location.origin){return false;}return !(a.pathname===location.pathname&&a.search===location.search&&a.hash);}function mark(e){if(shouldLoad(e)){setLoading();}}d.addEventListener('pointerdown',mark,true);d.addEventListener('mousedown',mark,true);d.addEventListener('click',mark,true);window.addEventListener('beforeunload',setLoading);}());
	</script>
	<?php
}
add_action( 'wp_head', 'yneko_reimu_critical_cursor', 0 );

function yneko_reimu_enqueue_assets() {
	$asset_strategy = yneko_reimu_asset_strategy();
	wp_enqueue_style( 'yneko-reimu-theme', get_stylesheet_uri(), array(), YNEKO_REIMU_VERSION );
	if ( ! empty( $asset_strategy['font_display'] ) ) {
		wp_enqueue_style( 'yneko-reimu-fonts', 'https://fonts.googleapis.com/css?family=Mulish:400,400italic,700,700italic|Noto+Serif+SC:400,400italic,700,700italic&display=swap', array(), null );
	}
	wp_enqueue_style( 'yneko-reimu-loader', YNEKO_REIMU_URI . '/assets/dist/loader.css', array( 'yneko-reimu-theme' ), yneko_reimu_asset_version( 'assets/dist/loader.css' ) );
	$aplayer_audio  = yneko_reimu_normalize_aplayer_audio( yneko_reimu_settings_music_items() );
	$enable_aplayer = yneko_reimu_player_enabled( $aplayer_audio );
	if ( $enable_aplayer ) {
		wp_enqueue_style( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.css' ), array(), '1.10.1' );
	}
	$main_style_deps = array( 'yneko-reimu-theme', 'yneko-reimu-loader' );
	if ( ! empty( $asset_strategy['font_display'] ) ) {
		$main_style_deps[] = 'yneko-reimu-fonts';
	}
	if ( $enable_aplayer ) {
		$main_style_deps[] = 'yneko-reimu-aplayer';
	}
	wp_enqueue_style( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.css', $main_style_deps, yneko_reimu_asset_version( 'assets/dist/reimu.css' ) );

	$accent = sanitize_hex_color( yneko_reimu_get_theme_mod( 'yneko_reimu_accent_color', '#ff5252' ) );
	$accent = $accent ? $accent : '#ff5252';
	wp_add_inline_style( 'yneko-reimu-main', ':root{--red-1:' . esc_html( $accent ) . ';--color-link:' . esc_html( $accent ) . ';}' );
	if ( ! yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false ) ) {
		wp_add_inline_style( 'yneko-reimu-main', yneko_reimu_cursor_variables_css() );
	}
	if ( ! yneko_reimu_get_theme_mod( 'yneko_reimu_sticky_nav', true ) ) {
		wp_add_inline_style( 'yneko-reimu-main', '#header-nav{position:absolute;}' );
	}

	$main_script_deps = array();

	$current_language   = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : get_locale();
	$builtin_search_url = function_exists( 'yneko_reimu_search_json_url' ) ? yneko_reimu_search_json_url( $current_language ) : '';
	$search_settings    = function_exists( 'yneko_reimu_settings_search' ) ? yneko_reimu_settings_search() : array();
	$player_settings    = function_exists( 'yneko_reimu_settings_player' ) ? yneko_reimu_settings_player() : array();
	$external_comments  = function_exists( 'yneko_reimu_settings_external_comments' ) ? yneko_reimu_settings_external_comments() : array();
	$local_search_url   = $search_settings['local_json_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_local_search_json', '' );
	$local_search_url   = $local_search_url ? $local_search_url : $builtin_search_url;
	$search             = array(
		'type'    => 'wordpress',
		'restUrl' => esc_url_raw( rest_url( 'wp/v2/search' ) ),
		'perPage' => 10,
		'language' => $current_language,
	);

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_algolia_enable', false ) && ! empty( $search_settings['algolia_app_id'] ) && ! empty( $search_settings['algolia_api_key'] ) && ! empty( $search_settings['algolia_index_name'] ) ) {
		$search = array(
			'type'      => 'algolia',
			'appId'     => $search_settings['algolia_app_id'],
			'apiKey'    => $search_settings['algolia_api_key'],
			'indexName' => $search_settings['algolia_index_name'],
			'perPage'   => 10,
		);
		wp_enqueue_script( 'yneko-reimu-algoliasearch', yneko_reimu_vendor_url( 'algoliasearch@4.24.0/dist/algoliasearch-lite.umd.js' ), array(), '4.24.0', true );
		$main_script_deps[] = 'yneko-reimu-algoliasearch';
	} elseif ( yneko_reimu_feature_enabled( 'yneko_reimu_generator_search_enable', true ) && $local_search_url ) {
		$search = array(
			'type'     => 'local',
			'localUrl' => esc_url_raw( $local_search_url ),
			'perPage'  => 10,
			'language' => $current_language,
		);
	}

	$custom_cursor = yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false );
	$i18n = array(
		'copy'                  => esc_html__( '复制', 'yneko-reimu' ),
		'copied'                => esc_html__( '复制成功 (*^▽^*)', 'yneko-reimu' ),
		'copyFailed'            => esc_html__( '复制失败 (ﾟ⊿ﾟ)ﾂ', 'yneko-reimu' ),
		'collapseCode'          => esc_html__( '折叠代码', 'yneko-reimu' ),
		'expandCode'            => esc_html__( '展开代码', 'yneko-reimu' ),
		'searchHint'            => esc_html__( '输入关键词后按回车搜索。', 'yneko-reimu' ),
		'searching'             => esc_html__( '少女检索中...', 'yneko-reimu' ),
		'searchStats'           => esc_html__( '找到 {count} 条结果', 'yneko-reimu' ),
		'searchEmpty'           => esc_html__( '未发现与「{query}」相关内容', 'yneko-reimu' ),
		'searchUntitled'        => esc_html__( '无标题', 'yneko-reimu' ),
		'searchNoResults'       => esc_html__( '没有结果', 'yneko-reimu' ),
		'searchIndexFailed'     => esc_html__( '本地搜索索引加载失败。', 'yneko-reimu' ),
		'loadMore'              => esc_html__( '加载更多...', 'yneko-reimu' ),
		'loadEnd'               => esc_html__( '到底了...', 'yneko-reimu' ),
		'commentPreviewEmpty'   => esc_html__( '还没有内容。', 'yneko-reimu' ),
		'invalidImageUrl'       => esc_html__( '请输入 http(s) 图片地址', 'yneko-reimu' ),
		'commentUploadLogin'    => esc_html__( '登录后可上传图片。', 'yneko-reimu' ),
		'commentUploadGifLogin' => esc_html__( '登录后可上传 GIF。', 'yneko-reimu' ),
		'commentUploadImageDisabled'=> esc_html__( '评论图片上传已关闭。', 'yneko-reimu' ),
		'commentUploadGifDisabled'=> esc_html__( '评论 GIF 上传已关闭。', 'yneko-reimu' ),
		'commentUploadChoose'   => esc_html__( '请先选择文件。', 'yneko-reimu' ),
		'commentUploadChooseFile'=> esc_html__( '选择文件', 'yneko-reimu' ),
		'commentUploadUploading'=> esc_html__( '上传中...', 'yneko-reimu' ),
		'commentUploadFailed'   => esc_html__( '上传失败。', 'yneko-reimu' ),
		'commentUploadDone'     => esc_html__( '已插入评论。', 'yneko-reimu' ),
		'commentGifEmpty'       => esc_html__( '暂无可选...', 'yneko-reimu' ),
		'commentSubmitting'     => esc_html__( '提交中...', 'yneko-reimu' ),
		'commentSubmitFailed'   => esc_html__( '评论提交失败。', 'yneko-reimu' ),
		'commentSubmitSuccess'  => esc_html__( '评论已发布。', 'yneko-reimu' ),
		'commentSubmitPending'  => esc_html__( '评论已提交，正在等待审核。', 'yneko-reimu' ),
		'commentEditSave'       => esc_html__( '保存', 'yneko-reimu' ),
		'commentEditCancel'     => esc_html__( '取消', 'yneko-reimu' ),
		'commentEditFailed'     => esc_html__( '评论更新失败。', 'yneko-reimu' ),
		'commentDeleteConfirm'  => esc_html__( '确定删除这条评论吗？', 'yneko-reimu' ),
		'commentDeleteFailed'   => esc_html__( '评论删除失败。', 'yneko-reimu' ),
		'commentEmpty'          => esc_html__( '还没有评论，来抢一张小板凳吧。', 'yneko-reimu' ),
		'cancelReply'           => esc_html__( '取消回复', 'yneko-reimu' ),
		'replyComment'          => esc_html__( '回复评论', 'yneko-reimu' ),
		'login'                 => esc_html__( '登录', 'yneko-reimu' ),
		'register'              => esc_html__( '注册', 'yneko-reimu' ),
		'lostPassword'          => esc_html__( '忘记密码？', 'yneko-reimu' ),
		'registerDesc'          => esc_html__( '验证邮箱后即可创建账号。', 'yneko-reimu' ),
		'lostPasswordDesc'      => esc_html__( '验证邮箱后即可重置密码。', 'yneko-reimu' ),
		'loginLoading'          => esc_html__( '登录中...', 'yneko-reimu' ),
		'registerLoading'       => esc_html__( '注册中...', 'yneko-reimu' ),
		'resetLoading'          => esc_html__( '重置中...', 'yneko-reimu' ),
		'showPassword'          => esc_html__( '显示密码', 'yneko-reimu' ),
		'hidePassword'          => esc_html__( '隐藏密码', 'yneko-reimu' ),
		'passwordMismatch'      => esc_html__( '两次输入的密码不一致。', 'yneko-reimu' ),
		'upload'                => esc_html__( '上传', 'yneko-reimu' ),
		'emailDuplicate'        => esc_html__( '新邮箱地址不要与原邮箱地址重复。', 'yneko-reimu' ),
		'avatarPending'         => esc_html__( '头像审核中', 'yneko-reimu' ),
		'avatarUpdating'        => esc_html__( '头像更新中', 'yneko-reimu' ),
		'avatarUpdated'         => esc_html__( '头像已更新', 'yneko-reimu' ),
		'avatarRejected'        => esc_html__( '头像审核不通过', 'yneko-reimu' ),
		'tagsPending'           => esc_html__( '标签审核中', 'yneko-reimu' ),
		'tagsUpdated'           => esc_html__( '标签已更新', 'yneko-reimu' ),
		'tagsRejected'          => esc_html__( '标签审核不通过', 'yneko-reimu' ),
		'commentsPending'       => esc_html__( '评论审核中', 'yneko-reimu' ),
		'commentsUpdated'       => esc_html__( '评论已更新', 'yneko-reimu' ),
		'commentsRejected'      => esc_html__( '评论审核不通过', 'yneko-reimu' ),
		'avatarReady'           => esc_html__( '头像已选择，保存后生效。', 'yneko-reimu' ),
		'avatarInvalidType'     => esc_html__( '头像仅支持 JPG、PNG 或 WebP。', 'yneko-reimu' ),
		'avatarTooLarge'        => esc_html__( '头像文件超过大小限制。', 'yneko-reimu' ),
		'commentTagLimit'       => esc_html__( '特殊标签和已勾选的自定义标签合计最多 2 个。', 'yneko-reimu' ),
		'enable'                => esc_html__( '启用', 'yneko-reimu' ),
		'loginSuccess'          => esc_html__( '登录成功。', 'yneko-reimu' ),
		'loginFailed'           => esc_html__( '登录失败，请检查账号和密码。', 'yneko-reimu' ),
		'registerCodeSending'   => esc_html__( '发送中...', 'yneko-reimu' ),
		'registerCodeSent'      => esc_html__( '验证码已发送，请检查您的邮箱。', 'yneko-reimu' ),
		'registerCodeWait'      => esc_html__( '%s 秒后重发', 'yneko-reimu' ),
		'profile2faGenerated'   => esc_html__( '请用认证器扫码，并输入 6 位验证码后保存。', 'yneko-reimu' ),
		'imagePreview'          => esc_html__( '图片预览', 'yneko-reimu' ),
		'closePreview'          => esc_html__( '关闭预览', 'yneko-reimu' ),
		'previousImage'         => esc_html__( '上一张图片', 'yneko-reimu' ),
		'nextImage'             => esc_html__( '下一张图片', 'yneko-reimu' ),
	);
	$config = array(
		'language'        => $current_language,
		'i18nPrefix'      => function_exists( 'yneko_reimu_i18n_url_prefix' ) ? yneko_reimu_i18n_url_prefix() : 'en',
		'i18n'            => $i18n,
		'darkModeDefault' => yneko_reimu_get_theme_mod( 'yneko_reimu_dark_mode_default', 'auto' ),
		'showThemeToggle' => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_show_theme_toggle', true ),
		'navHide'         => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_nav_hide', true ),
		'toc'             => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_show_toc', true ),
		'copyText'        => $i18n['copy'],
		'copiedText'      => $i18n['copied'],
		'failedText'      => $i18n['copyFailed'],
		'homeUrl'         => home_url( '/' ),
		'loaderTexts'     => array(
			'zh_CN' => '未来有你...',
			'en_US' => 'Loading...',
		),
		'firework'        => yneko_reimu_feature_enabled( 'yneko_reimu_firework_enable', false ),
		'customCursor'    => $custom_cursor,
		'pjax'            => yneko_reimu_feature_enabled( 'yneko_reimu_pjax_enable', false ),
		'katex'           => yneko_reimu_feature_enabled( 'yneko_reimu_katex_enable', false ),
		'photoswipe'      => yneko_reimu_feature_enabled( 'yneko_reimu_photoswipe_enable', false ),
		'mermaid'         => yneko_reimu_feature_enabled( 'yneko_reimu_mermaid_enable', false ),
		'search'          => $search,
		'searchHint'      => $i18n['searchHint'],
		'searchingText'   => $i18n['searching'],
		'searchStatsText' => $i18n['searchStats'],
		'searchEmptyText' => $i18n['searchEmpty'],
		'expandText'      => $i18n['expandCode'],
		'codeExpandThreshold' => absint( yneko_reimu_get_theme_mod( 'yneko_reimu_code_expand_threshold', 420 ) ),
		'login'           => array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'yneko_reimu_ajax_login' ),
			'registerNonce' => wp_create_nonce( 'yneko_reimu_ajax_register' ),
			'registerCodeNonce' => wp_create_nonce( 'yneko_reimu_ajax_register_code' ),
			'lostNonce'   => wp_create_nonce( 'yneko_reimu_ajax_lostpassword' ),
			'lostCodeNonce' => wp_create_nonce( 'yneko_reimu_ajax_lostpassword_code' ),
			'profileNonce'=> wp_create_nonce( 'yneko_reimu_profile' ),
			'logoutNonce' => wp_create_nonce( 'yneko_reimu_ajax_logout' ),
			'redirectUrl' => home_url( add_query_arg( null, null ) ),
			'loadingText' => $i18n['loginLoading'],
			'successText' => $i18n['loginSuccess'],
			'failedText'  => $i18n['loginFailed'],
		),
		'commentUploads'  => array(
			'enabled'    => function_exists( 'yneko_reimu_comment_upload_enabled' ) ? yneko_reimu_comment_upload_enabled() : true,
			'imageEnabled'=> function_exists( 'yneko_reimu_comment_upload_type_enabled' ) ? yneko_reimu_comment_upload_type_enabled( 'image' ) : true,
			'gifEnabled' => function_exists( 'yneko_reimu_comment_upload_type_enabled' ) ? yneko_reimu_comment_upload_type_enabled( 'gif' ) : true,
			'isLoggedIn' => is_user_logged_in(),
			'nonce'      => wp_create_nonce( 'yneko_reimu_comment_upload' ),
			'gifs'       => function_exists( 'yneko_reimu_comment_gif_library' ) ? yneko_reimu_comment_gif_library( 24, false ) : array(),
		),
		'comments'        => array(
			'nonce' => wp_create_nonce( 'yneko_reimu_submit_comment' ),
		),
		'aplayer'         => array(
			'audio'    => $aplayer_audio,
			'fixed'    => '1' === (string) ( $player_settings['fixed'] ?? '0' ),
			'autoplay' => '1' === (string) ( $player_settings['autoplay'] ?? '0' ),
			'loop'     => $player_settings['loop'] ?? 'all',
			'order'    => $player_settings['order'] ?? 'list',
			'preload'  => $player_settings['preload'] ?? 'metadata',
			'volume'   => (float) ( $player_settings['volume'] ?? '0.7' ),
			'mutex'    => '1' === (string) ( $player_settings['mutex'] ?? '1' ),
			'listFolded' => '1' === (string) ( $player_settings['list_folded'] ?? '1' ),
			'listMaxHeight' => $player_settings['list_max_height'] ?? '320px',
			'lrcType'    => absint( $player_settings['lrc_type'] ?? 3 ),
		),
	);
	if ( yneko_reimu_feature_enabled( 'yneko_reimu_firework_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-firework', yneko_reimu_vendor_url( 'mouse-firework@0.2.0/dist/index.umd.js' ), array(), '0.2.0', true );
		$main_script_deps[] = 'yneko-reimu-firework';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_busuanzi_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-busuanzi', yneko_reimu_vendor_url( 'busuanzi@2.3.0/bsz.pure.mini.js' ), array(), '2.3.0', true );
	}

	if ( $enable_aplayer ) {
		wp_enqueue_script( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.js' ), array(), '1.10.1', true );
		$main_script_deps[] = 'yneko-reimu-aplayer';
	}

	if ( yneko_reimu_meting_enabled() ) {
		wp_enqueue_script( 'yneko-reimu-meting', yneko_reimu_vendor_url( 'meting@2.0.1/dist/Meting.min.js' ), array( 'yneko-reimu-aplayer' ), '2.0.1', true );
		$main_script_deps[] = 'yneko-reimu-meting';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_live2d_widgets_enable', false ) ) {
		$live2d_base = yneko_reimu_live2d_base_url();
		wp_enqueue_style( 'yneko-reimu-live2d', $live2d_base . 'waifu.css', array(), '0.9.0' );
		wp_add_inline_style(
			'yneko-reimu-live2d',
			'#waifu{left:auto!important;right:0!important;z-index:99}#waifu-toggle{left:auto!important;right:0!important;margin-left:0!important;margin-right:-100px;padding:5px 5px 5px 2px;transition:margin-right 1s}#waifu-toggle.waifu-toggle-active{margin-right:-50px!important}#waifu-toggle.waifu-toggle-active:hover{margin-right:-30px!important}#waifu-tips{right:0;margin:-30px 20px 0 0}#waifu-tool{right:auto!important;left:-10px!important}'
		);
		wp_enqueue_script( 'yneko-reimu-live2d-core', $live2d_base . 'live2d.min.js', array(), '0.9.0', true );
		wp_enqueue_script( 'yneko-reimu-live2d-widget', $live2d_base . 'waifu-tips.js', array( 'yneko-reimu-live2d-core' ), '0.9.0', true );
		wp_add_inline_script(
			'yneko-reimu-live2d-widget',
			'window.addEventListener("load",function(){if(window.innerWidth<768||typeof window.initWidget!=="function"){return;}window.initWidget({waifuPath:"' . esc_js( $live2d_base . 'waifu-tips.json' ) . '",cdnPath:"' . esc_js( yneko_reimu_live2d_api_base_url() ) . '",tools:["hitokoto","switch-model","switch-texture","photo","info","quit"]});});'
		);
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_katex_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.css' ), array(), '0.16.24' );
		wp_enqueue_script( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.js' ), array(), '0.16.24', true );
		wp_enqueue_script( 'yneko-reimu-katex-auto', yneko_reimu_vendor_url( 'katex@0.16.24/dist/contrib/auto-render.min.js' ), array( 'yneko-reimu-katex' ), '0.16.24', true );
		$main_script_deps[] = 'yneko-reimu-katex-auto';
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_photoswipe_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-photoswipe', yneko_reimu_vendor_url( 'photoswipe@5.4.4/dist/photoswipe.css' ), array(), '5.4.4' );
	}

	if ( yneko_reimu_feature_enabled( 'yneko_reimu_mermaid_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-mermaid', yneko_reimu_vendor_url( 'mermaid@11.12.0/dist/mermaid.min.js' ), array(), '11.12.0', true );
		$main_script_deps[] = 'yneko-reimu-mermaid';
	}

	if ( ! empty( $external_comments['waline_enable'] ) && '1' === (string) $external_comments['waline_enable'] && ! empty( $external_comments['waline_server_url'] ) ) {
		wp_enqueue_style( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.css' ), array(), '2.15.8' );
		wp_enqueue_script( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.js' ), array(), '2.15.8', true );
	}

	if ( ! empty( $external_comments['twikoo_enable'] ) && '1' === (string) $external_comments['twikoo_enable'] && ! empty( $external_comments['twikoo_env_id'] ) ) {
		wp_enqueue_script( 'yneko-reimu-twikoo', yneko_reimu_vendor_url( 'twikoo@1.6.42/dist/twikoo.all.min.js' ), array(), '1.6.42', true );
	}

	if ( ! empty( $external_comments['valine_enable'] ) && '1' === (string) $external_comments['valine_enable'] && ! empty( $external_comments['valine_app_id'] ) && ! empty( $external_comments['valine_app_key'] ) ) {
		wp_enqueue_script( 'yneko-reimu-valine', yneko_reimu_vendor_url( 'valine@1.5.3/dist/Valine.min.js' ), array(), '1.5.3', true );
	}

	wp_enqueue_script( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.js', array_values( array_unique( $main_script_deps ) ), yneko_reimu_asset_version( 'assets/dist/reimu.js' ), true );
	if ( function_exists( 'wp_script_add_data' ) && ! empty( $asset_strategy['script_strategy'] ) ) {
		wp_script_add_data( 'yneko-reimu-main', 'strategy', sanitize_key( $asset_strategy['script_strategy'] ) );
	}
	wp_add_inline_script( 'yneko-reimu-main', 'window.REIMU_CONFIG=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'yneko_reimu_enqueue_assets' );

function yneko_reimu_favicon() {
	$settings         = function_exists( 'yneko_reimu_settings' ) ? yneko_reimu_settings() : array();
	$fallback         = yneko_reimu_normalize_png_jpeg_url( $settings['favicon_fallback_url'] ?? '' );
	$fallback_type    = '';
	$fallback_version = '';

	if ( $fallback ) {
		$path = (string) wp_parse_url( $fallback, PHP_URL_PATH );
		$fallback_type = preg_match( '/\.png$/i', $path ) ? 'image/png' : 'image/jpeg';
		$fallback_version = $fallback . ( false === strpos( $fallback, '?' ) ? '?' : '&' ) . 'yneko-reimu-fallback=1';
	}

	if ( has_site_icon() && $fallback ) {
		remove_action( 'wp_head', 'wp_site_icon', 99 );

		$site_icon_id = absint( get_option( 'site_icon', 0 ) );
		$site_icon    = $site_icon_id ? wp_get_attachment_url( $site_icon_id ) : get_site_icon_url( 192 );
		$site_icon_mime = $site_icon_id ? get_post_mime_type( $site_icon_id ) : '';

		if ( $site_icon ) {
			$type_attr = $site_icon_mime ? ' type="' . esc_attr( $site_icon_mime ) . '"' : '';
			echo '<link rel="icon"' . $type_attr . ' href="' . esc_url( $site_icon ) . '" sizes="32x32">' . "\n";
			echo '<link rel="icon"' . $type_attr . ' href="' . esc_url( $site_icon ) . '" sizes="192x192">' . "\n";
		}

		echo '<link rel="icon" type="' . esc_attr( $fallback_type ) . '" href="' . esc_url( $fallback_version ) . '" sizes="32x32">' . "\n";
		echo '<link rel="icon" type="' . esc_attr( $fallback_type ) . '" href="' . esc_url( $fallback_version ) . '" sizes="192x192">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $fallback_version ) . '">' . "\n";
		return;
	}

	if ( has_site_icon() ) {
		return;
	}

	if ( ! has_site_icon() ) {
		$site_logo = function_exists( 'yneko_reimu_get_site_logo_url' ) ? yneko_reimu_get_site_logo_url() : '';
		$favicon   = $site_logo ? $site_logo : YNEKO_REIMU_URI . '/assets/images/avatar.svg';

		echo '<link rel="icon" href="' . esc_url( $favicon ) . '">' . "\n";
		echo '<link rel="shortcut icon" href="' . esc_url( $favicon ) . '">' . "\n";
	}

	if ( $fallback ) {
		echo '<link rel="icon" type="' . esc_attr( $fallback_type ) . '" href="' . esc_url( $fallback_version ) . '" sizes="32x32">' . "\n";
		echo '<link rel="icon" type="' . esc_attr( $fallback_type ) . '" href="' . esc_url( $fallback_version ) . '" sizes="192x192">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $fallback_version ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'yneko_reimu_favicon', 5 );

function yneko_reimu_front_matter_meta() {
	if ( function_exists( 'yneko_reimu_should_output_theme_meta' ) && ! yneko_reimu_should_output_theme_meta() ) {
		return;
	}

	$post_id     = is_singular() ? get_queried_object_id() : 0;
	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$image       = function_exists( 'yneko_reimu_get_site_logo_url' ) ? yneko_reimu_get_site_logo_url() : '';
	$url         = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	$keywords    = '';

	if ( $post_id ) {
		$keywords = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_keywords', true );
		$summary  = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_summary', true );

		if ( $summary ) {
			$description = wp_strip_all_tags( $summary );
		} elseif ( has_excerpt( $post_id ) ) {
			$description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		}

		if ( has_post_thumbnail( $post_id ) ) {
			$post_image = get_the_post_thumbnail_url( $post_id, 'full' );
			if ( $post_image ) {
				$image = $post_image;
			}
		}

		$url = get_permalink( $post_id );
	}

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $keywords ) {
		echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
	}

	if ( $title ) {
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	}
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta property="og:type" content="' . esc_attr( $post_id ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'yneko_reimu_front_matter_meta', 6 );

function yneko_reimu_preload_theme_script() {
	$default = esc_js( yneko_reimu_get_theme_mod( 'yneko_reimu_dark_mode_default', 'auto' ) );
	?>
	<script>
		(function(){try{var s=localStorage.getItem('dark_mode')||'<?php echo $default; ?>';var d=s==='auto'?((window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'):(s==='true'||s==='dark'?'dark':'light');document.documentElement.setAttribute('data-theme',d);}catch(e){}}());
	</script>
	<?php
}
add_action( 'wp_head', 'yneko_reimu_preload_theme_script', 1 );
