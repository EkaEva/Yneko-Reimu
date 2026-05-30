<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title       = is_singular() ? get_the_title() : yneko_reimu_archive_title();
$description = is_singular() ? get_the_excerpt() : yneko_reimu_archive_description();
$banner      = yneko_reimu_get_default_banner_url();
?>
<section class="reimu-hero" style="<?php echo esc_attr( yneko_reimu_background_style( $banner ) ); ?>">
	<div class="reimu-hero__grain" aria-hidden="true"></div>
	<div class="reimu-shell reimu-hero__inner">
		<div class="reimu-hero__copy">
			<p class="reimu-hero__eyebrow"><?php esc_html_e( 'Yneko-Reimu', 'yneko-reimu' ); ?></p>
			<h1 class="reimu-hero__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $description ) : ?>
				<div class="reimu-hero__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
			<?php endif; ?>
			<?php if ( is_singular( 'post' ) ) : ?>
				<?php get_template_part( 'template-parts/meta/post-meta' ); ?>
			<?php endif; ?>
		</div>
		<div class="reimu-hero__figure" aria-hidden="true">
			<img src="<?php echo esc_url( yneko_reimu_get_default_avatar_url() ); ?>" alt="">
			<?php
			if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_taichi', true ) ) {
				get_template_part( 'template-parts/components/taichi' );
			}
			?>
		</div>
	</div>
</section>
