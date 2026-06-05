<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_normalize_user_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	if ( ! preg_match( '#^[a-z][a-z0-9+.-]*://#i', $url ) && preg_match( '#^[^\s/@]+\.[^\s]+#', $url ) ) {
		$url = 'https://' . $url;
	}

	return esc_url_raw( $url );
}

function yneko_reimu_user_avatar_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$custom = get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
	return $custom ? esc_url_raw( $custom ) : '';
}

function yneko_reimu_user_profile_avatar_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$custom = yneko_reimu_user_avatar_url( $user_id );
	if ( $custom ) {
		return $custom;
	}

	foreach ( array( '_yneko_reimu_github_avatar_url', '_yneko_github_avatar_url' ) as $meta_key ) {
		$avatar = get_user_meta( $user_id, $meta_key, true );
		if ( $avatar ) {
			return esc_url_raw( $avatar );
		}
	}

	return '';
}

function yneko_reimu_user_review_status_meta_key( $type ) {
	$type = sanitize_key( $type );
	if ( ! in_array( $type, array( 'avatar', 'tags', 'comments' ), true ) ) {
		return '';
	}

	return 'avatar' === $type ? '_yneko_reimu_avatar_status' : '_yneko_reimu_' . $type . '_status';
}

function yneko_reimu_set_user_review_status( $user_id, $type, $status, $comment_id = 0 ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	$status  = sanitize_key( $status );
	if ( ! $user_id || ! $key || ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
		return;
	}

	update_user_meta( $user_id, $key, $status );
	update_user_meta( $user_id, $key . '_time', time() );
	if ( $comment_id ) {
		update_user_meta( $user_id, $key . '_comment_id', absint( $comment_id ) );
	}
}

function yneko_reimu_increment_user_review_status_count( $user_id, $type, $status, $comment_id = 0 ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	$status  = sanitize_key( $status );
	if ( ! $user_id || ! $key || ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
		return;
	}

	$current_status = (string) get_user_meta( $user_id, $key, true );
	$count_key      = $key . '_count';
	$count          = absint( get_user_meta( $user_id, $count_key, true ) );
	update_user_meta( $user_id, $key, $status );
	update_user_meta( $user_id, $key . '_time', time() );
	update_user_meta( $user_id, $count_key, $status === $current_status ? max( 1, $count + 1 ) : 1 );
	if ( $comment_id ) {
		update_user_meta( $user_id, $key . '_comment_id', absint( $comment_id ) );
	}
}

function yneko_reimu_clear_user_review_status( $user_id, $type ) {
	$user_id = absint( $user_id );
	$key     = yneko_reimu_user_review_status_meta_key( $type );
	if ( ! $user_id || ! $key ) {
		return;
	}

	delete_user_meta( $user_id, $key );
	delete_user_meta( $user_id, $key . '_time' );
	delete_user_meta( $user_id, $key . '_comment_id' );
	delete_user_meta( $user_id, $key . '_count' );
}

function yneko_reimu_user_pending_comment_review_count( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return 0;
	}

	$comment_ids = array();
	foreach ( get_comments( array( 'user_id' => $user_id, 'status' => 'hold', 'fields' => 'ids', 'number' => 300 ) ) as $comment_id ) {
		$comment_ids[ absint( $comment_id ) ] = true;
	}
	foreach ( yneko_reimu_comment_upload_library( 300, 'all', true ) as $item ) {
		if ( absint( $item['user'] ?? 0 ) !== $user_id ) {
			continue;
		}
		if ( in_array( (string) ( $item['status'] ?? '' ), array( 'pending', 'revoked' ), true ) ) {
			$comment_id = absint( $item['comment_id'] ?? 0 );
			if ( $comment_id ) {
				$comment_ids[ $comment_id ] = true;
			}
		}
	}
	foreach ( yneko_reimu_comment_pending_temp_uploads( 300 ) as $item ) {
		if ( absint( $item['user'] ?? 0 ) === $user_id ) {
			$comment_id = absint( $item['comment_id'] ?? 0 );
			if ( $comment_id ) {
				$comment_ids[ $comment_id ] = true;
			}
		}
	}
	return count( $comment_ids );
}

function yneko_reimu_user_review_status_payload( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$payload = array();
	foreach ( array( 'avatar', 'tags', 'comments' ) as $type ) {
		$key    = yneko_reimu_user_review_status_meta_key( $type );
		$status = $key ? (string) get_user_meta( $user_id, $key, true ) : '';
		if ( ! in_array( $status, array( 'pending', 'updated', 'rejected' ), true ) ) {
			continue;
		}
		$payload[ $type ] = array(
			'status'    => $status,
			'timestamp' => absint( get_user_meta( $user_id, $key . '_time', true ) ),
			'commentId' => absint( get_user_meta( $user_id, $key . '_comment_id', true ) ),
			'count'     => absint( get_user_meta( $user_id, $key . '_count', true ) ),
		);
		if ( 'pending' === $status && 'tags' === $type ) {
			$payload[ $type ]['count'] = count( yneko_reimu_comment_user_pending_tags( $user_id ) );
		} elseif ( 'pending' === $status && 'comments' === $type ) {
			$payload[ $type ]['count'] = max( 1, yneko_reimu_user_pending_comment_review_count( $user_id ) );
		} elseif ( empty( $payload[ $type ]['count'] ) ) {
			$payload[ $type ]['count'] = 1;
		}
	}

	return $payload;
}

function yneko_reimu_user_review_primary_status_html( $user_id ) {
	$statuses = yneko_reimu_user_review_status_payload( $user_id );
	$priority = array( 'avatar', 'tags', 'comments' );
	$html     = '';
	foreach ( $priority as $type ) {
		if ( empty( $statuses[ $type ]['status'] ) ) {
			continue;
		}
		$status = (string) $statuses[ $type ]['status'];
		$label  = '';
		$class  = 'reimu-comment-current-user__status';
		if ( 'avatar' === $type ) {
			$label = 'pending' === $status ? __( '头像审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '头像审核不通过', 'yneko-reimu' ) : __( '头像已更新', 'yneko-reimu' ) );
		} elseif ( 'tags' === $type ) {
			$label = 'pending' === $status ? __( '标签审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '标签审核不通过', 'yneko-reimu' ) : __( '标签已更新', 'yneko-reimu' ) );
		} else {
			$label = 'pending' === $status ? __( '评论审核中', 'yneko-reimu' ) : ( 'rejected' === $status ? __( '评论审核不通过', 'yneko-reimu' ) : __( '评论已更新', 'yneko-reimu' ) );
		}
		if ( 'rejected' === $status ) {
			$class .= ' is-error';
		} elseif ( 'updated' === $status ) {
			$class .= ' is-success';
		} else {
			$class .= ' is-pending';
		}
		$count = absint( $statuses[ $type ]['count'] ?? 0 );
		$html .= '<span class="' . esc_attr( $class ) . '" data-profile-inline-status data-profile-status-kind="' . esc_attr( $type ) . '" data-profile-status-state="' . esc_attr( $status ) . '">' . esc_html( $label );
		if ( 'pending' === $status && $count > 1 && in_array( $type, array( 'tags', 'comments' ), true ) ) {
			$html .= '<b class="reimu-comment-current-user__status-count">' . esc_html( (string) $count ) . '</b>';
		}
		$html .= '</span>';
	}

	return $html ? '<span class="reimu-comment-current-user__statuses" data-profile-inline-status-list>' . $html . '</span>' : '';
}

function yneko_reimu_handle_profile_avatar_upload( $user_id, $file ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return new WP_Error( 'invalid_user', __( '请先登录。', 'yneko-reimu' ) );
	}
	if ( ! yneko_reimu_avatar_upload_enabled() ) {
		return new WP_Error( 'avatar_upload_disabled', __( '当前未开启头像上传。', 'yneko-reimu' ) );
	}
	if ( empty( $file['name'] ) ) {
		return new WP_Error( 'avatar_file_missing', __( '请选择头像文件。', 'yneko-reimu' ) );
	}
	if ( ! empty( $file['size'] ) && absint( $file['size'] ) > yneko_reimu_avatar_upload_limit() ) {
		return new WP_Error( 'avatar_too_large', __( '头像文件超过大小限制。', 'yneko-reimu' ) );
	}

	$allowed_mimes = array( 'image/jpeg', 'image/png', 'image/webp' );
	$file_type     = wp_check_filetype_and_ext( $file['tmp_name'] ?? '', $file['name'] ?? '' );
	$mime_type     = isset( $file_type['type'] ) ? (string) $file_type['type'] : '';
	if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
		return new WP_Error( 'avatar_invalid_type', __( '头像仅支持 JPG、PNG 或 WebP。', 'yneko-reimu' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( yneko_reimu_avatar_review_enabled() ) {
		$GLOBALS['yneko_reimu_avatar_upload_pending'] = true;
	}
	add_filter( 'upload_dir', 'yneko_reimu_avatar_upload_dir' );
	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => array(
				'jpg|jpeg' => 'image/jpeg',
				'png'      => 'image/png',
				'webp'     => 'image/webp',
			),
		)
	);
	remove_filter( 'upload_dir', 'yneko_reimu_avatar_upload_dir' );
	unset( $GLOBALS['yneko_reimu_avatar_upload_pending'] );

	if ( empty( $upload['url'] ) || ! empty( $upload['error'] ) ) {
		return new WP_Error( 'avatar_upload_failed', ! empty( $upload['error'] ) ? $upload['error'] : __( '头像上传失败。', 'yneko-reimu' ) );
	}

	$avatar_url = esc_url_raw( $upload['url'] );
	$pending    = yneko_reimu_avatar_review_enabled();
	if ( $pending ) {
		update_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', $avatar_url );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'pending' );
	} else {
		$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );
		if ( $current && $current !== $avatar_url ) {
			yneko_reimu_delete_upload_by_url( $current );
		}
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $avatar_url );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	}

	return array(
		'url'     => $avatar_url,
		'pending' => $pending,
	);
}

function yneko_reimu_comment_avatar_frame_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id || ! function_exists( 'yneko_reimu_settings_user_badges' ) ) {
		return '';
	}

	if ( '0' === (string) get_user_meta( $user_id, '_yneko_reimu_avatar_frame_enabled', true ) ) {
		return '';
	}

	$config = yneko_reimu_settings_user_badges();
	$frames = isset( $config['avatar_frames'] ) && is_array( $config['avatar_frames'] ) ? $config['avatar_frames'] : array();
	if ( '1' !== (string) ( $frames['enabled'] ?? '0' ) ) {
		return '';
	}
	$frame_urls = isset( $frames['frames'] ) && is_array( $frames['frames'] ) ? $frames['frames'] : array();
	foreach ( yneko_reimu_comment_user_special_badge_types( $user_id ) as $type ) {
		$url = isset( $frame_urls[ $type ] ) ? esc_url_raw( $frame_urls[ $type ] ) : '';
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

function yneko_reimu_comment_avatar_with_frame( $avatar_html, $user_id, $class = '' ) {
	$frame_url = yneko_reimu_comment_avatar_frame_url( $user_id );
	if ( ! $frame_url ) {
		return $avatar_html;
	}

	$class = trim( 'reimu-avatar-frame ' . $class );
	return '<span class="' . esc_attr( $class ) . '" style="--reimu-avatar-frame:url(' . esc_url( $frame_url ) . ');">' . $avatar_html . '</span>';
}

function yneko_reimu_comment_avatar_for_user_html( $user_id, $size = 56 ) {
	$user_id = absint( $user_id );
	$user = $user_id ? get_userdata( $user_id ) : null;
	if ( ! $user ) {
		return '';
	}

	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	$avatar_url   = yneko_reimu_user_avatar_url( $user_id );
	$avatar       = $avatar_url ? '<img alt="' . esc_attr( $display_name ) . '" src="' . esc_url( $avatar_url ) . '" class="avatar avatar-' . absint( $size ) . ' photo yneko-user-avatar" height="' . absint( $size ) . '" width="' . absint( $size ) . '" loading="lazy" decoding="async">' : get_avatar( $user_id, $size, '', $display_name );
	return yneko_reimu_comment_avatar_with_frame( $avatar, $user_id, 'reimu-avatar-frame--current' );
}

function yneko_reimu_avatar_upload_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['avatar_enabled'] ?? '0' );
}

function yneko_reimu_avatar_review_enabled() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return '1' === (string) ( $settings['avatar_review'] ?? '0' );
}

function yneko_reimu_avatar_upload_limit() {
	$settings = function_exists( 'yneko_reimu_settings_comment_upload' ) ? yneko_reimu_settings_comment_upload() : array();
	return max( 1, absint( $settings['avatar_max_mb'] ?? 1 ) ) * MB_IN_BYTES;
}

function yneko_reimu_avatar_upload_dir( $dirs ) {
	$pending = ! empty( $GLOBALS['yneko_reimu_avatar_upload_pending'] );
	$subdir  = ( $pending ? '/yneko-reimu-avatars-pending' : '/yneko-reimu-avatars' ) . gmdate( '/Y/m' );
	$dirs['subdir'] = $subdir;
	$dirs['path']   = $dirs['basedir'] . $subdir;
	$dirs['url']    = $dirs['baseurl'] . $subdir;
	return $dirs;
}

function yneko_reimu_delete_upload_by_url( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return;
	}
	$uploads = wp_get_upload_dir();
	if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) || 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return;
	}
	$path = $uploads['basedir'] . str_replace( '/', DIRECTORY_SEPARATOR, substr( $url, strlen( $uploads['baseurl'] ) ) );
	if ( is_file( $path ) ) {
		wp_delete_file( $path );
	}
}
