<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_login_default_options() {
	return array(
		'client_id'     => '',
		'client_secret' => '',
		'callback_url'  => '',
		'auto_create'   => '1',
	);
}

function yneko_reimu_github_login_get_options() {
	if ( function_exists( 'yneko_reimu_settings_github_oauth' ) ) {
		return wp_parse_args(
			yneko_reimu_settings_github_oauth(),
			yneko_reimu_github_login_default_options()
		);
	}

	$options = get_option( 'yneko_reimu_github_login_options', array() );

	if ( ! is_array( $options ) || ( empty( $options['client_id'] ) && empty( $options['client_secret'] ) ) ) {
		$legacy_options = get_option( 'yneko_github_login_options', array() );
		if ( is_array( $legacy_options ) && ( ! empty( $legacy_options['client_id'] ) || ! empty( $legacy_options['client_secret'] ) ) ) {
			$options = wp_parse_args( $options, $legacy_options );
		}
	}

	return wp_parse_args(
		$options,
		yneko_reimu_github_login_default_options()
	);
}

function yneko_reimu_github_login_is_configured() {
	$options = yneko_reimu_github_login_get_options();
	return ! empty( $options['client_id'] ) && ! empty( $options['client_secret'] );
}

function yneko_reimu_github_login_callback_url() {
	$options = yneko_reimu_github_login_get_options();
	if ( ! empty( $options['callback_url'] ) ) {
		return esc_url_raw( $options['callback_url'] );
	}

	return add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() );
}

function yneko_reimu_github_login_start_url( $redirect_to = '' ) {
	$args = array( 'action' => 'yneko_reimu_github_login' );

	if ( $redirect_to ) {
		$args['redirect_to'] = $redirect_to;
	}

	return add_query_arg( $args, wp_login_url() );
}

function yneko_reimu_github_login_bind_url( $redirect_to = '' ) {
	if ( ! is_user_logged_in() ) {
		return wp_login_url( $redirect_to );
	}

	$args = array(
		'action' => 'yneko_reimu_github_bind',
		'nonce'  => wp_create_nonce( 'yneko_reimu_github_bind' ),
	);

	if ( $redirect_to ) {
		$args['redirect_to'] = $redirect_to;
	}

	return add_query_arg( $args, wp_login_url() );
}

function yneko_reimu_github_login_sanitize_options( $input ) {
	$defaults = yneko_reimu_github_login_default_options();
	$input    = is_array( $input ) ? $input : array();

	return array(
		'client_id'     => isset( $input['client_id'] ) ? sanitize_text_field( $input['client_id'] ) : $defaults['client_id'],
		'client_secret' => isset( $input['client_secret'] ) ? sanitize_text_field( $input['client_secret'] ) : $defaults['client_secret'],
		'callback_url'  => isset( $input['callback_url'] ) ? esc_url_raw( $input['callback_url'] ) : $defaults['callback_url'],
		'auto_create'   => empty( $input['auto_create'] ) ? '0' : '1',
	);
}

function yneko_reimu_github_login_register_settings() {
	register_setting(
		'yneko_reimu_github_login',
		'yneko_reimu_github_login_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'yneko_reimu_github_login_sanitize_options',
			'default'           => yneko_reimu_github_login_default_options(),
		)
	);
}
add_action( 'admin_init', 'yneko_reimu_github_login_register_settings' );

function yneko_reimu_github_login_admin_menu() {
	return;

	add_theme_page(
		__( 'Yneko-Reimu GitHub Login', 'yneko-reimu' ),
		__( 'Yneko-Reimu GitHub Login', 'yneko-reimu' ),
		'manage_options',
		'yneko-reimu-github-login',
		'yneko_reimu_github_login_options_page'
	);
}
add_action( 'admin_menu', 'yneko_reimu_github_login_admin_menu' );

function yneko_reimu_github_login_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options = yneko_reimu_github_login_get_options();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Yneko-Reimu GitHub Login', 'yneko-reimu' ); ?></h1>
		<p><?php esc_html_e( 'Create a GitHub OAuth App and paste its credentials here. Keep the client secret private.', 'yneko-reimu' ); ?></p>
		<table class="widefat striped" style="max-width: 860px; margin: 16px 0;">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Callback URL', 'yneko-reimu' ); ?></th>
					<td><code><?php echo esc_html( yneko_reimu_github_login_callback_url() ); ?></code></td>
				</tr>
			</tbody>
		</table>
		<form method="post" action="options.php">
			<?php settings_fields( 'yneko_reimu_github_login' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="yneko-github-client-id"><?php esc_html_e( 'Client ID', 'yneko-reimu' ); ?></label></th>
					<td><input class="regular-text" id="yneko-github-client-id" name="yneko_reimu_github_login_options[client_id]" type="text" value="<?php echo esc_attr( $options['client_id'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-github-client-secret"><?php esc_html_e( 'Client Secret', 'yneko-reimu' ); ?></label></th>
					<td><input class="regular-text" id="yneko-github-client-secret" name="yneko_reimu_github_login_options[client_secret]" type="password" value="<?php echo esc_attr( $options['client_secret'] ); ?>" autocomplete="off"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Users', 'yneko-reimu' ); ?></th>
					<td>
						<label>
							<input name="yneko_reimu_github_login_options[auto_create]" type="checkbox" value="1" <?php checked( '1', $options['auto_create'] ); ?>>
							<?php esc_html_e( 'Create a subscriber account when a verified GitHub user logs in for the first time.', 'yneko-reimu' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function yneko_reimu_github_login_render_button( $redirect_to = '' ) {
	if ( ! yneko_reimu_github_login_is_configured() ) {
		return '';
	}

	$url = yneko_reimu_github_login_start_url( $redirect_to ? $redirect_to : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) );

	return sprintf(
		'<a class="yneko-reimu-button" href="%1$s" data-no-pjax><span class="yneko-reimu-icon" aria-hidden="true">%2$s</span><span>%3$s</span></a>',
		esc_url( $url ),
		yneko_reimu_github_login_icon(),
		esc_html__( '使用 GitHub 登录', 'yneko-reimu' )
	);
}

function yneko_reimu_github_login_render_reimu_button() {
	$redirect_to = is_singular() ? get_permalink() : home_url( '/' );
	$button      = yneko_reimu_github_login_render_button( $redirect_to );

	if ( ! $button ) {
		return;
	}

	echo '<div class="reimu-login-social">' . $button . '<div class="reimu-login-divider"><span>' . esc_html__( '或使用 WordPress 账号', 'yneko-reimu' ) . '</span></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'yneko_reimu_login_modal_social', 'yneko_reimu_github_login_render_reimu_button' );
add_action( 'reimu_wp_login_modal_social', 'yneko_reimu_github_login_render_reimu_button' );

function yneko_reimu_github_login_icon() {
	return '<svg viewBox="0 0 16 16" width="18" height="18" fill="currentColor" focusable="false" aria-hidden="true"><path d="M8 0C3.58 0 0 3.64 0 8.13c0 3.59 2.29 6.63 5.47 7.71.4.08.55-.18.55-.39 0-.19-.01-.83-.01-1.5-2.01.38-2.53-.5-2.69-.96-.09-.24-.48-.96-.82-1.15-.28-.15-.68-.53-.01-.54.63-.01 1.08.59 1.23.84.72 1.23 1.87.88 2.33.67.07-.53.28-.88.51-1.08-1.78-.21-3.64-.91-3.64-4.03 0-.89.31-1.62.82-2.19-.08-.21-.36-1.04.08-2.16 0 0 .67-.22 2.2.84A7.5 7.5 0 0 1 8 3.92c.68 0 1.36.09 2 .27 1.53-1.06 2.2-.84 2.2-.84.44 1.12.16 1.95.08 2.16.51.57.82 1.3.82 2.19 0 3.13-1.87 3.82-3.65 4.03.29.25.54.74.54 1.5 0 1.08-.01 1.95-.01 2.22 0 .21.15.47.55.39A8.03 8.03 0 0 0 16 8.13C16 3.64 12.42 0 8 0Z"></path></svg>';
}

function yneko_reimu_github_login_enqueue_styles() {
	$css = '
		.reimu-login-social { margin: 0 0 18px; }
		.yneko-reimu-button {
			display: inline-flex;
			width: 100%;
			min-height: 42px;
			box-sizing: border-box;
			align-items: center;
			justify-content: center;
			gap: 10px;
			padding: 0 18px;
			color: #fff !important;
			background: #24292f;
			border: 0;
			border-radius: 8px;
			box-shadow: 0 8px 18px rgba(36, 41, 47, .18);
			font-weight: 700;
			text-decoration: none !important;
			transition: opacity .2s ease, transform .2s ease, box-shadow .2s ease;
		}
		.yneko-reimu-button:hover {
			opacity: .92;
			transform: translateY(-1px);
			box-shadow: 0 10px 22px rgba(36, 41, 47, .24);
		}
		.yneko-reimu-icon { display: inline-flex; }
		.reimu-login-divider {
			display: flex;
			align-items: center;
			gap: 12px;
			margin: 16px 0 4px;
			color: #999;
			font-size: 13px;
		}
		.reimu-login-divider:before,
		.reimu-login-divider:after {
			content: "";
			height: 1px;
			flex: 1;
			background: rgba(255, 82, 82, .18);
		}
	';

	wp_register_style( 'yneko-reimu-github-login', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-github-login' );
	wp_add_inline_style( 'yneko-reimu-github-login', $css );
}
add_action( 'wp_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' );
add_action( 'login_enqueue_scripts', 'yneko_reimu_github_login_enqueue_styles' );

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

	$redirect_to = isset( $_GET['redirect_to'] ) ? wp_unslash( $_GET['redirect_to'] ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$redirect_to = wp_validate_redirect( $redirect_to, home_url( '/' ) );
	$state       = wp_generate_password( 32, false, false );
	$state_key   = 'yneko_reimu_github_login_state_' . hash( 'sha256', $state );

	set_transient(
		$state_key,
		array(
			'redirect_to'  => $redirect_to,
			'link_user_id' => $bind_current_user ? get_current_user_id() : 0,
			'mode'         => $bind_current_user ? 'bind' : 'login',
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

	wp_safe_redirect( wp_validate_redirect( $payload['redirect_to'] ?? home_url( '/' ), home_url( '/' ) ) );
	exit;
}
add_action( 'login_form_yneko_reimu_github_callback', 'yneko_reimu_github_login_callback' );
add_action( 'login_form_yneko_github_callback', 'yneko_reimu_github_login_callback' );

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
}

function yneko_reimu_github_login_avatar_data( $args, $id_or_email ) {
	$user_id = yneko_reimu_github_login_resolve_user_id( $id_or_email );
	if ( ! $user_id ) {
		return $args;
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
