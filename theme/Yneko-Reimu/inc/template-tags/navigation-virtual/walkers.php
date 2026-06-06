<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Yneko_Reimu_Menu_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$atts           = array();
		$atts['href']   = ! empty( $item->url ) ? yneko_reimu_nav_localized_url( $item->url ) : '';
		$atts['class']  = 'main-nav-link-wrap';
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

		$output .= '<a' . yneko_reimu_nav_attributes_html( $atts ) . '>';
		$output .= '<div class="icon main-nav-icon rotate">&#xe62b;</div>';
		$output .= '<span class="main-nav-link">' . esc_html( yneko_reimu_nav_localized_title( apply_filters( 'the_title', $item->title, $item->ID ), $item->url ) ) . '</span>';
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
		$href  = ! empty( $item->url ) ? yneko_reimu_nav_localized_url( $item->url ) : '#';
		$title = yneko_reimu_nav_localized_title( apply_filters( 'the_title', $item->title, $item->ID ), $item->url );

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

function yneko_reimu_nav_attributes_html( $atts ) {
	$attributes = '';
	foreach ( $atts as $attr => $value ) {
		if ( '' !== $value ) {
			$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
		}
	}

	return $attributes;
}
