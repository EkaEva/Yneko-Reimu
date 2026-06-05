<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_group_open( $title_zh, $title_en, $desc_zh = '', $desc_en = '' ) {
	?>
	<div class="yneko-reimu-settings-group">
		<div class="yneko-reimu-settings-group__header">
			<h3><?php yneko_reimu_admin_bilingual_heading( $title_zh, $title_en ); ?></h3>
			<?php if ( '' !== $desc_zh || '' !== $desc_en ) : ?>
				<?php yneko_reimu_admin_bilingual_description( $desc_zh, $desc_en ); ?>
			<?php endif; ?>
		</div>
		<div class="yneko-reimu-settings-group__body">
	<?php
}

function yneko_reimu_settings_group_close() {
	?>
		</div>
	</div>
	<?php
}

function yneko_reimu_settings_field_open( $label_zh, $label_en, $for = '' ) {
	?>
	<div class="yneko-reimu-field">
		<?php if ( $for ) : ?>
			<label class="yneko-reimu-field__label" for="<?php echo esc_attr( $for ); ?>"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></label>
		<?php else : ?>
			<div class="yneko-reimu-field__label"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></div>
		<?php endif; ?>
	<?php
}

function yneko_reimu_settings_field_close() {
	?>
	</div>
	<?php
}

function yneko_reimu_render_settings_i18n_panel( $i18n ) {
	?>
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
	<?php
}

function yneko_reimu_render_settings_github_panel( $oauth, $callback ) {
	?>
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
	<?php
}

function yneko_reimu_render_settings_comments_panel( $settings ) {
	$comment_upload = yneko_reimu_settings_comment_upload();
	?>
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
					<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
						<?php yneko_reimu_settings_field_open( '图片上传', 'Image uploads' ); ?>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_enabled]" value="1" <?php checked( '1', $comment_upload['image_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传图片', 'Allow logged-in users to upload images' ); ?></label>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][image_review]" value="1" <?php checked( '1', $comment_upload['image_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '图片人工审核', 'Review uploaded images' ); ?></label>
							<label><?php yneko_reimu_admin_bilingual_label( '图片上限 MB', 'Image max MB' ); ?> <input class="small-text" type="number" min="1" max="20" name="yneko_reimu_settings[comment_upload][image_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['image_max_mb'] ) ); ?>"></label>
						<?php yneko_reimu_settings_field_close(); ?>
						<?php yneko_reimu_settings_field_open( 'GIF 上传', 'GIF uploads' ); ?>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_enabled]" value="1" <?php checked( '1', $comment_upload['gif_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许登录用户上传 GIF', 'Allow logged-in users to upload GIFs' ); ?></label>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][gif_review]" value="1" <?php checked( '1', $comment_upload['gif_review'] ); ?>> <?php yneko_reimu_admin_bilingual_label( 'GIF 人工审核', 'Review uploaded GIFs' ); ?></label>
							<label><?php yneko_reimu_admin_bilingual_label( 'GIF 上限 MB', 'GIF max MB' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][gif_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['gif_max_mb'] ) ); ?>"></label>
						<?php yneko_reimu_settings_field_close(); ?>
						<?php yneko_reimu_settings_field_open( '清理规则', 'Cleanup rules' ); ?>
							<label><?php yneko_reimu_admin_bilingual_label( '临时文件清理天数', 'Temporary cleanup days' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][temp_cleanup_days]" value="<?php echo esc_attr( absint( $comment_upload['temp_cleanup_days'] ) ); ?>"></label>
							<label><?php yneko_reimu_admin_bilingual_label( '驳回后清理小时', 'Rejected cleanup hours' ); ?> <input class="small-text" type="number" min="1" max="168" name="yneko_reimu_settings[comment_upload][rejected_cleanup_hours]" value="<?php echo esc_attr( absint( $comment_upload['rejected_cleanup_hours'] ?? 24 ) ); ?>"></label>
						<?php yneko_reimu_settings_field_close(); ?>
					</div>
					<?php yneko_reimu_admin_bilingual_description( '未启用某类上传时，评论区对应上传按钮会隐藏。启用人工审核后，文件先留在临时目录并出现在下方待审核列表；批准后评论中的图片/GIF 才会生效。', 'When a type is disabled, its upload button is hidden in comments. With review enabled, uploads stay in the temporary folder and appear in the pending list below; approved files are then applied to comments.' ); ?>
				</td>
			</tr>
		</table>

		<h2><?php yneko_reimu_admin_bilingual_heading( '评论上传管理', 'Comment upload manager' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '登录用户上传的图片和 GIF 会集中显示在这里。待审核文件需要批准后才会在评论中生效；GIF 批准后也会出现在评论区 GIF 面板中。', 'Images and GIFs uploaded by logged-in users are listed here. Pending files must be approved before they work in comments; approved GIFs also appear in the comment GIF picker.' ); ?>
		<?php yneko_reimu_render_admin_comment_gif_upload(); ?>
		<?php yneko_reimu_render_comment_upload_admin(); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_users_panel( $review_badges ) {
	$comment_upload = yneko_reimu_settings_comment_upload();
	$user_badges    = yneko_reimu_settings_user_badges();
	$avatar_frames  = isset( $user_badges['avatar_frames'] ) && is_array( $user_badges['avatar_frames'] ) ? $user_badges['avatar_frames'] : array();
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="users" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户设置', 'User settings' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( '基本设置', 'Basic settings' ); ?></th>
				<td>
					<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-2">
						<?php yneko_reimu_settings_field_open( '用户标签与头像框', 'User badges and avatar frames' ); ?>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[user_badges][enabled]" value="1" <?php checked( '1', $user_badges['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区用户标签', 'Enable comment user badges' ); ?></label>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[user_badges][review_enabled]" value="1" <?php checked( '1', $user_badges['review_enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户标签审核', 'Review user custom badges' ); ?></label>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[user_badges][avatar_frames][enabled]" value="1" <?php checked( '1', $avatar_frames['enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '开启评论区头像框', 'Enable comment avatar frames' ); ?></label>
						<?php yneko_reimu_settings_field_close(); ?>
						<?php yneko_reimu_settings_field_open( '用户头像上传', 'User avatar uploads' ); ?>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_enabled]" value="1" <?php checked( '1', $comment_upload['avatar_enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许用户上传个人头像', 'Allow users to upload profile avatars' ); ?></label>
							<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][avatar_review]" value="1" <?php checked( '1', $comment_upload['avatar_review'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '用户头像审核', 'Review user avatars' ); ?></label>
							<label><?php yneko_reimu_admin_bilingual_label( '头像上限 MB', 'Avatar max MB' ); ?> <input class="small-text" type="number" min="1" max="10" name="yneko_reimu_settings[comment_upload][avatar_max_mb]" value="<?php echo esc_attr( absint( $comment_upload['avatar_max_mb'] ) ); ?>"></label>
						<?php yneko_reimu_settings_field_close(); ?>
					</div>
					<?php yneko_reimu_admin_bilingual_description( '用户标签、头像框和头像上传共同控制评论区用户身份展示。开启审核后，非管理员的新自定义标签或头像需要在下方批准后才会显示。', 'User badges, avatar frames, and avatar uploads together control comment-user identity display. With review enabled, new custom badges or avatars from non-admin users require approval below before they display.' ); ?>
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
		</table>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户标签审核', 'User badge review' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['user_badges'] ?? 0 ) ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '这里会列出已有和待审核的用户自定义标签。即使未开启审核，也可以在这里单独撤销某个用户的标签。', 'This lists existing and pending user custom badges. Even when review is disabled, you can revoke individual user badges here.' ); ?>
		<?php yneko_reimu_render_user_badge_admin(); ?>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户头像管理', 'User avatar manager' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['avatars'] ?? 0 ) ); ?></h2>
		<?php yneko_reimu_render_user_avatar_admin(); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_security_panel( $auth_security, $review_badges ) {
	$events          = function_exists( 'yneko_reimu_auth_security_events' ) ? yneko_reimu_auth_security_events() : array();
	$unhandled_count = absint( $review_badges['security'] ?? 0 );
	$mark_url        = wp_nonce_url( add_query_arg( 'yneko_auth_security_action', 'mark_handled', admin_url( 'themes.php?page=yneko-reimu-settings' ) ), 'yneko_reimu_auth_security_mark_handled' );
	$clear_url       = wp_nonce_url( add_query_arg( 'yneko_auth_security_action', 'clear', admin_url( 'themes.php?page=yneko-reimu-settings' ) ), 'yneko_reimu_auth_security_clear' );
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="security" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '安全设置', 'Security settings' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $unhandled_count ) ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '集中管理注册、忘记密码和资料邮箱验证码的发送限额。设备限制使用随机 Cookie，不做浏览器指纹。', 'Manage send limits for registration, lost-password, and profile email verification codes. Device limits use a random cookie, not browser fingerprinting.' ); ?>

		<?php yneko_reimu_settings_group_open( '认证邮件风控', 'Authentication email guard', '默认开启主题级保护，同时覆盖主题前台 AJAX 和 WordPress 原生登录入口。', 'Enabled by default. It covers both theme front-end AJAX and native WordPress login endpoints.' ); ?>
			<div class="yneko-reimu-checkbox-grid">
				<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][enabled]" value="1" <?php checked( '1', $auth_security['enabled'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用认证邮件风控', 'Enable authentication email guard' ); ?></label>
				<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][protect_ajax]" value="1" <?php checked( '1', $auth_security['protect_ajax'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '保护主题前台 AJAX', 'Protect theme front-end AJAX' ); ?></label>
				<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][protect_wp_login]" value="1" <?php checked( '1', $auth_security['protect_wp_login'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '保护 wp-login.php 注册/忘记密码', 'Protect wp-login.php register/lost password' ); ?></label>
				<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][email_alert_enabled]" value="1" <?php checked( '1', $auth_security['email_alert_enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '高危事件邮件报警', 'Email alerts for high-risk events' ); ?></label>
			</div>
			<?php yneko_reimu_admin_bilingual_description( '邮件报警默认关闭，避免被攻击时形成报警邮件风暴；后台角标和 error_log 始终可用。', 'Email alerts are disabled by default to avoid alert-mail storms during attacks. Admin badges and error_log remain available.' ); ?>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '限额预算', 'Rate budgets', '限额在发送验证码前检查，通过后立即计入预算；邮件发送失败也会记录报警。', 'Limits are checked before code emails are sent and counted immediately after passing; mail failures are logged as alerts.' ); ?>
			<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
				<label><?php yneko_reimu_admin_bilingual_label( '同一邮箱 / 小时', 'Same email / hour' ); ?> <input class="small-text" type="number" min="1" max="1000" name="yneko_reimu_settings[auth_security][email_hour_limit]" value="<?php echo esc_attr( absint( $auth_security['email_hour_limit'] ?? 3 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一邮箱 / 天', 'Same email / day' ); ?> <input class="small-text" type="number" min="1" max="5000" name="yneko_reimu_settings[auth_security][email_day_limit]" value="<?php echo esc_attr( absint( $auth_security['email_day_limit'] ?? 8 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一请求冷却秒数', 'Same request cooldown seconds' ); ?> <input class="small-text" type="number" min="10" max="3600" name="yneko_reimu_settings[auth_security][cooldown_seconds]" value="<?php echo esc_attr( absint( $auth_security['cooldown_seconds'] ?? 60 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一 IP / 小时', 'Same IP / hour' ); ?> <input class="small-text" type="number" min="1" max="5000" name="yneko_reimu_settings[auth_security][ip_hour_limit]" value="<?php echo esc_attr( absint( $auth_security['ip_hour_limit'] ?? 10 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一 IP / 天', 'Same IP / day' ); ?> <input class="small-text" type="number" min="1" max="20000" name="yneko_reimu_settings[auth_security][ip_day_limit]" value="<?php echo esc_attr( absint( $auth_security['ip_day_limit'] ?? 30 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '全站每日预算预警 %', 'Global daily warning %' ); ?> <input class="small-text" type="number" min="50" max="100" name="yneko_reimu_settings[auth_security][global_warning_threshold]" value="<?php echo esc_attr( absint( $auth_security['global_warning_threshold'] ?? 80 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一设备 / 小时', 'Same device / hour' ); ?> <input class="small-text" type="number" min="1" max="5000" name="yneko_reimu_settings[auth_security][device_hour_limit]" value="<?php echo esc_attr( absint( $auth_security['device_hour_limit'] ?? 5 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '同一设备 / 天', 'Same device / day' ); ?> <input class="small-text" type="number" min="1" max="20000" name="yneko_reimu_settings[auth_security][device_day_limit]" value="<?php echo esc_attr( absint( $auth_security['device_day_limit'] ?? 15 ) ); ?>"></label>
				<label><?php yneko_reimu_admin_bilingual_label( '全站每日发送预算', 'Global daily send budget' ); ?> <input class="small-text" type="number" min="1" max="100000" name="yneko_reimu_settings[auth_security][global_day_limit]" value="<?php echo esc_attr( absint( $auth_security['global_day_limit'] ?? 100 ) ); ?>"></label>
			</div>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '安全报警', 'Security alerts', '最近 100 条认证邮件风控事件会显示在这里。标记已处理只影响角标，不会清除限额计数。', 'The latest 100 authentication email guard events are shown here. Marking handled only affects badges and does not clear rate-limit counters.' ); ?>
			<div class="yneko-reimu-security-alert-actions">
				<span class="yneko-reimu-admin-text"><?php echo esc_html( sprintf( yneko_reimu_admin_prefers_zh() ? '未处理 %d 条' : '%d unhandled', $unhandled_count ) ); ?></span>
				<a class="button" href="<?php echo esc_url( $mark_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '全部标记已处理', 'Mark all handled' ); ?></a>
				<a class="button button-link-delete" href="<?php echo esc_url( $clear_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '清空日志', 'Clear log' ); ?></a>
			</div>
			<?php if ( $events ) : ?>
				<div class="yneko-reimu-security-alert-list">
					<?php foreach ( array_slice( $events, 0, 20 ) as $event ) : ?>
						<div class="yneko-reimu-security-alert-card<?php echo empty( $event['handled'] ) ? ' is-unhandled' : ''; ?>">
							<div class="yneko-reimu-security-alert-card__head">
								<strong><?php echo esc_html( function_exists( 'yneko_reimu_auth_security_event_type_label' ) ? yneko_reimu_auth_security_event_type_label( $event['type'] ?? '' ) : ( $event['type'] ?? '' ) ); ?></strong>
								<span><?php echo esc_html( date_i18n( 'Y-m-d H:i:s', absint( $event['time'] ?? 0 ) ) ); ?></span>
							</div>
							<div class="yneko-reimu-security-alert-card__meta">
								<code><?php echo esc_html( function_exists( 'yneko_reimu_auth_security_scope_label' ) ? yneko_reimu_auth_security_scope_label( $event['scope'] ?? '' ) : ( $event['scope'] ?? '' ) ); ?></code>
								<code><?php echo esc_html( ( $event['channel'] ?? '' ) . '/' . ( $event['dimension'] ?? '' ) . '/' . ( $event['period'] ?? '' ) ); ?></code>
								<code><?php echo esc_html( absint( $event['value'] ?? 0 ) . '/' . absint( $event['limit'] ?? 0 ) ); ?></code>
							</div>
							<div class="yneko-reimu-security-alert-card__hashes">
								<span>email: <code><?php echo esc_html( $event['email_hash'] ?? '' ); ?></code></span>
								<span>ip: <code><?php echo esc_html( $event['ip_hash'] ?? '' ); ?></code></span>
								<span>device: <code><?php echo esc_html( $event['device_hash'] ?? '' ); ?></code></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="description"><?php yneko_reimu_admin_bilingual_label( '暂无安全报警事件。', 'No security alert events yet.' ); ?></p>
			<?php endif; ?>
		<?php yneko_reimu_settings_group_close(); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_search_panel( $search ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="search" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '搜索设置', 'Search settings' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '搜索不依赖实时预览，因此统一在这里管理。优先级：Algolia 配置完整时优先；否则使用本地 JSON；再否则回退 WordPress REST。', 'Search does not need live preview, so it is managed here. Priority: Algolia when fully configured, then local JSON, then WordPress REST.' ); ?>
		<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索入口', 'Search providers' ); ?></th>
			<td>
				<label><input type="checkbox" name="yneko_reimu_settings[search][algolia_enable]" value="1" <?php checked( '1', $search['algolia_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Algolia 搜索入口', 'Enable Algolia search' ); ?></label><br>
				<label><input type="checkbox" name="yneko_reimu_settings[search][local_enable]" value="1" <?php checked( '1', $search['local_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用本地搜索入口', 'Enable local search' ); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-algolia-app-id">Algolia App ID</label></th>
			<td><input id="yneko-reimu-algolia-app-id" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_app_id]" value="<?php echo esc_attr( $search['algolia_app_id'] ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-algolia-api-key">Algolia Search API Key</label></th>
			<td>
				<input id="yneko-reimu-algolia-api-key" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_api_key]" value="<?php echo esc_attr( $search['algolia_api_key'] ); ?>">
				<?php yneko_reimu_admin_bilingual_description( '填写 Search-Only API Key，不要填写 Admin API Key。', 'Enter the Search-Only API Key, not an Admin API Key.' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-algolia-index-name">Algolia Index Name</label></th>
			<td><input id="yneko-reimu-algolia-index-name" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_index_name]" value="<?php echo esc_attr( $search['algolia_index_name'] ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-local-json"><?php yneko_reimu_admin_bilingual_label( '本地搜索 JSON URL', 'Local search JSON URL' ); ?></label></th>
			<td>
				<input id="yneko-reimu-local-json" class="regular-text" type="url" name="yneko_reimu_settings[search][local_json_url]" value="<?php echo esc_attr( $search['local_json_url'] ); ?>">
				<?php yneko_reimu_admin_bilingual_description( '留空时使用主题自动生成的 /search.json。', 'Leave empty to use the theme-generated /search.json.' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索索引内容', 'Search index content' ); ?></th>
			<td>
				<label><input type="checkbox" name="yneko_reimu_settings[search][index_full_content]" value="1" <?php checked( '1', $search['index_full_content'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '搜索索引包含全文', 'Include full content in search index' ); ?></label>
				<?php yneko_reimu_admin_bilingual_description( '默认关闭，仅输出标题、摘要、分类、标签和 URL。开启后 /search.json 会公开文章纯文本全文。', 'Disabled by default; only title, excerpt, categories, tags, and URL are output. When enabled, /search.json exposes plain-text post content.' ); ?>
			</td>
		</tr>
		</table>
	</section>
	<?php
}

function yneko_reimu_render_settings_friends_panel( $settings ) {
	$site_friend = yneko_reimu_sanitize_site_friend_info( $settings['friend_site'] ?? array() );
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="friends" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '友链设置', 'Friend link settings' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '用于友链页面的卡片列表，支持名称、链接、描述和头像。', 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.' ); ?>
		<h3><?php yneko_reimu_admin_bilingual_heading( '本站友链信息', 'Site friend-link info' ); ?></h3>
		<?php yneko_reimu_admin_bilingual_description( '用于友链页“本站信息”代码块。未配置图片时，将依次使用站点头像、作者头像和主题内置头像。', 'Used by the Site info code block on the friend-links page. When image is empty, the site avatar, author avatar, and bundled theme avatar are used in order.' ); ?>
		<table class="form-table yneko-reimu-site-friend-table" role="presentation">
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( '名称', 'Name' ); ?></th>
				<td><input type="text" class="regular-text" name="yneko_reimu_settings[friend_site][name]" value="<?php echo esc_attr( $site_friend['name'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( '链接', 'URL' ); ?></th>
				<td><input type="url" class="regular-text" name="yneko_reimu_settings[friend_site][url]" value="<?php echo esc_attr( $site_friend['url'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( '描述', 'Description' ); ?></th>
				<td><input type="text" class="regular-text" name="yneko_reimu_settings[friend_site][desc]" value="<?php echo esc_attr( $site_friend['desc'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Image', 'Image' ); ?></th>
				<td>
					<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[friend_site][image]', $site_friend['image'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ), 'image/png,image/webp' ); ?>
					<?php yneko_reimu_admin_bilingual_description( '仅建议使用 WebP 或 PNG，推荐正方形 512×512，体积控制在 200KB 以内。', 'Use WebP or PNG. A square 512x512 image under 200KB is recommended.' ); ?>
				</td>
			</tr>
		</table>
		<div class="yneko-reimu-repeatable" data-repeatable="friends">
			<div class="yneko-reimu-repeatable-list">
				<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
					<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增友链', 'Add friend' ) ); ?></button>
		</div>
	</section>
	<?php
}

function yneko_reimu_render_settings_music_panel( $settings, $player ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="music" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '曲目设置', 'Music track settings' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '播放器启用、播放行为、Meting 歌单和媒体库曲目统一在这里管理。播放器位置保留在自定义器中，方便观察侧栏布局。', 'Player enablement, playback behavior, Meting playlists, and Media Library tracks are managed here. Player position remains in the Customizer so sidebar layout can be previewed.' ); ?>
		<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放器入口', 'Player providers' ); ?></th>
			<td>
				<label><input type="checkbox" name="yneko_reimu_settings[player][aplayer_enable]" value="1" <?php checked( '1', $player['aplayer_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 APlayer 媒体库曲目', 'Enable APlayer Media Library tracks' ); ?></label><br>
				<label><input type="checkbox" name="yneko_reimu_settings[player][meting_enable]" value="1" <?php checked( '1', $player['meting_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Meting 歌单', 'Enable Meting playlist' ); ?></label>
				<?php yneko_reimu_admin_bilingual_description( 'APlayer 需要至少一首曲目；Meting 需要 auto URL，或同时填写 ID、server、type。配置不完整时前台不会输出空播放器。', 'APlayer needs at least one track. Meting needs an auto URL, or ID, server, and type together. Incomplete configuration does not render an empty player.' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放行为', 'Playback behavior' ); ?></th>
			<td>
				<label><input type="checkbox" name="yneko_reimu_settings[player][fixed]" value="1" <?php checked( '1', $player['fixed'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '固定播放器', 'Fixed player' ); ?></label><br>
				<label><input type="checkbox" name="yneko_reimu_settings[player][autoplay]" value="1" <?php checked( '1', $player['autoplay'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '自动播放', 'Autoplay' ); ?></label><br>
				<label><input type="checkbox" name="yneko_reimu_settings[player][mutex]" value="1" <?php checked( '1', $player['mutex'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '播放器互斥', 'Mutex' ); ?></label><br>
				<label><input type="checkbox" name="yneko_reimu_settings[player][list_folded]" value="1" <?php checked( '1', $player['list_folded'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '默认折叠播放列表', 'Fold playlist by default' ); ?></label>
			</td>
		</tr>
			<tr>
				<th scope="row"><?php yneko_reimu_admin_bilingual_label( '播放参数', 'Playback options' ); ?></th>
				<td>
				<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
					<label><?php yneko_reimu_admin_bilingual_label( '循环模式', 'Loop' ); ?>
						<select name="yneko_reimu_settings[player][loop]">
							<option value="all" <?php selected( $player['loop'], 'all' ); ?>>all</option>
							<option value="one" <?php selected( $player['loop'], 'one' ); ?>>one</option>
							<option value="none" <?php selected( $player['loop'], 'none' ); ?>>none</option>
						</select>
					</label>
					<label><?php yneko_reimu_admin_bilingual_label( '播放顺序', 'Order' ); ?>
						<select name="yneko_reimu_settings[player][order]">
							<option value="list" <?php selected( $player['order'], 'list' ); ?>>list</option>
							<option value="random" <?php selected( $player['order'], 'random' ); ?>>random</option>
						</select>
					</label>
					<label><?php yneko_reimu_admin_bilingual_label( '预加载', 'Preload' ); ?>
						<select name="yneko_reimu_settings[player][preload]">
							<option value="metadata" <?php selected( $player['preload'], 'metadata' ); ?>>metadata</option>
							<option value="none" <?php selected( $player['preload'], 'none' ); ?>>none</option>
							<option value="auto" <?php selected( $player['preload'], 'auto' ); ?>>auto</option>
						</select>
					</label>
					<label><?php yneko_reimu_admin_bilingual_label( '默认音量 0-1', 'Volume 0-1' ); ?> <input class="small-text" type="number" min="0" max="1" step="0.1" name="yneko_reimu_settings[player][volume]" value="<?php echo esc_attr( $player['volume'] ); ?>"></label>
					<label><?php yneko_reimu_admin_bilingual_label( '歌词模式', 'LRC type' ); ?> <input class="small-text" type="number" min="0" max="3" step="1" name="yneko_reimu_settings[player][lrc_type]" value="<?php echo esc_attr( absint( $player['lrc_type'] ) ); ?>"></label>
					<label><?php yneko_reimu_admin_bilingual_label( '列表最大高度', 'List max height' ); ?> <input type="text" name="yneko_reimu_settings[player][list_max_height]" value="<?php echo esc_attr( $player['list_max_height'] ); ?>" placeholder="320px"></label>
				</div>
				<?php yneko_reimu_admin_bilingual_description( '预加载默认 metadata，避免首屏过早下载完整音频。隐私/性能优先时可选 none。', 'Preload defaults to metadata to avoid downloading full audio during first paint. Use none for a privacy/performance-first setup.' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Meting 配置', 'Meting configuration' ); ?></th>
			<td>
				<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
					<label class="yneko-reimu-field-wide">Meting auto URL <input class="regular-text" type="url" name="yneko_reimu_settings[player][meting_auto]" value="<?php echo esc_attr( $player['meting_auto'] ); ?>"></label>
					<label>Meting ID <input type="text" name="yneko_reimu_settings[player][meting_id]" value="<?php echo esc_attr( $player['meting_id'] ); ?>"></label>
					<label>server <input type="text" name="yneko_reimu_settings[player][meting_server]" value="<?php echo esc_attr( $player['meting_server'] ); ?>" placeholder="netease"></label>
					<label>type <input type="text" name="yneko_reimu_settings[player][meting_type]" value="<?php echo esc_attr( $player['meting_type'] ); ?>" placeholder="playlist"></label>
				</div>
				<?php yneko_reimu_admin_bilingual_description( '填写 auto URL 后可不填 ID/server/type。', 'When auto URL is filled, ID/server/type can stay empty.' ); ?>
			</td>
		</tr>
		</table>
		<h3><?php yneko_reimu_admin_bilingual_heading( '媒体库曲目', 'Media Library tracks' ); ?></h3>
		<?php yneko_reimu_admin_bilingual_description( '未配置曲目且未配置 Meting 时，前台不会加载音乐播放器。', 'If neither tracks nor Meting are configured, the front-end music player is not loaded.' ); ?>
		<div class="yneko-reimu-repeatable" data-repeatable="music">
			<div class="yneko-reimu-repeatable-list">
				<?php foreach ( yneko_reimu_sanitize_music_items( $settings['music'] ) as $index => $track ) : ?>
					<?php yneko_reimu_render_music_row( $index, $track ); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button yneko-reimu-add-row" data-template="music"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增曲目', 'Add track' ) ); ?></button>
		</div>
	</section>
	<?php
}

function yneko_reimu_render_settings_extensions_panel( $features, $third_party ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="extensions" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '扩展与第三方', 'Extensions and third-party resources' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '这些功能通常会加载额外脚本或连接第三方域名，因此从自定义器移到主设置页集中管理。视觉与布局仍在自定义器中实时预览。', 'These features usually load extra scripts or contact third-party domains, so they are managed here. Visual and layout options remain in the Customizer for live preview.' ); ?>
		<?php yneko_reimu_settings_group_open( '前台增强', 'Front-end enhancements', '这些开关会改变前台加载、交互或内容增强行为。默认保持轻量。', 'These switches affect front-end loading, interactions, or content enhancements. Defaults stay lightweight.' ); ?>
			<?php
			$feature_labels = array(
				'preloader_enable' => array( '加载动画', 'Loading animation' ),
				'top_enable'       => array( '回到顶部太极按钮', 'Back-to-top Taichi button' ),
				'triangle_badge'   => array( '右上角 GitHub 三角标', 'GitHub corner ribbon' ),
				'firework_enable'  => array( '鼠标烟花', 'Mouse firework' ),
				'pjax_enable'      => array( 'PJAX 软导航', 'PJAX navigation' ),
				'busuanzi_enable'  => array( '不蒜子统计', 'Busuanzi statistics' ),
				'katex_enable'     => array( 'KaTeX 数学公式', 'KaTeX math' ),
				'photoswipe_enable' => array( 'PhotoSwipe 图片灯箱', 'PhotoSwipe lightbox' ),
				'mermaid_enable'    => array( 'Mermaid 图表', 'Mermaid diagrams' ),
				'custom_cursor'     => array( '自定义鼠标指针', 'Custom cursor' ),
			);
			?>
			<div class="yneko-reimu-checkbox-grid">
				<?php foreach ( $feature_labels as $key => $label ) : ?>
					<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( '1', $features[ $key ] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( $label[0], $label[1] ); ?></label>
				<?php endforeach; ?>
			</div>
		<?php yneko_reimu_settings_group_close(); ?>

		<?php yneko_reimu_settings_group_open( '第三方资源', 'Third-party resources', '启用后可能连接第三方域名；需要隐私优先时可关闭功能或替换为自托管地址。', 'These options may contact third-party domains when enabled. For privacy-first sites, disable them or replace URLs with self-hosted resources.' ); ?>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[third_party][live2d_enable]" value="1" <?php checked( '1', $third_party['live2d_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Live2D Widgets', 'Enable Live2D Widgets' ); ?></label>
			<div class="yneko-reimu-field">
				<label class="yneko-reimu-field__label" for="yneko-reimu-live2d-base"><?php yneko_reimu_admin_bilingual_label( 'Live2D Widgets 资源地址', 'Live2D Widgets resource URL' ); ?></label>
				<input id="yneko-reimu-live2d-base" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_base_url]" value="<?php echo esc_attr( $third_party['live2d_base_url'] ); ?>">
			</div>
			<div class="yneko-reimu-field">
				<label class="yneko-reimu-field__label" for="yneko-reimu-live2d-api"><?php yneko_reimu_admin_bilingual_label( 'Live2D 模型 CDN 地址', 'Live2D model CDN URL' ); ?></label>
				<input id="yneko-reimu-live2d-api" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_api_base_url]" value="<?php echo esc_attr( $third_party['live2d_api_base_url'] ); ?>">
			</div>
			<div class="yneko-reimu-field">
				<label class="yneko-reimu-field__label" for="yneko-reimu-vendor-cdn"><?php yneko_reimu_admin_bilingual_label( 'Vendor CDN 前缀', 'Vendor CDN base' ); ?></label>
				<input id="yneko-reimu-vendor-cdn" class="regular-text" type="url" name="yneko_reimu_settings[third_party][vendor_cdn_base]" value="<?php echo esc_attr( $third_party['vendor_cdn_base'] ); ?>">
				<?php yneko_reimu_admin_bilingual_description( '用于 Reimu 扩展包的 CDN 前缀。默认使用 jsDelivr，需要隐私优先时可替换为自托管资源。', 'CDN prefix for Reimu extension packages. The default uses jsDelivr; replace it with self-hosted resources for a privacy-first setup.' ); ?>
			</div>
		<?php yneko_reimu_settings_group_close(); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_external_comments_panel( $external_comments ) {
	$external_comment_fields = array(
		'giscus'     => array( 'Giscus', array( 'repo', 'repo_id', 'category', 'category_id' ) ),
		'utterances' => array( 'Utterances', array( 'repo' ) ),
		'disqus'     => array( 'Disqus', array( 'shortname' ) ),
		'waline'     => array( 'Waline', array( 'server_url' ) ),
		'twikoo'     => array( 'Twikoo', array( 'env_id' ) ),
		'valine'     => array( 'Valine', array( 'app_id', 'app_key', 'server_url' ) ),
	);
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="external-comments" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '外部评论', 'External comments' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( 'WordPress 评论始终可用；第三方评论未启用或未填配置时不会加载。', 'WordPress comments are always available. Third-party comments load only when enabled and configured.' ); ?>
		<table class="form-table" role="presentation">
		<?php foreach ( $external_comment_fields as $prefix => $meta ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $meta[0] ); ?></th>
				<td>
					<label><input type="checkbox" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix ); ?>_enable]" value="1" <?php checked( '1', $external_comments[ $prefix . '_enable' ] ?? '0' ); ?>> <?php echo esc_html( $meta[0] ); ?></label>
					<?php foreach ( $meta[1] as $field ) : ?>
						<p><label><?php echo esc_html( $meta[0] . ' ' . $field ); ?><br><input class="regular-text" type="text" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix . '_' . $field ); ?>]" value="<?php echo esc_attr( $external_comments[ $prefix . '_' . $field ] ?? '' ); ?>"></label></p>
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	</section>
	<?php
}
