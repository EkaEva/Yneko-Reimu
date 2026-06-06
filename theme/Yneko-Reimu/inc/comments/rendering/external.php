<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_external_comment_systems() {
	$systems = array();
	$config  = function_exists( 'yneko_reimu_settings_external_comments' ) ? yneko_reimu_settings_external_comments() : array();

	if (
		! empty( $config['giscus_enable'] ) && '1' === (string) $config['giscus_enable']
		&& ! empty( $config['giscus_repo'] )
		&& ! empty( $config['giscus_repo_id'] )
		&& ! empty( $config['giscus_category'] )
		&& ! empty( $config['giscus_category_id'] )
	) {
		$systems['giscus'] = array(
			'label'       => 'giscus',
			'repo'        => $config['giscus_repo'],
			'repo_id'     => $config['giscus_repo_id'],
			'category'    => $config['giscus_category'] ? $config['giscus_category'] : 'General',
			'category_id' => $config['giscus_category_id'],
		);
	}

	if ( ! empty( $config['utterances_enable'] ) && '1' === (string) $config['utterances_enable'] && ! empty( $config['utterances_repo'] ) ) {
		$systems['utterances'] = array(
			'label' => 'utterances',
			'repo'  => $config['utterances_repo'],
		);
	}

	if ( ! empty( $config['disqus_enable'] ) && '1' === (string) $config['disqus_enable'] && ! empty( $config['disqus_shortname'] ) ) {
		$systems['disqus'] = array(
			'label'     => 'disqus',
			'shortname' => $config['disqus_shortname'],
		);
	}

	if ( ! empty( $config['waline_enable'] ) && '1' === (string) $config['waline_enable'] && ! empty( $config['waline_server_url'] ) ) {
		$systems['waline'] = array(
			'label'      => 'waline',
			'server_url' => $config['waline_server_url'],
		);
	}

	if ( ! empty( $config['twikoo_enable'] ) && '1' === (string) $config['twikoo_enable'] && ! empty( $config['twikoo_env_id'] ) ) {
		$systems['twikoo'] = array(
			'label'  => 'twikoo',
			'env_id' => $config['twikoo_env_id'],
		);
	}

	if ( ! empty( $config['valine_enable'] ) && '1' === (string) $config['valine_enable'] && ! empty( $config['valine_app_id'] ) && ! empty( $config['valine_app_key'] ) ) {
		$systems['valine'] = array(
			'label'      => 'valine',
			'app_id'     => $config['valine_app_id'],
			'app_key'    => $config['valine_app_key'],
			'server_url' => $config['valine_server_url'],
		);
	}

	return $systems;
}

function yneko_reimu_render_external_comment_panel( $key, $config ) {
	$context = yneko_reimu_external_comment_page_context();

	switch ( $key ) {
		case 'giscus':
			yneko_reimu_render_giscus_comment_panel( $config, $context );
			break;

		case 'utterances':
			yneko_reimu_render_utterances_comment_panel( $config );
			break;

		case 'disqus':
			yneko_reimu_render_disqus_comment_panel( $config, $context );
			break;

		case 'waline':
			yneko_reimu_render_waline_comment_panel( $config );
			break;

		case 'twikoo':
			yneko_reimu_render_twikoo_comment_panel( $config );
			break;

		case 'valine':
			yneko_reimu_render_valine_comment_panel( $config );
			break;
	}
}
