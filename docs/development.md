# Development

Yneko-Reimu is a Classic Hybrid WordPress theme. The installable theme lives in `theme/Yneko-Reimu`; repository-level tooling stays outside that runtime tree.

## Commands

```bash
npm install
npm run check:js
npm run check:settings-admin
npm run check:auth-security
npm run check:config-surface
npm run check:customizer
npm run check:template-tags
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

`npm run check:settings-admin` verifies the admin settings page contract after renderer splits. It checks that the settings tabs still have matching panels, that `inc/settings/page.php` calls each internal panel renderer, and that key option fields, review helper calls, TOTP controls, and security alert anchors remain present.

`npm run check:auth-security` verifies the authentication email guard contract. It protects the `auth_security` defaults and sanitizers, the random device cookie (`yneko_reimu_auth_device`), transient counter dimensions, global daily budget warning, bounded alert log, native `registration_errors` / `lostpassword_errors` coverage, and the three front-end verification-code send handlers. Keep registration, lost-password, and profile email code sends behind this helper unless a migration is documented.

`npm run check:config-surface` audits the theme's configurable surface by category. It protects admin UI coverage for `security.allow_svg_uploads` and `security.comment_ip_region_lookup`, representative Customizer-owned visual settings, documented developer extension filters, and internal/legacy compatibility values that should not become admin controls. Update this gate whenever a new user-configurable behavior is added or when a developer hook is intentionally documented instead of productized.

`npm run check:customizer` verifies the Customizer visual-preview contract before further decomposition. It checks the public customize hook, panel/section IDs, key setting/control IDs, and sanitizer callbacks so future helper extraction does not silently rename saved `theme_mod` or option-backed Customizer fields.

`npm run check:template-tags` verifies the Template Tags contract after helper splits. It checks the `inc/template-tags.php` entrypoint, internal module loading, key template helper functions, virtual page slugs, navigation hooks, sponsor shortcode, share/social platform definitions, and GitHub project transient/filter contracts.

The Template Tags PHP entrypoint is `theme/Yneko-Reimu/inc/template-tags.php`. Internal helpers may live under `theme/Yneko-Reimu/inc/template-tags/`; currently `layout-content.php` owns layout/content display helpers, `social-share.php` owns social/share helpers, `navigation-virtual.php` owns navigation and virtual pages, and `content-tools.php` owns home category capsules, YML rendering, sponsor/friend/project helpers, sticky/word-count helpers, and Taichi SVG output. Keep function names, virtual page slugs, template paths, public URLs, hooks, filters, shortcodes, and transient keys unchanged unless a compatibility note is added.

`npm run check:enqueue` verifies the front-end enqueue contract after PHP helper splits. It checks the public enqueue hook, critical script/style handles, third-party asset paths, `window.REIMU_CONFIG` keys, and nonce names so future asset-configuration cleanup does not silently change the front-end runtime contract.

`npm run check:comments-profile` verifies the comments/profile runtime contract before any further split. It checks high-risk AJAX actions, nonce creation and verification, front-end config keys, request payload fields, DOM selectors, source module boundaries, PHP module boundaries, comment rendering anchors, external comment panel anchors, and CSS anchors used by login, profile, comment upload, comment mutation, rendering, and review-status flows.

The comments/profile PHP entrypoint is `theme/Yneko-Reimu/inc/comments.php`. Internal helpers may live under `theme/Yneko-Reimu/inc/comments/`; currently `uploads.php` owns comment media upload/review helpers, `modals.php` owns request-free login/profile modal rendering, `auth.php` owns login/register/lost-password handlers, `profile.php` owns profile/TOTP/avatar handlers, `mutations.php` owns comment like/submit/edit/delete handlers plus review-status sync hooks, and `rendering.php` owns comment form/identity/comment-item/external-comment output. Keep function names, action names, nonce names, payload fields, JSON shapes, and front-end markup contracts unchanged unless the migration is documented in `docs/comments-profile-contract.md`.

`npm run check:github-oauth` verifies the GitHub OAuth public contract: module loading, login form actions, callback and bind URLs, bind nonce, settings keys, legacy option/meta compatibility, GitHub API scope/endpoints, popup message type, avatar fallback, admin-access hooks, and high-impact OAuth error strings. Update it in the same change only when an intentional compatibility migration is documented.

The GitHub OAuth PHP entrypoint is `theme/Yneko-Reimu/inc/github-login.php`. Internal helpers may live under `theme/Yneko-Reimu/inc/github-login/`; currently `settings.php` owns OAuth defaults/options/URLs and legacy setting-page compatibility, `rendering.php` owns login buttons/icons/styles, `oauth.php` owns begin/bind/callback/token/API/popup output, `users.php` owns lookup/create/bind/meta writes, `avatars.php` owns GitHub avatar fallback filters, `access.php` owns comment-user admin-bar/dashboard restrictions, and `login-2fa.php` owns the WordPress `wp-login.php` TOTP/recovery-code field, authenticate filter, recovery-code hashing, and single-use consumption for accounts that have enabled TOTP. Keep action names, nonce names, option keys, meta keys, OAuth scope/endpoints, popup message shape, avatar priority, and the `yneko_reimu_login_totp_code` login field unchanged unless a compatibility note is added.

`npm run check:release-readiness` verifies release-facing theme basics before the build: every runtime PHP file has an `ABSPATH` direct-access guard, `style.css` declares the expected `Tested up to` version, required theme header fields are present, runtime `readme.txt` exists with privacy/licensing notes, and `screenshot.png` is the standard `1200x900` PNG. If screenshot artwork is being refreshed manually, replace `theme/Yneko-Reimu/screenshot.png` before running the full release check.

`npm run check:css-split` verifies the planned CSS split candidates before and after stylesheets move out of `reimu.css`. It checks the machine-readable plan in `tools/css-split-plan.mjs`, candidate selectors in the current source CSS, target output names, per-component budgets, the post-comments-split 150 KB main CSS budget, the 20 KB player CSS budget, the 12 KB PhotoSwipe enhancement CSS budget, the 14 KB global share CSS budget, the 24 KB code/content CSS budget, the 16 KB search CSS budget, and the 52 KB comments/profile CSS budget.

`assets/dist/reimu-share.css` is intentionally enqueued globally. Article and virtual-page share markup can arrive through PJAX after the initial page load, so conditional PHP enqueueing can leave the first PJAX-rendered share/footer area unstyled until a full refresh.

`assets/dist/reimu-comments.css` is intentionally enqueued globally. The footer can render the login/profile modal shell outside singular comment pages, so making this stylesheet page-conditional would require a separate PHP output change and manual WordPress QA.

GitHub OAuth local/staging QA is documented in `docs/github-oauth-qa.md`.

Email verification, password-reset, profile email, and TOTP QA is documented in `docs/email-totp-qa.md`.

The admin settings page also exposes a current-user TOTP management entry under `General -> Account security`. It reuses the profile modal user meta (`_yneko_reimu_totp_secret`, `_yneko_reimu_totp_enabled`, and `_yneko_reimu_totp_pending_secret`) through admin-only AJAX actions, so it must not be treated as a global theme option or a forced site-wide 2FA switch. Once a user enables TOTP, both the front-end comment login AJAX flow and the WordPress `wp-login.php` password-login flow must verify the same current authenticator code or an unused recovery code. Recovery codes live in `_yneko_reimu_totp_recovery_codes` as password hashes only; plain text is returned only at generation time and each code is consumed after one successful login. Disabling TOTP must clear the enabled flag, current secret, pending secret, and recovery codes so a later enablement starts from a fresh secret.

The authentication email guard lives in `theme/Yneko-Reimu/inc/security-auth-mail.php` and stores its settings under `yneko_reimu_settings['auth_security']`. It covers front-end `register_code`, `lostpassword_code`, and `profile_email_code` sends plus native `wp-login.php` registration/lost-password requests. The device dimension uses a random 180-day cookie and stores only hashes in transients and logs. The security alert log is bounded to 100 events and should stay non-enumerating: lost-password responses continue to use generic success copy.

Broader security/privacy toggles live under `yneko_reimu_settings['security']`. `security.allow_svg_uploads` controls the default administrator SVG upload gate while preserving the `yneko_reimu_allow_svg_uploads` developer filter as the final override. `security.comment_ip_region_lookup` controls whether comment environment badges may call `ipwho.is`; when disabled, comments keep browser/system badges but skip IP region lookup before validation, cache access, or remote requests.

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
- Authentication email settings belong under Security settings and must keep privacy-friendly device IDs, generic lost-password responses, and bounded alert logging.
- Security/privacy settings that affect uploads or remote lookups belong under Security settings. Preserve existing site behavior by default unless a security migration explicitly documents a changed default.
- Front-end-visible article/card/sidebar modules that are stored as `theme_mod` values should have a Customizer control and be covered by `npm run check:customizer`.
- Do not move every filter into the admin UI. Developer extension filters such as asset strategy, security headers, schema graph, content width, SVG final override, and virtual-page definitions should stay documented in `docs/hooks.md` unless there is a clear non-developer site-owner workflow.
- Heavy or third-party features should stay disabled by default and gated by a setting, page context, or user interaction.
- The front-end WordPress admin toolbar stays hidden by default, including for administrators, to keep the public front end clean. The `show_admin_toolbar` feature setting is rendered under General -> Administrator experience and should be enabled only when an administrator needs temporary front-end debugging/plugin toolbar access; when it is off, Rank Math front-end Analytics/PRO toolbar prompts are hidden as a compatibility layer.
- Settings page UI should use grouped blocks for new dense sections. Keep the tab structure, setting keys, and form submission model stable; prefer moving markup into existing settings renderers before adding new admin frameworks.
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
