<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_login_begin() {
	yneko_reimu_github_login_begin_oauth( false );
}

function yneko_reimu_github_login_begin_bind() {
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}

	check_admin_referer( 'yneko_reimu_github_bind', 'nonce' );
	yneko_reimu_github_login_begin_oauth( true );
}

function yneko_reimu_github_login_begin_oauth( $bind_current_user = false ) {
	$options = yneko_reimu_github_login_get_options();

	if ( empty( $options['client_id'] ) || empty( $options['client_secret'] ) ) {
		wp_die( esc_html__( 'GitHub login is not configured.', 'yneko-reimu' ), 403 );
	}

	$redirect_to = isset( $_GET['redirect_to'] ) ? sanitize_url( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$redirect_to = wp_validate_redirect( $redirect_to, home_url( '/' ) );
	$state       = wp_generate_password( 32, false, false );
	$state_key   = 'yneko_reimu_github_login_state_' . hash( 'sha256', $state );

	set_transient(
		$state_key,
		array(
			'redirect_to'  => $redirect_to,
			'link_user_id' => $bind_current_user ? get_current_user_id() : 0,
			'mode'         => $bind_current_user ? 'bind' : 'login',
			'popup'        => ! empty( $_GET['popup'] ) ? '1' : '0', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'created_at'   => time(),
		),
		10 * MINUTE_IN_SECONDS
	);

	$url = 'https://github.com/login/oauth/authorize?' . http_build_query(
		array(
			'client_id'    => $options['client_id'],
			'redirect_uri' => yneko_reimu_github_login_callback_url(),
			'scope'        => 'read:user user:email',
			'state'        => $state,
			'allow_signup' => 'true',
		),
		'',
		'&',
		PHP_QUERY_RFC3986
	);

	wp_redirect( $url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
	exit;
}

add_action( 'login_form_yneko_reimu_github_login', 'yneko_reimu_github_login_begin' );

add_action( 'login_form_yneko_reimu_github_bind', 'yneko_reimu_github_login_begin_bind' );

add_action( 'login_form_yneko_github_login', 'yneko_reimu_github_login_begin' );

function yneko_reimu_github_login_callback() {
	if ( isset( $_GET['error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_die( esc_html( sanitize_text_field( wp_unslash( $_GET['error_description'] ?? $_GET['error'] ) ) ), 403 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$code  = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $code || ! $state ) {
		wp_die( esc_html__( 'Missing GitHub OAuth response.', 'yneko-reimu' ), 400 );
	}

	$state_key = 'yneko_reimu_github_login_state_' . hash( 'sha256', $state );
	$payload   = get_transient( $state_key );
	delete_transient( $state_key );

	if ( ! is_array( $payload ) ) {
		wp_die( esc_html__( 'GitHub login state expired. Please try again.', 'yneko-reimu' ), 403 );
	}

	$token = yneko_reimu_github_login_exchange_code( $code );
	if ( is_wp_error( $token ) ) {
		wp_die( esc_html( $token->get_error_message() ), 403 );
	}

	$profile = yneko_reimu_github_login_github_get( 'https://api.github.com/user', $token );
	if ( is_wp_error( $profile ) ) {
		wp_die( esc_html( $profile->get_error_message() ), 403 );
	}

	$emails = yneko_reimu_github_login_github_get( 'https://api.github.com/user/emails', $token );
	if ( is_wp_error( $emails ) ) {
		$emails = array();
	}

	$user_id = yneko_reimu_github_login_find_or_create_user( $profile, $emails, absint( $payload['link_user_id'] ?? 0 ), ( $payload['mode'] ?? 'login' ) );
	if ( is_wp_error( $user_id ) ) {
		wp_die( esc_html( $user_id->get_error_message() ), 403 );
	}

	$user = get_user_by( 'id', $user_id );
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );
	do_action( 'wp_login', $user->user_login, $user );

	if ( ! empty( $payload['popup'] ) && '1' === (string) $payload['popup'] ) {
		yneko_reimu_github_login_popup_done( wp_validate_redirect( $payload['redirect_to'] ?? home_url( '/' ), home_url( '/' ) ) );
	}

	wp_safe_redirect( wp_validate_redirect( $payload['redirect_to'] ?? home_url( '/' ), home_url( '/' ) ) );
	exit;
}

add_action( 'login_form_yneko_reimu_github_callback', 'yneko_reimu_github_login_callback' );

add_action( 'login_form_yneko_github_callback', 'yneko_reimu_github_login_callback' );

function yneko_reimu_github_login_popup_done( $redirect_to ) {
	$origin = wp_parse_url( home_url( '/' ), PHP_URL_SCHEME ) . '://' . wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$port   = wp_parse_url( home_url( '/' ), PHP_URL_PORT );
	if ( $port ) {
		$origin .= ':' . $port;
	}

	nocache_headers();
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php esc_html_e( 'GitHub 登录成功', 'yneko-reimu' ); ?></title>
	</head>
	<body>
		<p><?php esc_html_e( 'GitHub 登录成功，正在返回评论区...', 'yneko-reimu' ); ?></p>
		<script>
			(function() {
				var payload = { type: 'yneko-reimu-github-login', success: true, redirectTo: <?php echo wp_json_encode( esc_url_raw( $redirect_to ) ); ?> };
				try {
					window.localStorage.setItem('yneko-reimu-github-login', JSON.stringify(Object.assign({ time: Date.now() }, payload)));
				} catch (error) {}
				if (window.opener && !window.opener.closed) {
					window.opener.postMessage(payload, <?php echo wp_json_encode( $origin ); ?>);
					window.close();
				} else {
					document.body.setAttribute('data-yneko-reimu-github-login-done', '1');
					window.setTimeout(function() {
						if (!window.closed) {
							window.close();
						}
					}, 120);
				}
			}());
		</script>
	</body>
	</html>
	<?php
	exit;
}

function yneko_reimu_github_login_exchange_code( $code ) {
	$options = yneko_reimu_github_login_get_options();

	$response = wp_remote_post(
		'https://github.com/login/oauth/access_token',
		array(
			'timeout' => 15,
			'headers' => array(
				'Accept' => 'application/json',
			),
			'body'    => array(
				'client_id'     => $options['client_id'],
				'client_secret' => $options['client_secret'],
				'code'          => $code,
				'redirect_uri'  => yneko_reimu_github_login_callback_url(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['access_token'] ) ) {
		return new WP_Error( 'yneko_github_token_failed', __( 'GitHub did not return an access token.', 'yneko-reimu' ) );
	}

	return sanitize_text_field( $body['access_token'] );
}

function yneko_reimu_github_login_github_get( $url, $token ) {
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 15,
			'headers' => array(
				'Accept'        => 'application/vnd.github+json',
				'Authorization' => 'Bearer ' . $token,
				'User-Agent'    => 'Yneko-WordPress-GitHub-Login',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( wp_remote_retrieve_response_code( $response ) >= 400 ) {
		return new WP_Error( 'yneko_github_api_failed', __( 'GitHub API request failed.', 'yneko-reimu' ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	return is_array( $body ) ? $body : array();
}
