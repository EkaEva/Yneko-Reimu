<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_github_username() {
	$github = trim( (string) yneko_reimu_settings_github_url() );
	if ( '' === $github ) {
		return '';
	}

	$path = trim( (string) wp_parse_url( $github, PHP_URL_PATH ), '/' );
	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return '';
	}

	$username = preg_replace( '/[^A-Za-z0-9-]/', '', $path );
	return $username ? $username : '';
}

function yneko_reimu_project_fallback_items() {
	$username = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	return array(
		array(
			'name'        => $username,
			'url'         => 'https://github.com/' . rawurlencode( $username ),
			'desc'        => __( '我的 GitHub 主页与项目索引。', 'yneko-reimu' ),
			'image'       => yneko_reimu_get_default_avatar_url(),
			'language'    => 'GitHub',
			'stars'       => 0,
			'updated_at'  => '',
			'is_fallback' => true,
		),
	);
}

function yneko_reimu_normalize_github_repo_items( $repos, $limit = 12 ) {
	usort( $repos, 'yneko_reimu_compare_github_repo_items' );

	$items = array();
	foreach ( $repos as $repo ) {
		$item = yneko_reimu_github_repo_item( $repo );
		if ( ! $item ) {
			continue;
		}

		$items[] = $item;
		if ( count( $items ) >= $limit ) {
			break;
		}
	}

	return $items;
}

function yneko_reimu_compare_github_repo_items( $a, $b ) {
	$a_fork = ! empty( $a['fork'] ) ? 1 : 0;
	$b_fork = ! empty( $b['fork'] ) ? 1 : 0;

	if ( $a_fork !== $b_fork ) {
		return $a_fork <=> $b_fork;
	}

	return strcmp( (string) ( $b['updated_at'] ?? '' ), (string) ( $a['updated_at'] ?? '' ) );
}

function yneko_reimu_github_repo_item( $repo ) {
	if ( empty( $repo['name'] ) || empty( $repo['html_url'] ) ) {
		return array();
	}

	$owner = isset( $repo['owner'] ) && is_array( $repo['owner'] ) ? $repo['owner'] : array();
	return array(
		'name'       => (string) $repo['name'],
		'url'        => esc_url_raw( (string) $repo['html_url'] ),
		'desc'       => ! empty( $repo['description'] ) ? (string) $repo['description'] : __( 'GitHub 项目', 'yneko-reimu' ),
		'image'      => ! empty( $owner['avatar_url'] ) ? esc_url_raw( (string) $owner['avatar_url'] ) : yneko_reimu_get_default_avatar_url(),
		'language'   => ! empty( $repo['language'] ) ? (string) $repo['language'] : '',
		'stars'      => isset( $repo['stargazers_count'] ) ? absint( $repo['stargazers_count'] ) : 0,
		'updated_at' => ! empty( $repo['updated_at'] ) ? (string) $repo['updated_at'] : '',
		'is_fork'    => ! empty( $repo['fork'] ),
	);
}

function yneko_reimu_github_api_get( $path, $transient_key, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get( 'https://api.github.com' . $path, array( 'timeout' => 4, 'headers' => yneko_reimu_github_api_headers() ) );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	set_transient( $transient_key, $data, $cache_seconds );
	return $data;
}

function yneko_reimu_github_api_headers() {
	$token = defined( 'YNEKO_REIMU_GITHUB_TOKEN' ) ? trim( (string) YNEKO_REIMU_GITHUB_TOKEN ) : '';
	$token = trim( (string) apply_filters( 'yneko_reimu_github_token', $token ) );
	$headers = array(
		'Accept'     => 'application/vnd.github+json',
		'User-Agent' => 'yneko-reimu/' . wp_get_theme()->get( 'Version' ) . '; ' . home_url( '/' ),
	);
	if ( '' !== $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}

	return $headers;
}

function yneko_reimu_github_api_get_pages( $path_template, $transient_key, $pages = 1, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$items = yneko_reimu_github_api_page_items( $path_template, $transient_key, $pages, $cache_seconds );
	if ( ! $items ) {
		$items = $fallback;
	}

	set_transient( $transient_key, $items, $cache_seconds );
	return $items;
}

function yneko_reimu_github_api_page_items( $path_template, $transient_key, $pages, $cache_seconds ) {
	$items = array();
	$pages = max( 1, min( 5, absint( $pages ) ) );

	for ( $page = 1; $page <= $pages; $page++ ) {
		$path = str_replace( '%d', (string) $page, $path_template );
		$data = yneko_reimu_github_api_get( $path, $transient_key . '_page_' . $page, array(), $cache_seconds );
		if ( ! is_array( $data ) || ! $data ) {
			break;
		}
		$items = array_merge( $items, $data );
		if ( count( $data ) < 100 ) {
			break;
		}
	}

	return $items;
}

function yneko_reimu_github_projects() {
	$username = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_projects_' . md5( strtolower( $username ) );
	$repos         = yneko_reimu_github_api_get_pages( '/users/' . rawurlencode( $username ) . '/repos?sort=updated&per_page=100&page=%d', $transient_key . '_raw_v2', 1, array(), 6 * HOUR_IN_SECONDS );
	$items         = yneko_reimu_normalize_github_repo_items( $repos, 48 );

	if ( ! $items ) {
		$items = yneko_reimu_project_fallback_items();
	}

	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );
	return $items;
}

function yneko_reimu_github_starred_projects() {
	$username = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_starred_projects_' . md5( strtolower( $username ) );
	$repos         = yneko_reimu_github_api_get_pages( '/users/' . rawurlencode( $username ) . '/starred?sort=updated&per_page=100&page=%d', $transient_key . '_raw_v2', 3, array(), 6 * HOUR_IN_SECONDS );
	$items         = yneko_reimu_normalize_github_repo_items( $repos, 240 );
	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );

	return $items;
}
