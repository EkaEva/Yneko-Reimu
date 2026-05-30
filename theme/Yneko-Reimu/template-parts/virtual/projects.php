<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_projects = yneko_reimu_github_projects();
$reimu_starred  = yneko_reimu_github_starred_projects();
$reimu_github_username = yneko_reimu_github_username();
$reimu_profile  = $reimu_github_username ? 'https://github.com/' . rawurlencode( $reimu_github_username ) : '';

if ( ! function_exists( 'yneko_reimu_render_project_cards' ) ) {
	function yneko_reimu_render_project_cards( $projects ) {
		foreach ( $projects as $project ) :
			$updated = '';
			if ( ! empty( $project['updated_at'] ) ) {
				$timestamp = strtotime( $project['updated_at'] );
				$updated   = $timestamp ? date_i18n( 'Y-m-d', $timestamp ) : '';
			}
			?>
			<div class="friend-item-wrap project-item-wrap" data-reimu-loadmore-item>
				<a href="<?php echo esc_url( $project['url'] ); ?>" rel="nofollow noopener noreferrer" target="_blank" aria-label="<?php echo esc_attr( $project['name'] ); ?>"></a>
				<div class="friend-icon-wrap project-icon-wrap">
					<img class="no-lightbox lazyload" src="<?php echo esc_url( $project['image'] ); ?>" data-src="<?php echo esc_url( $project['image'] ); ?>" alt="<?php echo esc_attr( $project['name'] ); ?>">
				</div>
				<div class="friend-info-wrap project-info-wrap">
					<div class="project-heading">
						<div class="friend-name project-name"><?php echo esc_html( $project['name'] ); ?></div>
						<div class="project-meta" aria-label="<?php esc_attr_e( '项目信息', 'yneko-reimu' ); ?>">
							<?php if ( ! empty( $project['language'] ) ) : ?>
								<span class="project-meta-item project-language"><?php echo esc_html( $project['language'] ); ?></span>
							<?php endif; ?>
							<span class="project-meta-item project-stars">Star <?php echo esc_html( (string) absint( $project['stars'] ?? 0 ) ); ?></span>
							<?php if ( $updated ) : ?>
								<span class="project-meta-item project-updated"><?php echo esc_html( $updated ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="friend-desc project-desc"><?php echo esc_html( $project['desc'] ); ?></div>
				</div>
			</div>
			<?php
		endforeach;
	}
}

if ( ! function_exists( 'yneko_reimu_project_load_more_button' ) ) {
	function yneko_reimu_project_load_more_button( $target_id, $items ) {
		?>
		<div class="reimu-load-more-wrap project-load-more-wrap">
			<button type="button" class="reimu-load-more" data-reimu-loadmore-target="#<?php echo esc_attr( $target_id ); ?>" data-label-more="<?php esc_attr_e( '加载更多...', 'yneko-reimu' ); ?>" data-label-end="<?php esc_attr_e( '到底了...', 'yneko-reimu' ); ?>"><?php echo count( $items ) > 12 ? esc_html__( '加载更多...', 'yneko-reimu' ) : esc_html__( '到底了...', 'yneko-reimu' ); ?></button>
		</div>
		<?php
	}
}
?>
<article id="page-projects" class="h-entry article reimu-virtual-page reimu-project-page">
	<div class="article-inner" data-aos="fade-up">
		<div class="article-meta">
			<div class="article-date">
				<span class="article-date-link icon-calendar" data-aos="zoom-in">
					<time datetime="<?php echo esc_attr( gmdate( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( gmdate( 'Y-m-d' ) ); ?></time>
					<time style="display:none" id="post-update-time"><?php echo esc_html( gmdate( 'Y-m-d' ) ); ?></time>
				</span>
			</div>
		</div>
		<div class="hr-line"></div>
		<div class="e-content article-entry" itemprop="articleBody">
			<h2 id="my-projects"><a class="markdownIt-Anchor" href="#my-projects"></a><?php esc_html_e( '我的项目', 'yneko-reimu' ); ?></h2>
			<?php if ( $reimu_projects ) : ?>
				<div id="reimu-my-projects-list" class="friend-wrap project-wrap" data-aos="zoom-in">
					<?php yneko_reimu_render_project_cards( $reimu_projects ); ?>
				</div>
			<?php else : ?>
				<div class="friend-wrap project-wrap">
					<div class="reimu-empty reimu-virtual-empty">
						<h2><?php esc_html_e( '项目待同步', 'yneko-reimu' ); ?></h2>
						<p>
							<?php if ( $reimu_profile ) : ?>
								<a href="<?php echo esc_url( $reimu_profile ); ?>" target="_blank" rel="noopener nofollow noreferrer"><?php echo esc_html( $reimu_profile ); ?></a>
							<?php else : ?>
								<?php esc_html_e( '请在“外观 -> Yneko-Reimu 设置”中配置 GitHub 主页链接。', 'yneko-reimu' ); ?>
							<?php endif; ?>
						</p>
					</div>
				</div>
			<?php endif; ?>

			<h2 id="recommended-projects"><a class="markdownIt-Anchor" href="#recommended-projects"></a><?php esc_html_e( '项目推荐', 'yneko-reimu' ); ?></h2>
			<?php if ( $reimu_starred ) : ?>
				<div id="reimu-recommended-projects-list" class="friend-wrap project-wrap" data-aos="zoom-in" data-reimu-loadmore-root data-reimu-loadmore-batch="12">
					<?php yneko_reimu_render_project_cards( $reimu_starred ); ?>
				</div>
				<?php yneko_reimu_project_load_more_button( 'reimu-recommended-projects-list', $reimu_starred ); ?>
			<?php else : ?>
				<div class="friend-wrap project-wrap">
					<div class="reimu-empty reimu-virtual-empty">
						<h2><?php esc_html_e( '推荐项目待同步', 'yneko-reimu' ); ?></h2>
						<p>
							<?php if ( $reimu_profile ) : ?>
								<a href="<?php echo esc_url( $reimu_profile . '?tab=stars' ); ?>" target="_blank" rel="noopener nofollow noreferrer"><?php echo esc_html( $reimu_profile . '?tab=stars' ); ?></a>
							<?php else : ?>
								<?php esc_html_e( '配置 GitHub 主页链接后会自动同步 starred 项目。', 'yneko-reimu' ); ?>
							<?php endif; ?>
						</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</article>
