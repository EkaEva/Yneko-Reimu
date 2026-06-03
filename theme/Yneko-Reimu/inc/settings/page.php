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
				<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '视觉预览工作台', 'Visual preview workspace' ); ?></th>
					<td>
						<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '打开 WordPress 自定义', 'Open WordPress Customizer' ); ?></a>
						<?php yneko_reimu_admin_bilingual_description( '站点图标、Logo、作者头像、横幅、封面、搜索背景、强调色、侧栏、导航、首页胶囊和页脚文字保留在自定义器中，方便使用右侧实时预览。', 'Site icon, logo, author avatar, banners, covers, search background, accent color, sidebar, navigation, home capsules, and footer text remain in the Customizer for live preview.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '第三方资源提示', 'Third-party resources' ); ?></th>
					<td>
						<?php yneko_reimu_admin_bilingual_description( '启用 Google Fonts、GA、Cloudflare RUM、jsDelivr、APlayer、mouse-firework、Live2D、Algolia、Busuanzi 等功能后，前台可能连接对应第三方域名。需要隐私优先时，请关闭相关扩展，或在“扩展与第三方”中把 Vendor CDN / Live2D 地址替换为自托管资源。', 'When Google Fonts, GA, Cloudflare RUM, jsDelivr, APlayer, mouse-firework, Live2D, Algolia, Busuanzi, or similar features are enabled, the front end may contact those third-party domains. For a privacy-first setup, disable those extensions or replace Vendor CDN / Live2D URLs with self-hosted resources in Extensions.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( '内置页面开关', 'Built-in pages' ); ?></th>
					<td>
						<input type="hidden" name="yneko_reimu_settings[builtin_pages][_present]" value="1">
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][projects]" value="1" <?php checked( '1', $builtin_pages['projects'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '项目页', 'Projects page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][archives]" value="1" <?php checked( '1', $builtin_pages['archives'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '归档页', 'Archives page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][about]" value="1" <?php checked( '1', $builtin_pages['about'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '关于页', 'About page' ); ?></label><br>
						<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[builtin_pages][friend]" value="1" <?php checked( '1', $builtin_pages['friend'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '友链页', 'Friends page' ); ?></label>
						<?php yneko_reimu_admin_bilingual_description( '关闭后会从主题默认导航和菜单中的对应内置链接移除，并让对应内置路径返回 404；不会影响 WordPress 原生分类、标签、日期等归档页。', 'Disabled pages are removed from the theme default navigation and matching built-in menu links, and their built-in paths return 404. Native WordPress category, tag, date, and other archives are not affected.' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Favicon / Apple Touch 兜底图', 'Favicon / Apple Touch fallback' ); ?></th>
					<td>
						<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[favicon_fallback_url]', $settings['favicon_fallback_url'], yneko_reimu_admin_bilingual_text( '选择 PNG/JPG', 'Choose PNG/JPG' ), 'image/png,image/jpeg' ); ?>
						<?php yneko_reimu_admin_bilingual_description( '站点图标和 Logo 仍可使用 SVG；这里建议额外设置一张 512×512 的 PNG/JPG，用于不稳定支持 SVG favicon 或 apple-touch-icon 的浏览器、移动端和聊天软件预览。此项不会影响 Rank Math 的 og:image。', 'The site icon and logo can still use SVG. Add a square 512x512 PNG/JPG here as a fallback for browsers, mobile devices, and chat previews that do not reliably support SVG favicon or apple-touch-icon. This does not affect Rank Math og:image.' ); ?>
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

			<?php yneko_reimu_render_settings_i18n_panel( $i18n ); ?>

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
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_enabled]" value="1" <?php checked( '1', $comment_upload['image_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传图片', 'Allow logged-in users to upload images' ); ?></label>
								&nbsp;
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_review]" value="1" <?php checked( '1', $comment_upload['image_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '图片人工审核', 'Review uploaded images' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '图片上限 MB', 'Image max MB' ); ?> <input class="small-text" type="number" min="1" max="20" name="yneko_reimu_settings[comment_upload][image_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['image_max_mb'] ) ); ?>"></label>
							</p>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_enabled]" value="1" <?php checked( '1', $comment_upload['gif_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传 GIF', 'Allow logged-in users to upload GIFs' ); ?></label>
								&nbsp;
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_review]" value="1" <?php checked( '1', $comment_upload['gif_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( 'GIF 人工审核', 'Review uploaded GIFs' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( 'GIF 上限 MB', 'GIF max MB' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][gif_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['gif_max_mb'] ) ); ?>"></label>
							</p>
							<p>
								<label><?php yneko_reimu_admin_bilingual_label( '临时文件清理天数', 'Temporary file cleanup days' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][temp_cleanup_days]" value="<?php echo esc_attr( absint( $comment_upload['temp_cleanup_days'] ) ); ?>"></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '驳回后文件清理时间（小时）', 'Rejected file cleanup hours' ); ?> <input class="small-text" type="number" min="1" max="168" name="yneko_reimu_settings[comment_upload][rejected_cleanup_hours]" value="<?php echo esc_attr( absint( $comment_upload['rejected_cleanup_hours'] ?? 24 ) ); ?>"></label>
							</p>
							<?php yneko_reimu_admin_bilingual_description( '未启用某类上传时，评论区对应上传按钮会隐藏。启用人工审核后，文件先留在临时目录并出现在下方待审核列表；批准后评论中的图片/GIF 才会生效。', 'When a type is disabled, its upload button is hidden in comments. With review enabled, uploads stay in the temporary folder and appear in the pending list below; approved files are then applied to comments.' ); ?>
						</td>
					</tr>
				</table>

				<h2><?php yneko_reimu_admin_bilingual_heading( '评论上传管理', 'Comment upload manager' ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '登录用户上传的图片和 GIF 会集中显示在这里。待审核文件需要批准后才会在评论中生效；GIF 批准后也会出现在评论区 GIF 面板中。', 'Images and GIFs uploaded by logged-in users are listed here. Pending files must be approved before they work in comments; approved GIFs also appear in the comment GIF picker.' ); ?>
				<?php yneko_reimu_render_admin_comment_gif_upload(); ?>
				<?php yneko_reimu_render_comment_upload_admin(); ?>
			</section>

			<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="users" hidden>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户设置', 'User settings' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '用户标签及头像框', 'User badges and avatar frames' ); ?></th>
						<td>
							<?php $user_badges = yneko_reimu_settings_user_badges(); ?>
							<?php $avatar_frames = isset( $user_badges['avatar_frames'] ) && is_array( $user_badges['avatar_frames'] ) ? $user_badges['avatar_frames'] : array(); ?>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][enabled]" value="1" <?php checked( '1', $user_badges['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区用户标签', 'Enable comment user badges' ); ?></label></p>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][review_enabled]" value="1" <?php checked( '1', $user_badges['review_enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户标签审核', 'Review user custom badges' ); ?></label></p>
							<p><label><input type="checkbox" name="yneko_reimu_settings[user_badges][avatar_frames][enabled]" value="1" <?php checked( '1', $avatar_frames['enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区头像框', 'Enable comment avatar frames' ); ?></label></p>
							<?php yneko_reimu_admin_bilingual_description( '关闭总开关后，除站长/管理员等特殊身份外，普通用户自定义标签不会显示，个人资料中也不显示标签设置区。开启审核后，非管理员的新自定义标签需要在下方批准后才会显示。', 'When the main switch is off, ordinary custom badges are hidden except special identity badges, and profile badge settings are hidden. With review enabled, new custom badges from non-admin users require approval below before they display.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '特殊标签及用户头像框管理', 'Special badges and avatar frames' ); ?></th>
						<td>
							<div class="yneko-reimu-special-badge-table">
								<?php
								foreach ( yneko_reimu_user_badge_base_definitions() as $badge_key => $definition ) :
									$row = $user_badges['special'][ $badge_key ] ?? array( 'enabled' => '1', 'zh' => $definition['zh'], 'en' => $definition['en'] );
									$frame_url = $avatar_frames['frames'][ $badge_key ] ?? yneko_reimu_default_avatar_frame_url();
									?>
									<div class="yneko-reimu-special-badge-row">
										<label><input type="checkbox" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][enabled]" value="1" <?php checked( '1', $row['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( $definition['title_zh'], $definition['title_en'] ); ?></label>
										<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][zh]" value="<?php echo esc_attr( $row['zh'] ); ?>" placeholder="<?php echo esc_attr( $definition['zh'] ); ?>">
										<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][en]" value="<?php echo esc_attr( $row['en'] ); ?>" placeholder="<?php echo esc_attr( $definition['en'] ); ?>">
										<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[user_badges][avatar_frames][frames][' . $badge_key . ']', $frame_url, yneko_reimu_admin_bilingual_text( '选择头像框', 'Choose frame' ), 'image/png,image/webp,image/avif' ); ?>
										<span class="description"><?php echo esc_html( yneko_reimu_admin_prefers_zh() ? $definition['desc_zh'] : $definition['desc_en'] ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
							<?php yneko_reimu_admin_bilingual_description( '七种基础特殊标签按“站长 > 管理员 > 编辑 > 作者 > 贡献者 > 会员 > 订阅者”排序。标签原名和当前显示名都会作为保留词。头像框支持 PNG、WebP、AVIF；用户同时拥有多个特殊标签时，按这个顺序使用第一个可用头像框。', 'The seven base special badges are ordered by Owner > Admin > Editor > Author > Contributor > Member > Subscriber. Original and current labels are reserved. Avatar frames support PNG, WebP, and AVIF; when a user has multiple special badges, the first available frame in this order is used.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自定义标签屏蔽词', 'Custom badge blocklist' ); ?></th>
						<td>
							<input class="regular-text" type="text" name="yneko_reimu_settings[user_badges][blocklist]" value="<?php echo esc_attr( $user_badges['blocklist'] ?? '' ); ?>" placeholder="<?php esc_attr_e( '广告/官方/测试', 'yneko-reimu' ); ?>">
							<?php yneko_reimu_admin_bilingual_description( '用 / 分隔。保存后，匹配屏蔽词或保留词的旧自定义标签会自动停止显示，用户也不能再次设置。', 'Separate words with /. After saving, old custom badges matching blocked or reserved words stop displaying automatically, and users cannot set them again.' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php yneko_reimu_admin_bilingual_label( '用户头像上传', 'User avatar uploads' ); ?></th>
						<td>
							<?php $comment_upload = yneko_reimu_settings_comment_upload(); ?>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_enabled]" value="1" <?php checked( '1', $comment_upload['avatar_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许用户上传个人头像', 'Allow users to upload profile avatars' ); ?></label>
							</p>
							<p>
								<label><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_review]" value="1" <?php checked( '1', $comment_upload['avatar_review'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户头像审核', 'Review user avatars' ); ?></label>
								&nbsp;
								<label><?php yneko_reimu_admin_bilingual_label( '头像上限 MB', 'Avatar max MB' ); ?> <input class="small-text" type="number" min="1" max="10" name="yneko_reimu_settings[comment_upload][avatar_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['avatar_max_mb'] ) ); ?>"></label>
							</p>
							<?php yneko_reimu_admin_bilingual_description( '未开启上传时，用户仍可填写头像图片链接。开启审核后，新上传头像先进入临时目录，批准后才会应用。', 'When upload is disabled, users can still use an avatar image URL. When review is enabled, new uploads go to a temporary directory and apply only after approval.' ); ?>
						</td>
					</tr>
				</table>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户标签审核', 'User badge review' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['user_badges'] ?? 0 ) ); ?></h2>
				<?php yneko_reimu_admin_bilingual_description( '这里会列出已有和待审核的用户自定义标签。即使未开启审核，也可以在这里单独撤销某个用户的标签。', 'This lists existing and pending user custom badges. Even when review is disabled, you can revoke individual user badges here.' ); ?>
				<?php yneko_reimu_render_user_badge_admin(); ?>
				<h2><?php yneko_reimu_admin_bilingual_heading( '用户头像管理', 'User avatar manager' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['avatars'] ?? 0 ) ); ?></h2>
				<?php yneko_reimu_render_user_avatar_admin(); ?>
			</section>

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
