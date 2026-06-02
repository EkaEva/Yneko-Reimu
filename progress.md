# Yneko-Reimu Optimization Progress

## 2026-05-30

- Started implementation from approved plan.
- Confirmed clean git worktree before edits.
- Added task tracking files for the optimization implementation.
- Added Vite, Composer/PHPCS scaffolding, and changed build/package scripts to build before packaging.
- Moved the upstream Reimu source mirror out of the installable theme tree; later source slimming removed that mirror from the repository because the maintained CSS snapshot is sufficient for builds.
- Updated README build-script notes for Vite and PHPCS.
- Generated WebP derivatives for banner, search background, and screenshot using ImageMagick.
- Updated image helpers to prefer WebP banner/search assets and responsive banner sources.
- Added lightweight JSON-LD schema output with `yneko_reimu_schema_enabled` and `yneko_reimu_schema_graph` filters.
- Promoted main content wrappers to `<main>` and changed listing cards to semantic `<article>` elements.
- Added block pattern registration and extended `theme.json` spacing/typography settings for a stronger block-editor baseline.
- Added pattern styling and reduced-motion CSS support.
- Updated release workflow to run Vite build and PHPCS/WPCS before packaging.
- Adjusted loader CSS build so cursor PNGs remain external cacheable files instead of Vite-inlined base64.
- `composer install` could not run locally because Composer is not installed in this Windows environment.
- Added docs for development, hooks, release packaging, and Theme Check expectations.
- Updated packaging so repository docs referenced by README are included in the release ZIP.
- Updated README release checks and directory tree for the new toolchain layout.
- Synced the English README development section with the new Vite/PHPCS workflow.
- Replaced theme screenshot with the user-provided compressed PNG from `C:\Users\86135\Downloads\screenshot.png`.
- Fixed the release workflow after the first v0.1.2 tag run failed because CI had not installed npm dependencies before running Vite.
- Scoped PHPCS/WPCS to an incremental gate after the full legacy theme tree produced broad pre-existing style failures.

## 2026-06-02

- Started online theme audit for https://yneko.com/ using the live site plus local theme source.
- Collected desktop/mobile browser signals: no console errors, no mobile horizontal overflow, but many default scripts/fixed overlays and limited lazy-loading signals.
- Matched live resource behavior to `inc/enqueue.php`, `inc/features.php`, `content-card.php`, `search-index.php`, and `inc/comments.php`.
- Completed prioritized audit summary for the user.
- Cleaned the local WordPress Docker dev stack with `docker compose down -v --remove-orphans`, removed `wp-local`, removed generated `node_modules`, and kept only the latest release zip.
- Added theme SEO compatibility so common SEO plugins suppress theme meta/schema; Rank Math canonical, OG URL, hreflang, English post sitemap URLs, and `/en/` sitemap entry are handled by theme i18n helpers.
- Hardened front-end login and password-reset flows against account enumeration and added transient cooldown/failure tracking by email/IP.
- Changed search index full content to opt-in, added native lazy/decoding/dimensions on card images, lightened default preloader/custom cursor behavior, and documented third-party resource/privacy implications.
- Changed APlayer default preload to `metadata`, deferred APlayer initialization until visibility/user interaction, and patched APlayer controls with accessible labels.
- Added hidden/inert/aria-hidden synchronization for search, login/profile modals, and mobile navigation.
- Ran `npm install`, `npm run check:js`, and `npm run build`; Composer/PHP lint remains unavailable locally.
- Bumped the theme/package version to `0.1.12`, built `releases/Yneko-Reimu-v0.1.12.zip`, verified the zip contains the new SEO compatibility file and excludes development/local directories, then removed temporary `node_modules` and the older `v0.1.11` zip.
- Verified the deployed `v0.1.12` theme on `https://yneko.com/`: duplicate meta/schema, `/en/` canonical, hreflang, search index content exposure, card image attributes, and APlayer control labels are improved; sitemap URL rewriting and no-interaction audio requests still need another fix.
- Verified the deployed `v0.1.13` assets: APlayer no longer requests audio/lyrics before interaction, but Rank Math sitemap output remains stale and still needs cache invalidation or a different sitemap integration.
- Reset local/public version fields back to `0.1.12`, changed package naming to `Yneko-Reimu-v0.1.12-YYYYMMDD-HHMM.zip`, removed the stale v0.1.13 local ZIP, renamed the unused layout `hero.php` to `banner.php`, and removed the upstream source mirror plus large PNG fallbacks.
- Verified `Yneko-Reimu-v0.1.12-20260602-1046.zip`: version header is `0.1.12`, `inc/seo-compat.php` is included, `banner.php` is included, and `hero.php`, `vendor-src`, `assets/src`, `node_modules`, `banner.png`, and `search-bg.png` are absent.
- Updated README configuration documentation for the split between `Yneko-Reimu Settings` and `Yneko-Reimu Visual Preview`, including SEO compatibility, comment upload review, SVG upload, APlayer behavior, local search privacy, sidebar widgets, and timestamped local package names.
- Added `docs/release-notes-v0.1.12.md` with bilingual release notes for the GitHub Release workflow.
- Ran release validation for v0.1.12: `npm run check:js`, `npm run build`, targeted `php -l` on changed PHP entry points, and `npm run package` all passed.
- Created `releases/Yneko-Reimu-v0.1.12-20260602-1631.zip` and verified it includes `seo-compat.php`, `security.php`, `svg.php`, and `banner.php`, while excluding `hero.php`, `assets/src`, and old large banner PNG files.
- Composer is still unavailable locally, so PHPCS/WPCS will be covered by GitHub Actions after pushing the tag.
