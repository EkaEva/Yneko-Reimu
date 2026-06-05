<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
