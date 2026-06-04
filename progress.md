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

## 2026-06-03

- Started full Customizer-managed share/social implementation.
- Confirmed upstream Reimu sidebar social list has 29 platforms and article sharing supports 8 platforms.
- Added Customizer controls for article share enablement and sidebar social enablement/URLs.
- Added PHP helper definitions for share/social platforms, share URL generation, enabled link filtering, and share images.
- Added post share template and inserted it into single posts and pages.
- Added icon mappings/colors for the full upstream icon set plus RSS compatibility, and added Weixin share popup JS.
- Switched Weixin QR generation to a local `qrcode` Vite split chunk and updated the build manifest to record generated chunk outputs.
- Fixed the local Bilibili CSS hiding rule so the sidebar Bilibili social icon can render when enabled.
- Ran `npm run check:js`, targeted `php -l` on changed PHP templates/helpers, and `npm run build`; all passed. Restored cursor PNG build side effects afterward.
- Hardened social URL handling so Email supports direct addresses and `mailto:` links, RSS can fall back to the site feed, and all sidebar/share pseudo-icons explicitly use the Reimu icon font.
- Ran `npm run package` and verified the release ZIP includes the Weixin QR dynamic chunk and the new post share template; restored cursor PNG build side effects afterward.
- Added Xiaohongshu to sidebar social definitions and created `assets/images/icons/xiaohongshu.svg` from the user-provided SVG.
- Added the GitHub triangle badge checkbox to the Customizer's "分享与社交链接" section, using the existing settings key that front-end rendering already respects.
- Updated share CSS so post footer share buttons occupy the article footer grid area, page share buttons stay inside article content margins, and share icons use pointer cursor/hover scaling like sidebar social icons.
- Set Vite `base` to `./` so generated CSS references `xiaohongshu.svg` with a theme-relative URL instead of the site root.
- Tightened share CSS so page share buttons stay within article content width and all share button children override the base `#main span` text cursor rule.
- Ran `npm run build`, `npm run check:js`, and targeted `php -l` checks for the changed PHP files; all passed. Restored cursor PNG build side effects afterward.
- Generated local verification package `releases/Yneko-Reimu-v0.1.13-20260603-1052.zip` with `npm run package`; verified it includes `post-share.php`, Xiaohongshu SVG assets, built CSS/JS, and the QR dynamic chunk, while excluding manifest/source files as intended.
- Diagnosed the loading-screen regression in the local validation package: the built `reimu.js` used `import.meta.url` from a Vite dynamic QR chunk while WordPress enqueued it as a classic script.
- Replaced the Weixin QR dynamic import with lazy classic-script loading and updated `tools/build-reimu.mjs` to copy `node_modules/qrcode/build/qrcode.js` into `assets/dist/qrcode.js`.
- Ran `npm run build`, verified `reimu.js` parses with `new Function`, confirmed no `import.meta` or dynamic `import(` remains in the built main script, and confirmed `assets/dist/qrcode.js` exists.
- Ran `npm run check:js` and `npm run package`; generated `releases/Yneko-Reimu-v0.1.13-20260603-1104.zip`.
- Verified the new ZIP includes `assets/dist/qrcode.js`, `template-parts/meta/post-share.php`, `assets/dist/xiaohongshu.svg`, and `assets/images/icons/xiaohongshu.svg`, while still excluding `assets/dist/manifest.json` and `assets/src/reimu.js`.
- Added post share rendering to the about, friend, and projects virtual templates and updated share context generation so virtual pages use their own localized URL/title/description.
- Confirmed hot comment sorting uses nested reply count under each top-level comment, with comment time as a tie-breaker.
- Added immediate profile avatar upload flow: local type/size validation, dedicated `yneko_reimu_profile_avatar_upload` AJAX action, upload button text stays as “上传”, upload URL is returned to the avatar input, and the modal closes after a successful upload.
- Added inline avatar status under the current-user avatar in the comment form, including updating, pending review, updated, and rejected states; rejected avatar reviews now persist `_yneko_reimu_avatar_status=rejected` for front-end feedback.
- Fixed special tag persistence by adding `_yneko_reimu_comment_special_badges_touched`, allowing saved users to enable more than the first special tag; custom tags are stored up to two slots and inactive rows are frozen instead of removed when special tags consume display capacity.
- Ran `npm run check:js`, targeted `php -l` checks, `npm run build`, and `npm run package`; generated `releases/Yneko-Reimu-v0.1.13-20260603-1146.zip`.
- Verified the `1146` ZIP includes the changed comments/template/enqueue/virtual/share files, built `reimu.js`, built `reimu.css`, and `assets/dist/qrcode.js`, while excluding manifest/source files as intended.
- Started follow-up refinement for share placement, GitHub avatar profile input, staged avatar upload saving, selectable custom tags, current-user avatar status placement, and admin review notification badges.
- Moved post share below copyright, about-page share below sponsor, and friend/projects share into the virtual footer after its separator line.
- Updated profile payloads so GitHub-login users without a custom avatar expose their GitHub avatar URL in the profile avatar input.
- Changed avatar file selection back to a staged profile-save flow with local validation and per-click hint clearing.
- Added selectable custom comment tags: up to five stored rows, with enabled custom tags plus enabled special tags capped at two.
- Added admin review badge counting for pending comment image/GIF uploads, avatar reviews, and user tag reviews, scoped to the matching review setting switches.
- Ran `npm run check:js`, targeted `php -l` checks, `npm run build`, classic-script parse verification, and `npm run package`; generated `releases/Yneko-Reimu-v0.1.13-20260603-1312.zip`.
- Verified the `1312` ZIP includes changed profile/settings/share files, built `reimu.js`, built `reimu.css`, `assets/dist/qrcode.js`, and the virtual footer/share templates.
- Started review status sync follow-up: added shared avatar/tag/comment review statuses, cleaned duplicate admin badge placement, added WordPress Appearance/Yneko-Reimu settings menu badges, and began front-end status polling/comment refresh work.
- Completed the review status sync follow-up: username-under prompts now cover avatar, tag, and comment pending/updated/rejected states; admin badges only show on settings tabs and concrete review sections; WordPress Appearance and Yneko-Reimu settings menu items get native pending badges; approved custom tags merge back into the active profile payload; front-end polling refreshes profile/avatar/tag/comment DOM and re-fetches the current comments block after review changes.
- Ran `npm run check:js`, targeted `php -l` checks for `inc/comments.php`, `inc/settings.php`, and `inc/enqueue.php`, `npm run build`, classic-script parse verification, and `npm run package`; generated `releases/Yneko-Reimu-v0.1.13-20260603-1406.zip`.
- Verified the `1406` ZIP contains `inc/comments.php`, `inc/settings.php`, `inc/enqueue.php`, built `assets/dist/reimu.js`, built `assets/dist/reimu.css`, `assets/dist/qrcode.js`, and refreshed `en_US`/`zh_CN` language files; restored cursor build side effects afterward.
## 2026-06-03 Development Standards and Guardrails

- Created local-only `PROJECT.md` and `AGENTS.md` with architecture, compatibility, performance, security, and agent execution rules.
- Added `PROJECT.md` and `AGENTS.md` to `.git/info/exclude` so they do not appear in Git status, do not sync to GitHub, and do not enter release ZIPs.
- Added `tools/check-size.mjs` to enforce short-term `reimu.js` and `reimu.css` budgets and classic script compatibility.
- Added `tools/check-package.mjs` to inspect the newest release ZIP for forbidden development/local files.
- Added `theme/Yneko-Reimu/assets/src/admin-settings.js` and updated Vite so it builds to `assets/dist/admin-settings.js`.
- Updated `settings.php` to enqueue the built admin settings script and inject only the `YNEKO_REIMU_ADMIN_I18N` config object before it.
- Updated `docs/development.md` with development constraints, size budgets, admin JS source ownership, and package checks.
- Updated the Lily cursor build to strip PNG metadata and exclude time/date chunks so repeated builds produce smaller, more stable cursor assets.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 67 theme PHP files, `npm run package`, and `npm run check:package`.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2208.zip` and reported 124 entries with no forbidden development files.

## 2026-06-03 Module Boundaries and Lazy-Loading Prep

- Committed and pushed the guardrails baseline as `6f613de Add development guardrails and admin asset build`; no v0.1.15 tag was created.
- Added `theme/Yneko-Reimu/assets/src/reimu/core.js` and wired `assets/src/reimu.js` to consume the extracted helpers while preserving `window.ReimuWP`.
- Split the front-end source boundary further into `dom.js`, `storage.js`, `events.js`, `search.js`, and `share.js`; `assets/src/reimu.js` remains the single public entrypoint and keeps the existing init order.
- Encapsulated Weixin share QR lazy loading inside the internal share module while continuing to load `assets/dist/qrcode.js` as a classic script.
- Updated `npm run check:js` to use `tools/check-js.mjs`, recursively checking all JS/MJS files under `theme/Yneko-Reimu/assets/src` and `tools`.
- Adjusted build ownership so CSS/static assets are built by Vite config and classic JS entries are built one at a time by `tools/build-reimu.mjs`.
- Moved comment media upload/review functions from `inc/comments.php` into `inc/comments/uploads.php`, with `comments.php` requiring the internal module.
- Verification passed after module split: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 68 theme PHP files, `npm run package`, and `npm run check:package`.
- Confirmed built `assets/dist/reimu.js` has no `import.meta`, dynamic `import(`, or top-level ESM import/export and parses as a classic script.
- Final module-split package check used `Yneko-Reimu-v0.1.15-20260603-2238.zip` and reported 125 entries with no forbidden development files.

## 2026-06-03 Settings Schema Split

- Split settings defaults, normalization, sanitization, and read helper functions from `inc/settings.php` into `inc/settings/schema.php`.
- Kept `inc/settings.php` as the runtime/admin entrypoint with settings registration, admin menu badges, page rendering, review admin sections, and admin asset enqueue.
- Ran `npm run lint:php`; fixed one PHPCS trailing-blank-line issue in the new schema module, then lint passed.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, and full `php -l` over 69 theme PHP files.
- Ran `npm run package` and `npm run check:package`; `Yneko-Reimu-v0.1.15-20260603-2251.zip` contains 126 entries, includes `inc/settings/schema.php`, and excludes development/local-only files.

## 2026-06-03 Settings Admin Helper Split

- Split admin settings helper functions from `inc/settings.php` into `inc/settings/admin.php`, including settings page registration, admin menu review badges, bilingual admin text helpers, review badge counters, media field helper, and admin settings asset enqueue.
- Kept `inc/settings.php` as the public entrypoint with settings registration, post-save badge cleanup, the main settings page renderer, and the review/row renderers.
- Verification passed: `npm run lint:php`, full `php -l` over 70 theme PHP files, `npm run check`, `npm audit --audit-level=moderate`, `npm run package`, and `npm run check:package`.
- Confirmed built `assets/dist/reimu.js` still has no `import.meta`, dynamic `import(`, or top-level ESM import/export and parses as a classic script.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2259.zip`, includes `inc/settings/admin.php` and `inc/settings/schema.php`, and excludes development/local-only files.

## 2026-06-03 Settings Renderer Split

- Split independent settings render helpers from `inc/settings.php` into `inc/settings/renderers.php`.
- Kept `yneko_reimu_render_settings_page()` and all settings form field names in `inc/settings.php`.
- Verification passed: `npm run lint:php`, full `php -l` over 71 theme PHP files, `npm run check`, `npm audit --audit-level=moderate`, `npm run package`, and `npm run check:package`.
- Confirmed built `assets/dist/reimu.js` still has no `import.meta`, dynamic `import(`, or top-level ESM import/export and parses as a classic script.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2307.zip`, includes `inc/settings/admin.php`, `inc/settings/schema.php`, and `inc/settings/renderers.php`, and excludes development/local-only files.

## 2026-06-03 Settings Page Split

- Split the main settings page renderer `yneko_reimu_render_settings_page()` from `inc/settings.php` into `inc/settings/page.php`.
- Kept `inc/settings.php` as the settings entrypoint with module requires, `register_setting()`, and blocked user badge cleanup after settings save.
- Verification passed: `npm run lint:php`, full `php -l` over 72 theme PHP files, `npm run check`, `npm audit --audit-level=moderate`, `npm run package`, and `npm run check:package`.
- Confirmed built `assets/dist/reimu.js` still has no `import.meta`, dynamic `import(`, or top-level ESM import/export and parses as a classic script.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2314.zip`, includes `inc/settings/admin.php`, `inc/settings/schema.php`, `inc/settings/renderers.php`, and `inc/settings/page.php`, and excludes development/local-only files.
- Next round should start runtime lazy-loading/budget enforcement rather than continuing settings-file decomposition.

## 2026-06-03 Runtime Loading Strategy

- Started the runtime lazy-loading and budget-enforcement round from a clean `main...origin/main` worktree.
- Added `tools/feature-loading-plan.mjs` with loading metadata for search, share, comments/profile, APlayer, PhotoSwipe, Mermaid, and KaTeX.
- Extended `tools/check-size.mjs` so `npm run check:size` reports the loading plan and fails if a feature entry is missing trigger, target loading mode, or gate metadata.
- Updated `docs/development.md` to document that the size check now also owns the feature loading plan report.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 72 theme PHP files, `npm run package`, and `npm run check:package`.
- Confirmed built `assets/dist/reimu.js` parses as a classic script and contains no `import.meta`, dynamic `import(`, or top-level ESM import/export.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2326.zip`, reported 129 entries, and excluded development/local-only files.
- Next round should start the first actual runtime source extraction target from this loading plan, preferably search or PhotoSwipe, while preserving the single classic public entry.

## 2026-06-03 Search Runtime Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu-search.js` as a lazy classic search runtime entry.
- Updated `theme/Yneko-Reimu/assets/src/reimu/search.js` to expose `openSearch()` in addition to `initSearch()`.
- Replaced the main-bundle search module import with a small lazy search trigger loader in `assets/src/reimu.js`.
- Updated `tools/build-reimu.mjs` so `assets/dist/reimu-search.js` is built as its own IIFE classic script.
- Updated `tools/feature-loading-plan.mjs`, `tools/check-size.mjs`, and `docs/development.md` so search is marked as a lazy runtime, the lazy runtime has a 24 KB budget, and classic compatibility checks cover both public runtime scripts.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 72 theme PHP files, `npm run package`, and `npm run check:package`.
- Size check results: `reimu.js` 108.6 KB / 120 KB, `reimu-search.js` 9.8 KB / 24 KB, and `reimu.css` 205.3 KB / 220 KB.
- Confirmed `assets/dist/reimu.js` and `assets/dist/reimu-search.js` parse as classic scripts and contain no `import.meta`, dynamic `import(`, or top-level ESM import/export.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2336.zip`, reported 130 entries, included `assets/dist/reimu-search.js`, and excluded manifest/source/local-only files.
- Next round should split or lazy-gate PhotoSwipe runtime behavior, because search now has the first interaction-loaded classic runtime.

## 2026-06-03 PhotoSwipe Runtime Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu/photoswipe.js` for the PhotoSwipe image wrapping and overlay implementation.
- Added `theme/Yneko-Reimu/assets/src/reimu-photoswipe.js` as a lazy classic runtime entry that registers `window.ReimuPhotoSwipeRuntime`.
- Replaced the main-bundle PhotoSwipe implementation with a small loader in `assets/src/reimu.js` that loads the runtime only when the feature is enabled and article images/items exist.
- Updated `tools/build-reimu.mjs`, `tools/check-size.mjs`, `tools/feature-loading-plan.mjs`, and `docs/development.md` so `assets/dist/reimu-photoswipe.js` is built, budgeted at 24 KB, reported as lazy runtime, and checked for classic script compatibility.
- Initial verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, plus explicit classic parse/import checks for `reimu.js`, `reimu-search.js`, and `reimu-photoswipe.js`.
- Initial size results: `reimu.js` 106.3 KB / 120 KB, `reimu-search.js` 9.8 KB / 24 KB, `reimu-photoswipe.js` 5.6 KB / 24 KB, and `reimu.css` 205.3 KB / 220 KB.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 72 theme PHP files, `npm run package`, and `npm run check:package`.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2345.zip`, reported 131 entries, included `assets/dist/reimu-photoswipe.js`, and excluded manifest/source/local-only files.
- Next round should either split another low-risk visual/content runtime or pause runtime splitting to review comments/profile before touching AJAX-sensitive code.

## 2026-06-03 Share Runtime Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu-share.js` as a lazy classic runtime entry that registers `window.ReimuShareRuntime`.
- Removed the direct `createShareModule` import from the main script and replaced it with a small loader that runs only when `.share-wrapper` is present.
- Kept the existing `assets/dist/qrcode.js` lazy loading inside `assets/src/reimu/share.js`; Weixin QR generation remains click-triggered.
- Updated `tools/build-reimu.mjs`, `tools/check-size.mjs`, `tools/feature-loading-plan.mjs`, and `docs/development.md` so `assets/dist/reimu-share.js` is built, budgeted at 24 KB, reported as lazy runtime, and checked for classic script compatibility.
- Initial verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, plus explicit classic parse/import checks for `reimu.js`, `reimu-search.js`, `reimu-photoswipe.js`, and `reimu-share.js`.
- Initial size results: `reimu.js` 104.5 KB / 120 KB, `reimu-search.js` 9.8 KB / 24 KB, `reimu-photoswipe.js` 5.6 KB / 24 KB, `reimu-share.js` 4.6 KB / 24 KB, and `reimu.css` 205.3 KB / 220 KB.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 72 theme PHP files, `npm run package`, and `npm run check:package`.
- Final package check used `Yneko-Reimu-v0.1.15-20260603-2353.zip`, reported 132 entries, included `assets/dist/reimu-share.js` and `assets/dist/qrcode.js`, and excluded manifest/source/local-only files.
- Next round should pause runtime splitting for a focused comments/profile safety and interface review before touching AJAX-sensitive code, unless choosing another non-AJAX visual runtime.

## 2026-06-03 Comments/Profile Safety Interface Review

- Started from a clean `main...origin/main` worktree.
- Reviewed `inc/comments.php`, `inc/comments/uploads.php`, `inc/enqueue.php`, and the comments/profile sections of `assets/src/reimu.js`.
- Recorded the comments/profile AJAX action map, nonce map, front-end payload map, DOM trigger map, and rebind surfaces in `findings.md`.
- Confirmed this round should not move runtime code because comments/profile includes auth, profile save, upload review, comment mutation, login-state DOM replacement, and polling refresh behavior.
- Updated `task_plan.md` with a completed comments/profile safety interface review phase.
- Next round should start a source-only extraction of low-risk comment UI utilities/binders from `assets/src/reimu.js`, keeping the built main classic script and all AJAX/profile handlers unchanged.

## 2026-06-04 Comment Media Source Module Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu/comment-media.js` for comment media token, preview, text counting, media limit, insert, replacement, and unsubmitted-upload cleanup helpers.
- Updated `assets/src/reimu.js` to consume the new internal module while leaving comment submit/upload/like/edit/delete, auth, profile save, and status polling in the main source file.
- Preserved PJAX config semantics by injecting `getConfig()` into the module instead of capturing the initial `window.REIMU_CONFIG` object.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, explicit classic-script parse checks for `reimu.js`, `reimu-search.js`, `reimu-photoswipe.js`, and `reimu-share.js`, `npm audit --audit-level=moderate`, `npm run lint:php`, full `php -l` over 72 theme PHP files, `npm run check`, `npm run package`, and `npm run check:package`.
- Final package check used `Yneko-Reimu-v0.1.15-20260604-0011.zip`, reported 132 entries, included `assets/dist/reimu.js`, and excluded `assets/src/reimu/comment-media.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md`.
- Next round should extract comment popover/tool binding helpers or profile form UI helpers as source-only modules, still keeping AJAX request handlers in the main bundle.

## 2026-06-04 Comment Tools Source Module Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu/comment-tools.js` for comment popover state, toolbar binding, GIF library rendering, quick insert, URL insert, and preview refresh binding.
- Kept comment upload row state and upload AJAX in `assets/src/reimu.js`, injecting `initCommentUploadRows()` into the module so upload behavior stays on the existing request path.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, explicit classic-script parse checks for `reimu.js`, `reimu-search.js`, `reimu-photoswipe.js`, and `reimu-share.js`, `npm audit --audit-level=moderate`, `npm run lint:php`, full `php -l` over 72 theme PHP files, and `npm run check`.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.1.15-20260604-0020.zip`.
- `npm run check:package` was first started in parallel with packaging and inspected the previous `0011` ZIP; rerunning after package completion correctly inspected `Yneko-Reimu-v0.1.15-20260604-0020.zip`.
- Final package check reported 132 entries, included `assets/dist/reimu.js`, and excluded `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md`.
- Next round should extract comment sorting/load-more helpers or profile form UI-only helpers, still keeping AJAX request handlers in the main bundle.

## 2026-06-04 Comment List Source Module Split

- Started from a clean `main...origin/main` worktree.
- Added `theme/Yneko-Reimu/assets/src/reimu/comment-list.js` for comment hot sorting, latest activity time, load-more item collection, load-more visibility syncing, sort mode lookup, and sort/load-more button binding.
- Kept submitted-comment insertion, comment submit, comment like, edit/delete, WordPress reply movement, login-state refresh, profile save, and profile polling in `assets/src/reimu.js`.
- Verification passed: `npm run check:js`, `npm run build`, `npm run check:size`, explicit classic-script parse checks for `reimu.js`, `reimu-search.js`, `reimu-photoswipe.js`, and `reimu-share.js`, `npm audit --audit-level=moderate`, `npm run lint:php`, full `php -l` over 72 theme PHP files, and `npm run check`.
- Ran `npm run package` followed by `npm run check:package`; generated and checked `releases/Yneko-Reimu-v0.1.15-20260604-0030.zip`.
- Final package check reported 132 entries, included `assets/dist/reimu.js`, and excluded `assets/src/reimu/comment-list.js`, `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md`.
- Next round should extract profile form UI-only helpers, or pause source splitting to reassess the remaining comments/profile code before any AJAX-sensitive extraction.

## 2026-06-04 Profile Form UI Source Module Split

- Added `theme/Yneko-Reimu/assets/src/reimu/profile-form.js` for profile modal URL normalization, password validation/toggles, avatar hint/dirty-state helpers, custom tag rendering, tag error display, and selected tag limit UI.
- Updated `theme/Yneko-Reimu/assets/src/reimu.js` to consume the new internal module while keeping profile fetch/save, email code, TOTP generation, avatar upload, login-state refresh, and profile review-status polling in the main entrypoint.
- Verification passed before final record updates: `npm run check:js`, `npm run build`, `npm run check:size`, explicit classic-script parse checks for `reimu.js`, `reimu-search.js`, `reimu-photoswipe.js`, and `reimu-share.js`, `npm audit --audit-level=moderate`, `npm run lint:php`, full `php -l` over 72 theme PHP files, `npm run check`, `npm run package`, and `npm run check:package`.
- Final size check in `npm run check` reported `reimu.js` at 108.3 KB / 120 KB, with lazy runtimes and CSS still under budget.
- Generated and checked `releases/Yneko-Reimu-v0.1.15-20260604-0038.zip`.
- Manual ZIP check confirmed `assets/dist/reimu.js` is included, while `assets/src/reimu/profile-form.js`, `assets/src/reimu/comment-list.js`, `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are excluded.
- Next round should stop broad source extraction briefly and build a focused manual QA checklist/runtime contract for comments and profile before any AJAX-sensitive handler is moved.

## 2026-06-04 Comments/Profile Runtime Contract

- Added `docs/comments-profile-contract.md` to document the comments/profile preserved surface: config keys, AJAX actions, DOM selectors, runtime invariants, allowed split boundaries, manual QA checklist, and verification commands.
- Linked the new contract from `docs/development.md` so future contributors see it before changing comments/profile AJAX handlers, login-state DOM replacement, or runtime boundaries.
- Updated `task_plan.md` and `findings.md` with the new contract as a safety gate for later modularity work.
- This round intentionally changed documentation/process files only; no runtime code or build artifacts were changed.
- Next round should audit remaining comments/profile code in `assets/src/reimu.js` against the contract and decide whether one more request-free source module is safe, or whether to pause front-end splitting until manual WordPress QA can be run.

## 2026-06-04 Profile Status UI Source Module Split

- Audited the remaining comments/profile functions in `assets/src/reimu.js` against `docs/comments-profile-contract.md`.
- Added `theme/Yneko-Reimu/assets/src/reimu/profile-status.js` for request-free profile review status UI: message mapping, status row normalization, inline current-user status rendering, pending-count badge rendering, and autohide scheduling.
- Kept `ackProfileStatuses()`, profile polling, profile fetch/save/email/TOTP/avatar request handlers, comment mutation handlers, login-state DOM replacement, and rebind orchestration in `assets/src/reimu.js`.
- Verification passed so far: `npm run check:js`, `npm run build`, `npm run check:size`, and explicit classic-script pattern checks for public runtime scripts.
- Size check reported `reimu.js` at 108.7 KB / 120 KB, with lazy runtimes and CSS still under budget.
- Next round should stop comments/profile source extraction unless manual WordPress QA is available, and should move to another low-risk optimization area.

## 2026-06-04 PHP Complexity Report Gate

- Added `tools/report-php-complexity.mjs`, a dependency-free Node report for PHP file size, named-function length, and approximate branch complexity under `theme/Yneko-Reimu`.
- Added `npm run report:php-complexity`.
- Updated `docs/development.md` to document the report as informational quality tooling rather than a failing check.
- Ran `npm run report:php-complexity`; baseline reported 72 PHP files, 566 named functions, 13,988 nonblank lines, and branch score 5,043.
- Initial hotspots: `inc/comments.php`, `inc/template-tags.php`, `inc/comments/uploads.php`, `inc/settings/page.php`, `inc/customizer.php`, and `inc/settings/schema.php`.
- Ran `npm run check:js`; 26 JavaScript files passed syntax checks.
- Next round should use the report to choose a low-risk PHP decomposition target or add CI/docs guidance for collecting this report without failing legacy code.

## 2026-06-04 Settings Panels Split

- Added `theme/Yneko-Reimu/inc/settings/panels.php`.
- Moved the friend-links settings panel and music-track settings panel out of `inc/settings/page.php` into internal renderer functions.
- Updated `inc/settings.php` to require the new panels module before `inc/settings/page.php`.
- Replaced the moved panel markup in `yneko_reimu_render_settings_page()` with `yneko_reimu_render_settings_friends_panel( $settings )` and `yneko_reimu_render_settings_music_panel( $settings, $player )`.
- Verified targeted syntax with `php -l` on `inc/settings.php`, `inc/settings/page.php`, and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 426 lines and score 503.
- Next round should continue with another self-contained settings panel, likely `external-comments` or `extensions`, while avoiding comments/profile PHP request handlers.

## 2026-06-04 Settings Extension Panels Split

- Moved the extensions panel and external-comments panel from `inc/settings/page.php` into `inc/settings/panels.php`.
- Replaced the moved markup with `yneko_reimu_render_settings_extensions_panel( $features, $third_party )` and `yneko_reimu_render_settings_external_comments_panel( $external_comments )`.
- Kept all `features`, `third_party`, and `external_comments` option names and tab keys unchanged.
- Verified targeted syntax with `php -l` on `inc/settings/page.php` and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 353 lines and score 426.
- Next round should continue with the search or i18n panel, or pause for manual admin UI checks.

## 2026-06-04 Settings Search/I18n Panels Split

- Moved the i18n panel and search panel from `inc/settings/page.php` into `inc/settings/panels.php`.
- Replaced the moved markup with `yneko_reimu_render_settings_i18n_panel( $i18n )` and `yneko_reimu_render_settings_search_panel( $search )`.
- Kept all `i18n` and `search` option names, tab keys, field names, and descriptive text unchanged.
- Verified targeted syntax with `php -l` on `inc/settings/page.php` and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 273 lines and score 353.
- Next round should either split one remaining settings panel cautiously, likely GitHub, or pause for manual admin UI checks before moving comments/users review sections.

## 2026-06-04 Settings GitHub Panel Split

- Moved the GitHub OAuth settings panel from `inc/settings/page.php` into `inc/settings/panels.php`.
- Replaced the moved markup with `yneko_reimu_render_settings_github_panel( $oauth, $callback )`.
- Kept all `github_oauth` option names, field names, callback placeholder behavior, client secret field behavior, auto-create checkbox, and bind/rebind UI unchanged.
- Verified targeted syntax with `php -l` on `inc/settings/page.php` and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 219 lines and score 298.
- Next round should pause for an admin settings UI check or split the comments/users renderer panels with extra care because they include review-management UI.

## 2026-06-04 Settings Comments Panel Split

- Moved the comments settings panel and comment upload manager section from `inc/settings/page.php` into `inc/settings/panels.php`.
- Replaced the moved markup with `yneko_reimu_render_settings_comments_panel( $settings )`.
- Kept all `comment_avatar_url` and `comment_upload` field names, tab keys, review helper calls, and admin descriptions unchanged.
- Verified targeted syntax with `php -l` on `inc/settings/page.php` and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 177 lines and score 243.
- Next round should either split the users renderer panel or run a focused admin settings UI check before closing the settings-page decomposition.

## 2026-06-04 Settings Users Panel Split

- Moved the users settings panel from `inc/settings/page.php` into `inc/settings/panels.php`.
- Replaced the moved markup with `yneko_reimu_render_settings_users_panel( $review_badges )`.
- Kept all `user_badges`, avatar-frame, avatar upload, badge review, avatar review field names, tab keys, badge counts, helper calls, and admin descriptions unchanged.
- Verified targeted syntax with `php -l` on `inc/settings/page.php` and `inc/settings/panels.php`.
- Ran `npm run lint:php`; PHPCS passed.
- Ran `npm run report:php-complexity`; `yneko_reimu_render_settings_page()` dropped to 114 lines and score 133.
- Next round should perform a focused admin settings UI/manual checklist and decide whether the settings-page decomposition phase can be considered complete.

## 2026-06-04 Settings Admin Contract Gate

- Added `tools/check-settings-admin-contract.mjs` to verify settings tabs, matching panels, extracted renderer calls, key settings fields, repeatable friend/music row fields, admin GIF upload form ownership, and comments/users review helper contracts.
- Added `npm run check:settings-admin` and included it in `npm run check`.
- Updated `docs/development.md` with the new command and settings panel ownership notes.
- Ran `npm run check:settings-admin`; it passed with 10 tabs and 10 panels verified.
- Next round should perform a final completion audit against the full plan before deciding whether the persistent goal can be marked complete.

## 2026-06-04 Final Optimization Completion Audit

- Confirmed the branch was clean and aligned with `origin/main` before the final audit record updates.
- Latest verification evidence before this audit: `npm run check`, `npm audit --audit-level=moderate`, `npm run report:php-complexity`, and full theme PHP syntax lint all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.1.15-20260604-0958.zip`.
- Ran `npm run check:package`; the final ZIP contains 134 entries and no forbidden development files.
- Spot-checked the final ZIP: `Yneko-Reimu/inc/settings/panels.php` and `Yneko-Reimu/inc/settings/page.php` are included; `Yneko-Reimu/tools/check-settings-admin-contract.mjs`, `PROJECT.md`, `AGENTS.md`, `task_plan.md`, `findings.md`, `progress.md`, `Yneko-Reimu/assets/src/reimu.js`, and `Yneko-Reimu/assets/dist/manifest.json` are absent.
- Ran `git diff --check`; no whitespace errors were reported.
- Ran `git tag --list 'v0.1.15'`; no tag exists, so no v0.1.15 tag was created or pushed.
- `PROJECT.md` and `AGENTS.md` remain local-only and absent from Git status.
- Manual WordPress admin UI/browser QA was not performed in this environment; settings structure is protected by the static contract gate, and comments/profile manual coverage is documented in `docs/comments-profile-contract.md`.
- This completes the current optimization plan. The next meaningful work should be a real WordPress admin/front-end manual QA pass before any further comments/profile runtime or request-handler split.

## 2026-06-04 Local WordPress QA Pass

- Started Docker Desktop and created a local-only WordPress QA environment under `.gitignore`-excluded `wp-local/`.
- Installed WordPress through a local-only PHP setup script, activated the mounted `Yneko-Reimu` theme, and seeded a QA post, test user, and baseline comment.
- Browser QA confirmed the real WordPress admin settings page renders 10 tabs and 10 panels, loads `assets/dist/admin-settings.js`, switches tabs without console errors, and keeps the Friend links panel reachable.
- Found a real admin settings regression: newly added repeatable rows did not get a row number/title because `refreshNumbers()` ignored the passed repeatable root.
- Fixed `theme/Yneko-Reimu/assets/src/admin-settings.js` so `refreshNumbers(root)` processes the root when it is itself `.yneko-reimu-repeatable`, then scans descendant repeatable sections.
- Rebuilt `theme/Yneko-Reimu/assets/dist/admin-settings.js`; `theme/Yneko-Reimu/assets/dist/manifest.json` now only reflects the new admin script size.
- Browser retest confirmed adding a friend row now updates headings through `Friend #4`.
- Front-end browser QA passed for search lazy loading, share runtime loading, Weixin QR second-stage `qrcode.js` loading, PhotoSwipe runtime loading after enabling the feature, profile modal smoke rendering, and AJAX comment insertion.
- Verification passed after the fix: `npm run check:js`, `npm run build`, `npm run check`, `npm audit --audit-level=moderate`, `npm run package`, `npm run check:package`, and full PHP syntax lint over all theme PHP files.
- Final package check used `Yneko-Reimu-v0.1.15-20260604-1037.zip` and reported 134 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Remaining QA depth: email/TOTP/avatar upload/media review/OAuth/admin approval-rejection flows were not fully exercised in this smoke pass.

## 2026-06-04 Comments/Profile Review Flow QA

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Added this QA round to `task_plan.md`.
- Created local-only `wp-local/seed-review-qa.php` and `wp-local/login-as.php`; `wp-local/` remains ignored and is not a public change.
- Enabled local review settings and seeded qauser with pending avatar, pending user badge, and a held comment containing a pending temporary image.
- Browser QA as qauser confirmed the post renders as logged in, the profile modal opens, and pending avatar text `头像审核中` is visible with no site console errors.
- Server-side profile save QA found and fixed an English localization gap: `个人资料已保存，评论标签审核中。` had an empty `en_US` translation, causing successful profile saves with pending comment badges to return an empty `message`.
- Updated `tools/build-i18n.mjs`, regenerated `theme/Yneko-Reimu/languages/en_US.po` and `theme/Yneko-Reimu/languages/en_US.mo`, and confirmed the profile save AJAX response now returns `Profile saved. Comment badges are pending review.`.
- Browser admin QA confirmed Users review cards render for pending badge/avatar states; approving the badge exposes revoke, and approving the avatar exposes delete.
- Browser/server admin QA confirmed Comments review cards render for pending temporary image uploads; direct admin-action verification promoted a temp image, approved the held comment, replaced the temp URL with the permanent upload URL, and created an approved attachment.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1100.zip` and reported 134 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should broaden automated coverage around i18n completeness or add a targeted check that important AJAX success/error strings in `en_US.po` are not empty.

## 2026-06-04 I18n Message Contract Gate

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Added `tools/check-i18n-messages.mjs` to verify 27 high-impact auth/profile/comment/upload/review `en_US.po` messages are present and non-empty.
- Added `npm run check:i18n-messages` and wired it into `npm run check` after `npm run build`, so regenerated gettext files are checked.
- Updated `tools/build-i18n.mjs` with focused English translations for avatar/tag/comment review statuses, pending upload review, invalid comment upload attachment, insufficient permissions, and comment update/delete feedback.
- Regenerated `theme/Yneko-Reimu/languages/en_US.po` and `theme/Yneko-Reimu/languages/en_US.mo`.
- Updated `docs/development.md` to document the focused i18n message contract.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1108.zip` and reported 134 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should either expand the i18n contract to email/OAuth security messages or move to another high-risk QA surface such as GitHub OAuth callback behavior.

## 2026-06-04 I18n Email/OAuth Contract Expansion

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Expanded `tools/check-i18n-messages.mjs` from 27 to 57 required high-impact English messages.
- Added coverage for registration email verification, lost-password verification, profile email/password/TOTP feedback, and GitHub OAuth callback/login/bind errors.
- Added missing English translation sources in `tools/build-i18n.mjs` for GitHub OAuth success/error messages and the lost-password registered-email prompt.
- Ran `npm run i18n`; gettext files regenerated with 504 strings.
- Ran `npm run check:i18n-messages`; the first run failed on missing `请输入注册邮箱。`, then passed after adding the translation source.
- Updated `docs/development.md`, `task_plan.md`, and `findings.md` to describe the expanded contract.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1117.zip` and reported 134 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should perform GitHub OAuth callback QA/error-path review or add a static OAuth endpoint/settings contract.

## 2026-06-04 GitHub OAuth Static Contract Gate

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Added `tools/check-github-oauth-contract.mjs` as a dependency-free static check for GitHub OAuth public and compatibility contracts.
- Added `npm run check:github-oauth` and wired it into `npm run check` before build/i18n/size/PHPCS.
- Updated `docs/development.md` to document the GitHub OAuth contract check.
- Ran `npm run check:github-oauth`; it passed with 10 contract groups verified.
- Updated `task_plan.md` and `findings.md` with this round's scope and next-round target.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1125.zip` and reported 134 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should perform OAuth error-path QA with local/stubbed callback states, or document a manual GitHub OAuth staging checklist.

## 2026-06-04 GitHub OAuth Error-Path QA

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Confirmed local Docker WordPress containers were running and the mounted Yneko-Reimu theme was active.
- Observed host-side `127.0.0.1:8095/wp-login.php?action=...` requests returning proxy-level 502, while container-internal requests reached WordPress normally.
- Verified missing callback response, unconfigured login start, expired state, configured GitHub redirect, fake token failure, and state transient consumption using container-internal requests.
- Created local-only ignored `wp-local/oauth-qa-*.php` helper scripts and copied them into the container to stub GitHub HTTP responses through WordPress filters.
- Verified stubbed API failure, invalid profile, no linked account, existing email, and bind-conflict error paths.
- Reset local OAuth settings back to empty and removed the temporary admin GitHub ID meta used for bind-conflict QA.
- Added `docs/github-oauth-qa.md` and linked it from `docs/development.md`.
- Updated `task_plan.md` and `findings.md` with the QA scope, evidence, and remaining real-app staging path.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1135.zip` and reported 135 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should either do real GitHub OAuth happy-path staging QA or move to another remaining high-risk surface such as email/TOTP QA.

## 2026-06-04 Email and TOTP QA

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Confirmed local Docker WordPress containers were running and the mounted Yneko-Reimu theme was active.
- Created/updated local-only ignored `wp-local/email-totp-qa.php` and copied it into the WordPress container; the helper is not staged and must not be committed.
- The first successful local helper run after fixes passed all targeted paths: registration invalid/code/cooldown/wrong/success, lost-password invalid/unknown/code/weak/wrong/success, profile email same/code/wrong/success, TOTP missing/generate/wrong/success, and 2FA login require/wrong/success.
- The helper captured 6 outbound messages through `pre_wp_mail`.
- QA exposed blank English verification emails because `en_US.po` had empty translations for verification email subjects/body template strings.
- Added English translation sources for registration, password-reset, and profile-email verification subjects/body/expiry messages in `tools/build-i18n.mjs`.
- Expanded `tools/check-i18n-messages.mjs` to require those 9 verification email template strings; `npm run check:i18n-messages` now reports 66 high-impact messages translated.
- Ran `npm run i18n`; regenerated `theme/Yneko-Reimu/languages/en_US.po` and `theme/Yneko-Reimu/languages/en_US.mo`.
- Added `docs/email-totp-qa.md` and linked it from `docs/development.md`.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1151.zip` and reported 136 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` remains empty; no release tag was created.
- Next round should perform staging/manual SMTP and browser-modal QA for email/TOTP, or move to real GitHub OAuth happy-path staging.

## 2026-06-04 Real SMTP and Browser Email/TOTP QA

- Started from a clean `main...origin/main` worktree with no `v0.1.15` tag.
- Confirmed WordPress Docker containers were running and the mounted Yneko-Reimu theme was active.
- Started local-only Mailpit container `wp-local-mailpit` on the same Docker network as WordPress.
- Added local-only `wp-local/mailpit-smtp.php` and copied it into the WordPress container as a mu-plugin so browser-triggered `wp_mail()` uses real SMTP delivery to Mailpit.
- Proved SMTP delivery with a direct `wp_mail()` call captured by Mailpit.
- Reset local QA users/transients with local-only helpers and cleared Mailpit by recreating the container.
- Browser QA opened `http://127.0.0.1:8095/yneko-qa-post/` successfully.
- Browser registration flow passed: code button countdown, real SMTP registration message, code submission, and return to login panel.
- Browser password-reset flow passed: code button countdown, real SMTP password-reset message, code submission, password reset, and return to login panel.
- Browser profile email flow passed: profile modal opened, code button countdown, real SMTP profile-email message, code submission, profile save, and current email display update.
- Browser TOTP flow passed: TOTP checkbox, secret generation, visible QR image, profile save with generated code, and confirmed `_yneko_reimu_totp_enabled` with no pending secret.
- Browser 2FA login flow passed: password-only login showed the two-factor prompt, entering the current generated TOTP code logged in successfully.
- Browser input limitation: clipboard-backed `fill()` / `type()` failed in this in-app browser backend; raw keyboard input was used, and `login-as.php` was used only to establish a browser session after server-side verification of reset credentials.
- Checked GitHub OAuth happy-path prerequisites: WordPress has no GitHub OAuth Client ID/Secret configured and no matching OAuth environment variables are present. Real GitHub OAuth happy-path QA cannot be completed without a real OAuth App and callback configuration.
- Updated `docs/email-totp-qa.md`, `docs/github-oauth-qa.md`, `task_plan.md`, `findings.md`, and `progress.md` with real SMTP/browser QA evidence and GitHub OAuth credential limitation.
- Verification passed: `npm run check` and `npm audit --audit-level=moderate`.
- `git status --short --branch` shows only public docs/record files modified; local-only `wp-local/` helpers remain ignored.
- Next step: commit and push public records; do not create the `v0.1.15` tag.
