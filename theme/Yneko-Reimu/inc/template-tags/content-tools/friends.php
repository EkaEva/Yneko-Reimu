<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_friend_items() {
	$settings_friends = yneko_reimu_settings_friend_items();
	if ( $settings_friends ) {
		return array_map(
			static function ( $friend ) {
				$friend['image'] = $friend['image'] ? $friend['image'] : yneko_reimu_get_default_avatar_url();
				return $friend;
			},
			$settings_friends
		);
	}

	$items = yneko_reimu_legacy_friend_items();
	return $items ? $items : yneko_reimu_settings_friend_items();
}

function yneko_reimu_legacy_friend_items() {
	$raw   = (string) yneko_reimu_get_theme_mod( 'yneko_reimu_friend_links', '' );
	$items = array();
	$seen  = array();

	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$item = yneko_reimu_legacy_friend_item_from_line( $line );
		if ( ! $item ) {
			continue;
		}

		$key = strtolower( untrailingslashit( $item['url'] ) );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$items[]      = $item;
	}

	return $items;
}

function yneko_reimu_legacy_friend_item_from_line( $line ) {
	$line = trim( $line );
	if ( '' === $line ) {
		return array();
	}

	$parts = array_pad( array_map( 'trim', explode( '|', $line ) ), 4, '' );
	if ( '' === $parts[0] || '' === $parts[1] ) {
		return array();
	}

	if ( ! preg_match( '#^https?://#i', $parts[1] ) ) {
		$parts[1] = 'https://' . ltrim( $parts[1], '/' );
	}

	return array(
		'name'  => $parts[0],
		'url'   => esc_url_raw( $parts[1] ),
		'desc'  => $parts[2],
		'image' => esc_url_raw( $parts[3] ? $parts[3] : yneko_reimu_get_default_avatar_url() ),
	);
}
