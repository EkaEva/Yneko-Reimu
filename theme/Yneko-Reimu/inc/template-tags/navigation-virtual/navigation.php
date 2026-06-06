<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_default_nav_items() {
	$items = array(
		'home'     => yneko_reimu_default_nav_item( '首页', 'Home', function_exists( 'yneko_reimu_i18n_home_url' ) ? yneko_reimu_i18n_home_url() : home_url( '/' ) ),
		'projects' => yneko_reimu_default_nav_item( '项目', 'Projects', function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'projects' ) : home_url( '/projects/' ) ),
		'archives' => yneko_reimu_default_nav_item( '归档', 'Archives', function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'archives' ) : home_url( '/archives/' ) ),
		'about'    => yneko_reimu_default_nav_item( '关于', 'About', function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'about' ) : home_url( '/about/' ) ),
		'friend'   => yneko_reimu_default_nav_item( '友链', 'Friends', function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'friend' ) : home_url( '/friend/' ) ),
	);

	foreach ( array( 'projects', 'archives', 'about', 'friend' ) as $slug ) {
		if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) {
			unset( $items[ $slug ] );
		}
	}

	return $items;
}

function yneko_reimu_default_nav_item( $zh_label, $en_label, $url ) {
	$translated_labels = array(
		'首页' => __( '首页', 'yneko-reimu' ),
		'项目' => __( '项目', 'yneko-reimu' ),
		'归档' => __( '归档', 'yneko-reimu' ),
		'关于' => __( '关于', 'yneko-reimu' ),
		'友链' => __( '友链', 'yneko-reimu' ),
	);
	$translated_en_labels = array(
		'Home'     => __( 'Home', 'yneko-reimu' ),
		'Projects' => __( 'Projects', 'yneko-reimu' ),
		'Archives' => __( 'Archives', 'yneko-reimu' ),
		'About'    => __( 'About', 'yneko-reimu' ),
		'Friends'  => __( 'Friends', 'yneko-reimu' ),
	);

	return array(
		'source_label' => $zh_label,
		'label'        => isset( $translated_labels[ $zh_label ] ) ? $translated_labels[ $zh_label ] : $zh_label,
		'en_label'     => isset( $translated_en_labels[ $en_label ] ) ? $translated_en_labels[ $en_label ] : $en_label,
		'url'          => $url,
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

function yneko_reimu_nav_builtin_slug_from_url( $url, $include_disabled = false ) {
	$path = yneko_reimu_nav_relative_path( $url );
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

function yneko_reimu_nav_relative_path( $url ) {
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

	return trim( $path, '/' );
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

	$item_url = function_exists( 'yneko_reimu_i18n_localize_url' ) ? yneko_reimu_i18n_localize_url( $item->url ) : $item->url;
	return trim( $path, '/' ) === yneko_reimu_nav_relative_path( $item_url );
}

function yneko_reimu_ensure_projects_menu_item( $items, $args ) {
	if ( ! yneko_reimu_should_add_projects_menu_item( $items, $args ) ) {
		return $items;
	}

	array_splice( $items, yneko_reimu_projects_menu_insert_index( $items ), 0, array( yneko_reimu_projects_menu_item() ) );
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'yneko_reimu_ensure_projects_menu_item', 10, 2 );

function yneko_reimu_should_add_projects_menu_item( $items, $args ) {
	if ( is_admin() || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return false;
	}
	if ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( 'projects' ) ) {
		return false;
	}

	foreach ( $items as $item ) {
		if ( yneko_reimu_menu_item_matches_url( $item, 'projects' ) ) {
			return false;
		}
	}

	return true;
}

function yneko_reimu_projects_menu_item() {
	return (object) array(
		'ID'                   => -3024,
		'db_id'                => -3024,
		'menu_item_parent'     => '0',
		'object_id'            => -3024,
		'object'               => 'custom',
		'type'                 => 'custom',
		'type_label'           => __( '自定义链接', 'yneko-reimu' ),
		'title'                => function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '项目' ) : __( '项目', 'yneko-reimu' ),
		'url'                  => function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( 'projects' ) : home_url( '/projects/' ),
		'target'               => '',
		'attr_title'           => '',
		'description'          => '',
		'classes'              => array( 'menu-item', 'menu-item-type-custom', 'reimu-projects-menu-item' ),
		'xfn'                  => '',
		'current'              => yneko_reimu_is_virtual_page( 'projects' ) || yneko_reimu_special_page_slug() === 'projects',
		'current_item_ancestor' => false,
		'current_item_parent'  => false,
	);
}

function yneko_reimu_projects_menu_insert_index( $items ) {
	foreach ( $items as $index => $item ) {
		if ( yneko_reimu_menu_item_matches_url( $item, 'archives' ) ) {
			return $index;
		}
	}

	foreach ( $items as $index => $item ) {
		if ( yneko_reimu_menu_item_matches_url( $item, '' ) || untrailingslashit( $item->url ) === untrailingslashit( home_url( '/' ) ) ) {
			return $index + 1;
		}
	}

	return 1;
}

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

		if ( ( function_exists( 'yneko_reimu_builtin_page_enabled' ) && ! yneko_reimu_builtin_page_enabled( $slug ) ) || isset( $seen[ $slug ] ) ) {
			unset( $items[ $index ] );
			continue;
		}

		$seen[ $slug ] = true;
	}

	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'yneko_reimu_dedupe_builtin_menu_items', 20, 2 );
