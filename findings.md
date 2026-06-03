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
