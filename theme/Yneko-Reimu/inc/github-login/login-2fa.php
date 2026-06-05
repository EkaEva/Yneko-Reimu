<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_login_2fa_is_wp_login_request() {
	$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
	if ( 'post' !== $request_method ) {
		return false;
	}

	$script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) : '';
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( false === strpos( $script_name . ' ' . $request_uri, 'wp-login.php' ) ) {
		return false;
	}

	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return '' === $action || 'login' === $action;
}

function yneko_reimu_login_2fa_field() {
	$value = isset( $_POST['yneko_reimu_login_totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['yneko_reimu_login_totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	?>
	<p class="yneko-reimu-login-totp">
		<label for="yneko-reimu-login-totp-code"><?php esc_html_e( '认证器验证码或恢复码', 'yneko-reimu' ); ?></label>
		<input id="yneko-reimu-login-totp-code" class="input" name="yneko_reimu_login_totp_code" type="text" value="<?php echo esc_attr( $value ); ?>" inputmode="text" autocomplete="one-time-code" pattern="[0-9A-Za-z -]{6,23}" maxlength="23" size="20">
		<span class="description"><?php esc_html_e( '如果当前账号已开启二次认证，请输入认证器中的 6 位验证码，或输入一次性恢复码。', 'yneko-reimu' ); ?></span>
	</p>
	<?php
}
add_action( 'login_form', 'yneko_reimu_login_2fa_field' );

function yneko_reimu_login_2fa_error() {
	return new WP_Error(
		'yneko_reimu_login_2fa_failed',
		esc_html__( '登录信息或二次验证码不正确。', 'yneko-reimu' )
	);
}

function yneko_reimu_login_2fa_recovery_meta_key() {
	return '_yneko_reimu_totp_recovery_codes';
}

function yneko_reimu_login_2fa_normalize_recovery_code( $code ) {
	return strtoupper( preg_replace( '/[^A-Za-z0-9]+/', '', (string) $code ) );
}

function yneko_reimu_login_2fa_random_recovery_token( $length = 16 ) {
	$alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
	$max      = strlen( $alphabet ) - 1;
	$token    = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$index  = function_exists( 'random_int' ) ? random_int( 0, $max ) : wp_rand( 0, $max );
		$token .= $alphabet[ $index ];
	}
	return $token;
}

function yneko_reimu_login_2fa_generate_recovery_codes( $count = 10 ) {
	$count = max( 1, min( 20, absint( $count ) ) );
	$codes = array();
	while ( count( $codes ) < $count ) {
		$token = yneko_reimu_login_2fa_random_recovery_token();
		$code  = implode( '-', str_split( $token, 4 ) );
		if ( ! in_array( $code, $codes, true ) ) {
			$codes[] = $code;
		}
	}
	return $codes;
}

function yneko_reimu_login_2fa_store_recovery_codes( $user_id, $codes ) {
	$hashes = array();
	foreach ( (array) $codes as $code ) {
		$normalized = yneko_reimu_login_2fa_normalize_recovery_code( $code );
		if ( strlen( $normalized ) < 12 ) {
			continue;
		}
		$hashes[] = wp_hash_password( $normalized );
	}
	update_user_meta( absint( $user_id ), yneko_reimu_login_2fa_recovery_meta_key(), $hashes );
}

function yneko_reimu_login_2fa_recovery_code_count( $user_id ) {
	$codes = get_user_meta( absint( $user_id ), yneko_reimu_login_2fa_recovery_meta_key(), true );
	return is_array( $codes ) ? count( array_filter( $codes, 'is_string' ) ) : 0;
}

function yneko_reimu_login_2fa_clear_recovery_codes( $user_id ) {
	delete_user_meta( absint( $user_id ), yneko_reimu_login_2fa_recovery_meta_key() );
}

function yneko_reimu_login_2fa_consume_recovery_code( $user_id, $code ) {
	$normalized = yneko_reimu_login_2fa_normalize_recovery_code( $code );
	if ( strlen( $normalized ) < 12 ) {
		return false;
	}

	$hashes = get_user_meta( absint( $user_id ), yneko_reimu_login_2fa_recovery_meta_key(), true );
	if ( ! is_array( $hashes ) || ! $hashes ) {
		return false;
	}

	foreach ( $hashes as $index => $hash ) {
		if ( is_string( $hash ) && wp_check_password( $normalized, $hash, absint( $user_id ) ) ) {
			unset( $hashes[ $index ] );
			update_user_meta( absint( $user_id ), yneko_reimu_login_2fa_recovery_meta_key(), array_values( $hashes ) );
			return true;
		}
	}

	return false;
}

function yneko_reimu_login_2fa_authenticate( $user ) {
	if ( is_wp_error( $user ) || ! $user instanceof WP_User ) {
		return $user;
	}

	if ( ! yneko_reimu_login_2fa_is_wp_login_request() ) {
		return $user;
	}

	if ( ! function_exists( 'yneko_reimu_user_2fa_enabled' ) || ! function_exists( 'yneko_reimu_user_2fa_secret' ) || ! function_exists( 'yneko_reimu_totp_verify' ) ) {
		return $user;
	}

	if ( ! yneko_reimu_user_2fa_enabled( $user->ID ) ) {
		return $user;
	}

	$code = isset( $_POST['yneko_reimu_login_totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['yneko_reimu_login_totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! preg_match( '/^\d{6}$/', $code ) ) {
		$raw_code = isset( $_POST['yneko_reimu_login_totp_code'] ) ? (string) wp_unslash( $_POST['yneko_reimu_login_totp_code'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return yneko_reimu_login_2fa_consume_recovery_code( $user->ID, $raw_code ) ? $user : yneko_reimu_login_2fa_error();
	}

	if ( ! yneko_reimu_totp_verify( yneko_reimu_user_2fa_secret( $user->ID ), $code ) ) {
		$raw_code = isset( $_POST['yneko_reimu_login_totp_code'] ) ? (string) wp_unslash( $_POST['yneko_reimu_login_totp_code'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return yneko_reimu_login_2fa_consume_recovery_code( $user->ID, $raw_code ) ? $user : yneko_reimu_login_2fa_error();
	}

	return $user;
}
add_filter( 'authenticate', 'yneko_reimu_login_2fa_authenticate', 30 );
