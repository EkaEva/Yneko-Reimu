<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_users_panel( $review_badges ) {
	$comment_upload = yneko_reimu_settings_comment_upload();
	$user_badges    = yneko_reimu_settings_user_badges();
	$avatar_frames  = isset( $user_badges['avatar_frames'] ) && is_array( $user_badges['avatar_frames'] ) ? $user_badges['avatar_frames'] : array();
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="users" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户设置', 'User settings' ); ?></h2>
		<table class="form-table" role="presentation">
			<?php yneko_reimu_render_settings_users_basic_row( $comment_upload, $user_badges, $avatar_frames ); ?>
			<?php yneko_reimu_render_settings_users_special_badges_row( $user_badges, $avatar_frames ); ?>
			<?php yneko_reimu_render_settings_users_blocklist_row( $user_badges ); ?>
		</table>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户标签审核', 'User badge review' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['user_badges'] ?? 0 ) ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '这里会列出已有和待审核的用户自定义标签。即使未开启审核，也可以在这里单独撤销某个用户的标签。', 'This lists existing and pending user custom badges. Even when review is disabled, you can revoke individual user badges here.' ); ?>
		<?php yneko_reimu_render_user_badge_admin(); ?>
		<h2><?php yneko_reimu_admin_bilingual_heading( '用户头像管理', 'User avatar manager' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['avatars'] ?? 0 ) ); ?></h2>
		<?php yneko_reimu_render_user_avatar_admin(); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_users_basic_row( $comment_upload, $user_badges, $avatar_frames ) {
	?>
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
	<?php
}

function yneko_reimu_render_settings_users_special_badges_row( $user_badges, $avatar_frames ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '特殊标签及用户头像框管理', 'Special badges and avatar frames' ); ?></th>
		<td>
			<div class="yneko-reimu-special-badge-table">
				<?php foreach ( yneko_reimu_user_badge_base_definitions() as $badge_key => $definition ) : ?>
					<?php yneko_reimu_render_settings_users_special_badge_row( $badge_key, $definition, $user_badges, $avatar_frames ); ?>
				<?php endforeach; ?>
			</div>
			<?php yneko_reimu_admin_bilingual_description( '七种基础特殊标签按“站长 > 管理员 > 编辑 > 作者 > 贡献者 > 会员 > 订阅者”排序。标签原名和当前显示名都会作为保留词。头像框支持 PNG、WebP、AVIF；用户同时拥有多个特殊标签时，按这个顺序使用第一个可用头像框。', 'The seven base special badges are ordered by Owner > Admin > Editor > Author > Contributor > Member > Subscriber. Original and current labels are reserved. Avatar frames support PNG, WebP, and AVIF; when a user has multiple special badges, the first available frame in this order is used.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_users_special_badge_row( $badge_key, $definition, $user_badges, $avatar_frames ) {
	$row       = $user_badges['special'][ $badge_key ] ?? array(
		'enabled' => '1',
		'zh'      => $definition['zh'],
		'en'      => $definition['en'],
	);
	$frame_url = $avatar_frames['frames'][ $badge_key ] ?? yneko_reimu_default_avatar_frame_url();
	?>
	<div class="yneko-reimu-special-badge-row">
		<label><input type="checkbox" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][enabled]" value="1" <?php checked( '1', $row['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( $definition['title_zh'], $definition['title_en'] ); ?></label>
		<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][zh]" value="<?php echo esc_attr( $row['zh'] ); ?>" placeholder="<?php echo esc_attr( $definition['zh'] ); ?>">
		<input type="text" name="yneko_reimu_settings[user_badges][special][<?php echo esc_attr( $badge_key ); ?>][en]" value="<?php echo esc_attr( $row['en'] ); ?>" placeholder="<?php echo esc_attr( $definition['en'] ); ?>">
		<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[user_badges][avatar_frames][frames][' . $badge_key . ']', $frame_url, yneko_reimu_admin_bilingual_text( '选择头像框', 'Choose frame' ), 'image/png,image/webp,image/avif' ); ?>
		<span class="description"><?php echo esc_html( yneko_reimu_admin_prefers_zh() ? $definition['desc_zh'] : $definition['desc_en'] ); ?></span>
	</div>
	<?php
}

function yneko_reimu_render_settings_users_blocklist_row( $user_badges ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '自定义标签屏蔽词', 'Custom badge blocklist' ); ?></th>
		<td>
			<input class="regular-text" type="text" name="yneko_reimu_settings[user_badges][blocklist]" value="<?php echo esc_attr( $user_badges['blocklist'] ?? '' ); ?>" placeholder="<?php esc_attr_e( '广告/官方/测试', 'yneko-reimu' ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '用 / 分隔。保存后，匹配屏蔽词或保留词的旧自定义标签会自动停止显示，用户也不能再次设置。', 'Separate words with /. After saving, old custom badges matching blocked or reserved words stop displaying automatically, and users cannot set them again.' ); ?>
		</td>
	</tr>
	<?php
}
