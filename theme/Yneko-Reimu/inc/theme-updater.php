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

function yneko_reimu_theme_updater_asset_url( $release, $version ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}

	$expected = 'Yneko-Reimu-v' . $version . '.zip';
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

function yneko_reimu_theme_updater_fetch_release() {
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

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $release ) || ! empty( $release['draft'] ) || ! empty( $release['prerelease'] ) ) {
		return false;
	}

	$version = yneko_reimu_theme_updater_normalize_version( $release['tag_name'] ?? '' );
	if ( ! $version ) {
		return false;
	}

	$package = yneko_reimu_theme_updater_asset_url( $release, $version );
	if ( ! $package ) {
		return false;
	}

	return array(
		'version'     => $version,
		'package'     => $package,
		'url'         => esc_url_raw( $release['html_url'] ?? 'https://github.com/' . yneko_reimu_theme_updater_repo() . '/releases/tag/v' . $version ),
		'requires'    => '',
		'requires_php'=> '',
	);
}

function yneko_reimu_theme_updater_cached_release() {
	$cached = get_site_transient( yneko_reimu_theme_updater_cache_key() );
	if ( is_array( $cached ) ) {
		return ! empty( $cached['version'] ) ? $cached : false;
	}

	$release = yneko_reimu_theme_updater_fetch_release();
	set_site_transient( yneko_reimu_theme_updater_cache_key(), $release ? $release : array(), yneko_reimu_theme_update_cache_minutes() * MINUTE_IN_SECONDS );

	return $release;
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
	$release    = yneko_reimu_theme_updater_cached_release();

	if ( ! $release || empty( $release['version'] ) || ! version_compare( $release['version'], $current, '>' ) ) {
		if ( isset( $transient->response[ $stylesheet ] ) ) {
			unset( $transient->response[ $stylesheet ] );
		}
		return $transient;
	}

	$transient->response[ $stylesheet ] = array(
		'theme'        => $stylesheet,
		'new_version'  => $release['version'],
		'url'          => $release['url'],
		'package'      => $release['package'],
		'requires'     => $release['requires'],
		'requires_php' => $release['requires_php'],
	);

	return $transient;
}
add_filter( 'site_transient_update_themes', 'yneko_reimu_theme_updater_update_transient' );

function yneko_reimu_theme_updater_clear_cache_on_settings_save() {
	delete_site_transient( yneko_reimu_theme_updater_cache_key() );
}
add_action( 'updated_option_yneko_reimu_settings', 'yneko_reimu_theme_updater_clear_cache_on_settings_save' );
