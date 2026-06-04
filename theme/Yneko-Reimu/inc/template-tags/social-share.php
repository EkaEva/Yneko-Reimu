<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_share_context( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_the_ID() );
	$slug    = '';
	$pages   = function_exists( 'yneko_reimu_virtual_pages' ) ? yneko_reimu_virtual_pages() : array();

	if ( function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page() && function_exists( 'yneko_reimu_virtual_page_slug' ) ) {
		$slug = yneko_reimu_virtual_page_slug();
	} elseif ( $post_id && function_exists( 'yneko_reimu_special_page_slug' ) ) {
		$slug = yneko_reimu_special_page_slug( $post_id );
	}

	$is_virtual = $slug && isset( $pages[ $slug ] ) && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page();
	$url        = $post_id ? get_permalink( $post_id ) : home_url( '/' );
	$title      = $post_id ? wp_strip_all_tags( get_the_title( $post_id ) ) : get_bloginfo( 'name' );
	$desc       = $post_id ? yneko_reimu_excerpt( $post_id ) : get_bloginfo( 'description' );
	$author     = $post_id ? get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) : get_bloginfo( 'name' );

	if ( $is_virtual ) {
		$url    = function_exists( 'yneko_reimu_i18n_virtual_path' ) ? yneko_reimu_i18n_virtual_path( $slug ) : home_url( '/' . $slug . '/' );
		$title  = $pages[ $slug ]['title'];
		$desc   = $pages[ $slug ]['description'];
		$author = get_bloginfo( 'name' );
	}

	return array(
		'post_id'     => $post_id,
		'url'         => $url,
		'title'       => $title,
		'description' => $desc,
		'author'      => $author,
		'image'       => yneko_reimu_get_post_share_image( $post_id ),
	);
}

function yneko_reimu_customizer_bool( $name, $default = false ) {
	$value = yneko_reimu_get_theme_mod( $name, null );
	return null === $value ? (bool) $default : (bool) $value;
}

function yneko_reimu_social_definitions() {
	return array(
		'email'         => array(
			'label'       => __( 'Email', 'yneko-reimu' ),
			'placeholder' => __( 'Email URL 或 mailto:', 'yneko-reimu' ),
		),
		'github'        => array(
			'label'       => 'GitHub',
			'placeholder' => __( '请到“外观 -> Yneko-Reimu 设置”中配置 GitHub 主页链接。', 'yneko-reimu' ),
		),
		'google'        => array( 'label' => 'Google', 'placeholder' => 'https://plus.google.com/yourname' ),
		'twitter'       => array( 'label' => 'Twitter / X', 'placeholder' => 'https://twitter.com/yourname' ),
		'bluesky'       => array( 'label' => 'Bluesky', 'placeholder' => 'https://bsky.app/profile/yourname' ),
		'facebook'      => array( 'label' => 'Facebook', 'placeholder' => 'https://www.facebook.com/yourname' ),
		'instagram'     => array( 'label' => 'Instagram', 'placeholder' => 'https://www.instagram.com/yourname' ),
		'linkedin'      => array( 'label' => 'LinkedIn', 'placeholder' => 'https://www.linkedin.com/in/yourname' ),
		'pinterest'     => array( 'label' => 'Pinterest', 'placeholder' => 'https://www.pinterest.com/yourname' ),
		'youtube'       => array( 'label' => 'YouTube', 'placeholder' => 'https://www.youtube.com/channel/yourname' ),
		'vimeo'         => array( 'label' => 'Vimeo', 'placeholder' => 'https://vimeo.com/yourname' ),
		'flickr'        => array( 'label' => 'Flickr', 'placeholder' => 'https://www.flickr.com/photos/yourname' ),
		'dribbble'      => array( 'label' => 'Dribbble', 'placeholder' => 'https://dribbble.com/yourname' ),
		'behance'       => array( 'label' => 'Behance', 'placeholder' => 'https://www.behance.net/yourname' ),
		'bilibili'      => array( 'label' => 'Bilibili', 'placeholder' => 'https://space.bilibili.com/yourname' ),
		'xiaohongshu'   => array( 'label' => __( '小红书', 'yneko-reimu' ), 'placeholder' => 'https://www.xiaohongshu.com/user/profile/yourname' ),
		'weibo'         => array( 'label' => 'Weibo', 'placeholder' => 'https://weibo.com/yourname' ),
		'zhihu'         => array( 'label' => 'Zhihu', 'placeholder' => 'https://www.zhihu.com/people/yourname' ),
		'reddit'        => array( 'label' => 'Reddit', 'placeholder' => 'https://www.reddit.com/user/yourname' ),
		'tumblr'        => array( 'label' => 'Tumblr', 'placeholder' => 'https://yourname.tumblr.com' ),
		'medium'        => array( 'label' => 'Medium', 'placeholder' => 'https://medium.com/@yourname' ),
		'deviantart'    => array( 'label' => 'DeviantArt', 'placeholder' => 'https://yourname.deviantart.com' ),
		'stackoverflow' => array( 'label' => 'Stack Overflow', 'placeholder' => 'https://stackoverflow.com/users/yourname' ),
		'keybase'       => array( 'label' => 'Keybase', 'placeholder' => 'https://keybase.io/yourname' ),
		'telegram'      => array( 'label' => 'Telegram', 'placeholder' => 'https://t.me/yourname' ),
		'discord'       => array( 'label' => 'Discord', 'placeholder' => 'https://discordapp.com/users/yourname' ),
		'steam'         => array( 'label' => 'Steam', 'placeholder' => 'https://steamcommunity.com/id/yourname' ),
		'weixin'        => array( 'label' => __( '微信', 'yneko-reimu' ), 'placeholder' => 'https://example.com/your-weixin-link' ),
		'qq'            => array( 'label' => 'QQ', 'placeholder' => 'https://example.com/your-qq-link' ),
		'tiktok'        => array( 'label' => 'TikTok', 'placeholder' => 'https://www.tiktok.com/@yourname' ),
		'rss'           => array( 'label' => 'RSS', 'placeholder' => get_feed_link() ),
	);
}

function yneko_reimu_share_definitions() {
	return array(
		'facebook' => array( 'label' => 'Facebook' ),
		'twitter'  => array( 'label' => 'Twitter / X' ),
		'bluesky'  => array( 'label' => 'Bluesky' ),
		'linkedin' => array( 'label' => 'LinkedIn' ),
		'reddit'   => array( 'label' => 'Reddit' ),
		'weibo'    => array( 'label' => 'Weibo' ),
		'qq'       => array( 'label' => 'QQ' ),
		'weixin'   => array( 'label' => __( '微信', 'yneko-reimu' ) ),
	);
}

function yneko_reimu_social_url( $key ) {
	if ( 'github' === $key ) {
		return yneko_reimu_settings_github_url();
	}

	$legacy_key = 'twitter' === $key ? 'yneko_reimu_social_x' : '';
	$url        = trim( (string) yneko_reimu_get_theme_mod( 'yneko_reimu_social_' . $key, '' ) );
	if ( '' === $url && $legacy_key ) {
		$url = trim( (string) yneko_reimu_get_theme_mod( $legacy_key, '' ) );
	}

	if ( '' === $url && 'rss' === $key ) {
		$url = get_feed_link();
	}

	if ( 'email' === $key && is_email( $url ) ) {
		$url = 'mailto:' . sanitize_email( $url );
	}

	return '' === $url ? '' : esc_url_raw( $url );
}

function yneko_reimu_social_links() {
	$links = array();

	foreach ( yneko_reimu_social_definitions() as $key => $item ) {
		$enabled = yneko_reimu_customizer_bool( 'yneko_reimu_social_' . $key . '_enabled', 'github' === $key );
		$url     = yneko_reimu_social_url( $key );
		if ( ! $enabled || '' === $url ) {
			continue;
		}

		$links[ $key ] = array(
			'label' => $item['label'],
			'url'   => $url,
		);
	}

	return $links;
}

function yneko_reimu_share_url( $key, $post_id = 0 ) {
	$context = yneko_reimu_share_context( $post_id );
	$url     = rawurlencode( $context['url'] );
	$title   = rawurlencode( $context['title'] );
	$desc    = rawurlencode( $context['description'] );
	$source  = rawurlencode( home_url( '/' ) );

	switch ( $key ) {
		case 'facebook':
			return 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		case 'twitter':
			return 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '&via=' . rawurlencode( get_bloginfo( 'name' ) );
		case 'bluesky':
			return 'https://bsky.app/intent/compose?text=' . rawurlencode( $context['title'] . ' ' . $context['url'] );
		case 'linkedin':
			return 'https://www.linkedin.com/shareArticle?url=' . $url . '&title=' . $title . '&summary=' . $desc . '&mini=true&ro=true';
		case 'reddit':
			return 'https://www.reddit.com/submit?url=' . $url . '&title=' . $title;
		case 'weibo':
			return 'https://service.weibo.com/share/share.php?url=' . $url . '&appkey=&title=' . $title . '&pic=&ralateUid=';
		case 'qq':
			return 'https://connect.qq.com/widget/shareqq/index.html?url=' . $url . '&title=' . $title . '&desc=' . $desc . '&source=' . $source;
		case 'weixin':
			return $context['url'];
	}

	return '#';
}

function yneko_reimu_share_links( $post_id = 0 ) {
	$links = array();

	foreach ( yneko_reimu_share_definitions() as $key => $item ) {
		$enabled = yneko_reimu_customizer_bool( 'yneko_reimu_share_' . $key . '_enabled', in_array( $key, array( 'qq', 'weixin' ), true ) );
		if ( ! $enabled ) {
			continue;
		}

		$links[ $key ] = array(
			'label' => $item['label'],
			'url'   => yneko_reimu_share_url( $key, $post_id ),
		);
	}

	return $links;
}

function yneko_reimu_get_post_share_image( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$image = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $image ) {
			return $image;
		}
	}

	return yneko_reimu_get_default_cover_url();
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

function yneko_reimu_player_position() {
	$position = yneko_reimu_get_theme_mod( 'yneko_reimu_player_position', 'before_sidebar' );
	$allowed  = array( 'before_sidebar', 'after_sidebar', 'after_widget' );

	return in_array( $position, $allowed, true ) ? $position : 'before_sidebar';
}

function yneko_reimu_social_icon_class( $key ) {
	$key = sanitize_html_class( $key );
	return 'icon-' . $key;
}
