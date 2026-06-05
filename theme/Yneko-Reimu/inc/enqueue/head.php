<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_cursor_variables_css() {
	if ( ! yneko_reimu_feature_enabled( 'yneko_reimu_custom_cursor', false ) ) {
		return ':root{--cursor-default:auto;--cursor-pointer:pointer;--cursor-text:text;--cursor-busy:wait;--cursor-progress:progress;--cursor-not-allowed:not-allowed;--cursor-help:help;--cursor-move:move;--cursor-grab:grab;--cursor-grabbing:grabbing;--cursor-zoom-in:zoom-in;--cursor-crosshair:crosshair;--cursor-ew-resize:ew-resize;--cursor-ns-resize:ns-resize;--cursor-nwse-resize:nwse-resize;--cursor-nesw-resize:nesw-resize;--cursor-alias:alias;}';
	}

	$base = YNEKO_REIMU_URI . '/assets/images/cursor/';
	$default_cursor  = yneko_reimu_visual_asset_url( 'yneko_reimu_cursor_default_url', $base . 'lily-normal.png' );
	$pointer_cursor  = yneko_reimu_visual_asset_url( 'yneko_reimu_cursor_pointer_url', $base . 'lily-link.png' );
	$text_cursor     = yneko_reimu_visual_asset_url( 'yneko_reimu_cursor_text_url', $base . 'lily-text.png' );
	$progress_cursor = yneko_reimu_visual_asset_url( 'yneko_reimu_cursor_progress_url', $base . 'lily-work.png' );

	return ':root{' .
		'--cursor-default:' . yneko_reimu_cursor_css_value( $default_cursor, 'auto' ) . ';' .
		'--cursor-pointer:' . yneko_reimu_cursor_css_value( $pointer_cursor, 'pointer' ) . ';' .
		'--cursor-text:' . yneko_reimu_cursor_css_value( $text_cursor, 'text' ) . ';' .
		'--cursor-busy:' . yneko_reimu_cursor_css_value( $progress_cursor, 'wait' ) . ';' .
		'--cursor-progress:' . yneko_reimu_cursor_css_value( $progress_cursor, 'progress' ) . ';' .
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

function yneko_reimu_visual_asset_url( $theme_mod_key, $fallback = '' ) {
	$url = yneko_reimu_get_theme_mod( $theme_mod_key, '' );
	$url = is_string( $url ) ? trim( $url ) : '';

	return '' !== $url ? esc_url_raw( $url ) : $fallback;
}

function yneko_reimu_cursor_css_value( $url, $fallback_cursor ) {
	$url = esc_url( $url );
	if ( '' === $url ) {
		return $fallback_cursor;
	}

	return 'url("' . str_replace( '"', '%22', $url ) . '"), ' . $fallback_cursor;
}

function yneko_reimu_preloader_image_size() {
	$size = yneko_reimu_get_theme_mod( 'yneko_reimu_preloader_image_size', 150 );
	return function_exists( 'yneko_reimu_sanitize_preloader_image_size' ) ? yneko_reimu_sanitize_preloader_image_size( $size ) : max( 48, min( 320, absint( $size ) ) );
}

function yneko_reimu_preloader_texts() {
	$legacy_zh = yneko_reimu_get_theme_mod( 'yneko_reimu_preloader_text', __( '未来有你...', 'yneko-reimu' ) );
	if ( __( '少女祈祷中...', 'yneko-reimu' ) === $legacy_zh || '少女祈祷中...' === $legacy_zh ) {
		$legacy_zh = __( '未来有你...', 'yneko-reimu' );
	}

	$zh = yneko_reimu_get_theme_mod( 'yneko_reimu_preloader_text_zh', $legacy_zh );
	$en = yneko_reimu_get_theme_mod( 'yneko_reimu_preloader_text_en', 'Loading...' );

	return array(
		'zh_CN' => '' !== trim( (string) $zh ) ? (string) $zh : __( '未来有你...', 'yneko-reimu' ),
		'en_US' => '' !== trim( (string) $en ) ? (string) $en : 'Loading...',
	);
}

function yneko_reimu_visual_asset_variables_css() {
	$declarations = array(
		'--reimu-loader-asset-size:' . yneko_reimu_preloader_image_size() . 'px',
	);

	foreach (
		array(
			'yneko_reimu_top_icon_url'     => '--top-icon',
			'yneko_reimu_sponsor_icon_url' => '--sponsor-icon',
		) as $theme_mod_key => $css_var
	) {
		$url = yneko_reimu_visual_asset_url( $theme_mod_key, '' );
		if ( '' !== $url ) {
			$declarations[] = $css_var . ':url("' . str_replace( '"', '%22', esc_url( $url ) ) . '")';
		}
	}

	return ':root{' . implode( ';', $declarations ) . ';}';
}

function yneko_reimu_typography_font_stack( $key, $default_stack ) {
	$stacks = array(
		'default'  => $default_stack,
		'system'   => '-apple-system,BlinkMacSystemFont,"Segoe UI",PingFang SC,Microsoft YaHei,sans-serif',
		'serif'    => 'Noto Serif SC,Songti SC,SimSun,serif',
		'rounded'  => 'LXGW WenKai,PingFang SC,Microsoft YaHei,sans-serif',
		'mono'     => 'Consolas,Monaco,Menlo,monospace',
		'wenkai'   => 'LXGW WenKai,-apple-system,PingFang SC,Microsoft YaHei,sans-serif',
		'notoserif'=> 'Noto Serif SC,Songti SC,SimSun,serif',
	);
	$value = yneko_reimu_get_theme_mod( $key, 'default' );
	$value = is_string( $value ) ? $value : 'default';

	return $stacks[ $value ] ?? $default_stack;
}

function yneko_reimu_typography_layout_css() {
	$body_stack    = yneko_reimu_typography_font_stack( 'yneko_reimu_font_body', 'LXGW WenKai,-apple-system,PingFang SC,Microsoft YaHei,"sans-serif"' );
	$heading_stack = yneko_reimu_typography_font_stack( 'yneko_reimu_font_heading', 'inherit' );
	$code_stack    = yneko_reimu_typography_font_stack( 'yneko_reimu_font_code', 'Consolas,Monaco,Menlo,monospace' );

	$base_font_size       = function_exists( 'yneko_reimu_sanitize_base_font_size' ) ? yneko_reimu_sanitize_base_font_size( yneko_reimu_get_theme_mod( 'yneko_reimu_base_font_size', 16 ) ) : 16;
	$article_font_size    = function_exists( 'yneko_reimu_sanitize_article_font_size' ) ? yneko_reimu_sanitize_article_font_size( yneko_reimu_get_theme_mod( 'yneko_reimu_article_font_size', 16 ) ) : 16;
	$article_line_height  = function_exists( 'yneko_reimu_sanitize_article_line_height' ) ? yneko_reimu_sanitize_article_line_height( yneko_reimu_get_theme_mod( 'yneko_reimu_article_line_height', 167 ) ) : 167;
	$content_max_width    = function_exists( 'yneko_reimu_sanitize_content_max_width' ) ? yneko_reimu_sanitize_content_max_width( yneko_reimu_get_theme_mod( 'yneko_reimu_content_max_width', 1550 ) ) : 1550;
	$article_content_width= function_exists( 'yneko_reimu_sanitize_article_content_width' ) ? yneko_reimu_sanitize_article_content_width( yneko_reimu_get_theme_mod( 'yneko_reimu_article_content_width', 0 ) ) : 0;
	$card_radius          = function_exists( 'yneko_reimu_sanitize_radius_px' ) ? yneko_reimu_sanitize_radius_px( yneko_reimu_get_theme_mod( 'yneko_reimu_card_radius', 12 ) ) : 12;
	$image_radius         = function_exists( 'yneko_reimu_sanitize_radius_px' ) ? yneko_reimu_sanitize_radius_px( yneko_reimu_get_theme_mod( 'yneko_reimu_image_radius', 12 ) ) : 12;
	$density              = yneko_reimu_get_theme_mod( 'yneko_reimu_layout_density', 'default' );
	$shadow_strength      = yneko_reimu_get_theme_mod( 'yneko_reimu_shadow_strength', 'default' );

	$density_values = array(
		'comfortable' => array( 'main' => 24, 'article_x' => 44, 'article_meta_x' => 44, 'article_y' => 20, 'widget' => 48 ),
		'default'     => array( 'main' => 20, 'article_x' => 36, 'article_meta_x' => 36, 'article_y' => 16, 'widget' => 40 ),
		'compact'     => array( 'main' => 14, 'article_x' => 24, 'article_meta_x' => 24, 'article_y' => 12, 'widget' => 28 ),
	);
	$density_vars = $density_values[ $density ] ?? $density_values['default'];

	$shadow_values = array(
		'none'    => array( 'card' => 'none', 'card_hover' => 'none', 'meta' => 'none', 'meta_hover' => 'none' ),
		'soft'    => array( 'card' => '0 0 8px 1px var(--color-hover-shadow)', 'card_hover' => '0 0 9px 2px var(--color-hover-shadow)', 'meta' => '0 0 4px 1px var(--color-meta-shadow)', 'meta_hover' => '0 0 5px 2px var(--color-meta-shadow)' ),
		'default' => array( 'card' => '0 0 10px 2px var(--color-hover-shadow)', 'card_hover' => '0 0 10px 4px var(--color-hover-shadow)', 'meta' => '0 0 5px 2px var(--color-meta-shadow)', 'meta_hover' => '0 0 6px 4px var(--color-meta-shadow)' ),
		'strong'  => array( 'card' => '0 0 14px 4px var(--color-hover-shadow)', 'card_hover' => '0 0 18px 6px var(--color-hover-shadow)', 'meta' => '0 0 8px 3px var(--color-meta-shadow)', 'meta_hover' => '0 0 10px 5px var(--color-meta-shadow)' ),
	);
	$shadow_vars = $shadow_values[ $shadow_strength ] ?? $shadow_values['default'];

	$declarations = array(
		'--reimu-font-body:' . $body_stack,
		'--reimu-font-heading:' . $heading_stack,
		'--reimu-font-code:' . $code_stack,
		'--reimu-base-font-size:' . $base_font_size . 'px',
		'--reimu-article-font-size:' . $article_font_size . 'px',
		'--reimu-article-line-height:' . ( $article_line_height / 100 ),
		'--reimu-content-max-width:' . $content_max_width . 'px',
		'--reimu-article-content-width:' . ( $article_content_width ? $article_content_width . 'px' : 'none' ),
		'--reimu-main-padding:' . $density_vars['main'] . 'px',
		'--reimu-article-padding-x:' . $density_vars['article_x'] . 'px',
		'--reimu-article-meta-padding-x:' . $density_vars['article_meta_x'] . 'px',
		'--reimu-article-block-margin:' . $density_vars['article_y'] . 'px',
		'--reimu-widget-gap:' . $density_vars['widget'] . 'px',
		'--reimu-card-radius:' . $card_radius . 'px',
		'--reimu-image-radius:' . $image_radius . 'px',
		'--shadow-card:' . $shadow_vars['card'],
		'--shadow-card-hover:' . $shadow_vars['card_hover'],
		'--shadow-meta:' . $shadow_vars['meta'],
		'--shadow-meta-hover:' . $shadow_vars['meta_hover'],
	);

	return ':root{' . implode( ';', $declarations ) . ';}' .
		'body{font-family:var(--reimu-font-body);font-size:var(--reimu-base-font-size);}' .
		'.article-entry,.wl-content{font-size:var(--reimu-article-font-size);}' .
		'.article-entry p,.article-entry table,.wl-content p,.wl-content table{line-height:var(--reimu-article-line-height);margin:var(--reimu-article-block-margin) 0;}' .
		'.article-entry ul,.article-entry ol,.article-entry dl,.wl-content ul,.wl-content ol,.wl-content dl{line-height:var(--reimu-article-line-height);}' .
		'h1,h2,h3,h4,h5,h6,#logo,.article-title,.archive-article-title,.widget-title,.toc-title{font-family:var(--reimu-font-heading);}' .
		'code,kbd,pre,.highlight,.article-entry code,.article-entry pre,.reimu-yml-editor{font-family:var(--reimu-font-code);}' .
		'#content{max-width:var(--reimu-content-max-width);}' .
		'#main{padding-left:var(--reimu-main-padding);padding-right:var(--reimu-main-padding);}' .
		'@media screen and (min-width:960px){.sidebar-left #main,.sidebar-right #main{padding-left:0;padding-right:0}}' .
		'.article-entry{padding-left:var(--reimu-article-padding-x);padding-right:var(--reimu-article-padding-x);}' .
		'.article-meta{padding-left:var(--reimu-article-meta-padding-x);padding-right:var(--reimu-article-meta-padding-x);}' .
		'.article-inner,.archive-article,.post-card,.post-link-card,.widget-wrap,.sidebar-wrap,.popup,.custom-block,.article-entry blockquote,.article-entry details{border-radius:var(--reimu-card-radius);}' .
		'.article-entry img,.article-entry video,.article-entry iframe,.article-gallery-item img,.post-card img{border-radius:var(--reimu-image-radius);}' .
		'.widget-wrap{margin-top:var(--reimu-widget-gap);}' .
		'.article-entry{max-width:var(--reimu-article-content-width);margin-left:auto;margin-right:auto;}' .
		'@media screen and (max-width:767px){#main{padding-left:16px;padding-right:16px}.article-entry,.article-meta{padding-left:16px;padding-right:16px}.article-entry{max-width:none}}';
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
		(function(){try{var s=localStorage.getItem('dark_mode')||'<?php echo $default; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';var d=s==='auto'?((window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'):(s==='true'||s==='dark'?'dark':'light');document.documentElement.setAttribute('data-theme',d);}catch(e){}}());
	</script>
	<?php
}
add_action( 'wp_head', 'yneko_reimu_preload_theme_script', 1 );
