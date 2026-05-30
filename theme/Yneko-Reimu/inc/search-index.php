<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_search_json_url( $language = '' ) {
	$language = function_exists( 'yneko_reimu_i18n_language_exists' ) && yneko_reimu_i18n_language_exists( $language )
		? $language
		: ( function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : 'zh_CN' );

	if ( 'en_US' === $language && function_exists( 'yneko_reimu_i18n_prefixed_url' ) ) {
		return yneko_reimu_i18n_prefixed_url( 'search.json' );
	}

	return home_url( '/search.json' );
}

function yneko_reimu_search_index_rewrite() {
	add_rewrite_rule( '^search\.json$', 'index.php?yneko_reimu_search_json=1', 'top' );
	if ( function_exists( 'yneko_reimu_i18n_url_prefix' ) ) {
		add_rewrite_rule( '^' . preg_quote( yneko_reimu_i18n_url_prefix(), '/' ) . '/search\.json$', 'index.php?yneko_reimu_search_json=1&yneko_reimu_search_lang=en_US', 'top' );
	}
}
add_action( 'init', 'yneko_reimu_search_index_rewrite' );

function yneko_reimu_search_index_query_vars( $vars ) {
	$vars[] = 'yneko_reimu_search_json';
	$vars[] = 'yneko_reimu_search_lang';
	return $vars;
}
add_filter( 'query_vars', 'yneko_reimu_search_index_query_vars' );

function yneko_reimu_search_index_activation() {
	yneko_reimu_search_index_rewrite();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'yneko_reimu_search_index_activation' );

function yneko_reimu_search_index_item( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'publish' !== get_post_status( $post ) || post_password_required( $post_id ) ) {
		return null;
	}

	$categories = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'names' ) );
	$tags       = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'names' ) );
	$content    = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$excerpt    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( $content, 90, '...' );

	return array(
		'id'         => absint( $post_id ),
		'language'   => function_exists( 'yneko_reimu_i18n_post_language' ) ? yneko_reimu_i18n_post_language( $post_id ) : 'zh_CN',
		'title'      => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'url'        => get_permalink( $post_id ),
		'path'       => wp_make_link_relative( get_permalink( $post_id ) ),
		'date'       => get_the_date( DATE_W3C, $post_id ),
		'content'    => html_entity_decode( $content, ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'excerpt'    => html_entity_decode( wp_strip_all_tags( $excerpt ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'categories' => is_wp_error( $categories ) ? array() : array_values( $categories ),
		'tags'       => is_wp_error( $tags ) ? array() : array_values( $tags ),
	);
}

function yneko_reimu_search_index_data( $language = '' ) {
	$language   = function_exists( 'yneko_reimu_i18n_language_exists' ) && yneko_reimu_i18n_language_exists( $language ) ? $language : ( function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : 'zh_CN' );
	$meta_query = function_exists( 'yneko_reimu_i18n_language_meta_query' ) ? yneko_reimu_i18n_language_meta_query( $language ) : array();
	$args       = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 300,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => true,
	);
	if ( $meta_query ) {
		$args['meta_query'] = $meta_query;
	}

	$query = new WP_Query(
		$args
	);
	$items = array();

	foreach ( $query->posts as $post_id ) {
		$item = yneko_reimu_search_index_item( $post_id );

		if ( $item ) {
			$items[] = $item;
		}
	}

	return array(
		'language' => $language,
		'posts'    => $items,
	);
}

function yneko_reimu_is_search_json_request() {
	if ( get_query_var( 'yneko_reimu_search_json' ) || get_query_var( 'reimu_search_json' ) ) {
		return true;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( rawurldecode( $path ), '/' );

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	}

	if ( 'search.json' === $path ) {
		return true;
	}

	if ( function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ) {
		return 'search.json' === yneko_reimu_i18n_relative_without_prefix( $path );
	}

	return false;
}

function yneko_reimu_search_index_template_redirect() {
	if ( ! yneko_reimu_is_search_json_request() ) {
		return;
	}

	nocache_headers();
	header( 'Content-Type: application/json; charset=' . get_bloginfo( 'charset' ) );
	$language = get_query_var( 'yneko_reimu_search_lang' );
	if ( ! $language && function_exists( 'yneko_reimu_i18n_current_language' ) ) {
		$language = yneko_reimu_i18n_current_language();
	}
	echo wp_json_encode( yneko_reimu_search_index_data( $language ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	exit;
}
add_action( 'template_redirect', 'yneko_reimu_search_index_template_redirect', 0 );
