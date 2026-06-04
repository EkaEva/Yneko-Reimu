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

## 2026-06-04 GitHub OAuth Happy-Path Prerequisite Audit

- Started from clean `main...origin/main`; `git tag --list 'v0.1.15'` returned no tag.
- Confirmed local WordPress, database, and Mailpit containers were running.
- Checked WordPress GitHub OAuth settings through the local helper: no Client ID, no Client Secret, no callback override, auto-create disabled.
- Checked environment variables for GitHub/OAuth/Yneko/tunnel credentials; none were present.
- Checked GitHub CLI: logged in as `EkaEva` for repo operations, but this is not an OAuth App Client Secret.
- Checked for local tunnel tools: `ngrok`, `cloudflared`, and `localtunnel` were not found.
- Updated `docs/github-oauth-qa.md` with required real-app inputs, callback URL format, observable popup/non-popup/bind success signals, and current blocked status.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, and `git diff --check`.
- Next step: commit and push public record/doc updates; do not create the `v0.1.15` tag.

## 2026-06-04 GitHub OAuth Real Happy-Path QA

- Received real GitHub OAuth App credentials from the user and treated the Client Secret as local-only QA input.
- Started/used the local-only `wp-local-oauth-proxy` nginx proxy so WordPress was reachable at `http://localhost:8080`.
- Configured the local WordPress site URL and GitHub OAuth settings through ignored `wp-local/` helpers, with callback `http://localhost:8080/wp-login.php?action=yneko_github_callback`.
- Verified OAuth start redirects to GitHub with the expected authorization parameters.
- Verified real non-popup GitHub OAuth login completes and returns to `http://localhost:8080/yneko-qa-post/`.
- Verified real GitHub account binding for the existing `qauser` account after clearing current and legacy GitHub meta from the auto-created user.
- Verified popup login from the comment login modal closes the popup, refreshes the opener state, closes the modal, and shows the logged-in profile UI.
- Verified linked non-popup login returns to the same existing `qauser` account and redirects back to the original post URL.
- Confirmed with the local helper that `qauser` is linked to GitHub login `EkaEva`.
- Updated `docs/github-oauth-qa.md`, `task_plan.md`, and `findings.md` with public evidence while omitting the OAuth Client Secret.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1242.zip` and reported 136 entries with no forbidden development files.
- `git tag --list 'v0.1.15'` returned no tag; no release tag was created.
- `git status --short --branch` shows only public docs/record files modified; local-only OAuth helpers, `PROJECT.md`, and `AGENTS.md` remain untracked/ignored.
- Next step: commit and push public records only; do not create the `v0.1.15` tag.

## 2026-06-04 v0.2.0 Version Line

- User confirmed the pending release should be promoted from `v0.1.15` to `v0.2.0`.
- Started from a clean `main...origin/main` worktree.
- Updated npm package metadata to `0.2.0` with `npm version --no-git-tag-version 0.2.0`; no Git tag was created.
- Updated `YNEKO_REIMU_VERSION`, the theme `style.css` header, README release examples, QA no-tag reminders, and release notes content to `0.2.0` / `v0.2.0`.
- Renamed `docs/release-notes-v0.1.15.md` to `docs/release-notes-v0.2.0.md`.
- Preserved historical progress entries that name earlier `v0.1.15` validation ZIPs because those are audit facts, not current release instructions.
- Verification passed after the version-line change: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.0-20260604-1254.zip` and reported 136 entries with no forbidden development files.
- `git tag --list 'v0.2.0'` and `git tag --list 'v0.1.15'` returned no tags; no release tag was created.
- Next step: commit and push public version-line changes only.

## 2026-06-04 Release Readiness Hardening

- Started from a clean `main...origin/main` worktree.
- Added `ABSPATH` direct-access guards to the five missing top-level wrapper templates: `author.php`, `category.php`, `home.php`, `search.php`, and `tag.php`.
- Updated the theme header `Tested up to` field from `6.5` to `7.0`.
- Added runtime `theme/Yneko-Reimu/readme.txt` with installation, privacy/remote-resource, credits, MIT license, and WordPress GPL ecosystem redistribution notes.
- Added `tools/check-release-readiness.mjs` and wired it into `npm run check` before build.
- Extended `tools/check-package.mjs` and `tools/package-theme.ps1` so runtime `readme.txt` is included in the release ZIP and required by the package check.
- Updated README, development docs, theme-check notes, release docs, v0.2.0 release notes, and NOTICE license wording for the new release-readiness baseline.
- Ran `npm run check:release-readiness`; it passed ABSPATH/style/readme checks and initially failed only on the expected screenshot blocker: current `screenshot.png` was `1910x1433`, while the new gate requires `1200x900`.
- User provided `C:\Users\86135\Downloads\screenshot.png`; inspected it as `1200x900` and copied it to `theme/Yneko-Reimu/screenshot.png`.
- Re-ran `npm run check:release-readiness`; it passed all guard, style, readme, and screenshot checks.
- Verification passed after screenshot replacement: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP readme/screenshot spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.0-20260604-1537.zip` and reported 137 entries with no forbidden development files.
- Attempted a local WordPress HTTP smoke check for homepage/category/tag/author/search wrapper templates on `127.0.0.1:8095`, but the host returned 502 and no matching local WordPress Docker container was running. Automated PHP/build/package gates passed; browser/runtime template QA remains dependent on restarting the local WordPress QA environment.
- Removed unused `theme/Yneko-Reimu/screenshot.webp` after confirming no code, docs, package script, or release-readiness check references it. Re-ran `npm run check:release-readiness` and `npm run check:package`; both passed.

## 2026-06-04 v0.2.1 Version and Template Smoke QA

- User requested the next round and version promotion to `v0.2.1`.
- Started with the release-readiness hardening changes still unstaged in the working tree and continued on top of them without reverting.
- Ran `npm version --no-git-tag-version 0.2.1`; no Git tag was created.
- Updated `YNEKO_REIMU_VERSION`, the theme `style.css` header, runtime `readme.txt`, README examples, QA no-tag reminders, and added `docs/release-notes-v0.2.1.md`.
- Confirmed local WordPress containers were running, but the local site still had `home` and `siteurl` set to `http://localhost:8080` from OAuth QA.
- Restored local-only WordPress `home` and `siteurl` to `http://127.0.0.1:8095` inside the QA container.
- Host requests through the default PowerShell HTTP stack still returned 502 because of proxy handling; using `curl.exe --noproxy "*"` reached the local WordPress site correctly.
- Smoke-tested wrapper-template URLs with no-proxy requests: homepage 200, category 200, author 200, search 200, and tag 404 due to missing local tag content. No response contained fatal, parse, or warning output.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP style/readme/screenshot spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1550.zip` and reported 138 entries with no forbidden development files.

## 2026-06-04 Customizer Contract Prep

- Started the maintenance-hotspot follow-up with `inc/customizer.php`, choosing a low-risk contract-first pass rather than moving large blocks immediately.
- Changed `yneko_reimu_customize_register()` into a thin public callback that delegates to internal `yneko_reimu_register_customizer_sections()`.
- Added `tools/check-customizer-contract.mjs` to verify the public customize hook, key panel/section IDs, key setting/control IDs, and sanitizer callbacks.
- Wired `npm run check:customizer` into `npm run check`.
- Updated development docs and v0.2.1 release notes to document the new Customizer contract gate.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, `npm run report:php-complexity`, targeted `php -l` for `inc/customizer.php`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1556.zip` and reported 138 entries with no forbidden development files.
- Next round: split `yneko_reimu_register_customizer_sections()` into focused internal helpers for preset/sidebar/visual/images/cards/articles/social/footer-virtual sections, protected by `npm run check:customizer`.

## 2026-06-04 Customizer Helper Split and Missing Image Refresh

- Replaced `theme/Yneko-Reimu/assets/images/comment-missing.webp` with the user-provided `C:\Users\86135\Downloads\comment-missing.webp`.
- Confirmed the source and runtime missing-image files both have length 10,234 bytes and SHA256 `F269AD5AA32128E321A44CC25D9F1D6435403D5F9945B581B123431600FD7DF7`.
- Confirmed the only runtime reference to `comment-missing.webp` is `theme/Yneko-Reimu/inc/comments/uploads.php:180`.
- Split `theme/Yneko-Reimu/inc/customizer.php` so `yneko_reimu_register_customizer_sections()` delegates to focused internal helpers for the panel, preset, sidebar widgets, visual, images, cards, articles, social, and footer/virtual page sections.
- Corrected a mechanical split boundary that had made `yneko_reimu_register_customizer_footer_virtual_sections()` a nested function inside the social helper even though PHP syntax passed.
- Ran `php -l theme\Yneko-Reimu\inc\customizer.php`; it passed.
- Ran `npm run check:customizer`; it passed.
- Ran `npm run report:php-complexity`; `inc/customizer.php` now reports 733 nonblank lines and 11 functions, with the largest Customizer helpers at 157 and 146 lines instead of one 739-line registration function.
- Ran `npm run check`; it passed, including JS syntax, settings/admin contract, Customizer contract, GitHub OAuth contract, release-readiness, build/i18n, i18n message contract, size/classic-script/loading checks, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1614.zip`.
- Ran `npm run check:package`; the ZIP contains 138 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/images/comment-missing.webp` is included at 10,234 bytes, `Yneko-Reimu/inc/customizer.php` and `Yneko-Reimu/readme.txt` are included, and `Yneko-Reimu/screenshot.webp`, `Yneko-Reimu/assets/src/reimu.js`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md` and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: split `inc/enqueue.php` around `yneko_reimu_enqueue_assets()` by extracting internal asset context/config helpers while preserving public handles, localized config keys, lazy runtime URLs, and classic script compatibility.

## 2026-06-04 Enqueue Contract and Helper Split

- Started the next optimization round on `theme/Yneko-Reimu/inc/enqueue.php`.
- Added `tools/check-enqueue-contract.mjs` to verify the public enqueue callback, key script/style handles, third-party asset paths, `window.REIMU_CONFIG` keys, and nonce names.
- Added `npm run check:enqueue` and wired it into `npm run check` before GitHub OAuth and release-readiness checks.
- Split `yneko_reimu_enqueue_assets()` into focused helpers:
  - `yneko_reimu_enqueue_theme_styles()`
  - `yneko_reimu_build_search_config()`
  - `yneko_reimu_frontend_i18n()`
  - `yneko_reimu_build_frontend_config()`
  - `yneko_reimu_enqueue_optional_vendor_assets()`
  - `yneko_reimu_enqueue_main_runtime()`
- Preserved `yneko_reimu_enqueue_assets()` as the public `wp_enqueue_scripts` callback.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`; it passed.
- Ran `npm run report:php-complexity`; `inc/enqueue.php` now reports 520 nonblank lines and 23 functions, and the former 287-line `yneko_reimu_enqueue_assets()` is no longer in the largest-function list.
- Ran `npm run check`; it passed, including the new enqueue contract, build/i18n, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1621.zip`.
- Ran `npm run check:package`; the ZIP contains 138 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/inc/enqueue.php` and `Yneko-Reimu/readme.txt` are included, while `Yneko-Reimu/tools/check-enqueue-contract.mjs`, `Yneko-Reimu/assets/src/reimu.js`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed the existing CRLF normalization warnings for `README.md` and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: audit CSS split candidates and add a CSS/component budget contract before moving any styles out of the main `reimu.css`.

## 2026-06-04 CSS Split Plan Gate

- Started the asset-budget follow-up by inspecting CSS build inputs and selector density.
- Confirmed CSS build inputs are `theme/Yneko-Reimu/assets/src/yneko-reimu-base.css` and `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`.
- Confirmed current `theme/Yneko-Reimu/assets/dist/manifest.json` reports `assets/dist/reimu.css` at 210,179 bytes, about 205.3 KB against the 220 KB budget.
- Added `tools/css-split-plan.mjs` with planned split candidates for comments/profile, APlayer, code content, PhotoSwipe, share, and search.
- Added `tools/check-css-split-plan.mjs` to verify candidate metadata, target output names, per-candidate budgets, source selectors, build CSS inputs, and the existing main `reimu.css` 220 KB budget.
- Added `npm run check:css-split` and wired it into `npm run check` before the build.
- The first `npm run check:css-split` failed because the planned search selector `#reimu-stats` does not exist in current CSS. Replaced it with `.reimu-results`, then the check passed.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new CSS split-plan gate.
- Ran `npm run check:css-split`; it passed.
- Ran `npm run check:js`; 34 JavaScript files passed syntax checks.
- Ran `npm run check`; it passed, including the new CSS split-plan gate, build/i18n, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1628.zip`.
- Ran `npm run check:package`; the ZIP contains 138 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu.css` is included, while `Yneko-Reimu/tools/css-split-plan.mjs`, `Yneko-Reimu/tools/check-css-split-plan.mjs`, source CSS, `Yneko-Reimu/assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed the existing CRLF normalization warnings for `README.md` and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: implement the first actual CSS split for APlayer/player styles as `assets/dist/reimu-player.css`, conditionally enqueue it through the existing player-enabled context, and lower or extend size gates based on the resulting headroom.

## 2026-06-04 APlayer CSS Runtime Split

- Started the first actual CSS split round for APlayer/player styles.
- Added `theme/Yneko-Reimu/assets/src/reimu-player.css` and moved the readable sidebar APlayer enhancement block out of `assets/src/yneko-reimu-adapter.css`.
- Left the compressed upstream/base `.aplayer` rules in the main CSS because they are embedded in the single-line reference snapshot and are riskier to cut by hand.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-player.css` and include `assets/src/reimu-player.css` in `manifest.json` `cssSources`.
- Updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-player` only when `$enable_aplayer` is true, with `yneko-reimu-aplayer` as its dependency.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 212 KB, and `assets/dist/reimu-player.css` has a 20 KB budget.
- Updated `tools/check-css-split-plan.mjs` so selector checks include `assets/src/reimu-player.css` and verify the new size budgets.
- Updated `tools/css-split-plan.mjs` so the APlayer candidate is marked as conditionally split to `assets/dist/reimu-player.css`.
- Updated `tools/check-enqueue-contract.mjs` to protect the `yneko-reimu-player` handle and `assets/dist/reimu-player.css` output path.
- Ran `npm run build`; `assets/dist/reimu.css` built at 200,770 bytes and `assets/dist/reimu-player.css` at 9,505 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`; it passed.
- Ran `npm run check:css-split`; it initially failed until the check script included `reimu-player.css` in selector scanning, then passed.
- Ran `npm run check:size`; it passed with `reimu.css` at 196.1 KB / 212 KB and `reimu-player.css` at 9.3 KB / 20 KB.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new player stylesheet split and budgets.
- Ran `npm run check`; it passed, including build/i18n, CSS split contract, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1637.zip`.
- Ran `npm run check:package`; the ZIP contains 139 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu.css` and `Yneko-Reimu/assets/dist/reimu-player.css` are included, while `Yneko-Reimu/assets/src/reimu-player.css`, CSS split tools, `Yneko-Reimu/assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: split PhotoSwipe enhancement styles into `assets/dist/reimu-photoswipe.css` and conditionally enqueue them alongside the existing PhotoSwipe feature gate/lazy runtime.

## 2026-06-04 PhotoSwipe CSS Runtime Split

- Started the next CSS split round for PhotoSwipe lightbox enhancement styles.
- Added `theme/Yneko-Reimu/assets/src/reimu-photoswipe.css` and moved the isolated PhotoSwipe overlay/item/navigation/mobile style block out of `assets/src/yneko-reimu-adapter.css`.
- Kept generic article image, dark-mode image, `.pswp__img`, and article gallery cursor rules in the main CSS.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-photoswipe.css` and include `assets/src/reimu-photoswipe.css` in `manifest.json` `cssSources`.
- Updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-photoswipe-enhance` only when `yneko_reimu_photoswipe_enable` is true, with `yneko-reimu-photoswipe` as its dependency.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 208 KB, and `assets/dist/reimu-photoswipe.css` has a 12 KB budget.
- Updated `tools/check-css-split-plan.mjs`, `tools/css-split-plan.mjs`, and `tools/check-enqueue-contract.mjs` so the new source file, output file, budget, and enqueue handle are protected.
- Ran `npm run build`; `assets/dist/reimu.css` built at 198,949 bytes and `assets/dist/reimu-photoswipe.css` at 1,842 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`, `npm run check:css-split`, and `npm run check:size`; all passed.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new PhotoSwipe stylesheet split and budgets.
- Ran `npm run check`; it passed, including build/i18n, CSS split contract, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1645.zip`.
- Ran `npm run check:package`; the ZIP contains 140 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu.css`, `Yneko-Reimu/assets/dist/reimu-player.css`, and `Yneko-Reimu/assets/dist/reimu-photoswipe.css` are included, while `Yneko-Reimu/assets/src/reimu-photoswipe.css`, `Yneko-Reimu/assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: split article share/Weixin popup styles into `assets/dist/reimu-share.css` and conditionally enqueue them on pages that render `.share-wrapper`.

## 2026-06-04 Share CSS Runtime Split

- Started the next CSS split round for article share and Weixin popup enhancement styles.
- Confirmed actual share markup is rendered from `theme/Yneko-Reimu/template-parts/meta/post-share.php`, not a generic `template-parts/share.php`.
- Added `theme/Yneko-Reimu/assets/src/reimu-share.css` and moved the readable `.share-wrapper`, `.share-link`, `.share-icon`, `.share-weixin`, Weixin card child, active-state, and mobile positioning style block out of `assets/src/yneko-reimu-adapter.css`.
- Kept shared sidebar/social icon glyph/color rules and compressed upstream/base share rules in the main CSS.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-share.css` and include `assets/src/reimu-share.css` in `manifest.json` `cssSources`.
- Added internal `yneko_reimu_should_enqueue_share_styles()` and updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-share` only when share markup can render, with `yneko-reimu-main` as its dependency.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 204 KB, and `assets/dist/reimu-share.css` has a 14 KB budget.
- Updated `tools/check-css-split-plan.mjs`, `tools/css-split-plan.mjs`, and `tools/check-enqueue-contract.mjs` so the new source file, output file, budget, and enqueue handle are protected.
- Ran `npm run build`; `assets/dist/reimu.css` built at 195,989 bytes and `assets/dist/reimu-share.css` at 2,940 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`, `npm run check:css-split`, and `npm run check:size`; all passed.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new share stylesheet split and budgets.
- Ran `npm run check`; it passed, including build/i18n, CSS split contract, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1653.zip`.
- Ran `npm run check:package`; the ZIP contains 141 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu.css`, `Yneko-Reimu/assets/dist/reimu-player.css`, `Yneko-Reimu/assets/dist/reimu-photoswipe.css`, and `Yneko-Reimu/assets/dist/reimu-share.css` are included, while `Yneko-Reimu/assets/src/reimu-share.css`, `Yneko-Reimu/assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: split code/content enhancement styles into `assets/dist/reimu-code.css`, focusing on YML/code editor, virtual-page highlight, and Mermaid content selectors while keeping generic article typography in the main CSS.

## 2026-06-04 Code/Content CSS Runtime Split

- Started the next CSS split round for code/content enhancement styles.
- Added `theme/Yneko-Reimu/assets/src/reimu-code.css` and moved the readable YML/code editor, virtual-page highlight, Mermaid, and related mobile code-editor style blocks out of `assets/src/yneko-reimu-adapter.css`.
- Kept generic article typography, image sizing, broad cursor rules, and compressed upstream/base CSS in the main CSS.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-code.css` and include `assets/src/reimu-code.css` in `manifest.json` `cssSources`.
- Added internal `yneko_reimu_should_enqueue_code_styles()` and updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-code` on singular and virtual-page contexts, with `yneko-reimu-main` as its dependency.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 200 KB, and `assets/dist/reimu-code.css` has a 24 KB budget.
- Updated `tools/check-css-split-plan.mjs`, `tools/css-split-plan.mjs`, and `tools/check-enqueue-contract.mjs` so the new source file, output file, budget, and enqueue handle are protected.
- Ran `npm run build`; `assets/dist/reimu.css` built at 192,348 bytes and `assets/dist/reimu-code.css` at 3,601 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`, `npm run check:css-split`, and `npm run check:size`; all passed.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new code/content stylesheet split and budgets.
- Ran `npm run check`; it passed, including build/i18n, CSS split contract, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1703.zip`.
- Ran `npm run check:package`; the ZIP contains 142 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu-code.css` is included, while `Yneko-Reimu/assets/src/reimu-code.css`, `Yneko-Reimu/assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: audit the remaining CSS split candidates and either split search popup styles into `assets/dist/reimu-search.css` or defer search/comments if the selector boundary is too entangled.

## 2026-06-04 Search CSS Runtime Split

- Started the next CSS split round by auditing search selectors in `assets/src/yneko-reimu-base.css`, `assets/src/yneko-reimu-adapter.css`, `template-parts/layout/search-popup.php`, and the lazy search runtime.
- Confirmed the full popup/result-list base layout is still embedded in the compressed upstream/base CSS snapshot, so this round avoids cutting that single-line source.
- Added `theme/Yneko-Reimu/assets/src/reimu-search.css` and moved readable search result form, search-popup body state, search result type label, popup input cursor, and search background image enhancement rules out of `assets/src/yneko-reimu-adapter.css`.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-search.css` and include `assets/src/reimu-search.css` in `manifest.json` `cssSources`.
- Updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-search` globally before `yneko-reimu-main`, matching the globally rendered search popup template while keeping `reimu-search.js` lazy-loaded on interaction.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 198 KB, and `assets/dist/reimu-search.css` has a 16 KB budget.
- Updated `tools/check-css-split-plan.mjs`, `tools/css-split-plan.mjs`, and `tools/check-enqueue-contract.mjs` so the new source file, output file, budget, and enqueue handle are protected.
- Ran `npm run build`; `assets/dist/reimu.css` built at 191,405 bytes and `assets/dist/reimu-search.css` at 1,289 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`, `npm run check:css-split`, and `npm run check:size`; all passed.
- Updated `docs/development.md` and `docs/release-notes-v0.2.1.md` with the new search stylesheet split and budgets.
- Ran `npm run check`; it passed, including build/i18n, CSS split contract, size/classic-script checks, release-readiness, and PHP standards wrapper.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1713.zip`.
- Ran `npm run check:package`; the ZIP contains 143 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu-search.css` is included, while `Yneko-Reimu/assets/src/reimu-search.css`, `Yneko-Reimu/assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: audit comments/profile CSS and runtime contracts before deciding whether to split `assets/dist/reimu-comments.css` or first add a narrower comments/profile static contract.

## 2026-06-04 Comments/Profile Static Contract Gate

- Started the comments/profile safety round by re-reading `PROJECT.md`, planning records, and the current git status.
- Audited front-end comments/profile code in `assets/src/reimu.js` and internal modules, plus PHP handlers in `inc/comments.php` and `inc/comments/uploads.php`.
- Added `tools/check-comments-profile-contract.mjs` to verify high-risk AJAX actions, nonce creation and verification, `REIMU_CONFIG` keys, front-end request payload fields, key DOM selectors, comments/profile source module boundaries, and CSS anchors.
- Added `npm run check:comments-profile` and wired it into `npm run check` after the enqueue contract gate.
- The first `npm run check:comments-profile` run failed because the draft gate used stale or overly strict names for `ReimuReload`, profile avatar upload, nonce attributes, and login-state/profile-polling functions.
- Corrected the gate to match the actual preserved contracts: `refreshCommentLoginState`, `applyCommentLoggedInState`, `applyCommentLoggedOutState`, `startProfileStatusPolling`, PHP-rendered nonce data attributes, and the current profile-save `avatar_file` path with the legacy avatar upload AJAX endpoint preserved.
- Ran `npm run check:comments-profile`; it passed.
- Ran `node --check tools/check-comments-profile-contract.mjs`; it passed.
- Ran `npm run check:js`; 35 JavaScript files passed syntax checks.
- Updated `docs/development.md`, `docs/comments-profile-contract.md`, `docs/release-notes-v0.2.1.md`, `task_plan.md`, `findings.md`, and `progress.md` with the new comments/profile static gate.
- Ran `npm run check`; it passed, including JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build/i18n, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1721.zip`.
- Ran `npm run check:package`; the ZIP contains 143 entries and no forbidden development files.
- ZIP spot check confirmed runtime `readme.txt` and split CSS files are included, while `tools/check-comments-profile-contract.mjs`, `assets/src/reimu.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed existing CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- After documenting the verification results, ran `npm run check:comments-profile`, `npm run check:js`, `git diff --check`, `npm run package`, and `npm run check:package` again. The final package for this round is `releases/Yneko-Reimu-v0.2.1-20260604-1723.zip` with 143 entries and no forbidden development files.
- Final ZIP spot check confirmed `readme.txt`, `docs/development.md`, `docs/comments-profile-contract.md`, `assets/dist/reimu.css`, and `assets/dist/reimu-search.css` are included, while `tools/check-comments-profile-contract.mjs`, `assets/src/reimu.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round: attempt a stylesheet-only comments/profile CSS split into `assets/dist/reimu-comments.css` if the selector boundary is clean enough, while keeping all AJAX/profile/comment runtime handlers in the main classic script.

## 2026-06-04 Comments/Profile CSS Runtime Split

- Started the stylesheet-only comments/profile split round.
- Added `theme/Yneko-Reimu/assets/src/reimu-comments.css` and moved comments, login modal, profile modal, comment upload, profile tag override, dark-mode comment/profile, and login/profile body-state styles out of `assets/src/yneko-reimu-adapter.css`.
- Kept all comments/profile AJAX, nonce, PHP handler, request payload, DOM replacement, polling, and main classic runtime behavior unchanged.
- Kept generic `.reimu-load-more` styles in the main CSS because projects virtual pages use the same load-more classes.
- Kept mixed mobile comment rules in the main CSS for now because they share a responsive block with project/category/friend layout rules.
- Updated `tools/build-reimu.mjs` to emit `assets/dist/reimu-comments.css` and include `assets/src/reimu-comments.css` in `manifest.json` `cssSources`.
- Updated `theme/Yneko-Reimu/inc/enqueue.php` to enqueue `yneko-reimu-comments` globally before `yneko-reimu-main`, because login/profile modal markup can be rendered outside singular comment pages.
- Updated `tools/check-size.mjs`: main `assets/dist/reimu.css` budget is now 150 KB, and `assets/dist/reimu-comments.css` has a 52 KB budget.
- Updated `tools/check-css-split-plan.mjs`, `tools/css-split-plan.mjs`, `tools/check-enqueue-contract.mjs`, and `tools/check-comments-profile-contract.mjs` so the new comments stylesheet source, output file, budget, enqueue handle, and CSS anchors are protected.
- Ran `npm run build`; `assets/dist/reimu.css` built at 142,448 bytes and `assets/dist/reimu-comments.css` at 48,592 bytes.
- Ran `php -l theme\Yneko-Reimu\inc\enqueue.php`; it passed.
- Ran `npm run check:enqueue`, `npm run check:comments-profile`, `npm run check:css-split`, and `npm run check:size`; all passed.
- Updated `docs/development.md`, `docs/release-notes-v0.2.1.md`, `task_plan.md`, `findings.md`, and `progress.md` with the comments/profile stylesheet split and global enqueue rationale.
- Ran `npm run check`; it passed, including JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build/i18n, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 73 theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1738.zip`.
- Ran `npm run check:package`; the ZIP contains 144 entries and no forbidden development files.
- ZIP spot check confirmed `Yneko-Reimu/assets/dist/reimu-comments.css`, `Yneko-Reimu/assets/dist/reimu.css`, `Yneko-Reimu/assets/dist/reimu-search.css`, `Yneko-Reimu/readme.txt`, and `Yneko-Reimu/docs/development.md` are included, while `Yneko-Reimu/assets/src/reimu-comments.css`, CSS/check tools, `Yneko-Reimu/assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed existing CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Next round: perform a closure audit against the original “Yneko-Reimu 当前不足审查与优化计划”, identify any remaining code/documentation gaps, and close the plan if only documented future work remains.

## 2026-06-04 Comments/Profile Modal Renderer Split

- Started the final maintainability/closure round for the original “Yneko-Reimu 当前不足审查与优化计划”.
- Confirmed the internal modal split keeps `inc/comments.php` as the comments/profile entrypoint and requires `inc/comments/modals.php`.
- Confirmed `inc/comments/modals.php` has an `ABSPATH` guard and contains only `yneko_reimu_login_modal()`, `yneko_reimu_login_modal_html()`, `yneko_reimu_profile_modal()`, and `yneko_reimu_profile_modal_html()`.
- Updated `tools/check-comments-profile-contract.mjs` to read `inc/comments/modals.php` so login/profile modal DOM selectors remain covered after the PHP split.
- Ran `php -l theme\Yneko-Reimu\inc\comments.php`; it passed.
- Ran `php -l theme\Yneko-Reimu\inc\comments\modals.php`; it passed.
- Ran `npm run check:comments-profile`; it passed.
- Ran `npm run check:release-readiness`; it passed and reported 74 guarded runtime PHP files, `Tested up to: 7.0`, runtime `readme.txt`, and a `1200x900` screenshot.
- Ran `npm run report:php-complexity`; it scanned 74 PHP files and reported `inc/comments.php` at 2764 nonblank lines after the modal split.
- Updated `docs/comments-profile-contract.md`, `docs/development.md`, and `docs/release-notes-v0.2.1.md` with the internal modal renderer split.
- Ran `npm run check`; it passed across JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build/i18n, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full `php -l` over 74 runtime theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1751.zip`.
- Ran `npm run check:package`; the ZIP contains 145 entries and no forbidden development files.
- ZIP spot check confirmed `inc/comments/modals.php`, `assets/dist/reimu-comments.css`, runtime `readme.txt`, and `screenshot.png` are included, while `assets/src/reimu-comments.css`, `assets/dist/manifest.json`, tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed existing CRLF normalization warnings for `README.md`, `theme/Yneko-Reimu/assets/src/yneko-reimu-adapter.css`, and `theme/Yneko-Reimu/inc/customizer.php`.
- Closed the approved “Yneko-Reimu 当前不足审查与优化计划” for the GitHub Release professional theme target. No next round is required for this plan.

## 2026-06-04 Customizer Update Time Control

- Audited front-end `theme_mod` reads against Customizer and settings-page UI coverage.
- Confirmed `yneko_reimu_show_update_time` was the true missing UI entry; legacy friend links, APlayer JSON, old social URL fallbacks, and compatibility avatar settings were intentionally not exposed.
- Added `yneko_reimu_show_update_time` to the Customizer article-page controls with default `false` and `yneko_reimu_sanitize_checkbox`.
- Updated `tools/check-customizer-contract.mjs` so the updated-date control is protected by `npm run check:customizer`.
- Updated README and `docs/development.md` to mention the article updated-date control and the Customizer coverage rule for front-end-visible `theme_mod` modules.
- Ran `php -l theme\Yneko-Reimu\inc\customizer.php`; it passed.
- Ran `npm run check:customizer`; it passed.
- Ran `npm run check:js`; it passed.
- Ran `npm run build`; it regenerated 505 gettext strings and completed successfully.
- Ran `npm run check`; it passed across all configured gates.
- Generated local validation package with `npm run package`: `releases/Yneko-Reimu-v0.2.1-20260604-1837.zip`.
- Ran `npm run check:package`; the ZIP contains 145 entries and no forbidden development files.
- ZIP spot check confirmed runtime `readme.txt`, `screenshot.png`, `inc/customizer.php`, language MO files, `assets/dist/reimu.css`, and `assets/dist/reimu-comments.css` are included, while `assets/src/reimu.js`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.

## 2026-06-04 Image And SVG Resource Hygiene

- Started the image/SVG resource hygiene round after confirming the desired rule: cacheable/reusable images should be standalone files, while small UI SVG components can remain inline.
- Set `vite.config.mjs` `build.assetsInlineLimit` to `0`, so small CSS-referenced images no longer become base64 payloads in `assets/dist/reimu.css`.
- Added `tools/check-assets.mjs`, exposed it as `npm run check:assets`, and wired it into `npm run check` after build.
- Preserved the original comment-login password visibility SVG shapes by extracting them into `theme/Yneko-Reimu/assets/images/icons/password-hidden.svg` and `password-visible.svg`.
- Updated `theme/Yneko-Reimu/assets/src/reimu-comments.css` to reference the standalone password icon SVG files through CSS masks.
- Updated `tools/build-reimu.mjs` to remove duplicate Vite-copied Lily cursor PNGs from `assets/dist`; the canonical cursor files remain in `assets/images/cursor`.
- Ran `npm run build`; it passed and produced standalone `assets/dist/taichi.png` without the duplicate Lily cursor files in `assets/dist`.
- Ran `npm run check:assets`; it passed across 116 runtime PHP/CSS/JS files, and a targeted `rg` found no `data:image`, SVG base64, or `;base64,` payloads in runtime source/build paths.
- Updated README, `docs/development.md`, runtime `readme.txt`, release notes, `task_plan.md`, `findings.md`, and this progress log with the resource convention and new gate.
- Ran `npm run check`; it passed across JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build, asset hygiene, i18n message, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 74 runtime theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.1-20260604-1906.zip`.
- Ran `npm run check:package`; the ZIP contains 148 entries and no forbidden development files.
- ZIP spot check confirmed `assets/images/icons/password-hidden.svg`, `assets/images/icons/password-visible.svg`, `assets/dist/taichi.png`, `assets/dist/reimu.css`, and `assets/dist/reimu-comments.css` are included, while `assets/dist/manifest.json`, `assets/src/reimu-comments.css`, `PROJECT.md`, and `AGENTS.md` are absent.
- Package inline-image scan confirmed no `data:image`, SVG base64, or `;base64,` payloads in packaged PHP/CSS/JS.
- Ran `git diff --check`; no whitespace errors were reported. Git printed existing CRLF normalization warnings for `README.md` and `theme/Yneko-Reimu/assets/src/reimu-comments.css`.
- The image/SVG resource hygiene plan is complete. No next round is required for this plan unless a future icon system migration is desired.

## 2026-06-04 v0.2.2 PJAX Share/Footer Style Fix

- Started a focused bug-fix round for article/virtual-page footer layout disorder on first PJAX navigation.
- Confirmed `reimu-share.css` was previously conditionally enqueued through `yneko_reimu_should_enqueue_share_styles()`, which works on full refresh but can miss share/footer markup inserted by PJAX.
- Updated `theme/Yneko-Reimu/inc/enqueue.php` so `yneko-reimu-share` is globally enqueued before `yneko-reimu-main`, while `reimu-share.js` remains lazy-loaded only when `.share-wrapper` exists.
- Removed the no-longer-needed `yneko_reimu_should_enqueue_share_styles()` helper.
- Updated `tools/css-split-plan.mjs` and `docs/development.md` to document the global share stylesheet rationale for PJAX safety.
- Updated the version line to `0.2.2` in `package.json`, `package-lock.json`, `style.css`, `functions.php`, runtime `readme.txt`, and README release examples.
- Added `docs/release-notes-v0.2.2.md` following the previous bilingual release-note format.
- Ran targeted checks: `npm run check:enqueue`, `npm run check:css-split`, and `npm run check:release-readiness`; all passed.
- Ran `npm run build`; it passed and rebuilt v0.2.2 assets.
- Ran `npm run check`; it passed across JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build, asset hygiene, i18n message, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 74 runtime theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.2-20260604-2114.zip`.
- Ran `npm run check:package`; the ZIP contains 149 entries and no forbidden development files.
- ZIP spot check confirmed `assets/dist/reimu-share.css`, `assets/dist/reimu.css`, `assets/dist/reimu-share.js`, and `docs/release-notes-v0.2.2.md` are included, while `assets/dist/manifest.json`, `assets/src/reimu-share.css`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported. Git printed an existing CRLF normalization warning for `README.md`.

## 2026-06-04 v0.2.2 Comments/Profile/Auth Handler Split

- Started the v0.2.2 extension pass to keep the PJAX share/footer fix and add a safer comments/profile/auth PHP handler boundary split.
- Added internal `theme/Yneko-Reimu/inc/comments/auth.php`, `profile.php`, and `mutations.php` modules, each with an `ABSPATH` guard.
- Kept `theme/Yneko-Reimu/inc/comments.php` as the comments/profile entrypoint and added `require_once` calls for the three new modules.
- Moved existing login-state, logout, login, registration, lost-password, profile, TOTP, avatar, comment like, comment submit, comment edit, comment delete, visible-comment, render-item, and review-status-sync functions without changing function names or hook callback names.
- Left shared rendering and later comment-moderation/upload helpers in `comments.php` or their existing modules where they already belonged.
- Updated `tools/check-comments-profile-contract.mjs` so it reads `auth.php`, `profile.php`, and `mutations.php`, routes handler checks to the owning module, checks dynamic comment nonces in `mutations.php`, and verifies `comments.php` loads each internal module.
- Updated `docs/comments-profile-contract.md`, `docs/development.md`, `docs/release-notes-v0.2.2.md`, runtime `readme.txt`, and planning records to describe the internal handler split and preserved public contracts.
- Ran targeted PHP syntax checks for `comments.php`, `auth.php`, `profile.php`, and `mutations.php`; all passed.
- Ran `npm run check:comments-profile`, `npm run check:js`, `npm run build`, and `npm run check:size`; all passed.
- Ran `npm run check`; it passed across JS syntax, settings admin, Customizer, enqueue, comments/profile, GitHub OAuth, release-readiness, CSS split, build, asset hygiene, i18n messages, size/classic-script, and PHP standards wrapper gates.
- Ran `npm audit --audit-level=moderate`; it reported 0 vulnerabilities.
- Ran full PHP syntax lint over 77 runtime theme PHP files; all passed.
- Ran `npm run package`; generated `releases/Yneko-Reimu-v0.2.2-20260604-2140.zip`.
- Ran `npm run check:package`; the ZIP contains 152 entries and no forbidden development files.
- ZIP spot check confirmed `inc/comments/auth.php`, `inc/comments/profile.php`, `inc/comments/mutations.php`, `assets/dist/reimu-share.css`, and `docs/release-notes-v0.2.2.md` are included, while `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Ran `git diff --check`; no whitespace errors were reported.

## 2026-06-04 v0.2.3 Comments Rendering Split

- Started the v0.2.3 maintenance round focused on comments rendering, without changing comment/profile/login/upload/PJAX behavior.
- Added internal `theme/Yneko-Reimu/inc/comments/rendering.php` with an `ABSPATH` guard.
- Kept `theme/Yneko-Reimu/inc/comments.php` as the comments/profile entrypoint and added a `require_once` for the rendering module.
- Moved existing comment toolbar, guest fields, login link, current-user identity HTML, comment author link, comment summary, avatar rendering, UA/IP badge rendering, Markdown rendering, comment callback, comment field ordering, and external comment panel rendering into `rendering.php`.
- Preserved existing function names, `comment_form_fields` filter registration, `wp_list_comments()` callback name, DOM classes, data attributes, dynamic nonce creation, external comment script output, and `ipwho.is` lookup behavior.
- Updated `tools/check-comments-profile-contract.mjs` so it reads `rendering.php`, verifies that `comments.php` loads it, checks dynamic nonce creation in the rendering module, and protects comment/external-comment rendering anchors.
- Updated version fields to `0.2.3` and added `docs/release-notes-v0.2.3.md`.
- Updated `docs/comments-profile-contract.md`, `docs/development.md`, runtime `readme.txt`, and planning records to describe the rendering split.
- Targeted checks passed: `php -l` for `inc/comments.php` and `inc/comments/rendering.php`, `npm run check:comments-profile`, `node --check tools/check-comments-profile-contract.mjs`, and `npm run report:php-complexity`.
- Complexity report now scans 78 PHP files, and `inc/comments.php` is down to 986 nonblank lines after moving rendering responsibilities into the internal module.
- Full verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 78 runtime theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Generated local validation package `releases/Yneko-Reimu-v0.2.3-20260604-2210.zip`; it contains `inc/comments/rendering.php` and `docs/release-notes-v0.2.3.md`, and excludes `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md`.
- `git diff --check` passed with only the existing CRLF normalization warning for `README.md`.

## 2026-06-04 v0.2.3 Template Tags Split

- Started the v0.2.3 extension round after confirming the comments rendering split alone felt too small for the release.
- Kept `theme/Yneko-Reimu/inc/template-tags.php` as the Template Tags entrypoint loaded by `functions.php`.
- Added internal modules under `theme/Yneko-Reimu/inc/template-tags/`: `layout-content.php`, `social-share.php`, `navigation-virtual.php`, and `content-tools.php`.
- Moved existing Template Tags functions and hook/filter/shortcode registrations into the internal modules without renaming functions, virtual page slugs, template paths, navigation URLs, share URLs, GitHub transient keys, or public hooks.
- Added `tools/check-template-tags-contract.mjs`, exposed it as `npm run check:template-tags`, and wired it into `npm run check`.
- Ran targeted syntax checks for `inc/template-tags.php` and the four new Template Tags modules; all passed.
- Ran `npm run check:template-tags`; it passed after matching the contract gate to the current dynamic setting-key and virtual-template structure, and after adding the public menu Walker classes to the navigation module contract.
- Ran `npm run report:php-complexity`; it now scans 82 PHP files and `inc/template-tags.php` no longer appears among the largest files after the split.
- Updated `docs/release-notes-v0.2.3.md`, `docs/development.md`, and planning records with the Template Tags split and new contract gate.
- Full verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 82 runtime theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Generated local validation package `releases/Yneko-Reimu-v0.2.3-20260604-2230.zip`; it contains the new Template Tags modules, `inc/comments/rendering.php`, and `docs/release-notes-v0.2.3.md`, and excludes `assets/src`, `assets/dist/manifest.json`, tools, `PROJECT.md`, and `AGENTS.md`.

## 2026-06-04 v0.2.3 GitHub OAuth Split

- Started the next v0.2.3 maintenance expansion for GitHub OAuth after the Comments rendering and Template Tags splits.
- Kept `theme/Yneko-Reimu/inc/github-login.php` as the GitHub OAuth entrypoint loaded by `functions.php`.
- Added internal modules under `theme/Yneko-Reimu/inc/github-login/`: `settings.php`, `rendering.php`, `oauth.php`, `users.php`, `avatars.php`, and `access.php`.
- Moved existing defaults/options/URL helpers, login button rendering, OAuth begin/bind/callback/token/API flow, user lookup/create/bind helpers, avatar fallback filters, and comment-user access restrictions into the internal modules without renaming functions or hook callbacks.
- Preserved public OAuth actions, legacy actions, bind nonce, option keys, settings fields, meta keys, scope/endpoints, state transient shape, popup message type, avatar priority, and admin-access behavior.
- Extended `tools/check-github-oauth-contract.mjs` so it reads the entrypoint plus all six modules and verifies that `github-login.php` requires each internal module.
- Targeted checks passed: `php -l` for `inc/github-login.php` and all six new modules, `npm run check:github-oauth`, `node --check tools/check-github-oauth-contract.mjs`, and `npm run report:php-complexity`.
- Updated `docs/release-notes-v0.2.3.md`, `docs/development.md`, `docs/github-oauth-qa.md`, and planning records with the GitHub OAuth split.
- Full verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 88 runtime theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Generated local validation package `releases/Yneko-Reimu-v0.2.3-20260604-2247.zip`; it contains all six `inc/github-login/*.php` modules, Template Tags modules, `inc/comments/rendering.php`, and `docs/release-notes-v0.2.3.md`, and excludes `assets/src`, `assets/dist/manifest.json`, tools, `PROJECT.md`, and `AGENTS.md`.

## 2026-06-04 v0.2.4 Phase 1 Comment Dialog and Login Password Button

- Started v0.2.4 phase 1 after the v0.2.3 release tag, focused on two UX regressions only.
- Replaced the front-end comment media replacement `window.confirm()` path with an injected theme confirmation dialog that returns a Promise.
- Updated GIF library insertion, image/GIF URL insertion, and local upload insertion paths so cancel preserves the existing comment content and confirm continues the old cleanup/insert flow.
- Replaced front-end comment delete `window.confirm()` with the same theme confirmation dialog while preserving the existing `yneko_reimu_delete_comment` AJAX action, nonce, payload, count update, and load-more sync.
- Left `assets/src/admin-settings.js` upload-delete confirmation unchanged because this phase only targets front-end comment interactions.
- Updated the WordPress login inline stylesheet so `.wp-hide-pw` is absolutely centered inside `.wp-pwd`, with transparent background, no border/frame, no box shadow, and visible hover/focus states.
- Updated comments/profile and GitHub OAuth contract gates to protect the new theme confirmation dialog and the login password button style contract.
- Bumped the version line to `0.2.4` in npm metadata, theme headers/constants, runtime `readme.txt`, README examples, and release docs; added `docs/release-notes-v0.2.4.md`.
- Targeted checks passed: `npm run check:js`, `npm run check:comments-profile`, `npm run check:github-oauth`, and `php -l theme\Yneko-Reimu\inc\github-login\rendering.php`.
- Full verification passed: `npm run build`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 88 runtime theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Generated local validation package `releases/Yneko-Reimu-v0.2.4-20260604-2323.zip`; it contains v0.2.4 release notes, built `reimu.js`, built `reimu-comments.css`, login rendering PHP, and runtime `readme.txt`, and excludes source assets, manifest, tools, `PROJECT.md`, and `AGENTS.md`.
