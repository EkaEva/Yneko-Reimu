<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_login_avatar_data( $args, $id_or_email ) {
	$user_id = yneko_reimu_github_login_resolve_user_id( $id_or_email );
	if ( ! $user_id ) {
		return $args;
	}

	if ( function_exists( 'yneko_reimu_user_avatar_url' ) ) {
		$custom_avatar = yneko_reimu_user_avatar_url( $user_id );
		if ( $custom_avatar ) {
			$args['url']          = $custom_avatar;
			$args['found_avatar'] = true;
			return $args;
		}
	}

	if ( yneko_reimu_github_login_user_has_local_avatar( $user_id ) ) {
		return $args;
	}

	$url = yneko_reimu_github_login_avatar_url( $user_id, absint( $args['size'] ?? 96 ) );
	if ( ! $url ) {
		return $args;
	}

	$args['url']       = $url;
	$args['found_avatar'] = true;

	return $args;
}

add_filter( 'get_avatar_data', 'yneko_reimu_github_login_avatar_data', 1000, 2 );

function yneko_reimu_github_login_avatar_html( $avatar, $id_or_email, $size, $default, $alt, $args ) {
	$user_id = yneko_reimu_github_login_resolve_user_id( $id_or_email );
	if ( ! $user_id ) {
		return $avatar;
	}

	if ( function_exists( 'yneko_reimu_user_avatar_url' ) ) {
		$custom_avatar = yneko_reimu_user_avatar_url( $user_id );
		if ( $custom_avatar ) {
			return sprintf(
				'<img alt="%1$s" src="%2$s" class="%3$s" height="%4$d" width="%4$d" loading="lazy" decoding="async">',
				esc_attr( $alt ),
				esc_url( $custom_avatar ),
				esc_attr( 'avatar avatar-' . absint( $size ) . ' photo yneko-user-avatar' ),
				absint( $size )
			);
		}
	}

	if ( yneko_reimu_github_login_user_has_local_avatar( $user_id ) ) {
		return $avatar;
	}

	$url = yneko_reimu_github_login_avatar_url( $user_id, absint( $size ) );
	if ( ! $url ) {
		return $avatar;
	}

	$classes = array( 'avatar', 'avatar-' . absint( $size ), 'photo', 'yneko-github-avatar' );
	if ( ! empty( $args['class'] ) ) {
		$extra = is_array( $args['class'] ) ? $args['class'] : preg_split( '/\s+/', (string) $args['class'] );
		$classes = array_merge( $classes, array_filter( $extra ) );
	}

	return sprintf(
		'<img alt="%1$s" src="%2$s" class="%3$s" height="%4$d" width="%4$d" loading="lazy" decoding="async">',
		esc_attr( $alt ),
		esc_url( $url ),
		esc_attr( implode( ' ', array_unique( $classes ) ) ),
		absint( $size )
	);
}

add_filter( 'get_avatar', 'yneko_reimu_github_login_avatar_html', 1000, 6 );

function yneko_reimu_github_login_user_has_local_avatar( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return false;
	}

	$local_avatar_id = absint( get_user_meta( $user_id, 'wp_user_avatar', true ) );
	if ( $local_avatar_id && wp_get_attachment_url( $local_avatar_id ) ) {
		return true;
	}

	return false;
}

function yneko_reimu_github_login_avatar_url( $user_id, $size = 96 ) {
	$avatar = get_user_meta( $user_id, '_yneko_reimu_github_avatar_url', true );
	if ( ! $avatar ) {
		$avatar = get_user_meta( $user_id, '_yneko_github_avatar_url', true );
	}
	if ( ! $avatar ) {
		return '';
	}

	$separator = false === strpos( $avatar, '?' ) ? '?' : '&';
	return esc_url_raw( $avatar . $separator . 's=' . max( 1, absint( $size ) ) );
}

function yneko_reimu_github_login_resolve_user_id( $id_or_email ) {
	if ( $id_or_email instanceof WP_User ) {
		return absint( $id_or_email->ID );
	}

	if ( $id_or_email instanceof WP_Comment ) {
		return absint( $id_or_email->user_id );
	}

	if ( is_numeric( $id_or_email ) ) {
		return absint( $id_or_email );
	}

	if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
		return $user ? absint( $user->ID ) : 0;
	}

	return 0;
}
