<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_ip_region_lookup_enabled() {
	if ( function_exists( 'yneko_reimu_security_comment_ip_region_lookup' ) ) {
		return yneko_reimu_security_comment_ip_region_lookup();
	}

	if ( function_exists( 'yneko_reimu_settings_security' ) ) {
		$security = yneko_reimu_settings_security();
		return '1' === (string) ( $security['comment_ip_region_lookup'] ?? '1' );
	}

	return true;
}

function yneko_reimu_comment_region_from_ip( $ip ) {
	if ( ! yneko_reimu_comment_ip_region_lookup_enabled() ) {
		return '';
	}

	$ip = trim( (string) $ip );

	if ( '' === $ip ) {
		return '';
	}

	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		return '';
	}

	$cache_key = 'reimu_comment_region_' . md5( $ip );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return (string) $cached;
	}

	$region   = '';
	$response = wp_remote_get(
		'https://ipwho.is/' . rawurlencode( $ip ) . '?lang=zh-CN',
		array(
			'timeout'     => 2,
			'redirection' => 1,
			'user-agent'  => 'Yneko-Reimu/' . ( defined( 'YNEKO_REIMU_VERSION' ) ? YNEKO_REIMU_VERSION : '1.0' ),
		)
	);

	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $data ) && ! empty( $data['success'] ) ) {
			$country_code = isset( $data['country_code'] ) ? strtoupper( (string) $data['country_code'] ) : '';
			$connection   = isset( $data['connection'] ) && is_array( $data['connection'] ) ? $data['connection'] : array();
			if ( 'CN' === $country_code ) {
				$region = ! empty( $data['region'] ) ? (string) $data['region'] : ( ! empty( $data['city'] ) ? (string) $data['city'] : '' );
				$region = preg_replace( '/(省|市|自治区|壮族自治区|回族自治区|维吾尔自治区)$/u', '', $region );
			}
			if ( '' === $region && ! empty( $connection['org'] ) ) {
				$region = (string) $connection['org'];
			}
			if ( '' === $region && ! empty( $connection['isp'] ) ) {
				$region = (string) $connection['isp'];
			}
			if ( '' === $region ) {
				$region = ! empty( $data['country'] ) ? (string) $data['country'] : '';
			}
		}
	}

	$region = trim( wp_strip_all_tags( $region ) );
	$region = mb_substr( $region, 0, 24 );
	set_transient( $cache_key, $region, 30 * DAY_IN_SECONDS );

	return $region;
}

function yneko_reimu_comment_agent_badges( $agent, $ip = '' ) {
	$agent   = (string) $agent;
	$badges  = array();
	$region  = yneko_reimu_comment_region_from_ip( $ip );
	$browser = '';
	$system  = '';

	if ( $region ) {
		$badges[] = $region;
	}

	if ( preg_match( '/Edg(?:e|A|iOS)?\/([0-9.]+)/i', $agent, $matches ) ) {
		$browser = 'Edge' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Chrome\/([0-9.]+)/i', $agent, $matches ) && false === stripos( $agent, 'Chromium' ) ) {
		$browser = 'Chrome' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Firefox\/([0-9.]+)/i', $agent, $matches ) ) {
		$browser = 'Firefox' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/Version\/([0-9.]+).*Safari/i', $agent, $matches ) ) {
		$browser = 'Safari' . yneko_reimu_comment_major_minor_version( $matches[1] );
	} elseif ( preg_match( '/MSIE\s([0-9.]+)|Trident\/.*rv:([0-9.]+)/i', $agent, $matches ) ) {
		$version  = ! empty( $matches[1] ) ? $matches[1] : $matches[2];
		$browser = 'IE' . yneko_reimu_comment_major_minor_version( $version );
	}

	if ( preg_match( '/Windows NT 10/i', $agent ) ) {
		$system = 'Windows 11';
	} elseif ( preg_match( '/Windows NT 6\.3/i', $agent ) ) {
		$system = 'Windows 8.1';
	} elseif ( preg_match( '/Windows NT 6\.1/i', $agent ) ) {
		$system = 'Windows 7';
	} elseif ( preg_match( '/Android\s?([0-9.]*)/i', $agent, $matches ) ) {
		$system = 'Android' . ( ! empty( $matches[1] ) ? ' ' . yneko_reimu_comment_major_minor_version( $matches[1] ) : '' );
	} elseif ( preg_match( '/iPhone|iPad|iPod/i', $agent ) ) {
		$system = 'iOS';
	} elseif ( preg_match( '/Mac OS X\s?([0-9_\.]*)/i', $agent, $matches ) ) {
		$system = 'macOS' . ( ! empty( $matches[1] ) ? ' ' . yneko_reimu_comment_major_minor_version( str_replace( '_', '.', $matches[1] ) ) : '' );
	} elseif ( preg_match( '/Linux/i', $agent ) ) {
		$system = 'Linux';
	}

	if ( $browser ) {
		$badges[] = $browser;
	}

	if ( $system ) {
		$badges[] = $system;
	}

	$badges = array_values( array_unique( array_filter( $badges ) ) );

	if ( empty( $badges ) ) {
		return '';
	}

	$html = '<span class="reimu-comment__badges" aria-label="' . esc_attr__( '评论环境信息', 'yneko-reimu' ) . '">';
	foreach ( $badges as $badge ) {
		$html .= '<span class="reimu-comment__badge">' . esc_html( $badge ) . '</span>';
	}
	$html .= '</span>';

	return $html;
}

function yneko_reimu_comment_major_minor_version( $version ) {
	$parts = preg_split( '/[._]/', (string) $version );
	$major = isset( $parts[0] ) && '' !== $parts[0] ? preg_replace( '/\D+/', '', $parts[0] ) : '';
	$minor = isset( $parts[1] ) && '' !== $parts[1] ? preg_replace( '/\D+/', '', $parts[1] ) : '0';

	if ( '' === $major ) {
		return '';
	}

	return $major . '.' . ( '' === $minor ? '0' : $minor );
}
