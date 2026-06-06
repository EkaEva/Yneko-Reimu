<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_post_is_sticky( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$meta    = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_sticky', true );

	if ( '1' === $meta ) {
		return true;
	}

	if ( '0' === $meta ) {
		return false;
	}

	$source_result = yneko_reimu_source_post_sticky_state( $post_id );
	return null === $source_result ? is_sticky( $post_id ) : $source_result;
}

function yneko_reimu_source_post_sticky_state( $post_id ) {
	if ( ! function_exists( 'yneko_reimu_get_visual_source_post_id' ) ) {
		return null;
	}

	$source_id = yneko_reimu_get_visual_source_post_id( $post_id );
	if ( ! $source_id ) {
		return null;
	}

	$source_meta = yneko_reimu_get_post_meta( $source_id, '_yneko_reimu_sticky', true );
	if ( '1' === $source_meta ) {
		return true;
	}

	if ( '0' === $source_meta ) {
		return false;
	}

	return is_sticky( $source_id );
}

function yneko_reimu_total_word_count() {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$total = 0;

	foreach ( $query->posts as $post_id ) {
		$count  = yneko_reimu_content_word_count_parts( $post_id );
		$total += $count['words'] + $count['chars'];
	}

	return $total >= 1000 ? round( $total / 1000, 1 ) . 'k' : (string) $total;
}

function yneko_reimu_render_taichi_svg( $size = 150 ) {
	?>
	<svg width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" aria-hidden="true" focusable="false">
		<path d="M303.5 432A80 80 0 0 1 291.5 592A80 80 0 0 1 303.5 432z" fill="var(--red-1, #ff5252)" />
		<path d="M512 65A447 447 0 0 1 512 959L512 929A417 417 0 0 0 512 95A417 417 0 0 0 512 929L512 959A447 447 0 0 1 512 65z M512 95A417 417 0 0 1 929 512A208.5 208.5 0 0 1 720.5 720.5L720.5 592A80 80 0 0 0 720.5 432A80 80 0 0 0 720.5 592L720.5 720.5A208.5 208.5 0 0 1 512 512A208.5 208.5 0 0 0 303.5 303.5A208.5 208.5 0 0 0 95 512A417 417 0 0 1 512 95z" fill="var(--red-1, #ff5252)" />
	</svg>
	<?php
}
