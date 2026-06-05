<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_tag_reserved_labels() {
	$labels = array(
		'站长',
		'管理员',
		'管理員',
		'博主',
		'作者',
		'编辑',
		'訂閱者',
		'订阅者',
		'贡献者',
		'貢獻者',
		'版主',
		'官方',
		'会员',
		'會員',
		'admin',
		'administrator',
		'owner',
		'webmaster',
		'blogger',
		'author',
		'editor',
		'subscriber',
		'contributor',
		'moderator',
		'official',
		'member',
		'yko',
	);

	if ( function_exists( 'yneko_reimu_settings_user_badges' ) ) {
		$config = yneko_reimu_settings_user_badges();
		if ( ! empty( $config['special'] ) && is_array( $config['special'] ) ) {
			foreach ( $config['special'] as $key => $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				if ( function_exists( 'yneko_reimu_user_badge_base_definitions' ) ) {
					$definitions = yneko_reimu_user_badge_base_definitions();
					if ( isset( $definitions[ $key ] ) ) {
						$labels[] = $definitions[ $key ]['title_zh'] ?? '';
						$labels[] = $definitions[ $key ]['title_en'] ?? '';
						$labels[] = $definitions[ $key ]['zh'] ?? '';
						$labels[] = $definitions[ $key ]['en'] ?? '';
					}
				}
				foreach ( array( 'zh', 'en' ) as $lang_key ) {
					if ( ! empty( $row[ $lang_key ] ) ) {
						$labels[] = $row[ $lang_key ];
					}
				}
			}
		}
		if ( ! empty( $config['blocklist'] ) ) {
			foreach ( preg_split( '#/+#u', (string) $config['blocklist'] ) as $blocked ) {
				$labels[] = $blocked;
			}
		}
	}

	return array_values( array_unique( array_map( static function ( $label ) {
		return trim( mb_strtolower( wp_strip_all_tags( (string) $label ) ) );
	}, $labels ) ) );
}

function yneko_reimu_comment_tag_label_is_reserved( $label ) {
	$label = trim( mb_strtolower( wp_strip_all_tags( (string) $label ) ) );
	if ( '' === $label ) {
		return false;
	}

	return in_array( $label, yneko_reimu_comment_tag_reserved_labels(), true );
}

function yneko_reimu_comment_tag_review_enabled() {
	$config = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	return '1' === (string) ( $config['review_enabled'] ?? '0' );
}

function yneko_reimu_comment_user_can_bypass_tag_review( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	return $user_id && ( user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'moderate_comments' ) );
}

function yneko_reimu_comment_custom_tag_storage_limit() {
	return 5;
}

function yneko_reimu_comment_badge_display_limit() {
	return 2;
}

function yneko_reimu_comment_tag_id( $id = '' ) {
	$id = sanitize_key( (string) $id );
	if ( '' !== $id ) {
		return $id;
	}

	return 'tag_' . substr( md5( wp_generate_uuid4() . '|' . microtime( true ) ), 0, 16 );
}

function yneko_reimu_comment_normalize_tag_list( $stored, $limit = null ) {
	if ( null === $limit ) {
		$limit = yneko_reimu_comment_custom_tag_storage_limit();
	}
	$stored = is_array( $stored ) ? $stored : array();
	$tags   = array();
	foreach ( $stored as $tag ) {
		if ( count( $tags ) >= $limit || ! is_array( $tag ) ) {
			break;
		}

		$label = yneko_reimu_sanitize_comment_tag_label( $tag['label'] ?? '' );
		if ( '' === $label || yneko_reimu_comment_tag_label_is_reserved( $label ) ) {
			continue;
		}

		$color = sanitize_hex_color( $tag['color'] ?? '' );
		$clean_tag = array(
			'id'      => yneko_reimu_comment_tag_id( $tag['id'] ?? '' ),
			'label'   => $label,
			'color'   => $color ? $color : '#3b82f6',
			'enabled' => '0' === (string) ( $tag['enabled'] ?? '1' ) ? '0' : '1',
		);
		if ( ! empty( $tag['old_id'] ) ) {
			$clean_tag['old_id'] = sanitize_key( $tag['old_id'] );
		}
		if ( ! empty( $tag['old_label'] ) ) {
			$clean_tag['old_label'] = yneko_reimu_sanitize_comment_tag_label( $tag['old_label'] );
		}
		$tags[] = $clean_tag;
	}
	return $tags;
}

function yneko_reimu_comment_user_pending_tags( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$stored = get_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', true );
	if ( ! is_array( $stored ) ) {
		return array();
	}

	$normalized = yneko_reimu_comment_normalize_tag_list( $stored, yneko_reimu_comment_custom_tag_storage_limit() );
	if ( $normalized !== $stored ) {
		if ( $normalized ) {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $normalized );
		} else {
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
		}
	}

	return $normalized;
}

function yneko_reimu_comment_tag_map_by_id( $tags ) {
	$map = array();
	foreach ( is_array( $tags ) ? $tags : array() as $tag ) {
		if ( ! is_array( $tag ) || empty( $tag['id'] ) ) {
			continue;
		}
		$map[ sanitize_key( $tag['id'] ) ] = $tag;
	}
	return $map;
}

function yneko_reimu_comment_tags_same_label( $left, $right ) {
	return mb_strtolower( (string) $left ) === mb_strtolower( (string) $right );
}

function yneko_reimu_comment_prepare_reviewed_tags( $current_tags, $submitted_tags ) {
	$current_tags  = yneko_reimu_comment_normalize_tag_list( $current_tags, yneko_reimu_comment_custom_tag_storage_limit() );
	$submitted_tags = yneko_reimu_comment_normalize_tag_list( $submitted_tags, yneko_reimu_comment_custom_tag_storage_limit() );
	$current_map   = yneko_reimu_comment_tag_map_by_id( $current_tags );
	$active        = array();
	$pending       = array();

	foreach ( $submitted_tags as $submitted ) {
		$id      = sanitize_key( $submitted['id'] ?? '' );
		$current = $id && isset( $current_map[ $id ] ) ? $current_map[ $id ] : null;
		if ( $current ) {
			if ( yneko_reimu_comment_tags_same_label( $current['label'] ?? '', $submitted['label'] ?? '' ) ) {
				$current['color']   = $submitted['color'];
				$current['enabled'] = $submitted['enabled'];
				$active[] = $current;
			} else {
				$active[] = $current;
				$pending[] = array_merge(
					$submitted,
					array(
						'id'      => $id,
						'old_id'  => $id,
						'old_label' => $current['label'],
					)
				);
			}
			continue;
		}

		$pending[] = $submitted;
	}

	return array(
		'active'  => yneko_reimu_comment_normalize_tag_list( $active, yneko_reimu_comment_custom_tag_storage_limit() ),
		'pending' => yneko_reimu_comment_normalize_tag_list( $pending, yneko_reimu_comment_custom_tag_storage_limit() ),
	);
}

function yneko_reimu_sanitize_comment_tag_label( $label ) {
	$label = trim( wp_strip_all_tags( (string) $label ) );
	$label = preg_replace( '/[\r\n\t]+/u', ' ', $label );
	$label = preg_replace( '/\s{2,}/u', ' ', $label );
	$label = trim( $label );
	if ( '' === $label ) {
		return '';
	}

	return mb_substr( $label, 0, 8 );
}

function yneko_reimu_comment_user_custom_tags( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$stored = get_user_meta( $user_id, '_yneko_reimu_comment_tags', true );
	if ( ! is_array( $stored ) ) {
		return array();
	}

	$normalized = yneko_reimu_comment_normalize_tag_list( $stored, yneko_reimu_comment_custom_tag_storage_limit() );
	if ( $normalized !== $stored ) {
		if ( $normalized ) {
			update_user_meta( $user_id, '_yneko_reimu_comment_tags', $normalized );
		} else {
			delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
		}
	}

	return $normalized;
}
