<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_external_comments_panel( $external_comments ) {
	?>
	<section class="yneko-reimu-settings-panel" data-yneko-settings-panel="external-comments" hidden>
		<h2><?php yneko_reimu_admin_bilingual_heading( '外部评论', 'External comments' ); ?></h2>
		<?php yneko_reimu_admin_bilingual_description( 'WordPress 评论始终可用；第三方评论未启用或未填配置时不会加载。', 'WordPress comments are always available. Third-party comments load only when enabled and configured.' ); ?>
		<table class="form-table" role="presentation">
		<?php foreach ( yneko_reimu_settings_external_comment_fields() as $prefix => $meta ) : ?>
			<?php yneko_reimu_render_settings_external_comment_row( $prefix, $meta, $external_comments ); ?>
		<?php endforeach; ?>
		</table>
	</section>
	<?php
}

function yneko_reimu_settings_external_comment_fields() {
	return array(
		'giscus'     => array( 'Giscus', array( 'repo', 'repo_id', 'category', 'category_id' ) ),
		'utterances' => array( 'Utterances', array( 'repo' ) ),
		'disqus'     => array( 'Disqus', array( 'shortname' ) ),
		'waline'     => array( 'Waline', array( 'server_url' ) ),
		'twikoo'     => array( 'Twikoo', array( 'env_id' ) ),
		'valine'     => array( 'Valine', array( 'app_id', 'app_key', 'server_url' ) ),
	);
}

function yneko_reimu_render_settings_external_comment_row( $prefix, $meta, $external_comments ) {
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $meta[0] ); ?></th>
		<td>
			<label><input type="checkbox" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix ); ?>_enable]" value="1" <?php checked( '1', $external_comments[ $prefix . '_enable' ] ?? '0' ); ?>> <?php echo esc_html( $meta[0] ); ?></label>
			<?php foreach ( $meta[1] as $field ) : ?>
				<p><label><?php echo esc_html( $meta[0] . ' ' . $field ); ?><br><input class="regular-text" type="text" name="yneko_reimu_settings[external_comments][<?php echo esc_attr( $prefix . '_' . $field ); ?>]" value="<?php echo esc_attr( $external_comments[ $prefix . '_' . $field ] ?? '' ); ?>"></label></p>
			<?php endforeach; ?>
		</td>
	</tr>
	<?php
}
