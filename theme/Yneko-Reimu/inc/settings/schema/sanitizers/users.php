<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_sanitize_user_badge_label( $label ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	$label = preg_replace( '/[\r\n\t]+/u', ' ', $label );
	$label = preg_replace( '/\s{2,}/u', ' ', $label );
	return mb_substr( trim( $label ), 0, 12 );
}

function yneko_reimu_sanitize_user_badges_settings( $input, $defaults ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array(
		'enabled'        => ! empty( $input['enabled'] ) ? '1' : '0',
		'review_enabled' => ! empty( $input['review_enabled'] ) ? '1' : '0',
		'blocklist'      => yneko_reimu_sanitize_user_badge_blocklist( $input['blocklist'] ?? '' ),
		'avatar_frames'  => array(
			'enabled' => ! empty( $input['avatar_frames']['enabled'] ) ? '1' : '0',
			'frames'  => array(),
		),
		'special'        => array(),
	);

	$special_input = isset( $input['special'] ) && is_array( $input['special'] ) ? $input['special'] : array();
	$frame_input   = isset( $input['avatar_frames']['frames'] ) && is_array( $input['avatar_frames']['frames'] ) ? $input['avatar_frames']['frames'] : array();
	foreach ( array_keys( yneko_reimu_user_badge_base_definitions() ) as $key ) {
		$definition = yneko_reimu_user_badge_base_definitions()[ $key ];
		$default    = $defaults['special'][ $key ] ?? array(
			'enabled' => '1',
			'zh'      => $definition['zh'],
			'en'      => $definition['en'],
		);
		$row        = isset( $special_input[ $key ] ) && is_array( $special_input[ $key ] ) ? $special_input[ $key ] : array();
		$zh         = yneko_reimu_sanitize_user_badge_label( $row['zh'] ?? $default['zh'] );
		$en         = yneko_reimu_sanitize_user_badge_label( $row['en'] ?? $default['en'] );
		if ( '' === $zh && '' === $en ) {
			$zh = $default['zh'];
			$en = $default['en'];
		}
		$clean['special'][ $key ] = array(
			'enabled' => ! empty( $row['enabled'] ) ? '1' : '0',
			'zh'      => $zh,
			'en'      => $en,
		);
		$frame_url = yneko_reimu_normalize_avatar_frame_url( $frame_input[ $key ] ?? ( $defaults['avatar_frames']['frames'][ $key ] ?? yneko_reimu_default_avatar_frame_url() ) );
		$clean['avatar_frames']['frames'][ $key ] = $frame_url ? $frame_url : yneko_reimu_default_avatar_frame_url();
	}

	return $clean;
}

function yneko_reimu_sanitize_user_badge_blocklist( $value ) {
	$items = preg_split( '#/+#u', (string) $value );
	$clean = array();
	foreach ( $items as $item ) {
		$item = yneko_reimu_sanitize_user_badge_label( $item );
		if ( '' !== $item ) {
			$clean[] = $item;
		}
	}
	return implode( '/', array_values( array_unique( $clean ) ) );
}
