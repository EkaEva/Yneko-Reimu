<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_security_panel( $auth_security, $security, $review_badges ) {
	$events          = function_exists( 'yneko_reimu_auth_security_events' ) ? yneko_reimu_auth_security_events() : array();
	$unhandled_count = absint( $review_badges['security'] ?? 0 );
	$mark_url        = wp_nonce_url( add_query_arg( 'yneko_auth_security_action', 'mark_handled', admin_url( 'themes.php?page=yneko-reimu-settings' ) ), 'yneko_reimu_auth_security_mark_handled' );
	$clear_url       = wp_nonce_url( add_query_arg( 'yneko_auth_security_action', 'clear', admin_url( 'themes.php?page=yneko-reimu-settings' ) ), 'yneko_reimu_auth_security_clear' );
	$security        = yneko_reimu_settings_security_panel_values( $security );
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="security" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '安全设置', 'Security settings' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $unhandled_count ) ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '集中管理注册、忘记密码和资料邮箱验证码的发送限额。设备限制使用随机 Cookie，不做浏览器指纹。', 'Manage send limits for registration, lost-password, and profile email verification codes. Device limits use a random cookie, not browser fingerprinting.' ); ?>

		<?php yneko_reimu_render_settings_auth_security_toggles( $auth_security ); ?>
		<?php yneko_reimu_render_settings_auth_security_budgets( $auth_security ); ?>
		<?php yneko_reimu_render_settings_security_privacy_group( $security ); ?>
		<?php yneko_reimu_render_settings_security_alerts( $events, $unhandled_count, $mark_url, $clear_url ); ?>
	</section>
	<?php
}

function yneko_reimu_settings_security_panel_values( $security ) {
	$defaults = array(
		'allow_svg_uploads'        => '1',
		'comment_ip_region_lookup' => '1',
	);

	return is_array( $security ) ? wp_parse_args( $security, $defaults ) : $defaults;
}

function yneko_reimu_render_settings_auth_security_toggles( $auth_security ) {
	?>
	<?php yneko_reimu_settings_group_open( '认证邮件风控', 'Authentication email guard', '默认开启主题级保护，同时覆盖主题前台 AJAX 和 WordPress 原生登录入口。', 'Enabled by default. It covers both theme front-end AJAX and native WordPress login endpoints.' ); ?>
		<div class="yneko-reimu-checkbox-grid">
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][enabled]" value="1" <?php checked( '1', $auth_security['enabled'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用认证邮件风控', 'Enable authentication email guard' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][protect_ajax]" value="1" <?php checked( '1', $auth_security['protect_ajax'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '保护主题前台 AJAX', 'Protect theme front-end AJAX' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][protect_wp_login]" value="1" <?php checked( '1', $auth_security['protect_wp_login'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '保护 wp-login.php 注册/忘记密码', 'Protect wp-login.php register/lost password' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[auth_security][email_alert_enabled]" value="1" <?php checked( '1', $auth_security['email_alert_enabled'] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( '高危事件邮件报警', 'Email alerts for high-risk events' ); ?></label>
		</div>
		<?php yneko_reimu_admin_bilingual_description( '邮件报警默认关闭，避免被攻击时形成报警邮件风暴；后台角标和 error_log 始终可用。', 'Email alerts are disabled by default to avoid alert-mail storms during attacks. Admin badges and error_log remain available.' ); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_auth_security_budgets( $auth_security ) {
	$fields = array(
		array( 'email_hour_limit', '同一邮箱 / 小时', 'Same email / hour', 1, 1000, 3 ),
		array( 'email_day_limit', '同一邮箱 / 天', 'Same email / day', 1, 5000, 8 ),
		array( 'cooldown_seconds', '同一请求冷却秒数', 'Same request cooldown seconds', 10, 3600, 60 ),
		array( 'ip_hour_limit', '同一 IP / 小时', 'Same IP / hour', 1, 5000, 10 ),
		array( 'ip_day_limit', '同一 IP / 天', 'Same IP / day', 1, 20000, 30 ),
		array( 'global_warning_threshold', '全站每日预算预警 %', 'Global daily warning %', 50, 100, 80 ),
		array( 'device_hour_limit', '同一设备 / 小时', 'Same device / hour', 1, 5000, 5 ),
		array( 'device_day_limit', '同一设备 / 天', 'Same device / day', 1, 20000, 15 ),
		array( 'global_day_limit', '全站每日发送预算', 'Global daily send budget', 1, 100000, 100 ),
	);
	?>
	<?php yneko_reimu_settings_group_open( '限额预算', 'Rate budgets', '限额在发送验证码前检查，通过后立即计入预算；邮件发送失败也会记录报警。', 'Limits are checked before code emails are sent and counted immediately after passing; mail failures are logged as alerts.' ); ?>
		<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
			<?php foreach ( $fields as $field ) : ?>
				<label><?php yneko_reimu_admin_bilingual_label( $field[1], $field[2] ); ?> <input class="small-text" type="number" min="<?php echo esc_attr( $field[3] ); ?>" max="<?php echo esc_attr( $field[4] ); ?>" name="yneko_reimu_settings[auth_security][<?php echo esc_attr( $field[0] ); ?>]" value="<?php echo esc_attr( absint( $auth_security[ $field[0] ] ?? $field[5] ) ); ?>"></label>
			<?php endforeach; ?>
		</div>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_security_privacy_group( $security ) {
	?>
	<?php yneko_reimu_settings_group_open( '媒体与隐私', 'Media and privacy', '这里控制站点媒体上传能力和评论区可能产生的第三方地区查询。默认保持现有主题行为。', 'Control media upload capability and comment-area third-party region lookups here. Defaults preserve the current theme behavior.' ); ?>
		<div class="yneko-reimu-checkbox-grid">
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[security][allow_svg_uploads]" value="1" <?php checked( '1', $security['allow_svg_uploads'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '允许管理员上传 SVG', 'Allow administrators to upload SVG' ); ?></label>
			<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[security][comment_ip_region_lookup]" value="1" <?php checked( '1', $security['comment_ip_region_lookup'] ?? '1' ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示评论 IP 地区信息', 'Show comment IP region info' ); ?></label>
		</div>
		<?php yneko_reimu_admin_bilingual_description( 'SVG 上传仍只开放给管理员，并会经过主题基础净化；关闭后主题不再为媒体库放行 SVG。关闭评论 IP 地区信息后，主题不会请求 ipwho.is，评论环境标签只保留浏览器和系统信息。', 'SVG uploads remain administrator-only and pass through the theme sanitizer; disabling this stops the theme from allowing SVG in the Media Library. Disabling comment IP region info prevents ipwho.is requests, leaving only browser and OS badges.' ); ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_security_alerts( $events, $unhandled_count, $mark_url, $clear_url ) {
	?>
	<?php yneko_reimu_settings_group_open( '安全报警', 'Security alerts', '最近 100 条认证邮件风控事件会显示在这里。标记已处理只影响角标，不会清除限额计数。', 'The latest 100 authentication email guard events are shown here. Marking handled only affects badges and does not clear rate-limit counters.' ); ?>
		<div class="yneko-reimu-security-alert-actions">
			<span class="yneko-reimu-admin-text"><?php echo esc_html( sprintf( yneko_reimu_admin_prefers_zh() ? '未处理 %d 条' : '%d unhandled', $unhandled_count ) ); ?></span>
			<a class="button" href="<?php echo esc_url( $mark_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '全部标记已处理', 'Mark all handled' ); ?></a>
			<a class="button button-link-delete" href="<?php echo esc_url( $clear_url ); ?>"><?php yneko_reimu_admin_bilingual_label( '清空日志', 'Clear log' ); ?></a>
		</div>
		<?php if ( $events ) : ?>
			<div class="yneko-reimu-security-alert-list">
				<?php foreach ( array_slice( $events, 0, 20 ) as $event ) : ?>
					<?php yneko_reimu_render_settings_security_alert_card( $event ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="description"><?php yneko_reimu_admin_bilingual_label( '暂无安全报警事件。', 'No security alert events yet.' ); ?></p>
		<?php endif; ?>
	<?php yneko_reimu_settings_group_close(); ?>
	<?php
}

function yneko_reimu_render_settings_security_alert_card( $event ) {
	?>
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
	<?php
}
