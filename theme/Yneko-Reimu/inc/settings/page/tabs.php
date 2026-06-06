<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_render_settings_nav_tabs( $review_badges ) {
	$review_badges = is_array( $review_badges ) ? $review_badges : array();
	?>
	<nav class="nav-tab-wrapper yneko-reimu-settings-tabs" aria-label="<?php esc_attr_e( 'Yneko-Reimu 设置分类', 'yneko-reimu' ); ?>">
		<?php foreach ( yneko_reimu_settings_tab_definitions() as $tab ) : ?>
			<?php yneko_reimu_render_settings_nav_tab( $tab, $review_badges ); ?>
		<?php endforeach; ?>
	</nav>
	<?php
}

function yneko_reimu_settings_tab_definitions() {
	return array(
		array( 'general', '常规设置', 'General', '' ),
		array( 'github', 'GitHub 登录设置', 'GitHub login', '' ),
		array( 'i18n', '多语言设置', 'Multilingual', '' ),
		array( 'comments', '评论设置', 'Comments', 'comments' ),
		array( 'users', '用户设置', 'Users', 'users' ),
		array( 'security', '安全设置', 'Security', 'security' ),
		array( 'search', '搜索设置', 'Search', '' ),
		array( 'extensions', '扩展与第三方', 'Extensions', '' ),
		array( 'external-comments', '外部评论', 'External comments', '' ),
		array( 'friends', '友链设置', 'Friend links', '' ),
		array( 'music', '曲目设置', 'Music', '' ),
	);
}

function yneko_reimu_render_settings_nav_tab( $tab, $review_badges ) {
	$slug = $tab[0];
	?>
	<button type="button" class="nav-tab<?php echo 'general' === $slug ? ' nav-tab-active' : ''; ?>" data-yneko-settings-tab="<?php echo esc_attr( $slug ); ?>"><?php yneko_reimu_admin_bilingual_label( $tab[1], $tab[2] ); ?><?php echo wp_kses_post( $tab[3] ? yneko_reimu_admin_badge( $review_badges[ $tab[3] ] ?? 0 ) : '' ); ?></button>
	<?php
}
