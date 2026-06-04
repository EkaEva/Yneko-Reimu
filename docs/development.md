# Development

Yneko-Reimu is a Classic Hybrid WordPress theme. The installable theme lives in `theme/Yneko-Reimu`; repository-level tooling stays outside that runtime tree.

## Commands

```bash
npm install
npm run check:js
npm run check:settings-admin
npm run check:customizer
npm run check:enqueue
npm run check:comments-profile
npm run check:github-oauth
npm run check:release-readiness
npm run check:css-split
npm run build
npm run check:assets
npm run check:i18n-messages
npm run check:size
npm run report:php-complexity
npm run package
```

PHP coding standards require Composer:

```bash
composer install
npm run lint:php
```

`npm run build` generates gettext files, cursor PNGs, minified Vite assets, and the build manifest in `theme/Yneko-Reimu/assets/dist/`.

`npm run check:i18n-messages` verifies that high-impact English feedback strings in auth, profile, comment, upload, review, email verification, password reset, and GitHub OAuth flows are not empty after gettext files are regenerated. It is a focused user-facing message contract, not a requirement that every historical `en_US.po` entry is translated.

`npm run check:assets` verifies that runtime PHP/CSS/JS files do not contain `data:image` URLs or base64 image payloads. Large, replaceable, or cacheable images should stay as files under `assets/images` or be emitted into `assets/dist` by the build. Small UI SVG components may remain inline when they are part of markup behavior rather than replaceable media.

`npm run check:size` enforces the short-term public asset budgets:

- `assets/dist/reimu.js` must stay at or below 120 KB.
- `assets/dist/reimu-search.js` must stay at or below 24 KB.
- `assets/dist/reimu-photoswipe.js` must stay at or below 24 KB.
- `assets/dist/reimu-share.js` must stay at or below 24 KB.
- `assets/dist/reimu.css` must stay at or below 150 KB.
- `assets/dist/reimu-player.css` must stay at or below 20 KB.
- `assets/dist/reimu-photoswipe.css` must stay at or below 12 KB.
- `assets/dist/reimu-share.css` must stay at or below 14 KB.
- `assets/dist/reimu-code.css` must stay at or below 24 KB.
- `assets/dist/reimu-search.css` must stay at or below 16 KB.
- `assets/dist/reimu-comments.css` must stay at or below 52 KB.
- Public runtime script builds must remain compatible with classic script loading and must not contain `import.meta`, unresolved dynamic `import(` calls, or top-level ESM import/export syntax.
- The feature loading report comes from `tools/feature-loading-plan.mjs`; update it before moving code out of the main bundle.

`npm run report:php-complexity` scans the runtime theme PHP files and reports the largest files, largest named functions, and highest approximate branch scores. It is informational for now so the project can track legacy complexity before turning any threshold into a failing quality gate.

`npm run check:settings-admin` verifies the admin settings page contract after renderer splits. It checks that the 10 settings tabs still have matching panels, that `inc/settings/page.php` calls each internal panel renderer, and that key option fields and review helper calls remain present.

`npm run check:customizer` verifies the Customizer visual-preview contract before further decomposition. It checks the public customize hook, panel/section IDs, key setting/control IDs, and sanitizer callbacks so future helper extraction does not silently rename saved `theme_mod` or option-backed Customizer fields.

`npm run check:enqueue` verifies the front-end enqueue contract after PHP helper splits. It checks the public enqueue hook, critical script/style handles, third-party asset paths, `window.REIMU_CONFIG` keys, and nonce names so future asset-configuration cleanup does not silently change the front-end runtime contract.

`npm run check:comments-profile` verifies the comments/profile runtime contract before any further split. It checks high-risk AJAX actions, nonce creation and verification, front-end config keys, request payload fields, DOM selectors, source module boundaries, and CSS anchors used by login, profile, comment upload, comment mutation, and review-status flows.

The comments/profile PHP entrypoint is `theme/Yneko-Reimu/inc/comments.php`. Internal helpers may live under `theme/Yneko-Reimu/inc/comments/`; currently `uploads.php` owns comment media upload/review helpers and `modals.php` owns request-free login/profile modal rendering. Keep function names and front-end markup contracts unchanged unless the migration is documented in `docs/comments-profile-contract.md`.

`npm run check:github-oauth` verifies the GitHub OAuth public contract: login form actions, callback and bind URLs, bind nonce, settings keys, legacy option/meta compatibility, GitHub API scope/endpoints, popup message type, and high-impact OAuth error strings. Update it in the same change only when an intentional compatibility migration is documented.

`npm run check:release-readiness` verifies release-facing theme basics before the build: every runtime PHP file has an `ABSPATH` direct-access guard, `style.css` declares the expected `Tested up to` version, required theme header fields are present, runtime `readme.txt` exists with privacy/licensing notes, and `screenshot.png` is the standard `1200x900` PNG. If screenshot artwork is being refreshed manually, replace `theme/Yneko-Reimu/screenshot.png` before running the full release check.

`npm run check:css-split` verifies the planned CSS split candidates before and after stylesheets move out of `reimu.css`. It checks the machine-readable plan in `tools/css-split-plan.mjs`, candidate selectors in the current source CSS, target output names, per-component budgets, the post-comments-split 150 KB main CSS budget, the 20 KB player CSS budget, the 12 KB PhotoSwipe enhancement CSS budget, the 14 KB global share CSS budget, the 24 KB code/content CSS budget, the 16 KB search CSS budget, and the 52 KB comments/profile CSS budget.

`assets/dist/reimu-share.css` is intentionally enqueued globally. Article and virtual-page share markup can arrive through PJAX after the initial page load, so conditional PHP enqueueing can leave the first PJAX-rendered share/footer area unstyled until a full refresh.

`assets/dist/reimu-comments.css` is intentionally enqueued globally. The footer can render the login/profile modal shell outside singular comment pages, so making this stylesheet page-conditional would require a separate PHP output change and manual WordPress QA.

GitHub OAuth local/staging QA is documented in `docs/github-oauth-qa.md`.

Email verification, password-reset, profile email, and TOTP QA is documented in `docs/email-totp-qa.md`.

## Source Layout

- `theme/Yneko-Reimu/assets/src/` contains maintained frontend sources.
- `theme/Yneko-Reimu/assets/dist/` contains runtime assets loaded by WordPress.
- `tools/` contains i18n, cursor, asset, and package scripts.

Images and standalone SVG icons should be committed as files instead of encoded into CSS or PHP strings. Use `theme/Yneko-Reimu/assets/images/` for theme images, `theme/Yneko-Reimu/assets/images/icons/` for standalone icon files, and build-emitted `assets/dist/` files for generated runtime assets. Vite is configured with `assetsInlineLimit: 0`, so even small images such as `taichi.png` remain independently cacheable files.

Admin settings JavaScript is maintained in `theme/Yneko-Reimu/assets/src/admin-settings.js` and built to `assets/dist/admin-settings.js`. PHP should only enqueue the built admin script and inject the small `YNEKO_REIMU_ADMIN_I18N` configuration object before it.

Admin settings PHP panels are internal renderers in `theme/Yneko-Reimu/inc/settings/panels.php`. When changing tabs, panel names, field names, repeatable rows, or review sections, update `tools/check-settings-admin-contract.mjs` in the same change.

## Development Constraints

- Keep the installable theme rooted at `theme/Yneko-Reimu`.
- Keep front-end scripts compatible with WordPress classic script enqueueing unless a release plan explicitly changes that public behavior.
- Do not rename saved settings, post meta keys, AJAX action names, nonce names, documented filters/actions, template paths, virtual page slugs, or public URLs without a compatibility plan.
- New settings need a default value, sanitizer, UI location, migration decision, and a note about whether they affect front-end loading.
- Front-end-visible article/card/sidebar modules that are stored as `theme_mod` values should have a Customizer control and be covered by `npm run check:customizer`.
- Heavy or third-party features should stay disabled by default and gated by a setting, page context, or user interaction.
- Do not add hand-written base64 image payloads or `data:image` URLs to runtime PHP, CSS, or JavaScript. Add image files to `assets/images` or use a small inline SVG component when it is truly UI markup.
- Before moving comments/profile AJAX handlers, login-state DOM replacement, or runtime boundaries, follow `docs/comments-profile-contract.md`.

## Package Checks

`npm run check:package` inspects the newest ZIP in `releases/` and fails if development-only files are present, including `assets/src`, `node_modules`, `vendor`, `tools`, planning files, local-only agent files, or `assets/dist/manifest.json`.

The package check also requires the runtime `readme.txt` to be present in the installable ZIP.

## Performance Defaults

Fresh installs default to a lighter front end. Users can enable heavier effects in Customizer:

- Custom cursor: off.
- Mouse firework: off.
- PJAX: off.
- APlayer/Meting: off unless explicitly enabled and configured.
- Live2D, KaTeX, PhotoSwipe, Mermaid, third-party comments, and statistics: off.

Existing sites that already saved these options keep their stored values.
