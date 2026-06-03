<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reimu_post_id = get_the_ID();
$reimu_shares  = yneko_reimu_share_links( $reimu_post_id );
$reimu_context = yneko_reimu_share_context( $reimu_post_id );

if ( empty( $reimu_shares ) ) {
	return;
}

$reimu_share_data = array(
	'url'         => $reimu_context['url'],
	'title'       => $reimu_context['title'],
	'description' => $reimu_context['description'],
	'author'      => $reimu_context['author'],
	'image'       => $reimu_context['image'],
	'theme'       => sprintf(
		/* translators: %s: theme name. */
		__( 'Powered By %s', 'yneko-reimu' ),
		'Yneko-Reimu'
	),
);
?>
<div class="share-wrapper" data-reimu-share="<?php echo esc_attr( wp_json_encode( $reimu_share_data ) ); ?>" aria-label="<?php esc_attr_e( '分享', 'yneko-reimu' ); ?>">
	<?php foreach ( $reimu_shares as $reimu_share_key => $reimu_share ) : ?>
		<a
			href="<?php echo esc_url( $reimu_share['url'] ); ?>"
			class="share-link share-link-<?php echo esc_attr( $reimu_share_key ); ?>"
			<?php if ( 'weixin' !== $reimu_share_key ) : ?>
				target="_blank" rel="noopener noreferrer"
			<?php endif; ?>
			aria-label="<?php echo esc_attr( sprintf( __( '分享到 %s', 'yneko-reimu' ), $reimu_share['label'] ) ); ?>"
			title="<?php echo esc_attr( $reimu_share['label'] ); ?>"
			data-share-service="<?php echo esc_attr( $reimu_share_key ); ?>"
			data-no-pjax
		>
			<span class="share-icon icon icon-<?php echo esc_attr( $reimu_share_key ); ?>" aria-hidden="true">
				<?php if ( 'weixin' === $reimu_share_key ) : ?>
					<span class="share-weixin" data-share-weixin-popup aria-hidden="true">
						<span class="share-weixin-dom">
							<span class="share-weixin-content">
								<img class="share-weixin-banner" data-share-weixin-banner alt="" loading="lazy" decoding="async">
								<span class="share-weixin-title" data-share-weixin-title></span>
								<span class="share-weixin-desc" data-share-weixin-desc></span>
							</span>
							<span class="share-weixin-qrcode">
								<span class="share-weixin-info">
									<span class="share-weixin-author" data-share-weixin-author></span>
									<span class="share-weixin-theme" data-share-weixin-theme></span>
								</span>
								<img class="share-weixin-qr" data-share-weixin-qr alt="<?php esc_attr_e( '微信分享二维码', 'yneko-reimu' ); ?>" loading="lazy" decoding="async">
							</span>
						</span>
						<span class="share-weixin-canvas"></span>
					</span>
				<?php endif; ?>
			</span>
		</a>
	<?php endforeach; ?>
</div>
