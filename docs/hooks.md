# Hooks and Filters

## `yneko_reimu_feature_defaults`

Filters first-install defaults for feature toggles.

```php
add_filter( 'yneko_reimu_feature_defaults', function ( $defaults ) {
	$defaults['yneko_reimu_pjax_enable'] = true;
	return $defaults;
} );
```

## `yneko_reimu_asset_strategy`

Controls front-end asset behavior.

```php
add_filter( 'yneko_reimu_asset_strategy', function ( $strategy ) {
	$strategy['preload_cursor_images'] = true;
	$strategy['script_strategy'] = 'defer';
	return $strategy;
} );
```

Supported keys:

- `font_display`: enqueue Google Fonts when true.
- `preload_cursor_images`: emit cursor image preload tags when true.
- `preload_cursor_variants`: cursor filenames to preload.
- `script_strategy`: WordPress script loading strategy for the main theme script.

## `yneko_reimu_schema_enabled`

Disables the theme JSON-LD output when another SEO plugin should own schema.

```php
add_filter( 'yneko_reimu_schema_enabled', '__return_false' );
```

## `yneko_reimu_schema_graph`

Filters the JSON-LD `@graph` array before output.

```php
add_filter( 'yneko_reimu_schema_graph', function ( $graph ) {
	$graph[] = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
	);
	return $graph;
} );
```

## `yneko_reimu_security_headers`

Filters the HTTP security headers emitted by the theme.

```php
add_filter( 'yneko_reimu_security_headers', function ( $headers ) {
	unset( $headers['X-Frame-Options'] );
	return $headers;
} );
```

## `yneko_reimu_content_width`

Filters the classic WordPress content width used by the theme.

```php
add_filter( 'yneko_reimu_content_width', function () {
	return 960;
} );
```

## `yneko_reimu_allow_svg_uploads`

Provides a developer-level override for the SVG upload gate. The admin UI setting still controls the default behavior.

```php
add_filter( 'yneko_reimu_allow_svg_uploads', function ( $enabled ) {
	return $enabled && current_user_can( 'manage_options' );
} );
```

## `yneko_reimu_virtual_pages`

Filters built-in virtual page definitions.

```php
add_filter( 'yneko_reimu_virtual_pages', function ( $pages ) {
	unset( $pages['projects'] );
	return $pages;
} );
```
