<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_i18n_defaults() {
	return array(
		'enabled'       => '1',
		'default'       => 'zh_CN',
		'en_prefix'     => 'en',
		'zh_label'      => '简体中文',
		'en_label'      => 'English',
	);
}

function yneko_reimu_i18n_settings() {
	$options  = get_option( 'yneko_reimu_settings', array() );
	$options  = is_array( $options ) ? $options : array();
	$settings = isset( $options['i18n'] ) && is_array( $options['i18n'] ) ? $options['i18n'] : array();
	return wp_parse_args( $settings, yneko_reimu_i18n_defaults() );
}

function yneko_reimu_i18n_enabled() {
	$settings = yneko_reimu_i18n_settings();
	return ! empty( $settings['enabled'] );
}

function yneko_reimu_i18n_default_language() {
	$settings = yneko_reimu_i18n_settings();
	return 'en_US' === $settings['default'] ? 'en_US' : 'zh_CN';
}

function yneko_reimu_i18n_languages() {
	$settings = yneko_reimu_i18n_settings();
	return array(
		'zh_CN' => array(
			'code'   => 'zh_CN',
			'slug'   => '',
			'label'  => $settings['zh_label'] ? $settings['zh_label'] : '简体中文',
			'locale' => 'zh_CN',
		),
		'en_US' => array(
			'code'   => 'en_US',
			'slug'   => trim( sanitize_title( $settings['en_prefix'] ? $settings['en_prefix'] : 'en' ), '/' ),
			'label'  => $settings['en_label'] ? $settings['en_label'] : 'English',
			'locale' => 'en_US',
		),
	);
}

function yneko_reimu_i18n_language_exists( $language ) {
	return isset( yneko_reimu_i18n_languages()[ $language ] );
}

function yneko_reimu_i18n_url_prefix() {
	$languages = yneko_reimu_i18n_languages();
	return $languages['en_US']['slug'] ? $languages['en_US']['slug'] : 'en';
}

function yneko_reimu_i18n_request_path() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( rawurldecode( $path ), '/' );

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	} elseif ( $home_path === $path ) {
		$path = '';
	}

	return $path;
}

function yneko_reimu_i18n_current_language() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return yneko_reimu_i18n_default_language();
	}

	if ( isset( $GLOBALS['yneko_reimu_current_language'] ) ) {
		return $GLOBALS['yneko_reimu_current_language'];
	}

	$prefix = yneko_reimu_i18n_url_prefix();
	$path   = yneko_reimu_i18n_request_path();
	$lang   = ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ? 'en_US' : 'zh_CN';

	$GLOBALS['yneko_reimu_current_language'] = $lang;
	return $lang;
}

function yneko_reimu_i18n_is_english_request() {
	return 'en_US' === yneko_reimu_i18n_current_language();
}

function yneko_reimu_i18n_filter_locale( $locale ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $locale;
	}

	return yneko_reimu_i18n_enabled() ? yneko_reimu_i18n_current_language() : $locale;
}
add_filter( 'locale', 'yneko_reimu_i18n_filter_locale', 20 );

function yneko_reimu_i18n_language_attributes( $output ) {
	if ( ! yneko_reimu_i18n_enabled() || is_admin() ) {
		return $output;
	}

	$lang = 'en_US' === yneko_reimu_i18n_current_language() ? 'en-US' : 'zh-CN';
	return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $lang ) . '"', $output );
}
add_filter( 'language_attributes', 'yneko_reimu_i18n_language_attributes', 20 );

function yneko_reimu_i18n_relative_without_prefix( $path ) {
	$path   = trim( (string) $path, '/' );
	$prefix = yneko_reimu_i18n_url_prefix();

	if ( $path === $prefix ) {
		return '';
	}

	if ( 0 === strpos( $path, $prefix . '/' ) ) {
		return trim( substr( $path, strlen( $prefix ) ), '/' );
	}

	return $path;
}

function yneko_reimu_i18n_prefixed_url( $relative = '' ) {
	$prefix   = yneko_reimu_i18n_url_prefix();
	$relative = trim( (string) $relative, '/' );
	$path     = $prefix . ( $relative ? '/' . $relative : '' );
	return home_url( user_trailingslashit( $path ) );
}

function yneko_reimu_i18n_home_url( $language = '' ) {
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url() : home_url( '/' );
}

function yneko_reimu_i18n_localize_url( $url, $language = '' ) {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return $url;
	}

	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	$url      = (string) $url;

	if ( '' === $url || '#' === $url || preg_match( '#^(mailto|tel):#i', $url ) ) {
		return $url;
	}

	$home = wp_parse_url( home_url( '/' ) );
	$raw  = wp_parse_url( $url );
	if ( ! is_array( $raw ) ) {
		return $url;
	}

	if ( isset( $raw['host'], $home['host'] ) && strtolower( $raw['host'] ) !== strtolower( $home['host'] ) ) {
		return $url;
	}

	$path      = isset( $raw['path'] ) ? trim( rawurldecode( $raw['path'] ), '/' ) : '';
	$home_path = isset( $home['path'] ) ? trim( rawurldecode( $home['path'] ), '/' ) : '';
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	} elseif ( $home_path === $path ) {
		$path = '';
	}

	$path = yneko_reimu_i18n_relative_without_prefix( $path );
	$base = 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $path ) : home_url( user_trailingslashit( $path ) );

	if ( ! empty( $raw['query'] ) ) {
		$base .= '?' . $raw['query'];
	}
	if ( ! empty( $raw['fragment'] ) ) {
		$base .= '#' . $raw['fragment'];
	}

	return $base;
}

function yneko_reimu_i18n_post_language( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$lang    = $post_id ? (string) get_post_meta( $post_id, '_yneko_reimu_language', true ) : '';
	return yneko_reimu_i18n_language_exists( $lang ) ? $lang : 'zh_CN';
}

function yneko_reimu_i18n_translation_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return $post_id ? absint( get_post_meta( $post_id, '_yneko_reimu_translation_id', true ) ) : 0;
}

function yneko_reimu_i18n_get_translation_id( $post_id, $target_language ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! yneko_reimu_i18n_language_exists( $target_language ) ) {
		return 0;
	}

	if ( yneko_reimu_i18n_post_language( $post_id ) === $target_language ) {
		return $post_id;
	}

	$translation_id = yneko_reimu_i18n_translation_id( $post_id );
	if ( $translation_id && get_post( $translation_id ) && yneko_reimu_i18n_post_language( $translation_id ) === $target_language ) {
		return $translation_id;
	}

	return 0;
}

function yneko_reimu_i18n_post_url( $post_id, $language = '' ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return yneko_reimu_i18n_home_url( $language );
	}

	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_post_language( $post_id );
	$url      = get_permalink( $post_id );

	if ( 'en_US' !== $language ) {
		return $url;
	}

	$relative = trim( wp_make_link_relative( $url ), '/' );
	$relative = yneko_reimu_i18n_relative_without_prefix( $relative );
	return yneko_reimu_i18n_prefixed_url( $relative );
}

function yneko_reimu_i18n_filter_post_link( $url, $post ) {
	if ( ! $post instanceof WP_Post || ! yneko_reimu_i18n_enabled() || ! empty( $GLOBALS['yneko_reimu_generating_i18n_permalink'] ) ) {
		return $url;
	}

	if ( 'en_US' !== yneko_reimu_i18n_post_language( $post->ID ) ) {
		return $url;
	}

	$GLOBALS['yneko_reimu_generating_i18n_permalink'] = true;
	$relative = trim( wp_make_link_relative( $url ), '/' );
	$relative = yneko_reimu_i18n_relative_without_prefix( $relative );
	$GLOBALS['yneko_reimu_generating_i18n_permalink'] = false;

	return yneko_reimu_i18n_prefixed_url( $relative );
}
add_filter( 'post_link', 'yneko_reimu_i18n_filter_post_link', 20, 2 );

function yneko_reimu_i18n_filter_page_link( $url, $post_id ) {
	$post = get_post( $post_id );
	return yneko_reimu_i18n_filter_post_link( $url, $post );
}
add_filter( 'page_link', 'yneko_reimu_i18n_filter_page_link', 20, 2 );

function yneko_reimu_i18n_rewrite_rules() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return;
	}

	$prefix = yneko_reimu_i18n_url_prefix();
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/page/([0-9]+)/?$', 'index.php?yneko_reimu_lang=en_US&paged=$matches[1]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/category/(.+?)/page/([0-9]+)/?$', 'index.php?yneko_reimu_lang=en_US&category_name=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/category/(.+?)/?$', 'index.php?yneko_reimu_lang=en_US&category_name=$matches[1]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/tag/(.+?)/page/([0-9]+)/?$', 'index.php?yneko_reimu_lang=en_US&tag=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/tag/(.+?)/?$', 'index.php?yneko_reimu_lang=en_US&tag=$matches[1]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/author/([^/]+)/page/([0-9]+)/?$', 'index.php?yneko_reimu_lang=en_US&author_name=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/author/([^/]+)/?$', 'index.php?yneko_reimu_lang=en_US&author_name=$matches[1]', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/?$', 'index.php?yneko_reimu_lang=en_US', 'top' );
	add_rewrite_rule( '^' . preg_quote( $prefix, '/' ) . '/(.+?)/?$', 'index.php?yneko_reimu_lang=en_US&yneko_reimu_en_path=$matches[1]', 'top' );
}
add_action( 'init', 'yneko_reimu_i18n_rewrite_rules', 5 );

function yneko_reimu_i18n_detect_en_path() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return null;
	}

	$path   = yneko_reimu_i18n_request_path();
	$prefix = yneko_reimu_i18n_url_prefix();

	if ( $path === $prefix ) {
		return '';
	}

	if ( 0 === strpos( $path, $prefix . '/' ) ) {
		return yneko_reimu_i18n_relative_without_prefix( $path );
	}

	return null;
}

function yneko_reimu_i18n_query_vars( $vars ) {
	$vars[] = 'yneko_reimu_lang';
	$vars[] = 'yneko_reimu_en_path';
	return $vars;
}
add_filter( 'query_vars', 'yneko_reimu_i18n_query_vars' );

function yneko_reimu_i18n_parse_request_fallback( $wp ) {
	$path = yneko_reimu_i18n_detect_en_path();
	if ( null === $path ) {
		return;
	}

	$GLOBALS['yneko_reimu_current_language'] = 'en_US';
	$wp->query_vars['yneko_reimu_lang'] = 'en_US';
	$wp->query_vars['yneko_reimu_en_path'] = $path;
}
add_action( 'parse_request', 'yneko_reimu_i18n_parse_request_fallback', 1 );

function yneko_reimu_i18n_clear_singular_query_vars( $query ) {
	foreach ( array( 'name', 'pagename', 'page_id', 'post_type', 'attachment', 'attachment_id' ) as $key ) {
		$query->set( $key, '' );
	}
}

function yneko_reimu_i18n_clear_archive_query_vars( $query ) {
	foreach ( array( 'category_name', 'cat', 'tag', 'tag_id', 'author', 'author_name', 'year', 'monthnum', 'day' ) as $key ) {
		$query->set( $key, '' );
	}
}

function yneko_reimu_i18n_mark_home_query( $query ) {
	yneko_reimu_i18n_clear_singular_query_vars( $query );
	yneko_reimu_i18n_clear_archive_query_vars( $query );
	$query->set( 'error', '' );
	$query->is_home       = true;
	$query->is_front_page = 'page' !== get_option( 'show_on_front' );
	$query->is_page       = false;
	$query->is_single     = false;
	$query->is_singular   = false;
	$query->is_archive    = false;
	$query->is_category   = false;
	$query->is_tag        = false;
	$query->is_author     = false;
	$query->is_date       = false;
	$query->is_404        = false;
}

function yneko_reimu_i18n_mark_page_query( $query, $path ) {
	yneko_reimu_i18n_clear_archive_query_vars( $query );
	$query->set( 'name', '' );
	$query->set( 'page_id', '' );
	$query->set( 'post_type', 'page' );
	$query->set( 'pagename', $path );
	$query->set( 'error', '' );
	$query->is_home     = false;
	$query->is_page     = true;
	$query->is_single   = false;
	$query->is_singular = true;
	$query->is_archive  = false;
	$query->is_404      = false;
}

function yneko_reimu_i18n_mark_post_query( $query, $post ) {
	yneko_reimu_i18n_clear_archive_query_vars( $query );
	$query->set( 'pagename', '' );
	$query->set( 'page_id', '' );
	$query->set( 'post_type', $post->post_type );
	$query->set( 'name', $post->post_name );
	$query->set( 'error', '' );
	$query->is_home     = false;
	$query->is_page     = false;
	$query->is_single   = true;
	$query->is_singular = true;
	$query->is_archive  = false;
	$query->is_404      = false;
}

function yneko_reimu_i18n_resolve_archive_path( $query, $path ) {
	if ( preg_match( '#^category/(.+?)(?:/page/([0-9]+))?$#', $path, $matches ) ) {
		yneko_reimu_i18n_clear_singular_query_vars( $query );
		$query->set( 'category_name', sanitize_title( $matches[1] ) );
		if ( ! empty( $matches[2] ) ) {
			$query->set( 'paged', absint( $matches[2] ) );
		}
		return true;
	}

	if ( preg_match( '#^tag/(.+?)(?:/page/([0-9]+))?$#', $path, $matches ) ) {
		yneko_reimu_i18n_clear_singular_query_vars( $query );
		$query->set( 'tag', sanitize_title( $matches[1] ) );
		if ( ! empty( $matches[2] ) ) {
			$query->set( 'paged', absint( $matches[2] ) );
		}
		return true;
	}

	if ( preg_match( '#^author/([^/]+)(?:/page/([0-9]+))?$#', $path, $matches ) ) {
		yneko_reimu_i18n_clear_singular_query_vars( $query );
		$query->set( 'author_name', sanitize_title( $matches[1] ) );
		if ( ! empty( $matches[2] ) ) {
			$query->set( 'paged', absint( $matches[2] ) );
		}
		return true;
	}

	if ( preg_match( '#^page/([0-9]+)$#', $path, $matches ) ) {
		yneko_reimu_i18n_mark_home_query( $query );
		$query->set( 'paged', absint( $matches[1] ) );
		return true;
	}

	return false;
}

function yneko_reimu_i18n_resolve_en_request( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$path = yneko_reimu_i18n_detect_en_path();
	if ( null !== $path ) {
		$query->set( 'yneko_reimu_lang', 'en_US' );
		$query->set( 'yneko_reimu_en_path', $path );
	}

	if ( 'en_US' !== $query->get( 'yneko_reimu_lang' ) && 'en_US' !== get_query_var( 'yneko_reimu_lang' ) ) {
		return;
	}

	$GLOBALS['yneko_reimu_current_language'] = 'en_US';
	$path = trim( (string) $query->get( 'yneko_reimu_en_path' ), '/' );

	if ( '' === $path ) {
		yneko_reimu_i18n_mark_home_query( $query );
		return;
	}

	if ( 'search.json' === trim( $path, '/' ) ) {
		yneko_reimu_i18n_mark_home_query( $query );
		$query->set( 'yneko_reimu_search_json', '1' );
		$query->set( 'yneko_reimu_search_lang', 'en_US' );
		return;
	}

	if ( yneko_reimu_i18n_resolve_archive_path( $query, $path ) ) {
		return;
	}

	if ( in_array( $path, array( 'about', 'projects', 'archives', 'friend' ), true ) ) {
		yneko_reimu_i18n_mark_page_query( $query, $path );
		return;
	}

	$page = get_page_by_path( $path, OBJECT, array( 'post', 'page' ) );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		if ( 'page' === $page->post_type ) {
			yneko_reimu_i18n_mark_page_query( $query, $path );
		} else {
			yneko_reimu_i18n_mark_post_query( $query, $page );
		}
		return;
	}

	yneko_reimu_i18n_clear_singular_query_vars( $query );
	yneko_reimu_i18n_clear_archive_query_vars( $query );
	$query->set( 'error', '404' );
}
add_action( 'pre_get_posts', 'yneko_reimu_i18n_resolve_en_request', 1 );

function yneko_reimu_i18n_language_meta_query( $language = '' ) {
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();

	if ( 'zh_CN' === $language ) {
		return array(
			'relation' => 'OR',
			array(
				'key'     => '_yneko_reimu_language',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_yneko_reimu_language',
				'value'   => 'zh_CN',
				'compare' => '=',
			),
			array(
				'key'     => '_yneko_reimu_language',
				'value'   => '',
				'compare' => '=',
			),
		);
	}

	return array(
		array(
			'key'     => '_yneko_reimu_language',
			'value'   => $language,
			'compare' => '=',
		),
	);
}

function yneko_reimu_i18n_merge_meta_query( $query, $language = '' ) {
	$existing = $query->get( 'meta_query' );
	$existing = is_array( $existing ) ? $existing : array();
	$language_query = yneko_reimu_i18n_language_meta_query( $language );

	if ( empty( $existing ) ) {
		$query->set( 'meta_query', $language_query );
		return;
	}

	$query->set(
		'meta_query',
		array(
			'relation' => 'AND',
			$existing,
			$language_query,
		)
	);
}

function yneko_reimu_i18n_filter_main_query( $query ) {
	if ( is_admin() || ! yneko_reimu_i18n_enabled() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_home() || is_archive() || is_search() ) {
		yneko_reimu_i18n_merge_meta_query( $query );
	}
}
add_action( 'pre_get_posts', 'yneko_reimu_i18n_filter_main_query', 20 );

function yneko_reimu_i18n_rest_post_query( $args, $request ) {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return $args;
	}

	$language = $request instanceof WP_REST_Request ? (string) $request->get_param( 'reimu_language' ) : '';
	if ( ! yneko_reimu_i18n_language_exists( $language ) ) {
		return $args;
	}

	$existing = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : array();
	$language_query = yneko_reimu_i18n_language_meta_query( $language );
	$args['meta_query'] = $existing ? array( 'relation' => 'AND', $existing, $language_query ) : $language_query;
	return $args;
}
add_filter( 'rest_post_query', 'yneko_reimu_i18n_rest_post_query', 20, 2 );

function yneko_reimu_i18n_virtual_path( $slug, $language = '' ) {
	$slug     = trim( (string) $slug, '/' );
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $slug ) : home_url( user_trailingslashit( $slug ) );
}

function yneko_reimu_i18n_switch_url( $language ) {
	$language = yneko_reimu_i18n_language_exists( $language ) ? $language : 'zh_CN';

	$special_slug = function_exists( 'yneko_reimu_special_page_slug' ) ? yneko_reimu_special_page_slug() : '';
	if ( function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page() ) {
		return yneko_reimu_i18n_virtual_path( yneko_reimu_virtual_page_slug(), $language );
	}
	if ( $special_slug ) {
		return yneko_reimu_i18n_virtual_path( $special_slug, $language );
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$target  = yneko_reimu_i18n_get_translation_id( $post_id, $language );
		return $target ? yneko_reimu_i18n_post_url( $target, $language ) : yneko_reimu_i18n_home_url( $language );
	}

	$path = yneko_reimu_i18n_request_path();
	$path = yneko_reimu_i18n_relative_without_prefix( $path );

	if ( '' === $path ) {
		return yneko_reimu_i18n_home_url( $language );
	}

	return 'en_US' === $language ? yneko_reimu_i18n_prefixed_url( $path ) : home_url( user_trailingslashit( $path ) );
}

function yneko_reimu_i18n_options() {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return array();
	}

	$current = yneko_reimu_i18n_current_language();
	$options = array();

	foreach ( yneko_reimu_i18n_languages() as $language ) {
		$options[] = array(
			'code'     => $language['code'],
			'label'    => $language['label'],
			'url'      => yneko_reimu_i18n_switch_url( $language['code'] ),
			'selected' => $language['code'] === $current,
		);
	}

	return $options;
}

function yneko_reimu_i18n_filter_term_link( $url ) {
	return yneko_reimu_i18n_enabled() && 'en_US' === yneko_reimu_i18n_current_language() ? yneko_reimu_i18n_localize_url( $url, 'en_US' ) : $url;
}
add_filter( 'term_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'author_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'year_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'month_link', 'yneko_reimu_i18n_filter_term_link', 20 );
add_filter( 'day_link', 'yneko_reimu_i18n_filter_term_link', 20 );

function yneko_reimu_i18n_flush_rewrite_rules() {
	yneko_reimu_i18n_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'yneko_reimu_i18n_flush_rewrite_rules' );

function yneko_reimu_i18n_maybe_flush_rewrite_rules() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		$state = array(
			'version' => defined( 'YNEKO_REIMU_VERSION' ) ? YNEKO_REIMU_VERSION : '',
			'enabled' => yneko_reimu_i18n_enabled() ? '1' : '0',
			'prefix'  => yneko_reimu_i18n_url_prefix(),
		);
		$key = md5( wp_json_encode( $state ) );
		if ( get_option( 'yneko_reimu_i18n_rewrite_state' ) !== $key ) {
			yneko_reimu_i18n_flush_rewrite_rules();
			update_option( 'yneko_reimu_i18n_rewrite_state', $key, false );
		}
	}
}
add_action( 'admin_init', 'yneko_reimu_i18n_maybe_flush_rewrite_rules' );
