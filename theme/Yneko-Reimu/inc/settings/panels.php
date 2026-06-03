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
				<label><?php yneko_reimu_admin_bilingual_label( '循环模式', 'Loop' ); ?>
					<select name="yneko_reimu_settings[player][loop]">
						<option value="all" <?php selected( $player['loop'], 'all' ); ?>>all</option>
						<option value="one" <?php selected( $player['loop'], 'one' ); ?>>one</option>
						<option value="none" <?php selected( $player['loop'], 'none' ); ?>>none</option>
					</select>
				</label>
				&nbsp;
				<label><?php yneko_reimu_admin_bilingual_label( '播放顺序', 'Order' ); ?>
					<select name="yneko_reimu_settings[player][order]">
						<option value="list" <?php selected( $player['order'], 'list' ); ?>>list</option>
						<option value="random" <?php selected( $player['order'], 'random' ); ?>>random</option>
					</select>
				</label>
				&nbsp;
				<label><?php yneko_reimu_admin_bilingual_label( '预加载', 'Preload' ); ?>
					<select name="yneko_reimu_settings[player][preload]">
						<option value="metadata" <?php selected( $player['preload'], 'metadata' ); ?>>metadata</option>
						<option value="none" <?php selected( $player['preload'], 'none' ); ?>>none</option>
						<option value="auto" <?php selected( $player['preload'], 'auto' ); ?>>auto</option>
					</select>
				</label>
				<p>
					<label><?php yneko_reimu_admin_bilingual_label( '默认音量 0-1', 'Volume 0-1' ); ?> <input class="small-text" type="number" min="0" max="1" step="0.1" name="yneko_reimu_settings[player][volume]" value="<?php echo esc_attr( $player['volume'] ); ?>"></label>
					&nbsp;
					<label><?php yneko_reimu_admin_bilingual_label( '歌词模式', 'LRC type' ); ?> <input class="small-text" type="number" min="0" max="3" step="1" name="yneko_reimu_settings[player][lrc_type]" value="<?php echo esc_attr( absint( $player['lrc_type'] ) ); ?>"></label>
					&nbsp;
					<label><?php yneko_reimu_admin_bilingual_label( '列表最大高度', 'List max height' ); ?> <input type="text" name="yneko_reimu_settings[player][list_max_height]" value="<?php echo esc_attr( $player['list_max_height'] ); ?>" placeholder="320px"></label>
				</p>
				<?php yneko_reimu_admin_bilingual_description( '预加载默认 metadata，避免首屏过早下载完整音频。隐私/性能优先时可选 none。', 'Preload defaults to metadata to avoid downloading full audio during first paint. Use none for a privacy/performance-first setup.' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Meting 配置', 'Meting configuration' ); ?></th>
			<td>
				<p><label>Meting auto URL<br><input class="regular-text" type="url" name="yneko_reimu_settings[player][meting_auto]" value="<?php echo esc_attr( $player['meting_auto'] ); ?>"></label></p>
				<p>
					<label>Meting ID <input type="text" name="yneko_reimu_settings[player][meting_id]" value="<?php echo esc_attr( $player['meting_id'] ); ?>"></label>
					&nbsp;
					<label>server <input type="text" name="yneko_reimu_settings[player][meting_server]" value="<?php echo esc_attr( $player['meting_server'] ); ?>" placeholder="netease"></label>
					&nbsp;
					<label>type <input type="text" name="yneko_reimu_settings[player][meting_type]" value="<?php echo esc_attr( $player['meting_type'] ); ?>" placeholder="playlist"></label>
				</p>
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
