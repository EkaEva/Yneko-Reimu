<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_defaults() {
	return array(
		'site_avatar_url'   => '',
		'author_avatar_url' => '',
		'comment_avatar_url'=> '',
		'github_url'        => '',
		'friends'           => yneko_reimu_default_friend_items(),
		'sponsor_qr_url'    => '',
		'github_oauth'      => array(
			'client_id'     => '',
			'client_secret' => '',
			'callback_url'  => '',
			'auto_create'   => '0',
		),
		'i18n'              => function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : array(
			'enabled'   => '1',
			'default'   => 'zh_CN',
			'en_prefix' => 'en',
			'zh_label'  => '简体中文',
			'en_label'  => 'English',
		),
		'music'             => array(),
	);
}

function yneko_reimu_default_friend_items() {
	return array(
		array(
			'name'  => 'EkaEva',
			'url'   => 'https://github.com/EkaEva',
			'desc'  => __( 'Yneko-Reimu 主题作者', 'yneko-reimu' ),
			'image' => '',
		),
		array(
			'name'  => '拔剑Sketon',
			'url'   => 'https://d-sketon.github.io/',
			'desc'  => __( 'hexo-theme-reimu 原作者', 'yneko-reimu' ),
			'image' => 'https://d-sketon.github.io/avatar/avatar.webp',
		),
		array(
			'name'  => '天羊EdSky',
			'url'   => 'https://space.bilibili.com/16573583',
			'desc'  => __( '莉莉概念光标作者', 'yneko-reimu' ),
			'image' => yneko_reimu_asset_uri( 'assets/images/tianyang-edsky.jpg' ),
		),
	);
}

function yneko_reimu_normalize_settings_url( $url ) {
	$url = trim( (string) $url );
	return '' === $url ? '' : esc_url_raw( $url );
}

function yneko_reimu_sanitize_friend_items( $items ) {
	$clean = array();
	$seen  = array();

	if ( ! is_array( $items ) ) {
		return $clean;
	}

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$name = sanitize_text_field( $item['name'] ?? '' );
		$url  = yneko_reimu_normalize_settings_url( $item['url'] ?? '' );
		$desc = sanitize_text_field( $item['desc'] ?? '' );
		$image = yneko_reimu_normalize_settings_url( $item['image'] ?? '' );

		if ( '' === $name || '' === $url ) {
			continue;
		}

		$key = strtolower( untrailingslashit( $url ) );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}

		$seen[ $key ] = true;
		$clean[] = array(
			'name'  => $name,
			'url'   => $url,
			'desc'  => $desc,
			'image' => $image,
		);
	}

	return $clean;
}

function yneko_reimu_sanitize_music_items( $items ) {
	$clean = array();

	if ( ! is_array( $items ) ) {
		return $clean;
	}

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$name   = sanitize_text_field( $item['name'] ?? '' );
		$artist = sanitize_text_field( $item['artist'] ?? '' );
		$url    = yneko_reimu_normalize_settings_url( $item['url'] ?? '' );
		$cover  = yneko_reimu_normalize_settings_url( $item['cover'] ?? '' );
		$lrc    = yneko_reimu_normalize_settings_url( $item['lrc'] ?? '' );
		$theme  = sanitize_hex_color( $item['theme'] ?? '' );

		if ( '' === $name || '' === $url ) {
			continue;
		}

		$clean[] = array(
			'name'   => $name,
			'artist' => $artist,
			'url'    => $url,
			'cover'  => $cover,
			'lrc'    => $lrc,
			'theme'  => $theme ? $theme : '#ff5252',
		);
	}

	return $clean;
}

function yneko_reimu_sanitize_settings( $input ) {
	$defaults = yneko_reimu_settings_defaults();
	$input    = is_array( $input ) ? $input : array();
	$oauth    = isset( $input['github_oauth'] ) && is_array( $input['github_oauth'] ) ? $input['github_oauth'] : array();
	$i18n     = isset( $input['i18n'] ) && is_array( $input['i18n'] ) ? $input['i18n'] : array();
	$i18n_default = function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : $defaults['i18n'];
	$i18n_default_language = isset( $i18n['default'] ) && 'en_US' === $i18n['default'] ? 'en_US' : 'zh_CN';
	$i18n_prefix = trim( sanitize_title( $i18n['en_prefix'] ?? $i18n_default['en_prefix'] ), '/' );

	return array(
		'site_avatar_url'   => yneko_reimu_normalize_settings_url( $input['site_avatar_url'] ?? '' ),
		'author_avatar_url' => yneko_reimu_normalize_settings_url( $input['author_avatar_url'] ?? '' ),
		'comment_avatar_url'=> yneko_reimu_normalize_settings_url( $input['comment_avatar_url'] ?? '' ),
		'github_url'        => yneko_reimu_normalize_settings_url( $input['github_url'] ?? '' ),
		'friends'           => yneko_reimu_sanitize_friend_items( array_key_exists( 'friends', $input ) ? $input['friends'] : array() ),
		'sponsor_qr_url'    => yneko_reimu_normalize_settings_url( $input['sponsor_qr_url'] ?? '' ),
		'github_oauth'      => array(
			'client_id'     => sanitize_text_field( $oauth['client_id'] ?? '' ),
			'client_secret' => sanitize_text_field( $oauth['client_secret'] ?? '' ),
			'callback_url'  => yneko_reimu_normalize_settings_url( $oauth['callback_url'] ?? '' ),
			'auto_create'   => ! empty( $oauth['auto_create'] ) ? '1' : '0',
		),
		'i18n'              => array(
			'enabled'   => ! empty( $i18n['enabled'] ) ? '1' : '0',
			'default'   => $i18n_default_language,
			'en_prefix' => $i18n_prefix ? $i18n_prefix : 'en',
			'zh_label'  => sanitize_text_field( $i18n['zh_label'] ?? $i18n_default['zh_label'] ),
			'en_label'  => sanitize_text_field( $i18n['en_label'] ?? $i18n_default['en_label'] ),
		),
		'music'             => yneko_reimu_sanitize_music_items( array_key_exists( 'music', $input ) ? $input['music'] : array() ),
	);
}

function yneko_reimu_settings() {
	$settings = get_option( 'yneko_reimu_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$defaults = yneko_reimu_settings_defaults();

	foreach ( array( 'friends', 'music' ) as $list_key ) {
		if ( array_key_exists( $list_key, $settings ) && is_array( $settings[ $list_key ] ) && ! $settings[ $list_key ] ) {
			$defaults[ $list_key ] = array();
		}
	}

	return wp_parse_args( $settings, $defaults );
}

function yneko_reimu_setting( $key, $default = '' ) {
	$settings = yneko_reimu_settings();
	return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
}

function yneko_reimu_settings_github_url() {
	$github = yneko_reimu_setting( 'github_url', '' );
	if ( $github ) {
		return esc_url_raw( $github );
	}

	return esc_url_raw( yneko_reimu_get_theme_mod( 'yneko_reimu_social_github', '' ) );
}

function yneko_reimu_settings_sponsor_qr_url() {
	$qr = yneko_reimu_setting( 'sponsor_qr_url', '' );
	if ( $qr ) {
		return esc_url_raw( $qr );
	}

	return esc_url_raw( yneko_reimu_get_theme_mod( 'yneko_reimu_sponsor_qr', '' ) );
}

function yneko_reimu_settings_comment_avatar_url() {
	$avatar = yneko_reimu_setting( 'comment_avatar_url', '' );
	return $avatar ? esc_url_raw( $avatar ) : '';
}

function yneko_reimu_settings_friend_items() {
	$raw = get_option( 'yneko_reimu_settings', null );
	if ( is_array( $raw ) && array_key_exists( 'friends', $raw ) ) {
		return yneko_reimu_sanitize_friend_items( $raw['friends'] );
	}

	$settings = yneko_reimu_settings();
	$friends  = isset( $settings['friends'] ) && is_array( $settings['friends'] ) ? $settings['friends'] : array();

	if ( $friends ) {
		return yneko_reimu_sanitize_friend_items( $friends );
	}

	return yneko_reimu_default_friend_items();
}

function yneko_reimu_settings_music_items() {
	$raw = get_option( 'yneko_reimu_settings', null );
	if ( is_array( $raw ) && array_key_exists( 'music', $raw ) ) {
		return yneko_reimu_sanitize_music_items( $raw['music'] );
	}

	$settings = yneko_reimu_settings();
	$music    = isset( $settings['music'] ) && is_array( $settings['music'] ) ? $settings['music'] : array();

	if ( $music ) {
		return yneko_reimu_sanitize_music_items( $music );
	}

	$legacy = yneko_reimu_json_theme_mod( 'yneko_reimu_aplayer_audio_json', '' );
	return $legacy ? yneko_reimu_sanitize_music_items( $legacy ) : array();
}

function yneko_reimu_settings_github_oauth() {
	$settings = yneko_reimu_settings();
	$oauth    = isset( $settings['github_oauth'] ) && is_array( $settings['github_oauth'] ) ? $settings['github_oauth'] : array();
	$legacy   = get_option( 'yneko_reimu_github_login_options', array() );

	if ( is_array( $legacy ) ) {
		$oauth = yneko_reimu_merge_github_oauth_fallback( $oauth, $legacy );
	}

	if ( empty( $oauth['client_id'] ) || empty( $oauth['client_secret'] ) ) {
		$old_legacy = get_option( 'yneko_github_login_options', array() );
		if ( is_array( $old_legacy ) ) {
			$oauth = yneko_reimu_merge_github_oauth_fallback( $oauth, $old_legacy );
		}
	}

	return wp_parse_args(
		$oauth,
		array(
			'client_id'     => '',
			'client_secret' => '',
			'callback_url'  => '',
			'auto_create'   => '0',
		)
	);
}

function yneko_reimu_merge_github_oauth_fallback( $oauth, $fallback ) {
	$oauth    = is_array( $oauth ) ? $oauth : array();
	$fallback = is_array( $fallback ) ? $fallback : array();

	foreach ( array( 'client_id', 'client_secret', 'callback_url', 'auto_create' ) as $key ) {
		if ( empty( $oauth[ $key ] ) && isset( $fallback[ $key ] ) && '' !== $fallback[ $key ] ) {
			$oauth[ $key ] = $fallback[ $key ];
		}
	}

	return $oauth;
}

function yneko_reimu_register_settings() {
	register_setting(
		'yneko_reimu_settings',
		'yneko_reimu_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'yneko_reimu_sanitize_settings',
			'default'           => yneko_reimu_settings_defaults(),
		)
	);
}
add_action( 'admin_init', 'yneko_reimu_register_settings' );

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

function yneko_reimu_admin_media_field( $name, $value, $label ) {
	?>
	<div class="yneko-reimu-media-field">
		<input type="url" class="regular-text yneko-reimu-media-url" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( $label ); ?></button>
	</div>
	<?php
}

function yneko_reimu_admin_bilingual_text( $zh, $en, $tag = 'span' ) {
	$tag = in_array( $tag, array( 'span', 'p', 'div', 'button' ), true ) ? $tag : 'span';
	return sprintf(
		'<%1$s class="yneko-reimu-bi"><span class="yneko-reimu-bi-zh">%2$s</span><span class="yneko-reimu-bi-en">%3$s</span></%1$s>',
		tag_escape( $tag ),
		esc_html( $zh ),
		esc_html( $en )
	);
}

function yneko_reimu_admin_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_description( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en, 'p' ) );
}

function yneko_reimu_admin_bilingual_heading( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_button_text( $zh, $en ) {
	return yneko_reimu_admin_bilingual_text( $zh, $en );
}

function yneko_reimu_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = yneko_reimu_settings();
	$oauth    = yneko_reimu_settings_github_oauth();
	$i18n     = isset( $settings['i18n'] ) && is_array( $settings['i18n'] ) ? wp_parse_args( $settings['i18n'], yneko_reimu_i18n_defaults() ) : yneko_reimu_i18n_defaults();
	$callback = function_exists( 'yneko_reimu_github_login_callback_url' ) ? yneko_reimu_github_login_callback_url() : add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() );
	?>
	<div class="wrap yneko-reimu-settings-page">
		<h1><?php esc_html_e( 'Yneko-Reimu 设置', 'yneko-reimu' ); ?></h1>
		<?php yneko_reimu_admin_bilingual_description( '这些内容保存在 WordPress 数据库中，不会写入主题源码或主题包。', 'These settings are stored in the WordPress database and are never written into the theme source or release package.' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'yneko_reimu_settings' ); ?>
			<h2><?php yneko_reimu_admin_bilingual_heading( '站点资料', 'Site profile' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '站点头像', 'Site avatar' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[site_avatar_url]', $settings['site_avatar_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
						<?php yneko_reimu_admin_bilingual_description( '用于站点图标、默认 logo、分享图标兜底等站点级图片。', 'Used as the site icon, default logo, and fallback sharing image.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '作者头像', 'Author avatar' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[author_avatar_url]', $settings['author_avatar_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
						<?php yneko_reimu_admin_bilingual_description( '用于前台侧栏作者卡、页面角色图、友链/项目缺省图；不覆盖 WordPress 用户资料头像。', 'Used by the front-end author card, character image, and friend/project fallback images; it does not override WordPress user avatars.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '游客评论头像', 'Guest comment avatar' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[comment_avatar_url]', $settings['comment_avatar_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
						<?php yneko_reimu_admin_bilingual_description( '用于未登录访客评论的默认头像。留空时使用 One User Avatar 的全站默认头像，再留空则使用作者头像。', 'Default avatar for logged-out commenters. If empty, One User Avatar site default is used first, then the author avatar.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-github-url"><?php yneko_reimu_admin_bilingual_label( 'GitHub 主页链接', 'GitHub profile URL' ); ?></label></th>
					<td>
						<input id="yneko-reimu-github-url" class="regular-text" type="url" name="yneko_reimu_settings[github_url]" value="<?php echo esc_attr( $settings['github_url'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。', 'Used by the GitHub corner ribbon, sidebar GitHub link, and project-page repository source.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '赞助二维码', 'Sponsor QR code' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[sponsor_qr_url]', $settings['sponsor_qr_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
						<?php yneko_reimu_admin_bilingual_description( '用于底部赞助入口。留空时不会显示赞助二维码。', 'Used by the footer sponsor entry. If empty, the sponsor QR code is hidden.' ); ?>
					</td>
				</tr>
			</table>

			<h2><?php yneko_reimu_admin_bilingual_heading( '多语言设置', 'Multilingual settings' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '启用语言切换', 'Enable language switcher' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[i18n][enabled]" value="1" <?php checked( '1', $i18n['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示前台语言切换入口', 'Show the front-end language switcher' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '默认语言', 'Default language' ); ?></th>
					<td>
						<select name="yneko_reimu_settings[i18n][default]">
							<option value="zh_CN" <?php selected( $i18n['default'], 'zh_CN' ); ?>>简体中文 / Simplified Chinese</option>
							<option value="en_US" <?php selected( $i18n['default'], 'en_US' ); ?>>English / 英文</option>
						</select>
						<?php yneko_reimu_admin_bilingual_description( '默认建议保持简体中文，中文内容继续使用站点原始地址。', 'Keeping Simplified Chinese as the default is recommended; Chinese content keeps the original site URLs.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-en-prefix"><?php yneko_reimu_admin_bilingual_label( '英文路径前缀', 'English URL prefix' ); ?></label></th>
					<td>
						<input id="yneko-reimu-en-prefix" class="regular-text" type="text" name="yneko_reimu_settings[i18n][en_prefix]" value="<?php echo esc_attr( $i18n['en_prefix'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '例如 en 会让英文内容使用 /en/ 开头的地址。', 'For example, en makes English content use URLs starting with /en/.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-zh-label"><?php yneko_reimu_admin_bilingual_label( '中文显示名', 'Chinese label' ); ?></label></th>
					<td>
						<input id="yneko-reimu-zh-label" class="regular-text" type="text" name="yneko_reimu_settings[i18n][zh_label]" value="<?php echo esc_attr( $i18n['zh_label'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '显示在前台语言切换菜单中的中文名称。', 'The Chinese language name shown in the front-end language switcher.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-en-label"><?php yneko_reimu_admin_bilingual_label( '英文显示名', 'English label' ); ?></label></th>
					<td>
						<input id="yneko-reimu-en-label" class="regular-text" type="text" name="yneko_reimu_settings[i18n][en_label]" value="<?php echo esc_attr( $i18n['en_label'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '显示在前台语言切换菜单中的英文名称。', 'The English language name shown in the front-end language switcher.' ); ?>
					</td>
				</tr>
			</table>

			<h2><?php yneko_reimu_admin_bilingual_heading( 'GitHub 登录', 'GitHub login' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="yneko-reimu-callback-url"><?php yneko_reimu_admin_bilingual_label( '回调地址', 'Callback URL' ); ?></label></th>
					<td>
						<input id="yneko-reimu-callback-url" class="regular-text" type="url" name="yneko_reimu_settings[github_oauth][callback_url]" value="<?php echo esc_attr( $oauth['callback_url'] ); ?>" placeholder="<?php echo esc_attr( $callback ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '留空时自动使用下方默认地址；如果站点经过反向代理、固定域名或特殊登录路径，可在这里覆盖。GitHub OAuth App 中的 Authorization callback URL 需要与最终地址完全一致。', 'Leave empty to use the default URL below. Override it when the site uses a reverse proxy, fixed public domain, or custom login path. The Authorization callback URL in GitHub OAuth App must match the final URL exactly.' ); ?>
						<p class="description"><code><?php echo esc_html( $callback ); ?></code></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-id"><?php yneko_reimu_admin_bilingual_label( '客户端 ID', 'Client ID' ); ?></label></th>
					<td>
						<input id="yneko-reimu-client-id" class="regular-text" type="text" name="yneko_reimu_settings[github_oauth][client_id]" value="<?php echo esc_attr( $oauth['client_id'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '填写 GitHub OAuth App 提供的 Client ID。留空时前台不显示 GitHub 登录按钮。', 'Enter the Client ID from your GitHub OAuth App. If empty, the GitHub login button is hidden on the front end.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-secret"><?php yneko_reimu_admin_bilingual_label( '客户端密钥', 'Client Secret' ); ?></label></th>
					<td>
						<input id="yneko-reimu-client-secret" class="regular-text" type="password" autocomplete="off" name="yneko_reimu_settings[github_oauth][client_secret]" value="<?php echo esc_attr( $oauth['client_secret'] ); ?>">
						<?php yneko_reimu_admin_bilingual_description( '密钥只保存在 WordPress 数据库中，不会写入主题源码或发布包。', 'The secret is stored only in the WordPress database and is never written into the theme source or release package.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自动创建用户', 'Auto-create users' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[github_oauth][auto_create]" value="1" <?php checked( '1', $oauth['auto_create'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许 GitHub 登录自动创建 WordPress 用户', 'Allow GitHub login to create WordPress users automatically' ); ?></label></td>
				</tr>
			</table>

			<h2><?php yneko_reimu_admin_bilingual_heading( '友链列表', 'Friend links' ); ?></h2>
			<?php yneko_reimu_admin_bilingual_description( '用于友链页面的卡片列表，支持名称、链接、描述和头像。', 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.' ); ?>
			<div class="yneko-reimu-repeatable" data-repeatable="friends">
				<div class="yneko-reimu-repeatable-list">
					<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
						<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增友链', 'Add friend' ) ); ?></button>
			</div>

			<h2><?php yneko_reimu_admin_bilingual_heading( '音乐列表', 'Music playlist' ); ?></h2>
			<?php yneko_reimu_admin_bilingual_description( '播放器曲目从媒体库读取。未配置曲目时，前台不会加载音乐播放器。', 'The player reads tracks from the Media Library. If no tracks are configured, the front-end music player is not loaded.' ); ?>
			<div class="yneko-reimu-repeatable" data-repeatable="music">
				<div class="yneko-reimu-repeatable-list">
					<?php foreach ( yneko_reimu_sanitize_music_items( $settings['music'] ) as $index => $track ) : ?>
						<?php yneko_reimu_render_music_row( $index, $track ); ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button yneko-reimu-add-row" data-template="music"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增曲目', 'Add track' ) ); ?></button>
			</div>

			<p class="submit">
				<button type="submit" class="button button-primary yneko-reimu-submit-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '保存设置', 'Save settings' ) ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

function yneko_reimu_render_friend_row( $index, $friend = array() ) {
	$friend = wp_parse_args(
		$friend,
		array(
			'name'  => '',
			'url'   => '',
			'desc'  => '',
			'image' => '',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-friend">
			<label><?php yneko_reimu_admin_bilingual_label( '名称', 'Name' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $friend['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '链接', 'URL' ); ?><input type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $friend['url'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '描述', 'Description' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][desc]" value="<?php echo esc_attr( $friend['desc'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '头像', 'Avatar' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][image]" value="<?php echo esc_attr( $friend['image'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_music_row( $index, $track = array() ) {
	$track = wp_parse_args(
		$track,
		array(
			'name'   => '',
			'artist' => '',
			'url'    => '',
			'cover'  => '',
			'lrc'    => '',
			'theme'  => '#ff5252',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-music">
			<label><?php yneko_reimu_admin_bilingual_label( '歌名', 'Track title' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $track['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '作者', 'Artist' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][artist]" value="<?php echo esc_attr( $track['artist'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '音频', 'Audio' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $track['url'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '封面', 'Cover' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][cover]" value="<?php echo esc_attr( $track['cover'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '歌词 LRC', 'Lyrics LRC' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][lrc]" value="<?php echo esc_attr( $track['lrc'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '主题色', 'Theme color' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][theme]" value="<?php echo esc_attr( $track['theme'] ); ?>"></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_enqueue_settings_admin_assets( $hook ) {
	if ( 'appearance_page_yneko-reimu-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_register_style( 'yneko-reimu-admin-settings', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-admin-settings' );
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-settings-page h2{margin-top:32px}.yneko-reimu-bi{display:inline-flex;flex-direction:column;gap:2px;line-height:1.35}.yneko-reimu-bi-en{font-size:12px;color:#646970;font-weight:400}.description.yneko-reimu-bi,.yneko-reimu-bi.description,.yneko-reimu-settings-page p.yneko-reimu-bi{margin:6px 0 0;color:#646970}.yneko-reimu-settings-page .button .yneko-reimu-bi{vertical-align:middle;text-align:left}.yneko-reimu-submit-button .yneko-reimu-bi{color:#fff}.yneko-reimu-submit-button .yneko-reimu-bi-en{color:rgba(255,255,255,.82)}.yneko-reimu-media-field,.yneko-reimu-inline-media{display:flex;gap:8px;align-items:center}.yneko-reimu-inline-media input{flex:1;min-width:0}.yneko-reimu-repeatable-row{margin:14px 0;padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-row-grid{display:grid;gap:12px}.yneko-reimu-row-grid-friend{grid-template-columns:repeat(4,minmax(0,1fr))}.yneko-reimu-row-grid-music{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-row-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.yneko-reimu-row-grid input{width:100%}.yneko-reimu-row-actions{display:flex;gap:8px;margin-top:12px}@media(max-width:960px){.yneko-reimu-row-grid-friend,.yneko-reimu-row-grid-music{grid-template-columns:1fr}}'
	);

	wp_register_script( 'yneko-reimu-admin-settings', false, array( 'jquery' ), YNEKO_REIMU_VERSION, true );
	wp_enqueue_script( 'yneko-reimu-admin-settings' );
	$admin_i18n = array(
		'mediaTitle'      => array( 'zh' => '选择媒体', 'en' => 'Select media' ),
		'useMedia'        => array( 'zh' => '使用此媒体', 'en' => 'Use this media' ),
		'choose'          => array( 'zh' => '选择', 'en' => 'Choose' ),
		'remove'          => array( 'zh' => '删除', 'en' => 'Remove' ),
		'name'            => array( 'zh' => '名称', 'en' => 'Name' ),
		'url'             => array( 'zh' => '链接', 'en' => 'URL' ),
		'description'     => array( 'zh' => '描述', 'en' => 'Description' ),
		'avatar'          => array( 'zh' => '头像', 'en' => 'Avatar' ),
		'trackTitle'      => array( 'zh' => '歌名', 'en' => 'Track title' ),
		'artist'          => array( 'zh' => '作者', 'en' => 'Artist' ),
		'audio'           => array( 'zh' => '音频', 'en' => 'Audio' ),
		'cover'           => array( 'zh' => '封面', 'en' => 'Cover' ),
		'lyrics'          => array( 'zh' => '歌词 LRC', 'en' => 'Lyrics LRC' ),
		'themeColor'      => array( 'zh' => '主题色', 'en' => 'Theme color' ),
	);
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		'window.YNEKO_REIMU_ADMIN_I18N=' . wp_json_encode( $admin_i18n ) . ';' .
		"(function(){var labels=window.YNEKO_REIMU_ADMIN_I18N||{};var counters={friend:Date.now(),music:Date.now()+1000};function esc(value){return String(value||'').replace(/[&<>\"']/g,function(chr){return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',\"'\":'&#039;'}[chr];});}function plain(key,zh,en){var item=labels[key]||{};return (item.zh||zh)+' / '+(item.en||en);}function bi(key,zh,en){var item=labels[key]||{};return '<span class=\"yneko-reimu-bi\"><span class=\"yneko-reimu-bi-zh\">'+esc(item.zh||zh)+'</span><span class=\"yneko-reimu-bi-en\">'+esc(item.en||en)+'</span></span>';}function fieldLabel(key,zh,en,control){return '<label>'+bi(key,zh,en)+control+'</label>';}function media(button){var field=button.closest('.yneko-reimu-inline-media')||button.closest('.yneko-reimu-media-field');var input=field?field.querySelector('.yneko-reimu-media-url'):null;if(!input||!window.wp||!wp.media){return;}var frame=wp.media({title:plain('mediaTitle','选择媒体','Select media'),button:{text:plain('useMedia','使用此媒体','Use this media')},multiple:false});frame.on('select',function(){var attachment=frame.state().get('selection').first().toJSON();input.value=attachment.url||'';input.dispatchEvent(new Event('change',{bubbles:true}));});frame.open();}function pickButton(){return '<button type=\"button\" class=\"button yneko-reimu-media-button\">'+bi('choose','选择','Choose')+'</button>';}function mediaInput(name){return '<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"'+name+'\">'+pickButton()+'</span>';}function friendTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\"><div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-friend\">'+fieldLabel('name','名称','Name','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][name]\">')+fieldLabel('url','链接','URL','<input type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][url]\">')+fieldLabel('description','描述','Description','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][desc]\">')+fieldLabel('avatar','头像','Avatar','<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][image]\">'+pickButton()+'</span>')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+bi('remove','删除','Remove')+'</button></div></div>';}function musicTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\"><div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-music\">'+fieldLabel('trackTitle','歌名','Track title','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][name]\">')+fieldLabel('artist','作者','Artist','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][artist]\">')+fieldLabel('audio','音频','Audio',mediaInput('yneko_reimu_settings[music]['+i+'][url]'))+fieldLabel('cover','封面','Cover',mediaInput('yneko_reimu_settings[music]['+i+'][cover]'))+fieldLabel('lyrics','歌词 LRC','Lyrics LRC',mediaInput('yneko_reimu_settings[music]['+i+'][lrc]'))+fieldLabel('themeColor','主题色','Theme color','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][theme]\" value=\"#ff5252\">')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+bi('remove','删除','Remove')+'</button></div></div>';}document.addEventListener('click',function(event){var target=event.target;if(target.closest('.yneko-reimu-media-button')){event.preventDefault();media(target.closest('.yneko-reimu-media-button'));}if(target.closest('.yneko-reimu-remove-row')){event.preventDefault();target.closest('.yneko-reimu-repeatable-row').remove();}var add=target.closest('.yneko-reimu-add-row');if(add){event.preventDefault();var type=add.dataset.template;var list=add.closest('.yneko-reimu-repeatable').querySelector('.yneko-reimu-repeatable-list');var i=counters[type]++;list.insertAdjacentHTML('beforeend',type==='friend'?friendTemplate(i):musicTemplate(i));}});}());"
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_enqueue_settings_admin_assets' );
