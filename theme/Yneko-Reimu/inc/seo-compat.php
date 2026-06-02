<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' )
		|| defined( 'WPSEO_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' )
		|| function_exists( 'rank_math' )
		|| function_exists( 'wpseo_init' )
		|| function_exists( 'aioseo' )
		|| function_exists( 'seopress_init' )
		|| function_exists( 'the_seo_framework' );
}

function yneko_reimu_should_output_theme_meta() {
	return (bool) apply_filters( 'yneko_reimu_output_theme_meta', ! yneko_reimu_seo_plugin_active() );
}

function yneko_reimu_i18n_current_url() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		if ( $post_id && function_exists( 'yneko_reimu_i18n_post_url' ) ) {
			return yneko_reimu_i18n_post_url( $post_id, yneko_reimu_i18n_current_language() );
		}
		return $post_id ? get_permalink( $post_id ) : home_url( '/' );
	}

	$request = isset( $GLOBALS['wp']->request ) ? trim( (string) $GLOBALS['wp']->request, '/' ) : '';
	if ( function_exists( 'yneko_reimu_i18n_is_english_request' ) && yneko_reimu_i18n_is_english_request() && function_exists( 'yneko_reimu_i18n_prefixed_url' ) && function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ) {
		return yneko_reimu_i18n_prefixed_url( yneko_reimu_i18n_relative_without_prefix( $request ) );
	}

	return $request ? home_url( user_trailingslashit( $request ) ) : home_url( '/' );
}

function yneko_reimu_i18n_alternate_urls() {
	if ( ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() ) {
		return array();
	}

	$urls = array();
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			return array();
		}

		$zh_id = yneko_reimu_i18n_get_translation_id( $post_id, 'zh_CN' );
		$en_id = yneko_reimu_i18n_get_translation_id( $post_id, 'en_US' );
		if ( $zh_id && 'publish' === get_post_status( $zh_id ) ) {
			$urls['zh-CN'] = yneko_reimu_i18n_post_url( $zh_id, 'zh_CN' );
		}
		if ( $en_id && 'publish' === get_post_status( $en_id ) ) {
			$urls['en'] = yneko_reimu_i18n_post_url( $en_id, 'en_US' );
		}
	} else {
		$path = function_exists( 'yneko_reimu_i18n_request_path' ) ? yneko_reimu_i18n_request_path() : '';
		$path = yneko_reimu_i18n_relative_without_prefix( $path );
		if ( '' === $path ) {
			$urls['zh-CN'] = yneko_reimu_i18n_home_url( 'zh_CN' );
			$urls['en']    = yneko_reimu_i18n_home_url( 'en_US' );
		} else {
			$urls['zh-CN'] = home_url( user_trailingslashit( $path ) );
			$urls['en']    = yneko_reimu_i18n_prefixed_url( $path );
		}
	}

	if ( isset( $urls['zh-CN'] ) ) {
		$urls['x-default'] = $urls['zh-CN'];
	}

	return array_filter( array_map( 'esc_url_raw', $urls ) );
}

function yneko_reimu_i18n_hreflang_links() {
	$urls = yneko_reimu_i18n_alternate_urls();
	if ( empty( $urls['zh-CN'] ) || empty( $urls['en'] ) ) {
		return;
	}

	foreach ( $urls as $hreflang => $url ) {
		echo '<link rel="alternate" hreflang="' . esc_attr( $hreflang ) . '" href="' . esc_url( $url ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'yneko_reimu_i18n_hreflang_links', 4 );

function yneko_reimu_rank_math_canonical( $canonical ) {
	if ( function_exists( 'yneko_reimu_i18n_is_english_request' ) && yneko_reimu_i18n_is_english_request() ) {
		if ( is_singular() ) {
			return yneko_reimu_i18n_post_url( get_queried_object_id(), 'en_US' );
		}
		return yneko_reimu_i18n_current_url();
	}

	return $canonical;
}
add_filter( 'rank_math/frontend/canonical', 'yneko_reimu_rank_math_canonical', 20 );

function yneko_reimu_rank_math_opengraph_url( $url ) {
	return yneko_reimu_rank_math_canonical( $url );
}
add_filter( 'rank_math/opengraph/url', 'yneko_reimu_rank_math_opengraph_url', 20 );

function yneko_reimu_i18n_is_english_post_for_seo( $post ) {
	$post = $post instanceof WP_Post ? $post : get_post( $post );
	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	if ( function_exists( 'yneko_reimu_i18n_post_language' ) && 'en_US' === yneko_reimu_i18n_post_language( $post->ID ) ) {
		return true;
	}

	return (bool) preg_match( '/-en$/i', (string) $post->post_name );
}

function yneko_reimu_rank_math_sitemap_post_url( $url, $post ) {
	if ( yneko_reimu_i18n_is_english_post_for_seo( $post ) ) {
		return yneko_reimu_i18n_post_url( $post->ID, 'en_US' );
	}

	return $url;
}
add_filter( 'rank_math/sitemap/xml_post_url', 'yneko_reimu_rank_math_sitemap_post_url', 20, 2 );

function yneko_reimu_rank_math_sitemap_entry( $url, $type, $object ) {
	if ( is_array( $url ) && yneko_reimu_i18n_is_english_post_for_seo( $object ) ) {
		$url['loc'] = yneko_reimu_i18n_post_url( $object->ID, 'en_US' );
	}

	return $url;
}
add_filter( 'rank_math/sitemap/entry', 'yneko_reimu_rank_math_sitemap_entry', 20, 3 );

function yneko_reimu_rank_math_extra_sitemap_url_markup() {
	if ( ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() ) {
		return '';
	}

	return sprintf(
		'<url><loc>%1$s</loc><lastmod>%2$s</lastmod></url>',
		esc_url( yneko_reimu_i18n_home_url( 'en_US' ) ),
		esc_html( gmdate( DATE_W3C ) )
	);
}

function yneko_reimu_rank_math_page_sitemap_content( $content ) {
	$extra = yneko_reimu_rank_math_extra_sitemap_url_markup();
	if ( '' === $extra ) {
		return $content;
	}

	return (string) $content . "\n" . $extra;
}
add_filter( 'rank_math/sitemap/page_content', 'yneko_reimu_rank_math_page_sitemap_content', 20, 1 );

function yneko_reimu_rank_math_invalidate_sitemap_cache() {
	if ( class_exists( '\RankMath\Sitemap\Cache' ) && method_exists( '\RankMath\Sitemap\Cache', 'invalidate_storage' ) ) {
		\RankMath\Sitemap\Cache::invalidate_storage( 'post' );
		\RankMath\Sitemap\Cache::invalidate_storage( 'page' );
	}
}
add_action( 'after_switch_theme', 'yneko_reimu_rank_math_invalidate_sitemap_cache' );
add_action( 'customize_save_after', 'yneko_reimu_rank_math_invalidate_sitemap_cache' );
add_action( 'update_option_yneko_reimu_settings', 'yneko_reimu_rank_math_invalidate_sitemap_cache' );
