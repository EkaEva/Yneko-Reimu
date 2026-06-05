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
