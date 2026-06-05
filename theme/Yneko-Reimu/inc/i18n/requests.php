<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		$category_path = implode(
			'/',
			array_filter(
				array_map(
					'sanitize_title',
					explode( '/', trim( $matches[1], '/' ) )
				)
			)
		);
		$query->set( 'category_name', $category_path );
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
		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $path ) ) {
			yneko_reimu_i18n_mark_404_query( $query );
			return;
		}

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

function yneko_reimu_i18n_flush_rewrite_rules() {
	yneko_reimu_i18n_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'yneko_reimu_i18n_flush_rewrite_rules' );

function yneko_reimu_i18n_maybe_flush_rewrite_rules() {
	if ( wp_doing_ajax() ) {
		return;
	}

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
add_action( 'admin_init', 'yneko_reimu_i18n_maybe_flush_rewrite_rules' );
add_action( 'wp_loaded', 'yneko_reimu_i18n_maybe_flush_rewrite_rules', 20 );
