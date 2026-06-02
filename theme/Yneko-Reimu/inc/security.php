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
