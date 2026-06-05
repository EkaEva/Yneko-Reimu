<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_sanitize_comment_upload_settings( $upload ) {
	$upload = is_array( $upload ) ? $upload : array();

	return array(
		'enabled'                => ( ! empty( $upload['image_enabled'] ) || ! empty( $upload['gif_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
		'image_enabled'          => ( ! empty( $upload['image_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
		'gif_enabled'            => ( ! empty( $upload['gif_enabled'] ) || ! empty( $upload['enabled'] ) ) ? '1' : '0',
		'image_review'           => ! empty( $upload['image_review'] ) ? '1' : '0',
		'gif_review'             => ! empty( $upload['gif_review'] ) ? '1' : '0',
		'image_max_mb'           => max( 1, min( 20, absint( $upload['image_max_mb'] ?? 1 ) ) ),
		'gif_max_mb'             => max( 1, min( 30, absint( $upload['gif_max_mb'] ?? 3 ) ) ),
		'temp_cleanup_days'      => max( 1, min( 30, absint( $upload['temp_cleanup_days'] ?? 7 ) ) ),
		'rejected_cleanup_hours' => max( 1, min( 168, absint( $upload['rejected_cleanup_hours'] ?? 24 ) ) ),
		'avatar_enabled'         => ! empty( $upload['avatar_enabled'] ) ? '1' : '0',
		'avatar_review'          => ! empty( $upload['avatar_review'] ) ? '1' : '0',
		'avatar_max_mb'          => max( 1, min( 10, absint( $upload['avatar_max_mb'] ?? 1 ) ) ),
	);
}
