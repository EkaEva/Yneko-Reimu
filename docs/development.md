# Development

Yneko-Reimu is a Classic Hybrid WordPress theme. The installable theme lives in `theme/Yneko-Reimu`; repository-level tooling stays outside that runtime tree.

## Commands

```bash
npm install
npm run check:js
npm run build
npm run check:size
npm run package
```

PHP coding standards require Composer:

```bash
composer install
npm run lint:php
```

`npm run build` generates gettext files, cursor PNGs, minified Vite assets, and the build manifest in `theme/Yneko-Reimu/assets/dist/`.

`npm run check:size` enforces the short-term public asset budgets:

- `assets/dist/reimu.js` must stay at or below 120 KB.
- `assets/dist/reimu.css` must stay at or below 220 KB.
- The main `reimu.js` build must remain compatible with classic script loading and must not contain `import.meta` or unresolved dynamic `import(` calls.
- The feature loading report comes from `tools/feature-loading-plan.mjs`; update it before moving code out of the main bundle.

## Source Layout

- `theme/Yneko-Reimu/assets/src/` contains maintained frontend sources.
- `theme/Yneko-Reimu/assets/dist/` contains runtime assets loaded by WordPress.
- `tools/` contains i18n, cursor, asset, and package scripts.

Admin settings JavaScript is maintained in `theme/Yneko-Reimu/assets/src/admin-settings.js` and built to `assets/dist/admin-settings.js`. PHP should only enqueue the built admin script and inject the small `YNEKO_REIMU_ADMIN_I18N` configuration object before it.

## Development Constraints

- Keep the installable theme rooted at `theme/Yneko-Reimu`.
- Keep front-end scripts compatible with WordPress classic script enqueueing unless a release plan explicitly changes that public behavior.
- Do not rename saved settings, post meta keys, AJAX action names, nonce names, documented filters/actions, template paths, virtual page slugs, or public URLs without a compatibility plan.
- New settings need a default value, sanitizer, UI location, migration decision, and a note about whether they affect front-end loading.
- Heavy or third-party features should stay disabled by default and gated by a setting, page context, or user interaction.

## Package Checks

`npm run check:package` inspects the newest ZIP in `releases/` and fails if development-only files are present, including `assets/src`, `node_modules`, `vendor`, `tools`, planning files, local-only agent files, or `assets/dist/manifest.json`.

## Performance Defaults

Fresh installs default to a lighter front end. Users can enable heavier effects in Customizer:

- Custom cursor: off.
- Mouse firework: off.
- PJAX: off.
- APlayer/Meting: off unless explicitly enabled and configured.
- Live2D, KaTeX, PhotoSwipe, Mermaid, third-party comments, and statistics: off.

Existing sites that already saved these options keep their stored values.
