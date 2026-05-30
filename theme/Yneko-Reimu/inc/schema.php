<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_schema_is_enabled() {
	return (bool) apply_filters( 'yneko_reimu_schema_enabled', true );
}

function yneko_reimu_schema_graph() {
	$site_id = home_url( '#website' );
	$graph   = array(
		array(
			'@type' => 'WebSite',
			'@id'   => $site_id,
			'url'   => home_url( '/' ),
			'name'  => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'inLanguage'  => get_bloginfo( 'language' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => home_url( '/?s={search_term_string}' ),
				'query-input' => 'required name=search_term_string',
			),
		),
	);

	if ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();
		$image   = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : yneko_reimu_get_default_banner_url();
		$graph[] = array_filter(
			array(
				'@type' => 'BlogPosting',
				'@id'   => get_permalink( $post_id ) . '#blogposting',
				'url'   => get_permalink( $post_id ),
				'headline' => wp_strip_all_tags( get_the_title( $post_id ) ),
				'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
				'image' => $image ? array( esc_url_raw( $image ) ) : null,
				'datePublished' => get_the_date( DATE_W3C, $post_id ),
				'dateModified'  => get_the_modified_date( DATE_W3C, $post_id ),
				'inLanguage'    => get_bloginfo( 'language' ),
				'author'        => array(
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ),
				),
				'publisher'     => array(
					'@type' => 'Organization',
					'name'  => get_bloginfo( 'name' ),
					'logo'  => array(
						'@type' => 'ImageObject',
						'url'   => yneko_reimu_get_site_logo_url(),
					),
				),
				'isPartOf'      => array( '@id' => $site_id ),
			)
		);
	}

	if ( ! is_front_page() ) {
		$items = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( '首页', 'yneko-reimu' ),
				'item'     => home_url( '/' ),
			),
		);

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => wp_get_document_title(),
			'item'     => is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ),
		);

		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	return apply_filters( 'yneko_reimu_schema_graph', $graph );
}

function yneko_reimu_schema_json_ld() {
	if ( ! yneko_reimu_schema_is_enabled() ) {
		return;
	}

	$graph = yneko_reimu_schema_graph();
	if ( ! $graph ) {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	) . '</script>' . "\n";
}
add_action( 'wp_head', 'yneko_reimu_schema_json_ld', 7 );
