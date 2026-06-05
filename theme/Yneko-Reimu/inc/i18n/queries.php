<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
