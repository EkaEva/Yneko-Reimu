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
		<button type="button" class="button yneko-reimu-media-button"><?php echo esc_html( $label ); ?></button>
	</div>
	<?php
}

function yneko_reimu_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = yneko_reimu_settings();
	$oauth    = yneko_reimu_settings_github_oauth();
	$callback = function_exists( 'yneko_reimu_github_login_callback_url' ) ? yneko_reimu_github_login_callback_url() : add_query_arg( 'action', 'yneko_reimu_github_callback', wp_login_url() );
	?>
	<div class="wrap yneko-reimu-settings-page">
		<h1><?php esc_html_e( 'Yneko-Reimu 设置', 'yneko-reimu' ); ?></h1>
		<p><?php esc_html_e( '这些内容保存在 WordPress 数据库中，不会写入主题源码或主题包。', 'yneko-reimu' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'yneko_reimu_settings' ); ?>
			<h2><?php esc_html_e( '站点资料', 'yneko-reimu' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( '站点头像', 'yneko-reimu' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[site_avatar_url]', $settings['site_avatar_url'], __( '选择图片', 'yneko-reimu' ) ); ?>
						<p class="description"><?php esc_html_e( '用于站点图标、默认 logo、分享图标兜底等站点级图片。', 'yneko-reimu' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( '作者头像', 'yneko-reimu' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[author_avatar_url]', $settings['author_avatar_url'], __( '选择图片', 'yneko-reimu' ) ); ?>
						<p class="description"><?php esc_html_e( '用于前台侧栏作者卡、页面角色图、友链/项目缺省图；不覆盖 WordPress 用户资料头像。', 'yneko-reimu' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( '游客评论头像', 'yneko-reimu' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[comment_avatar_url]', $settings['comment_avatar_url'], __( '选择图片', 'yneko-reimu' ) ); ?>
						<p class="description"><?php esc_html_e( '用于未登录访客评论的默认头像。留空时使用 One User Avatar 的全站默认头像，再留空则使用作者头像。', 'yneko-reimu' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-github-url"><?php esc_html_e( 'GitHub 主页链接', 'yneko-reimu' ); ?></label></th>
					<td>
						<input id="yneko-reimu-github-url" class="regular-text" type="url" name="yneko_reimu_settings[github_url]" value="<?php echo esc_attr( $settings['github_url'] ); ?>">
						<p class="description"><?php esc_html_e( '统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。', 'yneko-reimu' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( '赞助二维码', 'yneko-reimu' ); ?></th>
					<td><?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[sponsor_qr_url]', $settings['sponsor_qr_url'], __( '选择图片', 'yneko-reimu' ) ); ?></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'GitHub 登录', 'yneko-reimu' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Callback URL', 'yneko-reimu' ); ?></th>
					<td>
						<input id="yneko-reimu-callback-url" class="regular-text" type="url" name="yneko_reimu_settings[github_oauth][callback_url]" value="<?php echo esc_attr( $oauth['callback_url'] ); ?>" placeholder="<?php echo esc_attr( $callback ); ?>">
						<p class="description">
							<?php esc_html_e( '留空时自动使用下方默认地址；如果站点经过反向代理、固定域名或特殊登录路径，可在这里覆盖。GitHub OAuth App 中的 Authorization callback URL 需要与最终地址完全一致。', 'yneko-reimu' ); ?>
						</p>
						<p class="description"><code><?php echo esc_html( $callback ); ?></code></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-id"><?php esc_html_e( 'Client ID', 'yneko-reimu' ); ?></label></th>
					<td><input id="yneko-reimu-client-id" class="regular-text" type="text" name="yneko_reimu_settings[github_oauth][client_id]" value="<?php echo esc_attr( $oauth['client_id'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="yneko-reimu-client-secret"><?php esc_html_e( 'Client Secret', 'yneko-reimu' ); ?></label></th>
					<td><input id="yneko-reimu-client-secret" class="regular-text" type="password" autocomplete="off" name="yneko_reimu_settings[github_oauth][client_secret]" value="<?php echo esc_attr( $oauth['client_secret'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( '自动创建用户', 'yneko-reimu' ); ?></th>
					<td><label><input type="checkbox" name="yneko_reimu_settings[github_oauth][auto_create]" value="1" <?php checked( '1', $oauth['auto_create'] ); ?>> <?php esc_html_e( '允许 GitHub 登录自动创建 WordPress 用户', 'yneko-reimu' ); ?></label></td>
				</tr>
			</table>

			<h2><?php esc_html_e( '友链列表', 'yneko-reimu' ); ?></h2>
			<div class="yneko-reimu-repeatable" data-repeatable="friends">
				<div class="yneko-reimu-repeatable-list">
					<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
						<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php esc_html_e( '新增友链', 'yneko-reimu' ); ?></button>
			</div>

			<h2><?php esc_html_e( '音乐列表', 'yneko-reimu' ); ?></h2>
			<div class="yneko-reimu-repeatable" data-repeatable="music">
				<div class="yneko-reimu-repeatable-list">
					<?php foreach ( yneko_reimu_sanitize_music_items( $settings['music'] ) as $index => $track ) : ?>
						<?php yneko_reimu_render_music_row( $index, $track ); ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button yneko-reimu-add-row" data-template="music"><?php esc_html_e( '新增曲目', 'yneko-reimu' ); ?></button>
			</div>

			<?php submit_button(); ?>
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
			<label><?php esc_html_e( '名称', 'yneko-reimu' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $friend['name'] ); ?>"></label>
			<label><?php esc_html_e( '链接', 'yneko-reimu' ); ?><input type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $friend['url'] ); ?>"></label>
			<label><?php esc_html_e( '描述', 'yneko-reimu' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][desc]" value="<?php echo esc_attr( $friend['desc'] ); ?>"></label>
			<label><?php esc_html_e( '头像', 'yneko-reimu' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][image]" value="<?php echo esc_attr( $friend['image'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php esc_html_e( '选择', 'yneko-reimu' ); ?></button></span></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php esc_html_e( '删除', 'yneko-reimu' ); ?></button>
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
			<label><?php esc_html_e( '歌名', 'yneko-reimu' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $track['name'] ); ?>"></label>
			<label><?php esc_html_e( '作者', 'yneko-reimu' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][artist]" value="<?php echo esc_attr( $track['artist'] ); ?>"></label>
			<label><?php esc_html_e( '音频', 'yneko-reimu' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $track['url'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php esc_html_e( '选择', 'yneko-reimu' ); ?></button></span></label>
			<label><?php esc_html_e( '封面', 'yneko-reimu' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][cover]" value="<?php echo esc_attr( $track['cover'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php esc_html_e( '选择', 'yneko-reimu' ); ?></button></span></label>
			<label><?php esc_html_e( '歌词 LRC', 'yneko-reimu' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][lrc]" value="<?php echo esc_attr( $track['lrc'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php esc_html_e( '选择', 'yneko-reimu' ); ?></button></span></label>
			<label><?php esc_html_e( '主题色', 'yneko-reimu' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][theme]" value="<?php echo esc_attr( $track['theme'] ); ?>"></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php esc_html_e( '删除', 'yneko-reimu' ); ?></button>
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
		'.yneko-reimu-settings-page h2{margin-top:32px}.yneko-reimu-media-field,.yneko-reimu-inline-media{display:flex;gap:8px;align-items:center}.yneko-reimu-inline-media input{flex:1;min-width:0}.yneko-reimu-repeatable-row{margin:14px 0;padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-row-grid{display:grid;gap:12px}.yneko-reimu-row-grid-friend{grid-template-columns:repeat(4,minmax(0,1fr))}.yneko-reimu-row-grid-music{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-row-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.yneko-reimu-row-grid input{width:100%}.yneko-reimu-row-actions{display:flex;gap:8px;margin-top:12px}@media(max-width:960px){.yneko-reimu-row-grid-friend,.yneko-reimu-row-grid-music{grid-template-columns:1fr}}'
	);

	wp_register_script( 'yneko-reimu-admin-settings', false, array( 'jquery' ), YNEKO_REIMU_VERSION, true );
	wp_enqueue_script( 'yneko-reimu-admin-settings' );
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		"(function(){var counters={friend:Date.now(),music:Date.now()+1000};function media(button){var field=button.closest('.yneko-reimu-inline-media')||button.closest('.yneko-reimu-media-field');var input=field?field.querySelector('.yneko-reimu-media-url'):null;if(!input||!window.wp||!wp.media){return;}var frame=wp.media({title:'选择媒体',button:{text:'使用此媒体'},multiple:false});frame.on('select',function(){var attachment=frame.state().get('selection').first().toJSON();input.value=attachment.url||'';input.dispatchEvent(new Event('change',{bubbles:true}));});frame.open();}function pickButton(){return '<button type=\"button\" class=\"button yneko-reimu-media-button\">选择</button>';}function friendTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\"><div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-friend\"><label>名称<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][name]\"></label><label>链接<input type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][url]\"></label><label>描述<input type=\"text\" name=\"yneko_reimu_settings[friends]['+i+'][desc]\"></label><label>头像<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"yneko_reimu_settings[friends]['+i+'][image]\">'+pickButton()+'</span></label></div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">删除</button></div></div>';}function mediaInput(name){return '<span class=\"yneko-reimu-inline-media\"><input class=\"yneko-reimu-media-url\" type=\"url\" name=\"'+name+'\">'+pickButton()+'</span>';}function musicTemplate(i){return '<div class=\"yneko-reimu-repeatable-row\"><div class=\"yneko-reimu-row-grid yneko-reimu-row-grid-music\"><label>歌名<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][name]\"></label><label>作者<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][artist]\"></label><label>音频'+mediaInput('yneko_reimu_settings[music]['+i+'][url]')+'</label><label>封面'+mediaInput('yneko_reimu_settings[music]['+i+'][cover]')+'</label><label>歌词 LRC'+mediaInput('yneko_reimu_settings[music]['+i+'][lrc]')+'</label><label>主题色<input type=\"text\" name=\"yneko_reimu_settings[music]['+i+'][theme]\" value=\"#ff5252\"></label></div><div class=\"yneko-reimu-row-actions\"><button type=\"button\" class=\"button yneko-reimu-remove-row\">删除</button></div></div>';}document.addEventListener('click',function(event){var target=event.target;if(target.closest('.yneko-reimu-media-button')){event.preventDefault();media(target.closest('.yneko-reimu-media-button'));}if(target.closest('.yneko-reimu-remove-row')){event.preventDefault();target.closest('.yneko-reimu-repeatable-row').remove();}var add=target.closest('.yneko-reimu-add-row');if(add){event.preventDefault();var type=add.dataset.template;var list=add.closest('.yneko-reimu-repeatable').querySelector('.yneko-reimu-repeatable-list');var i=counters[type]++;list.insertAdjacentHTML('beforeend',type==='friend'?friendTemplate(i):musicTemplate(i));}});}());"
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_enqueue_settings_admin_assets' );
