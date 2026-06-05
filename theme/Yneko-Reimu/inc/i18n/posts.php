<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
