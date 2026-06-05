<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YNEKO_REIMU_VERSION', '0.2.5' );
define( 'YNEKO_REIMU_DIR', get_template_directory() );
define( 'YNEKO_REIMU_URI', get_template_directory_uri() );

require_once YNEKO_REIMU_DIR . '/inc/sanitizers.php';
require_once YNEKO_REIMU_DIR . '/inc/image-helpers.php';
require_once YNEKO_REIMU_DIR . '/inc/security-auth-mail.php';
require_once YNEKO_REIMU_DIR . '/inc/settings.php';
require_once YNEKO_REIMU_DIR . '/inc/features.php';
require_once YNEKO_REIMU_DIR . '/inc/svg.php';
require_once YNEKO_REIMU_DIR . '/inc/security.php';
require_once YNEKO_REIMU_DIR . '/inc/i18n.php';
require_once YNEKO_REIMU_DIR . '/inc/migrations.php';
require_once YNEKO_REIMU_DIR . '/inc/seo-compat.php';
require_once YNEKO_REIMU_DIR . '/inc/setup.php';
require_once YNEKO_REIMU_DIR . '/inc/enqueue.php';
require_once YNEKO_REIMU_DIR . '/inc/schema.php';
require_once YNEKO_REIMU_DIR . '/inc/search-index.php';
require_once YNEKO_REIMU_DIR . '/inc/template-tags.php';
require_once YNEKO_REIMU_DIR . '/inc/view-count.php';
require_once YNEKO_REIMU_DIR . '/inc/widgets.php';
require_once YNEKO_REIMU_DIR . '/inc/customizer.php';
require_once YNEKO_REIMU_DIR . '/inc/post-meta.php';
require_once YNEKO_REIMU_DIR . '/inc/toc.php';
require_once YNEKO_REIMU_DIR . '/inc/comments.php';
require_once YNEKO_REIMU_DIR . '/inc/github-login.php';
