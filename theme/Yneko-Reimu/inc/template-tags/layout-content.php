<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_theme_mod_bool( $name, $default = true ) {
	if ( function_exists( 'yneko_reimu_settings_feature_enabled' ) ) {
		return yneko_reimu_settings_feature_enabled( $name, yneko_reimu_feature_default( $name, $default ) );
	}

	return (bool) yneko_reimu_get_theme_mod( $name, yneko_reimu_feature_default( $name, $default ) );
}

function yneko_reimu_meta_choice( $post_id, $key, $default = 'inherit' ) {
	$value = yneko_reimu_get_post_meta( $post_id, $key, true );
	return $value ? $value : $default;
}

function yneko_reimu_is_meta_enabled( $post_id, $meta_key, $theme_mod_key, $default = true ) {
	$choice = yneko_reimu_meta_choice( $post_id, $meta_key );

	if ( 'show' === $choice ) {
		return true;
	}

	if ( 'hide' === $choice ) {
		return false;
	}

	return yneko_reimu_theme_mod_bool( $theme_mod_key, $default );
}

function yneko_reimu_sidebar_position( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : ( is_singular() ? absint( get_queried_object_id() ) : 0 );
	$choice  = $post_id ? yneko_reimu_meta_choice( $post_id, '_yneko_reimu_sidebar', 'inherit' ) : 'inherit';

	if ( in_array( $choice, array( 'left', 'right', 'disabled' ), true ) ) {
		return $choice;
	}

	$position = yneko_reimu_get_theme_mod( 'yneko_reimu_sidebar_position', 'left' );
	return in_array( $position, array( 'left', 'right', 'disabled' ), true ) ? $position : 'left';
}

function yneko_reimu_should_show_sidebar( $post_id = 0 ) {
	return 'disabled' !== yneko_reimu_sidebar_position( $post_id );
}

function yneko_reimu_should_show_toc( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return is_singular( 'post' ) && yneko_reimu_is_meta_enabled( $post_id, '_yneko_reimu_toc', 'yneko_reimu_show_toc', true );
}

function yneko_reimu_special_page_slug( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : ( is_singular( 'page' ) ? absint( get_queried_object_id() ) : 0 );

	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return '';
	}

	$slug = get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, array( 'about', 'projects', 'archives', 'friend' ), true ) ) {
		return '';
	}

	return function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ? '' : $slug;
}

function yneko_reimu_force_disabled_builtin_page_404() {
	if ( is_admin() || ! is_singular( 'page' ) ) {
		return;
	}

	$post_id = absint( get_queried_object_id() );
	if ( ! $post_id ) {
		return;
	}

	$slug = get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, array( 'about', 'projects', 'archives', 'friend' ), true ) || ! function_exists( 'yneko_reimu_builtin_page_enabled' ) || yneko_reimu_builtin_page_enabled( $slug ) ) {
		return;
	}

	global $wp_query;
	if ( $wp_query instanceof WP_Query && method_exists( $wp_query, 'set_404' ) ) {
		$wp_query->set_404();
	}
	status_header( 404 );
	nocache_headers();
}

add_action( 'template_redirect', 'yneko_reimu_force_disabled_builtin_page_404', 0 );

function yneko_reimu_term_count_by_slug( $taxonomy, $slug ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	return absint( $term->count );
}

function yneko_reimu_term_count_with_children_by_slug( $taxonomy, $slug ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$term_ids = array( absint( $term->term_id ) );

	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$children = get_term_children( $term->term_id, $taxonomy );

		if ( ! is_wp_error( $children ) ) {
			$term_ids = array_merge( $term_ids, array_map( 'absint', $children ) );
		}
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'term_id',
					'terms'            => array_unique( $term_ids ),
					'include_children' => false,
				),
			),
		)
	);

	return absint( $query->found_posts );
}

function yneko_reimu_category_link_by_slug( $slug, $fallback = '' ) {
	$term = get_term_by( 'slug', $slug, 'category' );

	if ( $term && ! is_wp_error( $term ) ) {
		return get_category_link( $term );
	}

	return $fallback ? $fallback : home_url( '/category/' . trim( $slug, '/' ) . '/' );
}

function yneko_reimu_post_link_parent_category( $category, $categories, $post ) {
	if ( ! $category || is_wp_error( $category ) ) {
		return $category;
	}

	while ( ! empty( $category->parent ) ) {
		$parent = get_category( $category->parent );

		if ( ! $parent || is_wp_error( $parent ) ) {
			break;
		}

		$category = $parent;
	}

	return $category;
}

add_filter( 'post_link_category', 'yneko_reimu_post_link_parent_category', 10, 3 );

function yneko_reimu_count_text( $count ) {
	return sprintf(
		/* translators: %d: post count. */
		_n( '%d 篇文章', '%d 篇文章', absint( $count ), 'yneko-reimu' ),
		absint( $count )
	);
}

function yneko_reimu_get_adjacent_post_for_language( $post_id = 0, $previous = true, $language = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return null;
	}

	$source_id = function_exists( 'yneko_reimu_i18n_source_post_id' ) ? yneko_reimu_i18n_source_post_id( $post_id ) : $post_id;
	$current  = get_post( $source_id );
	if ( ! $current || 'post' !== $current->post_type ) {
		return null;
	}

	$language = $language && function_exists( 'yneko_reimu_i18n_language_exists' ) && yneko_reimu_i18n_language_exists( $language )
		? $language
		: ( function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : '' );

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'orderby'                => array(
			'date' => $previous ? 'DESC' : 'ASC',
			'ID'   => $previous ? 'DESC' : 'ASC',
		),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'post__not_in'           => array( $source_id ),
		'date_query'             => array(
			array(
				$previous ? 'before' : 'after' => get_post_time( 'Y-m-d H:i:s', false, $source_id ),
				'inclusive' => false,
			),
		),
	);

	if ( function_exists( 'yneko_reimu_i18n_apply_language_query_args' ) ) {
		$args = yneko_reimu_i18n_apply_language_query_args( $args, 'zh_CN' );
	}

	$query = new WP_Query( $args );
	if ( ! $query->posts ) {
		return null;
	}

	$adjacent_id = absint( $query->posts[0]->ID );
	if ( function_exists( 'yneko_reimu_i18n_display_post_for_language' ) && $language ) {
		$adjacent_id = yneko_reimu_i18n_display_post_for_language( $adjacent_id, $language );
	}

	return get_post( $adjacent_id );
}

function yneko_reimu_should_show_comments( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$canonical_post_id = function_exists( 'yneko_reimu_comments_canonical_post_id' ) ? yneko_reimu_comments_canonical_post_id( $post_id ) : $post_id;
	$choice  = yneko_reimu_meta_choice( $post_id, '_yneko_reimu_comments' );

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
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$text    = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$words   = str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) );
	$chars   = preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text );
	$minutes = max( 1, (int) ceil( max( $words / 220, $chars / 500 ) ) );

	return sprintf(
		/* translators: %d: reading minutes. */
		esc_html__( '%d 分钟阅读', 'yneko-reimu' ),
		$minutes
	);
}

function yneko_reimu_word_count( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$text    = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$words   = str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) );
	$chars   = preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text );
	$count   = $words + $chars;

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
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$text    = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
	$words   = str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) );
	$chars   = preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text );

	return absint( $words + $chars );
}

function yneko_reimu_heatmap_config() {
	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 500,
		'orderby'                => 'date',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	$query = new WP_Query( $args );
	$stats = array();

	foreach ( $query->posts as $post_id ) {
		$stats[] = array(
			'title'     => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'url'       => get_permalink( $post_id ),
			'date'      => get_the_date( 'Y-m-d', $post_id ),
			'wordcount' => yneko_reimu_plain_word_count( $post_id ),
		);
	}

	return array(
		'levelStandard' => '1000,5000,10000',
		'articleStats'  => $stats,
		'i18n'          => array(
			'zh-CN' => array(
				'no_articles'    => '没有文章',
				'words'          => '字',
				'total_articles' => '共 $1 篇文章, $2 字',
				'no_writing_on'  => '{date} 没有写作',
				'writing_on'     => '{posts} {words} 于 {date}',
				'year_total'     => '{posts} {words} 于 {year}',
			),
			'en-US' => array(
				'no_articles'    => 'No posts',
				'words'          => 'words',
				'total_articles' => '$1 post(s), $2 word(s) in total',
				'no_writing_on'  => 'No writing on {date}',
				'writing_on'     => '{posts}, {words} on {date}',
				'year_total'     => '{posts}, {words} in {year}',
			),
			'en'    => array(
				'no_articles'    => 'No posts',
				'words'          => 'words',
				'total_articles' => '$1 post(s), $2 word(s) in total',
				'no_writing_on'  => 'No writing on {date}',
				'writing_on'     => '{posts}, {words} on {date}',
				'year_total'     => '{posts}, {words} in {year}',
			),
		),
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

	if ( has_excerpt( $post_id ) ) {
		$text = get_the_excerpt( $post_id );
	} else {
		$text = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
	}

	return wp_trim_words( $text, $length, '...' );
}

function yneko_reimu_archive_title() {
	if ( yneko_reimu_is_virtual_page() ) {
		$page = yneko_reimu_virtual_page();
		return $page['title'];
	}

	$special_slug = yneko_reimu_special_page_slug();
	if ( $special_slug ) {
		$pages = yneko_reimu_virtual_pages();
		if ( isset( $pages[ $special_slug ] ) ) {
			return $pages[ $special_slug ]['title'];
		}
	}

	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query. */
			esc_html__( '搜索：%s', 'yneko-reimu' ),
			get_search_query()
		);
	}

	if ( is_404() ) {
		return esc_html__( '404（´◔ ₃ ◔`)', 'yneko-reimu' );
	}

	if ( is_archive() ) {
		return wp_strip_all_tags( get_the_archive_title() );
	}

	if ( is_home() && ! is_front_page() ) {
		return get_the_title( get_option( 'page_for_posts' ) );
	}

	return get_bloginfo( 'name' );
}

function yneko_reimu_archive_description() {
	if ( yneko_reimu_is_virtual_page() ) {
		$page = yneko_reimu_virtual_page();
		return $page['description'];
	}

	$special_slug = yneko_reimu_special_page_slug();
	if ( $special_slug ) {
		$pages = yneko_reimu_virtual_pages();
		if ( isset( $pages[ $special_slug ] ) ) {
			return $pages[ $special_slug ]['description'];
		}
	}

	if ( is_search() ) {
		return esc_html__( '以下是与你输入关键词相关的文章。', 'yneko-reimu' );
	}

	if ( is_404() ) {
		return esc_html__( '少年，你迷路了吗？', 'yneko-reimu' );
	}

	if ( is_archive() ) {
		return get_the_archive_description();
	}

	return get_bloginfo( 'description' );
}

function yneko_reimu_footer_copyright() {
	$text  = yneko_reimu_get_theme_mod( 'yneko_reimu_footer_copyright', '' );
	$start = absint( yneko_reimu_get_theme_mod( 'yneko_reimu_footer_start_year', gmdate( 'Y' ) ) );
	$year  = absint( gmdate( 'Y' ) );
	$range = $start && $start < $year ? $start . '-' . $year : (string) $year;

	if ( ! $text ) {
		$text = sprintf(
			/* translators: %s: site name. */
			__( '© %s. Powered by WordPress.', 'yneko-reimu' ),
			get_bloginfo( 'name' )
		);
	}

	return str_replace( '{year}', $range, $text );
}
