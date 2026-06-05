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
		<label for="yneko-reimu-login-totp-code"><?php esc_html_e( '认证器验证码', 'yneko-reimu' ); ?></label>
		<input id="yneko-reimu-login-totp-code" class="input" name="yneko_reimu_login_totp_code" type="text" value="<?php echo esc_attr( $value ); ?>" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" size="20">
		<span class="description"><?php esc_html_e( '如果当前账号已开启二次认证，请输入认证器中的 6 位验证码。', 'yneko-reimu' ); ?></span>
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
		return yneko_reimu_login_2fa_error();
	}

	if ( ! yneko_reimu_totp_verify( yneko_reimu_user_2fa_secret( $user->ID ), $code ) ) {
		return yneko_reimu_login_2fa_error();
	}

	return $user;
}
add_filter( 'authenticate', 'yneko_reimu_login_2fa_authenticate', 30 );
