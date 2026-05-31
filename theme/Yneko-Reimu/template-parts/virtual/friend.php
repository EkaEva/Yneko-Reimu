<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_friends = yneko_reimu_friend_items();
$reimu_site_yml = sprintf(
	"- name: %s\n  url: %s\n  desc: %s\n  image: %s",
	get_bloginfo( 'name' ),
	home_url( '/' ),
	get_bloginfo( 'description' ),
	yneko_reimu_get_default_avatar_url()
);
$reimu_is_english = function_exists( 'yneko_reimu_i18n_is_english_request' ) && yneko_reimu_i18n_is_english_request();
$reimu_apply_yml  = $reimu_is_english
	? "```yml\n- name: # Your name\n  url: # Your site URL\n  desc: # Short description\n  image: # Image URL\n```"
	: "```yml\n- name: #您的名字\n  url: #您的网址\n  desc: #简短描述\n  image: #一张图片\n```";
?>
<article id="page-friend" class="h-entry article reimu-virtual-page">
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
			<h2 id="site-info"><a class="markdownIt-Anchor" href="#site-info"></a><?php echo esc_html( function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '本站信息' ) : __( '本站信息', 'yneko-reimu' ) ); ?></h2>
			<?php echo yneko_reimu_yml_editor( $reimu_site_yml ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<h2 id="how-to-apply"><a class="markdownIt-Anchor" href="#how-to-apply"></a><?php echo esc_html( function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '申请方法' ) : __( '申请方法', 'yneko-reimu' ) ); ?></h2>
			<ul>
				<li><?php echo esc_html( function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '添加本站后，在本页留言，格式如下' ) : __( '添加本站后，在本页留言，格式如下', 'yneko-reimu' ) ); ?></li>
			</ul>
			<?php echo yneko_reimu_yml_editor( $reimu_apply_yml ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<h2 id="friends"><a class="markdownIt-Anchor" href="#friends"></a><?php echo esc_html( function_exists( 'yneko_reimu_i18n_frontend_text' ) ? yneko_reimu_i18n_frontend_text( '小伙伴们' ) : __( '小伙伴们', 'yneko-reimu' ) ); ?></h2>
			<?php if ( $reimu_friends ) : ?>
				<div class="friend-wrap" data-aos="zoom-in">
					<?php foreach ( $reimu_friends as $friend ) : ?>
						<div class="friend-item-wrap">
							<a href="<?php echo esc_url( $friend['url'] ); ?>" rel="nofollow noopener noreferrer" target="_blank"></a>
							<div class="friend-icon-wrap">
								<img class="no-lightbox lazyload" src="<?php echo esc_url( $friend['image'] ); ?>" data-src="<?php echo esc_url( $friend['image'] ); ?>" alt="<?php echo esc_attr( $friend['name'] ); ?>">
							</div>
							<div class="friend-info-wrap">
								<div class="friend-name"><?php echo esc_html( $friend['name'] ); ?></div>
								<div class="friend-desc"><?php echo esc_html( $friend['desc'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="friend-wrap">
					<div class="reimu-empty reimu-virtual-empty">
						<h2><?php esc_html_e( '友链待添加', 'yneko-reimu' ); ?></h2>
						<p><?php esc_html_e( '可在 Customizer 的“关于与友链”中配置，或创建 slug 为 friend 的真实页面覆盖。', 'yneko-reimu' ); ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php get_template_part( 'template-parts/meta/virtual-page-footer' ); ?>
	</div>
</article>
