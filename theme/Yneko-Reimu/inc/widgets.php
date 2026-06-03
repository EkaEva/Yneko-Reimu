<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_widgets_init() {
	$shared = array(
		'before_widget' => '<section id="%1$s" class="widget reimu-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Primary Sidebar', 'yneko-reimu' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Main Reimu sidebar.', 'yneko-reimu' ),
			)
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array_merge(
				$shared,
				array(
					'name' => sprintf(
						/* translators: %d: footer widget column number. */
						__( 'Footer Column %d', 'yneko-reimu' ),
						$i
					),
					'id'   => 'footer-' . $i,
				)
			)
		);
	}
}
add_action( 'widgets_init', 'yneko_reimu_widgets_init' );
