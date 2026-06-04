<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_default_nav_items() {
	$items = array(
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

	foreach ( array( 'projects', 'archives', 'about', 'friend' ) as $slug ) {
		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) {
			unset( $items[ $slug ] );
		}
	}

	return $items;
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

function yneko_reimu_nav_builtin_slug_from_url( $url, $include_disabled = false ) {
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

	if ( ! in_array( $path, array( 'projects', 'archives', 'about', 'friend' ), true ) ) {
		return '';
	}

	if ( ! $include_disabled && function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $path ) ) {
		return '';
	}

	return $path;
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

	$item_url  = function_exists( 'yneko_reimu_i18n_localize_url' ) ? yneko_reimu_i18n_localize_url( $item->url ) : $item->url;
	$item_path = trim( (string) wp_parse_url( $item_url, PHP_URL_PATH ), '/' );
	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	if ( '' !== $home_path && 0 === strpos( $item_path, $home_path . '/' ) ) {
		$item_path = trim( substr( $item_path, strlen( $home_path ) ), '/' );
	}

	if ( function_exists( 'yneko_reimu_i18n_relative_without_prefix' ) ) {
		$item_path = yneko_reimu_i18n_relative_without_prefix( $item_path );
	}

	return trim( $path, '/' ) === $item_path;
}

function yneko_reimu_ensure_projects_menu_item( $items, $args ) {
	if ( is_admin() || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}
	if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( 'projects' ) ) {
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
		'title'             => function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '项目' ) : __( '项目', 'yneko-reimu' ),
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

function yneko_reimu_dedupe_builtin_menu_items( $items, $args ) {
	if ( is_admin() || empty( $args->theme_location ) || ! in_array( $args->theme_location, array( 'primary', 'mobile' ), true ) ) {
		return $items;
	}

	$seen = array();
	foreach ( $items as $index => $item ) {
		$slug = yneko_reimu_nav_builtin_slug_from_url( $item->url, true );
		if ( ! $slug ) {
			continue;
		}

		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) {
			unset( $items[ $index ] );
			continue;
		}

		if ( isset( $seen[ $slug ] ) ) {
			unset( $items[ $index ] );
			continue;
		}

		$seen[ $slug ] = true;
	}

	return array_values( $items );
}

add_filter( 'wp_nav_menu_objects', 'yneko_reimu_dedupe_builtin_menu_items', 20, 2 );

function yneko_reimu_virtual_pages() {
	$pages = array(
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

	foreach ( array_keys( $pages ) as $slug ) {
		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) {
			unset( $pages[ $slug ] );
		}
	}

	return $pages;
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

	global $wp_query;
	if ( $wp_query instanceof WP_Query && $wp_query->get( 'yneko_reimu_force_404' ) ) {
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
