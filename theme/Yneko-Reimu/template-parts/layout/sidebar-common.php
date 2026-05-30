<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yneko_reimu_post_count = wp_count_posts( 'post' )->publish;
$yneko_reimu_cat_count  = wp_count_terms( array( 'taxonomy' => 'category' ) );
$yneko_reimu_tag_count  = wp_count_terms( array( 'taxonomy' => 'post_tag' ) );
$yneko_reimu_cat_count  = is_wp_error( $yneko_reimu_cat_count ) ? 0 : $yneko_reimu_cat_count;
$yneko_reimu_tag_count  = is_wp_error( $yneko_reimu_tag_count ) ? 0 : $yneko_reimu_tag_count;
$reimu_common_only   = ! empty( $args['common_only'] );
$reimu_has_toc       = ! $reimu_common_only && is_singular( 'post' ) && yneko_reimu_post_has_toc( get_the_ID() );
?>
<?php if ( $reimu_has_toc ) : ?>
	<div class="sidebar-toc-sidebar">
		<?php yneko_reimu_the_toc(); ?>
	</div>
	<div class="sidebar-common-sidebar hidden">
<?php endif; ?>

	<div class="sidebar-author">
		<img src="<?php echo esc_url( yneko_reimu_get_default_avatar_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="lazyload">
		<div class="sidebar-author-name"><?php bloginfo( 'name' ); ?></div>
		<div class="sidebar-description"><?php bloginfo( 'description' ); ?></div>
	</div>
	<div class="sidebar-state">
		<div class="sidebar-state-article">
			<div><?php esc_html_e( '文章', 'yneko-reimu' ); ?></div>
			<div class="sidebar-state-number"><?php echo esc_html( $yneko_reimu_post_count ); ?></div>
		</div>
		<div class="sidebar-state-category">
			<div><?php esc_html_e( '分类', 'yneko-reimu' ); ?></div>
			<div class="sidebar-state-number"><?php echo esc_html( $yneko_reimu_cat_count ); ?></div>
		</div>
		<div class="sidebar-state-tag">
			<div><?php esc_html_e( '标签', 'yneko-reimu' ); ?></div>
			<div class="sidebar-state-number"><?php echo esc_html( $yneko_reimu_tag_count ); ?></div>
		</div>
	</div>
	<div class="sidebar-social">
		<?php foreach ( yneko_reimu_social_links() as $key => $link ) : ?>
			<div class="<?php echo esc_attr( yneko_reimu_social_icon_class( $key ) ); ?> sidebar-social-icon">
				<a href="<?php echo esc_url( $link['url'] ); ?>" itemprop="url" target="_blank" aria-label="<?php echo esc_attr( $link['label'] ); ?>" rel="noopener nofollow noreferrer"></a>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="sidebar-menu">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'walker'         => new Yneko_Reimu_Sidebar_Menu_Walker(),
				)
			);
		} else {
			foreach ( yneko_reimu_nav_items() as $item ) :
				?>
				<div class="sidebar-menu-link-wrap">
					<a class="sidebar-menu-link-dummy" href="<?php echo esc_url( $item['url'] ); ?>" aria-label="<?php echo esc_attr( $item['label'] ); ?>"></a>
					<div class="icon rotate sidebar-menu-icon">&#xe62b;</div>
					<div class="sidebar-menu-link"><?php echo esc_html( $item['label'] ); ?></div>
				</div>
				<?php
			endforeach;
		}
		?>
	</div>

<?php if ( $reimu_has_toc ) : ?>
	</div>
<?php endif; ?>
