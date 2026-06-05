<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_comment_user_profile_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( $user && ! empty( $user->user_url ) ) {
		return esc_url_raw( $user->user_url );
	}

	return '';
}

function yneko_reimu_comment_user_github_url( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	foreach ( array( '_yneko_reimu_github_url', '_yneko_github_url' ) as $meta_key ) {
		$url = get_user_meta( $user_id, $meta_key, true );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	return '';
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

function yneko_reimu_comment_badges_enabled() {
	$config = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	return '0' !== (string) ( $config['enabled'] ?? '1' );
}

function yneko_reimu_comment_badge_label_for_language( $row, $fallback ) {
	$row = is_array( $row ) ? $row : array();
	$language = function_exists( 'yneko_reimu_i18n_current_language' ) ? yneko_reimu_i18n_current_language() : get_locale();
	$primary  = ( 0 === strpos( (string) $language, 'en' ) ) ? 'en' : 'zh';
	$label    = trim( (string) ( $row[ $primary ] ?? '' ) );
	if ( '' === $label ) {
		$label = trim( (string) ( $row[ 'en' === $primary ? 'zh' : 'en' ] ?? '' ) );
	}
	return '' !== $label ? $label : $fallback;
}

function yneko_reimu_comment_site_owner_user_id() {
	$users = get_users(
		array(
			'role'    => 'administrator',
			'orderby' => 'ID',
			'order'   => 'ASC',
			'number'  => 1,
			'fields'  => 'ID',
		)
	);
	return ! empty( $users[0] ) ? absint( $users[0] ) : 0;
}

function yneko_reimu_comment_special_badge_priority() {
	return array( 'owner', 'admin', 'editor', 'author', 'contributor', 'yko', 'subscriber' );
}

function yneko_reimu_comment_user_special_badge_types( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$user  = get_userdata( $user_id );
	$roles = $user ? array_map( 'sanitize_key', (array) $user->roles ) : array();
	$types = array();
	if ( yneko_reimu_comment_site_owner_user_id() === $user_id ) {
		$types = yneko_reimu_comment_special_badge_priority();
	} else {
		if ( in_array( 'administrator', $roles, true ) ) {
			$types[] = 'admin';
		} elseif ( in_array( 'editor', $roles, true ) ) {
			$types[] = 'editor';
		} elseif ( in_array( 'author', $roles, true ) ) {
			$types[] = 'author';
		} elseif ( in_array( 'contributor', $roles, true ) ) {
			$types[] = 'contributor';
		} elseif ( in_array( 'subscriber', $roles, true ) ) {
			$types[] = 'subscriber';
		}
		$types[] = 'yko';
	}

	$types = array_values( array_unique( $types ) );
	$priority = array_flip( yneko_reimu_comment_special_badge_priority() );
	usort(
		$types,
		static function ( $a, $b ) use ( $priority ) {
			return ( $priority[ $a ] ?? 99 ) <=> ( $priority[ $b ] ?? 99 );
		}
	);
	return $types;
}

function yneko_reimu_comment_user_special_badges( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$config  = function_exists( 'yneko_reimu_settings_user_badges' ) ? yneko_reimu_settings_user_badges() : array();
	$special = isset( $config['special'] ) && is_array( $config['special'] ) ? $config['special'] : array();
	$types   = yneko_reimu_comment_user_special_badge_types( $user_id );

	$fallbacks = array(
		'owner'       => __( '站长', 'yneko-reimu' ),
		'admin'       => __( '管理员', 'yneko-reimu' ),
		'yko'         => 'Yko',
		'subscriber'  => __( '订阅者', 'yneko-reimu' ),
		'contributor' => __( '贡献者', 'yneko-reimu' ),
		'author'      => __( '作者', 'yneko-reimu' ),
		'editor'      => __( '编辑', 'yneko-reimu' ),
	);

	$badges = array();
	foreach ( yneko_reimu_comment_special_badge_priority() as $type ) {
		if ( ! in_array( $type, $types, true ) ) {
			continue;
		}
		$row = isset( $special[ $type ] ) && is_array( $special[ $type ] ) ? $special[ $type ] : array();
		if ( '0' === (string) ( $row['enabled'] ?? '1' ) ) {
			continue;
		}
		if ( ! yneko_reimu_comment_badges_enabled() && ! in_array( $type, array( 'owner', 'admin' ), true ) ) {
			continue;
		}
		$badges[] = array(
			'type'  => $type,
			'label' => yneko_reimu_comment_badge_label_for_language( $row, $fallbacks[ $type ] ?? $type ),
		);
	}

	return $badges;
}

function yneko_reimu_comment_user_special_badge( $user_id ) {
	$badges = yneko_reimu_comment_user_special_badges( $user_id );
	return $badges ? $badges[0] : array();
}

function yneko_reimu_comment_user_hidden_special_badges( $user_id ) {
	$hidden = get_user_meta( absint( $user_id ), '_yneko_reimu_comment_hidden_special_badges', true );
	return is_array( $hidden ) ? array_map( 'sanitize_key', $hidden ) : array();
}

function yneko_reimu_comment_user_tags_payload( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return array();
	}

	$payload = array();
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	$hidden  = yneko_reimu_comment_user_hidden_special_badges( $user_id );
	$touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', true );
	foreach ( $special_badges as $index => $special ) {
		$enabled = $touched ? ! in_array( $special['type'], $hidden, true ) : 0 === $index;
		$payload[] = array(
			'type'    => 'special',
			'key'     => $special['type'],
			'label'   => $special['label'],
			'color'   => '',
			'enabled' => $enabled ? '1' : '0',
		);
	}

	$enabled_special_count = count(
		array_filter(
			$payload,
			static function ( $tag ) {
				return is_array( $tag ) && 'special' === ( $tag['type'] ?? '' ) && '0' !== (string) ( $tag['enabled'] ?? '1' );
			}
		)
	);
	$display_limit = yneko_reimu_comment_badge_display_limit();
	$custom_slots  = max( 0, $display_limit - $enabled_special_count );
	if ( ! yneko_reimu_comment_badges_enabled() ) {
		$custom_slots = 0;
	}
	$enabled_custom_count = 0;
	foreach ( yneko_reimu_comment_user_custom_tags( $user_id ) as $tag ) {
		$enabled = '0' !== (string) ( $tag['enabled'] ?? '1' ) && $enabled_custom_count < $custom_slots;
		if ( $enabled ) {
			$enabled_custom_count++;
		}
		$payload[] = array(
			'type' => 'custom',
			'id'   => $tag['id'],
			'key'  => '',
			'label' => $tag['label'],
			'color' => $tag['color'],
			'enabled' => $enabled ? '1' : '0',
		);
	}

	return $payload;
}

function yneko_reimu_comment_user_badges_html( $user_id ) {
	$user_id = absint( $user_id );
	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}

	$badges = array();
	$special_badges = yneko_reimu_comment_user_special_badges( $user_id );
	$hidden  = yneko_reimu_comment_user_hidden_special_badges( $user_id );
	$touched = '' !== (string) get_user_meta( $user_id, '_yneko_reimu_comment_special_badges_touched', true );
	foreach ( $special_badges as $index => $special ) {
		$enabled = $touched ? ! in_array( $special['type'], $hidden, true ) : 0 === $index;
		if ( ! $enabled ) {
			continue;
		}
		$badges[] = array(
			'label' => $special['label'],
			'class' => 'reimu-comment-user-tag--' . sanitize_html_class( $special['type'] ),
			'style' => '',
		);
	}

	$display_limit = yneko_reimu_comment_badge_display_limit();
	if ( yneko_reimu_comment_badges_enabled() ) {
		foreach ( yneko_reimu_comment_user_custom_tags( $user_id ) as $tag ) {
			if ( count( $badges ) >= $display_limit ) {
				break;
			}
			if ( '0' === (string) ( $tag['enabled'] ?? '1' ) ) {
				continue;
			}
			$badges[] = array(
				'label' => $tag['label'],
				'class' => 'reimu-comment-user-tag--custom',
				'style' => '--reimu-comment-tag-color:' . $tag['color'] . ';',
			);
		}
	}

	$badges = array_slice( $badges, 0, $display_limit );

	if ( empty( $badges ) ) {
		return '';
	}

	$html = '<span class="reimu-comment-user-tags" aria-label="' . esc_attr__( '用户标签', 'yneko-reimu' ) . '">';
	foreach ( $badges as $badge ) {
		$html .= '<span class="reimu-comment-user-tag ' . esc_attr( $badge['class'] ) . '"';
		if ( ! empty( $badge['style'] ) ) {
			$html .= ' style="' . esc_attr( $badge['style'] ) . '"';
		}
		$html .= '>' . esc_html( $badge['label'] ) . '</span>';
	}
	$html .= '</span>';

	return $html;
}
