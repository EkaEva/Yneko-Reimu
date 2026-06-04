<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_login_find_or_create_user( $profile, $emails, $link_user_id = 0, $mode = 'login' ) {
	$github_id = isset( $profile['id'] ) ? (string) absint( $profile['id'] ) : '';
	$login     = isset( $profile['login'] ) ? sanitize_user( $profile['login'], true ) : '';

	if ( ! $github_id || ! $login ) {
		return new WP_Error( 'yneko_github_profile_invalid', __( 'GitHub profile is missing required fields.', 'yneko-reimu' ) );
	}

	$existing_user_id = yneko_reimu_github_login_find_user_by_github_id( $github_id );

	if ( 'bind' === $mode && $link_user_id && get_user_by( 'id', $link_user_id ) ) {
		if ( $existing_user_id && absint( $existing_user_id ) !== absint( $link_user_id ) ) {
			return new WP_Error( 'yneko_github_already_linked', __( 'This GitHub account is already linked to another WordPress account.', 'yneko-reimu' ) );
		}

		yneko_reimu_github_login_update_user_meta( $link_user_id, $profile );
		return $link_user_id;
	}

	if ( $existing_user_id ) {
		yneko_reimu_github_login_update_user_meta( $existing_user_id, $profile );
		return $existing_user_id;
	}

	$options = yneko_reimu_github_login_get_options();
	if ( '1' !== $options['auto_create'] ) {
		return new WP_Error( 'yneko_github_no_account', __( 'No WordPress account is linked to this GitHub account.', 'yneko-reimu' ) );
	}

	$email = yneko_reimu_github_login_pick_email( $profile, $emails );
	if ( email_exists( $email ) ) {
		return new WP_Error( 'yneko_github_email_exists', __( 'This GitHub email already belongs to an existing WordPress account. Please log in normally first, then bind GitHub.', 'yneko-reimu' ) );
	}

	$username = yneko_reimu_github_login_unique_username( $login );
	$user_id  = wp_insert_user(
		array(
			'user_login'   => $username,
			'user_email'   => $email,
			'user_pass'    => wp_generate_password( 32, true, true ),
			'display_name' => ! empty( $profile['name'] ) ? sanitize_text_field( $profile['name'] ) : $login,
			'role'         => 'subscriber',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		return $user_id;
	}

	yneko_reimu_github_login_update_user_meta( $user_id, $profile );
	return absint( $user_id );
}

function yneko_reimu_github_login_find_user_by_github_id( $github_id ) {
	foreach ( array( '_yneko_reimu_github_id', '_yneko_github_id' ) as $meta_key ) {
		$existing = get_users(
			array(
				'meta_key'   => $meta_key,
				'meta_value' => $github_id,
				'number'     => 1,
				'fields'     => 'ID',
			)
		);

		if ( ! empty( $existing ) ) {
			return absint( $existing[0] );
		}
	}

	return 0;
}

function yneko_reimu_github_login_pick_email( $profile, $emails ) {
	foreach ( (array) $emails as $email ) {
		if ( ! empty( $email['primary'] ) && ! empty( $email['verified'] ) && ! empty( $email['email'] ) && is_email( $email['email'] ) ) {
			return sanitize_email( $email['email'] );
		}
	}

	foreach ( (array) $emails as $email ) {
		if ( ! empty( $email['verified'] ) && ! empty( $email['email'] ) && is_email( $email['email'] ) ) {
			return sanitize_email( $email['email'] );
		}
	}

	if ( ! empty( $profile['email'] ) && is_email( $profile['email'] ) ) {
		return sanitize_email( $profile['email'] );
	}

	$id    = isset( $profile['id'] ) ? absint( $profile['id'] ) : time();
	$login = isset( $profile['login'] ) ? sanitize_user( $profile['login'], true ) : 'github';
	return sanitize_email( $id . '+' . $login . '@users.noreply.github.com' );
}

function yneko_reimu_github_login_unique_username( $base ) {
	$base = sanitize_user( $base, true );
	$base = $base ? $base : 'github_user';
	$name = $base;
	$i    = 2;

	while ( username_exists( $name ) ) {
		$name = $base . '-' . $i;
		$i++;
	}

	return $name;
}

function yneko_reimu_github_login_update_user_meta( $user_id, $profile ) {
	$values = array(
		'id'         => isset( $profile['id'] ) ? (string) absint( $profile['id'] ) : '',
		'login'      => isset( $profile['login'] ) ? sanitize_text_field( $profile['login'] ) : '',
		'url'        => isset( $profile['html_url'] ) ? esc_url_raw( $profile['html_url'] ) : '',
		'avatar_url' => isset( $profile['avatar_url'] ) ? esc_url_raw( $profile['avatar_url'] ) : '',
	);

	update_user_meta( $user_id, '_yneko_reimu_github_id', $values['id'] );
	update_user_meta( $user_id, '_yneko_reimu_github_login', $values['login'] );
	update_user_meta( $user_id, '_yneko_reimu_github_url', $values['url'] );
	update_user_meta( $user_id, '_yneko_reimu_github_avatar_url', $values['avatar_url'] );

	update_user_meta( $user_id, '_yneko_github_id', $values['id'] );
	update_user_meta( $user_id, '_yneko_github_login', $values['login'] );
	update_user_meta( $user_id, '_yneko_github_url', $values['url'] );
	update_user_meta( $user_id, '_yneko_github_avatar_url', $values['avatar_url'] );

	$user = get_userdata( absint( $user_id ) );
	if ( $user && '' === (string) $user->user_url && '' === (string) get_user_meta( $user_id, '_yneko_reimu_profile_url_touched', true ) && $values['url'] ) {
		wp_update_user(
			array(
				'ID'       => absint( $user_id ),
				'user_url' => $values['url'],
			)
		);
	}
}
