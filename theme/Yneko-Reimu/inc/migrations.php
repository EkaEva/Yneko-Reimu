<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_old_key( $key ) {
	$key = (string) $key;

	if ( 0 === strpos( $key, 'yneko_reimu_' ) ) {
		return 'reimu_wp_' . substr( $key, strlen( 'yneko_reimu_' ) );
	}

	if ( 0 === strpos( $key, '_yneko_reimu_' ) ) {
		return '_reimu_wp_' . substr( $key, strlen( '_yneko_reimu_' ) );
	}

	return $key;
}

function yneko_reimu_new_key( $key ) {
	$key = (string) $key;

	if ( 0 === strpos( $key, 'reimu_wp_' ) ) {
		return 'yneko_reimu_' . substr( $key, strlen( 'reimu_wp_' ) );
	}

	if ( 0 === strpos( $key, '_reimu_wp_' ) ) {
		return '_yneko_reimu_' . substr( $key, strlen( '_reimu_wp_' ) );
	}

	return $key;
}

function yneko_reimu_get_theme_mod( $name, $default = false ) {
	$value = get_theme_mod( $name, null );

	if ( null !== $value ) {
		return $value;
	}

	$old_name = yneko_reimu_old_key( $name );
	if ( $old_name !== $name ) {
		$old_value = get_theme_mod( $old_name, null );
		if ( null !== $old_value ) {
			return $old_value;
		}
	}

	return $default;
}

function yneko_reimu_get_post_meta( $post_id, $key, $single = true ) {
	$value = get_post_meta( $post_id, $key, $single );

	if ( $single && '' === $value ) {
		$old_key = yneko_reimu_old_key( $key );
		if ( $old_key !== $key ) {
			$old_value = get_post_meta( $post_id, $old_key, true );
			if ( '' !== $old_value ) {
				return $old_value;
			}
		}
	}

	return $value;
}

function yneko_reimu_get_comment_meta( $comment_id, $key, $single = true ) {
	$value = get_comment_meta( $comment_id, $key, $single );

	if ( $single && '' === $value ) {
		$old_key = yneko_reimu_old_key( $key );
		if ( $old_key !== $key ) {
			$old_value = get_comment_meta( $comment_id, $old_key, true );
			if ( '' !== $old_value ) {
				return $old_value;
			}
		}
	}

	return $value;
}

function yneko_reimu_get_option( $option, $default = false ) {
	$value = get_option( $option, null );

	if ( null !== $value ) {
		return $value;
	}

	$old_option = yneko_reimu_old_key( $option );
	if ( $old_option !== $option ) {
		$old_value = get_option( $old_option, null );
		if ( null !== $old_value ) {
			return $old_value;
		}
	}

	return $default;
}

function yneko_reimu_copy_option_if_missing( $old_option, $new_option ) {
	if ( false !== get_option( $new_option, false ) ) {
		return;
	}

	$old_value = get_option( $old_option, false );
	if ( false !== $old_value ) {
		update_option( $new_option, $old_value, false );
	}
}

function yneko_reimu_migrate_theme_mods() {
	$old_mods = get_option( 'theme_mods_reimu-wp', array() );
	if ( ! is_array( $old_mods ) || ! $old_mods ) {
		return;
	}

	$current_theme = get_stylesheet();
	$new_option    = 'theme_mods_' . $current_theme;
	$new_mods      = get_option( $new_option, array() );
	$new_mods      = is_array( $new_mods ) ? $new_mods : array();

	foreach ( $old_mods as $key => $value ) {
		$new_key = yneko_reimu_new_key( $key );
		if ( ! array_key_exists( $new_key, $new_mods ) ) {
			$new_mods[ $new_key ] = $value;
		}
	}

	update_option( $new_option, $new_mods );
}

function yneko_reimu_migrate_post_meta() {
	global $wpdb;

	$rows = $wpdb->get_results(
		"SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE '\\_reimu\\_wp\\_%'"
	);

	foreach ( $rows as $row ) {
		$new_key = yneko_reimu_new_key( $row->meta_key );
		if ( $new_key === $row->meta_key ) {
			continue;
		}

		if ( ! metadata_exists( 'post', $row->post_id, $new_key ) ) {
			add_post_meta( $row->post_id, $new_key, maybe_unserialize( $row->meta_value ) );
		}
	}
}

function yneko_reimu_migrate_comment_meta() {
	global $wpdb;

	$rows = $wpdb->get_results(
		"SELECT comment_id, meta_key, meta_value FROM {$wpdb->commentmeta} WHERE meta_key LIKE '\\_reimu\\_wp\\_%'"
	);

	foreach ( $rows as $row ) {
		$new_key = yneko_reimu_new_key( $row->meta_key );
		if ( $new_key === $row->meta_key ) {
			continue;
		}

		if ( ! metadata_exists( 'comment', $row->comment_id, $new_key ) ) {
			add_comment_meta( $row->comment_id, $new_key, maybe_unserialize( $row->meta_value ) );
		}
	}
}

function yneko_reimu_migrate_options() {
	yneko_reimu_copy_option_if_missing( 'reimu_wp_site_pv', 'yneko_reimu_site_pv' );
	yneko_reimu_copy_option_if_missing( 'reimu_wp_site_uv', 'yneko_reimu_site_uv' );
	yneko_reimu_copy_option_if_missing( 'yneko_github_login_options', 'yneko_reimu_github_login_options' );
}

function yneko_reimu_parse_friend_links_text( $raw ) {
	$friends = array();

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		$parts = array_pad( array_map( 'trim', explode( '|', $line ) ), 4, '' );
		if ( '' === $parts[0] || '' === $parts[1] ) {
			continue;
		}

		if ( ! preg_match( '#^https?://#i', $parts[1] ) ) {
			$parts[1] = 'https://' . ltrim( $parts[1], '/' );
		}

		$friends[] = array(
			'name'  => $parts[0],
			'url'   => $parts[1],
			'desc'  => $parts[2],
			'image' => $parts[3],
		);
	}

	return $friends;
}

function yneko_reimu_migrate_unified_settings() {
	if ( ! function_exists( 'yneko_reimu_settings_defaults' ) ) {
		return;
	}

	$current  = get_option( 'yneko_reimu_settings', array() );
	$current  = is_array( $current ) ? $current : array();
	$mods     = get_option( 'theme_mods_' . get_stylesheet(), array() );
	$mods     = is_array( $mods ) ? $mods : array();
	$oauth    = get_option( 'yneko_reimu_github_login_options', array() );
	$oauth    = is_array( $oauth ) ? $oauth : array();
	$settings = wp_parse_args( $current, yneko_reimu_settings_defaults() );

	if ( empty( $settings['site_avatar_url'] ) && ! empty( $mods['yneko_reimu_default_avatar'] ) ) {
		$settings['site_avatar_url'] = $mods['yneko_reimu_default_avatar'];
	}

	if ( empty( $settings['author_avatar_url'] ) && ! empty( $mods['yneko_reimu_default_avatar'] ) ) {
		$settings['author_avatar_url'] = $mods['yneko_reimu_default_avatar'];
	}

	if ( empty( $settings['comment_avatar_url'] ) ) {
		$comment_avatar_id = absint( get_option( 'avatar_default_wp_user_avatar', 0 ) );
		$comment_avatar    = $comment_avatar_id ? wp_get_attachment_image_url( $comment_avatar_id, 'thumbnail' ) : '';
		if ( $comment_avatar ) {
			$settings['comment_avatar_url'] = $comment_avatar;
		}
	}

	if ( empty( $settings['github_url'] ) && ! empty( $mods['yneko_reimu_social_github'] ) ) {
		$settings['github_url'] = $mods['yneko_reimu_social_github'];
	}

	if ( empty( $settings['sponsor_qr_url'] ) && ! empty( $mods['yneko_reimu_sponsor_qr'] ) ) {
		$settings['sponsor_qr_url'] = $mods['yneko_reimu_sponsor_qr'];
	}

	if ( empty( $current['friends'] ) && ! empty( $mods['yneko_reimu_friend_links'] ) ) {
		$settings['friends'] = yneko_reimu_parse_friend_links_text( $mods['yneko_reimu_friend_links'] );
	}

	if ( empty( $current['music'] ) && ! empty( $mods['yneko_reimu_aplayer_audio_json'] ) ) {
		$music = json_decode( (string) $mods['yneko_reimu_aplayer_audio_json'], true );
		if ( is_array( $music ) ) {
			$settings['music'] = $music;
		}
	}

	if ( $oauth && function_exists( 'yneko_reimu_merge_github_oauth_fallback' ) ) {
		$settings['github_oauth'] = yneko_reimu_merge_github_oauth_fallback( $settings['github_oauth'], $oauth );
	}

	update_option( 'yneko_reimu_settings', yneko_reimu_sanitize_settings( $settings ), false );
}

function yneko_reimu_migrate_user_meta() {
	global $wpdb;

	$map = array(
		'_yneko_github_id'         => '_yneko_reimu_github_id',
		'_yneko_github_login'      => '_yneko_reimu_github_login',
		'_yneko_github_url'        => '_yneko_reimu_github_url',
		'_yneko_github_avatar_url' => '_yneko_reimu_github_avatar_url',
	);

	foreach ( $map as $old_key => $new_key ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
				$old_key
			)
		);

		foreach ( $rows as $row ) {
			if ( ! metadata_exists( 'user', $row->user_id, $new_key ) ) {
				add_user_meta( $row->user_id, $new_key, maybe_unserialize( $row->meta_value ) );
			}
		}
	}
}

function yneko_reimu_run_migration() {
	if ( '1' === get_option( 'yneko_reimu_migrated_010', '' ) ) {
		return;
	}

	yneko_reimu_migrate_theme_mods();
	yneko_reimu_migrate_options();
	yneko_reimu_migrate_unified_settings();
	yneko_reimu_migrate_post_meta();
	yneko_reimu_migrate_comment_meta();
	yneko_reimu_migrate_user_meta();

	update_option( 'yneko_reimu_migrated_010', '1', false );
}
add_action( 'after_switch_theme', 'yneko_reimu_run_migration' );
