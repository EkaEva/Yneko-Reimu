<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once YNEKO_REIMU_DIR . '/inc/comments/uploads.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/uploads/helpers.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/uploads/admin.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/modals.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/auth.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/profile-save.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/profile.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/mutations.php';
require_once YNEKO_REIMU_DIR . '/inc/comments/rendering.php';

function yneko_reimu_comments_canonical_post_id( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : absint( get_queried_object_id() );
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
	if ( ! $post_id ) {
		return 0;
	}

	if ( function_exists( 'yneko_reimu_i18n_source_post_id' ) ) {
		$source_id = yneko_reimu_i18n_source_post_id( $post_id );
		if ( $source_id ) {
			return $source_id;
		}
	}

	return $post_id;
}

function yneko_reimu_comments_current_display_post_id() {
	$post_id = absint( get_queried_object_id() );
	if ( ! $post_id && function_exists( 'yneko_reimu_is_virtual_page' ) && yneko_reimu_is_virtual_page( 'projects' ) ) {
		$post_id = yneko_reimu_comments_virtual_page_post_id( 'projects' );
	}
	return $post_id ? $post_id : get_the_ID();
}

function yneko_reimu_comments_virtual_page_post_id( $slug ) {
	$slug = sanitize_title( $slug );
	if ( ! $slug ) {
		return 0;
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page && 'publish' === get_post_status( $page ) ) {
		return absint( $page->ID );
	}

	if ( 'projects' === $slug ) {
		$carrier_id = absint( get_option( 'yneko_reimu_projects_comment_post_id' ) );
		$carrier    = $carrier_id ? get_post( $carrier_id ) : null;
		if ( $carrier && 'trash' !== get_post_status( $carrier ) ) {
			return $carrier_id;
		}

		$existing_carrier = get_page_by_path( 'yneko-reimu-projects-comments', OBJECT, 'page' );
		if ( $existing_carrier && 'trash' !== get_post_status( $existing_carrier ) ) {
			$carrier_id = absint( $existing_carrier->ID );
		} else {
			$carrier_id = wp_insert_post(
				array(
					'post_title'     => 'Yneko Reimu Projects Comments',
					'post_name'      => 'yneko-reimu-projects-comments',
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_content'   => '',
					'comment_status' => 'open',
					'ping_status'    => 'closed',
				),
				true
			);
			$carrier_id = is_wp_error( $carrier_id ) ? 0 : absint( $carrier_id );
		}

		if ( $carrier_id ) {
			update_option( 'yneko_reimu_projects_comment_post_id', $carrier_id, false );
			return $carrier_id;
		}
	}

	$fallback_id = absint( get_option( 'page_on_front' ) );
	if ( ! $fallback_id ) {
		$fallback_id = absint( get_option( 'page_for_posts' ) );
	}

	return $fallback_id;
}

function yneko_reimu_default_open_projects_comments( $post_id, $post, $update ) {
	if ( $update || 'page' !== $post->post_type || 'projects' !== $post->post_name ) {
		return;
	}

	if ( 'open' === $post->comment_status ) {
		return;
	}

	remove_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10 );
	wp_update_post(
		array(
			'ID'             => absint( $post_id ),
			'comment_status' => 'open',
		)
	);
	add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );
}
add_action( 'wp_insert_post', 'yneko_reimu_default_open_projects_comments', 10, 3 );

function yneko_reimu_ajax_language_from_url( $url ) {
	if ( ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() || ! function_exists( 'yneko_reimu_i18n_url_prefix' ) ) {
		return '';
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path = trim( is_string( $path ) ? $path : '', '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home && ( $path === $home || 0 === strpos( $path, $home . '/' ) ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}

	$prefix = trim( (string) yneko_reimu_i18n_url_prefix(), '/' );
	return ( $prefix && ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ) ? 'en_US' : 'zh_CN';
}

function yneko_reimu_ajax_set_language_from_redirect( $redirect ) {
	$language = yneko_reimu_ajax_language_from_url( $redirect );
	if ( ! $language || ! function_exists( 'yneko_reimu_i18n_enabled' ) || ! yneko_reimu_i18n_enabled() ) {
		return;
	}

	$GLOBALS['yneko_reimu_current_language'] = $language;
	if ( 'en_US' === $language ) {
		$mofile = YNEKO_REIMU_DIR . '/languages/en_US.mo';
		if ( file_exists( $mofile ) ) {
			unload_textdomain( 'yneko-reimu' );
			load_textdomain( 'yneko-reimu', $mofile, 'en_US' );
		}
	}
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

function yneko_reimu_hold_comment_with_pending_uploads( $approved, $commentdata ) {
	$content = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	if ( false !== strpos( $content, 'yneko-reimu-comments/tmp/' ) ) {
		return 0;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'yneko_reimu_hold_comment_with_pending_uploads', 10, 2 );


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

function yneko_reimu_avatar_admin_action() {
	$action  = isset( $_GET['yneko_avatar_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_avatar_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id ) {
		return;
	}
	if ( ! in_array( $action, array( 'approve', 'reject', 'delete' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_avatar_' . $action . '_' . $user_id );

	$pending = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_pending_url', true );
	$current = (string) get_user_meta( $user_id, '_yneko_reimu_avatar_url', true );

	if ( 'approve' === $action && $pending ) {
		if ( $current && $current !== $pending ) {
			yneko_reimu_delete_upload_by_url( $current );
		}
		update_user_meta( $user_id, '_yneko_reimu_avatar_url', $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'updated' );
	} elseif ( 'reject' === $action && $pending ) {
		yneko_reimu_delete_upload_by_url( $pending );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_pending_url' );
		yneko_reimu_set_user_review_status( $user_id, 'avatar', 'rejected' );
	} elseif ( 'delete' === $action ) {
		yneko_reimu_delete_upload_by_url( $current );
		delete_user_meta( $user_id, '_yneko_reimu_avatar_url' );
		yneko_reimu_clear_user_review_status( $user_id, 'avatar' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_avatar_action', 'user_id', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_avatar_admin_action' );

function yneko_reimu_user_badge_admin_action() {
	$action  = isset( $_GET['yneko_user_badge_action'] ) ? sanitize_key( wp_unslash( $_GET['yneko_user_badge_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$index   = isset( $_GET['tag_index'] ) ? absint( $_GET['tag_index'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $action || ! $user_id || ! in_array( $action, array( 'approve', 'reject', 'revoke' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '权限不足。', 'yneko-reimu' ), 403 );
	}
	check_admin_referer( 'yneko_reimu_user_badge_' . $action . '_' . $user_id . '_' . $index );

	$active  = yneko_reimu_comment_user_custom_tags( $user_id );
	$pending = yneko_reimu_comment_user_pending_tags( $user_id );

	if ( 'approve' === $action && isset( $pending[ $index ] ) ) {
		$approved_tag = $pending[ $index ];
		$replace_id   = sanitize_key( $approved_tag['old_id'] ?? ( $approved_tag['id'] ?? '' ) );
		$active = array_values(
			array_filter(
				$active,
				static function ( $tag ) use ( $approved_tag, $replace_id ) {
					if ( ! is_array( $tag ) ) {
						return false;
					}
					$tag_id = sanitize_key( $tag['id'] ?? '' );
					if ( $replace_id && $tag_id === $replace_id ) {
						return false;
					}
					return ! yneko_reimu_comment_tags_same_label( $tag['label'] ?? '', $approved_tag['label'] ?? '' );
				}
			)
		);
		unset( $approved_tag['old_id'], $approved_tag['old_label'] );
		$active[] = $approved_tag;
		$active   = yneko_reimu_comment_normalize_tag_list( $active, yneko_reimu_comment_custom_tag_storage_limit() );
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'updated' );
	} elseif ( 'reject' === $action && isset( $pending[ $index ] ) ) {
		unset( $pending[ $index ] );
		$pending = array_values( $pending );
		yneko_reimu_set_user_review_status( $user_id, 'tags', $pending ? 'pending' : 'rejected' );
	} elseif ( 'revoke' === $action && isset( $active[ $index ] ) ) {
		unset( $active[ $index ] );
		$active = array_values( $active );
		yneko_reimu_set_user_review_status( $user_id, 'tags', 'updated' );
	}

	if ( $active ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags', $active );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags' );
	}
	if ( $pending ) {
		update_user_meta( $user_id, '_yneko_reimu_comment_tags_pending', $pending );
	} else {
		delete_user_meta( $user_id, '_yneko_reimu_comment_tags_pending' );
	}

	wp_safe_redirect( remove_query_arg( array( 'yneko_user_badge_action', 'user_id', 'tag_index', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'yneko_reimu_user_badge_admin_action' );
