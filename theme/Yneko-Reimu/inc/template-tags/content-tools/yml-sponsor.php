<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_should_show_wp_widgets() {
	return false;
}

function yneko_reimu_should_show_clone_widgets() {
	return yneko_reimu_theme_mod_bool( 'yneko_reimu_strict_clone', true );
}

function yneko_reimu_yml_value_html( $value ) {
	$value = (string) $value;

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/^\s*#/', $value ) ) {
		return '<span class="comment">' . esc_html( $value ) . '</span>';
	}

	return '<span class="string">' . esc_html( $value ) . '</span>';
}

function yneko_reimu_yml_line_html( $line ) {
	$line = (string) $line;

	if ( preg_match( '/^```/', $line ) ) {
		return '<span class="string">' . esc_html( $line ) . '</span>';
	}

	if ( preg_match( '/^(\s*)(-\s+)?([A-Za-z0-9_-]+:)(\s*)(.*)$/u', $line, $matches ) ) {
		return yneko_reimu_yml_key_value_html( $matches );
	}

	return esc_html( $line );
}

function yneko_reimu_yml_key_value_html( $matches ) {
	$html = esc_html( $matches[1] );
	if ( '' !== $matches[2] ) {
		$html .= '<span class="bullet">-</span> ';
	}

	return $html . '<span class="attr">' . esc_html( $matches[3] ) . '</span>' . esc_html( $matches[4] ) . yneko_reimu_yml_value_html( $matches[5] );
}

function yneko_reimu_yml_editor( $code, $args = array() ) {
	$args = wp_parse_args( $args, array( 'lang' => 'YML', 'class' => '' ) );
	$code = trim( str_replace( array( "\r\n", "\r" ), "\n", (string) $code ), "\n" );
	if ( '' === $code ) {
		return '';
	}

	$lines        = explode( "\n", $code );
	$line_numbers = array();
	$code_lines   = array();
	foreach ( $lines as $index => $line ) {
		$line_numbers[] = '<span class="line">' . esc_html( (string) ( $index + 1 ) ) . '</span>';
		$code_lines[]   = '<span class="line">' . yneko_reimu_yml_line_html( $line ) . '</span>';
	}

	$extra_class = trim( (string) $args['class'] );
	$class_attr  = 'highlight yml reimu-yml-editor' . ( $extra_class ? ' ' . $extra_class : '' );

	return sprintf(
		'<figure class="%1$s" data-copy-text="%2$s"><div class="code-figcaption"><div class="code-left-wrap"><div class="code-decoration"></div><div class="code-lang">%3$s</div></div><div class="code-right-wrap"><button type="button" class="code-copy icon-copy" aria-label="%4$s"></button><button type="button" class="icon-chevron-down code-expand" aria-label="%5$s" aria-expanded="true"></button></div></div><div class="code-area"><table><tr><td class="gutter"><pre>%6$s<br></pre></td><td class="code"><pre>%7$s<br></pre></td></tr></table></div></figure>',
		esc_attr( $class_attr ),
		esc_attr( $code ),
		esc_html( strtoupper( (string) $args['lang'] ) ),
		esc_attr__( '复制', 'yneko-reimu' ),
		esc_attr__( '折叠代码', 'yneko-reimu' ),
		implode( '<br>', $line_numbers ),
		implode( '<br>', $code_lines )
	);
}

function yneko_reimu_sponsor_html() {
	$qr = yneko_reimu_get_sponsor_qr_url();
	if ( ! $qr ) {
		return '';
	}

	return sprintf(
		'<footer class="article-footer"><div class="sponsor-wrapper" data-aos="zoom-in"><div class="sponsor-button" role="button" tabindex="0" aria-expanded="false"><div class="sponsor-icon rotate"></div><div class="sponsor-title">%1$s</div><div class="sponsor-icon rotate"></div></div><div class="sponsor-tip">%2$s</div><div class="sponsor-qr"><div class="sponsor-qr-image-wrapper"><img class="lazyload no-lightbox" src="%3$s" data-src="%3$s" alt="%4$s" width="170"><p class="sponsor-qr-name">%5$s</p></div></div></div></footer>',
		esc_html__( '赞助', 'yneko-reimu' ),
		esc_html__( '无限进步', 'yneko-reimu' ),
		esc_url( $qr ),
		esc_attr__( '赞助二维码', 'yneko-reimu' ),
		esc_html__( '赞助', 'yneko-reimu' )
	);
}

function yneko_reimu_sponsor_shortcode() {
	return yneko_reimu_sponsor_html();
}
add_shortcode( 'yneko_reimu_sponsor', 'yneko_reimu_sponsor_shortcode' );
