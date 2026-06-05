<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_nav_tabs( $review_badges ) {
	$review_badges = is_array( $review_badges ) ? $review_badges : array();
	?>
	<nav class="nav-tab-wrapper yneko-reimu-settings-tabs" aria-label="<?php esc_attr_e( 'Yneko-Reimu 设置分类', 'yneko-reimu' ); ?>">
		<button type="button" class="nav-tab nav-tab-active" data-yneko-settings-tab="general"><?php yneko_reimu_admin_bilingual_label( '常规设置', 'General' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="github"><?php yneko_reimu_admin_bilingual_label( 'GitHub 登录设置', 'GitHub login' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="i18n"><?php yneko_reimu_admin_bilingual_label( '多语言设置', 'Multilingual' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="comments"><?php yneko_reimu_admin_bilingual_label( '评论设置', 'Comments' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['comments'] ?? 0 ) ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="users"><?php yneko_reimu_admin_bilingual_label( '用户设置', 'Users' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['users'] ?? 0 ) ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="security"><?php yneko_reimu_admin_bilingual_label( '安全设置', 'Security' ); ?><?php echo wp_kses_post( yneko_reimu_admin_badge( $review_badges['security'] ?? 0 ) ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="search"><?php yneko_reimu_admin_bilingual_label( '搜索设置', 'Search' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="extensions"><?php yneko_reimu_admin_bilingual_label( '扩展与第三方', 'Extensions' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="external-comments"><?php yneko_reimu_admin_bilingual_label( '外部评论', 'External comments' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="friends"><?php yneko_reimu_admin_bilingual_label( '友链设置', 'Friend links' ); ?></button>
		<button type="button" class="nav-tab" data-yneko-settings-tab="music"><?php yneko_reimu_admin_bilingual_label( '曲目设置', 'Music' ); ?></button>
	</nav>
	<?php
}
