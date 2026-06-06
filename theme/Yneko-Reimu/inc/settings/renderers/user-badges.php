<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
			<?php yneko_reimu_render_user_badge_admin_card( $user ); ?>
		<?php endforeach; ?>
	</div>
	<?php
}

function yneko_reimu_user_badge_admin_context( $user ) {
	$user_id = absint( $user->ID );
	$active  = function_exists( 'yneko_reimu_comment_user_custom_tags' ) ? yneko_reimu_comment_user_custom_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags', true );
	$pending = function_exists( 'yneko_reimu_comment_user_pending_tags' ) ? yneko_reimu_comment_user_pending_tags( $user_id ) : get_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', true );

	return array(
		'user_id' => $user_id,
		'name'    => $user->display_name ? $user->display_name : $user->user_login,
		'email'   => $user->user_email,
		'active'  => is_array( $active ) ? array_values( $active ) : array(),
		'pending' => is_array( $pending ) ? array_values( $pending ) : array(),
	);
}

function yneko_reimu_user_badge_admin_tag_context( $tag ) {
	if ( ! is_array( $tag ) ) {
		return null;
	}

	$label = function_exists( 'yneko_reimu_sanitize_comment_tag_label' ) ? yneko_reimu_sanitize_comment_tag_label( $tag['label'] ?? '' ) : sanitize_text_field( $tag['label'] ?? '' );
	if ( '' === $label ) {
		return null;
	}

	return array(
		'label'     => $label,
		'color'     => sanitize_hex_color( $tag['color'] ?? '' ),
		'enabled'   => '0' !== (string) ( $tag['enabled'] ?? '1' ),
		'old_label' => (string) ( $tag['old_label'] ?? '' ),
	);
}

function yneko_reimu_user_badge_admin_action_url( $action, $user_id, $index ) {
	$index = absint( $index );
	return wp_nonce_url(
		add_query_arg(
			array(
				'yneko_user_badge_action' => $action,
				'user_id'                 => $user_id,
				'tag_index'               => $index,
			)
		),
		'yneko_reimu_user_badge_' . $action . '_' . $user_id . '_' . $index
	);
}

function yneko_reimu_render_user_badge_admin_card( $user ) {
	$context = yneko_reimu_user_badge_admin_context( $user );
	if ( ! $context['active'] && ! $context['pending'] ) {
		return;
	}
	?>
	<div class="yneko-reimu-user-badge-card">
		<div class="yneko-reimu-user-badge-card__user">
			<strong><?php echo esc_html( $context['name'] ); ?></strong>
			<span><?php echo esc_html( $context['email'] ); ?></span>
		</div>
		<div class="yneko-reimu-user-badge-card__tags">
			<?php foreach ( array( 'pending' => $context['pending'], 'active' => $context['active'] ) as $status => $tags ) : ?>
				<?php foreach ( $tags as $index => $tag ) : ?>
					<?php yneko_reimu_render_user_badge_admin_item( $context['user_id'], $status, $index, $tag ); ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

function yneko_reimu_render_user_badge_admin_item( $user_id, $status, $index, $tag ) {
	$tag_context = yneko_reimu_user_badge_admin_tag_context( $tag );
	if ( ! $tag_context ) {
		return;
	}
	?>
	<div class="yneko-reimu-user-badge-item">
		<span class="yneko-reimu-user-badge-pill" style="<?php echo esc_attr( $tag_context['color'] ? '--badge-color:' . $tag_context['color'] . ';' : '' ); ?>"><?php echo esc_html( $tag_context['label'] ); ?></span>
		<span class="description"><?php echo $tag_context['enabled'] ? ( 'pending' === $status ? esc_html__( '待审核', 'yneko-reimu' ) : esc_html__( '已通过', 'yneko-reimu' ) ) : esc_html__( '未启用', 'yneko-reimu' ); ?></span>
		<?php if ( 'pending' === $status && '' !== $tag_context['old_label'] ) : ?>
			<?php
			$old_label_text = sprintf(
				/* translators: %s: previous user badge label. */
				__( '原标签：%s', 'yneko-reimu' ),
				$tag_context['old_label']
			);
			?>
			<span class="description"><?php echo esc_html( $old_label_text ); ?></span>
		<?php endif; ?>
		<?php yneko_reimu_render_user_badge_admin_actions( $user_id, $status, $index ); ?>
	</div>
	<?php
}

function yneko_reimu_render_user_badge_admin_actions( $user_id, $status, $index ) {
	?>
	<div class="yneko-reimu-user-badge-actions">
		<?php if ( 'pending' === $status ) : ?>
			<a class="button button-small" href="<?php echo esc_url( yneko_reimu_user_badge_admin_action_url( 'approve', $user_id, $index ) ); ?>"><?php esc_html_e( '批准', 'yneko-reimu' ); ?></a>
			<a class="button button-small button-link-delete" href="<?php echo esc_url( yneko_reimu_user_badge_admin_action_url( 'reject', $user_id, $index ) ); ?>"><?php esc_html_e( '驳回', 'yneko-reimu' ); ?></a>
		<?php else : ?>
			<a class="button button-small button-link-delete" href="<?php echo esc_url( yneko_reimu_user_badge_admin_action_url( 'revoke', $user_id, $index ) ); ?>"><?php esc_html_e( '撤销', 'yneko-reimu' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}
