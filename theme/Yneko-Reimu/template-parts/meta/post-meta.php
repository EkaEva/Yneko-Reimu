<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="post-meta">
	<span><span class="icon-calendar"></span><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></time></span>
	<?php if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_reading_time', true ) ) : ?>
		<span><span class="icon-pencil"></span><?php echo esc_html( yneko_reimu_word_count( get_the_ID() ) ); ?></span>
		<span><span class="icon-clock"></span><?php echo esc_html( yneko_reimu_reading_time( get_the_ID() ) ); ?></span>
	<?php endif; ?>
</div>
