<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yneko_reimu_admin_media_field( $name, $value, $label, $accept = '' ) {
	?>
	<div class="yneko-reimu-media-field">
		<input type="url" class="regular-text yneko-reimu-media-url" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $accept ? ' data-accept="' . esc_attr( $accept ) . '"' : ''; ?>>
		<button type="button" class="button yneko-reimu-media-button"><?php echo wp_kses_post( $label ); ?></button>
	</div>
	<?php
}

function yneko_reimu_admin_bilingual_text( $zh, $en, $tag = 'span' ) {
	$tag  = in_array( $tag, array( 'span', 'p', 'div', 'button' ), true ) ? $tag : 'span';
	$text = yneko_reimu_admin_prefers_zh() ? $zh : $en;
	return sprintf(
		'<%1$s class="yneko-reimu-admin-text">%2$s</%1$s>',
		tag_escape( $tag ),
		esc_html( $text )
	);
}

function yneko_reimu_admin_prefers_zh() {
	$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	return 0 === strpos( strtolower( str_replace( '-', '_', (string) $locale ) ), 'zh_' );
}

function yneko_reimu_admin_bilingual_label( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_description( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en, 'p' ) );
}

function yneko_reimu_admin_bilingual_heading( $zh, $en ) {
	echo wp_kses_post( yneko_reimu_admin_bilingual_text( $zh, $en ) );
}

function yneko_reimu_admin_bilingual_button_text( $zh, $en ) {
	return yneko_reimu_admin_bilingual_text( $zh, $en );
}

function yneko_reimu_admin_badge( $count ) {
	$count = absint( $count );
	if ( ! $count ) {
		return '';
	}

	return '<span class="yneko-reimu-admin-badge" aria-label="' . esc_attr(
		sprintf(
			/* translators: %d: pending review item count. */
			__( '%d 个待处理项目', 'yneko-reimu' ),
			$count
		)
	) . '">' . esc_html( (string) $count ) . '</span>';
}
