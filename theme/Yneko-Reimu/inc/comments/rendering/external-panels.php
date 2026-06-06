<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_external_comment_page_context() {
	$locale = str_replace( '_', '-', get_locale() );

	return array(
		'post_url' => get_permalink(),
		'post_id'  => 'post-' . get_the_ID(),
		'lang'     => in_array( $locale, array( 'zh-CN', 'zh-HK', 'zh-TW' ), true ) ? $locale : substr( $locale, 0, 2 ),
	);
}

function yneko_reimu_render_giscus_comment_panel( $config, $context ) {
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
		data-lang="<?php echo esc_attr( $context['lang'] ); ?>"
		crossorigin="anonymous"
		async></script>
	<?php
}

function yneko_reimu_render_utterances_comment_panel( $config ) {
	?>
	<div class="comment utterances-comment" id="utterances-comment"></div>
	<script src="https://utteranc.es/client.js"
		repo="<?php echo esc_attr( $config['repo'] ); ?>"
		issue-term="pathname"
		theme="preferred-color-scheme"
		crossorigin="anonymous"
		async></script>
	<?php
}

function yneko_reimu_render_disqus_comment_panel( $config, $context ) {
	?>
	<div class="comment disqus-comment"><div id="disqus_thread"></div></div>
	<script>
		var disqus_config = function () {
			this.page.url = <?php echo wp_json_encode( $context['post_url'] ); ?>;
			this.page.identifier = <?php echo wp_json_encode( $context['post_id'] ); ?>;
		};
		(function() {
			var d = document, s = d.createElement('script');
			s.src = 'https://' + <?php echo wp_json_encode( $config['shortname'] ); ?> + '.disqus.com/embed.js';
			s.setAttribute('data-timestamp', +new Date());
			(d.head || d.body).appendChild(s);
		})();
	</script>
	<?php
}

function yneko_reimu_render_waline_comment_panel( $config ) {
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
}

function yneko_reimu_render_twikoo_comment_panel( $config ) {
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
}

function yneko_reimu_render_valine_comment_panel( $config ) {
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
}
