<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_clone_only = ! empty( $args['clone_only'] );
?>
<div class="widget-wrapper">
	<div class="widget-wrap" data-aos="fade-up">
		<h3 class="widget-title"><?php esc_html_e( '标签云', 'yneko-reimu' ); ?></h3>
		<div class="tagcloud widget">
			<?php wp_tag_cloud( array( 'smallest' => 10, 'largest' => 20, 'unit' => 'px' ) ); ?>
		</div>
	</div>
</div>
<?php if ( ! $reimu_clone_only ) : ?>
<div class="widget-wrapper">
	<div class="widget-wrap" data-aos="fade-up">
		<h3 class="widget-title"><?php esc_html_e( '归档', 'yneko-reimu' ); ?></h3>
		<ul><?php wp_get_archives( array( 'type' => 'monthly' ) ); ?></ul>
	</div>
</div>
<?php endif; ?>
