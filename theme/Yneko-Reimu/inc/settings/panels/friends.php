<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_friends_panel( $settings ) {
	$site_friend = yneko_reimu_sanitize_site_friend_info( $settings['friend_site'] ?? array() );
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="friends" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '友链设置', 'Friend link settings' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '用于友链页面的卡片列表，支持名称、链接、描述和头像。', 'Cards shown on the friend-links page. Each item supports a name, URL, description, and avatar.' ); ?>
		<?php yneko_reimu_render_settings_friend_site_table( $site_friend ); ?>
		<?php yneko_reimu_render_settings_friend_repeatable( $settings ); ?>
	</section>
	<?php
}

function yneko_reimu_render_settings_friend_site_table( $site_friend ) {
	?>
	<h3><?php yneko_reimu_admin_bilingual_heading( '本站友链信息', 'Site friend-link info' ); ?></h3>
	<?php yneko_reimu_admin_bilingual_description( '用于友链页“本站信息”代码块。未配置图片时，将依次使用站点头像、作者头像和主题内置头像。', 'Used by the Site info code block on the friend-links page. When image is empty, the site avatar, author avatar, and bundled theme avatar are used in order.' ); ?>
	<table class="form-table yneko-reimu-site-friend-table" role="presentation">
		<?php yneko_reimu_render_settings_friend_site_row( '名称', 'Name', 'text', 'name', $site_friend ); ?>
		<?php yneko_reimu_render_settings_friend_site_row( '链接', 'URL', 'url', 'url', $site_friend ); ?>
		<?php yneko_reimu_render_settings_friend_site_row( '描述', 'Description', 'text', 'desc', $site_friend ); ?>
		<?php yneko_reimu_render_settings_friend_site_image_row( $site_friend ); ?>
	</table>
	<?php
}

function yneko_reimu_render_settings_friend_site_row( $label_zh, $label_en, $type, $key, $site_friend ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></th>
		<td><input type="<?php echo esc_attr( $type ); ?>" class="regular-text" name="yneko_reimu_settings[friend_site][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $site_friend[ $key ] ); ?>"></td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_friend_site_image_row( $site_friend ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Image', 'Image' ); ?></th>
		<td>
			<?php yneko_reimu_admin_media_field( 'yneko_reimu_settings[friend_site][image]', $site_friend['image'], yneko_reimu_admin_bilingual_text( '选择图片', 'Choose image' ), 'image/png,image/webp' ); ?>
			<?php yneko_reimu_admin_bilingual_description( '仅建议使用 WebP 或 PNG，推荐正方形 512×512，体积控制在 200KB 以内。', 'Use WebP or PNG. A square 512x512 image under 200KB is recommended.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_friend_repeatable( $settings ) {
	?>
	<div class="yneko-reimu-repeatable" data-repeatable="friends">
		<div class="yneko-reimu-repeatable-list">
			<?php foreach ( yneko_reimu_sanitize_friend_items( $settings['friends'] ) as $index => $friend ) : ?>
				<?php yneko_reimu_render_friend_row( $index, $friend ); ?>
			<?php endforeach; ?>
		</div>
		<button type="button" class="button yneko-reimu-add-row" data-template="friend"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '新增友链', 'Add friend' ) ); ?></button>
	</div>
	<?php
}
