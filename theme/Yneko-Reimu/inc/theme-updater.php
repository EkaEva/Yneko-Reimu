<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_theme_updater_repo() {
	return 'EkaEva/Yneko-Reimu';
}

function yneko_reimu_theme_updater_api_url() {
	return 'https://api.github.com/repos/' . yneko_reimu_theme_updater_repo() . '/releases/latest';
}

function yneko_reimu_theme_updater_cache_key() {
	return 'yneko_reimu_github_release_update';
}

function yneko_reimu_theme_update_settings() {
	$updates = yneko_reimu_settings_group( 'updates' );

	return wp_parse_args(
		$updates,
		array(
			'github_release_check' => '1',
			'cache_minutes'        => 360,
		)
	);
}

function yneko_reimu_theme_update_check_enabled() {
	$settings = yneko_reimu_theme_update_settings();
	return '1' === (string) ( $settings['github_release_check'] ?? '1' );
}

function yneko_reimu_theme_update_cache_minutes() {
	$settings = yneko_reimu_theme_update_settings();
	return max( 5, min( 4320, absint( $settings['cache_minutes'] ?? 360 ) ) );
}

function yneko_reimu_theme_updater_normalize_version( $version ) {
	$version = trim( (string) $version );
	$version = preg_replace( '/^[vV]/', '', $version );

	return preg_match( '/^\d+(?:\.\d+){1,3}(?:[-+][0-9A-Za-z.-]+)?$/', $version ) ? $version : '';
}

function yneko_reimu_theme_updater_expected_asset_name( $version ) {
	return $version ? 'Yneko-Reimu-v' . $version . '.zip' : '';
}

function yneko_reimu_theme_updater_asset_url( $release, $version ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}

	$expected = yneko_reimu_theme_updater_expected_asset_name( $version );
	foreach ( $release['assets'] as $asset ) {
		if ( ! is_array( $asset ) ) {
			continue;
		}

		$name = isset( $asset['name'] ) ? (string) $asset['name'] : '';
		$url  = isset( $asset['browser_download_url'] ) ? esc_url_raw( $asset['browser_download_url'] ) : '';
		if ( $expected === $name && $url ) {
			return $url;
		}
	}

	return '';
}

function yneko_reimu_theme_updater_status_defaults() {
	$checked_at    = time();
	$cache_minutes = yneko_reimu_theme_update_cache_minutes();

	return array(
		'ok'           => false,
		'error_code'   => '',
		'message'      => '',
		'version'      => '',
		'package'      => '',
		'url'          => '',
		'asset_name'   => '',
		'checked_at'   => $checked_at,
		'expires_at'   => $checked_at + ( $cache_minutes * MINUTE_IN_SECONDS ),
		'cache_minutes'=> $cache_minutes,
		'requires'     => '',
		'requires_php'=> '',
	);
}

function yneko_reimu_theme_updater_status( $args = array() ) {
	return wp_parse_args( is_array( $args ) ? $args : array(), yneko_reimu_theme_updater_status_defaults() );
}

function yneko_reimu_theme_updater_success_status( $release, $version, $package ) {
	return yneko_reimu_theme_updater_status(
		array(
			'ok'         => true,
			'message'    => __( 'GitHub Release update check completed.', 'yneko-reimu' ),
			'version'    => $version,
			'package'    => $package,
			'url'        => esc_url_raw( $release['html_url'] ?? 'https://github.com/' . yneko_reimu_theme_updater_repo() . '/releases/tag/v' . $version ),
			'asset_name' => yneko_reimu_theme_updater_expected_asset_name( $version ),
		)
	);
}

function yneko_reimu_theme_updater_error_status( $error_code, $message, $args = array() ) {
	return yneko_reimu_theme_updater_status(
		array_merge(
			is_array( $args ) ? $args : array(),
			array(
				'ok'         => false,
				'error_code' => sanitize_key( $error_code ),
				'message'    => (string) $message,
			)
		)
	);
}

function yneko_reimu_theme_updater_fetch_status() {
	$response = wp_remote_get(
		yneko_reimu_theme_updater_api_url(),
		array(
			'timeout'    => 5,
			'user-agent' => 'Yneko-Reimu/' . YNEKO_REIMU_VERSION . '; ' . home_url( '/' ),
			'headers'    => array(
				'Accept' => 'application/vnd.github+json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return yneko_reimu_theme_updater_error_status(
			'wp_http_error',
			$response->get_error_message()
		);
	}

	$status_code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $status_code ) {
		return yneko_reimu_theme_updater_error_status(
			'http_error',
			sprintf(
				/* translators: %d: HTTP status code. */
				__( 'GitHub API returned HTTP %d.', 'yneko-reimu' ),
				$status_code
			)
		);
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $release ) ) {
		return yneko_reimu_theme_updater_error_status(
			'invalid_json',
			__( 'GitHub API response was not valid JSON.', 'yneko-reimu' )
		);
	}

	if ( ! empty( $release['draft'] ) || ! empty( $release['prerelease'] ) ) {
		return yneko_reimu_theme_updater_error_status(
			'unstable_release',
			__( 'The latest GitHub Release is a draft or prerelease.', 'yneko-reimu' )
		);
	}

	$version = yneko_reimu_theme_updater_normalize_version( $release['tag_name'] ?? '' );
	if ( ! $version ) {
		return yneko_reimu_theme_updater_error_status(
			'invalid_tag',
			__( 'The latest GitHub Release tag is not a valid theme version.', 'yneko-reimu' )
		);
	}

	$asset_name = yneko_reimu_theme_updater_expected_asset_name( $version );
	$package    = yneko_reimu_theme_updater_asset_url( $release, $version );
	if ( ! $package ) {
		return yneko_reimu_theme_updater_error_status(
			'missing_asset',
			sprintf(
				/* translators: %s: expected release asset name. */
				__( 'The latest GitHub Release is missing %s.', 'yneko-reimu' ),
				$asset_name
			),
			array(
				'version'    => $version,
				'url'        => esc_url_raw( $release['html_url'] ?? '' ),
				'asset_name' => $asset_name,
			)
		);
	}

	return yneko_reimu_theme_updater_success_status( $release, $version, $package );
}

function yneko_reimu_theme_updater_normalize_cached_status( $cached ) {
	if ( ! is_array( $cached ) ) {
		return false;
	}

	if ( array_key_exists( 'ok', $cached ) || array_key_exists( 'checked_at', $cached ) || array_key_exists( 'error_code', $cached ) ) {
		return yneko_reimu_theme_updater_status( $cached );
	}

	if ( ! empty( $cached['version'] ) && ! empty( $cached['package'] ) ) {
		return yneko_reimu_theme_updater_status(
			array(
				'ok'         => true,
				'message'    => __( 'Cached GitHub Release update result.', 'yneko-reimu' ),
				'version'    => (string) $cached['version'],
				'package'    => esc_url_raw( $cached['package'] ),
				'url'        => esc_url_raw( $cached['url'] ?? '' ),
				'asset_name' => yneko_reimu_theme_updater_expected_asset_name( (string) $cached['version'] ),
				'requires'   => (string) ( $cached['requires'] ?? '' ),
				'requires_php'=> (string) ( $cached['requires_php'] ?? '' ),
			)
		);
	}

	return false;
}

function yneko_reimu_theme_updater_get_cached_status() {
	return yneko_reimu_theme_updater_normalize_cached_status( get_site_transient( yneko_reimu_theme_updater_cache_key() ) );
}

function yneko_reimu_theme_updater_cached_status( $force = false ) {
	if ( ! $force ) {
		$cached = yneko_reimu_theme_updater_get_cached_status();
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$status = yneko_reimu_theme_updater_fetch_status();
	set_site_transient( yneko_reimu_theme_updater_cache_key(), $status, yneko_reimu_theme_update_cache_minutes() * MINUTE_IN_SECONDS );

	return $status;
}

function yneko_reimu_theme_updater_cached_release() {
	$status = yneko_reimu_theme_updater_cached_status();
	if ( empty( $status['ok'] ) || empty( $status['version'] ) || empty( $status['package'] ) ) {
		return false;
	}

	return array(
		'version'     => $status['version'],
		'package'     => $status['package'],
		'url'         => $status['url'],
		'requires'    => $status['requires'],
		'requires_php'=> $status['requires_php'],
	);
}

function yneko_reimu_theme_updater_delete_caches() {
	delete_site_transient( yneko_reimu_theme_updater_cache_key() );
	delete_site_transient( 'update_themes' );
}

function yneko_reimu_theme_updater_update_transient( $transient ) {
	if ( ! is_object( $transient ) || ! yneko_reimu_theme_update_check_enabled() ) {
		return $transient;
	}

	if ( ! is_admin() && ! wp_doing_cron() ) {
		return $transient;
	}

	$stylesheet = get_template();
	$current    = yneko_reimu_theme_updater_normalize_version( YNEKO_REIMU_VERSION );
	$status     = yneko_reimu_theme_updater_cached_status();

	if ( empty( $status['ok'] ) || empty( $status['version'] ) || empty( $status['package'] ) || ! version_compare( $status['version'], $current, '>' ) ) {
		if ( isset( $transient->response[ $stylesheet ] ) ) {
			unset( $transient->response[ $stylesheet ] );
		}
		return $transient;
	}

	$transient->response[ $stylesheet ] = array(
		'theme'        => $stylesheet,
		'new_version'  => $status['version'],
		'url'          => $status['url'],
		'package'      => $status['package'],
		'requires'     => $status['requires'],
		'requires_php' => $status['requires_php'],
	);

	return $transient;
}
add_filter( 'site_transient_update_themes', 'yneko_reimu_theme_updater_update_transient' );

function yneko_reimu_theme_updater_clear_cache_on_settings_save() {
	yneko_reimu_theme_updater_delete_caches();
}
add_action( 'updated_option_yneko_reimu_settings', 'yneko_reimu_theme_updater_clear_cache_on_settings_save' );

function yneko_reimu_theme_updater_admin_action_url( $action ) {
	return wp_nonce_url(
		add_query_arg(
			array(
				'page'                      => 'yneko-reimu-settings',
				'yneko_theme_update_action' => sanitize_key( $action ),
			),
			admin_url( 'themes.php' )
		),
		'yneko_reimu_theme_update_' . sanitize_key( $action )
	);
}

function yneko_reimu_theme_updater_admin_notice_key() {
	return 'yneko_theme_update_notice';
}

function yneko_reimu_theme_updater_handle_admin_action() {
	if ( ! is_admin() || empty( $_GET['yneko_theme_update_action'] ) ) {
		return;
	}

	$action = sanitize_key( wp_unslash( $_GET['yneko_theme_update_action'] ) );
	if ( ! in_array( $action, array( 'force_check', 'clear_cache' ), true ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage theme updates.', 'yneko-reimu' ) );
	}

	check_admin_referer( 'yneko_reimu_theme_update_' . $action );
	yneko_reimu_theme_updater_delete_caches();

	$notice = 'cleared';
	if ( 'force_check' === $action ) {
		$status = yneko_reimu_theme_updater_cached_status( true );
		$notice = ! empty( $status['ok'] ) ? 'checked' : 'failed';
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'                                    => 'yneko-reimu-settings',
				yneko_reimu_theme_updater_admin_notice_key() => $notice,
			),
			admin_url( 'themes.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'yneko_reimu_theme_updater_handle_admin_action' );

function yneko_reimu_theme_updater_admin_notices() {
	if ( empty( $_GET[ yneko_reimu_theme_updater_admin_notice_key() ] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$notice = sanitize_key( wp_unslash( $_GET[ yneko_reimu_theme_updater_admin_notice_key() ] ) );
	$type   = 'success';
	$text   = __( 'Theme update cache was cleared.', 'yneko-reimu' );

	if ( 'checked' === $notice ) {
		$text = __( 'GitHub Release update check completed.', 'yneko-reimu' );
	} elseif ( 'failed' === $notice ) {
		$type = 'warning';
		$text = __( 'GitHub Release update check completed with a visible failure reason below.', 'yneko-reimu' );
	}
	?>
	<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
		<p><?php echo esc_html( $text ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'yneko_reimu_theme_updater_admin_notices' );
