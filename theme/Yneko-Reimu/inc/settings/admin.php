<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_register_settings_page() {
	add_theme_page(
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		__( 'Yneko-Reimu 设置', 'yneko-reimu' ),
		'manage_options',
		'yneko-reimu-settings',
		'yneko_reimu_render_settings_page'
	);
}
add_action( 'admin_menu', 'yneko_reimu_register_settings_page' );

function yneko_reimu_admin_menu_badge_markup( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return ' <span class="update-plugins yneko-reimu-menu-badge count-' . esc_attr( $count ) . '"><span class="plugin-count">' . esc_html( number_format_i18n( $count ) ) . '</span></span>';
}

function yneko_reimu_add_admin_menu_review_badges() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $menu, $submenu;
	$counts = yneko_reimu_admin_review_badge_counts();
	$total  = absint( ( $counts['comments'] ?? 0 ) + ( $counts['users'] ?? 0 ) );
	if ( ! $total ) {
		return;
	}

	foreach ( $menu as &$item ) {
		if ( ! isset( $item[2] ) || 'themes.php' !== $item[2] ) {
			continue;
		}
		$item[0] = preg_replace( '#\s*<span class="update-plugins yneko-reimu-menu-badge.*?</span></span>#', '', (string) $item[0] );
		$item[0] .= yneko_reimu_admin_menu_badge_markup( $total );
		break;
	}
	unset( $item );

	if ( empty( $submenu['themes.php'] ) || ! is_array( $submenu['themes.php'] ) ) {
		return;
	}
	foreach ( $submenu['themes.php'] as &$subitem ) {
		if ( ! isset( $subitem[2] ) || 'yneko-reimu-settings' !== $subitem[2] ) {
			continue;
		}
		$subitem[0] = preg_replace( '#\s*<span class="update-plugins yneko-reimu-menu-badge.*?</span></span>#', '', (string) $subitem[0] );
		$subitem[0] .= yneko_reimu_admin_menu_badge_markup( $total );
		break;
	}
	unset( $subitem );
}
add_action( 'admin_menu', 'yneko_reimu_add_admin_menu_review_badges', 99 );

function yneko_reimu_admin_media_field( $name, $value, $label, $accept = '' ) {
	?>
	<div class="yneko-reimu-media-field">
		<input type="url" class="regular-text yneko-reimu-media-url" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $accept ? ' data-accept="' . esc_attr( $accept ) . '"' : ''; ?>>
		<button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( $label ); ?></button>
	</div>
	<?php
}

function yneko_reimu_admin_bilingual_text( $zh, $en, $tag = 'span' ) {
	$tag = in_array( $tag, array( 'span', 'p', 'div', 'button' ), true ) ? $tag : 'span';
	$text = yneko_reimu_admin_prefers_zh() ? $zh : $en;
	return sprintf(
		'<%1$s class="yneko-reimu-admin-text">%2$s</%1$s>',
		tag_escape( $tag ),
		esc_html( $text )
	);
}

function yneko_reimu_admin_prefers_zh() {
	$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	return 0 === strpos( strtolower( str_replace( '-', '_', (string) $locale ) ), 'zh_' );
}

function yneko_reimu_admin_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_description( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en, 'p' ) );
}

function yneko_reimu_admin_bilingual_heading( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_button_text( $zh, $en ) {
	return yneko_reimu_admin_bilingual_text( $zh, $en );
}

function yneko_reimu_admin_badge( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return '<span class="yneko-reimu-admin-badge" aria-label="' . esc_attr(
		sprintf(
			/* translators: %d: pending review item count. */
			__( '%d 个待处理项目', 'yneko-reimu' ),
			$count
		)
	) . '">' . esc_html( (string) $count ) . '</span>';
}

function yneko_reimu_admin_current_user_totp_payload() {
	$user_id = get_current_user_id();
	return array(
		'enabled' => $user_id && function_exists( 'yneko_reimu_user_2fa_enabled' ) ? yneko_reimu_user_2fa_enabled( $user_id ) : false,
		'nonce'   => wp_create_nonce( 'yneko_reimu_admin_totp' ),
		'recoveryCount' => $user_id && function_exists( 'yneko_reimu_login_2fa_recovery_code_count' ) ? yneko_reimu_login_2fa_recovery_code_count( $user_id ) : 0,
	);
}

function yneko_reimu_admin_totp_verify_request() {
	check_ajax_referer( 'yneko_reimu_admin_totp', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) || ! get_current_user_id() ) {
		wp_send_json_error( array( 'message' => esc_html__( '权限不足。', 'yneko-reimu' ) ), 403 );
	}
	if ( ! function_exists( 'yneko_reimu_totp_generate_secret' ) || ! function_exists( 'yneko_reimu_totp_verify' ) || ! function_exists( 'yneko_reimu_totp_uri' ) || ! function_exists( 'yneko_reimu_user_2fa_secret' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '二次认证模块尚未加载。', 'yneko-reimu' ) ), 500 );
	}
}

function yneko_reimu_ajax_admin_totp_generate() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	$secret  = yneko_reimu_totp_generate_secret();
	update_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', $secret );

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'secret'  => $secret,
				'uri'     => yneko_reimu_totp_uri( $user_id, $secret ),
				'message' => esc_html__( '请用认证器扫码，并输入 6 位验证码后启用。', 'yneko-reimu' ),
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_generate', 'yneko_reimu_ajax_admin_totp_generate' );

function yneko_reimu_ajax_admin_totp_enable() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	$code    = isset( $_POST['totp_code'] ) ? preg_replace( '/\D+/', '', (string) wp_unslash( $_POST['totp_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is checked by yneko_reimu_admin_totp_verify_request(); regex keeps only digits.
	$secret  = (string) get_user_meta( $user_id, '_yneko_reimu_totp_pending_secret', true );
	if ( '' === $secret ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先生成认证器密钥。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! yneko_reimu_totp_verify( $secret, $code ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '认证器验证码不正确。', 'yneko-reimu' ) ), 400 );
	}

	update_user_meta( $user_id, '_yneko_reimu_totp_secret', $secret );
	update_user_meta( $user_id, '_yneko_reimu_totp_enabled', '1' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );

	$recovery_codes = array();
	if ( function_exists( 'yneko_reimu_login_2fa_generate_recovery_codes' ) && function_exists( 'yneko_reimu_login_2fa_store_recovery_codes' ) ) {
		$recovery_codes = yneko_reimu_login_2fa_generate_recovery_codes();
		yneko_reimu_login_2fa_store_recovery_codes( $user_id, $recovery_codes );
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'message'       => esc_html__( '二次认证已开启。请立即保存这些一次性恢复码，它们只会显示这一次。', 'yneko-reimu' ),
				'recoveryCodes' => $recovery_codes,
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_enable', 'yneko_reimu_ajax_admin_totp_enable' );

function yneko_reimu_ajax_admin_totp_recovery_generate() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	if ( ! function_exists( 'yneko_reimu_user_2fa_enabled' ) || ! yneko_reimu_user_2fa_enabled( $user_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请先开启二次认证。', 'yneko-reimu' ) ), 400 );
	}
	if ( ! function_exists( 'yneko_reimu_login_2fa_generate_recovery_codes' ) || ! function_exists( 'yneko_reimu_login_2fa_store_recovery_codes' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '恢复码模块尚未加载。', 'yneko-reimu' ) ), 500 );
	}

	$recovery_codes = yneko_reimu_login_2fa_generate_recovery_codes();
	yneko_reimu_login_2fa_store_recovery_codes( $user_id, $recovery_codes );

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array(
				'message'       => esc_html__( '新的恢复码已生成，旧恢复码已失效。请立即保存这些恢复码。', 'yneko-reimu' ),
				'recoveryCodes' => $recovery_codes,
			)
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_recovery_generate', 'yneko_reimu_ajax_admin_totp_recovery_generate' );

function yneko_reimu_ajax_admin_totp_disable() {
	yneko_reimu_admin_totp_verify_request();

	$user_id = get_current_user_id();
	delete_user_meta( $user_id, '_yneko_reimu_totp_enabled' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_secret' );
	delete_user_meta( $user_id, '_yneko_reimu_totp_pending_secret' );
	if ( function_exists( 'yneko_reimu_login_2fa_clear_recovery_codes' ) ) {
		yneko_reimu_login_2fa_clear_recovery_codes( $user_id );
	}

	wp_send_json_success(
		array_merge(
			yneko_reimu_admin_current_user_totp_payload(),
			array( 'message' => esc_html__( '二次认证已关闭。', 'yneko-reimu' ) )
		)
	);
}
add_action( 'wp_ajax_yneko_reimu_admin_totp_disable', 'yneko_reimu_ajax_admin_totp_disable' );

function yneko_reimu_admin_count_pending_comment_uploads( $type = 'all' ) {
	if ( ! function_exists( 'yneko_reimu_comment_pending_temp_uploads' ) || ! function_exists( 'yneko_reimu_comment_upload_library' ) ) {
		return 0;
	}

	$type = in_array( $type, array( 'all', 'image', 'gif' ), true ) ? $type : 'all';
	$count = 0;
	foreach ( yneko_reimu_comment_pending_temp_uploads( 300 ) as $item ) {
		$item_type = 'gif' === ( $item['type'] ?? '' ) ? 'gif' : 'image';
		if ( 'all' === $type || $type === $item_type ) {
			$count++;
		}
	}
	foreach ( yneko_reimu_comment_upload_library( 300, $type, true ) as $item ) {
		if ( 'pending' === (string) ( $item['status'] ?? '' ) ) {
			$count++;
		}
	}

	return $count;
}

function yneko_reimu_admin_count_pending_avatars() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_avatar_pending_url',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	return count( $users );
}

function yneko_reimu_admin_count_pending_user_badges() {
	$users = get_users(
		array(
			'number'     => 300,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_yneko_reimu_comment_tags_pending',
					'compare' => 'EXISTS',
				),
			),
		)
	);
	$count = 0;
	foreach ( $users as $user_id ) {
		$tags = get_user_meta( absint( $user_id ), '_yneko_reimu_comment_tags_pending', true );
		if ( is_array( $tags ) ) {
			$count += count( array_filter( $tags, 'is_array' ) );
		}
	}
	return $count;
}

function yneko_reimu_admin_review_badge_counts() {
	$upload = yneko_reimu_settings_comment_upload();
	$badges = yneko_reimu_settings_user_badges();
	$counts = array(
		'comment_images' => '1' === (string) ( $upload['image_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'image' ) : 0,
		'comment_gifs'   => '1' === (string) ( $upload['gif_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_comment_uploads( 'gif' ) : 0,
		'avatars'        => '1' === (string) ( $upload['avatar_review'] ?? '0' ) ? yneko_reimu_admin_count_pending_avatars() : 0,
		'user_badges'    => '1' === (string) ( $badges['review_enabled'] ?? '0' ) ? yneko_reimu_admin_count_pending_user_badges() : 0,
	);
	$counts['comments'] = $counts['comment_images'] + $counts['comment_gifs'];
	$counts['users']    = $counts['avatars'] + $counts['user_badges'];
	return $counts;
}

function yneko_reimu_enqueue_settings_admin_assets( $hook ) {
	if ( 'appearance_page_yneko-reimu-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_register_style( 'yneko-reimu-admin-settings', false, array(), YNEKO_REIMU_VERSION );
	wp_enqueue_style( 'yneko-reimu-admin-settings' );
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-settings-page{padding-bottom:96px}.yneko-reimu-settings-tabs{display:flex;flex-wrap:wrap;gap:0;margin-top:20px}.yneko-reimu-settings-tabs .nav-tab{display:inline-flex;align-items:center;min-height:40px;margin-left:0;margin-right:6px;padding:8px 15px;background:#f0f0f1;border-bottom:1px solid #c3c4c7;color:#1d2327;cursor:pointer}.yneko-reimu-settings-tabs .nav-tab-active{background:#fff;border-bottom-color:#fff;color:#2271b1}.yneko-reimu-settings-panel{max-width:1280px;padding-top:4px}.yneko-reimu-settings-panel[hidden]{display:none!important}.yneko-reimu-settings-panel h2:first-child{margin-top:24px}.yneko-reimu-settings-group{max-width:1040px;margin:18px 0;padding:18px 20px;border:1px solid #dcdcde;border-radius:8px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.025)}.yneko-reimu-settings-group__header{display:flex;flex-direction:column;gap:4px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid #f0f0f1}.yneko-reimu-settings-group__header h3{margin:0;font-size:15px;line-height:1.4}.yneko-reimu-settings-group__body{display:flex;flex-direction:column;gap:12px}.yneko-reimu-field{display:flex;flex-direction:column;gap:7px;max-width:760px}.yneko-reimu-field__label{font-weight:600;color:#1d2327}.yneko-reimu-checkbox-line{display:inline-flex;align-items:center;gap:8px;margin:0 0 8px;line-height:1.55;font-weight:400;white-space:nowrap}.yneko-reimu-checkbox-line input[type=checkbox]{flex:0 0 auto;margin:0}.yneko-reimu-checkbox-line .yneko-reimu-admin-text{display:inline;margin:0;color:inherit}.yneko-reimu-checkbox-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:4px 18px}.yneko-reimu-settings-subgrid{display:grid;gap:12px 18px;align-items:start}.yneko-reimu-settings-subgrid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.yneko-reimu-settings-subgrid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-settings-subgrid label{display:flex;flex-direction:column;gap:6px;font-weight:600}.yneko-reimu-settings-subgrid label.yneko-reimu-checkbox-line{display:inline-flex;flex-direction:row;align-items:center;gap:8px;font-weight:400;white-space:nowrap}.yneko-reimu-settings-subgrid label.yneko-reimu-checkbox-line input[type=checkbox]{margin:0}.yneko-reimu-settings-subgrid input:not([type=checkbox]):not([type=radio]),.yneko-reimu-settings-subgrid select{width:100%;max-width:100%}.yneko-reimu-field-wide{grid-column:1/-1}.yneko-reimu-settings-panel .form-table{max-width:1040px;margin-top:14px;padding:12px 20px;border:1px solid #dcdcde;border-radius:8px;background:#fff;box-sizing:border-box}.yneko-reimu-settings-panel .form-table th{width:220px;padding-left:16px;padding-right:18px}.yneko-reimu-settings-panel .form-table td{padding-right:16px}.yneko-reimu-floating-submit{position:fixed;z-index:20;right:20px;bottom:0;left:180px;display:flex;align-items:center;justify-content:flex-end;gap:16px;min-height:64px;padding:12px 24px;background:rgba(240,240,241,.94);border-top:1px solid #dcdcde;box-shadow:0 -8px 24px rgba(0,0,0,.08);backdrop-filter:saturate(140%) blur(8px)}.folded .yneko-reimu-floating-submit{left:56px}.yneko-reimu-floating-submit__hint{color:#646970}.yneko-reimu-settings-page h2{margin-top:32px}.yneko-reimu-admin-text{line-height:1.35}.description.yneko-reimu-admin-text,.yneko-reimu-admin-text.description,.yneko-reimu-settings-page p.yneko-reimu-admin-text{display:block;margin:6px 0 0;color:#646970}.yneko-reimu-settings-page .button .yneko-reimu-admin-text{vertical-align:middle}.yneko-reimu-submit-button .yneko-reimu-admin-text{color:#fff}.yneko-reimu-admin-gif-upload{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin:14px 0 18px;padding:12px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-admin-gif-upload label{display:flex;flex-direction:column;gap:6px;font-weight:600}.yneko-reimu-special-badge-table{display:flex;flex-direction:column;gap:10px;max-width:760px}.yneko-reimu-special-badge-row{display:grid;grid-template-columns:130px minmax(120px,1fr) minmax(120px,1fr);gap:8px;align-items:center;padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-special-badge-row-extra{grid-template-columns:90px minmax(110px,.8fr) minmax(120px,1fr) minmax(120px,1fr)}.yneko-reimu-special-badge-row input[type=text]{width:100%}.yneko-reimu-media-field,.yneko-reimu-inline-media{display:flex;gap:8px;align-items:center}.yneko-reimu-media-field input,.yneko-reimu-inline-media input{flex:1 1 auto;min-width:0;max-width:100%}.yneko-reimu-repeatable-row{margin:14px 0;padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-row-heading{display:flex;align-items:center;margin:-2px 0 14px}.yneko-reimu-row-number{display:inline-flex;align-items:center;min-height:24px;padding:3px 10px;border-radius:999px;background:#f6f7f7;color:#1d2327;font-weight:600}.yneko-reimu-row-grid{display:grid;gap:12px}.yneko-reimu-row-grid-friend{grid-template-columns:repeat(4,minmax(0,1fr))}.yneko-reimu-row-grid-music{grid-template-columns:repeat(3,minmax(0,1fr))}.yneko-reimu-row-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.yneko-reimu-row-grid input{width:100%}.yneko-reimu-row-actions{display:flex;gap:8px;margin-top:12px}.yneko-reimu-upload-admin-section{margin-top:22px}.yneko-reimu-upload-admin-section h3{margin:0 0 10px}.yneko-reimu-upload-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-top:14px}.yneko-reimu-upload-admin-card{padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-upload-admin-card img{display:block;width:100%;aspect-ratio:1;object-fit:cover;border-radius:6px;background:#f6f7f7}.yneko-reimu-upload-admin-meta{display:flex;flex-direction:column;gap:3px;margin:9px 0;color:#646970;font-size:12px}.yneko-reimu-upload-admin-meta strong{color:#1d2327}.yneko-reimu-upload-admin-actions{display:flex;flex-wrap:wrap;gap:6px}@media(max-width:1100px){.yneko-reimu-settings-subgrid-3{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:960px){.yneko-reimu-row-grid-friend,.yneko-reimu-row-grid-music,.yneko-reimu-settings-subgrid-2,.yneko-reimu-settings-subgrid-3{grid-template-columns:1fr}.yneko-reimu-floating-submit{left:0;right:0;justify-content:space-between;padding:10px 14px}}@media(max-width:782px){.yneko-reimu-settings-tabs .nav-tab{flex:1 1 150px;margin-right:4px}.yneko-reimu-settings-group,.yneko-reimu-settings-panel .form-table{padding:14px}.yneko-reimu-settings-panel .form-table th,.yneko-reimu-settings-panel .form-table td{display:block;width:100%;padding:10px 0}.yneko-reimu-floating-submit{min-height:74px}.yneko-reimu-floating-submit__hint{display:none}}'
	);
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-admin-gif-upload{align-items:center;padding:16px;background:linear-gradient(180deg,#fff,#fbfbfc)}.yneko-reimu-admin-gif-upload .button{display:inline-flex;align-items:center;justify-content:center;min-height:34px;border-radius:6px;font-weight:600}.yneko-reimu-admin-gif-pick:before{content:"+";margin-right:6px;font-weight:700}.yneko-reimu-admin-gif-media:before{content:"";width:14px;height:14px;margin-right:6px;border:2px solid currentColor;border-radius:3px;box-sizing:border-box}.yneko-reimu-admin-gif-upload .button.is-loading,.yneko-reimu-admin-totp-actions .button.is-loading{pointer-events:none;opacity:.72}.yneko-reimu-upload-admin-actions .button{border-radius:5px}.yneko-reimu-upload-admin-actions .button-link-delete{color:#b32d2e}.yneko-reimu-admin-totp{display:flex;flex-direction:column;gap:12px;max-width:760px}.yneko-reimu-admin-totp-status{display:inline-flex;align-items:center;width:max-content;max-width:100%;padding:4px 10px;border-radius:999px;background:#f6f7f7;color:#1d2327;font-weight:600}.yneko-reimu-admin-totp-status.is-enabled{background:#edfaef;color:#0a7f20}.yneko-reimu-admin-totp-setup{display:grid;grid-template-columns:150px minmax(0,1fr);gap:12px;align-items:start;padding:12px;border:1px solid #dcdcde;border-radius:8px;background:#fbfbfc}.yneko-reimu-admin-totp-setup[hidden],.yneko-reimu-admin-totp-recovery[hidden]{display:none!important}.yneko-reimu-admin-totp-qr{width:132px;height:132px;border:1px solid #dcdcde;border-radius:6px;background:#fff}.yneko-reimu-admin-totp-secret{font-family:Consolas,Monaco,monospace;word-break:break-all}.yneko-reimu-admin-totp-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}.yneko-reimu-admin-totp-recovery{display:flex;flex-direction:column;gap:10px;padding:12px;border:1px solid #dcdcde;border-radius:8px;background:#fbfbfc}.yneko-reimu-admin-totp-recovery__header{display:flex;flex-wrap:wrap;align-items:center;gap:8px 12px}.yneko-reimu-admin-totp-recovery__codes{max-width:100%;margin:0;padding:12px;overflow:auto;border:1px solid #dcdcde;border-radius:6px;background:#fff;font-family:Consolas,Monaco,monospace;line-height:1.65;white-space:pre-wrap}.yneko-reimu-admin-totp-message{margin:0;color:#646970}.yneko-reimu-admin-totp-message.is-error{color:#b32d2e}@media(max-width:782px){.yneko-reimu-admin-totp-setup{grid-template-columns:1fr}.yneko-reimu-admin-totp-qr{width:128px;height:128px}}'
	);
	wp_add_inline_style(
		'yneko-reimu-admin-settings',
		'.yneko-reimu-special-badge-table{max-width:100%;box-sizing:border-box}.yneko-reimu-special-badge-row{display:grid;grid-template-columns:110px minmax(0,1fr) minmax(0,1fr) minmax(0,1.35fr);gap:8px;align-items:center;width:100%;box-sizing:border-box;padding:10px;border:1px solid #dcdcde;border-radius:8px;background:#fff;overflow:hidden}.yneko-reimu-special-badge-row label{min-width:0}.yneko-reimu-special-badge-row label .yneko-reimu-admin-text{display:inline;margin:0}.yneko-reimu-special-badge-row input[type=text]{min-width:0}.yneko-reimu-special-badge-row .description{grid-column:1/-1;margin:0;color:#646970}.yneko-reimu-special-badge-row .yneko-reimu-media-field,.yneko-reimu-special-badge-row .yneko-reimu-inline-media{min-width:0;width:100%;max-width:100%;box-sizing:border-box}.yneko-reimu-special-badge-row .yneko-reimu-media-field .button,.yneko-reimu-special-badge-row .yneko-reimu-inline-media .button{flex:0 0 auto}.yneko-reimu-user-badge-admin{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;margin-top:14px}.yneko-reimu-user-badge-card{padding:14px;border:1px solid #dcdcde;border-radius:8px;background:#fff}.yneko-reimu-user-badge-card__user{display:flex;flex-direction:column;gap:3px;margin-bottom:10px}.yneko-reimu-user-badge-card__user span{color:#646970}.yneko-reimu-user-badge-card__tags{display:flex;flex-direction:column;gap:8px}.yneko-reimu-user-badge-item{display:grid;grid-template-columns:auto 1fr auto;gap:8px;align-items:center;padding:8px;border:1px solid #f0f0f1;border-radius:8px;background:#fbfbfc}.yneko-reimu-user-badge-pill{display:inline-flex;align-items:center;width:max-content;max-width:120px;padding:3px 8px;border-radius:999px;color:var(--badge-color,#2271b1);background:color-mix(in srgb,var(--badge-color,#2271b1) 10%,#fff);border:1px solid color-mix(in srgb,var(--badge-color,#2271b1) 24%,#fff);font-size:12px;font-weight:700}.yneko-reimu-user-badge-actions{display:flex;gap:5px}.yneko-reimu-settings-tabs .nav-tab{position:relative;gap:7px}.yneko-reimu-settings-page h2,.yneko-reimu-settings-page th{position:relative}.yneko-reimu-admin-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;margin-left:7px;padding:0 6px;border-radius:999px;background:#d63638;color:#fff;font-size:11px;font-weight:700;line-height:18px;box-shadow:0 0 0 2px #fff}.nav-tab .yneko-reimu-admin-badge{position:absolute;top:-8px;right:-8px;margin-left:0}@media(max-width:1180px){.yneko-reimu-special-badge-row{grid-template-columns:120px minmax(0,1fr) minmax(0,1fr)}.yneko-reimu-special-badge-row .yneko-reimu-media-field{grid-column:2/4}}@media(max-width:960px){.yneko-reimu-special-badge-row,.yneko-reimu-user-badge-item{grid-template-columns:1fr}.yneko-reimu-special-badge-row .yneko-reimu-media-field{grid-column:auto}}'
	);

	wp_register_script( 'yneko-reimu-admin-settings', YNEKO_REIMU_URI . '/assets/dist/admin-settings.js', array( 'jquery' ), yneko_reimu_asset_version( 'assets/dist/admin-settings.js' ), true );
	wp_enqueue_script( 'yneko-reimu-admin-settings' );
	$admin_i18n = array(
		'locale'          => yneko_reimu_admin_prefers_zh() ? 'zh' : 'en',
		'mediaTitle'      => array( 'zh' => '选择媒体', 'en' => 'Select media' ),
		'useMedia'        => array( 'zh' => '使用此媒体', 'en' => 'Use this media' ),
		'invalidImage'    => array( 'zh' => '请选择此字段允许的图片格式。', 'en' => 'Please choose an image format allowed by this field.' ),
		'choose'          => array( 'zh' => '选择', 'en' => 'Choose' ),
		'remove'          => array( 'zh' => '删除', 'en' => 'Remove' ),
		'deleteUpload'    => array( 'zh' => '确定删除这个评论上传文件吗？', 'en' => 'Delete this comment upload file?' ),
		'adminGifTitle'   => array( 'zh' => '选择 GIF', 'en' => 'Select GIF' ),
		'adminGifUse'     => array( 'zh' => '加入表情库', 'en' => 'Add to library' ),
		'adminGifInvalid' => array( 'zh' => '请选择 GIF 文件。', 'en' => 'Please select a GIF file.' ),
		'adminGifAdded'   => array( 'zh' => 'GIF 已加入表情库。', 'en' => 'GIF added to the library.' ),
		'adminGifFailed'  => array( 'zh' => 'GIF 入库失败。', 'en' => 'Failed to add GIF.' ),
		'totpGenerateFailed' => array( 'zh' => '二次认证密钥生成失败。', 'en' => 'Failed to generate the two-factor secret.' ),
		'totpEnableFailed' => array( 'zh' => '二次认证启用失败。', 'en' => 'Failed to enable two-factor authentication.' ),
		'totpDisableFailed' => array( 'zh' => '二次认证关闭失败。', 'en' => 'Failed to disable two-factor authentication.' ),
		'totpDisableConfirm' => array( 'zh' => '确定关闭当前账号的二次认证吗？', 'en' => 'Disable two-factor authentication for the current account?' ),
		'totpRecoveryGenerateFailed' => array( 'zh' => '恢复码生成失败。', 'en' => 'Failed to generate recovery codes.' ),
		'totpRecoveryGenerateConfirm' => array( 'zh' => '重新生成恢复码会让旧恢复码全部失效，确定继续吗？', 'en' => 'Regenerating recovery codes will invalidate all old codes. Continue?' ),
		'totpRecoveryCopy' => array( 'zh' => '复制恢复码', 'en' => 'Copy recovery codes' ),
		'totpRecoveryCopied' => array( 'zh' => '恢复码已复制。', 'en' => 'Recovery codes copied.' ),
		'totpRecoveryCount' => array( 'zh' => '剩余 %d 个', 'en' => '%d remaining' ),
		'totpEnableAction' => array( 'zh' => '启用二次认证', 'en' => 'Enable two-factor authentication' ),
		'totpDisableAction' => array( 'zh' => '关闭二次认证', 'en' => 'Disable two-factor authentication' ),
		'totpEnabled'     => array( 'zh' => '已开启', 'en' => 'Enabled' ),
		'totpDisabled'    => array( 'zh' => '未开启', 'en' => 'Disabled' ),
		'name'            => array( 'zh' => '名称', 'en' => 'Name' ),
		'url'             => array( 'zh' => '链接', 'en' => 'URL' ),
		'description'     => array( 'zh' => '描述', 'en' => 'Description' ),
		'avatar'          => array( 'zh' => '头像', 'en' => 'Avatar' ),
		'trackTitle'      => array( 'zh' => '歌名', 'en' => 'Track title' ),
		'artist'          => array( 'zh' => '作者', 'en' => 'Artist' ),
		'audio'           => array( 'zh' => '音频', 'en' => 'Audio' ),
		'cover'           => array( 'zh' => '封面', 'en' => 'Cover' ),
		'lyrics'          => array( 'zh' => '歌词 LRC', 'en' => 'Lyrics LRC' ),
		'themeColor'      => array( 'zh' => '主题色', 'en' => 'Theme color' ),
		'friendItem'      => array( 'zh' => '友链', 'en' => 'Friend' ),
		'musicItem'       => array( 'zh' => '曲目', 'en' => 'Track' ),
	);
	wp_add_inline_script(
		'yneko-reimu-admin-settings',
		'window.YNEKO_REIMU_ADMIN_I18N=' . wp_json_encode( $admin_i18n ) . ';',
		'before'
	);
}
add_action( 'admin_enqueue_scripts', 'yneko_reimu_enqueue_settings_admin_assets' );
