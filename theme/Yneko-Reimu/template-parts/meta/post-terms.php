<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tags         = get_the_tags();
$categories   = get_the_category();
$seen_tags    = array();
$visible_tags = array();
$category_keys = array();

foreach ( $categories as $category ) {
	$category_keys[] = sanitize_title( $category->slug );
	$category_keys[] = sanitize_title( $category->name );
}

if ( $tags ) {
	foreach ( $tags as $tag ) {
		$tag_key  = sanitize_title( $tag->slug ? $tag->slug : $tag->name );
		$name_key = sanitize_title( $tag->name );

		if ( isset( $seen_tags[ $tag_key ] ) || in_array( $tag_key, $category_keys, true ) || in_array( $name_key, $category_keys, true ) ) {
			continue;
		}

		$seen_tags[ $tag_key ] = true;
		$visible_tags[]        = $tag;
	}
}
?>
<?php if ( yneko_reimu_theme_mod_bool( 'yneko_reimu_show_categories', true ) ) : ?>
	<?php foreach ( $categories as $category ) : ?>
		<a class="article-category-link" data-aos="zoom-in" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ( $visible_tags && yneko_reimu_theme_mod_bool( 'yneko_reimu_show_tags', true ) ) : ?>
	<ul class="article-tag-list" itemprop="keywords">
		<?php foreach ( $visible_tags as $tag ) : ?>
			<li class="article-tag-list-item" data-aos="zoom-in"><a class="article-tag-list-link" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" rel="tag" title="<?php echo esc_attr( $tag->name ); ?>"><?php echo esc_html( $tag->name ); ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
