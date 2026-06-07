<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_general_panel( $context ) {
	$context              = is_array( $context ) ? $context : array();
	$settings             = isset( $context['settings'] ) && is_array( $context['settings'] ) ? $context['settings'] : yneko_reimu_settings();
	$builtin_pages        = isset( $context['builtin_pages'] ) && is_array( $context['builtin_pages'] ) ? $context['builtin_pages'] : yneko_reimu_settings_builtin_pages();
	$features             = isset( $context['features'] ) && is_array( $context['features'] ) ? $context['features'] : yneko_reimu_settings_features();
	$updates              = function_exists( 'yneko_reimu_settings_updates' ) ? yneko_reimu_settings_updates() : array();
	$admin_totp           = isset( $context['admin_totp'] ) && is_array( $context['admin_totp'] ) ? $context['admin_totp'] : yneko_reimu_admin_current_user_totp_payload();
	$admin_totp_available = ! empty( $context['admin_totp_available'] );
	?>
	<section class="yneko-reimu-settings-panel is-active" data-yneko-settings-panel="general">
		<h2><?php yneko_reimu_admin_bilingual_heading( '常规设置', 'General settings' ); ?></h2>
		<?php yneko_reimu_render_settings_general_customizer_group(); ?>
		<?php yneko_reimu_render_settings_general_admin_experience_group( $features ); ?>
		<?php yneko_reimu_render_settings_general_updates_group( $updates ); ?>
		<?php yneko_reimu_render_settings_admin_totp_group( $admin_totp, $admin_totp_available ); ?>
		<?php yneko_reimu_render_settings_general_builtin_pages_group( $builtin_pages ); ?>
		<?php yneko_reimu_render_settings_general_resource_group( $settings ); ?>
		<?php yneko_reimu_render_settings_general_links_group( $settings ); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_general_customizer_group() {
	?>
	<?php yneko_reimu_settings_group_open( '视觉预览工作台', 'Visual preview workspace', '这些项目适合边看边调，因此继续留在 WordPress 自定义器。', 'These options stay in the WordPress Customizer because they benefit from live preview.' ); ?>
		<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '打开 WordPress 自定义', 'Open WordPress Customizer' ); ?></a>
		<?php yneko_reimu_admin_bilingual_description( '站点图标、Logo、作者头像、横幅、封面、搜索背景、强调色、侧栏、导航、首页胶囊和页脚文字保留在自定义器中。', 'Site icon, logo, author avatar, banners, covers, search background, accent color, sidebar, navigation, home capsules, and footer text remain in the Customizer.' ); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_general_admin_experience_group( $features ) {
	?>
	<?php yneko_reimu_settings_group_open( '管理员体验', 'Administrator experience', '这里控制管理员登录浏览器访问前台时的后台辅助显示，不影响普通评论用户。', 'This controls administrator-only front-end helpers and does not affect regular comment users.' ); ?>
		<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][show_admin_toolbar]" value="1" <?php checked( '1', $features['show_admin_toolbar'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示前台管理员工具条', 'Show front-end admin toolbar' ); ?></label>
		<?php yneko_reimu_admin_bilingual_description( '默认关闭，前台保持干净并隐藏 Rank Math 等插件工具条提示。需要临时调试 Rank Math、Query Monitor 或编辑入口时再开启。', 'Disabled by default to keep the front end clean and hide plugin toolbar prompts such as Rank Math. Enable it temporarily for Rank Math, Query Monitor, or edit-link debugging.' ); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_general_updates_group( $updates ) {
	$updates = wp_parse_args(
		is_array( $updates ) ? $updates : array(),
		array(
			'github_release_check' => '1',
			'cache_minutes'        => 360,
		)
	);
	?>
	<?php yneko_reimu_settings_group_open( '主题更新', 'Theme updates', '检测 GitHub Release 中的正式主题包，并接入 WordPress 原生一键更新。', 'Check stable GitHub Releases and expose them through WordPress native one-click theme updates.' ); ?>
		<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[updates][github_release_check]" value="1" <?php checked( '1', $updates['github_release_check'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '自动检测 GitHub Release 更新', 'Check GitHub Release updates automatically' ); ?></label>
		<?php yneko_reimu_admin_bilingual_description( '只读取正式 Release，并只安装 Release 附件中的 Yneko-Reimu-vX.Y.Z.zip；不会使用 GitHub 自动源码包。', 'Only stable Releases are checked, and updates install the Yneko-Reimu-vX.Y.Z.zip release asset instead of GitHub source archives.' ); ?>
		<div class="yneko-reimu-field">
			<label class="yneko-reimu-field__label" for="yneko-reimu-update-cache-minutes"><?php yneko_reimu_admin_bilingual_label( '更新检测缓存时间（分钟）', 'Update check cache time (minutes)' ); ?></label>
			<input id="yneko-reimu-update-cache-minutes" class="small-text" type="number" min="5" max="4320" step="5" name="yneko_reimu_settings[updates][cache_minutes]" value="<?php echo esc_attr( absint( $updates['cache_minutes'] ?? 360 ) ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '默认 360 分钟。测试时可临时改成 5 分钟；正式站点建议保持 360 分钟或更长。', 'Default is 360 minutes. Use 5 minutes temporarily for testing; keep 360 minutes or longer on production sites.' ); ?>
		</div>
		<?php yneko_reimu_render_settings_general_updates_status(); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_general_updates_status() {
	$enabled = yneko_reimu_theme_update_check_enabled();
	$status  = function_exists( 'yneko_reimu_theme_updater_get_cached_status' ) ? yneko_reimu_theme_updater_get_cached_status() : false;
	$current = yneko_reimu_theme_updater_normalize_version( YNEKO_REIMU_VERSION );
	$latest  = is_array( $status ) ? (string) ( $status['version'] ?? '' ) : '';
	$asset   = is_array( $status ) ? (string) ( $status['asset_name'] ?? '' ) : '';
	$package = is_array( $status ) ? (string) ( $status['package'] ?? '' ) : '';
	$conclusion = yneko_reimu_theme_update_status_conclusion( $enabled, $status, $current );
	?>
	<div class="yneko-reimu-update-status" data-yneko-theme-update-status>
		<div class="yneko-reimu-update-status__header">
			<strong><?php yneko_reimu_admin_bilingual_label( '更新检测状态', 'Update check status' ); ?></strong>
			<span class="yneko-reimu-update-status__pill is-<?php echo esc_attr( $conclusion['tone'] ); ?>"><?php echo esc_html( yneko_reimu_theme_update_status_text( $conclusion['zh'], $conclusion['en'] ) ); ?></span>
		</div>
		<dl class="yneko-reimu-update-status__grid">
			<?php yneko_reimu_theme_update_status_row( '当前安装版本', 'Installed version', $current ? $current : '-' ); ?>
			<?php yneko_reimu_theme_update_status_row( 'GitHub 最新正式版本', 'Latest stable GitHub version', $latest ? $latest : '-' ); ?>
			<?php yneko_reimu_theme_update_status_row( '目标附件名', 'Expected asset', $asset ? $asset : ( $latest ? yneko_reimu_theme_updater_expected_asset_name( $latest ) : '-' ) ); ?>
			<?php yneko_reimu_theme_update_status_row( '附件状态', 'Asset status', $package ? yneko_reimu_theme_update_status_text( '已找到', 'Found' ) : yneko_reimu_theme_update_status_text( '未找到', 'Not found' ) ); ?>
			<?php yneko_reimu_theme_update_status_row( '上次检测时间', 'Last checked', yneko_reimu_theme_update_format_time( is_array( $status ) ? absint( $status['checked_at'] ?? 0 ) : 0 ) ); ?>
			<?php yneko_reimu_theme_update_status_row( '缓存过期时间', 'Cache expires', yneko_reimu_theme_update_format_time( is_array( $status ) ? absint( $status['expires_at'] ?? 0 ) : 0 ) ); ?>
			<?php yneko_reimu_theme_update_status_row( '缓存时长', 'Cache window', sprintf( '%d min', yneko_reimu_theme_update_cache_minutes() ) ); ?>
			<?php yneko_reimu_theme_update_status_row( '失败原因', 'Failure reason', yneko_reimu_theme_update_status_error_text( $status ) ); ?>
		</dl>
		<div class="yneko-reimu-update-status__actions">
			<a class="button button-secondary" href="<?php echo esc_url( yneko_reimu_theme_updater_admin_action_url( 'force_check' ) ); ?>" data-yneko-theme-update-force><?php yneko_reimu_admin_bilingual_label( '立即重新检测', 'Check now' ); ?></a>
			<a class="button" href="<?php echo esc_url( yneko_reimu_theme_updater_admin_action_url( 'clear_cache' ) ); ?>" data-yneko-theme-update-clear><?php yneko_reimu_admin_bilingual_label( '清除缓存', 'Clear cache' ); ?></a>
			<a class="button-link" href="<?php echo esc_url( admin_url( 'update-core.php?force-check=1' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '打开 WordPress 更新页', 'Open WordPress Updates' ); ?></a>
		</div>
		<?php yneko_reimu_admin_bilingual_description( '普通加载只读取缓存；点击立即重新检测才会同步请求 GitHub。WordPress 仍会在「仪表盘 -> 更新」和「外观 -> 主题」中安装更新。', 'Normal page loads read the cache only; Check now performs a synchronous GitHub request. WordPress still installs updates through Dashboard -> Updates and Appearance -> Themes.' ); ?>
	</div>
	<?php
}

function yneko_reimu_theme_update_status_text( $zh, $en ) {
	return yneko_reimu_admin_prefers_zh() ? (string) $zh : (string) $en;
}

function yneko_reimu_theme_update_status_conclusion( $enabled, $status, $current ) {
	if ( ! $enabled ) {
		return array( 'zh' => '未启用检测', 'en' => 'Disabled', 'tone' => 'muted' );
	}

	if ( ! is_array( $status ) ) {
		return array( 'zh' => '尚未检测', 'en' => 'Not checked', 'tone' => 'muted' );
	}

	if ( empty( $status['ok'] ) ) {
		return array( 'zh' => '检测失败', 'en' => 'Check failed', 'tone' => 'error' );
	}

	if ( ! empty( $status['version'] ) && version_compare( $status['version'], $current, '>' ) ) {
		return array( 'zh' => '有可用更新', 'en' => 'Update available', 'tone' => 'success' );
	}

	return array( 'zh' => '当前已是最新', 'en' => 'Up to date', 'tone' => 'ok' );
}

function yneko_reimu_theme_update_status_row( $label_zh, $label_en, $value ) {
	?>
	<div class="yneko-reimu-update-status__row">
		<dt><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></dt>
		<dd><?php echo esc_html( $value ); ?></dd>
	</div>
	<?php
}

function yneko_reimu_theme_update_format_time( $timestamp ) {
	if ( ! $timestamp ) {
		return '-';
	}

	return wp_date( 'Y-m-d H:i:s T', $timestamp );
}

function yneko_reimu_theme_update_status_error_text( $status ) {
	if ( ! is_array( $status ) ) {
		return yneko_reimu_theme_update_status_text( '暂无检测记录。', 'No check has been recorded yet.' );
	}

	if ( ! empty( $status['ok'] ) ) {
		return '-';
	}

	$code    = (string) ( $status['error_code'] ?? '' );
	$message = (string) ( $status['message'] ?? '' );
	if ( $code && $message ) {
		return $code . ': ' . $message;
	}

	return $message ? $message : yneko_reimu_theme_update_status_text( '未知错误。', 'Unknown error.' );
}

function yneko_reimu_render_settings_general_builtin_pages_group( $builtin_pages ) {
	?>
	<?php yneko_reimu_settings_group_open( '内置页面', 'Built-in pages', '控制主题内置虚拟页面是否可访问，并同步影响主题默认导航。', 'Control whether built-in virtual pages are available and whether default theme navigation includes them.' ); ?>
		<input type="hidden" name="yneko_reimu_settings[builtin_pages][_present]" value="1">
		<?php foreach ( yneko_reimu_settings_builtin_page_fields() as $field ) : ?>
			<?php yneko_reimu_render_settings_builtin_page_toggle( $builtin_pages, $field ); ?>
		<?php endforeach; ?>
		<?php yneko_reimu_admin_bilingual_description( '关闭后会从主题默认导航和菜单中的对应内置链接移除，并让对应内置路径返回 404；不会影响 WordPress 原生分类、标签、日期等归档页。', 'Disabled pages are removed from the theme default navigation and matching built-in menu links, and their built-in paths return 404. Native WordPress category, tag, date, and other archives are not affected.' ); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_settings_builtin_page_fields() {
	return array(
		array( 'projects', '项目页', 'Projects page' ),
		array( 'archives', '归档页', 'Archives page' ),
		array( 'about', '关于页', 'About page' ),
		array( 'friend', '友链页', 'Friends page' ),
	);
}

function yneko_reimu_render_settings_builtin_page_toggle( $builtin_pages, $field ) {
	?>
	<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][<?php echo esc_attr( $field[0] ); ?>]" value="1" <?php checked( '1', $builtin_pages[ $field[0] ] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( $field[1], $field[2] ); ?></label>
	<?php
}

function yneko_reimu_render_settings_general_resource_group( $settings ) {
	?>
	<?php yneko_reimu_settings_group_open( '站点资源兜底', 'Site resource fallbacks', '用于补齐浏览器、移动端或聊天软件不稳定支持 SVG 的场景。', 'Use these fallbacks for browsers, mobile devices, or chat previews that do not reliably support SVG assets.' ); ?>
		<div class="yneko-reimu-field">
			<label class="yneko-reimu-field__label"><?php yneko_reimu_admin_bilingual_label( 'Favicon / Apple Touch 兜底图', 'Favicon / Apple Touch fallback' ); ?></label>
			<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[favicon_fallback_url]', $settings['favicon_fallback_url'], yneko_reimu_admin_bilingual_text( '选择 PNG/JPG', 'Choose PNG/JPG' ), 'image/png,image/jpeg' ); ?>
			<?php yneko_reimu_admin_bilingual_description( '建议设置一张 512×512 PNG/JPG；此项不会影响 Rank Math 的 og:image。', 'A square 512x512 PNG/JPG is recommended. This does not affect Rank Math og:image.' ); ?>
		</div>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_general_links_group( $settings ) {
	?>
	<?php yneko_reimu_settings_group_open( '站点展示链接', 'Site display links', '用于主题内置入口、项目页来源和底部赞助入口。', 'Used by bundled theme links, project-page source data, and the footer sponsor entry.' ); ?>
		<div class="yneko-reimu-field">
			<label class="yneko-reimu-field__label" for="yneko-reimu-github-url"><?php yneko_reimu_admin_bilingual_label( 'GitHub 主页链接', 'GitHub profile URL' ); ?></label>
			<input id="yneko-reimu-github-url" class="regular-text" type="url" name="yneko_reimu_settings[github_url]" value="<?php echo esc_attr( $settings['github_url'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '统一用于顶部 GitHub 三角标、侧栏 GitHub 链接和项目页拉取来源。', 'Used by the GitHub corner ribbon, sidebar GitHub link, and project-page repository source.' ); ?>
		</div>
		<div class="yneko-reimu-field">
			<label class="yneko-reimu-field__label"><?php yneko_reimu_admin_bilingual_label( '赞助二维码', 'Sponsor QR code' ); ?></label>
			<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[sponsor_qr_url]', $settings['sponsor_qr_url'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ) ); ?>
			<?php yneko_reimu_admin_bilingual_description( '用于底部赞助入口。留空时不会显示赞助二维码。', 'Used by the footer sponsor entry. If empty, the sponsor QR code is hidden.' ); ?>
		</div>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_admin_totp_group( $admin_totp, $admin_totp_available ) {
	?>
	<?php yneko_reimu_settings_group_open( '账号安全', 'Account security', '这里管理当前管理员账号的认证器二次认证，和前台个人资料弹窗使用同一套 TOTP 数据。', 'Manage authenticator-app two-factor authentication for the current administrator account. It uses the same TOTP data as the front-end profile modal.' ); ?>
		<div class="yneko-reimu-admin-totp" data-yneko-admin-totp data-nonce="<?php echo esc_attr( $admin_totp['nonce'] ); ?>" data-enabled="<?php echo $admin_totp['enabled'] ? '1' : '0'; ?>" data-qrcode-src="<?php echo esc_url( YNEKO_REIMU_URI . '/assets/dist/qrcode.js' ); ?>">
			<?php yneko_reimu_render_settings_admin_totp_status( $admin_totp ); ?>
			<?php yneko_reimu_render_settings_admin_totp_body( $admin_totp, $admin_totp_available ); ?>
		</div>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_admin_totp_body( $admin_totp, $admin_totp_available ) {
	if ( ! $admin_totp_available ) {
		yneko_reimu_admin_bilingual_description( '二次认证模块尚未加载，无法在后台管理。', 'The two-factor module is not loaded yet, so it cannot be managed in the admin page.' );
		return;
	}

	yneko_reimu_render_settings_admin_totp_setup();
	yneko_reimu_render_settings_admin_totp_actions( $admin_totp );
	yneko_reimu_render_settings_admin_totp_recovery( $admin_totp );
	?>
	<p class="yneko-reimu-admin-totp-message" data-yneko-admin-totp-message></p>
	<?php
	yneko_reimu_admin_bilingual_description( '启用后，当前账号从前台评论登录入口和后台 wp-login.php 登录时都需要输入认证器验证码；如果认证器不可用，可使用一个未用过的一次性恢复码登录。', 'After enabling it, this account must enter an authenticator code when logging in through the front-end comment login and backend wp-login.php. If the authenticator is unavailable, use an unused one-time recovery code.' );
}

function yneko_reimu_render_settings_admin_totp_status( $admin_totp ) {
	?>
	<span class="yneko-reimu-admin-totp-status<?php echo $admin_totp['enabled'] ? ' is-enabled' : ''; ?>" data-yneko-admin-totp-status><?php echo wp_kses_post( $admin_totp['enabled'] ? yneko_reimu_admin_bilingual_text( '已开启', 'Enabled' ) : yneko_reimu_admin_bilingual_text( '未开启', 'Disabled' ) ); ?></span>
	<?php
}

function yneko_reimu_render_settings_admin_totp_setup() {
	?>
	<div class="yneko-reimu-admin-totp-setup" data-yneko-admin-totp-setup hidden>
		<img class="yneko-reimu-admin-totp-qr" data-yneko-admin-totp-qr alt="">
		<div>
			<div class="yneko-reimu-admin-totp-secret" data-yneko-admin-totp-secret></div>
			<p class="description"><?php yneko_reimu_admin_bilingual_label( '用认证器 App 扫码，或手动输入密钥。', 'Scan with an authenticator app, or enter the secret manually.' ); ?></p>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_settings_admin_totp_actions( $admin_totp ) {
	?>
	<div class="yneko-reimu-admin-totp-actions">
		<button type="button" class="button" data-yneko-admin-totp-generate><?php yneko_reimu_admin_bilingual_label( '生成认证器密钥', 'Generate authenticator secret' ); ?></button>
		<input class="small-text" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" placeholder="123456" data-yneko-admin-totp-code>
		<button type="button" class="button<?php echo $admin_totp['enabled'] ? '' : ' button-primary'; ?>" data-yneko-admin-totp-toggle><?php echo wp_kses_post( $admin_totp['enabled'] ? yneko_reimu_admin_bilingual_text( '关闭二次认证', 'Disable two-factor authentication' ) : yneko_reimu_admin_bilingual_text( '启用二次认证', 'Enable two-factor authentication' ) ); ?></button>
	</div>
	<?php
}

function yneko_reimu_render_settings_admin_totp_recovery( $admin_totp ) {
	?>
	<div class="yneko-reimu-admin-totp-recovery" data-yneko-admin-totp-recovery<?php echo $admin_totp['enabled'] ? '' : ' hidden'; ?>>
		<div class="yneko-reimu-admin-totp-recovery__header">
			<strong><?php yneko_reimu_admin_bilingual_label( '一次性恢复码', 'One-time recovery codes' ); ?></strong>
			<span class="description" data-yneko-admin-totp-recovery-count><?php echo esc_html( sprintf( yneko_reimu_admin_prefers_zh() ? '剩余 %d 个' : '%d remaining', absint( $admin_totp['recoveryCount'] ?? 0 ) ) ); ?></span>
		</div>
		<p class="description"><?php yneko_reimu_admin_bilingual_label( '恢复码只在生成时显示明文，每个码只能使用一次。请离线保存，不要截图公开分享。', 'Recovery codes are shown in plain text only when generated. Each code can be used once. Save them offline and do not share screenshots publicly.' ); ?></p>
		<pre class="yneko-reimu-admin-totp-recovery__codes" data-yneko-admin-totp-recovery-codes hidden></pre>
		<div class="yneko-reimu-admin-totp-actions">
			<button type="button" class="button" data-yneko-admin-totp-recovery-generate><?php yneko_reimu_admin_bilingual_label( '重新生成恢复码', 'Regenerate recovery codes' ); ?></button>
			<button type="button" class="button" data-yneko-admin-totp-recovery-copy hidden><?php yneko_reimu_admin_bilingual_label( '复制恢复码', 'Copy recovery codes' ); ?></button>
		</div>
	</div>
	<?php
}
