<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_special_page_slug = yneko_reimu_special_page_slug();
$reimu_archive_like_page = yneko_reimu_is_virtual_page() || $reimu_special_page_slug;
$header_title            = $reimu_archive_like_page ? yneko_reimu_archive_title() : ( is_singular() ? get_the_title() : yneko_reimu_archive_title() );
$summary                 = is_singular() ? yneko_reimu_get_post_meta( get_the_ID(), '_yneko_reimu_summary', true ) : '';
$subtitle                = $reimu_archive_like_page || is_singular( 'post' ) ? '' : ( $summary ? $summary : ( is_singular() ? get_the_excerpt() : yneko_reimu_archive_description() ) );
$banner                  = yneko_reimu_get_default_banner_url();
?>
<div id="header-nav" data-site-header>
	<nav id="main-nav" aria-label="<?php esc_attr_e( '主导航', 'yneko-reimu' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'walker'         => new Yneko_Reimu_Menu_Walker(),
				)
			);
		} else {
			$items = yneko_reimu_nav_items();
			foreach ( $items as $item ) :
				?>
				<a class="main-nav-link-wrap" href="<?php echo esc_url( $item['url'] ); ?>">
					<div class="icon main-nav-icon rotate">&#xe62b;</div>
					<span class="main-nav-link"><?php echo esc_html( $item['label'] ); ?></span>
				</a>
				<?php
			endforeach;
		}
		?>
		<a id="main-nav-toggle" class="nav-icon" aria-label="<?php esc_attr_e( '切换导航', 'yneko-reimu' ); ?>" role="button"></a>
	</nav>
	<nav id="sub-nav" aria-label="<?php esc_attr_e( '辅助导航', 'yneko-reimu' ); ?>">
		<a id="nav-rss-link" class="nav-icon" href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>" title="<?php esc_attr_e( 'RSS 订阅', 'yneko-reimu' ); ?>" aria-label="<?php esc_attr_e( 'RSS 订阅', 'yneko-reimu' ); ?>" target="_blank" rel="noopener"></a>
		<a id="nav-search-btn" class="nav-icon popup-trigger" title="<?php esc_attr_e( '搜索', 'yneko-reimu' ); ?>" aria-label="<?php esc_attr_e( '搜索', 'yneko-reimu' ); ?>" role="button" tabindex="0"></a>
		<?php get_template_part( 'template-parts/components/theme-toggle' ); ?>
	</nav>
	<?php $reimu_i18n_options = yneko_reimu_i18n_options(); ?>
	<?php if ( $reimu_i18n_options ) : ?>
		<nav id="i18n-nav" aria-label="<?php esc_attr_e( '语言选择', 'yneko-reimu' ); ?>">
			<div class="custom-dropdown">
				<div class="select-selected" id="select-selected" role="button" aria-haspopup="listbox" aria-expanded="false" aria-label="<?php esc_attr_e( '语言菜单', 'yneko-reimu' ); ?>">
					<span id="nav-language-btn" class="nav-icon" style="padding:0 20px 0 0"></span>
					<span id="selected-lang">
						<?php
						$selected        = wp_list_filter( $reimu_i18n_options, array( 'selected' => true ) );
						$selected_option = $selected ? reset( $selected ) : $reimu_i18n_options[0];
						echo esc_html( $selected_option['label'] );
						?>
					</span>
				</div>
				<ul class="select-items" id="select-items" role="listbox" aria-label="<?php esc_attr_e( '语言', 'yneko-reimu' ); ?>">
					<?php foreach ( $reimu_i18n_options as $option ) : ?>
						<li data-value="<?php echo esc_attr( $option['code'] ); ?>" data-url="<?php echo esc_url( $option['url'] ); ?>" role="option" <?php echo $option['selected'] ? 'class="selected" aria-selected="true"' : 'aria-selected="false"'; ?>>
							<?php echo esc_html( $option['label'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</nav>
	<?php endif; ?>
	<?php $reimu_github_url = yneko_reimu_settings_github_url(); ?>
	<?php if ( $reimu_github_url && yneko_reimu_theme_mod_bool( 'yneko_reimu_triangle_badge', true ) ) : ?>
		<a href="<?php echo esc_url( $reimu_github_url ); ?>" class="triangle-badge" title="github" target="_blank" rel="noopener nofollow noreferrer">
			<div class="icon icon-github triangle-badge-icon"></div>
		</a>
	<?php endif; ?>
</div>
<header id="header" aria-label="<?php esc_attr_e( '站点头图', 'yneko-reimu' ); ?>">
	<picture>
		<?php foreach ( yneko_reimu_get_banner_srcset( $banner ) as $source ) : ?>
			<source media="<?php echo esc_attr( $source['media'] ); ?>" srcset="<?php echo esc_url( $source['src'] ); ?>">
		<?php endforeach; ?>
		<img fetchpriority="high" src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_attr( $header_title ); ?>">
	</picture>
	<img alt="" style="visibility:hidden">
	<div id="header-outer">
		<div id="header-title">
			<span id="logo">
				<h1 data-aos="slide-up"><?php echo esc_html( $header_title ); ?></h1>
			</span>
			<?php if ( $subtitle ) : ?>
				<h2 id="subtitle-wrap" data-aos="slide-down">
					<span id="subtitle"><?php echo esc_html( wp_strip_all_tags( $subtitle ) ); ?></span>
				</h2>
			<?php endif; ?>
		</div>
	</div>
</header>
