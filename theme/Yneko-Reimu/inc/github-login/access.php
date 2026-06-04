<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_current_user_can_access_admin() {
	return current_user_can( 'manage_options' );
}

function yneko_reimu_hide_admin_bar_for_comment_users( $show ) {
	if ( is_user_logged_in() ) {
		return false;
	}

	return $show;
}

add_filter( 'show_admin_bar', 'yneko_reimu_hide_admin_bar_for_comment_users' );

function yneko_reimu_block_comment_users_from_admin() {
	if ( ! is_user_logged_in() || yneko_reimu_current_user_can_access_admin() ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}

add_action( 'admin_init', 'yneko_reimu_block_comment_users_from_admin' );
