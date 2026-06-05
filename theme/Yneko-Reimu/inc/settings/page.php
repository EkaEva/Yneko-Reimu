<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = yneko_reimu_settings();
	$oauth    = yneko_reimu_settings_github_oauth();
	$builtin_pages = yneko_reimu_settings_builtin_pages();
	$i18n     = isset( $settings['i18n'] ) && is_array( $settings['i18n'] ) ? wp_parse_args( $settings['i18n'], yneko_reimu_i18n_defaults() ) : yneko_reimu_i18n_defaults();
	$search   = yneko_reimu_settings_search();
	$features = yneko_reimu_settings_features();
	$player   = yneko_reimu_settings_player();
	$third_party = yneko_reimu_settings_third_party();
	$external_comments = yneko_reimu_settings_external_comments();
	$review_badges = yneko_reimu_admin_review_badge_counts();
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
				<button type="button" class="nav-tab" data-yneko-settings-tab="comments"><?php yneko_reimu_admin_bilingual_label( '评论设置', 'Comments' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['comments'] ?? 0 ) ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="users"><?php yneko_reimu_admin_bilingual_label( '用户设置', 'Users' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['users'] ?? 0 ) ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="search"><?php yneko_reimu_admin_bilingual_label( '搜索设置', 'Search' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="extensions"><?php yneko_reimu_admin_bilingual_label( '扩展与第三方', 'Extensions' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="external-comments"><?php yneko_reimu_admin_bilingual_label( '外部评论', 'External comments' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="friends"><?php yneko_reimu_admin_bilingual_label( '友链设置', 'Friend links' ); ?></button>
				<button type="button" class="nav-tab" data-yneko-settings-tab="music"><?php yneko_reimu_admin_bilingual_label( '曲目设置', 'Music' ); ?></button>
			</nav>

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

			<?php yneko_reimu_render_settings_i18n_panel( $i18n ); ?>

			<?php yneko_reimu_render_settings_github_panel( $oauth, $callback ); ?>

			<?php yneko_reimu_render_settings_comments_panel( $settings ); ?>

			<?php yneko_reimu_render_settings_users_panel( $review_badges ); ?>

			<?php yneko_reimu_render_settings_search_panel( $search ); ?>

			<?php yneko_reimu_render_settings_extensions_panel( $features, $third_party ); ?>
			<?php yneko_reimu_render_settings_external_comments_panel( $external_comments ); ?>

			<?php yneko_reimu_render_settings_friends_panel( $settings ); ?>
			<?php yneko_reimu_render_settings_music_panel( $settings, $player ); ?>

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
