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

function yneko_reimu_i18n_normalize_language( $language ) {
	$language = (string) $language;
	$map      = array(
		'zh_CN' => 'zh_CN',
		'zh_cn' => 'zh_CN',
		'zh-cn' => 'zh_CN',
		'en_US' => 'en_US',
		'en_us' => 'en_US',
		'en-us' => 'en_US',
	);
	return isset( $map[ $language ] ) ? $map[ $language ] : '';
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

function yneko_reimu_i18n_frontend_text( $text ) {
	if ( ! yneko_reimu_i18n_enabled() || is_admin() && ! wp_doing_ajax() || 'en_US' !== yneko_reimu_i18n_current_language() ) {
		return $text;
	}

	$translations = array(
		'置顶' => 'Sticky',
		'本站信息' => 'Site info',
		'申请方法' => 'How to apply',
		'小伙伴们' => 'Friends',
		'添加本站后，在本页留言，格式如下' => 'After adding this site, leave a comment on this page in the following format.',
		'没有文章' => 'No posts',
		'字'   => 'words',
		'篇文章' => 'posts',
	);

	return isset( $translations[ $text ] ) ? $translations[ $text ] : $text;
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
	$lang    = yneko_reimu_i18n_normalize_language( $lang );
	return yneko_reimu_i18n_language_exists( $lang ) ? $lang : 'zh_CN';
}

function yneko_reimu_i18n_translation_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return $post_id ? absint( get_post_meta( $post_id, '_yneko_reimu_translation_id', true ) ) : 0;
}

function yneko_reimu_i18n_source_post_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	if ( ! $post_id ) {
		return 0;
	}

	$lang = yneko_reimu_i18n_post_language( $post_id );
	if ( 'en_US' !== $lang ) {
		return $post_id;
	}

	$translation_id = yneko_reimu_i18n_translation_id( $post_id );
	if ( $translation_id && get_post( $translation_id ) && 'en_US' !== yneko_reimu_i18n_post_language( $translation_id ) ) {
		return $translation_id;
	}

	return $post_id;
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
	$GLOBALS['yneko_reimu_generating_i18n_permalink'] = true;
	$url = get_permalink( $post_id );
	$GLOBALS['yneko_reimu_generating_i18n_permalink'] = false;

	if ( 'en_US' !== $language ) {
		return $url;
	}

	$relative = trim( wp_make_link_relative( $url ), '/' );
	$relative = yneko_reimu_i18n_relative_without_prefix( $relative );
	return yneko_reimu_i18n_prefixed_url( $relative );
}

function yneko_reimu_i18n_display_post_for_language( $post_id, $language = '' ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return 0;
	}

	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	$target   = yneko_reimu_i18n_get_translation_id( $post_id, $language );

	return $target ? $target : $post_id;
}

function yneko_reimu_i18n_find_post_by_en_path( $path ) {
	$path = trim( (string) $path, '/' );
	if ( '' === $path ) {
		return null;
	}

	$post = get_page_by_path( $path, OBJECT, array( 'post', 'page' ) );
	if ( $post && 'publish' === get_post_status( $post ) ) {
		return $post;
	}

	$front = (array) $GLOBALS['wp_rewrite']->front;
	$front = isset( $front[0] ) ? trim( (string) $front[0], '/' ) : '';
	if ( '' !== $front && 0 === strpos( $path, $front . '/' ) ) {
		$without_front = trim( substr( $path, strlen( $front ) + 1 ), '/' );
		$post = get_page_by_path( $without_front, OBJECT, array( 'post', 'page' ) );
		if ( $post && 'publish' === get_post_status( $post ) ) {
			return $post;
		}
	}

	$slug = basename( $path );
	if ( $slug && $slug !== $path ) {
		$candidates = get_posts(
			array(
				'name'           => sanitize_title( $slug ),
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'publish',
				'posts_per_page' => 10,
			)
		);

		foreach ( $candidates as $candidate ) {
			$relative = trim( wp_make_link_relative( get_permalink( $candidate ) ), '/' );
			$relative = yneko_reimu_i18n_relative_without_prefix( $relative );
			if ( $relative === $path ) {
				return $candidate;
			}
		}
	}

	return null;
}

function yneko_reimu_i18n_filter_post_link( $url, $post ) {
	if ( ! $post instanceof WP_Post || ! yneko_reimu_i18n_enabled() || ! empty( $GLOBALS['yneko_reimu_generating_i18n_permalink'] ) ) {
		return $url;
	}

	if ( 'en_US' !== yneko_reimu_i18n_current_language() && 'en_US' !== yneko_reimu_i18n_post_language( $post->ID ) ) {
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
	$vars[] = 'yneko_reimu_force_404';
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
	foreach ( array( 'p', 'name', 'pagename', 'page_id', 'post_type', 'attachment', 'attachment_id' ) as $key ) {
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

function yneko_reimu_i18n_mark_404_query( $query ) {
	yneko_reimu_i18n_clear_singular_query_vars( $query );
	yneko_reimu_i18n_clear_archive_query_vars( $query );
	$query->set( 'error', '404' );
	$query->set( 'yneko_reimu_force_404', '1' );

	if ( method_exists( $query, 'set_404' ) ) {
		$query->set_404();
	}

	$query->is_home       = false;
	$query->is_front_page = false;
	$query->is_page       = false;
	$query->is_single     = false;
	$query->is_singular   = false;
	$query->is_archive    = false;
	$query->is_category   = false;
	$query->is_tag        = false;
	$query->is_author     = false;
	$query->is_date       = false;
	$query->is_search     = false;
	$query->is_attachment = false;
	$query->is_404        = true;
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

	$page = yneko_reimu_i18n_find_post_by_en_path( $path );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		if ( 'page' === $page->post_type ) {
			yneko_reimu_i18n_mark_page_query( $query, $path );
		} else {
			yneko_reimu_i18n_mark_post_query( $query, $page );
		}
		return;
	}

	yneko_reimu_i18n_mark_404_query( $query );
}
add_action( 'pre_get_posts', 'yneko_reimu_i18n_resolve_en_request', 1 );

function yneko_reimu_i18n_force_404_status() {
	global $wp_query;

	if ( ! $wp_query instanceof WP_Query || ! $wp_query->get( 'yneko_reimu_force_404' ) ) {
		return;
	}

	if ( method_exists( $wp_query, 'set_404' ) ) {
		$wp_query->set_404();
	}
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'yneko_reimu_i18n_force_404_status', 0 );

function yneko_reimu_i18n_force_404_template( $template ) {
	global $wp_query;

	if ( ! $wp_query instanceof WP_Query || ! $wp_query->get( 'yneko_reimu_force_404' ) ) {
		return $template;
	}

	$template_404 = locate_template( '404.php' );
	return $template_404 ? $template_404 : $template;
}
add_filter( 'template_include', 'yneko_reimu_i18n_force_404_template', 100 );

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
				'value'   => array( 'zh_CN', 'zh_cn' ),
				'compare' => 'IN',
			),
			array(
				'key'     => '_yneko_reimu_language',
				'value'   => '',
				'compare' => '=',
			),
		);
	}

	return array(
		'relation' => 'OR',
		array(
			'key'     => '_yneko_reimu_language',
			'value'   => 'en_US' === $language ? array( 'en_US', 'en_us' ) : array( $language ),
			'compare' => 'IN',
		),
		array(
			'key'     => '_yneko_reimu_language',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_yneko_reimu_language',
			'value'   => array( 'zh_CN', 'zh_cn' ),
			'compare' => 'IN',
		),
		array(
			'key'     => '_yneko_reimu_language',
			'value'   => '',
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

function yneko_reimu_i18n_translated_original_ids( $language = '' ) {
	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	if ( 'en_US' !== $language ) {
		return array();
	}

	static $cache = array();
	if ( isset( $cache[ $language ] ) ) {
		return $cache[ $language ];
	}

	$english_posts = get_posts(
		array(
			'post_type'              => array( 'post', 'page' ),
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_language',
					'value'   => array( 'en_US', 'en_us' ),
					'compare' => 'IN',
				),
			),
		)
	);

	$exclude = array();
	foreach ( $english_posts as $english_post_id ) {
		$translation_id = yneko_reimu_i18n_translation_id( $english_post_id );
		if ( $translation_id && 'publish' === get_post_status( $translation_id ) && 'en_US' !== yneko_reimu_i18n_post_language( $translation_id ) ) {
			$exclude[] = absint( $translation_id );
		}
	}

	$cache[ $language ] = array_values( array_unique( array_filter( $exclude ) ) );
	return $cache[ $language ];
}

function yneko_reimu_i18n_merge_language_meta_query_args( $args, $language = '' ) {
	$args           = is_array( $args ) ? $args : array();
	$existing       = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : array();
	$language_query = yneko_reimu_i18n_language_meta_query( $language );

	if ( empty( $existing ) ) {
		$args['meta_query'] = $language_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		return $args;
	}

	$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'relation' => 'AND',
		$existing,
		$language_query,
	);

	return $args;
}

function yneko_reimu_i18n_apply_language_query_args( $args, $language = '' ) {
	if ( ! yneko_reimu_i18n_enabled() ) {
		return is_array( $args ) ? $args : array();
	}

	$language = $language && yneko_reimu_i18n_language_exists( $language ) ? $language : yneko_reimu_i18n_current_language();
	$args     = yneko_reimu_i18n_merge_language_meta_query_args( $args, $language );
	$exclude  = yneko_reimu_i18n_translated_original_ids( $language );

	if ( $exclude ) {
		$existing = array_map( 'absint', isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array() );
		$args['post__not_in'] = array_values( array_unique( array_merge( $existing, $exclude ) ) );
	}

	return $args;
}

function yneko_reimu_i18n_exclude_translated_originals( $query, $language = '' ) {
	$exclude = yneko_reimu_i18n_translated_original_ids( $language );

	if ( ! $exclude ) {
		return;
	}

	$existing = array_map( 'absint', (array) $query->get( 'post__not_in' ) );
	$query->set( 'post__not_in', array_values( array_unique( array_merge( $existing, $exclude ) ) ) );
}

function yneko_reimu_i18n_filter_main_query( $query ) {
	if ( is_admin() || ! yneko_reimu_i18n_enabled() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_home() || is_archive() || is_search() ) {
		yneko_reimu_i18n_merge_meta_query( $query );
		yneko_reimu_i18n_exclude_translated_originals( $query );
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

	return yneko_reimu_i18n_apply_language_query_args( $args, $language );
}
add_filter( 'rest_post_query', 'yneko_reimu_i18n_rest_post_query', 20, 2 );

function yneko_reimu_i18n_filter_sticky_posts( $sticky_posts ) {
	if ( is_admin() || ! yneko_reimu_i18n_enabled() || 'en_US' !== yneko_reimu_i18n_current_language() ) {
		return $sticky_posts;
	}

	$sticky_posts = array_map( 'absint', (array) $sticky_posts );
	$translated   = array();

	foreach ( $sticky_posts as $post_id ) {
		$translation_id = yneko_reimu_i18n_get_translation_id( $post_id, 'en_US' );
		if ( $translation_id && 'publish' === get_post_status( $translation_id ) ) {
			$translated[] = $translation_id;
		}
	}

	return array_values( array_unique( array_merge( $sticky_posts, $translated ) ) );
}
add_filter( 'option_sticky_posts', 'yneko_reimu_i18n_filter_sticky_posts', 20 );

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
		return $target ? yneko_reimu_i18n_post_url( $target, $language ) : yneko_reimu_i18n_post_url( $post_id, $language );
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
