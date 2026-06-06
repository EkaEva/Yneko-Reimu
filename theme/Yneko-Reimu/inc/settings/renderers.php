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
			<label><?php yneko_reimu_admin_bilingual_label( '名称', 'Name' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $friend['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '链接', 'URL' ); ?><input type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $friend['url'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '描述', 'Description' ); ?><input type="text" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][desc]" value="<?php echo esc_attr( $friend['desc'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '头像', 'Avatar' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[friends][<?php echo esc_attr( $index ); ?>][image]" value="<?php echo esc_attr( $friend['image'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
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
			<label><?php yneko_reimu_admin_bilingual_label( '歌名', 'Track title' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $track['name'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '作者', 'Artist' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][artist]" value="<?php echo esc_attr( $track['artist'] ); ?>"></label>
			<label><?php yneko_reimu_admin_bilingual_label( '音频', 'Audio' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $track['url'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '封面', 'Cover' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][cover]" value="<?php echo esc_attr( $track['cover'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '歌词 LRC', 'Lyrics LRC' ); ?><span class="yneko-reimu-inline-media"><input class="yneko-reimu-media-url" type="url" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][lrc]" value="<?php echo esc_attr( $track['lrc'] ); ?>"><button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '选择', 'Choose' ) ); ?></button></span></label>
			<label><?php yneko_reimu_admin_bilingual_label( '主题色', 'Theme color' ); ?><input type="text" name="yneko_reimu_settings[music][<?php echo esc_attr( $index ); ?>][theme]" value="<?php echo esc_attr( $track['theme'] ); ?>"></label>
		</div>
		<div class="yneko-reimu-row-actions">
			<button type="button" class="button yneko-reimu-remove-row"><?php echo wp_kses_post( yneko_reimu_admin_bilingual_button_text( '删除', 'Remove' ) ); ?></button>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_comment_upload_admin() {
	if ( ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return;
	}

	$items = array_merge( yneko_reimu_comment_pending_temp_uploads( 80 ), yneko_reimu_comment_upload_library( 80, 'all', true ) );
	if ( ! $items ) {
		echo '<p class="description">' . esc_html__( '暂无评论图片或 GIF 上传。', 'yneko-reimu' ) . '</p>';
		return;
	}
	$groups = yneko_reimu_comment_upload_admin_groups( $items );
	?>
	<?php foreach ( $groups as $group ) : ?>
		<div class="yneko-reimu-upload-admin-section">
			<h3><?php echo esc_html( $group['title'] ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $group['badge'] ?? 0 ) ); ?></h3>
			<?php if ( empty( $group['items'] ) ) : ?>
				<p class="description"><?php esc_html_e( '暂无可选...', 'yneko-reimu' ); ?></p>
				<?php continue; ?>
			<?php endif; ?>
			<div class="yneko-reimu-upload-admin-grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<?php yneko_reimu_render_comment_upload_admin_card( $item ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	<?php
}

function yneko_reimu_comment_upload_admin_groups( $items ) {
	$review_badges = yneko_reimu_admin_review_badge_counts();
	$groups        = array(
		'admin_gif'  => array(
			'title' => __( '后台上传的 GIF', 'yneko-reimu' ),
			'items' => array(),
		),
		'user_gif'   => array(
			'title' => __( '用户评论 GIF', 'yneko-reimu' ),
			'badge' => $review_badges['comment_gifs'] ?? 0,
			'items' => array(),
		),
		'user_image' => array(
			'title' => __( '用户评论图片', 'yneko-reimu' ),
			'badge' => $review_badges['comment_images'] ?? 0,
			'items' => array(),
		),
	);

	foreach ( $items as $item ) {
		if ( 'gif' === $item['type'] && empty( $item['comment_id'] ) ) {
			$groups['admin_gif']['items'][] = $item;
		} elseif ( 'gif' === $item['type'] ) {
			$groups['user_gif']['items'][] = $item;
		} else {
			$groups['user_image']['items'][] = $item;
		}
	}

	return $groups;
}

function yneko_reimu_comment_upload_admin_item_context( $item ) {
	$id      = $item['id'];
	$is_temp = is_string( $id ) && 0 === strpos( $id, 'temp:' );
	$type    = 'gif' === $item['type'] ? 'gif' : 'image';
	$status  = in_array( (string) $item['status'], array( 'approved', 'pending', 'revoked', 'rejected' ), true ) ? (string) $item['status'] : 'pending';

	return array(
		'id'            => $id,
		'is_temp'       => $is_temp,
		'type'          => $type,
		'status'        => $status,
		'user'          => $item['user'] ? get_user_by( 'id', $item['user'] ) : null,
		'label'         => 'gif' === $type ? __( 'GIF', 'yneko-reimu' ) : __( '图片', 'yneko-reimu' ),
		'temp_relative' => $is_temp ? rawurldecode( substr( $id, 5 ) ) : '',
	);
}

function yneko_reimu_comment_upload_admin_status_label( $type, $status ) {
	if ( 'rejected' === $status ) {
		return __( '审核未通过', 'yneko-reimu' );
	}
	if ( 'revoked' === $status ) {
		return __( '已撤销', 'yneko-reimu' );
	}
	if ( 'pending' === $status ) {
		return 'gif' === $type ? __( 'GIF 待审核', 'yneko-reimu' ) : __( '图片待审核', 'yneko-reimu' );
	}

	return 'gif' === $type ? __( 'GIF 已通过', 'yneko-reimu' ) : __( '图片已通过', 'yneko-reimu' );
}

function yneko_reimu_comment_upload_admin_action_url( $action, $context ) {
	if ( $context['is_temp'] ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'yneko_comment_upload_action' => $action,
					'temp_upload'                 => $context['temp_relative'],
				)
			),
			yneko_reimu_comment_temp_upload_nonce_action( $action, $context['temp_relative'] )
		);
	}

	$attachment_id = absint( $context['id'] );

	return wp_nonce_url(
		add_query_arg(
			array(
				'yneko_comment_upload_action' => $action,
				'attachment_id'               => $attachment_id,
			)
		),
		'yneko_reimu_comment_upload_' . $action . '_' . $attachment_id
	);
}

function yneko_reimu_render_comment_upload_admin_card( $item ) {
	$context = yneko_reimu_comment_upload_admin_item_context( $item );
	?>
	<div class="yneko-reimu-upload-admin-card">
		<img src="<?php echo esc_url( $item['url'] ); ?>" alt="">
		<div class="yneko-reimu-upload-admin-meta">
			<strong><?php echo esc_html( yneko_reimu_comment_upload_admin_status_label( $context['type'], $context['status'] ) ); ?></strong>
			<span><?php echo esc_html( $context['label'] ); ?></span>
			<span><?php echo esc_html( $context['user'] ? $context['user']->display_name : __( '未知用户', 'yneko-reimu' ) ); ?></span>
			<span><?php echo esc_html( $item['date'] ); ?></span>
		</div>
		<?php yneko_reimu_render_comment_upload_admin_actions( $context ); ?>
	</div>
	<?php
}

function yneko_reimu_render_comment_upload_admin_actions( $context ) {
	$status = $context['status'];
	?>
	<div class="yneko-reimu-upload-admin-actions">
		<?php if ( 'pending' === $status || 'revoked' === $status ) : ?>
			<a class="button button-small" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'approve', $context ) ); ?>"><?php esc_html_e( '通过', 'yneko-reimu' ); ?></a>
		<?php endif; ?>
		<?php if ( 'pending' === $status ) : ?>
			<a class="button button-small button-link-delete" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'reject', $context ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
		<?php elseif ( 'approved' === $status ) : ?>
			<a class="button button-small" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'revoke', $context ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
		<?php endif; ?>
		<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'delete', $context ) ); ?>"><?php esc_html_e( '删除', 'yneko-reimu' ); ?></a>
	</div>
	<?php
}

function yneko_reimu_render_user_avatar_admin() {
	$users = get_users(
		array(
			'number'     => 120,
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_avatar_url',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_avatar_pending_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	if ( ! $users ) {
		echo '<p class="description">' . esc_html__( '暂无用户上传头像。', 'yneko-reimu' ) . '</p>';
		return;
	}
	?>
	<div class="yneko-reimu-upload-admin-grid yneko-reimu-user-avatar-grid">
		<?php foreach ( $users as $user ) : ?>
			<?php
			$user_id = absint( $user->ID );
			$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
			$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
			$status  = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true );
			$shown   = $pending ? $pending : $current;
			if ( ! $shown ) {
				continue;
			}
			?>
			<div class="yneko-reimu-upload-admin-card">
				<img src="<?php echo esc_url( $shown ); ?>" alt="">
				<div class="yneko-reimu-upload-admin-meta">
					<strong><?php echo esc_html( $user->display_name ? $user->display_name : $user->user_login ); ?></strong>
					<span><?php echo esc_html( $user->user_email ); ?></span>
					<span><?php echo 'pending' === $status ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '已应用头像', 'yneko-reimu' ); ?></span>
				</div>
				<div class="yneko-reimu-upload-admin-actions">
					<?php if ( $pending ) : ?>
						<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'approve', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_approve_' . $user_id ) ); ?>"><?php esc_html_e( '批准头像', 'yneko-reimu' ); ?></a>
						<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'reject', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_reject_' . $user_id ) ); ?>"><?php esc_html_e( '驳回并删除', 'yneko-reimu' ); ?></a>
					<?php endif; ?>
					<?php if ( $current ) : ?>
						<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_avatar_action' => 'delete', 'user_id' => $user_id ) ), 'yneko_reimu_avatar_delete_' . $user_id ) ); ?>"><?php esc_html_e( '删除头像', 'yneko-reimu' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_render_user_badge_admin() {
	$users = get_users(
		array(
			'number'     => 200,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_yneko_reimu_comment_tags',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	if ( ! $users ) {
		echo '<p class="description">' . esc_html__( '暂无用户自定义标签。', 'yneko-reimu' ) . '</p>';
		return;
	}
	?>
	<div class="yneko-reimu-user-badge-admin">
		<?php foreach ( $users as $user ) : ?>
			<?php
			$user_id = absint( $user->ID );
			$active  = function_exists( 'yneko_reimu_comment_user_custom_tags' ) ? yneko_reimu_comment_user_custom_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags', true );
			$pending = function_exists( 'yneko_reimu_comment_user_pending_tags' ) ? yneko_reimu_comment_user_pending_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', true );
			$active  = is_array( $active ) ? array_values( $active ) : array();
			$pending = is_array( $pending ) ? array_values( $pending ) : array();
			if ( ! $active && ! $pending ) {
				continue;
			}
			?>
			<div class="yneko-reimu-user-badge-card">
				<div class="yneko-reimu-user-badge-card__user">
					<strong><?php echo esc_html( $user->display_name ? $user->display_name : $user->user_login ); ?></strong>
					<span><?php echo esc_html( $user->user_email ); ?></span>
				</div>
				<div class="yneko-reimu-user-badge-card__tags">
					<?php foreach ( array( 'pending' => $pending, 'active' => $active ) as $status => $tags ) : ?>
						<?php foreach ( $tags as $index => $tag ) : ?>
							<?php
							if ( ! is_array( $tag ) ) {
								continue;
							}
							$label = function_exists( 'yneko_reimu_sanitize_comment_tag_label' ) ? yneko_reimu_sanitize_comment_tag_label( $tag['label'] ?? '' ) : sanitize_text_field( $tag['label'] ?? '' );
							$color = sanitize_hex_color( $tag['color'] ?? '' );
							if ( '' === $label ) {
								continue;
							}
							?>
							<div class="yneko-reimu-user-badge-item">
								<span class="yneko-reimu-user-badge-pill" style="<?php echo esc_attr( $color ? '--badge-color:' . $color . ';' : '' ); ?>"><?php echo esc_html( $label ); ?></span>
								<span class="description"><?php echo '0' === (string) ( $tag['enabled'] ?? '1' ) ? esc_html__( '未启用', 'yneko-reimu' ) : ( 'pending' === $status ? esc_html__( '待审核', 'yneko-reimu' ) : esc_html__( '已通过', 'yneko-reimu' ) ); ?></span>
								<?php if ( 'pending' === $status && ! empty( $tag['old_label'] ) ) : ?>
									<?php
									$old_label_text = sprintf(
										/* translators: %s: previous user badge label. */
										__( '原标签：%s', 'yneko-reimu' ),
										$tag['old_label']
									);
									?>
									<span class="description"><?php echo esc_html( $old_label_text ); ?></span>
								<?php endif; ?>
								<div class="yneko-reimu-user-badge-actions">
									<?php if ( 'pending' === $status ) : ?>
										<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'approve', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_approve_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '批准', 'yneko-reimu' ); ?></a>
										<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'reject', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_reject_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
									<?php else : ?>
										<a class="button button-small button-link-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'yneko_user_badge_action' => 'revoke', 'user_id' => $user_id, 'tag_index' => absint( $index ) ) ), 'yneko_reimu_user_badge_revoke_' . $user_id . '_' . absint( $index ) ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
