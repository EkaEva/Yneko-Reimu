<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
			<?php yneko_reimu_render_user_avatar_admin_card( $user ); ?>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_user_avatar_admin_context( $user ) {
	$user_id = absint( $user->ID );
	$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
	$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
	$status  = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_status', true );

	return array(
		'user_id' => $user_id,
		'name'    => $user->display_name ? $user->display_name : $user->user_login,
		'email'   => $user->user_email,
		'current' => $current,
		'pending' => $pending,
		'status'  => $status,
		'shown'   => $pending ? $pending : $current,
	);
}

function yneko_reimu_user_avatar_admin_action_url( $action, $user_id ) {
	return wp_nonce_url(
		add_query_arg(
			array(
				'yneko_avatar_action' => $action,
				'user_id'             => $user_id,
			)
		),
		'yneko_reimu_avatar_' . $action . '_' . $user_id
	);
}

function yneko_reimu_render_user_avatar_admin_card( $user ) {
	$context = yneko_reimu_user_avatar_admin_context( $user );
	if ( ! $context['shown'] ) {
		return;
	}
	?>
	<div class="yneko-reimu-upload-admin-card">
		<img src="<?php echo esc_url( $context['shown'] ); ?>" alt="">
		<div class="yneko-reimu-upload-admin-meta">
			<strong><?php echo esc_html( $context['name'] ); ?></strong>
			<span><?php echo esc_html( $context['email'] ); ?></span>
			<span><?php echo 'pending' === $context['status'] ? esc_html__( '头像审核中', 'yneko-reimu' ) : esc_html__( '已应用头像', 'yneko-reimu' ); ?></span>
		</div>
		<?php yneko_reimu_render_user_avatar_admin_actions( $context ); ?>
	</div>
	<?php
}

function yneko_reimu_render_user_avatar_admin_actions( $context ) {
	?>
	<div class="yneko-reimu-upload-admin-actions">
		<?php if ( $context['pending'] ) : ?>
			<a class="button button-small" href="<?php echo esc_url( yneko_reimu_user_avatar_admin_action_url( 'approve', $context['user_id'] ) ); ?>"><?php esc_html_e( '批准头像', 'yneko-reimu' ); ?></a>
			<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( yneko_reimu_user_avatar_admin_action_url( 'reject', $context['user_id'] ) ); ?>"><?php esc_html_e( '驳回并删除', 'yneko-reimu' ); ?></a>
		<?php endif; ?>
		<?php if ( $context['current'] ) : ?>
			<a class="button button-small button-link-delete" data-yneko-upload-delete href="<?php echo esc_url( yneko_reimu_user_avatar_admin_action_url( 'delete', $context['user_id'] ) ); ?>"><?php esc_html_e( '删除头像', 'yneko-reimu' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}
