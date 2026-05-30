# Development

Yneko-Reimu is a Classic Hybrid WordPress theme. The installable theme lives in `theme/Yneko-Reimu`; repository-level tooling and upstream mirrors stay outside that runtime tree.

## Commands

```bash
npm install
npm run check:js
npm run build
npm run package
```

PHP coding standards require Composer:

```bash
composer install
npm run lint:php
```

`npm run build` generates gettext files, cursor PNGs, minified Vite assets, and the build manifest in `theme/Yneko-Reimu/assets/dist/`.

## Source Layout

- `theme/Yneko-Reimu/assets/src/` contains maintained frontend sources.
- `theme/Yneko-Reimu/assets/dist/` contains runtime assets loaded by WordPress.
- `vendor-src/reimu-upstream/` contains the upstream Reimu reference mirror for development and attribution.
- `tools/` contains i18n, cursor, asset, and package scripts.

## Performance Defaults

Fresh installs default to a lighter front end. Users can enable heavier effects in Customizer:

- Custom cursor: off.
- Mouse firework: off.
- PJAX: off.
- APlayer/Meting: off unless explicitly enabled and configured.
- Live2D, KaTeX, PhotoSwipe, Mermaid, third-party comments, and statistics: off.

Existing sites that already saved these options keep their stored values.
