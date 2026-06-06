<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_settings_page() {
	add_theme_page(
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		'manage_options',
		'yneko-reimu-settings',
		'yneko_reimu_render_settings_page'
	);
}
add_action( 'admin_menu', 'yneko_reimu_register_settings_page' );

function yneko_reimu_admin_menu_badge_markup( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return ' <span class="update-plugins yneko-reimu-menu-badge count-' . esc_attr( $count ) . '"><span class="plugin-count">' . esc_html( number_format_i18n( $count ) ) . '</span></span>';
}

function yneko_reimu_add_admin_menu_review_badges() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $menu, $submenu;
	$counts = yneko_reimu_admin_review_badge_counts();
	$total  = absint( ( $counts['comments'] ?? 0 ) + ( $counts['users'] ?? 0 ) + ( $counts['security'] ?? 0 ) );
	if ( ! $total ) {
		return;
	}

	yneko_reimu_admin_append_menu_badge( $menu, 'themes.php', 2, 0, $total );
	if ( ! empty( $submenu['themes.php'] ) && is_array( $submenu['themes.php'] ) ) {
		yneko_reimu_admin_append_menu_badge( $submenu['themes.php'], 'yneko-reimu-settings', 2, 0, $total );
	}
}
add_action( 'admin_menu', 'yneko_reimu_add_admin_menu_review_badges', 99 );

function yneko_reimu_admin_append_menu_badge( &$items, $slug, $slug_index, $label_index, $total ) {
	foreach ( $items as &$item ) {
		if ( ! isset( $item[ $slug_index ] ) || $slug !== $item[ $slug_index ] ) {
			continue;
		}
		$item[ $label_index ] = preg_replace( '#\s*<span class="update-plugins yneko-reimu-menu-badge.*?</span></span>#', '', (string) $item[ $label_index ] );
		$item[ $label_index ] .= yneko_reimu_admin_menu_badge_markup( $total );
		break;
	}
	unset( $item );
}
