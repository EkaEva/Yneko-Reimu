<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	yneko_reimu_render_comment_upload_admin_groups( yneko_reimu_comment_upload_admin_groups( $items ) );
}

function yneko_reimu_render_comment_upload_admin_groups( $groups ) {
	foreach ( $groups as $group ) {
		?>
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
		<?php
	}
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
		<?php yneko_reimu_render_comment_upload_admin_status_actions( $context, $status ); ?>
		<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'delete', $context ) ); ?>"><?php esc_html_e( '删除', 'yneko-reimu' ); ?></a>
	</div>
	<?php
}

function yneko_reimu_render_comment_upload_admin_status_actions( $context, $status ) {
	if ( 'pending' === $status || 'revoked' === $status ) {
		?>
		<a class="button button-small" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'approve', $context ) ); ?>"><?php esc_html_e( '通过', 'yneko-reimu' ); ?></a>
		<?php
	}
	if ( 'pending' === $status ) {
		?>
		<a class="button button-small button-link-delete" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'reject', $context ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
		<?php
	} elseif ( 'approved' === $status ) {
		?>
		<a class="button button-small" href="<?php echo esc_url( yneko_reimu_comment_upload_admin_action_url( 'revoke', $context ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
		<?php
	}
}
