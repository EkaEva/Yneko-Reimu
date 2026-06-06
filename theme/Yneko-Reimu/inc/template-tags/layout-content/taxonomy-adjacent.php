<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_term_count_by_slug( $taxonomy, $slug ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	return absint( $term->count );
}

function yneko_reimu_term_count_with_children_by_slug( $taxonomy, $slug ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$term_ids = array( absint( $term->term_id ) );
	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$children = get_term_children( $term->term_id, $taxonomy );
		if ( ! is_wp_error( $children ) ) {
			$term_ids = array_merge( $term_ids, array_map( 'absint', $children ) );
		}
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'term_id',
					'terms'            => array_unique( $term_ids ),
					'include_children' => false,
				),
			),
		)
	);

	return absint( $query->found_posts );
}

function yneko_reimu_category_link_by_slug( $slug, $fallback = '' ) {
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( $term && ! is_wp_error( $term ) ) {
		return get_category_link( $term );
	}

	return $fallback ? $fallback : home_url( '/category/' . trim( $slug, '/' ) . '/' );
}

function yneko_reimu_post_link_parent_category( $category, $categories, $post ) {
	if ( ! $category || is_wp_error( $category ) ) {
		return $category;
	}

	while ( ! empty( $category->parent ) ) {
		$parent = get_category( $category->parent );
		if ( ! $parent || is_wp_error( $parent ) ) {
			break;
		}
		$category = $parent;
	}

	return $category;
}
add_filter( 'post_link_category', 'yneko_reimu_post_link_parent_category', 10, 3 );

function yneko_reimu_count_text( $count ) {
	return sprintf(
		/* translators: %d: post count. */
		_n( '%d 篇文章', '%d 篇文章', absint( $count ), 'yneko-reimu' ),
		absint( $count )
	);
}

function yneko_reimu_get_adjacent_post_for_language( $post_id = 0, $previous = true, $language = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return null;
	}

	$source_id = function_exists( 'yneko_reimu_i18n_source_post_id' ) ? yneko_reimu_i18n_source_post_id( $post_id ) : $post_id;
	$current   = get_post( $source_id );
	if ( ! $current || 'post' !== $current->post_type ) {
		return null;
	}

	$language = $language && function_exists( 'yneko_reimu_i18n_language_exists' ) && yneko_reimu_i18n_language_exists( $language )
		? $language
		: ( function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : '' );

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'orderby'                => array( 'date' => $previous ? 'DESC' : 'ASC', 'ID' => $previous ? 'DESC' : 'ASC' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'post__not_in'           => array( $source_id ),
		'date_query'             => array(
			array(
				$previous ? 'before' : 'after' => get_post_time( 'Y-m-d H:i:s', false, $source_id ),
				'inclusive' => false,
			),
		),
	);

	if ( function_exists( 'yneko_reimu_i18n_apply_language_query_args' ) ) {
		$args = yneko_reimu_i18n_apply_language_query_args( $args, 'zh_CN' );
	}

	$query = new WP_Query( $args );
	if ( ! $query->posts ) {
		return null;
	}

	$adjacent_id = absint( $query->posts[0]->ID );
	if ( function_exists( 'yneko_reimu_i18n_display_post_for_language' ) && $language ) {
		$adjacent_id = yneko_reimu_i18n_display_post_for_language( $adjacent_id, $language );
	}

	return get_post( $adjacent_id );
}
