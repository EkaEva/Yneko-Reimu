<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav id="mobile-nav" aria-label="<?php esc_attr_e( '移动端导航', 'yneko-reimu' ); ?>">
	<div class="sidebar-wrap">
		<?php if ( is_singular( 'post' ) && yneko_reimu_post_has_toc( get_the_ID() ) ) : ?>
			<div class="sidebar-toc-sidebar">
				<?php yneko_reimu_the_toc(); ?>
			</div>
			<div class="sidebar-common-sidebar hidden">
				<?php get_template_part( 'template-parts/layout/sidebar-common', null, array( 'common_only' => true ) ); ?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/layout/sidebar-common' ); ?>
		<?php endif; ?>
	</div>
	<?php if ( is_singular( 'post' ) && yneko_reimu_post_has_toc( get_the_ID() ) ) : ?>
		<div class="sidebar-btn-wrapper">
			<div class="sidebar-toc-btn current"></div>
			<div class="sidebar-common-btn"></div>
		</div>
	<?php endif; ?>
</nav>
