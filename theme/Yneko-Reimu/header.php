<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( '跳到内容', 'yneko-reimu' ); ?></a>
<?php
get_template_part( 'template-parts/layout/loader' );
?>
<div id="container">
	<div id="wrap">
<?php
get_template_part( 'template-parts/layout/site-header' );
?>
