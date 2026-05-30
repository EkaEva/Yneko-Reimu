<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_theme_mod_bool( $name, $default = true ) {
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
	return in_array( $slug, array( 'about', 'projects', 'archives', 'friend' ), true ) ? $slug : '';
}

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

function yneko_reimu_should_show_comments( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
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

	return comments_open( $post_id ) || get_comments_number( $post_id );
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

	if ( function_exists( 'yneko_reimu_i18n_enabled' ) && yneko_reimu_i18n_enabled() && function_exists( 'yneko_reimu_i18n_language_meta_query' ) ) {
		$args['meta_query'] = yneko_reimu_i18n_language_meta_query(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

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
				'no_articles'    => __( '没有文章', 'yneko-reimu' ),
				'words'          => __( '字', 'yneko-reimu' ),
				'total_articles' => __( '共 $1 篇文章, $2 字', 'yneko-reimu' ),
				'no_writing_on'  => __( '{date} 没有写作', 'yneko-reimu' ),
				'writing_on'     => __( '{posts} {words} 于 {date}', 'yneko-reimu' ),
				'year_total'     => __( '{posts} {words} 于 {year}', 'yneko-reimu' ),
			),
			'en-US' => array(
				'no_articles'    => __( '没有文章', 'yneko-reimu' ),
				'words'          => __( 'words', 'yneko-reimu' ),
				'total_articles' => __( '$1 post(s), $2 word(s) in total', 'yneko-reimu' ),
				'no_writing_on'  => __( 'No writing on {date}', 'yneko-reimu' ),
				'writing_on'     => __( '{posts}, {words} on {date}', 'yneko-reimu' ),
				'year_total'     => __( '{posts}, {words} in {year}', 'yneko-reimu' ),
			),
			'en'    => array(
				'no_articles'    => __( '没有文章', 'yneko-reimu' ),
				'words'          => __( 'words', 'yneko-reimu' ),
				'total_articles' => __( '$1 post(s), $2 word(s) in total', 'yneko-reimu' ),
				'no_writing_on'  => __( 'No writing on {date}', 'yneko-reimu' ),
				'writing_on'     => __( '{posts}, {words} on {date}', 'yneko-reimu' ),
				'year_total'     => __( '{posts}, {words} in {year}', 'yneko-reimu' ),
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

function yneko_reimu_social_links() {
	$github = yneko_reimu_settings_github_url();

	return array_filter(
		array(
			'github' => array(
				'label' => 'GitHub',
				'url'   => $github,
			),
			'x'      => array(
				'label' => 'X',
				'url'   => yneko_reimu_get_theme_mod( 'yneko_reimu_social_x', '' ),
			),
			'email'  => array(
				'label' => __( 'Email', 'yneko-reimu' ),
				'url'   => yneko_reimu_get_theme_mod( 'yneko_reimu_social_email', '' ),
			),
			'rss'    => array(
				'label' => 'RSS',
				'url'   => yneko_reimu_get_theme_mod( 'yneko_reimu_social_rss', '' ),
			),
		),
		static function ( $item ) {
			return ! empty( $item['url'] );
		}
	);
}

function yneko_reimu_normalize_theme_url( $url, $fallback = '' ) {
	$url = trim( (string) $url );
	$url = '' === $url ? trim( (string) $fallback ) : $url;

	if ( '' === $url ) {
		return '#';
	}

	if ( preg_match( '#^(https?:)?//#i', $url ) || preg_match( '#^(mailto|tel):#i', $url ) || 0 === strpos( $url, '#' ) ) {
		return $url;
	}

	if ( 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

function yneko_reimu_default_nav_items() {
	return array(
		'home'     => array(
			'source_label' => '首页',
			'label' => __( '首页', 'yneko-reimu' ),
			'en_label' => __( 'Home', 'yneko-reimu' ),
			'url'   => function_exists( 'yneko_reimu_i18n_home_url' ) ? yneko_reimu_i18n_home_url() : home_url( '/' ),
		),
		'projects' => array(
			'source_label' => '项目',
			'label' => __( '项目', 'yneko-reimu' ),
			'en_label' => __( 'Projects', 'yneko-reimu' ),
			'url'   => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'projects' ) : home_url( '/projects/' ),
		),
		'archives' => array(
			'source_label' => '归档',
			'label' => __( '归档', 'yneko-reimu' ),
			'en_label' => __( 'Archives', 'yneko-reimu' ),
			'url'   => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'archives' ) : home_url( '/archives/' ),
		),
		'about'    => array(
			'source_label' => '关于',
			'label' => __( '关于', 'yneko-reimu' ),
			'en_label' => __( 'About', 'yneko-reimu' ),
			'url'   => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'about' ) : home_url( '/about/' ),
		),
		'friend'   => array(
			'source_label' => '友链',
			'label' => __( '友链', 'yneko-reimu' ),
			'en_label' => __( 'Friends', 'yneko-reimu' ),
			'url'   => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'friend' ) : home_url( '/friend/' ),
		),
	);
}

function yneko_reimu_nav_item_is_builtin_label( $label, $default ) {
	$label = trim( (string) $label );

	return '' === $label || $label === $default['label'] || ( isset( $default['source_label'] ) && $label === $default['source_label'] ) || ( isset( $default['en_label'] ) && $label === $default['en_label'] );
}

function yneko_reimu_nav_items() {
	$items = array();

	foreach ( yneko_reimu_default_nav_items() as $key => $default ) {
		$label = trim( (string) yneko_reimu_get_theme_mod( 'yneko_reimu_nav_' . $key . '_label', $default['label'] ) );
		$url   = yneko_reimu_get_theme_mod( 'yneko_reimu_nav_' . $key . '_url', $default['url'] );
		if ( function_exists( 'yneko_reimu_i18n_is_english_request' ) && yneko_reimu_i18n_is_english_request() && yneko_reimu_nav_item_is_builtin_label( $label, $default ) ) {
			$label = $default['en_label'];
		}

		$items[] = array(
			'key'   => $key,
			'label' => '' === $label ? $default['label'] : $label,
			'url'   => function_exists( 'yneko_reimu_i18n_localize_url' ) ? yneko_reimu_i18n_localize_url( yneko_reimu_normalize_theme_url( $url, $default['url'] ) ) : yneko_reimu_normalize_theme_url( $url, $default['url'] ),
		);
	}

	return $items;
}

function yneko_reimu_nav_builtin_slug_from_url( $url ) {
	$path      = trim( (string) wp_parse_url( (string) $url, PHP_URL_PATH ), '/' );
	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	} elseif ( $home_path === $path ) {
		$path = '';
	}

	if ( function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ) {
		$path = yneko_reimu_i18n_relative_without_prefix( $path );
	}

	$path = trim( $path, '/' );
	if ( '' === $path ) {
		return 'home';
	}

	return in_array( $path, array( 'projects', 'archives', 'about', 'friend' ), true ) ? $path : '';
}

function yneko_reimu_nav_localized_url( $url ) {
	$slug = yneko_reimu_nav_builtin_slug_from_url( $url );
	if ( 'home' === $slug && function_exists( 'yneko_reimu_i18n_home_url' ) ) {
		return yneko_reimu_i18n_home_url();
	}
	if ( $slug && function_exists( 'yneko_reimu_i18n_virtual_path' ) ) {
		return yneko_reimu_i18n_virtual_path( $slug );
	}

	return function_exists( 'yneko_reimu_i18n_localize_url' ) ? yneko_reimu_i18n_localize_url( $url ) : $url;
}

function yneko_reimu_nav_localized_title( $title, $url = '' ) {
	if ( ! function_exists( 'yneko_reimu_i18n_is_english_request' ) || ! yneko_reimu_i18n_is_english_request() ) {
		return $title;
	}

	$slug     = yneko_reimu_nav_builtin_slug_from_url( $url );
	$defaults = yneko_reimu_default_nav_items();
	if ( ! $slug || ! isset( $defaults[ $slug ] ) ) {
		return $title;
	}

	return yneko_reimu_nav_item_is_builtin_label( $title, $defaults[ $slug ] ) ? $defaults[ $slug ]['en_label'] : $title;
}

function yneko_reimu_menu_item_matches_url( $item, $path ) {
	if ( empty( $item->url ) ) {
		return false;
	}

	$item_path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	if ( '' !== $home_path && 0 === strpos( $item_path, $home_path . '/' ) ) {
		$item_path = trim( substr( $item_path, strlen( $home_path ) ), '/' );
	}

	return trim( $path, '/' ) === $item_path;
}

function yneko_reimu_ensure_projects_menu_item( $items, $args ) {
	if ( is_admin() || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( yneko_reimu_menu_item_matches_url( $item, 'projects' ) ) {
			return $items;
		}
	}

	$project_item = (object) array(
		'ID'                => -3024,
		'db_id'             => -3024,
		'menu_item_parent'  => '0',
		'object_id'         => -3024,
		'object'            => 'custom',
		'type'              => 'custom',
		'type_label'        => __( '自定义链接', 'yneko-reimu' ),
		'title'             => __( '项目', 'yneko-reimu' ),
		'url'               => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'projects' ) : home_url( '/projects/' ),
		'target'            => '',
		'attr_title'        => '',
		'description'       => '',
		'classes'           => array( 'menu-item', 'menu-item-type-custom', 'reimu-projects-menu-item' ),
		'xfn'               => '',
		'current'           => yneko_reimu_is_virtual_page( 'projects' ) || yneko_reimu_special_page_slug() === 'projects',
		'current_item_ancestor' => false,
		'current_item_parent'   => false,
	);

	$insert_at = null;
	foreach ( $items as $index => $item ) {
		if ( yneko_reimu_menu_item_matches_url( $item, 'archives' ) ) {
			$insert_at = $index;
			break;
		}
	}

	if ( null === $insert_at ) {
		foreach ( $items as $index => $item ) {
			if ( yneko_reimu_menu_item_matches_url( $item, '' ) || untrailingslashit( $item->url ) === untrailingslashit( home_url( '/' ) ) ) {
				$insert_at = $index + 1;
				break;
			}
		}
	}

	if ( null === $insert_at ) {
		$insert_at = 1;
	}

	array_splice( $items, $insert_at, 0, array( $project_item ) );
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'yneko_reimu_ensure_projects_menu_item', 10, 2 );

function yneko_reimu_virtual_pages() {
	return array(
		'about'    => array(
			'title'       => __( '关于', 'yneko-reimu' ),
			'description' => __( '关于这个站点与作者。', 'yneko-reimu' ),
		),
		'projects' => array(
			'title'       => __( '项目', 'yneko-reimu' ),
			'description' => __( 'GitHub 项目与作品。', 'yneko-reimu' ),
		),
		'archives' => array(
			'title'       => __( '归档', 'yneko-reimu' ),
			'description' => __( '按时间整理全部文章。', 'yneko-reimu' ),
		),
		'friend'   => array(
			'title'       => __( '友链', 'yneko-reimu' ),
			'description' => __( '朋友们的站点入口。', 'yneko-reimu' ),
		),
	);
}

function yneko_reimu_detect_virtual_page_slug() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$path = trim( rawurldecode( $path ), '/' );

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	}

	if ( function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ) {
		$path = yneko_reimu_i18n_relative_without_prefix( $path );
	}

	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return '';
	}

	$pages = yneko_reimu_virtual_pages();
	return isset( $pages[ $path ] ) ? $path : '';
}

function yneko_reimu_maybe_set_virtual_page() {
	if ( ! is_404() ) {
		return;
	}

	$slug = yneko_reimu_detect_virtual_page_slug();
	$existing_page = $slug ? get_page_by_path( $slug, OBJECT, 'page' ) : null;
	if ( ! $slug || ( $existing_page && 'publish' === get_post_status( $existing_page ) ) ) {
		return;
	}

	$pages = yneko_reimu_virtual_pages();

	$GLOBALS['yneko_reimu_virtual_page'] = array_merge(
		array(
			'slug' => $slug,
		),
		$pages[ $slug ]
	);

	global $wp_query;
	if ( $wp_query ) {
		$wp_query->is_404 = false;
	}

	status_header( 200 );
}
add_action( 'wp', 'yneko_reimu_maybe_set_virtual_page', 1 );

function yneko_reimu_virtual_page() {
	return isset( $GLOBALS['yneko_reimu_virtual_page'] ) && is_array( $GLOBALS['yneko_reimu_virtual_page'] )
		? $GLOBALS['yneko_reimu_virtual_page']
		: array();
}

function yneko_reimu_is_virtual_page( $slug = '' ) {
	$page = yneko_reimu_virtual_page();

	if ( ! $page ) {
		return false;
	}

	return '' === $slug || $slug === $page['slug'];
}

function yneko_reimu_virtual_page_slug() {
	$page = yneko_reimu_virtual_page();
	return $page ? $page['slug'] : '';
}

function yneko_reimu_virtual_template( $template ) {
	if ( ! yneko_reimu_is_virtual_page() ) {
		return $template;
	}

	$virtual_template = locate_template( 'virtual-page.php' );
	return $virtual_template ? $virtual_template : $template;
}
add_filter( 'template_include', 'yneko_reimu_virtual_template', 99 );

function yneko_reimu_home_category_capsules() {
	$defaults = array(
		1 => array(
			'title' => __( 'Yneko', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'yneko' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'yneko' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
		2 => array(
			'title' => __( '学习笔记', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'study-notes' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'study-notes' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
	);
	$items    = array();

	foreach ( $defaults as $index => $default ) {
		$title = trim( (string) yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_title', $default['title'] ) );
		$url   = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_url', $default['url'] );
		$count = $default['count'];
		$cover = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_cover', '' );

		if ( 1 === $index ) {
			if ( in_array( strtolower( $title ), array( 'hexo', 'project' ), true ) || '项目' === $title ) {
				$title = $default['title'];
			}

			if (
				in_array(
					yneko_reimu_normalize_theme_url( $url ),
					array(
						home_url( '/category/hexo/' ),
						home_url( '/category/project/' ),
						home_url( '/category/项目/' ),
						home_url( '/category/%e9%a1%b9%e7%9b%ae/' ),
						home_url( '/project/' ),
						home_url( '/项目/' ),
					),
					true
				)
			) {
				$url = $default['url'];
			}

		}

		if ( '' === $cover ) {
			$cover = $default['cover'];
		}

		if ( 1 === $index ) {
			$cover_url = yneko_reimu_normalize_theme_url( $cover );
			if (
				in_array(
					$cover_url,
					array(
						yneko_reimu_asset_uri( 'assets/images/banner.png' ),
						yneko_reimu_get_default_banner_url(),
					),
					true
				)
			) {
				$cover = $default['cover'];
			}
		}

		if ( 2 === $index ) {
			$cover_url = yneko_reimu_normalize_theme_url( $cover );
			if (
				in_array(
					$cover_url,
					array(
						yneko_reimu_asset_uri( 'assets/images/banner.png' ),
						yneko_reimu_get_default_banner_url(),
					),
					true
				)
			) {
				$cover = $default['cover'];
			}
		}

		$items[] = array(
			'title' => '' === $title ? $default['title'] : $title,
			'url'   => yneko_reimu_normalize_theme_url( $url, $default['url'] ),
			'count' => '' === $count ? $default['count'] : $count,
			'cover' => esc_url_raw( $cover ),
		);
	}

	return $items;
}

function yneko_reimu_should_show_wp_widgets() {
	return ! yneko_reimu_theme_mod_bool( 'yneko_reimu_strict_clone', true );
}

function yneko_reimu_should_show_clone_widgets() {
	return yneko_reimu_theme_mod_bool( 'yneko_reimu_strict_clone', true ) && yneko_reimu_theme_mod_bool( 'yneko_reimu_clone_tagcloud', true );
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
		$html = esc_html( $matches[1] );

		if ( '' !== $matches[2] ) {
			$html .= '<span class="bullet">-</span> ';
		}

		$html .= '<span class="attr">' . esc_html( $matches[3] ) . '</span>';
		$html .= esc_html( $matches[4] );
		$html .= yneko_reimu_yml_value_html( $matches[5] );

		return $html;
	}

	return esc_html( $line );
}

function yneko_reimu_yml_editor( $code, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'lang'  => 'YML',
			'class' => '',
		)
	);

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

function yneko_reimu_friend_items() {
	$settings_friends = yneko_reimu_settings_friend_items();
	if ( $settings_friends ) {
		return array_map(
			static function ( $friend ) {
				$friend['image'] = $friend['image'] ? $friend['image'] : yneko_reimu_get_default_avatar_url();
				return $friend;
			},
			$settings_friends
		);
	}

	$raw = (string) yneko_reimu_get_theme_mod( 'yneko_reimu_friend_links', '' );
	$items = array();
	$seen  = array();

	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		$parts = array_pad( array_map( 'trim', explode( '|', $line ) ), 4, '' );
		if ( '' === $parts[0] || '' === $parts[1] ) {
			continue;
		}

		if ( ! preg_match( '#^https?://#i', $parts[1] ) ) {
			$parts[1] = 'https://' . ltrim( $parts[1], '/' );
		}

		$key = strtolower( untrailingslashit( $parts[1] ) );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;

		$items[] = array(
			'name'  => $parts[0],
			'url'   => esc_url_raw( $parts[1] ),
			'desc'  => $parts[2],
			'image' => esc_url_raw( $parts[3] ? $parts[3] : yneko_reimu_get_default_avatar_url() ),
		);
	}

	return $items ? $items : yneko_reimu_settings_friend_items();
}

function yneko_reimu_github_username() {
	$github = trim( (string) yneko_reimu_settings_github_url() );

	if ( '' === $github ) {
		return '';
	}

	$path = (string) wp_parse_url( $github, PHP_URL_PATH );
	$path = trim( $path, '/' );

	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return '';
	}

	$username = preg_replace( '/[^A-Za-z0-9-]/', '', $path );
	return $username ? $username : '';
}

function yneko_reimu_project_fallback_items() {
	$username = yneko_reimu_github_username();

	if ( ! $username ) {
		return array();
	}

	return array(
		array(
			'name'        => $username,
			'url'         => 'https://github.com/' . rawurlencode( $username ),
			'desc'        => __( '我的 GitHub 主页与项目索引。', 'yneko-reimu' ),
			'image'       => yneko_reimu_get_default_avatar_url(),
			'language'    => 'GitHub',
			'stars'       => 0,
			'updated_at'  => '',
			'is_fallback' => true,
		),
	);
}

function yneko_reimu_normalize_github_repo_items( $repos, $limit = 12 ) {
	usort(
		$repos,
		static function ( $a, $b ) {
			$a_fork = ! empty( $a['fork'] ) ? 1 : 0;
			$b_fork = ! empty( $b['fork'] ) ? 1 : 0;

			if ( $a_fork !== $b_fork ) {
				return $a_fork <=> $b_fork;
			}

			return strcmp( (string) ( $b['updated_at'] ?? '' ), (string) ( $a['updated_at'] ?? '' ) );
		}
	);

	$items = array();
	foreach ( $repos as $repo ) {
		if ( empty( $repo['name'] ) || empty( $repo['html_url'] ) ) {
			continue;
		}

		$owner = isset( $repo['owner'] ) && is_array( $repo['owner'] ) ? $repo['owner'] : array();

		$items[] = array(
			'name'       => (string) $repo['name'],
			'url'        => esc_url_raw( (string) $repo['html_url'] ),
			'desc'       => ! empty( $repo['description'] ) ? (string) $repo['description'] : __( 'GitHub 项目', 'yneko-reimu' ),
			'image'      => ! empty( $owner['avatar_url'] ) ? esc_url_raw( (string) $owner['avatar_url'] ) : yneko_reimu_get_default_avatar_url(),
			'language'   => ! empty( $repo['language'] ) ? (string) $repo['language'] : '',
			'stars'      => isset( $repo['stargazers_count'] ) ? absint( $repo['stargazers_count'] ) : 0,
			'updated_at' => ! empty( $repo['updated_at'] ) ? (string) $repo['updated_at'] : '',
			'is_fork'    => ! empty( $repo['fork'] ),
		);

		if ( count( $items ) >= $limit ) {
			break;
		}
	}

	return $items;
}

function yneko_reimu_github_api_get( $path, $transient_key, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$headers = array(
		'Accept'     => 'application/vnd.github+json',
		'User-Agent' => 'yneko-reimu/' . wp_get_theme()->get( 'Version' ) . '; ' . home_url( '/' ),
	);
	$token   = '';
	if ( defined( 'YNEKO_REIMU_GITHUB_TOKEN' ) ) {
		$token = trim( (string) YNEKO_REIMU_GITHUB_TOKEN );
	}
	$token = trim( (string) apply_filters( 'yneko_reimu_github_token', $token ) );
	if ( '' !== $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}

	$response = wp_remote_get(
		'https://api.github.com' . $path,
		array(
			'timeout' => 4,
			'headers' => $headers,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	set_transient( $transient_key, $data, $cache_seconds );
	return $data;
}

function yneko_reimu_github_api_get_pages( $path_template, $transient_key, $pages = 1, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$items = array();
	$pages = max( 1, min( 5, absint( $pages ) ) );

	for ( $page = 1; $page <= $pages; $page++ ) {
		$path = str_replace( '%d', (string) $page, $path_template );
		$data = yneko_reimu_github_api_get( $path, $transient_key . '_page_' . $page, array(), $cache_seconds );
		if ( ! is_array( $data ) || ! $data ) {
			break;
		}
		$items = array_merge( $items, $data );
		if ( count( $data ) < 100 ) {
			break;
		}
	}

	if ( ! $items ) {
		$items = $fallback;
	}

	set_transient( $transient_key, $items, $cache_seconds );
	return $items;
}

function yneko_reimu_github_projects() {
	$username      = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_projects_' . md5( strtolower( $username ) );
	$fallback      = yneko_reimu_project_fallback_items();
	$repos         = yneko_reimu_github_api_get_pages(
		'/users/' . rawurlencode( $username ) . '/repos?sort=updated&per_page=100&page=%d',
		$transient_key . '_raw_v2',
		1,
		array(),
		6 * HOUR_IN_SECONDS
	);

	$items = yneko_reimu_normalize_github_repo_items( $repos, 48 );

	if ( ! $items ) {
		$items = $fallback;
	}

	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );
	return $items;
}

function yneko_reimu_github_starred_projects() {
	$username      = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_starred_projects_' . md5( strtolower( $username ) );
	$repos         = yneko_reimu_github_api_get_pages(
		'/users/' . rawurlencode( $username ) . '/starred?sort=updated&per_page=100&page=%d',
		$transient_key . '_raw_v2',
		3,
		array(),
		6 * HOUR_IN_SECONDS
	);

	$items = yneko_reimu_normalize_github_repo_items( $repos, 240 );
	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );

	return $items;
}

function yneko_reimu_player_position() {
	$position = yneko_reimu_get_theme_mod( 'yneko_reimu_player_position', 'before_sidebar' );
	$allowed  = array( 'before_sidebar', 'after_sidebar', 'after_widget' );

	return in_array( $position, $allowed, true ) ? $position : 'before_sidebar';
}

function yneko_reimu_social_icon_class( $key ) {
	$key = sanitize_html_class( $key );
	return 'icon-' . $key;
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

	return is_sticky( $post_id );
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
		$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
		$total += str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) );
		$total += preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text );
	}

	if ( $total >= 1000 ) {
		return round( $total / 1000, 1 ) . 'k';
	}

	return (string) $total;
}

function yneko_reimu_render_taichi_svg( $size = 150 ) {
	?>
	<svg width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" aria-hidden="true" focusable="false">
		<path d="M303.5 432A80 80 0 0 1 291.5 592A80 80 0 0 1 303.5 432z" fill="var(--red-1, #ff5252)" />
		<path d="M512 65A447 447 0 0 1 512 959L512 929A417 417 0 0 0 512 95A417 417 0 0 0 512 929L512 959A447 447 0 0 1 512 65z M512 95A417 417 0 0 1 929 512A208.5 208.5 0 0 1 720.5 720.5L720.5 592A80 80 0 0 0 720.5 432A80 80 0 0 0 720.5 592L720.5 720.5A208.5 208.5 0 0 1 512 512A208.5 208.5 0 0 0 303.5 303.5A208.5 208.5 0 0 0 95 512A417 417 0 0 1 512 95z" fill="var(--red-1, #ff5252)" />
	</svg>
	<?php
}

class Yneko_Reimu_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$atts           = array();
		$atts['href']   = ! empty( $item->url ) ? yneko_reimu_nav_localized_url( $item->url ) : '';
		$atts['class']  = 'main-nav-link-wrap';
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' !== $value ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$title   = yneko_reimu_nav_localized_title( apply_filters( 'the_title', $item->title, $item->ID ), $item->url );
		$output .= '<a' . $attributes . '>';
		$output .= '<div class="icon main-nav-icon rotate">&#xe62b;</div>';
		$output .= '<span class="main-nav-link">' . esc_html( $title ) . '</span>';
		$output .= '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
	}
}

class Yneko_Reimu_Sidebar_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$href   = ! empty( $item->url ) ? yneko_reimu_nav_localized_url( $item->url ) : '#';
		$title  = yneko_reimu_nav_localized_title( apply_filters( 'the_title', $item->title, $item->ID ), $item->url );
		$output .= '<div class="sidebar-menu-link-wrap">';
		$output .= '<a class="sidebar-menu-link-dummy" href="' . esc_url( $href ) . '" aria-label="' . esc_attr( $title ) . '"></a>';
		$output .= '<div class="icon rotate sidebar-menu-icon">&#xe62b;</div>';
		$output .= '<div class="sidebar-menu-link">' . esc_html( $title ) . '</div>';
		$output .= '</div>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
	}
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
