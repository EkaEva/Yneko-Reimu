<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_build_search_config( $current_language, $search_settings ) {
	$builtin_search_url = function_exists( 'yneko_reimu_search_json_url' ) ? yneko_reimu_search_json_url( $current_language ) : '';
	$local_search_url   = $search_settings['local_json_url'] ?? yneko_reimu_get_theme_mod( 'yneko_reimu_local_search_json', '' );
	$local_search_url   = $local_search_url ? $local_search_url : $builtin_search_url;
	$main_script_deps   = array();
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

	return array(
		'search' => $search,
		'deps'   => $main_script_deps,
	);
}

function yneko_reimu_frontend_i18n() {
	return array(
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
		/* translators: %s: remaining seconds. */
		'registerCodeWait'      => esc_html__( '%s 秒后重发', 'yneko-reimu' ),
		'profile2faGenerated'   => esc_html__( '请用认证器扫码，并输入 6 位验证码后保存。', 'yneko-reimu' ),
		'imagePreview'          => esc_html__( '图片预览', 'yneko-reimu' ),
		'closePreview'          => esc_html__( '关闭预览', 'yneko-reimu' ),
		'previousImage'         => esc_html__( '上一张图片', 'yneko-reimu' ),
		'nextImage'             => esc_html__( '下一张图片', 'yneko-reimu' ),
	);
}

function yneko_reimu_build_frontend_config( $current_language, $search, $aplayer_audio, $player_settings ) {
	$i18n          = yneko_reimu_frontend_i18n();
	$custom_cursor = yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false );

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
		'loaderTexts'     => function_exists( 'yneko_reimu_preloader_texts' ) ? yneko_reimu_preloader_texts() : array(
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

	return $config;
}
