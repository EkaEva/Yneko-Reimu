<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once YNEKO_REIMU_DIR . '/inc/settings/page/context.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/page/tabs.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/page/general.php';
require_once YNEKO_REIMU_DIR . '/inc/settings/page/submit.php';

function yneko_reimu_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$context = yneko_reimu_settings_page_context();
	?>
	<div class="wrap yneko-reimu-settings-page">
		<h1><?php esc_html_e( 'Yneko-Reimu 设置', 'yneko-reimu' ); ?></h1>
		<?php yneko_reimu_admin_bilingual_description( '这些内容保存在 WordPress 数据库中，不会写入主题源码或主题包。', 'These settings are stored in the WordPress database and are never written into the theme source or release package.' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'yneko_reimu_settings' ); ?>
			<?php yneko_reimu_render_settings_nav_tabs( $context['review_badges'] ); ?>

			<?php yneko_reimu_render_settings_general_panel( $context ); ?>

			<?php yneko_reimu_render_settings_i18n_panel( $context['i18n'] ); ?>

			<?php yneko_reimu_render_settings_github_panel( $context['oauth'], $context['callback'] ); ?>

			<?php yneko_reimu_render_settings_comments_panel( $context['settings'] ); ?>

			<?php yneko_reimu_render_settings_users_panel( $context['review_badges'] ); ?>

			<?php yneko_reimu_render_settings_security_panel( $context['auth_security'], $context['security'], $context['review_badges'] ); ?>

			<?php yneko_reimu_render_settings_search_panel( $context['search'] ); ?>

			<?php yneko_reimu_render_settings_extensions_panel( $context['features'], $context['third_party'] ); ?>
			<?php yneko_reimu_render_settings_external_comments_panel( $context['external_comments'] ); ?>

			<?php yneko_reimu_render_settings_friends_panel( $context['settings'] ); ?>
			<?php yneko_reimu_render_settings_music_panel( $context['settings'], $context['player'] ); ?>

			<?php yneko_reimu_render_settings_floating_submit(); ?>
		</form>
		<?php yneko_reimu_render_settings_hidden_upload_form(); ?>
	</div>
	<?php
}
