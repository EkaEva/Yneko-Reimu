<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_should_show_comments( $post_id = 0 ) {
	$post_id           = $post_id ? absint( $post_id ) : get_the_ID();
	$canonical_post_id = function_exists( 'yneko_reimu_comments_canonical_post_id' ) ? yneko_reimu_comments_canonical_post_id( $post_id ) : $post_id;
	$choice            = yneko_reimu_meta_choice( $post_id, '_yneko_reimu_comments' );

	if ( 'show' === $choice ) {
		return true;
	}

	if ( 'hide' === $choice ) {
		return false;
	}

	if ( function_exists( 'yneko_reimu_external_comment_systems' ) && yneko_reimu_external_comment_systems() ) {
		return true;
	}

	return comments_open( $canonical_post_id ) || get_comments_number( $canonical_post_id );
}

function yneko_reimu_reading_time( $post_id = 0 ) {
	$count   = yneko_reimu_content_word_count_parts( $post_id );
	$minutes = max( 1, (int) ceil( max( $count['words'] / 220, $count['chars'] / 500 ) ) );

	return sprintf(
		/* translators: %d: reading minutes. */
		esc_html__( '%d 分钟阅读', 'yneko-reimu' ),
		$minutes
	);
}

function yneko_reimu_word_count( $post_id = 0 ) {
	$count = yneko_reimu_plain_word_count( $post_id );
	if ( $count >= 1000 ) {
		return sprintf(
			/* translators: %s: word count in thousands. */
			esc_html__( '%s 字', 'yneko-reimu' ),
			esc_html( round( $count / 1000, 1 ) . 'k' )
		);
	}

	return sprintf(
		/* translators: %d: word count. */
		esc_html__( '%d 字', 'yneko-reimu' ),
		absint( $count )
	);
}

function yneko_reimu_plain_word_count( $post_id = 0 ) {
	$count = yneko_reimu_content_word_count_parts( $post_id, true );
	return absint( $count['words'] + $count['chars'] );
}

function yneko_reimu_content_word_count_parts( $post_id = 0, $strip_shortcodes = false ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$text    = get_post_field( 'post_content', $post_id );
	if ( $strip_shortcodes ) {
		$text = strip_shortcodes( $text );
	}
	$text = wp_strip_all_tags( $text );

	return array(
		'words' => str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) ),
		'chars' => preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text ),
	);
}

function yneko_reimu_heatmap_config() {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 500,
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	return array(
		'levelStandard' => '1000,5000,10000',
		'articleStats'  => yneko_reimu_heatmap_article_stats( $query->posts ),
		'i18n'          => yneko_reimu_heatmap_i18n(),
	);
}

function yneko_reimu_heatmap_article_stats( $post_ids ) {
	$stats = array();
	foreach ( $post_ids as $post_id ) {
		$stats[] = array(
			'title'     => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'url'       => get_permalink( $post_id ),
			'date'      => get_the_date( 'Y-m-d', $post_id ),
			'wordcount' => yneko_reimu_plain_word_count( $post_id ),
		);
	}
	return $stats;
}

function yneko_reimu_heatmap_i18n() {
	$en = array(
		'no_articles'    => 'No posts',
		'words'          => 'words',
		'total_articles' => '$1 post(s), $2 word(s) in total',
		'no_writing_on'  => 'No writing on {date}',
		'writing_on'     => '{posts}, {words} on {date}',
		'year_total'     => '{posts}, {words} in {year}',
	);

	return array(
		'zh-CN' => array(
			'no_articles'    => '没有文章',
			'words'          => '字',
			'total_articles' => '共 $1 篇文章, $2 字',
			'no_writing_on'  => '{date} 没有写作',
			'writing_on'     => '{posts} {words} 于 {date}',
			'year_total'     => '{posts} {words} 于 {year}',
		),
		'en-US' => $en,
		'en'    => $en,
	);
}

function yneko_reimu_render_heatmap() {
	?>
	<div id="heatmap" class="reimu-about-heatmap"></div>
	<script>
		window.REIMU_HEATMAP_CONFIG = <?php echo wp_json_encode( yneko_reimu_heatmap_config() ); ?>;
	</script>
	<?php
}

function yneko_reimu_excerpt( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$length  = max( 40, absint( yneko_reimu_get_theme_mod( 'yneko_reimu_excerpt_length', 150 ) ) );
	$text    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );

	return wp_trim_words( $text, $length, '...' );
}
