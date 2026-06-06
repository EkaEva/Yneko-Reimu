<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_login_modal() {
	if ( is_user_logged_in() ) {
		yneko_reimu_profile_modal();
		return;
	}
	echo yneko_reimu_login_modal_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function yneko_reimu_login_modal_html() {
	if ( is_user_logged_in() ) {
		return '';
	}
	$allow_registration = (bool) get_option( 'users_can_register' );
	ob_start();
	?>
	<div class="reimu-login-modal" id="reimu-login-modal" aria-hidden="true">
		<div class="reimu-login-modal__mask" data-login-close></div>
		<div class="reimu-login-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-login-title">
			<button type="button" class="reimu-login-modal__close popup-btn-close" data-login-close aria-label="<?php esc_attr_e( '关闭登录窗口', 'yneko-reimu' ); ?>"></button>
			<h2 id="reimu-login-title"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></h2>
			<p class="reimu-login-modal__desc" hidden></p>
			<?php yneko_reimu_login_modal_login_form( $allow_registration ); ?>
			<?php if ( $allow_registration ) : ?>
				<?php yneko_reimu_login_modal_register_form(); ?>
			<?php endif; ?>
			<?php yneko_reimu_login_modal_lost_form(); ?>
			<?php do_action( 'yneko_reimu_login_modal_social' ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function yneko_reimu_login_modal_password_row( $id, $name, $label, $autocomplete, $required = true ) {
	?>
	<p>
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<span class="reimu-login-password-row"><input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="password" autocomplete="<?php echo esc_attr( $autocomplete ); ?>"<?php echo $required ? ' required' : ''; ?><?php echo 'new-password' === $autocomplete ? ' minlength="8"' : ''; ?>><button type="button" class="reimu-password-toggle" data-password-toggle aria-label="<?php esc_attr_e( '显示密码', 'yneko-reimu' ); ?>"></button></span>
	</p>
	<?php
}

function yneko_reimu_login_modal_email_code_row( $id, $button_attr ) {
	$button_attr = in_array( $button_attr, array( 'data-register-code-send', 'data-lost-code-send' ), true ) ? $button_attr : '';
	?>
	<p>
		<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( '邮箱验证码', 'yneko-reimu' ); ?></label>
		<span class="reimu-login-code-row">
			<input id="<?php echo esc_attr( $id ); ?>" name="verify_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
			<button class="reimu-login-code-button" type="button" <?php echo $button_attr ? esc_attr( $button_attr ) : ''; ?>><?php esc_html_e( '发送验证码', 'yneko-reimu' ); ?></button>
		</span>
	</p>
	<?php
}

function yneko_reimu_login_modal_login_form( $allow_registration ) {
	?>
	<form class="reimu-login-form reimu-login-panel is-active" data-reimu-login-form data-login-panel="login">
		<p>
			<label for="reimu-login-user"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
			<input id="reimu-login-user" name="log" type="email" autocomplete="email" required>
		</p>
		<?php yneko_reimu_login_modal_password_row( 'reimu-login-password', 'pwd', __( '密码', 'yneko-reimu' ), 'current-password' ); ?>
		<p class="reimu-login-2fa" data-login-2fa hidden>
			<label for="reimu-login-2fa"><?php esc_html_e( '两步验证码', 'yneko-reimu' ); ?></label>
			<input id="reimu-login-2fa" name="two_factor_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
		</p>
		<label class="reimu-login-remember">
			<input name="rememberme" type="checkbox" value="forever">
			<span><?php esc_html_e( '记住我', 'yneko-reimu' ); ?></span>
		</label>
		<div class="reimu-login-message" data-login-message role="status" aria-live="polite"></div>
		<div class="reimu-login-actions">
			<button class="reimu-login-help-link" type="button" data-login-panel-trigger="lost"><?php esc_html_e( '忘记密码？', 'yneko-reimu' ); ?></button>
			<?php if ( $allow_registration ) : ?>
				<button class="reimu-login-register-button" type="button" data-login-panel-trigger="register"><?php esc_html_e( '注册', 'yneko-reimu' ); ?></button>
			<?php endif; ?>
			<button type="submit" class="reimu-login-submit"><?php esc_html_e( '登录', 'yneko-reimu' ); ?></button>
		</div>
	</form>
	<?php
}

function yneko_reimu_login_modal_register_form() {
	?>
	<form class="reimu-login-form reimu-login-panel" data-reimu-register-form data-login-panel="register" data-loading-text="<?php esc_attr_e( '注册中...', 'yneko-reimu' ); ?>" hidden>
		<p>
			<label for="reimu-register-display-name"><?php esc_html_e( '昵称', 'yneko-reimu' ); ?></label>
			<input id="reimu-register-display-name" name="display_name" type="text" autocomplete="nickname" required>
		</p>
		<p>
			<label for="reimu-register-email"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
			<input id="reimu-register-email" name="user_email" type="email" autocomplete="email" required>
		</p>
		<?php yneko_reimu_login_modal_password_row( 'reimu-register-password', 'user_password', __( '密码', 'yneko-reimu' ), 'new-password' ); ?>
		<?php yneko_reimu_login_modal_email_code_row( 'reimu-register-code', 'data-register-code-send' ); ?>
		<p class="reimu-login-note"><?php esc_html_e( '验证码会发送到您的邮箱，5 分钟内有效。', 'yneko-reimu' ); ?></p>
		<div class="reimu-login-message" data-register-message role="status" aria-live="polite"></div>
		<div class="reimu-login-actions">
			<button class="reimu-login-help-link" type="button" data-login-panel-trigger="login"><?php esc_html_e( '返回登录', 'yneko-reimu' ); ?></button>
			<button type="submit" class="reimu-login-submit"><?php esc_html_e( '注册', 'yneko-reimu' ); ?></button>
		</div>
	</form>
	<?php
}

function yneko_reimu_login_modal_lost_form() {
	?>
	<form class="reimu-login-form reimu-login-panel" data-reimu-lost-form data-login-panel="lost" data-loading-text="<?php esc_attr_e( '重置中...', 'yneko-reimu' ); ?>" hidden>
		<p>
			<label for="reimu-lost-user"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
			<input id="reimu-lost-user" name="user_login" type="email" autocomplete="email" required>
		</p>
		<?php yneko_reimu_login_modal_password_row( 'reimu-lost-password', 'user_password', __( '新密码', 'yneko-reimu' ), 'new-password' ); ?>
		<?php yneko_reimu_login_modal_email_code_row( 'reimu-lost-code', 'data-lost-code-send' ); ?>
		<p class="reimu-login-note"><?php esc_html_e( '验证码会发送到账号邮箱，5 分钟内有效。', 'yneko-reimu' ); ?></p>
		<div class="reimu-login-message" data-lost-message role="status" aria-live="polite"></div>
		<div class="reimu-login-actions">
			<button class="reimu-login-help-link" type="button" data-login-panel-trigger="login"><?php esc_html_e( '返回登录', 'yneko-reimu' ); ?></button>
			<button type="submit" class="reimu-login-submit"><?php esc_html_e( '重置密码', 'yneko-reimu' ); ?></button>
		</div>
	</form>
	<?php
}

function yneko_reimu_profile_modal() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	echo yneko_reimu_profile_modal_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function yneko_reimu_profile_modal_html() {
	if ( ! is_user_logged_in() ) {
		return '';
	}
	$context = yneko_reimu_profile_modal_context();
	$profile = $context['profile'];
	ob_start();
	?>
	<div class="reimu-profile-modal" id="reimu-profile-modal" aria-hidden="true" data-avatar-max-mb="<?php echo esc_attr( $profile['avatarMaxMb'] ); ?>">
		<div class="reimu-profile-modal__mask" data-profile-close></div>
		<div class="reimu-profile-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reimu-profile-title">
			<button type="button" class="reimu-login-modal__close popup-btn-close" data-profile-close aria-label="<?php esc_attr_e( '关闭个人资料窗口', 'yneko-reimu' ); ?>"></button>
			<h2 id="reimu-profile-title"><?php esc_html_e( '个人资料', 'yneko-reimu' ); ?></h2>
			<form class="reimu-profile-form" data-reimu-profile-form>
				<?php yneko_reimu_profile_modal_avatar_fields( $profile ); ?>
				<?php yneko_reimu_profile_modal_basic_fields( $profile ); ?>
				<?php yneko_reimu_profile_modal_tags( $profile, $context ); ?>
				<?php yneko_reimu_profile_modal_email_fields( $profile ); ?>
				<?php yneko_reimu_profile_modal_avatar_status( $profile ); ?>
				<?php yneko_reimu_profile_modal_password_fields(); ?>
				<?php yneko_reimu_profile_modal_totp_fields( $profile ); ?>
				<?php yneko_reimu_profile_modal_actions(); ?>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function yneko_reimu_profile_modal_context() {
	$profile      = yneko_reimu_user_profile_payload();
	$comment_tags = isset( $profile['commentTags'] ) && is_array( $profile['commentTags'] ) ? $profile['commentTags'] : yneko_reimu_comment_user_tags_payload( get_current_user_id() );
	$special_tags = array_values(
		array_filter(
			$comment_tags,
			static function ( $tag ) {
				return is_array( $tag ) && 'special' === ( $tag['type'] ?? '' ) && ! empty( $tag['key'] );
			}
		)
	);
	$enabled_special_count = count(
		array_filter(
			$special_tags,
			static function ( $tag ) {
				return is_array( $tag ) && '0' !== (string) ( $tag['enabled'] ?? '1' );
			}
		)
	);
	$custom_tags = array_values(
		array_filter(
			$comment_tags,
			static function ( $tag ) {
				return is_array( $tag ) && 'custom' === ( $tag['type'] ?? '' ) && '' !== ( $tag['label'] ?? '' );
			}
		)
	);

	return array(
		'profile'          => $profile,
		'special_tags'     => $special_tags,
		'custom_tags'      => $custom_tags,
		'custom_tag_slots' => max( 0, yneko_reimu_comment_badge_display_limit() - $enabled_special_count ),
	);
}

function yneko_reimu_profile_modal_avatar_fields( $profile ) {
	?>
	<div class="reimu-profile-avatar-preview"><img data-profile-avatar-preview src="<?php echo esc_url( $profile['avatarUrl'] ? $profile['avatarUrl'] : get_avatar_url( get_current_user_id(), array( 'size' => 96 ) ) ); ?>" alt=""></div>
	<p class="reimu-profile-avatar-field">
		<label for="reimu-profile-avatar-url" class="reimu-profile-avatar-label">
			<span><?php esc_html_e( '头像链接', 'yneko-reimu' ); ?></span>
			<small data-profile-avatar-hint role="status" aria-live="polite"></small>
		</label>
		<span class="reimu-profile-avatar-row">
			<input id="reimu-profile-avatar-url" name="avatar_url" type="url" value="<?php echo esc_attr( $profile['avatarUrl'] ); ?>">
			<input name="avatar_changed" type="hidden" value="0" data-profile-avatar-changed>
			<?php if ( ! empty( $profile['avatarUploadEnabled'] ) ) : ?>
				<button class="reimu-profile-avatar-upload" type="button" data-profile-avatar-upload><?php esc_html_e( '上传', 'yneko-reimu' ); ?></button>
				<input id="reimu-profile-avatar-file" name="avatar_file" type="file" accept="image/jpeg,image/png,image/webp" data-profile-avatar-file hidden>
			<?php endif; ?>
		</span>
	</p>
	<?php
}

function yneko_reimu_profile_modal_basic_fields( $profile ) {
	?>
	<p>
		<label for="reimu-profile-display-name"><?php esc_html_e( '昵称', 'yneko-reimu' ); ?></label>
		<input id="reimu-profile-display-name" name="display_name" type="text" value="<?php echo esc_attr( $profile['displayName'] ); ?>" required>
	</p>
	<p>
		<label for="reimu-profile-url"><?php esc_html_e( '个人主页', 'yneko-reimu' ); ?></label>
		<input id="reimu-profile-url" name="profile_url" type="text" inputmode="url" value="<?php echo esc_attr( $profile['profileUrl'] ); ?>">
	</p>
	<p class="reimu-profile-avatar-frame-toggle">
		<label class="reimu-login-remember"><input name="avatar_frame_enabled" type="checkbox" value="1" <?php checked( ! empty( $profile['avatarFrameEnabled'] ) ); ?>><span><?php esc_html_e( '显示我的评论头像框', 'yneko-reimu' ); ?></span></label>
	</p>
	<?php
}

function yneko_reimu_profile_modal_tags( $profile, $context ) {
	if ( empty( $profile['commentBadgesEnabled'] ) ) {
		return;
	}
	?>
	<div class="reimu-profile-tags">
		<div class="reimu-profile-tags__header">
			<span><?php esc_html_e( '评论标签', 'yneko-reimu' ); ?></span>
			<small><?php esc_html_e( '最多添加 5 个自定义标签；特殊标签和已勾选的自定义标签合计最多 2 个。自定义标签最多 8 个字符，保留标签不可自行设置。', 'yneko-reimu' ); ?></small>
		</div>
		<div class="reimu-profile-tags__message" data-profile-tags-message role="status" aria-live="polite" hidden></div>
		<?php yneko_reimu_profile_modal_special_tags( $context['special_tags'] ); ?>
		<div class="reimu-profile-tag-list" data-profile-tag-list data-max-tags="<?php echo esc_attr( $context['custom_tag_slots'] ); ?>" data-storage-limit="<?php echo esc_attr( yneko_reimu_comment_custom_tag_storage_limit() ); ?>" data-existing-tags="<?php echo esc_attr( wp_json_encode( $context['custom_tags'] ) ); ?>"></div>
		<button type="button" class="reimu-profile-add-tag" data-profile-add-tag><?php esc_html_e( '+ 新增自定义标签', 'yneko-reimu' ); ?></button>
	</div>
	<?php
}

function yneko_reimu_profile_modal_special_tags( $special_tags ) {
	if ( ! $special_tags ) {
		return;
	}
	?>
	<div class="reimu-profile-special-tag-list" data-profile-special-tag-list>
		<?php foreach ( $special_tags as $special_tag ) : ?>
			<label class="reimu-profile-special-tag-toggle">
				<input type="checkbox" name="comment_special_enabled[<?php echo esc_attr( $special_tag['key'] ); ?>]" value="1" <?php checked( '1', $special_tag['enabled'] ?? '1' ); ?>>
				<span><?php echo esc_html( $special_tag['label'] ); ?></span>
				<small><?php esc_html_e( '特殊标签', 'yneko-reimu' ); ?></small>
			</label>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_profile_modal_email_fields( $profile ) {
	?>
	<p>
		<label for="reimu-profile-email"><?php esc_html_e( '邮箱', 'yneko-reimu' ); ?></label>
		<output id="reimu-profile-email" class="reimu-profile-frozen-field" aria-readonly="true" aria-disabled="true" data-profile-current-email-display><?php echo esc_html( $profile['email'] ); ?></output>
		<input name="current_email" type="hidden" value="<?php echo esc_attr( $profile['email'] ); ?>" data-profile-current-email>
	</p>
	<p>
		<label for="reimu-profile-new-email"><?php esc_html_e( '新邮箱', 'yneko-reimu' ); ?></label>
		<input id="reimu-profile-new-email" name="user_email" type="email" autocomplete="email" data-profile-new-email>
	</p>
	<p>
		<label for="reimu-profile-email-code"><?php esc_html_e( '新邮箱验证码', 'yneko-reimu' ); ?></label>
		<span class="reimu-login-code-row">
			<input id="reimu-profile-email-code" name="email_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
			<button class="reimu-login-code-button" type="button" data-profile-email-code-send><?php esc_html_e( '发送验证码', 'yneko-reimu' ); ?></button>
		</span>
	</p>
	<?php
}

function yneko_reimu_profile_modal_avatar_status( $profile ) {
	if ( ! empty( $profile['avatarPending'] ) ) {
		?>
		<p class="reimu-profile-avatar-status" data-profile-avatar-status><?php esc_html_e( '头像审核中', 'yneko-reimu' ); ?></p>
		<?php
		return;
	}
	?>
	<p class="reimu-profile-avatar-status" data-profile-avatar-status hidden></p>
	<?php
}

function yneko_reimu_profile_modal_password_fields() {
	?>
	<?php yneko_reimu_login_modal_password_row( 'reimu-profile-password', 'new_password', __( '新密码', 'yneko-reimu' ), 'new-password', false ); ?>
	<?php yneko_reimu_login_modal_password_row( 'reimu-profile-password-confirm', 'new_password_confirm', __( '确认新密码', 'yneko-reimu' ), 'new-password', false ); ?>
	<?php
}

function yneko_reimu_profile_modal_totp_fields( $profile ) {
	$two_factor_active = ! empty( $profile['twoFactor'] );
	?>
	<div class="reimu-profile-2fa" data-profile-2fa-active="<?php echo $two_factor_active ? '1' : '0'; ?>">
		<label class="reimu-login-remember"><input name="totp_enabled" type="checkbox" value="1" <?php checked( ! empty( $profile['twoFactor'] ) ); ?> data-profile-2fa-toggle><span><?php esc_html_e( '开启认证器两步验证', 'yneko-reimu' ); ?></span></label>
		<div class="reimu-profile-2fa-setup" data-profile-2fa-setup hidden>
			<button class="reimu-login-code-button" type="button" data-profile-2fa-generate><?php esc_html_e( '生成密钥', 'yneko-reimu' ); ?></button>
			<div class="reimu-profile-2fa-secret" data-profile-2fa-secret></div>
			<img data-profile-2fa-qr alt="" hidden>
			<p>
				<label for="reimu-profile-2fa-code"><?php esc_html_e( '认证器验证码', 'yneko-reimu' ); ?></label>
				<input id="reimu-profile-2fa-code" name="totp_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6">
			</p>
		</div>
	</div>
	<?php
}

function yneko_reimu_profile_modal_actions() {
	?>
	<div class="reimu-login-message" data-profile-message role="status" aria-live="polite"></div>
	<div class="reimu-login-actions">
		<button class="reimu-login-help-link" type="button" data-profile-close><?php esc_html_e( '取消', 'yneko-reimu' ); ?></button>
		<button type="submit" class="reimu-login-submit"><?php esc_html_e( '保存', 'yneko-reimu' ); ?></button>
	</div>
	<?php
}
