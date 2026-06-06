<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_default_site_friend_info() {
	return array(
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
		'desc'  => get_bloginfo( 'description' ),
		'image' => '',
	);
}

function yneko_reimu_default_friend_items() {
	return array(
		yneko_reimu_default_friend_item(
			'EkaEva',
			'https://github.com/EkaEva',
			__( 'Yneko-Reimu 主题作者', 'yneko-reimu' ),
			''
		),
		yneko_reimu_default_friend_item(
			'拔剑Sketon',
			'https://d-sketon.github.io/',
			__( 'hexo-theme-reimu 原作者', 'yneko-reimu' ),
			'https://d-sketon.github.io/avatar/avatar.webp'
		),
		yneko_reimu_default_friend_item(
			'天羊EdSky',
			'https://space.bilibili.com/16573583',
			__( '莉莉概念光标作者', 'yneko-reimu' ),
			yneko_reimu_asset_uri( 'assets/images/tianyang-edsky.jpg' )
		),
	);
}

function yneko_reimu_default_friend_item( $name, $url, $desc, $image ) {
	return array(
		'name'  => $name,
		'url'   => $url,
		'desc'  => $desc,
		'image' => $image,
	);
}
