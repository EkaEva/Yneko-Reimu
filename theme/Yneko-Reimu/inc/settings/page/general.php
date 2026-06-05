<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_general_panel( $context ) {
	$context              = is_array( $context ) ? $context : array();
	$settings             = isset( $context['settings'] ) && is_array( $context['settings'] ) ? $context['settings'] : yneko_reimu_settings();
	$builtin_pages        = isset( $context['builtin_pages'] ) && is_array( $context['builtin_pages'] ) ? $context['builtin_pages'] : yneko_reimu_settings_builtin_pages();
	$features             = isset( $context['features'] ) && is_array( $context['features'] ) ? $context['features'] : yneko_reimu_settings_features();
	$admin_totp           = isset( $context['admin_totp'] ) && is_array( $context['admin_totp'] ) ? $context['admin_totp'] : yneko_reimu_admin_current_user_totp_payload();
	$admin_totp_available = ! empty( $context['admin_totp_available'] );
	?>
	<section class="yneko-reimu-settings-panel is-active" data-yneko-settings-panel="general">
		<h2><?php yneko_reimu_admin_bilingual_heading( '常规设置', 'General settings' ); ?></h2>
		<?php yneko_reimu_settings_group_open( '视觉预览工作台', 'Visual preview workspace', '这些项目适合边看边调，因此继续留在 WordPress 自定义器。', 'These options stay in the WordPress Customizer because they benefit from live preview.' ); ?>
			<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '打开 WordPress 自定义', 'Open WordPress Customizer' ); ?></a>
			<?php yneko_reimu_admin_bilingual_description( '站点图标、Logo、作者头像、横幅、封面、搜索背景、强调色、侧栏、导航、首页胶囊和页脚文字保留在自定义器中。', 'Site icon, logo, author avatar, banners, covers, search background, accent color, sidebar, navigation, home capsules, and footer text remain in the Customizer.' ); ?>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '管理员体验', 'Administrator experience', '这里控制管理员登录浏览器访问前台时的后台辅助显示，不影响普通评论用户。', 'This controls administrator-only front-end helpers and does not affect regular comment users.' ); ?>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][show_admin_toolbar]" value="1" <?php checked( '1', $features['show_admin_toolbar'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示前台管理员工具条', 'Show front-end admin toolbar' ); ?></label>
			<?php yneko_reimu_admin_bilingual_description( '默认关闭，前台保持干净并隐藏 Rank Math 等插件工具条提示。需要临时调试 Rank Math、Query Monitor 或编辑入口时再开启。', 'Disabled by default to keep the front end clean and hide plugin toolbar prompts such as Rank Math. Enable it temporarily for Rank Math, Query Monitor, or edit-link debugging.' ); ?>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '账号安全', 'Account security', '这里管理当前管理员账号的认证器二次认证，和前台个人资料弹窗使用同一套 TOTP 数据。', 'Manage authenticator-app two-factor authentication for the current administrator account. It uses the same TOTP data as the front-end profile modal.' ); ?>
			<div class="yneko-reimu-admin-totp" data-yneko-admin-totp data-nonce="<?php echo esc_attr( $admin_totp['nonce'] ); ?>" data-enabled="<?php echo $admin_totp['enabled'] ? '1' : '0'; ?>" data-qrcode-src="<?php echo esc_url( YNEKO_REIMU_URI . '/assets/dist/qrcode.js' ); ?>">
				<span class="yneko-reimu-admin-totp-status<?php echo $admin_totp['enabled'] ? ' is-enabled' : ''; ?>" data-yneko-admin-totp-status><?php echo wp_kses_post( $admin_totp['enabled'] ? yneko_reimu_admin_bilingual_text( '已开启', 'Enabled' ) : yneko_reimu_admin_bilingual_text( '未开启', 'Disabled' ) ); ?></span>
				<?php if ( $admin_totp_available ) : ?>
					<div class="yneko-reimu-admin-totp-setup" data-yneko-admin-totp-setup hidden>
						<img class="yneko-reimu-admin-totp-qr" data-yneko-admin-totp-qr alt="">
						<div>
							<div class="yneko-reimu-admin-totp-secret" data-yneko-admin-totp-secret></div>
							<p class="description"><?php yneko_reimu_admin_bilingual_label( '用认证器 App 扫码，或手动输入密钥。', 'Scan with an authenticator app, or enter the secret manually.' ); ?></p>
						</div>
					</div>
					<div class="yneko-reimu-admin-totp-actions">
						<button type="button" class="button" data-yneko-admin-totp-generate><?php yneko_reimu_admin_bilingual_label( '生成认证器密钥', 'Generate authenticator secret' ); ?></button>
						<input class="small-text" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" placeholder="123456" data-yneko-admin-totp-code>
						<button type="button" class="button<?php echo $admin_totp['enabled'] ? '' : ' button-primary'; ?>" data-yneko-admin-totp-toggle><?php echo wp_kses_post( $admin_totp['enabled'] ? yneko_reimu_admin_bilingual_text( '关闭二次认证', 'Disable two-factor authentication' ) : yneko_reimu_admin_bilingual_text( '启用二次认证', 'Enable two-factor authentication' ) ); ?></button>
					</div>
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
					<p class="yneko-reimu-admin-totp-message" data-yneko-admin-totp-message></p>
					<?php yneko_reimu_admin_bilingual_description( '启用后，当前账号从前台评论登录入口和后台 wp-login.php 登录时都需要输入认证器验证码；如果认证器不可用，可使用一个未用过的一次性恢复码登录。', 'After enabling it, this account must enter an authenticator code when logging in through the front-end comment login and backend wp-login.php. If the authenticator is unavailable, use an unused one-time recovery code.' ); ?>
				<?php else : ?>
					<?php yneko_reimu_admin_bilingual_description( '二次认证模块尚未加载，无法在后台管理。', 'The two-factor module is not loaded yet, so it cannot be managed in the admin page.' ); ?>
				<?php endif; ?>
			</div>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '内置页面', 'Built-in pages', '控制主题内置虚拟页面是否可访问，并同步影响主题默认导航。', 'Control whether built-in virtual pages are available and whether default theme navigation includes them.' ); ?>
			<input type="hidden" name="yneko_reimu_settings[builtin_pages][_present]" value="1">
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][projects]" value="1" <?php checked( '1', $builtin_pages['projects'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '项目页', 'Projects page' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][archives]" value="1" <?php checked( '1', $builtin_pages['archives'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '归档页', 'Archives page' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][about]" value="1" <?php checked( '1', $builtin_pages['about'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '关于页', 'About page' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][friend]" value="1" <?php checked( '1', $builtin_pages['friend'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '友链页', 'Friends page' ); ?></label>
			<?php yneko_reimu_admin_bilingual_description( '关闭后会从主题默认导航和菜单中的对应内置链接移除，并让对应内置路径返回 404；不会影响 WordPress 原生分类、标签、日期等归档页。', 'Disabled pages are removed from the theme default navigation and matching built-in menu links, and their built-in paths return 404. Native WordPress category, tag, date, and other archives are not affected.' ); ?>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '站点资源兜底', 'Site resource fallbacks', '用于补齐浏览器、移动端或聊天软件不稳定支持 SVG 的场景。', 'Use these fallbacks for browsers, mobile devices, or chat previews that do not reliably support SVG assets.' ); ?>
			<div class="yneko-reimu-field">
				<label class="yneko-reimu-field__label"><?php yneko_reimu_admin_bilingual_label( 'Favicon / Apple Touch 兜底图', 'Favicon / Apple Touch fallback' ); ?></label>
				<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[favicon_fallback_url]', $settings['favicon_fallback_url'], yneko_reimu_admin_bilingual_text( '选择 PNG/JPG', 'Choose PNG/JPG' ), 'image/png,image/jpeg' ); ?>
				<?php yneko_reimu_admin_bilingual_description( '建议设置一张 512×512 PNG/JPG；此项不会影响 Rank Math 的 og:image。', 'A square 512x512 PNG/JPG is recommended. This does not affect Rank Math og:image.' ); ?>
			</div>
		<?php yneko_reimu_settings_group_close(); ?>

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
	</section>
	<?php
}
