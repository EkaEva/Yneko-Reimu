<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_user_2fa_secret( $user_id ) {
	return (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_secret', true );
}

function yneko_reimu_user_2fa_enabled( $user_id ) {
	return '1' === (string) get_user_meta( absint( $user_id ), '_yneko_reimu_totp_enabled', true ) && '' !== yneko_reimu_user_2fa_secret( $user_id );
}

function yneko_reimu_totp_base32_chars() {
	return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
}

function yneko_reimu_totp_generate_secret( $length = 20 ) {
	$chars  = yneko_reimu_totp_base32_chars();
	$secret = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$secret .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
	}
	return $secret;
}

function yneko_reimu_totp_base32_decode( $secret ) {
	$secret = strtoupper( preg_replace( '/[^A-Z2-7]/', '', (string) $secret ) );
	$chars  = yneko_reimu_totp_base32_chars();
	$bits   = '';
	$bytes  = '';

	for ( $i = 0; $i < strlen( $secret ); $i++ ) {
		$index = strpos( $chars, $secret[ $i ] );
		if ( false === $index ) {
			continue;
		}
		$bits .= str_pad( decbin( $index ), 5, '0', STR_PAD_LEFT );
	}

	for ( $i = 0; $i + 8 <= strlen( $bits ); $i += 8 ) {
		$bytes .= chr( bindec( substr( $bits, $i, 8 ) ) );
	}

	return $bytes;
}

function yneko_reimu_totp_code( $secret, $time_slice = null ) {
	$time_slice = null === $time_slice ? floor( time() / 30 ) : (int) $time_slice;
	$key        = yneko_reimu_totp_base32_decode( $secret );
	if ( '' === $key ) {
		return '';
	}

	$counter = pack( 'N*', 0 ) . pack( 'N*', $time_slice );
	$hash    = hash_hmac( 'sha1', $counter, $key, true );
	$offset  = ord( substr( $hash, -1 ) ) & 0x0f;
	$value   = unpack( 'N', substr( $hash, $offset, 4 ) )[1] & 0x7fffffff;
	return str_pad( (string) ( $value % 1000000 ), 6, '0', STR_PAD_LEFT );
}

function yneko_reimu_totp_verify( $secret, $code ) {
	$code = preg_replace( '/\D+/', '', (string) $code );
	if ( ! preg_match( '/^\d{6}$/', $code ) || '' === $secret ) {
		return false;
	}

	$slice = floor( time() / 30 );
	for ( $i = -1; $i <= 1; $i++ ) {
		if ( hash_equals( yneko_reimu_totp_code( $secret, $slice + $i ), $code ) ) {
			return true;
		}
	}
	return false;
}

function yneko_reimu_totp_uri( $user_id, $secret ) {
	$user  = get_userdata( absint( $user_id ) );
	$email = $user ? $user->user_email : '';
	$label = rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . ':' . $email );
	return 'otpauth://totp/' . $label . '?secret=' . rawurlencode( $secret ) . '&issuer=' . rawurlencode( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) . '&algorithm=SHA1&digits=6&period=30';
}

function yneko_reimu_ajax_profile_totp_generate() {
	check_ajax_referer( 'yneko_reimu_profile', 'nonce' );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	yneko_reimu_ajax_set_language_from_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先登录。', 'yneko-reimu' ) ), 401 );
	}

	$secret = yneko_reimu_totp_generate_secret();
	update_user_meta( get_current_user_id(), '_yneko_reimu_totp_pending_secret', $secret );
	wp_send_json_success(
		array(
			'secret' => $secret,
			'uri'    => yneko_reimu_totp_uri( get_current_user_id(), $secret ),
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_profile_totp_generate', 'yneko_reimu_ajax_profile_totp_generate' );
