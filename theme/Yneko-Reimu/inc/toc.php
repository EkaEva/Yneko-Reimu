<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_heading_id( $text, &$used ) {
	$base = sanitize_title( wp_strip_all_tags( $text ) );

	if ( '' === $base ) {
		$base = 'section';
	}

	$id = $base;
	$i  = 2;

	while ( in_array( $id, $used, true ) ) {
		$id = $base . '-' . $i;
		$i++;
	}

	$used[] = $id;
	return $id;
}

function yneko_reimu_parse_headings( $content, $inject_anchor = false ) {
	$headings = array();
	$used     = array();

	$content = preg_replace_callback(
		'/<h([2-6])([^>]*)>(.*?)<\/h\1>/is',
		static function ( $matches ) use ( &$headings, &$used, $inject_anchor ) {
			$level = (int) $matches[1];
			$attrs = $matches[2];
			$inner = $matches[3];
			$text  = trim( wp_strip_all_tags( $inner ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attrs, $id_match ) ) {
				$id = $id_match[2];
				$used[] = $id;
			} else {
				$id    = yneko_reimu_heading_id( $text, $used );
				$attrs = rtrim( $attrs ) . ' id="' . esc_attr( $id ) . '"';
			}

			$headings[] = array(
				'level' => $level,
					'id'    => $id,
					'text'  => $text,
				);

			if ( $inject_anchor && false === strpos( $inner, 'paragraph-anchor' ) ) {
				$inner = '<a class="paragraph-anchor" href="#' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'anchor', 'yneko-reimu' ) . '"></a>' . $inner;
			}

			return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
		},
		$content
	);

	return array(
		'content'  => $content,
		'headings' => $headings,
	);
}

function yneko_reimu_add_heading_ids( $content ) {
	if ( is_singular() && in_the_loop() && is_main_query() ) {
		$parsed = yneko_reimu_parse_headings( $content, true );
		return $parsed['content'];
	}

	return $content;
}
add_filter( 'the_content', 'yneko_reimu_add_heading_ids', 9 );

function yneko_reimu_toc_source_content( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );

	if ( function_exists( 'do_blocks' ) ) {
		$content = do_blocks( $content );
	}

	return do_shortcode( $content );
}

function yneko_reimu_generate_toc( $content ) {
	$parsed = yneko_reimu_parse_headings( $content );

	if ( empty( $parsed['headings'] ) ) {
		return '';
	}

	$base_level = min( wp_list_pluck( $parsed['headings'], 'level' ) );
	$current    = 1;
	$open_item  = false;

	$output = '<h3 class="toc-title">' . esc_html__( '文章目录', 'yneko-reimu' ) . '</h3>';
	$output .= '<div class="sidebar-toc-wrapper toc-div-class">';
	$output .= '<ol class="toc">';

	foreach ( $parsed['headings'] as $heading ) {
		$depth = max( 1, (int) $heading['level'] - (int) $base_level + 1 );

		if ( $depth > $current ) {
			while ( $depth > $current ) {
				$output .= '<ol class="toc-child">';
				$current++;
			}
		} elseif ( $depth < $current ) {
			if ( $open_item ) {
				$output .= '</li>';
				$open_item = false;
			}

			while ( $depth < $current ) {
				$output .= '</ol></li>';
				$current--;
			}
		} elseif ( $open_item ) {
			$output .= '</li>';
			$open_item = false;
		}

		$output .= sprintf(
			'<li class="toc-item toc-level-%d"><a class="toc-link" href="#%s"><span class="toc-text">%s</span></a>',
			absint( $heading['level'] ),
			esc_attr( $heading['id'] ),
			esc_html( $heading['text'] )
		);
		$open_item = true;
	}

	if ( $open_item ) {
		$output .= '</li>';
	}

	while ( $current > 1 ) {
		$output .= '</ol></li>';
		$current--;
	}

	$output .= '</ol></div>';

	return $output;
}

function yneko_reimu_get_post_toc( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( ! yneko_reimu_should_show_toc( $post_id ) ) {
		return '';
	}

	static $cache = array();

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$cache[ $post_id ] = yneko_reimu_generate_toc( yneko_reimu_toc_source_content( $post_id ) );
	return $cache[ $post_id ];
}

function yneko_reimu_post_has_toc( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return '' !== yneko_reimu_get_post_toc( $post_id );
}

function yneko_reimu_the_toc( $post_id = 0 ) {
	$toc = yneko_reimu_get_post_toc( $post_id );

	if ( $toc ) {
		echo wp_kses(
			$toc,
			array(
				'h3'   => array( 'class' => true ),
				'div'  => array( 'class' => true ),
				'ol'   => array( 'class' => true ),
				'li'   => array( 'class' => true ),
				'a'    => array(
					'class' => true,
					'href'  => true,
				),
				'span' => array( 'class' => true ),
			)
		);
	}
}
