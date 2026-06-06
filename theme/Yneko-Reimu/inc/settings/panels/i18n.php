<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_i18n_panel( $i18n ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="i18n" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '多语言设置', 'Multilingual settings' ); ?></h2>
		<table class="form-table" role="presentation">
		<?php yneko_reimu_render_settings_i18n_enabled_row( $i18n ); ?>
		<?php yneko_reimu_render_settings_i18n_default_row( $i18n ); ?>
		<?php yneko_reimu_render_settings_i18n_text_row( 'yneko-reimu-en-prefix', '英文路径前缀', 'English URL prefix', 'en_prefix', $i18n, '例如 en 会让英文内容使用 /en/ 开头的地址。', 'For example, en makes English content use URLs starting with /en/.' ); ?>
		<?php yneko_reimu_render_settings_i18n_text_row( 'yneko-reimu-zh-label', '中文显示名', 'Chinese label', 'zh_label', $i18n, '显示在前台语言切换菜单中的中文名称。', 'The Chinese language name shown in the front-end language switcher.' ); ?>
		<?php yneko_reimu_render_settings_i18n_text_row( 'yneko-reimu-en-label', '英文显示名', 'English label', 'en_label', $i18n, '显示在前台语言切换菜单中的英文名称。', 'The English language name shown in the front-end language switcher.' ); ?>
		</table>
	</section>
	<?php
}

function yneko_reimu_render_settings_i18n_enabled_row( $i18n ) {
	?>
	<tr>
		<th scope="row"><?php yneko_reimu_admin_bilingual_label( '启用语言切换', 'Enable language switcher' ); ?></th>
		<td><label><input type="checkbox" name="yneko_reimu_settings[i18n][enabled]" value="1" <?php checked( '1', $i18n['enabled'] ); ?>> <?php yneko_reimu_admin_bilingual_label( '显示前台语言切换入口', 'Show the front-end language switcher' ); ?></label></td>
	</tr>
	<?php
}

function yneko_reimu_render_settings_i18n_default_row( $i18n ) {
	?>
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
	<?php
}

function yneko_reimu_render_settings_i18n_text_row( $id, $label_zh, $label_en, $key, $i18n, $desc_zh, $desc_en ) {
	?>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></label></th>
		<td>
			<input id="<?php echo esc_attr( $id ); ?>" class="regular-text" type="text" name="yneko_reimu_settings[i18n][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $i18n[ $key ] ); ?>">
			<?php yneko_reimu_admin_bilingual_description( $desc_zh, $desc_en ); ?>
		</td>
	</tr>
	<?php
}
