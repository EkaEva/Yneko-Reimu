<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title       = is_singular() ? get_the_title() : yneko_reimu_archive_title();
$description = is_singular() ? get_the_excerpt() : yneko_reimu_archive_description();
$banner      = yneko_reimu_get_default_banner_url();
?>
<section class="yneko-banner" style="<?php echo esc_attr( yneko_reimu_background_style( $banner ) ); ?>">
	<div class="yneko-banner__grain" aria-hidden="true"></div>
	<div class="reimu-shell yneko-banner__inner">
		<div class="yneko-banner__copy">
			<p class="yneko-banner__eyebrow"><?php esc_html_e( 'Yneko-Reimu', 'yneko-reimu' ); ?></p>
			<h1 class="yneko-banner__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $description ) : ?>
				<div class="yneko-banner__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
			<?php endif; ?>
			<?php if ( is_singular( 'post' ) ) : ?>
				<?php get_template_part( 'template-parts/meta/post-meta' ); ?>
			<?php endif; ?>
		</div>
		<div class="yneko-banner__figure" aria-hidden="true">
			<img src="<?php echo esc_url( yneko_reimu_get_default_avatar_url() ); ?>" alt="">
			<?php
			if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_taichi', true ) ) {
				get_template_part( 'template-parts/components/taichi' );
			}
			?>
		</div>
	</div>
</section>
