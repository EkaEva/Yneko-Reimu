<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_search_panel( $search ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="search" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '搜索设置', 'Search settings' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( '搜索不依赖实时预览，因此统一在这里管理。优先级：Algolia 配置完整时优先；否则使用本地 JSON；再否则回退 WordPress REST。', 'Search does not need live preview, so it is managed here. Priority: Algolia when fully configured, then local JSON, then WordPress REST.' ); ?>
		<table class="form-table" role="presentation">
		<?php yneko_reimu_render_settings_search_provider_row( $search ); ?>
		<?php yneko_reimu_render_settings_search_text_row( 'yneko-reimu-algolia-app-id', 'Algolia App ID', 'algolia_app_id', $search ); ?>
		<?php yneko_reimu_render_settings_search_api_key_row( $search ); ?>
		<?php yneko_reimu_render_settings_search_text_row( 'yneko-reimu-algolia-index-name', 'Algolia Index Name', 'algolia_index_name', $search ); ?>
		<?php yneko_reimu_render_settings_search_local_json_row( $search ); ?>
		<?php yneko_reimu_render_settings_search_index_row( $search ); ?>
		</table>
	</section>
	<?php
}

function yneko_reimu_render_settings_search_provider_row( $search ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索入口', 'Search providers' ); ?></th>
		<td>
			<label><input type="checkbox" name="yneko_reimu_settings[search][algolia_enable]" value="1" <?php checked( '1', $search['algolia_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用 Algolia 搜索入口', 'Enable Algolia search' ); ?></label><br>
			<label><input type="checkbox" name="yneko_reimu_settings[search][local_enable]" value="1" <?php checked( '1', $search['local_enable'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '启用本地搜索入口', 'Enable local search' ); ?></label>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_search_text_row( $id, $label, $key, $search ) {
	?>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td><input id="<?php echo esc_attr( $id ); ?>" class="regular-text" type="text" name="yneko_reimu_settings[search][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $search[ $key ] ); ?>"></td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_search_api_key_row( $search ) {
	?>
	<tr>
		<th scope="row"><label for="yneko-reimu-algolia-api-key">Algolia Search API Key</label></th>
		<td>
			<input id="yneko-reimu-algolia-api-key" class="regular-text" type="text" name="yneko_reimu_settings[search][algolia_api_key]" value="<?php echo esc_attr( $search['algolia_api_key'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '填写 Search-Only API Key，不要填写 Admin API Key。', 'Enter the Search-Only API Key, not an Admin API Key.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_search_local_json_row( $search ) {
	?>
	<tr>
		<th scope="row"><label for="yneko-reimu-local-json"><?php yneko_reimu_admin_bilingual_label( '本地搜索 JSON URL', 'Local search JSON URL' ); ?></label></th>
		<td>
			<input id="yneko-reimu-local-json" class="regular-text" type="url" name="yneko_reimu_settings[search][local_json_url]" value="<?php echo esc_attr( $search['local_json_url'] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( '留空时使用主题自动生成的 /search.json。', 'Leave empty to use the theme-generated /search.json.' ); ?>
		</td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_search_index_row( $search ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '搜索索引内容', 'Search index content' ); ?></th>
		<td>
			<label><input type="checkbox" name="yneko_reimu_settings[search][index_full_content]" value="1" <?php checked( '1', $search['index_full_content'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '搜索索引包含全文', 'Include full content in search index' ); ?></label>
			<?php yneko_reimu_admin_bilingual_description( '默认关闭，仅输出标题、摘要、分类、标签和 URL。开启后 /search.json 会公开文章纯文本全文。', 'Disabled by default; only title, excerpt, categories, tags, and URL are output. When enabled, /search.json exposes plain-text post content.' ); ?>
		</td>
	</tr>
	<?php
}
