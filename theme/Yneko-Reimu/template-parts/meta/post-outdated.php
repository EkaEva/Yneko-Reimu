<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! yneko_reimu_is_meta_enabled( get_the_ID(), '_yneko_reimu_outdated', 'yneko_reimu_show_outdated', true ) ) {
	return;
}

$days      = max( 30, absint( yneko_reimu_get_theme_mod( 'yneko_reimu_outdated_days', 365 ) ) );
$published = get_the_time( 'U' );

if ( ( time() - $published ) < DAY_IN_SECONDS * $days ) {
	return;
}
?>
<div class="warning custom-block reimu-notice--outdated">
	<p class="custom-block-title"><?php esc_html_e( 'WARNING', 'yneko-reimu' ); ?></p>
	<p>
		<?php
		printf(
			/* translators: %s: modified date. */
			esc_html__( '这篇文章最后更新于 %s，部分内容可能已经变化，请结合最新资料判断。', 'yneko-reimu' ),
			esc_html( get_the_modified_date( 'Y-m-d' ) )
		);
		?>
	</p>
</div>
