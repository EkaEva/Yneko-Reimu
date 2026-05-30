<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/layout/site-footer' );
get_template_part( 'template-parts/components/back-to-top' );
?>
		<div id="mask" class="hide"></div>
	</div>
	<?php
	get_template_part( 'template-parts/layout/mobile-nav' );
	get_template_part( 'template-parts/layout/search-popup' );
	if ( function_exists( 'yneko_reimu_login_modal' ) ) {
		yneko_reimu_login_modal();
	}
	?>
</div>
<?php
wp_footer();
?>
</body>
</html>
