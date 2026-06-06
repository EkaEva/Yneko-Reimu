<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_settings_group_open( $title_zh, $title_en, $desc_zh = '', $desc_en = '' ) {
	?>
	<div class="yneko-reimu-settings-group">
		<div class="yneko-reimu-settings-group__header">
			<h3><?php yneko_reimu_admin_bilingual_heading( $title_zh, $title_en ); ?></h3>
			<?php if ( '' !== $desc_zh || '' !== $desc_en ) : ?>
				<?php yneko_reimu_admin_bilingual_description( $desc_zh, $desc_en ); ?>
			<?php endif; ?>
		</div>
		<div class="yneko-reimu-settings-group__body">
	<?php
}

function yneko_reimu_settings_group_close() {
	?>
		</div>
	</div>
	<?php
}

function yneko_reimu_settings_field_open( $label_zh, $label_en, $for = '' ) {
	?>
	<div class="yneko-reimu-field">
		<?php if ( $for ) : ?>
			<label class="yneko-reimu-field__label" for="<?php echo esc_attr( $for ); ?>"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></label>
		<?php else : ?>
			<div class="yneko-reimu-field__label"><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?></div>
		<?php endif; ?>
	<?php
}

function yneko_reimu_settings_field_close() {
	?>
	</div>
	<?php
}
