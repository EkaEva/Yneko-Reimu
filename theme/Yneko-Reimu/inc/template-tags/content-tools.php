<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_home_category_capsules() {
	$defaults = array(
		1 => array(
			'title' => __( 'Yneko', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'yneko' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'yneko' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
		2 => array(
			'title' => __( '学习笔记', 'yneko-reimu' ),
			'url'   => yneko_reimu_category_link_by_slug( 'study-notes' ),
			'count' => yneko_reimu_count_text( yneko_reimu_term_count_with_children_by_slug( 'category', 'study-notes' ) ),
			'cover' => yneko_reimu_get_default_cover_url(),
		),
	);
	$items    = array();

	foreach ( $defaults as $index => $default ) {
		$title = trim( (string) yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_title', $default['title'] ) );
		$url   = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_url', $default['url'] );
		$count = $default['count'];
		$cover = yneko_reimu_get_theme_mod( 'yneko_reimu_home_category_' . $index . '_cover', '' );

		if ( 1 === $index ) {
			if ( in_array( strtolower( $title ), array( 'hexo', 'project' ), true ) || '项目' === $title ) {
				$title = $default['title'];
			}

			if (
				in_array(
					yneko_reimu_normalize_theme_url( $url ),
					array(
						home_url( '/category/hexo/' ),
						home_url( '/category/project/' ),
						home_url( '/category/项目/' ),
						home_url( '/category/%e9%a1%b9%e7%9b%ae/' ),
						home_url( '/project/' ),
						home_url( '/项目/' ),
					),
					true
				)
			) {
				$url = $default['url'];
			}

		}

		if ( '' === $cover ) {
			$cover = $default['cover'];
		}

		if ( 1 === $index ) {
			$cover_url = yneko_reimu_normalize_theme_url( $cover );
			if (
				in_array(
					$cover_url,
					array(
						yneko_reimu_asset_uri( 'assets/images/banner.png' ),
						yneko_reimu_asset_uri( 'assets/images/banner.webp' ),
						yneko_reimu_get_default_banner_url(),
					),
					true
				)
			) {
				$cover = $default['cover'];
			}
		}

		if ( 2 === $index ) {
			$cover_url = yneko_reimu_normalize_theme_url( $cover );
			if (
				in_array(
					$cover_url,
					array(
						yneko_reimu_asset_uri( 'assets/images/banner.png' ),
						yneko_reimu_asset_uri( 'assets/images/banner.webp' ),
						yneko_reimu_get_default_banner_url(),
					),
					true
				)
			) {
				$cover = $default['cover'];
			}
		}

		$items[] = array(
			'title' => '' === $title ? $default['title'] : $title,
			'url'   => yneko_reimu_normalize_theme_url( $url, $default['url'] ),
			'count' => '' === $count ? $default['count'] : $count,
			'cover' => esc_url_raw( $cover ),
		);
	}

	return $items;
}

function yneko_reimu_should_show_wp_widgets() {
	return false;
}

function yneko_reimu_should_show_clone_widgets() {
	return yneko_reimu_theme_mod_bool( 'yneko_reimu_strict_clone', true );
}

function yneko_reimu_yml_value_html( $value ) {
	$value = (string) $value;

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/^\s*#/', $value ) ) {
		return '<span class="comment">' . esc_html( $value ) . '</span>';
	}

	return '<span class="string">' . esc_html( $value ) . '</span>';
}

function yneko_reimu_yml_line_html( $line ) {
	$line = (string) $line;

	if ( preg_match( '/^```/', $line ) ) {
		return '<span class="string">' . esc_html( $line ) . '</span>';
	}

	if ( preg_match( '/^(\s*)(-\s+)?([A-Za-z0-9_-]+:)(\s*)(.*)$/u', $line, $matches ) ) {
		$html = esc_html( $matches[1] );

		if ( '' !== $matches[2] ) {
			$html .= '<span class="bullet">-</span> ';
		}

		$html .= '<span class="attr">' . esc_html( $matches[3] ) . '</span>';
		$html .= esc_html( $matches[4] );
		$html .= yneko_reimu_yml_value_html( $matches[5] );

		return $html;
	}

	return esc_html( $line );
}

function yneko_reimu_yml_editor( $code, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'lang'  => 'YML',
			'class' => '',
		)
	);

	$code = trim( str_replace( array( "\r\n", "\r" ), "\n", (string) $code ), "\n" );
	if ( '' === $code ) {
		return '';
	}

	$lines        = explode( "\n", $code );
	$line_numbers = array();
	$code_lines   = array();

	foreach ( $lines as $index => $line ) {
		$line_numbers[] = '<span class="line">' . esc_html( (string) ( $index + 1 ) ) . '</span>';
		$code_lines[]   = '<span class="line">' . yneko_reimu_yml_line_html( $line ) . '</span>';
	}

	$extra_class = trim( (string) $args['class'] );
	$class_attr  = 'highlight yml reimu-yml-editor' . ( $extra_class ? ' ' . $extra_class : '' );

	return sprintf(
		'<figure class="%1$s" data-copy-text="%2$s"><div class="code-figcaption"><div class="code-left-wrap"><div class="code-decoration"></div><div class="code-lang">%3$s</div></div><div class="code-right-wrap"><button type="button" class="code-copy icon-copy" aria-label="%4$s"></button><button type="button" class="icon-chevron-down code-expand" aria-label="%5$s" aria-expanded="true"></button></div></div><div class="code-area"><table><tr><td class="gutter"><pre>%6$s<br></pre></td><td class="code"><pre>%7$s<br></pre></td></tr></table></div></figure>',
		esc_attr( $class_attr ),
		esc_attr( $code ),
		esc_html( strtoupper( (string) $args['lang'] ) ),
		esc_attr__( '复制', 'yneko-reimu' ),
		esc_attr__( '折叠代码', 'yneko-reimu' ),
		implode( '<br>', $line_numbers ),
		implode( '<br>', $code_lines )
	);
}

function yneko_reimu_sponsor_html() {
	$qr = yneko_reimu_get_sponsor_qr_url();
	if ( ! $qr ) {
		return '';
	}

	return sprintf(
		'<footer class="article-footer"><div class="sponsor-wrapper" data-aos="zoom-in"><div class="sponsor-button" role="button" tabindex="0" aria-expanded="false"><div class="sponsor-icon rotate"></div><div class="sponsor-title">%1$s</div><div class="sponsor-icon rotate"></div></div><div class="sponsor-tip">%2$s</div><div class="sponsor-qr"><div class="sponsor-qr-image-wrapper"><img class="lazyload no-lightbox" src="%3$s" data-src="%3$s" alt="%4$s" width="170"><p class="sponsor-qr-name">%5$s</p></div></div></div></footer>',
		esc_html__( '赞助', 'yneko-reimu' ),
		esc_html__( '无限进步', 'yneko-reimu' ),
		esc_url( $qr ),
		esc_attr__( '赞助二维码', 'yneko-reimu' ),
		esc_html__( '赞助', 'yneko-reimu' )
	);
}

function yneko_reimu_sponsor_shortcode() {
	return yneko_reimu_sponsor_html();
}

add_shortcode( 'yneko_reimu_sponsor', 'yneko_reimu_sponsor_shortcode' );

function yneko_reimu_friend_items() {
	$settings_friends = yneko_reimu_settings_friend_items();
	if ( $settings_friends ) {
		return array_map(
			static function ( $friend ) {
				$friend['image'] = $friend['image'] ? $friend['image'] : yneko_reimu_get_default_avatar_url();
				return $friend;
			},
			$settings_friends
		);
	}

	$raw = (string) yneko_reimu_get_theme_mod( 'yneko_reimu_friend_links', '' );
	$items = array();
	$seen  = array();

	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		$parts = array_pad( array_map( 'trim', explode( '|', $line ) ), 4, '' );
		if ( '' === $parts[0] || '' === $parts[1] ) {
			continue;
		}

		if ( ! preg_match( '#^https?://#i', $parts[1] ) ) {
			$parts[1] = 'https://' . ltrim( $parts[1], '/' );
		}

		$key = strtolower( untrailingslashit( $parts[1] ) );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;

		$items[] = array(
			'name'  => $parts[0],
			'url'   => esc_url_raw( $parts[1] ),
			'desc'  => $parts[2],
			'image' => esc_url_raw( $parts[3] ? $parts[3] : yneko_reimu_get_default_avatar_url() ),
		);
	}

	return $items ? $items : yneko_reimu_settings_friend_items();
}

function yneko_reimu_github_username() {
	$github = trim( (string) yneko_reimu_settings_github_url() );

	if ( '' === $github ) {
		return '';
	}

	$path = (string) wp_parse_url( $github, PHP_URL_PATH );
	$path = trim( $path, '/' );

	if ( '' === $path || false !== strpos( $path, '/' ) ) {
		return '';
	}

	$username = preg_replace( '/[^A-Za-z0-9-]/', '', $path );
	return $username ? $username : '';
}

function yneko_reimu_project_fallback_items() {
	$username = yneko_reimu_github_username();

	if ( ! $username ) {
		return array();
	}

	return array(
		array(
			'name'        => $username,
			'url'         => 'https://github.com/' . rawurlencode( $username ),
			'desc'        => __( '我的 GitHub 主页与项目索引。', 'yneko-reimu' ),
			'image'       => yneko_reimu_get_default_avatar_url(),
			'language'    => 'GitHub',
			'stars'       => 0,
			'updated_at'  => '',
			'is_fallback' => true,
		),
	);
}

function yneko_reimu_normalize_github_repo_items( $repos, $limit = 12 ) {
	usort(
		$repos,
		static function ( $a, $b ) {
			$a_fork = ! empty( $a['fork'] ) ? 1 : 0;
			$b_fork = ! empty( $b['fork'] ) ? 1 : 0;

			if ( $a_fork !== $b_fork ) {
				return $a_fork <=> $b_fork;
			}

			return strcmp( (string) ( $b['updated_at'] ?? '' ), (string) ( $a['updated_at'] ?? '' ) );
		}
	);

	$items = array();
	foreach ( $repos as $repo ) {
		if ( empty( $repo['name'] ) || empty( $repo['html_url'] ) ) {
			continue;
		}

		$owner = isset( $repo['owner'] ) && is_array( $repo['owner'] ) ? $repo['owner'] : array();

		$items[] = array(
			'name'       => (string) $repo['name'],
			'url'        => esc_url_raw( (string) $repo['html_url'] ),
			'desc'       => ! empty( $repo['description'] ) ? (string) $repo['description'] : __( 'GitHub 项目', 'yneko-reimu' ),
			'image'      => ! empty( $owner['avatar_url'] ) ? esc_url_raw( (string) $owner['avatar_url'] ) : yneko_reimu_get_default_avatar_url(),
			'language'   => ! empty( $repo['language'] ) ? (string) $repo['language'] : '',
			'stars'      => isset( $repo['stargazers_count'] ) ? absint( $repo['stargazers_count'] ) : 0,
			'updated_at' => ! empty( $repo['updated_at'] ) ? (string) $repo['updated_at'] : '',
			'is_fork'    => ! empty( $repo['fork'] ),
		);

		if ( count( $items ) >= $limit ) {
			break;
		}
	}

	return $items;
}

function yneko_reimu_github_api_get( $path, $transient_key, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$headers = array(
		'Accept'     => 'application/vnd.github+json',
		'User-Agent' => 'yneko-reimu/' . wp_get_theme()->get( 'Version' ) . '; ' . home_url( '/' ),
	);
	$token   = '';
	if ( defined( 'YNEKO_REIMU_GITHUB_TOKEN' ) ) {
		$token = trim( (string) YNEKO_REIMU_GITHUB_TOKEN );
	}
	$token = trim( (string) apply_filters( 'yneko_reimu_github_token', $token ) );
	if ( '' !== $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}

	$response = wp_remote_get(
		'https://api.github.com' . $path,
		array(
			'timeout' => 4,
			'headers' => $headers,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) ) {
		set_transient( $transient_key, $fallback, HOUR_IN_SECONDS );
		return $fallback;
	}

	set_transient( $transient_key, $data, $cache_seconds );
	return $data;
}

function yneko_reimu_github_api_get_pages( $path_template, $transient_key, $pages = 1, $fallback = array(), $cache_seconds = 21600 ) {
	$cached = get_transient( $transient_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$items = array();
	$pages = max( 1, min( 5, absint( $pages ) ) );

	for ( $page = 1; $page <= $pages; $page++ ) {
		$path = str_replace( '%d', (string) $page, $path_template );
		$data = yneko_reimu_github_api_get( $path, $transient_key . '_page_' . $page, array(), $cache_seconds );
		if ( ! is_array( $data ) || ! $data ) {
			break;
		}
		$items = array_merge( $items, $data );
		if ( count( $data ) < 100 ) {
			break;
		}
	}

	if ( ! $items ) {
		$items = $fallback;
	}

	set_transient( $transient_key, $items, $cache_seconds );
	return $items;
}

function yneko_reimu_github_projects() {
	$username      = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_projects_' . md5( strtolower( $username ) );
	$fallback      = yneko_reimu_project_fallback_items();
	$repos         = yneko_reimu_github_api_get_pages(
		'/users/' . rawurlencode( $username ) . '/repos?sort=updated&per_page=100&page=%d',
		$transient_key . '_raw_v2',
		1,
		array(),
		6 * HOUR_IN_SECONDS
	);

	$items = yneko_reimu_normalize_github_repo_items( $repos, 48 );

	if ( ! $items ) {
		$items = $fallback;
	}

	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );
	return $items;
}

function yneko_reimu_github_starred_projects() {
	$username      = yneko_reimu_github_username();
	if ( ! $username ) {
		return array();
	}

	$transient_key = 'yneko_reimu_github_starred_projects_' . md5( strtolower( $username ) );
	$repos         = yneko_reimu_github_api_get_pages(
		'/users/' . rawurlencode( $username ) . '/starred?sort=updated&per_page=100&page=%d',
		$transient_key . '_raw_v2',
		3,
		array(),
		6 * HOUR_IN_SECONDS
	);

	$items = yneko_reimu_normalize_github_repo_items( $repos, 240 );
	set_transient( $transient_key, $items, 6 * HOUR_IN_SECONDS );

	return $items;
}

function yneko_reimu_post_is_sticky( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$meta    = yneko_reimu_get_post_meta( $post_id, '_yneko_reimu_sticky', true );

	if ( '1' === $meta ) {
		return true;
	}

	if ( '0' === $meta ) {
		return false;
	}

	if ( function_exists( 'yneko_reimu_get_visual_source_post_id' ) ) {
		$source_id = yneko_reimu_get_visual_source_post_id( $post_id );
		if ( $source_id ) {
			$source_meta = yneko_reimu_get_post_meta( $source_id, '_yneko_reimu_sticky', true );
			if ( '1' === $source_meta ) {
				return true;
			}

			if ( '0' === $source_meta ) {
				return false;
			}

			return is_sticky( $source_id );
		}
	}

	return is_sticky( $post_id );
}

function yneko_reimu_total_word_count() {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$total = 0;

	foreach ( $query->posts as $post_id ) {
		$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
		$total += str_word_count( preg_replace( '/[\x{4e00}-\x{9fff}]/u', ' ', $text ) );
		$total += preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $text );
	}

	if ( $total >= 1000 ) {
		return round( $total / 1000, 1 ) . 'k';
	}

	return (string) $total;
}

function yneko_reimu_render_taichi_svg( $size = 150 ) {
	?>
	<svg width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" aria-hidden="true" focusable="false">
		<path d="M303.5 432A80 80 0 0 1 291.5 592A80 80 0 0 1 303.5 432z" fill="var(--red-1, #ff5252)" />
		<path d="M512 65A447 447 0 0 1 512 959L512 929A417 417 0 0 0 512 95A417 417 0 0 0 512 929L512 959A447 447 0 0 1 512 65z M512 95A417 417 0 0 1 929 512A208.5 208.5 0 0 1 720.5 720.5L720.5 592A80 80 0 0 0 720.5 432A80 80 0 0 0 720.5 592L720.5 720.5A208.5 208.5 0 0 1 512 512A208.5 208.5 0 0 0 303.5 303.5A208.5 208.5 0 0 0 95 512A417 417 0 0 1 512 95z" fill="var(--red-1, #ff5252)" />
	</svg>
	<?php
}
