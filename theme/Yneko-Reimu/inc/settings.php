<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_defaults() {
	return array(
		'site_avatar_url'   => '',
		'author_avatar_url' => '',
		'comment_avatar_url'=> '',
		'comment_upload'    => array(
			'enabled'      => '1',
			'image_max_mb' => 1,
			'gif_max_mb'   => 1,
		),
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
	$upload   = isset( $input['comment_upload'] ) && is_array( $input['comment_upload'] ) ? $input['comment_upload'] : array();
	$i18n     = isset( $input['i18n'] ) && is_array( $input['i18n'] ) ? $input['i18n'] : array();
	$i18n_default = function_exists( 'yneko_reimu_i18n_defaults' ) ? yneko_reimu_i18n_defaults() : $defaults['i18n'];
	$i18n_default_language = isset( $i18n['default'] ) && 'en_US' === $i18n['default'] ? 'en_US' : 'zh_CN';
	$i18n_prefix = trim( sanitize_title( $i18n['en_prefix'] ?? $i18n_default['en_prefix'] ), '/' );

	return array(
		'site_avatar_url'   => yneko_reimu_normalize_settings_url( $input['site_avatar_url'] ?? '' ),
		'author_avatar_url' => yneko_reimu_normalize_settings_url( $input['author_avatar_url'] ?? '' ),
		'comment_avatar_url'=> yneko_reimu_normalize_settings_url( $input['comment_avatar_url'] ?? '' ),
		'comment_upload'    => array(
			'enabled'      => ! empty( $upload['enabled'] ) ? '1' : '0',
			'image_max_mb' => max( 1, min( 20, absint( $upload['image_max_mb'] ?? 1 ) ) ),
			'gif_max_mb'   => max( 1, min( 30, absint( $upload['gif_max_mb'] ?? 1 ) ) ),
		),
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

function yneko_reimu_settings_comment_upload() {
	$settings = yneko_reimu_settings();
	$upload   = isset( $settings['comment_upload'] ) && is_array( $settings['comment_upload'] ) ? $settings['comment_upload'] : array();

	return wp_parse_args(
		$upload,
		array(
			'enabled'      => '1',
			'image_max_mb' => 1,
			'gif_max_mb'   => 1,
		)
	);
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
	$text = yneko_reimu_admin_prefers_zh() ? $zh : $en;
	return sprintf(
		'<%1$s class="yneko-reimu-admin-text">%2$s</%1$s>',
		tag_escape( $tag ),
		esc_html( $text )
	);
}

function yneko_reimu_admin_prefers_zh() {
	$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	return 0 === strpos( strtolower( str_replace( '-', '_', (string) $locale ) ), 'zh_' );
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
			<nav class="nav-tab-wrapper yneko-reimu-settings-tabs" aria-label="<?php esc_attr_e( 'Yneko-Reimu 设置分类', 'yneko-reimu' ); ?>">
				<button type="button" class="nav-tab nav-tab-active" data-yneko-settings-tab="general"><?php yneko_reimu_admin_bilingual_label( '常规设置', 'General' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="github"><?php yneko_reimu_admin_bilingual_label( 'GitHub 登录设置', 'GitHub login' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="i18n"><?php yneko_reimu_admin_bilingual_label( '多语言设置', 'Multilingual' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="comments"><?php yneko_reimu_admin_bilingual_label( '评论设置', 'Comments' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="friends"><?php yneko_reimu_admin_bilingual_label( '友链设置', 'Friend links' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="music"><?php yneko_reimu_admin_bilingual_label( '曲目设置', 'Music' ); ?></button>
			</nav>

			<section class="yneko-reimu-settings-panel is-active" data-yneko-settings-panel="general">
				<h2><?php yneko_reimu_admin_bilingual_heading( '常规设置', 'General settings' ); ?></h2>
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
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="i18n" hidden>
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
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="github" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( 'GitHub 登录设置', 'GitHub login settings' ); ?></h2>
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
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '绑定当前账户', 'Bind current account' ); ?></th>
					<td>
						<?php
						$github_bind_url   = function_exists( 'yneko_reimu_github_login_bind_url' ) ? yneko_reimu_github_login_bind_url( admin_url( 'themes.php?page=yneko-reimu-settings' ) ) : '';
						$github_bound_name = get_user_meta( get_current_user_id(), '_yneko_reimu_github_login', true );
						?>
						<?php if ( $github_bound_name ) : ?>
							<p class="description">
								<?php
								printf(
									/* translators: %s: GitHub username. */
									esc_html__( '当前账户已绑定 GitHub：%s', 'yneko-reimu' ),
									esc_html( $github_bound_name )
								);
								?>
							</p>
						<?php endif; ?>
						<?php if ( $github_bind_url ) : ?>
							<p><a class="button" href="<?php echo esc_url( $github_bind_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '绑定/重新绑定 GitHub', 'Bind/Rebind GitHub' ); ?></a></p>
						<?php endif; ?>
						<?php yneko_reimu_admin_bilingual_description( '普通 GitHub 登录不会绑定当前 WordPress 用户；只有点击这里的绑定按钮才会把授权的 GitHub 账号绑定到当前账户。', 'Normal GitHub login never binds the current WordPress user. Only this binding button links the authorized GitHub account to the current account.' ); ?>
					</td>
				</tr>
				</table>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="comments" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '评论设置', 'Comment settings' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '游客评论头像', 'Guest comment avatar' ); ?></th>
						<td>
							<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[comment_avatar_url]', $settings['comment_avatar_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
							<?php yneko_reimu_admin_bilingual_description( '用于未登录访客评论的默认头像。留空时使用 One User Avatar 的全站默认头像，再留空则使用作者头像。', 'Default avatar for logged-out commenters. If empty, One User Avatar site default is used first, then the author avatar.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '评论图片上传', 'Comment image uploads' ); ?></th>
						<td>
							<?php $comment_upload = yneko_reimu_settings_comment_upload(); ?>
							<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][enabled]" value="1" <?php checked( '1', $comment_upload['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户在评论区上传图片和 GIF', 'Allow logged-in users to upload images and GIFs in comments' ); ?></label>
							<p>
								<label><?php yneko_reimu_admin_bilingual_label( '图片上限 MB', 'Image max MB' ); ?> <input class="small-text" type="number" min="1" max="20" name="yneko_reimu_settings[comment_upload][image_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['image_max_mb'] ) ); ?>"></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( 'GIF 上限 MB', 'GIF max MB' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][gif_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['gif_max_mb'] ) ); ?>"></label>
							</p>
							<?php yneko_reimu_admin_bilingual_description( '评论上传文件会存入 uploads/yneko-reimu-comments/ 专用目录，并默认从媒体库列表隐藏；GIF 需要在下方批准后才会出现在游客可选表情库。', 'Comment uploads are stored under the dedicated uploads/yneko-reimu-comments/ directory and hidden from the default Media Library list. GIFs appear in the public picker only after approval below.' ); ?>
						</td>
					</tr>
				</table>

				<h2><?php yneko_reimu_admin_bilingual_heading( '评论上传管理', 'Comment upload manager' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '登录用户上传的图片和 GIF 会集中显示在这里。图片可删除；GIF 会先进入待审核，批准后游客和登录用户都能在评论区 GIF 面板中选择使用。', 'Images and GIFs uploaded by logged-in users are listed here. Images can be deleted; GIFs stay pending first and appear in the public comment GIF picker after approval.' ); ?>
				<?php yneko_reimu_render_admin_comment_gif_upload(); ?>
				<?php yneko_reimu_render_comment_upload_admin(); ?>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="friends" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '友链设置', 'Friend link settings' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '用于友链页面的卡片列表，支持名称、链接、描述和头像。', 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.' ); ?>
				<div class="yneko-reimu-repeatable" data-repeatable="friends">
					<div class="yneko-reimu-repeatable-list">
						<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
							<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增友链', 'Add friend' ) ); ?></button>
				</div>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="music" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '曲目设置', 'Music track settings' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '播放器曲目从媒体库读取。未配置曲目时，前台不会加载音乐播放器。', 'The player reads tracks from the Media Library. If no tracks are configured, the front-end music player is not loaded.' ); ?>
				<div class="yneko-reimu-repeatable" data-repeatable="music">
					<div class="yneko-reimu-repeatable-list">
						<?php foreach ( yneko_reimu_sanitize_music_items( $settings['music'] ) as $index => $track ) : ?>
							<?php yneko_reimu_render_music_row( $index, $track ); ?>
						<?php endforeach; ?>
					</div>
					<button type="button" class="button yneko-reimu-add-row" data-template="music"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增曲目', 'Add track' ) ); ?></button>
				</div>
			</section>

			<div class="yneko-reimu-floating-submit">
				<span class="yneko-reimu-floating-submit__hint"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_text( '切换标签页不会保存修改。', 'Switching tabs does not save changes.' ) ); ?></span>
				<button type="submit" class="button button-primary yneko-reimu-submit-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '保存设置', 'Save settings' ) ); ?></button>
			</div>
		</form>
		<form id="yneko-reimu-admin-gif-upload-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ); ?>">
			<?php wp_nonce_field( 'yneko_reimu_admin_comment_gif_upload' ); ?>
			<input type="hidden" name="yneko_reimu_admin_comment_gif_upload" value="1">
		</form>
	</div>
	<?php
}

function yneko_reimu_render_admin_comment_gif_upload() {
	$status = isset( $_GET['yneko_comment_gif_upload'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_gif_upload'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$count  = isset( $_GET['yneko_comment_gif_count'] ) ? absint( $_GET['yneko_comment_gif_count'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $status ) {
		$messages = array(
			'success' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF 并加入表情库。', '已上传 %d 个 GIF 并加入表情库。', $count, 'yneko-reimu' ),
				$count
			) : __( 'GIF 已上传并加入表情库。', 'yneko-reimu' ),
			'partial' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF，部分文件未成功。', '已上传 %d 个 GIF，部分文件未成功。', $count, 'yneko-reimu' ),
				$count
			) : __( '部分 GIF 上传失败。', 'yneko-reimu' ),
			'empty'   => __( '请选择要上传的 GIF。', 'yneko-reimu' ),
			'invalid' => __( '仅支持未超出大小限制的 GIF 文件。', 'yneko-reimu' ),
			'failed'  => __( 'GIF 上传失败。', 'yneko-reimu' ),
		);
		$class = in_array( $status, array( 'success', 'partial' ), true ) ? 'notice notice-success inline' : 'notice notice-error inline';
		echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $messages[ $status ] ?? $messages['failed'] ) . '</p></div>';
	}
	?>
	<div class="yneko-reimu-admin-gif-upload">
		<label>
			<?php yneko_reimu_admin_bilingual_label( '管理员上传 GIF', 'Admin upload GIF' ); ?>
			<input form="yneko-reimu-admin-gif-upload-form" type="file" name="yneko_reimu_comment_gif[]" accept="image/gif" multiple>
		</label>
		<button form="yneko-reimu-admin-gif-upload-form" type="submit" class="button"><?php yneko_reimu_admin_bilingual_label( '上传并加入表情库', 'Upload and add to GIF library' ); ?></button>
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
		<div class="yneko-reimu-row-heading" data-row-label="friend">
			<span class="yneko-reimu-row-number"></span>
		</div>
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
		<div class="yneko-reimu-row-heading" data-row-label="music">
			<span class="yneko-reimu-row-number"></span>
		</div>
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

function yneko_reimu_render_comment_upload_admin() {
	if ( ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return;
	}

	$items = yneko_reimu_comment_upload_library( 80, 'all', true );
	if ( ! $items ) {
		echo '<p class="description">' . esc_html__( '暂无评论图片或 GIF 上传。', 'yneko-reimu' ) . '</p>';
		return;
	}
	$groups = array(
		'admin_gif'  => array(
			'title' => __( '后台上传的 GIF', 'yneko-reimu' ),
			'items' => array(),
		),
		'user_gif'   => array(
			'title' => __( '用户评论 GIF', 'yneko-reimu' ),
			'items' => array(),
		),
		'user_image' => array(
			'title' => __( '用户评论图片', 'yneko-reimu' ),
			'items' => array(),
		),
	);
	foreach ( $items as $item ) {
		if ( 'gif' === $item['type'] && empty( $item['comment_id'] ) ) {
			$groups['admin_gif']['items'][] = $item;
		} elseif ( 'gif' === $item['type'] ) {
			$groups['user_gif']['items'][] = $item;
		} else {
			$groups['user_image']['items'][] = $item;
		}
	}
	?>
	<?php foreach ( $groups as $group ) : ?>
		<div class="yneko-reimu-upload-admin-section">
			<h3><?php echo esc_html( $group['title'] ); ?></h3>
			<?php if ( empty( $group['items'] ) ) : ?>
				<p class="description"><?php esc_html_e( '暂无可选...', 'yneko-reimu' ); ?></p>
				<?php continue; ?>
			<?php endif; ?>
			<div class="yneko-reimu-upload-admin-grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<?php
					$id     = absint( $item['id'] );
					$type   = 'gif' === $item['type'] ? 'gif' : 'image';
					$status = 'approved' === $item['status'] ? 'approved' : ( 'pending' === $item['status'] ? 'pending' : 'private' );
					$user   = $item['user'] ? get_user_by( 'id', $item['user'] ) : null;
					$label  = 'gif' === $type ? __( 'GIF', 'yneko-reimu' ) : __( '图片', 'yneko-reimu' );
					?>
					<div class="yneko-reimu-upload-admin-card">
						<img src="<?php echo esc_url( $item['url'] ); ?>" alt="">
						<div class="yneko-reimu-upload-admin-meta">
							<strong>
								<?php
								if ( 'gif' === $type ) {
									echo 'approved' === $status ? esc_html__( 'GIF 已入库', 'yneko-reimu' ) : esc_html__( 'GIF 待审核', 'yneko-reimu' );
								} else {
									esc_html_e( '评论图片', 'yneko-reimu' );
								}
								?>
							</strong>
							<span><?php echo esc_html( $label ); ?></span>
							<span><?php echo esc_html( $user ? $user->display_name : __( '未知用户', 'yneko-reimu' ) ); ?></span>
							<span><?php echo esc_html( $item['date'] ); ?></span>
						</div>
						<div class="yneko-reimu-upload-admin-actions">
							<?php if ( 'gif' === $type ) : ?>
								<?php if ( 'approved' !== $status ) : ?>
									<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_comment_upload_action' => 'approve', 'attachment_id' => $id ) ), 'yneko_reimu_comment_upload_approve_' . $id ) ); ?>"><?php esc_html_e( '批准入库', 'yneko-reimu' ); ?></a>
								<?php else : ?>
									<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_comment_upload_action' => 'remove', 'attachment_id' => $id ) ), 'yneko_reimu_comment_upload_remove_' . $id ) ); ?>"><?php esc_html_e( '移出表情库', 'yneko-reimu' ); ?></a>
								<?php endif; ?>
							<?php endif; ?>
							<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_comment_upload_action' => 'delete', 'attachment_id' => $id ) ), 'yneko_reimu_comment_upload_delete_' . $id ) ); ?>"><?php esc_html_e( '删除文件', 'yneko-reimu' ); ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
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
		'.yneko-reimu-settings-page{padding-bottom:96px}.yneko-reimu-settings-tabs{display:flex;flex-wrap:wrap;gap:0;margin-top:20px}.yneko-reimu-settings-tabs .nav-tab{display:inline-flex;align-items:center;min-height:40px;margin-left:0;margin-right:6px;padding:8px 15px;background:#f0f0f1;border-bottom:1px solid #c3c4c7;color:#1d2327;cursor:pointer}.yneko-reimu-settings-tabs .nav-tab-active{background:#fff;border-bottom-color:#fff;color:#2271b1}.yneko-reimu-settings-panel{max-width:1280px;padding-top:4px}.yneko-reimu-settings-panel[hidden]{display:none!important}.yneko-reimu-settings-panel h2:first-child{margin-top:24px}.yneko-reimu-floating-submit{position:fixed;z-index:20;right:20px;bottom:0;left:180px;display:flex;align-items:center;justify-content:flex-end;gap:16px;min-height:64px;padding:12px 24px;background:rgba(240,240,241,.94);border-top:1px solid #dcdcde;box-shadow:0 -8px 24px rgba(0,0,0,.08);backdrop-filter:saturate(140%) blur(8px)}.folded .yneko-reimu-floating-submit{left:56px}.yneko-reimu-floating-submit__hint{color:#646970}.yneko-reimu-settings-page h2{margin-top:32px}.yneko-reimu-admin-text{line-height:1.35}.description.yneko-reimu-admin-text,.yneko-reimu-admin-text.description,.yneko-reimu-settings-page p.yneko-reimu-admin-text{display:block;margin:6px 0 0;color:#646970}.yneko-reimu-settings-page .button .yneko-reimu-admin-text{vertical-align:middle}.yneko-reimu-submit-button .yneko-reimu-admin-text{color:#fff}.yneko-reimu-admin-gif-upload{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin:14px 0 18px;padding:12px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-admin-gif-upload label{display:flex;flex-direction:column;gap:6px;font-weight:600}.yneko-reimu-media-field,.yneko-reimu-inline-media{display:flex;gap:8px;align-items:center}.yneko-reimu-inline-media input{flex:1;min-width:0}.yneko-reimu-repeatable-row{margin:14px 0;padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-row-heading{display:flex;align-items:center;margin:-2px 0 14px}.yneko-reimu-row-number{display:inline-flex;align-items:center;min-height:24px;padding:3px 10px;border-radius:999px;background:#f6f7f7;color:#1d2327;font-weight:600}.yneko-reimu-row-grid{display:grid;gap:12px}.yneko-reimu-row-grid-friend{grid-template-columns:repeat(4,minmax(0,1fr))}.yneko-reimu-row-grid-music{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-row-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.yneko-reimu-row-grid input{width:100%}.yneko-reimu-row-actions{display:flex;gap:8px;margin-top:12px}.yneko-reimu-upload-admin-section{margin-top:22px}.yneko-reimu-upload-admin-section h3{margin:0 0 10px}.yneko-reimu-upload-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-top:14px}.yneko-reimu-upload-admin-card{padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-upload-admin-card img{display:block;width:100%;aspect-ratio:1;object-fit:cover;border-radius:6px;background:#f6f7f7}.yneko-reimu-upload-admin-meta{display:flex;flex-direction:column;gap:3px;margin:9px 0;color:#646970;font-size:12px}.yneko-reimu-upload-admin-meta strong{color:#1d2327}.yneko-reimu-upload-admin-actions{display:flex;flex-wrap:wrap;gap:6px}@media(max-width:960px){.yneko-reimu-row-grid-friend,.yneko-reimu-row-grid-music{grid-template-columns:1fr}.yneko-reimu-floating-submit{left:0;right:0;justify-content:space-between;padding:10px 14px}}@media(max-width:782px){.yneko-reimu-settings-tabs .nav-tab{flex:1 1 150px;margin-right:4px}.yneko-reimu-floating-submit{min-height:74px}.yneko-reimu-floating-submit__hint{display:none}}'
	);

	wp_register_script( 'yneko-reimu-admin-settings', false, array( 'jquery' ), YNEKO_REIMU_VERSION, true );
	wp_enqueue_script( 'yneko-reimu-admin-settings' );
	$admin_i18n = array(
		'locale'          => yneko_reimu_admin_prefers_zh() ? 'zh' : 'en',
		'mediaTitle'      => array( 'zh' => '选择媒体', 'en' => 'Select media' ),
		'useMedia'        => array( 'zh' => '使用此媒体', 'en' => 'Use this media' ),
		'choose'          => array( 'zh' => '选择', 'en' => 'Choose' ),
		'remove'          => array( 'zh' => '删除', 'en' => 'Remove' ),
		'deleteUpload'    => array( 'zh' => '确定删除这个评论上传文件吗？', 'en' => 'Delete this comment upload file?' ),
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
		'friendItem'      => array( 'zh' => '友链', 'en' => 'Friend' ),
		'musicItem'       => array( 'zh' => '曲目', 'en' => 'Track' ),
	);
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		'window.YNEKO_REIMU_ADMIN_I18N=' . wp_json_encode( $admin_i18n ) . ';' .
		"(function(){var labels=window.YNEKO_REIMU_ADMIN_I18N||{};var locale=labels.locale==='zh'?'zh':'en';var counters={friend:Date.now(),music:Date.now()+1000};function esc(value){return String(value||'').replace(/[&<>\"']/g,function(chr){return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',\"'\":'&#039;'}[chr];});}function plain(key,zh,en){var item=labels[key]||{};return item[locale]||(locale==='zh'?zh:en);}function labelText(key,zh,en){return '<span class=\"yneko-reimu-admin-text\">'+esc(plain(key,zh,en))+'</span>';}function fieldLabel(key,zh,en,control){return '<label>'+labelText(key,zh,en)+control+'</label>';}function rowHeading(type){return '<div class=\"yneko-reimu-row-heading\" data-row-label=\"'+type+'\"><span class=\"yneko-reimu-row-number\"></span></div>';}function rowTitle(type,index){var key=type==='music'?'musicItem':'friendItem';var fallbackZh=type==='music'?'曲目':'友链';var fallbackEn=type==='music'?'Track':'Friend';return '<span class=\"yneko-reimu-admin-text\">'+esc(plain(key,fallbackZh,fallbackEn))+' #'+index+'</span>';}function activateTab(name){var tabs=document.querySelectorAll('[data-yneko-settings-tab]');var panels=document.querySelectorAll('[data-yneko-settings-panel]');var exists=false;tabs.forEach(function(tab){if(tab.getAttribute('data-yneko-settings-tab')===name){exists=true;}});if(!exists){name='general';}tabs.forEach(function(tab){var active=tab.getAttribute('data-yneko-settings-tab')===name;tab.classList.toggle('nav-tab-active',active);tab.setAttribute('aria-selected',active?'true':'false');});panels.forEach(function(panel){var active=panel.getAttribute('data-yneko-settings-panel')===name;panel.hidden=!active;panel.classList.toggle('is-active',active);});try{window.localStorage.setItem('ynekoReimuSettingsTab',name);}catch(error){}if(window.location.hash!=='#'+name){try{history.replaceState(null,'','#'+name);}catch(error){}}}function initTabs(){var initial=(window.location.hash||'').replace(/^#/,'');if(!initial){try{initial=window.localStorage.getItem('ynekoReimuSettingsTab')||'';}catch(error){}}activateTab(initial||'general');document.querySelectorAll('[data-yneko-settings-tab]').forEach(function(tab){tab.addEventListener('click',function(event){event.preventDefault();activateTab(tab.getAttribute('data-yneko-settings-tab')||'general');});});window.addEventListener('hashchange',function(){activateTab((window.location.hash||'').replace(/^#/,''));});}function refreshNumbers(root){(root||document).querySelectorAll('.yneko-reimu-repeatable').forEach(function(section){var type=section.dataset.repeatable==='music'?'music':'friend';section.querySelectorAll('.yneko-reimu-repeatable-row').forEach(function(row,index){var heading=row.querySelector('.yneko-reimu-row-heading');if(!heading){heading=document.createElement('div');heading.className='yneko-reimu-row-heading';heading.setAttribute('data-row-label',type);heading.innerHTML='<span class=\"yneko-reimu-row-number\"></span>';row.insertBefore(heading,row.firstChild);}var number=heading.querySelector('.yneko-reimu-row-number');if(number){number.innerHTML=rowTitle(type,index+1);}});});}function media(button){var field=button.closest('.yneko-reimu-inline-media')||button.closest('.yneko-reimu-media-field');var input=field?field.querySelector('.yneko-reimu-media-url'):null;if(!input||!window.wp||!wp.media){return;}var frame=wp.media({title:plain('mediaTitle','选择媒体','Select media'),button:{text:plain('useMedia','使用此媒体','Use this media')},multiple:false});frame.on('select',function(){var attachment=frame.state().get('selection').first().toJSON();input.value=attachment.url||'';input.dispatchEvent(new Event('change',{bubbles:true}));});frame.open();}function pickButton(){return '<button type=\"button\" class=\"button yneko-reimu-media-button\">'+labelText('choose','选择','Choose')+'</button>';}function mediaInput(name){return '<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"'+name+'\">'+pickButton()+'</span>';}function friendTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\">'+rowHeading('friend')+'<div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-friend\">'+fieldLabel('name','名称','Name','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][name]\">')+fieldLabel('url','链接','URL','<input type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][url]\">')+fieldLabel('description','描述','Description','<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][desc]\">')+fieldLabel('avatar','头像','Avatar','<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][image]\">'+pickButton()+'</span>')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+labelText('remove','删除','Remove')+'</button></div></div>';}function musicTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\">'+rowHeading('music')+'<div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-music\">'+fieldLabel('trackTitle','歌名','Track title','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][name]\">')+fieldLabel('artist','作者','Artist','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][artist]\">')+fieldLabel('audio','音频','Audio',mediaInput('yneko_reimu_settings[music]['+i+'][url]'))+fieldLabel('cover','封面','Cover',mediaInput('yneko_reimu_settings[music]['+i+'][cover]'))+fieldLabel('lyrics','歌词 LRC','Lyrics LRC',mediaInput('yneko_reimu_settings[music]['+i+'][lrc]'))+fieldLabel('themeColor','主题色','Theme color','<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][theme]\" value=\"#ff5252\">')+'</div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">'+labelText('remove','删除','Remove')+'</button></div></div>';}document.addEventListener('click',function(event){var target=event.target;if(target.closest('[data-yneko-upload-delete]')&&!window.confirm(plain('deleteUpload','确定删除这个评论上传文件吗？','Delete this comment upload file?'))){event.preventDefault();return;}if(target.closest('.yneko-reimu-media-button')){event.preventDefault();media(target.closest('.yneko-reimu-media-button'));}if(target.closest('.yneko-reimu-remove-row')){event.preventDefault();var repeatable=target.closest('.yneko-reimu-repeatable');target.closest('.yneko-reimu-repeatable-row').remove();refreshNumbers(repeatable||document);}var add=target.closest('.yneko-reimu-add-row');if(add){event.preventDefault();var type=add.dataset.template;var repeatable=add.closest('.yneko-reimu-repeatable');var list=repeatable.querySelector('.yneko-reimu-repeatable-list');var i=counters[type]++;list.insertAdjacentHTML('beforeend',type==='friend'?friendTemplate(i):musicTemplate(i));refreshNumbers(repeatable);}});initTabs();refreshNumbers();}());"
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_enqueue_settings_admin_assets' );
