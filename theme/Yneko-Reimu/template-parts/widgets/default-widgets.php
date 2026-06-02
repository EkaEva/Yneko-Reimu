<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_allowed_widgets = array( 'tagcloud', 'projects', 'recent_posts', 'categories', 'archives', 'recent_comments' );
$reimu_order = array_filter(
	array_map( 'sanitize_key', array_map( 'trim', explode( ',', (string) yneko_reimu_get_theme_mod( 'yneko_reimu_sidebar_widget_order', 'tagcloud,projects,recent_posts,categories,archives,recent_comments' ) ) ) )
);

foreach ( $reimu_allowed_widgets as $reimu_widget_key ) {
	if ( ! in_array( $reimu_widget_key, $reimu_order, true ) ) {
		$reimu_order[] = $reimu_widget_key;
	}
}

if ( ! function_exists( 'yneko_reimu_sidebar_widget_enabled' ) ) {
	function yneko_reimu_sidebar_widget_enabled( $key ) {
		$default = 'tagcloud' === $key;
		return (bool) yneko_reimu_get_theme_mod( 'yneko_reimu_sidebar_widget_' . $key, $default );
	}
}

if ( ! function_exists( 'yneko_reimu_sidebar_widget_limit' ) ) {
	function yneko_reimu_sidebar_widget_limit( $key, $default = 5 ) {
		$limit = absint( yneko_reimu_get_theme_mod( 'yneko_reimu_sidebar_widget_' . $key . '_limit', $default ) );
		return max( 1, min( 20, $limit ) );
	}
}

if ( ! function_exists( 'yneko_reimu_sidebar_widget_wrap' ) ) {
	function yneko_reimu_sidebar_widget_wrap( $title, $callback ) {
		ob_start();
		call_user_func( $callback );
		$content = trim( ob_get_clean() );
		if ( '' === $content ) {
			return;
		}
		?>
		<div class="widget-wrapper">
			<div class="widget-wrap" data-aos="fade-up">
				<h3 class="widget-title"><?php echo esc_html( $title ); ?></h3>
				<div class="widget">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_tagcloud_widget' ) ) {
	function yneko_reimu_render_sidebar_tagcloud_widget() {
		yneko_reimu_sidebar_widget_wrap(
			__( '标签云', 'yneko-reimu' ),
			static function () {
				echo '<div class="tagcloud">';
				wp_tag_cloud( array( 'smallest' => 10, 'largest' => 20, 'unit' => 'px' ) );
				echo '</div>';
			}
		);
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_projects_widget' ) ) {
	function yneko_reimu_render_sidebar_projects_widget() {
		$projects = function_exists( 'yneko_reimu_github_projects' ) ? array_slice( yneko_reimu_github_projects(), 0, yneko_reimu_sidebar_widget_limit( 'projects', 5 ) ) : array();
		yneko_reimu_sidebar_widget_wrap(
			__( '项目', 'yneko-reimu' ),
			static function () use ( $projects ) {
				if ( ! $projects ) {
					return;
				}
				echo '<ul class="reimu-sidebar-list reimu-sidebar-projects">';
				foreach ( $projects as $project ) {
					$name = (string) ( $project['name'] ?? '' );
					$url  = (string) ( $project['url'] ?? '' );
					if ( '' === $name || '' === $url ) {
						continue;
					}
					echo '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener nofollow noreferrer">' . esc_html( $name ) . '</a>';
					if ( isset( $project['stars'] ) ) {
						echo '<span>' . esc_html( 'Star ' . absint( $project['stars'] ) ) . '</span>';
					}
					echo '</li>';
				}
				echo '</ul>';
			}
		);
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_recent_posts_widget' ) ) {
	function yneko_reimu_render_sidebar_recent_posts_widget() {
		$args = array(
			'numberposts' => yneko_reimu_sidebar_widget_limit( 'recent_posts', 5 ),
			'post_status' => 'publish',
		);

		if ( function_exists( 'yneko_reimu_i18n_apply_language_query_args' ) ) {
			$args = yneko_reimu_i18n_apply_language_query_args( $args );
		}

		$posts = get_posts( $args );
		yneko_reimu_sidebar_widget_wrap(
			__( '近期文章', 'yneko-reimu' ),
			static function () use ( $posts ) {
				if ( ! $posts ) {
					return;
				}
				echo '<ul class="reimu-sidebar-list reimu-sidebar-recent-posts">';
				foreach ( $posts as $post ) {
					echo '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>';
				}
				echo '</ul>';
			}
		);
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_recent_comments_widget' ) ) {
	function yneko_reimu_render_sidebar_recent_comments_widget() {
		$comments = get_comments(
			array(
				'number' => yneko_reimu_sidebar_widget_limit( 'recent_comments', 5 ),
				'status' => 'approve',
				'type'   => 'comment',
			)
		);
		yneko_reimu_sidebar_widget_wrap(
			__( '近期评论', 'yneko-reimu' ),
			static function () use ( $comments ) {
				if ( ! $comments ) {
					return;
				}
				echo '<ul class="reimu-sidebar-list reimu-sidebar-recent-comments">';
				foreach ( $comments as $comment ) {
					$text = wp_trim_words( wp_strip_all_tags( $comment->comment_content ), 10, '...' );
					echo '<li><a href="' . esc_url( get_comment_link( $comment ) ) . '">' . esc_html( $comment->comment_author ) . '</a><span>' . esc_html( $text ) . '</span></li>';
				}
				echo '</ul>';
			}
		);
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_archives_widget' ) ) {
	function yneko_reimu_render_sidebar_archives_widget() {
		yneko_reimu_sidebar_widget_wrap(
			__( '归档', 'yneko-reimu' ),
			static function () {
				echo '<ul class="reimu-sidebar-list reimu-sidebar-archives">';
				wp_get_archives(
					array(
						'type'  => 'monthly',
						'limit' => yneko_reimu_sidebar_widget_limit( 'archives', 8 ),
					)
				);
				echo '</ul>';
			}
		);
	}
}

if ( ! function_exists( 'yneko_reimu_render_sidebar_categories_widget' ) ) {
	function yneko_reimu_render_sidebar_categories_widget() {
		$categories = get_categories(
			array(
				'number'     => yneko_reimu_sidebar_widget_limit( 'categories', 8 ),
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		yneko_reimu_sidebar_widget_wrap(
			__( '分类', 'yneko-reimu' ),
			static function () use ( $categories ) {
				if ( ! $categories ) {
					return;
				}
				echo '<ul class="reimu-sidebar-list reimu-sidebar-categories">';
				foreach ( $categories as $category ) {
					echo '<li><a href="' . esc_url( get_category_link( $category ) ) . '">' . esc_html( $category->name ) . '</a><span>' . esc_html( absint( $category->count ) ) . '</span></li>';
				}
				echo '</ul>';
			}
		);
	}
}

foreach ( $reimu_order as $reimu_widget_key ) {
	if ( ! in_array( $reimu_widget_key, $reimu_allowed_widgets, true ) || ! yneko_reimu_sidebar_widget_enabled( $reimu_widget_key ) ) {
		continue;
	}

	$reimu_callback = 'yneko_reimu_render_sidebar_' . $reimu_widget_key . '_widget';
	if ( function_exists( $reimu_callback ) ) {
		call_user_func( $reimu_callback );
	}
}
