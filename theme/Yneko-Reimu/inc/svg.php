<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_svg_upload_enabled() {
	return (bool) apply_filters( 'yneko_reimu_allow_svg_uploads', current_user_can( 'manage_options' ) );
}

function yneko_reimu_svg_sanitize_markup( $svg ) {
	$svg = preg_replace( '/<\?xml.*?\?>/is', '', (string) $svg );
	$svg = preg_replace( '/<!DOCTYPE.*?>/is', '', $svg );
	$svg = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', '', $svg );
	$svg = preg_replace( '/<foreignObject\b[^>]*>.*?<\/foreignObject>/is', '', $svg );
	$svg = preg_replace( '/\s+on[a-z]+\s*=\s*(["\']).*?\1/is', '', $svg );
	$svg = preg_replace( '/\s+on[a-z]+\s*=\s*[^\s>]+/is', '', $svg );
	$svg = preg_replace( '/(href|xlink:href)\s*=\s*(["\'])\s*javascript:.*?\2/is', '$1="#"', $svg );

	return trim( $svg );
}

function yneko_reimu_sanitize_svg_upload( $file ) {
	if ( empty( $file['tmp_name'] ) || empty( $file['name'] ) || 'svg' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
		return $file;
	}

	if ( ! yneko_reimu_svg_upload_enabled() ) {
		$file['error'] = __( '当前用户无权上传 SVG。', 'yneko-reimu' );
		return $file;
	}

	$contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $contents || false === stripos( $contents, '<svg' ) ) {
		$file['error'] = __( '无效的 SVG 文件。', 'yneko-reimu' );
		return $file;
	}

	$sanitized = yneko_reimu_svg_sanitize_markup( $contents );
	if ( '' === $sanitized || false === stripos( $sanitized, '<svg' ) || preg_match( '/<(script|foreignObject)\b/i', $sanitized ) ) {
		$file['error'] = __( 'SVG 包含不安全内容，已拒绝上传。', 'yneko-reimu' );
		return $file;
	}

	file_put_contents( $file['tmp_name'], $sanitized ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'yneko_reimu_sanitize_svg_upload' );

function yneko_reimu_allow_svg_mime( $mimes ) {
	if ( yneko_reimu_svg_upload_enabled() ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'yneko_reimu_allow_svg_mime' );

function yneko_reimu_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	unset( $file, $mimes );

	if ( 'svg' !== strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) ) || ! yneko_reimu_svg_upload_enabled() ) {
		return $data;
	}

	$data['ext']             = 'svg';
	$data['type']            = 'image/svg+xml';
	$data['proper_filename'] = false;
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'yneko_reimu_fix_svg_filetype', 10, 4 );

function yneko_reimu_svg_attachment_metadata( $metadata, $attachment_id ) {
	if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
		return $metadata;
	}

	$metadata = is_array( $metadata ) ? $metadata : array();
	$metadata['width']  = $metadata['width'] ?? 512;
	$metadata['height'] = $metadata['height'] ?? 512;
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'yneko_reimu_svg_attachment_metadata', 10, 2 );

function yneko_reimu_svg_attachment_response( $response, $attachment ) {
	if ( ! $attachment instanceof WP_Post || 'image/svg+xml' !== get_post_mime_type( $attachment ) ) {
		return $response;
	}

	$url = wp_get_attachment_url( $attachment->ID );
	if ( ! $url ) {
		return $response;
	}

	$response['type']   = 'image';
	$response['subtype']= 'svg+xml';
	$response['mime']   = 'image/svg+xml';
	$response['width']  = $response['width'] ?? 512;
	$response['height'] = $response['height'] ?? 512;
	$response['sizes']  = array(
		'full' => array(
			'url'         => $url,
			'width'       => 512,
			'height'      => 512,
			'orientation' => 'landscape',
		),
	);

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'yneko_reimu_svg_attachment_response', 10, 2 );

function yneko_reimu_customizer_svg_note() {
	if ( ! current_user_can( 'customize' ) ) {
		return;
	}

	?>
	<script>
		window.addEventListener('load', function () {
			var sections = document.querySelectorAll('#accordion-section-title_tagline .customize-section-description, #sub-accordion-section-title_tagline');
			sections.forEach(function (section) {
				if (!section || section.querySelector('.yneko-reimu-svg-warning')) {
					return;
				}
				var note = document.createElement('p');
				note.className = 'description yneko-reimu-svg-warning';
				note.textContent = <?php echo wp_json_encode( __( 'Yneko-Reimu 允许管理员为站点图标和 Logo 上传 SVG。SVG 可无限缩放，但也可能携带脚本或外链，请只上传可信来源文件。主题会做基础净化，不能替代专业 SVG 安全插件。', 'yneko-reimu' ) ); ?>;
				section.appendChild(note);
			});

			if (!window.wp || !wp.media || !wp.customize) {
				return;
			}

			document.addEventListener('click', function (event) {
				var button = event.target && event.target.closest ? event.target.closest('#customize-control-site_icon button, #customize-control-site_icon .button') : null;
				if (!button || button.dataset.ynekoSvgSiteIconReady) {
					return;
				}
				button.dataset.ynekoSvgSiteIconReady = 'true';
				window.setTimeout(function () {
					if (!wp.media.frame) {
						return;
					}
					wp.media.frame.on('select', function () {
						var selection = wp.media.frame.state().get('selection');
						var attachment = selection && selection.first ? selection.first().toJSON() : null;
						if (!attachment || attachment.mime !== 'image/svg+xml' || !wp.customize('site_icon')) {
							return;
						}
						wp.customize('site_icon').set(String(attachment.id || ''));
						if (wp.media.frame.close) {
							wp.media.frame.close();
						}
					});
				}, 0);
			});
		});
	</script>
	<style>
		.yneko-reimu-svg-warning {
			margin-top: 12px;
			padding: 0;
			color: #646970;
			background: transparent;
			border: 0;
		}
	</style>
	<?php
}
add_action( 'customize_controls_print_footer_scripts', 'yneko_reimu_customizer_svg_note' );
