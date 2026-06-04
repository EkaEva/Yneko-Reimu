# Yneko-Reimu Optimization Findings

- Repo is clean at start.
- Current build is a lightweight Node copy/concat script.
- Current release package uses a whitelist and excludes development-only source directories.
- Earlier release ZIP checks showed large PNG fallbacks; these are now candidates for source slimming when WebP fallbacks exist.
- Frontend has no jQuery dependency on the public theme script; admin settings registers a small inline script with WordPress `jquery` dependency.
- Theme is currently Classic Hybrid: PHP templates plus `theme.json`, editor styles, and block support.

## 2026-06-02 Online Theme Audit Findings

- Live homepage `https://yneko.com/` loads successfully with title `Yneko - Yneko的博客`; no browser console warnings/errors were observed in the initial desktop check.
- Desktop and 390px mobile viewport do not show obvious horizontal overflow. Mobile viewport reports `scrollWidth` equal to `clientWidth`.
- Homepage head loads theme CSS plus Google Fonts, loader CSS, APlayer CSS/JS, mouse-firework JS, GA, WordPress emoji JS, Cloudflare beacon, and theme JS.
- Performance API resource entries were unavailable in the in-app browser page context, so asset sizing needs to be checked via page asset capability, HTTP requests, or source inspection instead.
- Images on the homepage are mostly WebP uploads. The hero and repeated category/post card images observed did not expose `loading="lazy"`; only search popup background had lazy loading.
- Several fixed layers are present in DOM on mobile even while hidden or inactive: loader, mobile nav/sidebar, search popup, login modal, mouse-firework canvas.
- Controls audit shows several empty-text buttons without `aria-label` in the collected top controls list; likely from search suggestions/login form/tab UI or icon-only buttons.
- Semantic baseline is decent on homepage: one `h1`, one `main`, six `article` elements, and two JSON-LD scripts were observed.
- Follow-up control audit identified the unnamed visible buttons as APlayer controls, not first-party theme templates.
- Page asset capability confirmed observed live assets include Google Fonts, Ali icon font, GA, Cloudflare RUM, mouse-firework, APlayer, `search.json`, an LRC file, and `audio.mp3`.
- Local theme default features keep preloader, custom cursor, and generated search enabled by default. APlayer/firework are disabled by default but enabled on the live site.
- `content-card.php` outputs card images with `class="lazyload"` but no native `loading`, `decoding`, `width`, or `height` attributes.
- `search-index.php` exposes up to 300 published posts with stripped full content in `/search.json`.
- Frontend AJAX login distinguishes missing user from wrong password; password reset code sender also reports missing users. This can leak account existence.

## 2026-06-02 Repair Findings

- Local cleanup verification shows no `yneko-wp-local` Docker containers, volumes, or networks remain, and `E:\GitProject\VS Code\Blog\wp-local` no longer exists.
- `releases` now only keeps `Yneko-Reimu-v0.1.12.zip`.
- Rank Math compatibility should own canonical/meta/schema on production; theme meta/schema now only output when no common SEO plugin is active.
- Rank Math sitemap hooks used: `rank_math/sitemap/xml_post_url`, `rank_math/sitemap/entry`, and `rank_math/sitemap/page_content`; the page sitemap content hook appends the `/en/` homepage entry instead of replacing existing content.
- Local Composer is still unavailable, so PHP lint/WPCS cannot be run in this environment.
- `npm install` completed with 2 moderate advisories; no forced audit fix was applied.

## 2026-06-02 Live v0.1.12 Verification

- Live `style.css` reports `Version: 0.1.12`; front-end assets load with `?ver=0.1.12...`.
- `/`, `/en/`, a Chinese post, and an English post each output one meta description and one JSON-LD script.
- `/en/` canonical is now `https://yneko.com/en/`, and English post canonical/OG URL point to `/en/blog/.../`.
- Hreflang is present on home and paired posts with `zh-CN`, `en`, and `x-default`.
- `/search.json` and `/en/search.json` no longer expose a `content` field by default.
- Homepage post-card images now have `loading="lazy"`, `decoding="async"`, `width`, and `height`; sidebar/logo/related-post images are separate templates and still lack full native dimensions.
- APlayer controls have accessible labels after initialization.
- Remaining SEO issue: `post-sitemap.xml` still lists English posts as `/blog/*-en/`, and `page-sitemap.xml` still lacks `/en/`.
- Remaining performance issue: with the player visible on initial load, the page still requests `audio.mp3` and `.lrc` before user interaction.

## 2026-06-02 Live v0.1.13 Verification

- Front-end assets load as `?ver=0.1.13...`, confirming the rebuilt JS/CSS assets are live.
- No-interaction homepage load no longer requests `audio.mp3` or `.lrc`; `#aplayer` stays deferred and no audio element is created before user input.
- Direct `style.css` still reports `Version: 0.1.12`, even though the front-end asset version is `0.1.13`; the running PHP constant appears updated, but the stylesheet header file on origin did not reflect the latest zip header.
- `post-sitemap.xml` still lists English posts as `/blog/*-en/`.
- `page-sitemap.xml` still lacks `https://yneko.com/en/`.
- Sitemap responses are `cf-cache-status: DYNAMIC` with no-store headers, so the stale sitemap is more likely Rank Math internal sitemap storage/cache or a sitemap generation hook path issue than Cloudflare edge cache.

## 2026-06-02 Version and Source Slimming Findings

- Public version fields were reset to `0.1.12` because the GitHub version line has not caught up.
- Local verification packages now use `Yneko-Reimu-v0.1.12-YYYYMMDD-HHMM.zip`.
- `vendor-src/reimu-upstream` was removed from the repository; builds continue from `theme/Yneko-Reimu/assets/src/yneko-reimu-base.css`.
- Large PNG fallbacks `assets/images/banner.png` and `assets/images/search-bg.png` were removed; WebP assets remain.
- No local `wp-local`, Docker Compose, or WordPress development-site files remain in the repository.

## 2026-06-03 Loading Screen Fix Findings

- The previous local validation package could stay on the loading screen because `assets/dist/reimu.js` contained `import.meta.url` from the Weixin QR dynamic import.
- WordPress enqueues `assets/dist/reimu.js` as a classic script, so the browser treats `import.meta` as a syntax error and never reaches the theme initialization that hides the loader.
- The QR library now ships as `assets/dist/qrcode.js` and is loaded lazily as a classic script only when Weixin share needs to render a QR code.

## 2026-06-03 Profile and Share Follow-up Findings

- About/friend/projects can render through virtual templates instead of `content-page.php`; those virtual templates previously only rendered the virtual footer, so article share icons were absent.
- Comment hot sorting is front-end only: `commentHotScore()` returns the number of nested child comments under a top-level comment. Hot mode sorts by that reply count descending, then by older comment time first as a tie-breaker.
- The profile avatar picker previously changed the upload button text to the selected filename and only uploaded as part of the whole profile save submit.
- Custom profile tags were removed from the DOM when special tags reduced the custom capacity; this also made them disappear from the saved profile. The UI now keeps them as inactive/frozen rows and the back end stores up to two custom tags regardless of how many are currently visible.

## 2026-06-03 Share/Profile/Admin Follow-up Findings

- The latest requested refinement changes the avatar upload flow back to profile-form submit: file selection should validate/stage, and the modal should close only after the user clicks profile Save and the save response succeeds.
- Settings are loaded before comments helpers in `functions.php`, so admin pending-review badge counters must guard comment-upload helper calls with `function_exists`.
- Custom comment tags now need separate limits: five stored custom rows and two active/displayed badges across special plus selected custom tags.
- To avoid turning a first-login GitHub avatar into a custom avatar accidentally, the profile form now posts `avatar_changed=1` only after the user edits the avatar URL or chooses an avatar file.

## 2026-06-03 Review Status Sync Findings

- The prior avatar-only inline status could not report tag or comment review outcomes, and avatar approval deleted the status before front-end polling could display "头像已更新".
- User custom tag review approval moved pending tags into active meta, but the profile modal preferred pending tags over active tags whenever any pending row remained. The modal now merges active and pending custom rows so approved tags stay visible while remaining pending rows still show.
- Duplicate admin badges came from both top tabs and broad headings/table rows. The cleaned layout keeps top tab totals and only shows section badges beside concrete review lists: user tag review, user avatar manager, user comment GIF, and user comment image.
- Comment/media review changes happen in the admin while the user is on a different page, so front-end updates need polling. The polling path now fetches profile status via AJAX and refreshes the current `#comments` block from the current page HTML after review completion.

## 2026-06-03 Development Standards Findings

- Root-level `PROJECT.md` and `AGENTS.md` did not exist before this pass. They are suitable for local-only development constraints because `.git/info/exclude` can hide them without changing the shared `.gitignore`.
- Current built asset baseline from `assets/dist/manifest.json`: `reimu.js` 116,191 bytes, `reimu.css` 215,531 bytes, `qrcode.js` 23,468 bytes, `loader.css` 1,753 bytes, `xiaohongshu.svg` 8,155 bytes.
- The short-term budgets of 120 KB for `reimu.js` and 220 KB for `reimu.css` pass against the current baseline, leaving only a small buffer. Future feature work should reduce or split assets before adding more global code.
- `settings.php` previously embedded a very large admin settings JavaScript string. Moving that logic into `assets/src/admin-settings.js` makes it eligible for `node --check`, Vite minification, and future modular cleanup.
- Release package checks should inspect ZIP contents directly because a whitelist can still drift when new generated assets or local-only files are introduced.
- The Lily cursor build rewrote PNG files during validation. Adding metadata stripping and excluding PNG time/date chunks made the output smaller and more deterministic; cursor files now shrink by roughly 250 bytes each.

## 2026-06-03 Module Boundary Findings

- Rollup cannot emit IIFE output for a code-splitting or multi-input JS build. Keeping classic script compatibility requires building public JS entries one at a time with `inlineDynamicImports`.
- The main Vite config now only needs to build CSS/static assets; `tools/build-reimu.mjs` can build `reimu.js` and `admin-settings.js` as separate single-entry IIFE scripts.
- First safe front-end extraction target is `assets/src/reimu/core.js`: DOM helpers, storage helpers, event dispatch, HTML escaping, debounce, and URL/language path helpers.
- The first source module boundary now covers `core`, `dom`, `storage`, `events`, `search`, and `share`. `assets/src/reimu.js` remains the only public front-end entrypoint and only composes modules/init order.
- `share` is a clean runtime-splitting candidate because its QR dependency is already lazy-loaded through classic `assets/dist/qrcode.js`; the new module keeps that promise/cache state private.
- `search` can be isolated behind injected DOM/config/i18n helpers without changing `window.ReimuSearchClose` or the search popup behavior.
- Comment upload/review functions form a mostly continuous block in `comments.php`, making `inc/comments/uploads.php` a low-risk first PHP extraction target while preserving existing function names and hook registrations.
- `npm run check:js` should scan source and tool directories recursively; otherwise future internal JS modules can bypass syntax checks.
- Lazy-loading candidates for later runtime splitting: search opens from `#nav-search-btn`; share only needs Weixin QR on Weixin click; comments/profile require `#comments` or profile triggers; APlayer depends on `config.aplayer`; PhotoSwipe depends on article images and `config.photoswipe`; Mermaid and KaTeX depend on their config flags and matching content blocks.

## 2026-06-03 Settings Schema Split Findings

- `inc/settings.php` had a clean first-phase split point: defaults, normalizers, sanitizers, settings getters, and fallback theme-mod readers all appear before `yneko_reimu_register_settings()`.
- Moving that schema/read layer into `inc/settings/schema.php` reduces the entry file by roughly 940 lines while keeping registration, admin menu badges, page rendering, review lists, and admin asset enqueue in the existing entrypoint.
- This split does not change the stored `yneko_reimu_settings` option, the `yneko_reimu_sanitize_settings` callback name, admin page slug, or settings UI payload shape.
- The release ZIP must include `inc/settings/schema.php`; the newest package check confirmed the file is present while development-only files remain excluded.

## 2026-06-03 Settings Admin Helper Split Findings

- After the schema split, `inc/settings.php` still had a clear admin-helper band before the main renderer plus a standalone admin asset enqueue function at the end.
- Moving page registration, admin menu badges, bilingual helper output, pending review counters, media field helper, and admin asset enqueue into `inc/settings/admin.php` cuts the entry file down to the settings registration/cleanup hooks plus render functions.
- The admin helper split does not change the `appearance_page_yneko-reimu-settings` hook, `yneko-reimu-admin-settings` style/script handles, settings page slug, or visible form structure.
- The next safe settings split is the render layer: friend/music row renderers and review list renderers can move before the large tabbed form is templated.

## 2026-06-03 Settings Renderer Split Findings

- The remaining render helpers after the main settings form are self-contained and only depend on existing admin/schema/comment helpers, so they can move without changing field names or form structure.
- `inc/settings/renderers.php` now owns repeatable friend/music rows plus comment upload, avatar, user badge, and admin GIF upload renderer fragments.
- `inc/settings.php` is reduced to settings registration, post-save cleanup, and the main tabbed form renderer. The last large responsibility in the file is the tabbed form itself.
- The next settings step should either template the tabbed form into a dedicated module or pause settings work and start runtime lazy-loading enforcement.

## 2026-06-03 Settings Page Split Findings

- `yneko_reimu_render_settings_page()` was the final large settings renderer left in `inc/settings.php`; moving it to `inc/settings/page.php` leaves the entrypoint focused on module loading, settings registration, and post-save cleanup.
- The settings page callback name remains unchanged, so the existing `add_theme_page()` callback in `inc/settings/admin.php` continues to work.
- The settings first-stage split now has four internal modules: `schema.php`, `admin.php`, `renderers.php`, and `page.php`.
- With settings decomposition complete for this phase, the next higher-value work is runtime lazy-loading/budget enforcement rather than further PHP file shuffling.

## 2026-06-03 Runtime Loading Strategy Findings

- The current size gate enforces the 120 KB/220 KB JS/CSS budgets and classic script compatibility, but it previously did not expose planned page or interaction triggers for future runtime splitting.
- `tools/feature-loading-plan.mjs` now records the first loading backlog as machine-readable entries. Search, share, comments/profile, APlayer, PhotoSwipe, Mermaid, and KaTeX each have an owner, current loading mode, target loading mode, trigger, gate, and compatibility note.
- This round intentionally does not change WordPress enqueue behavior or introduce dynamic imports in the main package. The quality gate now reports lazy-loading readiness while preserving the single classic `assets/dist/reimu.js` public interface.
- Current statuses: `share` is partial-lazy because Weixin QR already loads `qrcode.js` on interaction; APlayer, Mermaid, and KaTeX are condition-loaded at the vendor layer; search, comments/profile, and PhotoSwipe remain main-bundle candidates for later extraction.

## 2026-06-03 Search Runtime Split Findings

- Search is now the first actual runtime split target because the source module already accepted injected DOM/config/i18n helpers and had a clear trigger at `#nav-search-btn` / `.popup-trigger`.
- The main bundle now keeps only a small search loader and loads `assets/dist/reimu-search.js` as a classic script on first search interaction.
- `reimu-search.js` exposes internal `window.ReimuSearchRuntime.init/open` methods; existing public behavior through `window.ReimuSearchClose` is preserved.
- `npm run check:size` now budgets the lazy search runtime at 24 KB and checks both `reimu.js` and `reimu-search.js` for classic script compatibility.

## 2026-06-03 PhotoSwipe Runtime Split Findings

- PhotoSwipe was a low-risk second runtime split because the existing implementation was self-contained: image wrapping, click binding, overlay rendering, keyboard controls, and destroy all lived in one contiguous front-end block.
- `assets/src/reimu/photoswipe.js` now owns the implementation, and `assets/src/reimu-photoswipe.js` registers the internal `window.ReimuPhotoSwipeRuntime` classic runtime.
- The main script now loads `assets/dist/reimu-photoswipe.js` only when `window.REIMU_CONFIG.photoswipe` is enabled and article images or existing PhotoSwipe items are present.
- The compatibility surface is preserved: `window.REIMU_PHOTOSWIPE.destroy()` still exists while the overlay is open, and the setting/config gates are unchanged.

## 2026-06-03 Share Runtime Split Findings

- Share was a good low-risk follow-up split because the PHP/templates already output all share data and URLs, while JavaScript only manages the Weixin popup and QR rendering.
- `assets/src/reimu-share.js` now registers the internal `window.ReimuShareRuntime`, and the main script only loads it on pages with `.share-wrapper`.
- Weixin QR generation remains second-stage lazy: `assets/dist/reimu-share.js` loads `assets/dist/qrcode.js` only when the Weixin share link is clicked.
- Public behavior is unchanged: share Customizer settings, share URLs, template placement, and non-Weixin outbound links are still owned by PHP markup.

## 2026-06-03 Comments/Profile Safety Interface Review Findings

- The comments/profile area is the highest-risk remaining front-end split candidate because it combines authentication, profile saves, upload review state, comment mutation, login-state DOM replacement, and polling-driven refresh behavior.
- Public config/nonce surface from `window.REIMU_CONFIG` must stay stable:
  - `login.ajaxUrl`, `login.nonce`, `login.registerNonce`, `login.registerCodeNonce`, `login.lostNonce`, `login.lostCodeNonce`, `login.profileNonce`, and `login.logoutNonce`.
  - `commentUploads.enabled`, `commentUploads.imageEnabled`, `commentUploads.gifEnabled`, `commentUploads.isLoggedIn`, `commentUploads.nonce`, and `commentUploads.gifs`.
  - `comments.nonce`.
- Front-end auth/profile AJAX actions currently sent by `assets/src/reimu.js`:
  - `yneko_reimu_login` with `nonce=login.nonce`, `log`, `pwd`, `rememberme`, optional `two_factor_code`.
  - `yneko_reimu_register_code` with `nonce=login.registerCodeNonce`, `display_name`, `user_email`, and `redirect_to`.
  - `yneko_reimu_register` with `nonce=login.registerNonce`, `display_name`, `user_email`, `user_password`, `verify_code`, and `redirect_to`.
  - `yneko_reimu_lostpassword_code` with `nonce=login.lostCodeNonce`, `user_login`, and `redirect_to`.
  - `yneko_reimu_lostpassword` with `nonce=login.lostNonce`, `user_login`, `user_password`, `verify_code`, and `redirect_to`.
  - `yneko_reimu_profile_get`, `yneko_reimu_profile_status_ack`, `yneko_reimu_profile_email_code`, `yneko_reimu_profile_totp_generate`, `yneko_reimu_profile_avatar_upload`, and `yneko_reimu_profile_save`, all gated by `nonce=login.profileNonce`.
  - `yneko_reimu_logout` gated by `nonce=login.logoutNonce`.
- Front-end comment AJAX actions currently sent by `assets/src/reimu.js`:
  - `yneko_reimu_comment_upload` with `nonce=commentUploads.nonce`, `type`, and uploaded `file`.
  - `yneko_reimu_comment_upload_discard` with `nonce=commentUploads.nonce`, `url`, and `cleanup_key`.
  - `yneko_reimu_submit_comment` with `nonce=comments.nonce` plus the normal WordPress comment form fields.
  - `yneko_reimu_comment_like` with per-comment `data-like-nonce` and `comment_id`.
  - `yneko_reimu_edit_comment` / `yneko_reimu_delete_comment` with per-comment `data-comment-manage-nonce` and `comment_id`.
- PHP hook/action surface to preserve:
  - Public/guest auth: `wp_ajax_nopriv_yneko_reimu_login`, `wp_ajax_nopriv_yneko_reimu_register_code`, `wp_ajax_nopriv_yneko_reimu_register`, `wp_ajax_nopriv_yneko_reimu_lostpassword_code`, and `wp_ajax_nopriv_yneko_reimu_lostpassword`.
  - Login-state refresh: `wp_ajax_yneko_reimu_login_state` and `wp_ajax_nopriv_yneko_reimu_login_state`.
  - Logged-in profile/logout: `wp_ajax_yneko_reimu_logout`, `wp_ajax_yneko_reimu_profile_get`, `wp_ajax_yneko_reimu_profile_status_ack`, `wp_ajax_yneko_reimu_profile_email_code`, `wp_ajax_yneko_reimu_profile_totp_generate`, `wp_ajax_yneko_reimu_profile_avatar_upload`, and `wp_ajax_yneko_reimu_profile_save`.
  - Comments: `wp_ajax_yneko_reimu_comment_like`, `wp_ajax_nopriv_yneko_reimu_comment_like`, `wp_ajax_yneko_reimu_edit_comment`, `wp_ajax_yneko_reimu_delete_comment`, `wp_ajax_yneko_reimu_submit_comment`, and `wp_ajax_nopriv_yneko_reimu_submit_comment`.
  - Comment media uploads/admin: `wp_ajax_yneko_reimu_comment_upload`, `wp_ajax_yneko_reimu_comment_upload_discard`, `wp_ajax_yneko_reimu_admin_add_gif_media`, `admin_init` handlers for comment media review, avatar review, user badge review, and admin GIF uploads.
- DOM triggers/rebind surface to preserve:
  - Comment tools use `[data-comment-tool]`, `[data-comment-popover]`, `[data-comment-upload-button]`, `[data-comment-upload-input]`, `[data-comment-gif-library]`, and textareas under `.reimu-comment-form`.
  - Mutating comment actions use `#comments [data-comment-like]`, `[data-comment-edit]`, `[data-comment-delete]`, `[data-comment-sort]`, `#respond`, and WordPress reply/cancel behavior.
  - Auth/profile uses `#reimu-login-modal`, `#reimu-profile-modal`, `[data-reimu-profile-open]`, `.reimu-comment-login-link`, `[data-reimu-ajax-logout]`, `[data-reimu-github-popup]`, and `[data-reimu-auth-popup]`.
  - `refreshCommentLoginState()` may inject/replace login/profile modal HTML and current-user identity HTML, so any split must still re-run `initLoginModal()`, `initProfileModal()`, `initCommentAjaxLogout()`, `initCommentLoginTriggers()`, and comment upload row state.
- Safe source-module extraction candidates for the next round:
  - Pure comment media text utilities: token store, token resolution, media counting, media cleanup request wrapper, textarea insertion, preview rendering.
  - Pure comment UI binders with no PHP contract changes: selector tabs, popover open/close, GIF library rendering, upload row visibility, sort/load-more helpers.
  - Profile form UI utilities that do not send requests: password visibility/validation, URL normalization, avatar preview dirty-state tracking, custom-tag row rendering and selection cap logic.
- High-risk code that should remain in the main bundle until a stricter runtime contract exists:
  - Login/register/lost-password submission and nonce refresh.
  - Profile save, profile avatar upload, email code, TOTP generation, profile status polling, and status acknowledgement.
  - Comment submit, edit, delete, like, upload, upload discard, and login-state refresh.
  - DOM replacement after login/logout/profile refresh because it affects later `window.ReimuWP.init()` / PJAX rebinding behavior.
- Recommended implementation sequence: first extract source-only utilities and binders from `assets/src/reimu.js` into internal modules, rebuild the same main `assets/dist/reimu.js`, and only then consider a lazy `reimu-comments.js` runtime for non-auth comment UI. Comments/profile AJAX should be split only after tests or manual QA cover login, logout, profile save, comment submit, upload review, and PJAX navigation.

## 2026-06-04 Comment Media Source Module Split Findings

- `assets/src/reimu/comment-media.js` now owns comment textarea/media helpers: token storage, token-to-Markdown resolution, media entry detection, one-media limit checks, replacement confirmation text, unsubmitted upload cleanup requests, plain text counting, Markdown preview rendering, and media insertion.
- `assets/src/reimu.js` keeps the public orchestration and AJAX-sensitive flows, importing the module and exposing local variables with the same helper names used by the existing comment code.
- The module intentionally uses `getConfig()` rather than capturing the initial config object. This preserves the previous behavior after PJAX updates `window.REIMU_CONFIG` and `config` inside `syncInlineConfig()`.
- No public interface changed: AJAX action names, nonce names, comment upload payloads, profile payloads, `window.REIMU_CONFIG`, `window.ReimuWP`, and built script loading remain unchanged.
- Build results after the split: `reimu.js` is 105.6 KB / 120 KB, `reimu-search.js` is 9.8 KB / 24 KB, `reimu-photoswipe.js` is 5.6 KB / 24 KB, `reimu-share.js` is 4.6 KB / 24 KB, and `reimu.css` is 205.3 KB / 220 KB.
- The release ZIP check confirmed `assets/src/reimu/comment-media.js`, `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are not packaged, while `assets/dist/reimu.js` is included.
- The next low-risk candidate is comment popover/tool binding extraction: `closeCommentPopovers`, `setCommentToolState`, `toggleCommentPopover`, `initCommentPopoverOutsideClose`, `initCommentGifLibrary`, and upload row UI visibility can move behind injected helpers before any AJAX runtime split is attempted.

## 2026-06-04 Comment Tools Source Module Split Findings

- `assets/src/reimu/comment-tools.js` now owns comment toolbar/popover behavior: popover close/open state, preview toolbar state, outside-click close delegation, GIF library rendering, GIF insertion, toolbar click binding, quick insert, URL insert, and preview refresh binding.
- Upload row visibility and upload AJAX remain in `assets/src/reimu.js`; `comment-tools.js` only calls injected `initCommentUploadRows(form, textarea)`.
- This keeps the sensitive upload payload unchanged: `yneko_reimu_comment_upload` still posts `nonce`, `type`, and `file` from the main bundle, and `yneko_reimu_comment_upload_discard` remains owned by `comment-media.js` cleanup helpers from the previous round.
- `comment-tools.js` uses injected `getConfig()` for GIF library reads so PJAX-synced `window.REIMU_CONFIG` updates are preserved.
- Build results after the split: `reimu.js` is 106.5 KB / 120 KB, `reimu-search.js` is 9.8 KB / 24 KB, `reimu-photoswipe.js` is 5.6 KB / 24 KB, `reimu-share.js` is 4.6 KB / 24 KB, and `reimu.css` is 205.3 KB / 220 KB.
- `npm run check:package` must be run after `npm run package` completes; running them in parallel can inspect the previous timestamped ZIP. The current package check correctly inspected `Yneko-Reimu-v0.1.15-20260604-0020.zip`.
- The release ZIP check confirmed `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are not packaged, while `assets/dist/reimu.js` is included.
- The next low-risk candidate is comment sorting/load-more extraction (`commentHotScore`, `commentLatestActivityTime`, load-more helpers, and sorting controls) or profile form UI-only helpers. Comment submit, edit, delete, like, login, logout, profile save, and status polling should remain in the main source file.

## 2026-06-04 Comment List Source Module Split Findings

- `assets/src/reimu/comment-list.js` now owns comment list-only helpers: hot score, latest activity time, load-more item collection, load-more visibility syncing, load-more click binding, sort mode lookup, sorting, and sort button binding.
- The main source file keeps AJAX-sensitive and rebind-heavy behavior: submitted-comment insertion, comment likes, owner edit/delete actions, AJAX comment submission, WordPress reply form movement, login-state refresh, and profile status polling.
- Keeping `appendSubmittedComment()` in `assets/src/reimu.js` avoids moving the chain that calls `initCommentLikes()`, `initCommentOwnerActions()`, `initWordPressCommentForm()`, and `syncLoadMoreRoot()` after an AJAX submit.
- `comment-list.js` receives `revealViewportAos()` as an injected callback for the load-more click path; no new global API or lazy runtime was introduced.
- Build results after the split: `reimu.js` is 107.1 KB / 120 KB, `reimu-search.js` is 9.8 KB / 24 KB, `reimu-photoswipe.js` is 5.6 KB / 24 KB, `reimu-share.js` is 4.6 KB / 24 KB, and `reimu.css` is 205.3 KB / 220 KB.
- The release ZIP check confirmed `assets/src/reimu/comment-list.js`, `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are not packaged, while `assets/dist/reimu.js` is included.
- The remaining comment/profile extraction candidates are narrower. A safe next pass can move profile form UI-only helpers, but login/register/lost-password submission, profile save, profile polling, comment submit, comment like, edit, delete, and upload AJAX should remain in the main bundle unless a stronger runtime contract and manual QA checklist are created.

## 2026-06-04 Profile Form UI Source Module Split Findings

- `assets/src/reimu/profile-form.js` now owns profile modal form-only helpers: URL normalization, password validation and visibility toggles, avatar hint/dirty-state updates, profile tag error rendering, custom tag row rendering, custom tag capacity, and selected-tag limit UI.
- `assets/src/reimu.js` keeps the AJAX-sensitive profile flows: `yneko_reimu_profile_get`, `yneko_reimu_profile_save`, `yneko_reimu_profile_email_code`, `yneko_reimu_profile_totp_generate`, profile avatar upload, status acknowledgement/polling, login-state refresh, and modal DOM replacement reinitialization.
- Avatar dirty tracking now uses an injected mutable state object so the old submit decision remains explicit without keeping the helper implementation inline.
- Tag error timeout state is also injected, preserving the previous delayed clear behavior while moving the DOM-only rendering logic out of the main source file.
- Build results after the split: `reimu.js` is 108.3 KB / 120 KB, `reimu-search.js` is 9.8 KB / 24 KB, `reimu-photoswipe.js` is 5.6 KB / 24 KB, `reimu-share.js` is 4.6 KB / 24 KB, and `reimu.css` is 205.3 KB / 220 KB.
- The release ZIP check confirmed `assets/src/reimu/profile-form.js`, `assets/src/reimu/comment-list.js`, `assets/src/reimu/comment-tools.js`, `assets/src/reimu/comment-media.js`, `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are not packaged, while `assets/dist/reimu.js` is included.
- Remaining comments/profile code is now mostly AJAX-sensitive, polling-driven, or rebind-heavy. The next round should pause extraction and create a focused manual QA checklist/contract for comments and profile before moving any request handlers or runtime boundaries.

## 2026-06-04 Comments/Profile Runtime Contract Findings

- `docs/comments-profile-contract.md` now captures the preserved comments/profile public surface: front-end config keys, AJAX action names, DOM selector contracts, and runtime invariants.
- The manual QA checklist covers the regression paths that automated checks do not exercise locally: guest auth, profile save/avatar/TOTP/email/tag flows, comment submit/reply/upload/like/edit/delete, admin review status, PJAX navigation, and rebind duplication.
- The contract makes request-handler movement a gated change. Moving login/register/lost-password, profile request handlers, comment mutation handlers, or introducing `reimu-comments.js` / `reimu-profile.js` requires a before-and-after manual QA pass.
- `docs/development.md` now links to the contract from development constraints so it is visible to contributors, while `PROJECT.md` / `AGENTS.md` remain local-only and uncommitted.
- The next implementation round should audit the remaining `assets/src/reimu.js` comments/profile code against this contract and decide whether to extract a small request-free module or stop front-end splitting until manual WordPress QA is available.

## 2026-06-04 Profile Status UI Source Module Split Findings

- The remaining comments/profile code was audited against `docs/comments-profile-contract.md`. Most remaining functions touch AJAX, nonce refresh, polling, WordPress reply movement, login-state DOM replacement, or rebind orchestration and should not be moved without manual WordPress QA.
- `assets/src/reimu/profile-status.js` now owns the one remaining request-free profile slice: status message lookup, avatar/tag/comment review row normalization, inline current-user status rendering, pending-count badge rendering, and autohide scheduling.
- The module receives `ackProfileStatuses()` as an injected callback but does not create `FormData`, call `fetch()`, read nonces, or mutate `window.REIMU_CONFIG`.
- `assets/src/reimu.js` still owns the sensitive paths required by the contract: `yneko_reimu_profile_status_ack`, `yneko_reimu_profile_get`, profile save/email/TOTP/avatar flows, comment submit/upload/discard/like/edit/delete, login/logout refresh, and `window.ReimuWP.init()` rebind orchestration.
- Build results after the split: `reimu.js` is 108.7 KB / 120 KB, `reimu-search.js` is 9.8 KB / 24 KB, `reimu-photoswipe.js` is 5.6 KB / 24 KB, `reimu-share.js` is 4.6 KB / 24 KB, and `reimu.css` is 205.3 KB / 220 KB.
- Public runtime scripts still contain no `import.meta`, unresolved dynamic `import(`, or top-level ESM import/export syntax.
- This likely exhausts the safe comments/profile source-only extraction set. Further comments/profile runtime movement should wait for a local WordPress manual QA pass using the contract checklist.

## 2026-06-04 PHP Complexity Report Findings

- `tools/report-php-complexity.mjs` now scans all runtime PHP files and reports largest files, largest named functions, and highest approximate branch-score functions without adding a new dependency.
- Baseline scan: 72 PHP files, 566 named functions, 15,832 total lines, 13,988 nonblank lines, and an approximate branch score of 5,043.
- Largest files by nonblank lines: `inc/comments.php` (3,012), `inc/template-tags.php` (1,328), `inc/comments/uploads.php` (1,032), `inc/settings/schema.php` (861), and `inc/github-login.php` (839).
- Largest named functions by lines: `yneko_reimu_customize_register()` in `inc/customizer.php` (739), `yneko_reimu_render_settings_page()` in `inc/settings/page.php` (543), `yneko_reimu_enqueue_assets()` in `inc/enqueue.php` (287), and `yneko_reimu_ajax_profile_save()` in `inc/comments.php` (242).
- Highest approximate branch scores: `yneko_reimu_render_settings_page()` (636), `yneko_reimu_sanitize_settings()` (159), `yneko_reimu_profile_modal_html()` (138), `yneko_reimu_render_comment_upload_admin()` (98), and `yneko_reimu_comment_callback()` (96).
- The report should remain informational until a stable baseline and refactor budget exist. The next PHP refactor target should be renderer/schema decomposition rather than comments/profile request handlers unless manual QA is available.

## 2026-06-04 Settings Panels Split Findings

- `inc/settings/panels.php` now owns the friend-links and music-track settings panels, while `inc/settings/page.php` remains the top-level settings page/form/tab renderer.
- Public admin contracts are unchanged: `friends` and `music` tab keys, `data-yneko-settings-panel` values, option keys, input names, repeatable row data attributes, and save/sanitize flow all remain the same.
- `inc/settings.php` now requires `inc/settings/panels.php` before `inc/settings/page.php`, matching the existing internal module pattern for schema/admin/renderers/page.
- Complexity report after the split: `yneko_reimu_render_settings_page()` dropped from 543 lines / score 636 to 426 lines / score 503.
- The new `yneko_reimu_render_settings_music_panel()` appears as a contained hotspot at 86 lines / score 101, which is acceptable as an intermediate renderer split.
- Next low-risk targets inside the settings page are `external-comments` and `extensions`; both are admin-only renderers with stable field names and no request handlers.

## 2026-06-04 Settings Extension Panels Split Findings

- `inc/settings/panels.php` now also owns the extensions and external-comments settings panels.
- Public admin contracts are unchanged: `extensions` and `external-comments` tab keys, `data-yneko-settings-panel` values, feature/third-party/external-comment option keys, input names, labels, and save/sanitize flow all remain the same.
- `inc/settings/page.php` now calls `yneko_reimu_render_settings_extensions_panel( $features, $third_party )` and `yneko_reimu_render_settings_external_comments_panel( $external_comments )`.
- Complexity report after this split: `yneko_reimu_render_settings_page()` dropped from 426 lines / score 503 to 353 lines / score 426.
- This keeps decomposition in the low-risk admin-renderer layer and still avoids comments/profile PHP request handlers.
- Next low-risk settings page candidates are the search and i18n panels; both are smaller, mostly field rendering, and have stable option names.

## 2026-06-04 Settings Search/I18n Panels Split Findings

- `inc/settings/panels.php` now also owns the i18n and search settings panels.
- Public admin contracts are unchanged: `i18n` and `search` tab keys, `data-yneko-settings-panel` values, option keys, input names, labels, descriptions, and save/sanitize flow all remain the same.
- `inc/settings/page.php` now calls `yneko_reimu_render_settings_i18n_panel( $i18n )` and `yneko_reimu_render_settings_search_panel( $search )`.
- Complexity report after this split: `yneko_reimu_render_settings_page()` dropped from 353 lines / score 426 to 273 lines / score 353.
- This still avoids runtime search behavior, URL/i18n routing helpers, and comments/profile request handlers.
- Remaining settings page panels are higher risk than this batch because GitHub, comments, and users touch authentication/review workflows or larger admin helper surfaces; the next round should either split one cautiously or pause for manual admin UI checks.

## 2026-06-04 Settings GitHub Panel Split Findings

- `inc/settings/panels.php` now also owns the GitHub OAuth settings panel.
- Public admin contracts are unchanged: the `github` tab key, `data-yneko-settings-panel` value, `github_oauth` option keys, input names, callback placeholder display, client secret field, auto-create checkbox, and bind/rebind button behavior all remain the same.
- `inc/settings/page.php` now calls `yneko_reimu_render_settings_github_panel( $oauth, $callback )`.
- Complexity report after this split: `yneko_reimu_render_settings_page()` dropped from 273 lines / score 353 to 219 lines / score 298.
- This split does not touch GitHub OAuth callback handlers, login AJAX, binding URL generation logic, user meta keys, or sanitizer behavior.
- Remaining `comments` and `users` panels include review lists and upload/avatar/badge management. They can still be renderer splits, but should be treated as higher risk and ideally checked in the admin UI after moving.

## 2026-06-04 Settings Comments Panel Split Findings

- `inc/settings/panels.php` now also owns the comments settings panel and comment upload manager section.
- Public admin contracts are unchanged: the `comments` tab key, `data-yneko-settings-panel` value, `comment_avatar_url` setting field, `comment_upload` option keys, upload/review checkboxes, size inputs, cleanup inputs, and review list helper calls all remain the same.
- `inc/settings/page.php` now calls `yneko_reimu_render_settings_comments_panel( $settings )`.
- Complexity report after this split: `yneko_reimu_render_settings_page()` dropped from 219 lines / score 298 to 177 lines / score 243.
- This split does not touch comment upload AJAX handlers, admin review actions, nonce names, payload fields, temporary file cleanup logic, GIF library approval, or front-end comment behavior.
- The users panel is now the last large admin panel left in `inc/settings/page.php`; moving it should be a renderer-only pass with extra attention to badge counts and review helper calls.

## 2026-06-04 Settings Users Panel Split Findings

- `inc/settings/panels.php` now also owns the users settings panel, including user badge/avatar-frame fields and user badge/avatar review sections.
- Public admin contracts are unchanged: the `users` tab key, `data-yneko-settings-panel` value, `user_badges` option keys, avatar-frame media fields, avatar upload settings, user badge review heading badge, avatar review heading badge, and review helper calls all remain the same.
- `inc/settings/page.php` now calls `yneko_reimu_render_settings_users_panel( $review_badges )`.
- Complexity report after this split: `yneko_reimu_render_settings_page()` dropped from 177 lines / score 243 to 114 lines / score 133.
- This split does not touch profile AJAX handlers, avatar review approval/rejection actions, user badge approval/revoke actions, nonce names, payload fields, user meta keys, or front-end profile behavior.
- The first-stage settings page panel decomposition is now effectively complete: `inc/settings/page.php` owns the top-level form, tabs, general panel, submit controls, and admin GIF upload form, while all extracted tab panels live in `inc/settings/panels.php`.

## 2026-06-04 Settings Admin Contract Gate Findings

- `tools/check-settings-admin-contract.mjs` now verifies the extracted settings admin structure: 10 tabs, 10 matching panels, one page-level call for each extracted panel renderer, and required top-level form/GIF upload form snippets.
- The gate checks key field contracts across GitHub OAuth, i18n, search, comments, users, extensions, external comments, friends, and music settings, plus repeatable friend/music row fields in `inc/settings/renderers.php`.
- The gate also checks review-management contracts that are easy to break during renderer moves: comment GIF upload, comment upload review, user badge review, user avatar review, and user/avatar review badge headings.
- `npm run check` now includes `npm run check:settings-admin`, so this structural contract runs before build/size/PHPCS.
- This static gate is stronger than relying on diff review alone, but it does not prove browser-level admin UI behavior such as tab clicking, media modal interaction, repeatable add/remove actions, or save flows.

## 2026-06-04 Final Optimization Completion Audit Findings

- The latest full automated evidence shows `npm run check` passing with JS syntax, settings admin contract, build, size/classic-script checks, and PHPCS.
- `npm audit --audit-level=moderate` passes with `found 0 vulnerabilities`.
- `npm run report:php-complexity` passes and records the current post-split baseline: 73 PHP files, 575 named functions, and `yneko_reimu_render_settings_page()` reduced to 114 lines / score 133.
- Full PHP syntax lint over all runtime theme PHP files passed locally.
- The final validation package is `releases/Yneko-Reimu-v0.1.15-20260604-0958.zip`; `npm run check:package` reports 134 entries and no forbidden development files.
- ZIP spot check confirms `Yneko-Reimu/inc/settings/panels.php` and `Yneko-Reimu/inc/settings/page.php` are included, while `tools/check-settings-admin-contract.mjs`, `PROJECT.md`, `AGENTS.md`, `task_plan.md`, `findings.md`, `progress.md`, `assets/src/reimu.js`, and `assets/dist/manifest.json` are excluded.
- `git tag --list 'v0.1.15'` returns no tag, matching the explicit no-tag requirement.
- `git status --short --branch` was clean before final record updates; `PROJECT.md` and `AGENTS.md` do not appear in Git status because they are excluded through `.git/info/exclude`.
- Remaining unverified area: manual WordPress admin UI/browser QA was not performed locally. Static coverage now includes `tools/check-settings-admin-contract.mjs`, and manual comments/profile coverage is documented in `docs/comments-profile-contract.md`.

## 2026-06-04 Local WordPress QA Findings

- Docker Desktop was available after starting the daemon, so a local-only WordPress 6.9 / PHP 8.3 / MariaDB environment was created under `.gitignore`-excluded `wp-local/`.
- The current `theme/Yneko-Reimu` directory was mounted into WordPress and activated successfully; no release tag was created.
- Admin settings page QA at `http://127.0.0.1:8095/wp-admin/themes.php?page=yneko-reimu-settings` confirmed 10 settings tabs and 10 panels render in a real WordPress admin session with no browser console warnings/errors.
- Admin settings tab switching works: selecting the Friend links tab activates the `friends` panel and updates the hash to `#friends`.
- Real admin QA exposed a small regression missed by the static contract check: when a repeatable friend/music row was added, `refreshNumbers(repeatable)` did not update the new row heading because it only scanned descendant `.yneko-reimu-repeatable` nodes, not the passed repeatable root itself.
- `assets/src/admin-settings.js` now includes the passed `.yneko-reimu-repeatable` root before scanning descendants. Browser retest confirmed a newly added friend row receives `Friend #4`.
- Front-end post QA confirmed the main classic runtime initializes DOM bindings with no console warnings/errors; comment/profile binding markers are present, and no `type="module"` loading is required.
- Search lazy runtime QA passed: `reimu-search.js` is not present before interaction, then loads after the search button is clicked, opens the popup, and focuses the search input.
- Share lazy runtime QA passed: `reimu-share.js` loads on pages with `.share-wrapper`, and Weixin QR generation loads `qrcode.js` only after the Weixin share link is clicked. The QR image receives a data URL.
- PhotoSwipe lazy runtime QA passed after enabling `yneko_reimu_settings.features.photoswipe_enable` in the local test site: `reimu-photoswipe.js` loads only when the feature is enabled and an article image is present.
- Comments/profile smoke QA passed: the profile modal opens and renders profile fields; an AJAX comment submit inserted a new comment into the list, cleared the textarea, and produced no console warnings/errors.
- Limitation: this was a smoke QA pass, not the full comments/profile contract matrix. Email delivery, TOTP validation, avatar/media file upload review, admin approval/rejection, and OAuth callbacks still require a deeper local or staging QA session.

## 2026-06-04 Comments/Profile Review Flow QA Findings

- Local review switches were enabled in the WordPress test site only: comment image/GIF upload, image/GIF review, avatar upload/review, and user badge review.
- Seeded qauser with pending avatar, pending custom badge, and a held comment containing a pending temporary image upload. This uses local-only `wp-local/` helper scripts and does not change public repository files.
- Front-end qauser session at `http://127.0.0.1:8095/yneko-qa-post/` renders as logged-in: `[data-reimu-profile-open]` exists, `.reimu-comment-login-link` is absent, and current-user identity markup is present.
- Opening the profile modal shows the expected pending-avatar notice text `头像审核中`; the profile form and tag list render with no site console errors.
- Server-side profile save QA found a real localized response bug: with site locale `en_US`, `yneko_reimu_ajax_profile_save()` returned an empty success `message` for the comment-badge-review path because `languages/en_US.po` had an empty `msgstr` for `个人资料已保存，评论标签审核中。`.
- Added the missing English translation through `tools/build-i18n.mjs` and regenerated `en_US.po` / `en_US.mo`; the same profile save path now returns `Profile saved. Comment badges are pending review.`. Password mismatch still returns the expected error.
- Admin Users review UI renders one pending badge card and one pending avatar card for qauser. Approving the custom badge changes the action set from approve/reject to revoke; approving the avatar changes the action set from approve/reject to delete.
- Admin Comments review UI renders a pending temporary image upload card with approve/reject/delete links. A direct admin-action verification promoted a temporary image into `yneko-reimu-comments/YYYY/MM/`, updated the comment content URL, approved the held comment, cleared temp review meta, and created an approved attachment record.
- Limitation: browser text entry/file upload remained constrained by the in-app automation runtime, so profile form mutation and comment media upload were verified through the same PHP AJAX/admin handlers rather than direct browser file-selection.

## 2026-06-04 I18n Message Contract Gate Findings

- Added `tools/check-i18n-messages.mjs` as a dependency-free contract check for high-impact English feedback strings in auth, profile, comment, upload, and review flows.
- `npm run check` now runs the i18n message contract after `npm run build`, so it validates regenerated `theme/Yneko-Reimu/languages/en_US.po` rather than stale files.
- The gate currently covers 27 user-visible messages, including login expiry/success, profile save success variants, password mismatch, avatar/tag/comment review statuses, comment submit/update/delete feedback, upload errors, and permission/invalid-upload errors.
- Filled focused English translation gaps for review statuses and admin/upload feedback: avatar/tag/comment review rejected/pending, pending admin upload, invalid comment upload attachment, insufficient permissions, comment updated, and comment deleted.
- This is intentionally not a full translation-completeness check; existing low-risk historical empty `en_US.po` entries remain outside the gate until they become high-impact feedback or are translated in a dedicated pass.

## 2026-06-04 I18n Email/OAuth Contract Expansion Findings

- The focused i18n message contract now covers 57 high-impact `en_US` messages, up from 27.
- Added contract coverage for registration email-code feedback, lost-password code feedback, profile email/password/TOTP security messages, and GitHub OAuth callback/login/bind errors.
- The first expanded contract run caught a real remaining gap: `请输入注册邮箱。` had no English translation source. Added it as `Please enter the registered email address.`.
- Added English source translations for GitHub OAuth success/callback messages that were previously generated with empty `msgstr` values, including missing OAuth responses, expired state, token/API failures, missing profile fields, already-linked accounts, missing linked accounts, and existing-email bind guidance.
- The contract remains intentionally scoped; low-risk historical empty strings such as widget/admin labels are still outside this gate unless they become high-impact user feedback.

## 2026-06-04 GitHub OAuth Static Contract Findings

- Added a static GitHub OAuth contract gate covering 10 contract groups.
- The gate locks the public login form actions `yneko_reimu_github_login`, `yneko_reimu_github_bind`, `yneko_reimu_github_callback`, and the legacy `yneko_github_login` / `yneko_github_callback` actions.
- It also checks the bind nonce `yneko_reimu_github_bind`, state transient prefix, 10-minute state TTL, redirect validation, popup `data-reimu-github-popup` selector, popup message type `yneko-reimu-github-login`, and popup window name/size.
- Settings compatibility coverage includes `github_oauth` keys, legacy `yneko_reimu_github_login_options` and `yneko_github_login_options` fallback, admin field names, and the callback URL fallback.
- User compatibility coverage includes both current `_yneko_reimu_github_*` meta keys and legacy `_yneko_github_*` meta keys.
- This gate is static and does not prove a live GitHub OAuth callback. The next QA target should exercise missing-code/state-expired/token/API/profile/account-link error paths with a local or staging callback setup.

## 2026-06-04 GitHub OAuth Error-Path QA Findings

- The local WordPress QA environment was still active with the current Yneko-Reimu theme mounted and activated.
- Host-side requests to `http://127.0.0.1:8095/wp-login.php?action=...` returned a proxy-level 502, but container-internal requests to `http://127.0.0.1/wp-login.php?action=...` reached WordPress and produced expected statuses and messages. This appears to be an environment/proxy artifact rather than a theme callback failure.
- Verified callback with no `code` / `state`: HTTP 400 and `Missing GitHub OAuth response.`.
- Verified login start with empty OAuth settings: HTTP 403 and `GitHub login is not configured.`.
- Verified callback with fake code and missing/expired state: HTTP 403 and `GitHub login state expired. Please try again.`.
- Verified configured OAuth start redirects to GitHub with `client_id`, callback `redirect_uri`, `scope=read:user user:email`, generated `state`, and `allow_signup=true`.
- Verified fake token exchange failure returns `GitHub did not return an access token.` and consumes the state transient.
- Stubbed HTTP responses verified API failure, invalid profile, no linked account with auto-create disabled, existing WordPress email with auto-create enabled, and bind conflict with a GitHub ID already linked to another user.
- Added `docs/github-oauth-qa.md` so future staging QA can cover the real success path, popup close/postMessage behavior, non-popup redirect, and real GitHub binding flow.

## 2026-06-04 Email and TOTP QA Findings

- Local WordPress QA used an ignored `wp-local/email-totp-qa.php` helper with `pre_wp_mail` capture, AJAX `wp_die` interception, and transient cleanup so registration, lost-password, profile email, TOTP, and 2FA login handlers could be exercised without a real SMTP server.
- Verified registration invalid input, code send, cooldown, wrong code, and successful registration.
- Verified password-reset invalid email, unknown-email generic success without mail, known-email code send, weak password rejection, wrong code rejection, and successful reset.
- Verified profile email same-email rejection, new-email code send, wrong-code rejection, and successful email change.
- Verified TOTP missing-secret rejection, secret/URI generation, wrong-code rejection, successful enablement, login requiring 2FA, wrong 2FA rejection, and successful login with the current generated TOTP code.
- QA exposed a real English localization defect: `en_US.po` had empty translations for verification email subjects and body templates, causing captured English registration/password-reset/profile-email messages to have blank subject/body and no code.
- Added English source translations and expanded the high-impact i18n contract from 57 to 66 messages so the verification email templates cannot become empty again after `npm run build`.
- Added `docs/email-totp-qa.md` to document static, local/stubbed, and staging/manual SMTP coverage.
- Limitation: this pass stubs `wp_mail`; a staging/manual pass with real SMTP and browser modal interaction is still needed before final release tagging.

## 2026-06-04 Real SMTP and Browser Email/TOTP QA Findings

- Started Mailpit as `wp-local-mailpit` on the `wp-local_default` Docker network and installed a local-only `wp-local/mailpit-smtp.php` mu-plugin in the WordPress container. A direct `wp_mail()` proof delivered to Mailpit with subject `SMTP proof` and body `Code 123456`.
- Browser QA opened `http://127.0.0.1:8095/yneko-qa-post/` successfully; the host-side 8095 browser path worked for this pass.
- Registration browser flow: opened the theme login modal, switched to register, entered nickname/email/password, clicked `发送验证码`, saw the send button change to `58 秒后重发`, and received a real SMTP message to `browser-reg@example.test` with subject `[Yneko QA] Registration verification code`, six-digit code, and five-minute expiry text. Entering the code completed registration and returned the modal to login with the registered email filled.
- Password-reset browser flow: switched to forgot-password, sent the code for `browser-reg@example.test`, saw the countdown button, and received a real SMTP message with subject `[Yneko QA] Password reset verification code`, six-digit code, and five-minute expiry/security text. Entering the code and a new password completed reset and returned to login.
- Profile email browser flow: opened the profile modal for the test user, confirmed current email display, entered `browser-profile@example.test`, clicked send code, saw the countdown button, and received a real SMTP message with subject `[Yneko QA] Email change verification code`, six-digit code, and five-minute expiry text. Entering the code saved the profile and updated the current email display.
- TOTP browser flow: profile modal showed TOTP enablement, generated secret `RHOVPXAGNDH3KRL24DVM`, displayed a visible QR image with a `qrserver.com` URL, saved successfully with the current generated code, and cleared the pending secret in user meta.
- 2FA login browser flow: after logout, password-only login for the TOTP-enabled account showed `Please enter your two-factor code.` and revealed the two-factor input; entering the current generated code logged in successfully and hid the login modal.
- Browser automation limitation: the in-app browser backend could not use clipboard-backed `fill()` / `type()` methods, so raw keyboard input was used. For the profile/TOTP user session, local `login-as.php` was used to set the browser cookie after server-side verification confirmed the password reset had succeeded.
- Real GitHub OAuth happy-path remains unverified: local WordPress settings have no GitHub OAuth Client ID/Secret, environment variables contain no OAuth credentials, and the repo only has local fake OAuth helper scripts used for previous error-path QA.

## 2026-06-04 GitHub OAuth Happy-Path Prerequisite Audit Findings

- Current WordPress GitHub OAuth settings remain empty: no Client ID, no Client Secret, no callback override, and auto-create disabled.
- No local environment variables matching GitHub OAuth/Yneko/tunnel credentials were present.
- GitHub CLI is authenticated for repository operations as `EkaEva`, but that token cannot be used as the theme's OAuth App Client Secret and does not provide a real app callback configuration.
- No local tunnel tooling was found (`ngrok`, `cloudflared`, or `localtunnel`), so the local `127.0.0.1:8095` WordPress site cannot currently receive a real callback from GitHub.
- The real happy-path evidence still missing is: successful GitHub authorization, callback token exchange against GitHub, linked/account-created WordPress user state, popup `postMessage` close/refresh, and non-popup redirect back to `redirect_to`.
- `docs/github-oauth-qa.md` now records the exact required inputs and observable success signals for the next staging run.
