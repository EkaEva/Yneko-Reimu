<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_save_post_options( $post_id ) {
	if ( ! yneko_reimu_can_save_post_options( $post_id ) ) {
		return;
	}

	yneko_reimu_save_post_text_options( $post_id );
	yneko_reimu_save_post_i18n_options( $post_id );
	yneko_reimu_save_post_choice_options( $post_id );
}
add_action( 'save_post', 'yneko_reimu_save_post_options' );

function yneko_reimu_can_save_post_options( $post_id ) {
	if ( ! isset( $_POST['yneko_reimu_post_options_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yneko_reimu_post_options_nonce'] ) ), 'yneko_reimu_save_post_options' ) ) {
		return false;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	return current_user_can( 'edit_post', $post_id );
}

function yneko_reimu_save_post_text_options( $post_id ) {
	foreach ( yneko_reimu_post_meta_url_fields() as $meta_key => $field_name ) {
		yneko_reimu_update_or_delete_post_meta( $post_id, $meta_key, yneko_reimu_posted_url_field( $field_name ) );
	}

	foreach ( yneko_reimu_post_meta_textarea_fields() as $meta_key => $field_name ) {
		yneko_reimu_update_or_delete_post_meta( $post_id, $meta_key, yneko_reimu_posted_textarea_field( $field_name ) );
	}
}

function yneko_reimu_post_meta_url_fields() {
	return array(
		'_yneko_reimu_banner_url' => 'yneko_reimu_banner_url',
		'_yneko_reimu_cover_url'  => 'yneko_reimu_cover_url',
	);
}

function yneko_reimu_post_meta_textarea_fields() {
	return array(
		'_yneko_reimu_summary'  => 'yneko_reimu_summary',
		'_yneko_reimu_keywords' => 'yneko_reimu_keywords',
	);
}

function yneko_reimu_posted_url_field( $field_name ) {
	if ( ! isset( $_POST[ $field_name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
		return '';
	}

	return esc_url_raw( wp_unslash( $_POST[ $field_name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
}

function yneko_reimu_posted_textarea_field( $field_name ) {
	if ( ! isset( $_POST[ $field_name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
		return '';
	}

	return sanitize_textarea_field( wp_unslash( $_POST[ $field_name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
}

function yneko_reimu_update_or_delete_post_meta( $post_id, $meta_key, $value ) {
	if ( '' !== (string) $value ) {
		update_post_meta( $post_id, $meta_key, $value );
	} else {
		delete_post_meta( $post_id, $meta_key );
	}
}

function yneko_reimu_save_post_i18n_options( $post_id ) {
	$language = 'zh_CN';
	if ( isset( $_POST['yneko_reimu_language'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
		$language = sanitize_text_field( wp_unslash( $_POST['yneko_reimu_language'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
	}
	if ( ! function_exists( 'yneko_reimu_i18n_language_exists' ) || ! yneko_reimu_i18n_language_exists( $language ) ) {
		$language = 'zh_CN';
	}

	update_post_meta( $post_id, '_yneko_reimu_language', $language );
	$translation_id = isset( $_POST['yneko_reimu_translation_id'] ) ? absint( wp_unslash( $_POST['yneko_reimu_translation_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
	yneko_reimu_save_translation_link( $post_id, $translation_id );
}

function yneko_reimu_save_post_choice_options( $post_id ) {
	$values = array();
	if ( isset( $_POST['yneko_reimu_meta'] ) && is_array( $_POST['yneko_reimu_meta'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by yneko_reimu_can_save_post_options().
		$values = wp_unslash( $_POST['yneko_reimu_meta'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per registered meta field below.
	}

	foreach ( yneko_reimu_post_meta_choice_allowed_values() as $meta_key => $choices ) {
		$value = isset( $values[ $meta_key ] ) ? sanitize_key( $values[ $meta_key ] ) : 'inherit';
		yneko_reimu_save_post_choice_value( $post_id, $meta_key, in_array( $value, $choices, true ) ? $value : 'inherit' );
	}
}

function yneko_reimu_post_meta_choice_allowed_values() {
	return array(
		'_yneko_reimu_sidebar'   => array( 'inherit', 'right', 'left', 'disabled' ),
		'_yneko_reimu_toc'       => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_copyright' => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_outdated'  => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_comments'  => array( 'inherit', 'show', 'hide' ),
		'_yneko_reimu_sticky'    => array( 'inherit', 'show', 'hide' ),
	);
}

function yneko_reimu_save_post_choice_value( $post_id, $meta_key, $value ) {
	if ( '_yneko_reimu_sticky' === $meta_key ) {
		yneko_reimu_save_post_sticky_choice( $post_id, $value );
		return;
	}

	if ( 'inherit' === $value ) {
		delete_post_meta( $post_id, $meta_key );
	} else {
		update_post_meta( $post_id, $meta_key, $value );
	}
}

function yneko_reimu_save_post_sticky_choice( $post_id, $value ) {
	if ( 'show' === $value ) {
		update_post_meta( $post_id, '_yneko_reimu_sticky', '1' );
	} elseif ( 'hide' === $value ) {
		update_post_meta( $post_id, '_yneko_reimu_sticky', '0' );
	} else {
		delete_post_meta( $post_id, '_yneko_reimu_sticky' );
	}
}

function yneko_reimu_save_translation_link( $post_id, $translation_id ) {
	$post_id        = absint( $post_id );
	$translation_id = absint( $translation_id );

	if ( ! $post_id ) {
		return;
	}

	$old_translation = absint( get_post_meta( $post_id, '_yneko_reimu_translation_id', true ) );
	if ( $old_translation && $old_translation !== $translation_id && absint( get_post_meta( $old_translation, '_yneko_reimu_translation_id', true ) ) === $post_id ) {
		delete_post_meta( $old_translation, '_yneko_reimu_translation_id' );
	}

	if ( ! $translation_id || $translation_id === $post_id || get_post_type( $translation_id ) !== get_post_type( $post_id ) ) {
		delete_post_meta( $post_id, '_yneko_reimu_translation_id' );
		return;
	}

	update_post_meta( $post_id, '_yneko_reimu_translation_id', $translation_id );
	update_post_meta( $translation_id, '_yneko_reimu_translation_id', $post_id );

	if ( function_exists( 'yneko_reimu_i18n_post_language' ) && yneko_reimu_i18n_post_language( $translation_id ) === yneko_reimu_i18n_post_language( $post_id ) ) {
		$opposite = 'zh_CN' === yneko_reimu_i18n_post_language( $post_id ) ? 'en_US' : 'zh_CN';
		update_post_meta( $translation_id, '_yneko_reimu_language', $opposite );
	}
}
