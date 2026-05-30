<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_vendor_base_url() {
	$base = yneko_reimu_get_theme_mod( 'yneko_reimu_vendor_cdn_base', 'https://npm.webcache.cn' );
	$base = esc_url_raw( trim( (string) $base ) );

	return $base ? untrailingslashit( $base ) : 'https://npm.webcache.cn';
}

function yneko_reimu_vendor_url( $package_path ) {
	return yneko_reimu_vendor_base_url() . '/' . ltrim( $package_path, '/' );
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

function yneko_reimu_cursor_variables_css() {
	if ( ! yneko_reimu_get_theme_mod( 'yneko_reimu_custom_cursor', true ) ) {
		return ':root{--cursor-default:auto;--cursor-pointer:pointer;--cursor-text:text;--cursor-busy:wait;--cursor-progress:progress;--cursor-not-allowed:not-allowed;--cursor-help:help;--cursor-move:move;--cursor-grab:grab;--cursor-grabbing:grabbing;--cursor-crosshair:crosshair;--cursor-ew-resize:ew-resize;--cursor-ns-resize:ns-resize;--cursor-nwse-resize:nwse-resize;--cursor-nesw-resize:nesw-resize;--cursor-alias:alias;}';
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
	?>
	<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_custom_cursor', true ) ) : ?>
		<link rel="preload" as="image" href="<?php echo esc_url( $cursor_base . 'lily-normal.png' ); ?>">
		<link rel="preload" as="image" href="<?php echo esc_url( $cursor_base . 'lily-link.png' ); ?>">
		<link rel="preload" as="image" href="<?php echo esc_url( $cursor_base . 'lily-text.png' ); ?>">
		<link rel="preload" as="image" href="<?php echo esc_url( $cursor_base . 'lily-work.png' ); ?>">
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
	wp_enqueue_style( 'yneko-reimu-theme', get_stylesheet_uri(), array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-fonts', 'https://fonts.googleapis.com/css?family=Mulish:400,400italic,700,700italic|Noto+Serif+SC:400,400italic,700,700italic&display=swap', array(), null );
	wp_enqueue_style( 'yneko-reimu-loader', YNEKO_REIMU_URI . '/assets/dist/loader.css', array( 'yneko-reimu-theme' ), YNEKO_REIMU_VERSION );
	$aplayer_audio  = yneko_reimu_normalize_aplayer_audio( yneko_reimu_settings_music_items() );
	$enable_aplayer = ( yneko_reimu_get_theme_mod( 'yneko_reimu_player_aplayer_enable', true ) && ! empty( $aplayer_audio ) ) || yneko_reimu_get_theme_mod( 'yneko_reimu_player_meting_enable', false );
	if ( $enable_aplayer ) {
		wp_enqueue_style( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.css' ), array(), '1.10.1' );
	}
	$main_style_deps = array( 'yneko-reimu-theme', 'yneko-reimu-loader', 'yneko-reimu-fonts' );
	if ( $enable_aplayer ) {
		$main_style_deps[] = 'yneko-reimu-aplayer';
	}
	wp_enqueue_style( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.css', $main_style_deps, YNEKO_REIMU_VERSION );

	$accent = sanitize_hex_color( yneko_reimu_get_theme_mod( 'yneko_reimu_accent_color', '#ff5252' ) );
	$accent = $accent ? $accent : '#ff5252';
	wp_add_inline_style( 'yneko-reimu-main', ':root{--red-1:' . esc_html( $accent ) . ';--color-link:' . esc_html( $accent ) . ';}' );
	if ( ! yneko_reimu_get_theme_mod( 'yneko_reimu_custom_cursor', true ) ) {
		wp_add_inline_style( 'yneko-reimu-main', yneko_reimu_cursor_variables_css() );
	}
	if ( ! yneko_reimu_get_theme_mod( 'yneko_reimu_sticky_nav', true ) ) {
		wp_add_inline_style( 'yneko-reimu-main', '#header-nav{position:absolute;}' );
	}

	wp_enqueue_script( 'yneko-reimu-main', YNEKO_REIMU_URI . '/assets/dist/reimu.js', array(), YNEKO_REIMU_VERSION, true );

	$current_language   = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : get_locale();
	$builtin_search_url = function_exists( 'yneko_reimu_search_json_url' ) ? yneko_reimu_search_json_url( $current_language ) : '';
	$local_search_url   = yneko_reimu_get_theme_mod( 'yneko_reimu_local_search_json', '' );
	$local_search_url   = $local_search_url ? $local_search_url : $builtin_search_url;
	$search             = array(
		'type'    => 'wordpress',
		'restUrl' => esc_url_raw( rest_url( 'wp/v2/search' ) ),
		'perPage' => 10,
		'language' => $current_language,
	);

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_generator_search_enable', true ) && $local_search_url ) {
		$search = array(
			'type'     => 'local',
			'localUrl' => esc_url_raw( $local_search_url ),
			'perPage'  => 10,
			'language' => $current_language,
		);
	} elseif ( yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_app_id', '' ) && yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_api_key', '' ) && yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_index_name', '' ) ) {
		$search = array(
			'type'      => 'algolia',
			'appId'     => yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_app_id', '' ),
			'apiKey'    => yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_api_key', '' ),
			'indexName' => yneko_reimu_get_theme_mod( 'yneko_reimu_algolia_index_name', '' ),
			'perPage'   => 10,
		);
		wp_enqueue_script( 'yneko-reimu-algoliasearch', yneko_reimu_vendor_url( 'algoliasearch@4.24.0/dist/algoliasearch-lite.umd.js' ), array(), '4.24.0', true );
	}

	$custom_cursor = (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_custom_cursor', true );
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
		'cancelReply'           => esc_html__( '取消回复', 'yneko-reimu' ),
		'replyComment'          => esc_html__( '回复评论', 'yneko-reimu' ),
		'loginLoading'          => esc_html__( '登录中...', 'yneko-reimu' ),
		'loginSuccess'          => esc_html__( '登录成功，正在刷新...', 'yneko-reimu' ),
		'loginFailed'           => esc_html__( '登录失败，请检查账号和密码。', 'yneko-reimu' ),
	);
	$config = array(
		'language'        => $current_language,
		'i18n'            => $i18n,
		'darkModeDefault' => yneko_reimu_get_theme_mod( 'yneko_reimu_dark_mode_default', 'auto' ),
		'showThemeToggle' => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_show_theme_toggle', true ),
		'navHide'         => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_nav_hide', true ),
		'toc'             => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_show_toc', true ),
		'copyText'        => $i18n['copy'],
		'copiedText'      => $i18n['copied'],
		'failedText'      => $i18n['copyFailed'],
		'firework'        => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_firework_enable', true ),
		'customCursor'    => $custom_cursor,
		'pjax'            => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_pjax_enable', true ),
		'katex'           => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_katex_enable', false ),
		'mermaid'         => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_mermaid_enable', false ),
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
			'redirectUrl' => home_url( add_query_arg( null, null ) ),
			'loadingText' => $i18n['loginLoading'],
			'successText' => $i18n['loginSuccess'],
			'failedText'  => $i18n['loginFailed'],
		),
		'aplayer'         => array(
			'audio'    => $aplayer_audio,
			'fixed'    => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_fixed', false ),
			'autoplay' => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_autoplay', false ),
			'loop'     => yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_loop', 'all' ),
			'order'    => yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_order', 'list' ),
			'preload'  => yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_preload', 'auto' ),
			'volume'   => (float) yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_volume', '0.7' ),
			'mutex'    => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_mutex', true ),
			'listFolded' => (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_list_folded', true ),
			'listMaxHeight' => yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_list_max_height', '320px' ),
			'lrcType'    => absint( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_lrc_type', 3 ) ),
		),
	);
	wp_add_inline_script( 'yneko-reimu-main', 'window.REIMU_CONFIG=' . wp_json_encode( $config ) . ';', 'before' );

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_firework_enable', true ) ) {
		wp_enqueue_script( 'yneko-reimu-firework', yneko_reimu_vendor_url( 'mouse-firework@0.2.0/dist/index.umd.js' ), array(), '0.2.0', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_busuanzi_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-busuanzi', yneko_reimu_vendor_url( 'busuanzi@2.3.0/bsz.pure.mini.js' ), array(), '2.3.0', true );
	}

	if ( $enable_aplayer ) {
		wp_enqueue_script( 'yneko-reimu-aplayer', yneko_reimu_vendor_url( 'aplayer@1.10.1/dist/APlayer.min.js' ), array(), '1.10.1', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_player_meting_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-meting', yneko_reimu_vendor_url( 'meting@2.0.1/dist/Meting.min.js' ), array( 'yneko-reimu-aplayer' ), '2.0.1', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_live2d_widgets_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-live2d', yneko_reimu_vendor_url( 'live2d-widgets@0.9.0/autoload.js' ), array(), '0.9.0', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_katex_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.css' ), array(), '0.16.24' );
		wp_enqueue_script( 'yneko-reimu-katex', yneko_reimu_vendor_url( 'katex@0.16.24/dist/katex.min.js' ), array(), '0.16.24', true );
		wp_enqueue_script( 'yneko-reimu-katex-auto', yneko_reimu_vendor_url( 'katex@0.16.24/dist/contrib/auto-render.min.js' ), array( 'yneko-reimu-katex' ), '0.16.24', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_photoswipe_enable', false ) ) {
		wp_enqueue_style( 'yneko-reimu-photoswipe', yneko_reimu_vendor_url( 'photoswipe@5.4.4/dist/photoswipe.css' ), array(), '5.4.4' );
		if ( function_exists( 'wp_enqueue_script_module' ) ) {
			wp_enqueue_script_module( 'yneko-reimu-photoswipe-lightbox', yneko_reimu_vendor_url( 'photoswipe@5.4.4/dist/photoswipe-lightbox.esm.min.js' ), array(), '5.4.4' );
		}
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_mermaid_enable', false ) ) {
		wp_enqueue_script( 'yneko-reimu-mermaid', yneko_reimu_vendor_url( 'mermaid@11.12.0/dist/mermaid.min.js' ), array(), '11.12.0', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_waline_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_waline_server_url', '' ) ) {
		wp_enqueue_style( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.css' ), array(), '2.15.8' );
		wp_enqueue_script( 'yneko-reimu-waline', yneko_reimu_vendor_url( '@waline/client@2.15.8/dist/waline.umd.js' ), array(), '2.15.8', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_twikoo_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_twikoo_env_id', '' ) ) {
		wp_enqueue_script( 'yneko-reimu-twikoo', yneko_reimu_vendor_url( 'twikoo@1.6.42/dist/twikoo.all.min.js' ), array(), '1.6.42', true );
	}

	if ( yneko_reimu_get_theme_mod( 'yneko_reimu_valine_enable', false ) && yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_id', '' ) && yneko_reimu_get_theme_mod( 'yneko_reimu_valine_app_key', '' ) ) {
		wp_enqueue_script( 'yneko-reimu-valine', yneko_reimu_vendor_url( 'valine@1.5.3/dist/Valine.min.js' ), array(), '1.5.3', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'yneko_reimu_enqueue_assets' );

function yneko_reimu_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	$site_logo = function_exists( 'yneko_reimu_get_site_logo_url' ) ? yneko_reimu_get_site_logo_url() : '';
	$favicon   = $site_logo ? $site_logo : YNEKO_REIMU_URI . '/assets/images/avatar.svg';

	echo '<link rel="icon" href="' . esc_url( $favicon ) . '">' . "\n";
	echo '<link rel="shortcut icon" href="' . esc_url( $favicon ) . '">' . "\n";
}
add_action( 'wp_head', 'yneko_reimu_favicon', 5 );

function yneko_reimu_front_matter_meta() {
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
