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
