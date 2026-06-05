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
	$post_url = get_permalink();
	$post_id  = 'post-' . get_the_ID();
	$locale   = str_replace( '_', '-', get_locale() );
	$lang     = in_array( $locale, array( 'zh-CN', 'zh-HK', 'zh-TW' ), true ) ? $locale : substr( $locale, 0, 2 );

	switch ( $key ) {
		case 'giscus':
			?>
			<div class="comment giscus-comment" id="giscus-comment" data-aos="fade-up"></div>
			<script src="https://giscus.app/client.js"
				data-repo="<?php echo esc_attr( $config['repo'] ); ?>"
				data-repo-id="<?php echo esc_attr( $config['repo_id'] ); ?>"
				data-category="<?php echo esc_attr( $config['category'] ); ?>"
				data-category-id="<?php echo esc_attr( $config['category_id'] ); ?>"
				data-mapping="pathname"
				data-strict="0"
				data-reactions-enabled="1"
				data-emit-metadata="0"
				data-input-position="bottom"
				data-theme="preferred_color_scheme"
				data-lang="<?php echo esc_attr( $lang ); ?>"
				crossorigin="anonymous"
				async></script>
			<?php
			break;

		case 'utterances':
			?>
			<div class="comment utterances-comment" id="utterances-comment"></div>
			<script src="https://utteranc.es/client.js"
				repo="<?php echo esc_attr( $config['repo'] ); ?>"
				issue-term="pathname"
				theme="preferred-color-scheme"
				crossorigin="anonymous"
				async></script>
			<?php
			break;

		case 'disqus':
			?>
			<div class="comment disqus-comment"><div id="disqus_thread"></div></div>
			<script>
				var disqus_config = function () {
					this.page.url = <?php echo wp_json_encode( $post_url ); ?>;
					this.page.identifier = <?php echo wp_json_encode( $post_id ); ?>;
				};
				(function() {
					var d = document, s = d.createElement('script');
					s.src = 'https://' + <?php echo wp_json_encode( $config['shortname'] ); ?> + '.disqus.com/embed.js';
					s.setAttribute('data-timestamp', +new Date());
					(d.head || d.body).appendChild(s);
				})();
			</script>
			<?php
			break;

		case 'waline':
			?>
			<div class="comment waline-comment" id="waline-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.Waline) {
						window.Waline.init({ el: '#waline-comment', serverURL: <?php echo wp_json_encode( esc_url_raw( $config['server_url'] ) ); ?>, path: location.pathname });
					}
				});
			</script>
			<?php
			break;

		case 'twikoo':
			?>
			<div class="comment twikoo-comment" id="twikoo-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.twikoo) {
						window.twikoo.init({ envId: <?php echo wp_json_encode( $config['env_id'] ); ?>, el: '#twikoo-comment', path: location.pathname });
					}
				});
			</script>
			<?php
			break;

		case 'valine':
			?>
			<div class="comment valine-comment" id="valine-comment"></div>
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					if (window.Valine) {
						new window.Valine({
							el: '#valine-comment',
							appId: <?php echo wp_json_encode( $config['app_id'] ); ?>,
							appKey: <?php echo wp_json_encode( $config['app_key'] ); ?>,
							serverURLs: <?php echo wp_json_encode( $config['server_url'] ); ?> || undefined,
							path: location.pathname
						});
					}
				});
			</script>
			<?php
			break;
	}
}
