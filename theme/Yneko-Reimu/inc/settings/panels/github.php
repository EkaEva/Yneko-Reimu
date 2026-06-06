<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_github_panel( $oauth, $callback ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="github" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( 'GitHub 登录设置', 'GitHub login settings' ); ?></h2>
		<table class="form-table" role="presentation">
		<?php yneko_reimu_render_settings_github_callback_row( $oauth, $callback ); ?>
		<?php yneko_reimu_render_settings_github_client_row( $oauth ); ?>
		<?php yneko_reimu_render_settings_github_secret_row( $oauth ); ?>
		<?php yneko_reimu_render_settings_github_auto_create_row( $oauth ); ?>
		<?php yneko_reimu_render_settings_github_bind_row(); ?>
		</table>
	</section>
	<?php
}

function yneko_reimu_render_settings_github_callback_row( $oauth, $callback ) {
	?>
	<tr>
		<th scope="row"><label for="yneko-reimu-callback-url"><?php yneko_reimu_admin_bilingual_label( '回调地址', 'Callback URL' ); ?></label></th>
		<td>
			<input id="yneko-reimu-callback-url" class="regular-text" type="url" name="yneko_reimu_settings[github_oauth][callback_url]" value="<?php echo esc_attr( $oauth['callback_url'] ); ?>" placeholder="<?php echo esc_attr( $callback ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '留空时自动使用下方默认地址；如果站点经过反向代理、固定域名或特殊登录路径，可在这里覆盖。GitHub OAuth App 中的 Authorization callback URL 需要与最终地址完全一致。', 'Leave empty to use the default URL below. Override it when the site uses a reverse proxy, fixed public domain, or custom login path. The Authorization callback URL in GitHub OAuth App must match the final URL exactly.' ); ?>
			<p class="description"><code><?php echo esc_html( $callback ); ?></code></p>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_github_client_row( $oauth ) {
	?>
	<tr>
		<th scope="row"><label for="yneko-reimu-client-id"><?php yneko_reimu_admin_bilingual_label( '客户端 ID', 'Client ID' ); ?></label></th>
		<td>
			<input id="yneko-reimu-client-id" class="regular-text" type="text" name="yneko_reimu_settings[github_oauth][client_id]" value="<?php echo esc_attr( $oauth['client_id'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '填写 GitHub OAuth App 提供的 Client ID。留空时前台不显示 GitHub 登录按钮。', 'Enter the Client ID from your GitHub OAuth App. If empty, the GitHub login button is hidden on the front end.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_github_secret_row( $oauth ) {
	?>
	<tr>
		<th scope="row"><label for="yneko-reimu-client-secret"><?php yneko_reimu_admin_bilingual_label( '客户端密钥', 'Client Secret' ); ?></label></th>
		<td>
			<input id="yneko-reimu-client-secret" class="regular-text" type="password" autocomplete="off" name="yneko_reimu_settings[github_oauth][client_secret]" value="<?php echo esc_attr( $oauth['client_secret'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '密钥只保存在 WordPress 数据库中，不会写入主题源码或发布包。', 'The secret is stored only in the WordPress database and is never written into the theme source or release package.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_github_auto_create_row( $oauth ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自动创建用户', 'Auto-create users' ); ?></th>
		<td><label><input type="checkbox" name="yneko_reimu_settings[github_oauth][auto_create]" value="1" <?php checked( '1', $oauth['auto_create'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许 GitHub 登录自动创建 WordPress 用户', 'Allow GitHub login to create WordPress users automatically' ); ?></label></td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_github_bind_row() {
	$github_bind_url   = function_exists( 'yneko_reimu_github_login_bind_url' ) ? yneko_reimu_github_login_bind_url( admin_url( 'themes.php?page=yneko-reimu-settings' ) ) : '';
	$github_bound_name = get_user_meta( get_current_user_id(), '_yneko_reimu_github_login', true );
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '绑定当前账户', 'Bind current account' ); ?></th>
		<td>
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
	<?php
}
