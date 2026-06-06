<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_user_badges_defaults() {
	return array(
		'enabled'        => '1',
		'review_enabled' => '0',
		'blocklist'      => '',
		'avatar_frames'  => array(
			'enabled' => '0',
			'frames'  => yneko_reimu_settings_default_avatar_frames(),
		),
		'special'        => yneko_reimu_settings_default_special_badges(),
	);
}

function yneko_reimu_settings_default_avatar_frames() {
	$roles = array( 'owner', 'admin', 'editor', 'author', 'contributor', 'yko', 'subscriber' );
	$url   = yneko_reimu_default_avatar_frame_url();

	return array_fill_keys( $roles, $url );
}

function yneko_reimu_settings_default_special_badges() {
	return array(
		'owner'       => yneko_reimu_settings_default_special_badge( '站长', 'Owner' ),
		'admin'       => yneko_reimu_settings_default_special_badge( '管理员', 'Admin' ),
		'yko'         => yneko_reimu_settings_default_special_badge( 'Yko', 'Yko' ),
		'subscriber'  => yneko_reimu_settings_default_special_badge( '订阅者', 'Subscriber' ),
		'contributor' => yneko_reimu_settings_default_special_badge( '贡献者', 'Contributor' ),
		'author'      => yneko_reimu_settings_default_special_badge( '作者', 'Author' ),
		'editor'      => yneko_reimu_settings_default_special_badge( '编辑', 'Editor' ),
	);
}

function yneko_reimu_settings_default_special_badge( $zh, $en ) {
	return array(
		'enabled' => '1',
		'zh'      => $zh,
		'en'      => $en,
	);
}

function yneko_reimu_default_avatar_frame_url() {
	return function_exists( 'yneko_reimu_asset_uri' ) ? yneko_reimu_asset_uri( 'assets/images/avatar-frame.png' ) : get_template_directory_uri() . '/assets/images/avatar-frame.png';
}

function yneko_reimu_user_badge_base_definitions() {
	return array(
		'owner'       => yneko_reimu_user_badge_base_definition( '站长', 'Owner', '站长', 'Owner', '默认分配给站点第一位管理员。', 'Assigned to the first administrator by default.' ),
		'admin'       => yneko_reimu_user_badge_base_definition( '管理员', 'Admin', '管理员', 'Admin', '默认分配给除站长外的管理员。', 'Assigned to administrators except the site owner.' ),
		'editor'      => yneko_reimu_user_badge_base_definition( '编辑', 'Editor', '编辑', 'Editor', '默认分配给 WordPress 编辑角色。', 'Assigned to the WordPress Editor role.' ),
		'author'      => yneko_reimu_user_badge_base_definition( '作者', 'Author', '作者', 'Author', '默认分配给 WordPress 作者角色。', 'Assigned to the WordPress Author role.' ),
		'contributor' => yneko_reimu_user_badge_base_definition( '贡献者', 'Contributor', '贡献者', 'Contributor', '默认分配给 WordPress 贡献者角色。', 'Assigned to the WordPress Contributor role.' ),
		'yko'         => yneko_reimu_user_badge_base_definition( '会员', 'Member', 'Yko', 'Yko', '登录用户都会拥有这个基础标签。', 'Assigned to every logged-in user.' ),
		'subscriber'  => yneko_reimu_user_badge_base_definition( '订阅者', 'Subscriber', '订阅者', 'Subscriber', '默认分配给 WordPress 订阅者角色。', 'Assigned to the WordPress Subscriber role.' ),
	);
}

function yneko_reimu_user_badge_base_definition( $title_zh, $title_en, $zh, $en, $desc_zh, $desc_en ) {
	return array(
		'title_zh' => $title_zh,
		'title_en' => $title_en,
		'zh'       => $zh,
		'en'       => $en,
		'desc_zh'  => $desc_zh,
		'desc_en'  => $desc_en,
	);
}
