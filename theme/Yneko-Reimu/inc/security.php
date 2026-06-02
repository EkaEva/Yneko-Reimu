<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_xmlrpc_enabled() {
	return (bool) apply_filters( 'yneko_reimu_xmlrpc_enabled', false );
}

function yneko_reimu_disable_xmlrpc( $enabled ) {
	return yneko_reimu_xmlrpc_enabled() ? $enabled : false;
}
add_filter( 'xmlrpc_enabled', 'yneko_reimu_disable_xmlrpc' );

function yneko_reimu_disable_xmlrpc_methods( $methods ) {
	return yneko_reimu_xmlrpc_enabled() ? $methods : array();
}
add_filter( 'xmlrpc_methods', 'yneko_reimu_disable_xmlrpc_methods' );

function yneko_reimu_remove_xmlrpc_rsd_link() {
	if ( ! yneko_reimu_xmlrpc_enabled() ) {
		remove_action( 'wp_head', 'rsd_link' );
	}
}
add_action( 'init', 'yneko_reimu_remove_xmlrpc_rsd_link' );

function yneko_reimu_remove_wordpress_generator() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
}
add_action( 'init', 'yneko_reimu_remove_wordpress_generator' );

function yneko_reimu_empty_generator() {
	return '';
}
add_filter( 'the_generator', 'yneko_reimu_empty_generator' );

function yneko_reimu_block_author_id_enumeration() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( ! isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$author = (string) wp_unslash( $_GET['author'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! preg_match( '/^\d+$/', $author ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'yneko_reimu_block_author_id_enumeration', 0 );

function yneko_reimu_security_headers() {
	if ( headers_sent() ) {
		return;
	}

	header_remove( 'X-Powered-By' );

	$headers = apply_filters(
		'yneko_reimu_security_headers',
		array(
			'X-Content-Type-Options' => 'nosniff',
			'X-Frame-Options'        => 'SAMEORIGIN',
			'Referrer-Policy'        => 'strict-origin-when-cross-origin',
		)
	);

	foreach ( $headers as $name => $value ) {
		$name  = sanitize_key( $name );
		$value = trim( (string) $value );
		if ( '' === $name || '' === $value ) {
			continue;
		}

		header( str_replace( '_', '-', $name ) . ': ' . $value );
	}
}
add_action( 'send_headers', 'yneko_reimu_security_headers' );
