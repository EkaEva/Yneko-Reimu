<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once YNEKO_REIMU_DIR . '/inc/settings/schema.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/admin.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/renderers.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/panels.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/page.php';

function yneko_reimu_register_settings() {
	register_setting(
		'yneko_reimu_settings',
		'yneko_reimu_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'yneko_reimu_sanitize_settings',
			'default'           => yneko_reimu_settings_defaults(),
		)
	);
}
add_action( 'admin_init', 'yneko_reimu_register_settings' );

function yneko_reimu_cleanup_blocked_user_badges_after_settings_save( $old_value, $value, $option ) {
	unset( $old_value, $option );
	if ( empty( $value['user_badges'] ) || ! function_exists( 'yneko_reimu_comment_normalize_tag_list' ) ) {
		return;
	}

	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_comment_tags',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	foreach ( $users as $user_id ) {
		foreach ( array( '_yneko_reimu_comment_tags', '_yneko_reimu_comment_tags_pending' ) as $meta_key ) {
			$current = get_user_meta( absint( $user_id ), $meta_key, true );
			$limit   = function_exists( 'yneko_reimu_comment_custom_tag_storage_limit' ) ? yneko_reimu_comment_custom_tag_storage_limit() : 5;
			$clean   = yneko_reimu_comment_normalize_tag_list( $current, $limit );
			if ( $clean ) {
				update_user_meta( absint( $user_id ), $meta_key, $clean );
			} else {
				delete_user_meta( absint( $user_id ), $meta_key );
			}
		}
	}
}
add_action( 'updated_option_yneko_reimu_settings', 'yneko_reimu_cleanup_blocked_user_badges_after_settings_save', 10, 3 );
