<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_comment_markdown( $text ) {
	$text        = str_replace( array( "\r\n", "\r" ), "\n", (string) $text );
	$code_blocks = array();

	$text = preg_replace_callback(
		'/```\s*(?:[a-z0-9_-]+)?[^\n]*\n?([\s\S]*?)```/i',
		function ( $matches ) use ( &$code_blocks ) {
			$key                 = '@@REIMU_COMMENT_CODE_' . count( $code_blocks ) . '@@';
			$code_blocks[ $key ] = '<pre><code>' . esc_html( trim( $matches[1], "\n" ) ) . '</code></pre>';
			return "\n" . $key . "\n";
		},
		$text
	);

	$html = esc_html( $text );
	$html = preg_replace_callback(
		'/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) {
			$url = yneko_reimu_comment_resolve_image_url( html_entity_decode( $matches[2] ) );
			return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( html_entity_decode( $matches[1] ) ) . '" loading="lazy" decoding="async">';
		},
		$html
	);
	$html = preg_replace_callback(
		'/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/i',
		function ( $matches ) {
			return '<a href="' . esc_url( html_entity_decode( $matches[2] ) ) . '" rel="nofollow noopener noreferrer" target="_blank">' . esc_html( html_entity_decode( $matches[1] ) ) . '</a>';
		},
		$html
	);
	$html = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $html );
	$html = preg_replace( '/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $html );
	$html = wpautop( $html );

	foreach ( $code_blocks as $key => $block ) {
		$html = str_replace( '<p>' . esc_html( $key ) . '</p>', $block, $html );
		$html = str_replace( esc_html( $key ), $block, $html );
	}

	return wp_kses_post( $html );
}
