<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_sidebar_post_id = is_singular() ? get_queried_object_id() : 0;

if ( ! yneko_reimu_should_show_sidebar( $reimu_sidebar_post_id ) ) {
	return;
}

$reimu_player_enabled  = ( yneko_reimu_feature_enabled( 'player_aplayer_enable', true ) && yneko_reimu_settings_music_items() ) || yneko_reimu_feature_enabled( 'player_meting_enable', false );
$reimu_player_position = yneko_reimu_player_position();
$reimu_has_toc         = is_singular( 'post' ) && yneko_reimu_post_has_toc( get_the_ID() );
$reimu_render_player   = static function ( $class = '' ) use ( $reimu_player_enabled ) {
	if ( ! $reimu_player_enabled ) {
		return;
	}
	?>
	<?php if ( yneko_reimu_feature_enabled( 'player_meting_enable', false ) ) : ?>
		<meting-js
			theme="var(--color-link)"
			<?php if ( $class ) : ?>class="<?php echo esc_attr( $class ); ?>"<?php endif; ?>
			<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_id', '' ) ) : ?>id="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_id', '' ) ); ?>"<?php endif; ?>
			<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_server', '' ) ) : ?>server="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_server', '' ) ); ?>"<?php endif; ?>
			<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_type', '' ) ) : ?>type="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_type', '' ) ); ?>"<?php endif; ?>
			<?php if ( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_auto', '' ) ) : ?>auto="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_meting_auto', '' ) ); ?>"<?php endif; ?>
			fixed="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_fixed', false ) ? 'true' : 'false' ); ?>"
			autoplay="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_autoplay', false ) ? 'true' : 'false' ); ?>"
			loop="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_loop', 'all' ) ); ?>"
			order="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_order', 'list' ) ); ?>"
			preload="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_preload', 'auto' ) ); ?>"
			volume="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_volume', '0.7' ) ); ?>"
			mutex="<?php echo esc_attr( yneko_reimu_get_theme_mod( 'yneko_reimu_aplayer_mutex', true ) ? 'true' : 'false' ); ?>"
			data-aos="fade-up"
		></meting-js>
	<?php else : ?>
		<div id="aplayer" class="<?php echo esc_attr( $class ); ?>" theme="var(--color-link)" data-aos="fade-up"></div>
	<?php endif; ?>
	<?php
};
?>
<aside id="sidebar" class="<?php echo esc_attr( $reimu_has_toc ? 'has-toc' : 'no-toc' ); ?>" aria-label="<?php esc_attr_e( '侧边栏', 'yneko-reimu' ); ?>">
	<div class="sidebar-wrapper-container<?php echo is_singular( 'post' ) ? ' sticky' : ''; ?>">
		<?php if ( 'before_sidebar' === $reimu_player_position ) : ?>
			<?php $reimu_render_player(); ?>
		<?php endif; ?>
		<div class="sidebar-wrapper">
			<div class="sidebar-wrap" data-aos="fade-up">
				<?php get_template_part( 'template-parts/layout/sidebar-common' ); ?>
				<?php if ( $reimu_has_toc ) : ?>
					<div class="sidebar-btn-wrapper">
						<div class="sidebar-toc-btn current"></div>
						<div class="sidebar-common-btn"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( 'after_sidebar' === $reimu_player_position ) : ?>
			<?php $reimu_render_player( 'aplayer-after-sidebar' ); ?>
		<?php endif; ?>
		<div class="sidebar-widget">
			<?php if ( yneko_reimu_should_show_clone_widgets() ) : ?>
				<?php get_template_part( 'template-parts/widgets/default-widgets', null, array( 'clone_only' => true ) ); ?>
			<?php elseif ( yneko_reimu_should_show_wp_widgets() ) : ?>
				<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-1' ); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/widgets/default-widgets' ); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( 'after_widget' === $reimu_player_position ) : ?>
			<?php $reimu_render_player( 'aplayer-after-widget' ); ?>
		<?php endif; ?>
	</div>
</aside>
