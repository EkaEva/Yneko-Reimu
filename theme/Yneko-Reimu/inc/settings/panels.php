<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

function yneko_reimu_render_settings_extensions_panel( $features, $third_party ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="extensions" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '扩展与第三方', 'Extensions and third-party resources' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '这些功能通常会加载额外脚本或连接第三方域名，因此从自定义器移到主设置页集中管理。视觉与布局仍在自定义器中实时预览。', 'These features usually load extra scripts or contact third-party domains, so they are managed here. Visual and layout options remain in the Customizer for live preview.' ); ?>
		<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( '主题扩展', 'Theme extensions' ); ?></th>
			<td>
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
				<?php foreach ( $feature_labels as $key => $label ) : ?>
					<label class="yneko-reimu-checkbox-line"><input type="checkbox" name="yneko_reimu_settings[features][<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( '1', $features[ $key ] ?? '0' ); ?>> <?php yneko_reimu_admin_bilingual_label( $label[0], $label[1] ); ?></label><br>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php yneko_reimu_admin_bilingual_label( 'Live2D Widgets', 'Live2D Widgets' ); ?></th>
			<td><label><input type="checkbox" name="yneko_reimu_settings[third_party][live2d_enable]" value="1" <?php checked( '1', $third_party['live2d_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Live2D Widgets', 'Enable Live2D Widgets' ); ?></label></td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-live2d-base"><?php yneko_reimu_admin_bilingual_label( 'Live2D Widgets 资源地址', 'Live2D Widgets resource URL' ); ?></label></th>
			<td><input id="yneko-reimu-live2d-base" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_base_url]" value="<?php echo esc_attr( $third_party['live2d_base_url'] ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-live2d-api"><?php yneko_reimu_admin_bilingual_label( 'Live2D 模型 CDN 地址', 'Live2D model CDN URL' ); ?></label></th>
			<td><input id="yneko-reimu-live2d-api" class="regular-text" type="url" name="yneko_reimu_settings[third_party][live2d_api_base_url]" value="<?php echo esc_attr( $third_party['live2d_api_base_url'] ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="yneko-reimu-vendor-cdn"><?php yneko_reimu_admin_bilingual_label( 'Vendor CDN 前缀', 'Vendor CDN base' ); ?></label></th>
			<td>
				<input id="yneko-reimu-vendor-cdn" class="regular-text" type="url" name="yneko_reimu_settings[third_party][vendor_cdn_base]" value="<?php echo esc_attr( $third_party['vendor_cdn_base'] ); ?>">
				<?php yneko_reimu_admin_bilingual_description( '用于 Reimu 扩展包的 CDN 前缀。默认使用 jsDelivr，需要隐私优先时可替换为自托管资源。', 'CDN prefix for Reimu extension packages. The default uses jsDelivr; replace it with self-hosted resources for a privacy-first setup.' ); ?>
			</td>
		</tr>
		</table>
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
