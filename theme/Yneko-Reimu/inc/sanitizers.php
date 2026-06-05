<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function yneko_reimu_sanitize_select( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control ? $control->choices : array();
	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

function yneko_reimu_sanitize_positive_int( $value ) {
	return max( 0, absint( $value ) );
}

function yneko_reimu_sanitize_preloader_image_size( $value ) {
	return max( 48, min( 320, absint( $value ) ) );
}

function yneko_reimu_sanitize_base_font_size( $value ) {
	return max( 14, min( 20, absint( $value ) ) );
}

function yneko_reimu_sanitize_article_font_size( $value ) {
	return max( 14, min( 22, absint( $value ) ) );
}

function yneko_reimu_sanitize_article_line_height( $value ) {
	return max( 140, min( 220, absint( $value ) ) );
}

function yneko_reimu_sanitize_content_max_width( $value ) {
	return max( 960, min( 1800, absint( $value ) ) );
}

function yneko_reimu_sanitize_article_content_width( $value ) {
	if ( 0 === absint( $value ) ) {
		return 0;
	}

	return max( 640, min( 1100, absint( $value ) ) );
}

function yneko_reimu_sanitize_radius_px( $value ) {
	return max( 0, min( 32, absint( $value ) ) );
}

function yneko_reimu_sanitize_url_or_empty( $value ) {
	$value = trim( (string) $value );
	return '' === $value ? '' : esc_url_raw( $value );
}

function yneko_reimu_sanitize_social_url_or_empty( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( is_email( $value ) ) {
		return 'mailto:' . sanitize_email( $value );
	}

	return esc_url_raw( $value );
}

function yneko_reimu_sanitize_url_base_or_empty( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	return esc_url_raw( untrailingslashit( $value ) );
}

function yneko_reimu_sanitize_sidebar_widget_order( $value ) {
	$allowed = array( 'tagcloud', 'projects', 'recent_posts', 'categories', 'archives', 'recent_comments' );
	$items   = array();

	foreach ( explode( ',', (string) $value ) as $item ) {
		$item = sanitize_key( trim( $item ) );
		if ( in_array( $item, $allowed, true ) && ! in_array( $item, $items, true ) ) {
			$items[] = $item;
		}
	}

	foreach ( $allowed as $item ) {
		if ( ! in_array( $item, $items, true ) ) {
			$items[] = $item;
		}
	}

	return implode( ',', $items );
}
