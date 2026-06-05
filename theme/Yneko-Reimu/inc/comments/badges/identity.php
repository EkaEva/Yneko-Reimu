<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_user_profile_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( $user && ! empty( $user->user_url ) ) {
		return esc_url_raw( $user->user_url );
	}

	return '';
}

function yneko_reimu_comment_user_github_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	foreach ( array( '_yneko_reimu_github_url', '_yneko_github_url' ) as $meta_key ) {
		$url = get_user_meta( $user_id, $meta_key, true );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	return '';
}
