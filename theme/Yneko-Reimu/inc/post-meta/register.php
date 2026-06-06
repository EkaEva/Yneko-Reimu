<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_rest_meta() {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		yneko_reimu_register_text_post_meta( $post_type );
		yneko_reimu_register_i18n_post_meta( $post_type );
		yneko_reimu_register_choice_post_meta( $post_type );
	}
}
add_action( 'init', 'yneko_reimu_register_rest_meta' );

function yneko_reimu_register_text_post_meta( $post_type ) {
	foreach ( array( '_yneko_reimu_banner_url', '_yneko_reimu_cover_url', '_yneko_reimu_summary', '_yneko_reimu_keywords' ) as $meta_key ) {
		register_post_meta(
			$post_type,
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => 'yneko_reimu_post_meta_auth_callback',
			)
		);
	}
}

function yneko_reimu_register_i18n_post_meta( $post_type ) {
	register_post_meta(
		$post_type,
		'_yneko_reimu_language',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'yneko_reimu_sanitize_post_language',
			'auth_callback'     => 'yneko_reimu_post_meta_auth_callback',
		)
	);

	register_post_meta(
		$post_type,
		'_yneko_reimu_translation_id',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => 'yneko_reimu_post_meta_auth_callback',
		)
	);
}

function yneko_reimu_register_choice_post_meta( $post_type ) {
	foreach ( yneko_reimu_post_meta_choice_keys() as $meta_key ) {
		register_post_meta(
			$post_type,
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => 'yneko_reimu_post_meta_auth_callback',
			)
		);
	}
}

function yneko_reimu_post_meta_choice_keys() {
	return array(
		'_yneko_reimu_sidebar',
		'_yneko_reimu_toc',
		'_yneko_reimu_copyright',
		'_yneko_reimu_outdated',
		'_yneko_reimu_comments',
		'_yneko_reimu_sticky',
	);
}

function yneko_reimu_post_meta_auth_callback() {
	return current_user_can( 'edit_posts' );
}

function yneko_reimu_sanitize_post_language( $language ) {
	if ( function_exists( 'yneko_reimu_i18n_normalize_language' ) ) {
		$normalized = yneko_reimu_i18n_normalize_language( $language );
		return $normalized ? $normalized : 'zh_CN';
	}

	return 'en_US' === $language ? 'en_US' : 'zh_CN';
}
