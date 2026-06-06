<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_admin_comment_gif_upload() {
	$status = isset( $_GET['yneko_comment_gif_upload'] ) ? sanitize_key( wp_unslash( $_GET['yneko_comment_gif_upload'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$count  = isset( $_GET['yneko_comment_gif_count'] ) ? absint( $_GET['yneko_comment_gif_count'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $status ) {
		$messages = array(
			'success' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF 并加入表情库。', '已上传 %d 个 GIF 并加入表情库。', $count, 'yneko-reimu' ),
				$count
			) : __( 'GIF 已上传并加入表情库。', 'yneko-reimu' ),
			'partial' => $count ? sprintf(
				/* translators: %d: uploaded GIF count. */
				_n( '已上传 %d 个 GIF，部分文件未成功。', '已上传 %d 个 GIF，部分文件未成功。', $count, 'yneko-reimu' ),
				$count
			) : __( '部分 GIF 上传失败。', 'yneko-reimu' ),
			'empty'   => __( '请选择要上传的 GIF。', 'yneko-reimu' ),
			'invalid' => __( '仅支持未超出大小限制的 GIF 文件。', 'yneko-reimu' ),
			'failed'  => __( 'GIF 上传失败。', 'yneko-reimu' ),
		);
		$class = in_array( $status, array( 'success', 'partial' ), true ) ? 'notice notice-success inline' : 'notice notice-error inline';
		echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $messages[ $status ] ?? $messages['failed'] ) . '</p></div>';
	}
	?>
	<div class="yneko-reimu-admin-gif-upload">
		<input id="yneko-reimu-admin-gif-file" form="yneko-reimu-admin-gif-upload-form" type="file" name="yneko_reimu_comment_gif[]" accept="image/gif" multiple hidden>
		<button type="button" class="button button-primary yneko-reimu-admin-gif-pick" data-yneko-admin-gif-pick><?php yneko_reimu_admin_bilingual_label( '上传本地 GIF 并入库', 'Upload local GIFs' ); ?></button>
		<button type="button" class="button yneko-reimu-admin-gif-media" data-yneko-admin-gif-media data-nonce="<?php echo esc_attr( wp_create_nonce( 'yneko_reimu_admin_add_gif_media' ) ); ?>"><?php yneko_reimu_admin_bilingual_label( '从媒体库加入 GIF', 'Add GIF from Media Library' ); ?></button>
	</div>
	<?php
}
