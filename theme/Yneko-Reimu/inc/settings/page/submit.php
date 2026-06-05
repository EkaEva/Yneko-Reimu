<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_floating_submit() {
	?>
	<div class="yneko-reimu-floating-submit">
		<span class="yneko-reimu-floating-submit__hint"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_text( '切换标签页不会保存修改。', 'Switching tabs does not save changes.' ) ); ?></span>
		<button type="submit" class="button button-primary yneko-reimu-submit-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '保存设置', 'Save settings' ) ); ?></button>
	</div>
	<?php
}

function yneko_reimu_render_settings_hidden_upload_form() {
	?>
	<form id="yneko-reimu-admin-gif-upload-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'themes.php?page=yneko-reimu-settings#comments' ) ); ?>">
		<?php wp_nonce_field( 'yneko_reimu_admin_comment_gif_upload' ); ?>
		<input type="hidden" name="yneko_reimu_admin_comment_gif_upload" value="1">
	</form>
	<?php
}
