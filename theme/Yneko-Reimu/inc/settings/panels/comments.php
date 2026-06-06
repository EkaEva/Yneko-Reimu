<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
					<?php yneko_reimu_render_settings_comment_upload_grid( $comment_upload ); ?>
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

function yneko_reimu_render_settings_comment_upload_grid( $comment_upload ) {
	?>
	<div class="yneko-reimu-settings-subgrid yneko-reimu-settings-subgrid-3">
		<?php yneko_reimu_render_settings_comment_upload_group( '图片上传', 'Image uploads', $comment_upload, 'image', 20 ); ?>
		<?php yneko_reimu_render_settings_comment_upload_group( 'GIF 上传', 'GIF uploads', $comment_upload, 'gif', 30 ); ?>
		<?php yneko_reimu_render_settings_comment_cleanup_group( $comment_upload ); ?>
	</div>
	<?php
}

function yneko_reimu_settings_comment_upload_labels( $type ) {
	if ( 'gif' === $type ) {
		return array(
			'enabled_zh' => '允许登录用户上传 GIF',
			'enabled_en' => 'Allow logged-in users to upload GIFs',
			'review_zh'  => 'GIF 人工审核',
			'review_en'  => 'Review uploaded GIFs',
			'max_zh'     => 'GIF 上限 MB',
			'max_en'     => 'GIF max MB',
		);
	}

	return array(
		'enabled_zh' => '允许登录用户上传图片',
		'enabled_en' => 'Allow logged-in users to upload images',
		'review_zh'  => '图片人工审核',
		'review_en'  => 'Review uploaded images',
		'max_zh'     => '图片上限 MB',
		'max_en'     => 'Image max MB',
	);
}

function yneko_reimu_render_settings_comment_upload_group( $title_zh, $title_en, $comment_upload, $type, $max ) {
	$labels = yneko_reimu_settings_comment_upload_labels( $type );
	yneko_reimu_settings_field_open( $title_zh, $title_en );
	?>
		<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][<?php echo esc_attr( $type ); ?>_enabled]" value="1" <?php checked( '1', $comment_upload[ $type . '_enabled' ] ); ?>> <?php yneko_reimu_admin_bilingual_label( $labels['enabled_zh'], $labels['enabled_en'] ); ?></label>
		<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[comment_upload][<?php echo esc_attr( $type ); ?>_review]" value="1" <?php checked( '1', $comment_upload[ $type . '_review' ] ); ?>> <?php yneko_reimu_admin_bilingual_label( $labels['review_zh'], $labels['review_en'] ); ?></label>
		<label><?php yneko_reimu_admin_bilingual_label( $labels['max_zh'], $labels['max_en'] ); ?> <input class="small-text" type="number" min="1" max="<?php echo esc_attr( $max ); ?>" name="yneko_reimu_settings[comment_upload][<?php echo esc_attr( $type ); ?>_max_mb]" value="<?php echo esc_attr( absint( $comment_upload[ $type . '_max_mb' ] ) ); ?>"></label>
	<?php
	yneko_reimu_settings_field_close();
}

function yneko_reimu_render_settings_comment_cleanup_group( $comment_upload ) {
	yneko_reimu_settings_field_open( '清理规则', 'Cleanup rules' );
	?>
		<label><?php yneko_reimu_admin_bilingual_label( '临时文件清理天数', 'Temporary cleanup days' ); ?> <input class="small-text" type="number" min="1" max="30" name="yneko_reimu_settings[comment_upload][temp_cleanup_days]" value="<?php echo esc_attr( absint( $comment_upload['temp_cleanup_days'] ) ); ?>"></label>
		<label><?php yneko_reimu_admin_bilingual_label( '驳回后清理小时', 'Rejected cleanup hours' ); ?> <input class="small-text" type="number" min="1" max="168" name="yneko_reimu_settings[comment_upload][rejected_cleanup_hours]" value="<?php echo esc_attr( absint( $comment_upload['rejected_cleanup_hours'] ?? 24 ) ); ?>"></label>
	<?php
	yneko_reimu_settings_field_close();
}
