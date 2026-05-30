<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$links = paginate_links(
	array(
		'mid_size'  => 2,
		'prev_text' => __( '上一页', 'yneko-reimu' ),
		'next_text' => __( '下一页 »', 'yneko-reimu' ),
		'type'      => 'array',
	)
);

if ( $links ) :
	?>
	<nav id="page-nav" aria-label="<?php esc_attr_e( '分页导航', 'yneko-reimu' ); ?>" data-aos="fade-up">
		<?php
		foreach ( $links as $link ) {
			echo wp_kses_post( str_replace( 'page-numbers', 'page-number', $link ) );
		}
		?>
	</nav>
	<?php
endif;
