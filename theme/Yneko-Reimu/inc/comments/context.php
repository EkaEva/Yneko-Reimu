<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comments_canonical_post_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
	if ( ! $post_id ) {
		return 0;
	}

	if ( function_exists( 'yneko_reimu_i18n_source_post_id' ) ) {
		$source_id = yneko_reimu_i18n_source_post_id( $post_id );
		if ( $source_id ) {
			return $source_id;
		}
	}

	return $post_id;
}

function yneko_reimu_comments_current_display_post_id() {
	$post_id = absint( get_queried_object_id() );
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
	return $post_id ? $post_id : get_the_ID();
}

function yneko_reimu_comments_virtual_page_post_id( $slug ) {
	$slug = sanitize_title( $slug );
	if ( ! $slug ) {
		return 0;
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		return absint( $page->ID );
	}

	if ( 'projects' === $slug ) {
		$carrier_id = absint( get_option( 'yneko_reimu_projects_comment_post_id' ) );
		$carrier    = $carrier_id ? get_post( $carrier_id ) : null;
		if ( $carrier && 'trash' !== get_post_status( $carrier ) ) {
			return $carrier_id;
		}

		$existing_carrier = get_page_by_path( 'yneko-reimu-projects-comments', OBJECT, 'page' );
		if ( $existing_carrier && 'trash' !== get_post_status( $existing_carrier ) ) {
			$carrier_id = absint( $existing_carrier->ID );
		} else {
			$carrier_id = wp_insert_post(
				array(
					'post_title'     => 'Yneko Reimu Projects Comments',
					'post_name'      => 'yneko-reimu-projects-comments',
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_content'   => '',
					'comment_status' => 'open',
					'ping_status'    => 'closed',
				),
				true
			);
			$carrier_id = is_wp_error( $carrier_id ) ? 0 : absint( $carrier_id );
		}

		if ( $carrier_id ) {
			update_option( 'yneko_reimu_projects_comment_post_id', $carrier_id, false );
			return $carrier_id;
		}
	}

	$fallback_id = absint( get_option( 'page_on_front' ) );
	if ( ! $fallback_id ) {
		$fallback_id = absint( get_option( 'page_for_posts' ) );
	}

	return $fallback_id;
}

function yneko_reimu_default_open_projects_comments( $post_id, $post, $update ) {
	if ( $update || 'page' !== $post->post_type || 'projects' !== $post->post_name ) {
		return;
	}

	if ( 'open' === $post->comment_status ) {
		return;
	}

	remove_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10 );
	wp_update_post(
		array(
			'ID'             => absint( $post_id ),
			'comment_status' => 'open',
		)
	);
	add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );
}
add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );

function yneko_reimu_ajax_language_from_url( $url ) {
	if ( ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() || ! function_exists( 'yneko_reimu_i18n_url_prefix' ) ) {
		return '';
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path = trim( is_string( $path ) ? $path : '', '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home && ( $path === $home || 0 === strpos( $path, $home . '/' ) ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}

	$prefix = trim( (string) yneko_reimu_i18n_url_prefix(), '/' );
	return ( $prefix && ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ) ? 'en_US' : 'zh_CN';
}

function yneko_reimu_ajax_set_language_from_redirect( $redirect ) {
	$language = yneko_reimu_ajax_language_from_url( $redirect );
	if ( ! $language || ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() ) {
		return;
	}

	$GLOBALS['yneko_reimu_current_language'] = $language;
	if ( 'en_US' === $language ) {
		$mofile = YNEKO_REIMU_DIR . '/languages/en_US.mo';
		if ( file_exists( $mofile ) ) {
			unload_textdomain( 'yneko-reimu' );
			load_textdomain( 'yneko-reimu', $mofile, 'en_US' );
		}
	}
}
