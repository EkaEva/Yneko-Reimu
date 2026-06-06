<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_friend_row( $index, $friend = array() ) {
	$friend = wp_parse_args(
		$friend,
		array(
			'name'  => '',
			'url'   => '',
			'desc'  => '',
			'image' => '',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-heading" data-row-label="friend">
			<span class="yneko-reimu-row-number"></span>
		</div>
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-friend">
			<?php yneko_reimu_render_repeatable_input( 'friends', $index, 'name', 'text', '名称', 'Name', $friend['name'] ); ?>
			<?php yneko_reimu_render_repeatable_input( 'friends', $index, 'url', 'url', '链接', 'URL', $friend['url'] ); ?>
			<?php yneko_reimu_render_repeatable_input( 'friends', $index, 'desc', 'text', '描述', 'Description', $friend['desc'] ); ?>
			<?php yneko_reimu_render_repeatable_media_input( 'friends', $index, 'image', '头像', 'Avatar', $friend['image'] ); ?>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_music_row( $index, $track = array() ) {
	$track = wp_parse_args(
		$track,
		array(
			'name'   => '',
			'artist' => '',
			'url'    => '',
			'cover'  => '',
			'lrc'    => '',
			'theme'  => '#ff5252',
		)
	);
	?>
	<div class="yneko-reimu-repeatable-row">
		<div class="yneko-reimu-row-heading" data-row-label="music">
			<span class="yneko-reimu-row-number"></span>
		</div>
		<div class="yneko-reimu-row-grid yneko-reimu-row-grid-music">
			<?php yneko_reimu_render_repeatable_input( 'music', $index, 'name', 'text', '歌名', 'Track title', $track['name'] ); ?>
			<?php yneko_reimu_render_repeatable_input( 'music', $index, 'artist', 'text', '作者', 'Artist', $track['artist'] ); ?>
			<?php yneko_reimu_render_repeatable_media_input( 'music', $index, 'url', '音频', 'Audio', $track['url'] ); ?>
			<?php yneko_reimu_render_repeatable_media_input( 'music', $index, 'cover', '封面', 'Cover', $track['cover'] ); ?>
			<?php yneko_reimu_render_repeatable_media_input( 'music', $index, 'lrc', '歌词 LRC', 'Lyrics LRC', $track['lrc'] ); ?>
			<?php yneko_reimu_render_repeatable_input( 'music', $index, 'theme', 'text', '主题色', 'Theme color', $track['theme'] ); ?>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_repeatable_input( $group, $index, $key, $type, $label_zh, $label_en, $value ) {
	?>
	<label><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?><input type="<?php echo esc_attr( $type ); ?>" name="yneko_reimu_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>"></label>
	<?php
}

function yneko_reimu_render_repeatable_media_input( $group, $index, $key, $label_zh, $label_en, $value ) {
	?>
	<label><?php yneko_reimu_admin_bilingual_label( $label_zh, $label_en ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[<?php echo esc_attr( $group ); ?>][<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
	<?php
}
