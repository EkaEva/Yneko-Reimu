<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_admin_count_pending_comment_uploads( $type = 'all' ) {
	if ( ! function_exists( 'yneko_reimu_comment_pending_temp_uploads' ) || ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return 0;
	}

	$type  = in_array( $type, array( 'all', 'image', 'gif' ), true ) ? $type : 'all';
	$count = 0;
	foreach ( yneko_reimu_comment_pending_temp_uploads( 300 ) as $item ) {
		$item_type = 'gif' === ( $item['type'] ?? '' ) ? 'gif' : 'image';
		if ( 'all' === $type || $type === $item_type ) {
			$count++;
		}
	}
	foreach ( yneko_reimu_comment_upload_library( 300, $type, true ) as $item ) {
		if ( 'pending' === (string) ( $item['status'] ?? '' ) ) {
			$count++;
		}
	}

	return $count;
}

function yneko_reimu_admin_count_pending_avatars() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_avatar_pending_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	return count( $users );
}

function yneko_reimu_admin_count_pending_user_badges() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	$count = 0;
	foreach ( $users as $user_id ) {
		$tags = get_user_meta( absint( $user_id ), '_yneko_reimu_comment_tags_pending', true );
		if ( is_array( $tags ) ) {
			$count += count( array_filter( $tags, 'is_array' ) );
		}
	}
	return $count;
}

function yneko_reimu_admin_review_badge_counts() {
	$upload = yneko_reimu_settings_comment_upload();
	$badges = yneko_reimu_settings_user_badges();
	$counts = array(
		'comment_images' => '1' === (string) ( $upload['image_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'image' ) : 0,
		'comment_gifs'   => '1' === (string) ( $upload['gif_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'gif' ) : 0,
		'avatars'        => '1' === (string) ( $upload['avatar_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_avatars() : 0,
		'user_badges'    => '1' === (string) ( $badges['review_enabled'] ?? '0' ) ? yneko_reimu_admin_count_pending_user_badges() : 0,
	);
	$counts['comments'] = $counts['comment_images'] + $counts['comment_gifs'];
	$counts['users']    = $counts['avatars'] + $counts['user_badges'];
	$counts['security'] = function_exists( 'yneko_reimu_auth_security_unhandled_count' ) ? yneko_reimu_auth_security_unhandled_count() : 0;
	return $counts;
}
