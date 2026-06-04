# Yneko-Reimu Optimization Task Plan

Goal: implement the approved aggressive optimization plan for the Yneko-Reimu WordPress theme while keeping the Classic Hybrid architecture and GitHub Release distribution target.

## Phases

1. Baseline and notes - complete
2. Build pipeline and source structure - complete
3. Performance-first defaults and asset hooks - complete
4. Schema, semantics, and block-editor improvements - complete
5. Quality gates and CI/package scripts - complete
6. Documentation and verification - complete

## Decisions

- Keep `theme/Yneko-Reimu` as the installable theme source.
- Keep GitHub OAuth, third-party comments, and media features, but make defaults performance-first and feature-gated.
- Do not migrate to a full Block Theme in this iteration.

## Errors Encountered

| Error | Attempt | Resolution |
| --- | --- | --- |
| `composer` command not found locally | Tried `composer install --no-interaction --prefer-dist` | Keep Composer/WPCS tooling in repo and CI; report local environment limitation. |
| `composer` command not found locally | Checked before PHP lint in cleanup/repair pass | JS checks and build passed; PHP lint remains limited by local environment. |
| `npm install` reports 2 moderate advisories | Reinstalled dependencies to run build | Did not run `npm audit fix --force` because it may introduce breaking dependency changes; report residual advisory. |
| `composer` command not found locally | Checked before v0.1.12 release commit | Ran `npm run check:js`, `npm run build`, targeted `php -l`, and `npm run package`; GitHub Actions will run Composer PHPCS/WPCS. |

## 2026-06-02 Online Theme Audit

Goal: analyze https://yneko.com/ together with the local Yneko-Reimu theme code and identify likely theme-level problems, risks, and improvement opportunities without changing implementation code.

### Audit Phases

1. Online page baseline and visible behavior - complete
2. Asset, performance, and network signals - complete
3. Local theme code review for matching risks - complete
4. Summarize prioritized findings for the user - complete

## 2026-06-02 Cleanup and Theme Repair Implementation

Goal: implement the approved local cleanup and theme repair plan, covering SEO compatibility, security, performance, image attributes, accessibility, privacy notes, and package hygiene.

### Implementation Phases

1. Local WordPress Docker cleanup - complete
2. Theme SEO / Rank Math compatibility - complete
3. Auth enumeration and cooldown hardening - complete
4. Performance, search index, image, accessibility, and privacy changes - complete
5. Build and cleanup verification - complete

### Decisions

- Rank Math remains the primary SEO owner; theme meta and schema are fallback-only when common SEO plugins are absent.
- `/en/` and English post URLs are generated through the existing theme i18n helpers rather than a new routing layer.
- Local search keeps `/search.json`, but full content is opt-in through a Customizer setting/filter.
- APlayer remains available, but initializes after visibility/user interaction and defaults to metadata preload.
- The local WordPress dev site and its Docker resources are intentionally removed.

## 2026-06-02 Version and Source Slimming

Goal: keep the public version line at `v0.1.12`, generate local verification ZIPs with timestamped names, remove unneeded local/source files, and replace leftover `hero` file naming with Yneko banner terminology.

### Phases

1. Inventory removable files and stale naming - complete
2. Version/package naming update - complete
3. Runtime naming cleanup - complete
4. Source slimming and verification - complete

## 2026-06-02 v0.1.12 Documentation and GitHub Release

Goal: update README/admin configuration docs, add v0.1.12 release notes, validate the build/package, push `main`, and push the `v0.1.12` tag to trigger GitHub Actions.

### Phases

1. README configuration and packaging documentation - complete
2. v0.1.12 release notes - complete
3. Local validation and package refresh - complete
4. Commit, push, and tag release - complete

## 2026-06-03 Social and Share Configuration

Goal: add Customizer-managed article sharing and sidebar social links, matching the upstream Reimu icon families while separating stored URLs from front-end enablement.

### Phases

1. Confirm upstream and local behavior - complete
2. Add Customizer settings for share/social enablement and URLs - complete
3. Render article share buttons and full sidebar social set - complete
4. Add icon mappings and WeChat share interaction - complete
5. Build and verification - complete

### Decisions

- Manage article sharing and sidebar social links together in the WordPress Customizer.
- Article share supports the 8 upstream platforms and defaults to QQ and Weixin enabled.
- Sidebar social supports the 29 upstream platforms and defaults to GitHub enabled only.
- URL fields can be filled while their corresponding enable checkbox is off; disabled means hidden on the front end.
- QQ sharing opens the upstream `connect.qq.com` share URL; Weixin uses a lightweight QR/card popup rather than full screenshot capture.
- Weixin QR codes are generated locally through a Vite split chunk, so the front end does not depend on an external QR image API.

## 2026-06-03 Share and Social Follow-up

Goal: fix article share alignment/cursor behavior, expose the GitHub triangle badge toggle in the social Customizer section, and add Xiaohongshu as a sidebar social option.

### Phases

1. Confirm current share/social implementation and target files - complete
2. Add Customizer triangle badge toggle and Xiaohongshu definition/icon asset - complete
3. Fix article share layout and hover cursor behavior - complete
4. Build and verification - complete

## 2026-06-03 Loading Screen Fix

Goal: fix the local validation package getting stuck on the loader after the share/social build.

### Phases

1. Diagnose built front-end script parse failure - complete
2. Restore classic script compatibility for Weixin QR loading - complete
3. Rebuild, validate, and regenerate local package - complete

### Decisions

- Keep `reimu.js` compatible with WordPress classic script enqueuing instead of converting the enqueue to `type="module"`.
- Load the QR library as a lazy classic script from `assets/dist/qrcode.js`.

## 2026-06-03 Profile Modal and Virtual Share Fixes

Goal: fix missing share icons on virtual pages, clarify hot comment sorting, improve profile avatar upload feedback, and preserve custom tags when special tags are toggled.

### Phases

1. Inspect current share, comment sort, profile avatar, and tag flows - complete
2. Add virtual page share rendering for about/friend/projects - complete
3. Split avatar upload into immediate validation/upload/save flow - complete
4. Preserve custom tags as inactive/frozen when special tags consume display slots - complete
5. Build, lint, and regenerate validation package - complete

## 2026-06-03 Share Placement, Profile Save Flow, and Admin Badges

Goal: refine share placement on posts/virtual pages, align GitHub avatar/profile input behavior, stage avatar uploads until profile save, support selectable custom tags, move avatar status under the user name, and add pending-review badges in admin settings.

### Phases

1. Inspect current templates, profile modal JS/PHP, and admin settings counts - complete
2. Adjust share template placement for posts/about/friend/projects - complete
3. Update GitHub avatar URL and avatar upload save flow - complete
4. Add selectable custom tags with five stored rows and two active slots - complete
5. Add admin pending-review badge counts and section markers - complete
6. Build, lint, package, and verify release ZIP - complete

## 2026-06-03 Review Status Sync and Badge Cleanup

Goal: move all review/update/reject prompts under the current user name, remove duplicate admin badge rendering while adding WordPress menu badges, fix approved custom tags returning to the profile/front end, and refresh approved comments/media/profile state without requiring a page refresh.

### Phases

1. Inspect current status, profile payload, upload approval, and admin badge code - complete
2. Add shared user-facing review status state for avatar, tags, and comments - complete
3. Clean admin badge placement and WordPress sidebar menu badges - complete
4. Add front-end profile/comment polling and DOM refresh - complete
5. Build, lint, package, and verify release ZIP - complete

## 2026-06-03 Development Standards and Optimization Guardrails

Goal: implement the follow-up optimization plan by adding local-only production/agent rules, public development constraints, asset/package quality gates, and a first low-risk modularity improvement without changing public theme interfaces.

### Phases

1. Local-only standards and Git exclude - complete
2. Baseline size measurement and size/package quality gates - complete
3. Admin settings JavaScript extraction - complete
4. Public development documentation and planning records - complete
5. Build, lint, audit, package, and diff verification - complete

### Decisions

- `PROJECT.md` and `AGENTS.md` are local-only files and are excluded through `.git/info/exclude`, not `.gitignore`.
- Keep the main public front-end script compatible with WordPress classic script loading.
- Use short-term budgets of 120 KB for `assets/dist/reimu.js` and 220 KB for `assets/dist/reimu.css`.
- Treat package contents as a quality gate so development-only and local-only files cannot silently enter release ZIPs.

## 2026-06-03 Module Boundaries and Lazy-Loading Prep

Goal: establish the first front-end and PHP module boundaries while preserving classic script output, public settings/AJAX/template interfaces, and the v0.1.15 release line.

### Phases

1. Commit and push existing guardrails without creating a tag - complete
2. Extract front-end core/search/share helpers into source modules while keeping a single classic `reimu.js` build - complete
3. Move comment media upload/review functions into `inc/comments/uploads.php` - complete
4. Record lazy-loading candidates and verification results - complete

### Decisions

- Keep `theme/Yneko-Reimu/assets/src/reimu.js` as the single public front-end entrypoint.
- Internal front-end modules currently cover `core`, `dom`, `storage`, `events`, `search`, and `share`; these are not public APIs.
- Use Vite/Rollup single-entry IIFE builds for public classic scripts; the CSS build stays separate.
- Treat `inc/comments.php` as the comment module entrypoint and `inc/comments/uploads.php` as an internal implementation module.
- `npm run check:js` recursively checks source/tool JavaScript so new internal modules cannot bypass syntax checks.

## 2026-06-03 Settings Schema Split

Goal: continue low-risk PHP modularity by separating settings defaults, sanitizers, and read helpers from the admin settings page/rendering layer while preserving the `yneko_reimu_settings` option, registration hook, page slug, and admin UI behavior.

### Phases

1. Inspect `inc/settings.php` function boundaries - complete
2. Move defaults/sanitizers/getters into `inc/settings/schema.php` - complete
3. Keep registration, admin menu badges, rendering, and admin assets in `inc/settings.php` - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- `inc/settings.php` remains the public settings entrypoint loaded by `functions.php`.
- `inc/settings/schema.php` is an internal implementation module, not a public API.
- The `yneko_reimu_settings` option name, sanitizer callback name, admin page slug, and menu hooks stay unchanged.

## 2026-06-03 Settings Admin Helper Split

Goal: continue the settings-page decomposition by moving admin helper functions and admin asset loading into an internal settings module while leaving the main settings page renderer and field markup in `inc/settings.php`.

### Phases

1. Inspect remaining `inc/settings.php` function groups after schema split - complete
2. Move settings page registration, menu review badges, bilingual admin helpers, review counters, media-field helper, and admin asset enqueue into `inc/settings/admin.php` - complete
3. Keep the settings form renderer and review list renderers in `inc/settings.php` - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- `inc/settings/admin.php` is internal and loaded by `inc/settings.php`.
- The admin page slug `yneko-reimu-settings`, settings page callback name, menu hooks, badge behavior, and enqueued asset handles stay unchanged.
- Large form rendering remains in `inc/settings.php` for now; moving it needs a separate render-template pass.

## 2026-06-03 Settings Renderer Split

Goal: move independent settings-page renderer helpers out of `inc/settings.php` while keeping the main tabbed settings form in the entrypoint.

### Phases

1. Confirm helper render function boundaries after the admin helper split - complete
2. Move GIF upload notice/control, friend row, music row, comment upload review list, avatar review list, and user badge review list renderers into `inc/settings/renderers.php` - complete
3. Keep `yneko_reimu_render_settings_page()` and all form field names in `inc/settings.php` - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- `inc/settings/renderers.php` is internal and loaded by `inc/settings.php`.
- Renderer function names and call sites stay unchanged.
- The main tabbed form remains in `inc/settings.php`; extracting it should be the final settings-page render pass.

## 2026-06-03 Settings Page Split

Goal: finish the settings first-stage decomposition by moving the main tabbed settings page renderer out of `inc/settings.php`.

### Phases

1. Confirm `yneko_reimu_render_settings_page()` is the final large render block in `inc/settings.php` - complete
2. Move `yneko_reimu_render_settings_page()` into `inc/settings/page.php` - complete
3. Keep `inc/settings.php` as the settings entrypoint with module requires, registration, and save cleanup hooks - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- `inc/settings/page.php` is internal and loaded by `inc/settings.php`.
- The settings page callback remains `yneko_reimu_render_settings_page`.
- All form field names, tab keys, option names, nonces, and admin page slug remain unchanged.
- Settings PHP first-stage decomposition is now complete enough to move the next round back to runtime lazy-loading and budget enforcement.

## 2026-06-03 Runtime Loading Strategy

Goal: make the planned lazy-loading boundaries visible to quality gates before changing runtime loading behavior.

### Phases

1. Inspect current front-end initialization and asset enqueue behavior - complete
2. Add a machine-readable feature loading plan for search/share/comments-profile/APlayer/PhotoSwipe/Mermaid/KaTeX - complete
3. Extend `npm run check:size` to print and validate loading-plan trigger/gate metadata - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- Keep `assets/dist/reimu.js` as a single classic script in this round.
- Do not add dynamic imports or change WordPress script enqueue handles yet.
- Use the loading plan as the source of truth for the next actual runtime split.

## 2026-06-03 Search Runtime Split

Goal: move the search implementation out of the main public script while preserving classic script loading, current search DOM behavior, and public compatibility.

### Phases

1. Inspect search module dependencies and public behavior - complete
2. Add a lazy classic search runtime entry and main-bundle trigger loader - complete
3. Extend size/classic checks to cover the lazy search runtime - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- Use `assets/dist/reimu-search.js` as an internal lazy classic runtime file.
- Keep `assets/dist/reimu.js` as the single WordPress-enqueued main script.
- Do not expose the internal search runtime as a documented public API.
- The next runtime split candidate should be PhotoSwipe because it has a visible page-context trigger and no AJAX/security payloads.

## 2026-06-03 PhotoSwipe Runtime Split

Goal: move the lightweight image-preview implementation out of the main public script while preserving the PhotoSwipe feature switch, image wrapping behavior, overlay UI, and `window.REIMU_PHOTOSWIPE.destroy()` compatibility.

### Phases

1. Inspect PhotoSwipe function boundaries and integration points - complete
2. Add a lazy classic PhotoSwipe runtime entry and internal source module - complete
3. Extend build and size/classic checks to cover the PhotoSwipe runtime - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- Use `assets/dist/reimu-photoswipe.js` as an internal lazy classic runtime file.
- Keep the existing `yneko_reimu_photoswipe_enable` setting and `window.REIMU_CONFIG.photoswipe` gate unchanged.
- Keep the main WordPress-enqueued script as `assets/dist/reimu.js`; do not enqueue the PhotoSwipe runtime through PHP.
- The next runtime candidate should avoid comment/profile security flows until a focused review plan is ready.

## 2026-06-03 Share Runtime Split

Goal: move share popup and Weixin QR behavior out of the main public script while preserving share markup, share URLs, Customizer settings, and the lazy `qrcode.js` dependency.

### Phases

1. Inspect share module boundaries and template integration - complete
2. Add a lazy classic share runtime entry - complete
3. Extend build and size/classic checks to cover the share runtime - complete
4. Build, lint, package, and verify release ZIP contents - complete

### Decisions

- Use `assets/dist/reimu-share.js` as an internal lazy classic runtime file.
- Keep `assets/dist/qrcode.js` as the Weixin-only QR dependency loaded from inside the share runtime.
- Keep all share Customizer settings, share link URLs, template markup, and public post/virtual page placement unchanged.
- The next round should either review comments/profile before runtime work there, or split a non-AJAX visual/content runtime with a similarly clear trigger.

## 2026-06-03 Comments/Profile Safety Interface Review

Goal: map the comments/profile public interface before any AJAX-sensitive runtime split, so future extraction preserves nonces, actions, payload fields, DOM triggers, login-state refresh behavior, and review-status polling.

### Phases

1. Inventory comment/profile AJAX actions and nonce names - complete
2. Map front-end DOM triggers and payload fields - complete
3. Identify safe split boundaries and high-risk prerequisites - complete
4. Record next implementation direction without changing runtime code - complete

### Decisions

- Do not split comments/profile into a lazy runtime as one large move.
- Keep all auth, profile, comment submit/edit/delete/like, upload, and review-status AJAX action names unchanged.
- Keep `window.REIMU_CONFIG.login`, `window.REIMU_CONFIG.commentUploads`, `window.REIMU_CONFIG.comments`, and `window.ReimuWP.init()` behavior unchanged.
- The next implementation round should extract low-risk comment UI/source modules first, while keeping AJAX submit/upload/profile save handlers in the main bundle until there is a narrower runtime contract.

## 2026-06-04 Comment Media Source Module Split

Goal: start the comments/profile follow-up with a source-only extraction of low-risk comment media utilities while keeping the public `assets/dist/reimu.js` classic script, all AJAX actions, nonce names, payload fields, and profile/comment runtime behavior unchanged.

### Phases

1. Extract comment media text/token/preview helpers into an internal source module - complete
2. Wire `assets/src/reimu.js` to consume the module without adding a lazy runtime - complete
3. Verify classic script compatibility, size budgets, audit, PHP lint, package contents, and local-only exclusions - complete
4. Record the next low-risk extraction target - complete

### Decisions

- `assets/src/reimu/comment-media.js` is an internal source module, not a public API and not a release ZIP file.
- The module receives `getConfig()`, `t()`, `escapeHtml()`, and `dispatchInputEvent()` as injected dependencies so it can keep reading the current PJAX-synced config.
- Comment submit/upload/like/edit/delete, login-state refresh, profile save, and profile polling remain in `assets/src/reimu.js`.
- The next round can extract comment popover/tool binders or profile form UI helpers, but should still avoid moving AJAX request handlers.

## 2026-06-04 Comment Tools Source Module Split

Goal: continue comments/profile modularity with a source-only extraction of comment popover, GIF library, and toolbar binding helpers while leaving comment upload AJAX and all auth/profile/comment mutation flows in the main entrypoint.

### Phases

1. Extract comment popover/tool/GIF binding helpers into an internal source module - complete
2. Keep comment upload row and upload AJAX request logic in `assets/src/reimu.js` - complete
3. Verify classic script compatibility, size budgets, audit, PHP lint, package contents, and local-only exclusions - complete
4. Record the next low-risk extraction target - complete

### Decisions

- `assets/src/reimu/comment-tools.js` is an internal source module, not a public API and not a release ZIP file.
- The module receives `initCommentUploadRows()` as an injected dependency so upload UI can still initialize without moving upload request code.
- Existing comment toolbar DOM attributes, preview behavior, GIF insertion, URL insertion, upload row behavior, and `window.ReimuWP.init()` rebinding remain unchanged.
- The next round should target either comment sorting/load-more helpers or profile form-only helpers, still avoiding AJAX handler migration.

## 2026-06-04 Comment List Source Module Split

Goal: continue source-only comments modularity by extracting comment sorting, hot-score calculation, latest-activity calculation, and load-more helpers while keeping comment submit, like, edit, delete, and login/profile flows in the main entrypoint.

### Phases

1. Extract comment sorting and load-more helpers into an internal source module - complete
2. Keep submitted-comment insertion and AJAX mutation handlers in `assets/src/reimu.js` - complete
3. Verify classic script compatibility, size budgets, audit, PHP lint, package contents, and local-only exclusions - complete
4. Record the next low-risk extraction target - complete

### Decisions

- `assets/src/reimu/comment-list.js` is an internal source module, not a public API and not a release ZIP file.
- The module owns only DOM/list behavior: hot score, latest activity, load-more item collection, load-more sync, sort mode, and sort button binding.
- `appendSubmittedComment()`, `initCommentLikes()`, `initCommentOwnerActions()`, and `initAjaxCommentSubmit()` remain in the main source file because they rebind or perform AJAX-sensitive flows.
- The next round should target profile form UI-only helpers or pause source splitting to reassess whether the remaining comments/profile code is now too AJAX-sensitive for safe extraction.

## 2026-06-04 Profile Form UI Source Module Split

Goal: extract profile modal form-only helpers while keeping profile fetch/save, email-code, TOTP, avatar upload, login-state refresh, and profile status polling in the main entrypoint.

### Phases

1. Extract profile form UI helpers into an internal source module - complete
2. Keep profile AJAX actions and status polling in `assets/src/reimu.js` - complete
3. Verify classic script compatibility, size budgets, audit, PHP lint, package contents, and local-only exclusions - complete
4. Record remaining extraction risk - complete

### Decisions

- `assets/src/reimu/profile-form.js` is an internal source module, not a public API and not a release ZIP file.
- The module owns URL normalization, password validation/toggles, avatar dirty/hint state, tag error rendering, custom tag row rendering, and selected tag limit UI.
- Profile payload application, `profile_get`, `profile_save`, `profile_email_code`, `profile_totp_generate`, login-state refresh, and review-status polling remain in the main source file.
- Remaining comments/profile code is now mostly AJAX-sensitive or rebind-heavy; the next round should reassess boundaries before any further split.

## 2026-06-04 Comments/Profile Runtime Contract

Goal: create a written contract and manual QA checklist for the comments/profile runtime before any future AJAX-sensitive extraction or lazy runtime split.

### Phases

1. Document preserved config keys, AJAX actions, DOM selectors, and runtime invariants - complete
2. Document manual QA coverage for guest auth, logged-in profile, comments, review status, and PJAX rebinds - complete
3. Link the contract from public development docs - complete
4. Record next implementation direction - complete

### Decisions

- `docs/comments-profile-contract.md` is a public development contract, not a new theme API.
- The contract explicitly blocks moving login/profile/comment request handlers or introducing `reimu-comments.js` / `reimu-profile.js` without a dedicated manual QA pass.
- `docs/development.md` now points developers to the contract before changing comments/profile AJAX handlers, DOM replacement, or runtime boundaries.
- The next round should use this contract to audit the remaining `assets/src/reimu.js` comments/profile code and identify whether any request-free module remains worth extracting.

## 2026-06-04 Profile Status UI Source Module Split

Goal: use the comments/profile contract to extract only request-free profile review status UI helpers, while keeping acknowledgement, polling, profile fetch/save, comment mutation, login-state refresh, and DOM replacement behavior in the main entrypoint.

### Phases

1. Audit remaining comments/profile functions against the contract - complete
2. Extract profile status text/row/inline DOM rendering into an internal source module - complete
3. Keep status acknowledgement, polling, and request handlers in `assets/src/reimu.js` - complete
4. Verify classic script compatibility and size budget - complete

### Decisions

- `assets/src/reimu/profile-status.js` is an internal source module, not a public API and not a release ZIP file.
- The module owns only status message lookup, review-status row normalization, current-user inline status DOM rendering, and autohide scheduling.
- `ackProfileStatuses()`, `startProfileStatusPolling()`, `refreshProfile()`, `postProfileAction()`, comment submit/upload/like/edit/delete, and login-state DOM replacement remain in `assets/src/reimu.js`.
- The next round should either stop comments/profile extraction until manual WordPress QA is available, or move to another low-risk area outside the AJAX/rebind contract.

## 2026-06-04 PHP Complexity Report Gate

Goal: add a reproducible PHP complexity report so future refactors can track large files/functions before turning thresholds into failing quality gates.

### Phases

1. Add a dependency-free PHP complexity report script - complete
2. Expose the report through npm scripts - complete
3. Document the report as informational quality tooling - complete
4. Record the current baseline and next refactor targets - complete

### Decisions

- `npm run report:php-complexity` is informational and does not fail the build yet.
- The script reports largest files, largest named functions, and highest approximate branch-score functions under `theme/Yneko-Reimu`.
- The current baseline should guide future PHP splits, especially `inc/comments.php`, `inc/settings/page.php`, `inc/customizer.php`, and `inc/template-tags.php`.
- The next round should either add this report to CI/check documentation as a non-failing artifact step, or start a targeted split of the highest-risk PHP renderer only if public interfaces stay unchanged.

## 2026-06-04 Settings Panels Split

Goal: reduce `inc/settings/page.php` complexity by moving low-risk settings page panels into an internal renderer module without changing option names, tab keys, field names, or save behavior.

### Phases

1. Identify self-contained settings page panels - complete
2. Move friend and music panel rendering into `inc/settings/panels.php` - complete
3. Require the new panel module before `inc/settings/page.php` - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- `inc/settings/panels.php` is an internal admin renderer module.
- The friend panel keeps `data-yneko-settings-panel="friends"` and all `yneko_reimu_settings[friend_site]` / `yneko_reimu_settings[friends]` field names unchanged.
- The music panel keeps `data-yneko-settings-panel="music"` and all `yneko_reimu_settings[player]` / `yneko_reimu_settings[music]` field names unchanged.
- The next round should continue settings page decomposition with another self-contained panel, likely external comments or extensions, before considering higher-risk comments/profile PHP handlers.

## 2026-06-04 Settings Extension Panels Split

Goal: continue settings page decomposition by moving the extensions and external-comments panels into `inc/settings/panels.php` without changing settings keys, tab keys, input names, or third-party feature defaults.

### Phases

1. Identify extensions and external-comments panel dependencies - complete
2. Move both panel renderers into `inc/settings/panels.php` - complete
3. Replace page markup with panel function calls - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- The extensions panel keeps `data-yneko-settings-panel="extensions"` and all `yneko_reimu_settings[features]` / `yneko_reimu_settings[third_party]` names unchanged.
- The external-comments panel keeps `data-yneko-settings-panel="external-comments"` and all `yneko_reimu_settings[external_comments]` names unchanged.
- `inc/settings/page.php` now stays focused on the top-level form, tabs, and remaining unextracted panels.
- The next round should continue with the search or i18n panel, which are smaller and self-contained, or pause PHP splitting to run manual admin UI checks.

## 2026-06-04 Settings Search/I18n Panels Split

Goal: continue low-risk settings page decomposition by moving the search and i18n settings panels into `inc/settings/panels.php` without changing tab keys, option names, field names, or save behavior.

### Phases

1. Identify search and i18n panel dependencies - complete
2. Move both panel renderers into `inc/settings/panels.php` - complete
3. Replace page markup with panel function calls - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- The i18n panel keeps `data-yneko-settings-panel="i18n"` and all `yneko_reimu_settings[i18n]` field names unchanged.
- The search panel keeps `data-yneko-settings-panel="search"` and all `yneko_reimu_settings[search]` field names unchanged.
- This split remains in the admin-renderer layer and does not touch settings sanitization, front-end search runtime, URL routing, or i18n helpers.
- The next round should either split the remaining GitHub/admin comments/users panels cautiously or pause for manual admin UI checks before moving higher-risk review sections.

## 2026-06-04 Settings GitHub Panel Split

Goal: continue cautious settings page decomposition by moving the GitHub OAuth settings panel into `inc/settings/panels.php` without changing OAuth settings keys, callback URL behavior, bind URL behavior, or login actions.

### Phases

1. Identify GitHub panel dependencies - complete
2. Move GitHub panel rendering into `inc/settings/panels.php` - complete
3. Replace page markup with a panel function call - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- The GitHub panel keeps `data-yneko-settings-panel="github"` and all `yneko_reimu_settings[github_oauth]` field names unchanged.
- The panel still receives the same callback URL value calculated by `yneko_reimu_render_settings_page()`.
- GitHub OAuth callbacks, binding helper functions, user meta keys, and front-end login behavior are untouched.
- The next round should pause for an admin UI/manual settings-page check or split only the comments/users renderer panels with extra care because they include review-management surfaces.

## 2026-06-04 Settings Comments Panel Split

Goal: move the comments settings panel into `inc/settings/panels.php` while preserving comment upload settings keys, review helper calls, admin upload form behavior, and all comment/profile AJAX surfaces.

### Phases

1. Identify comments panel dependencies and review helper calls - complete
2. Move comments panel rendering into `inc/settings/panels.php` - complete
3. Replace page markup with a panel function call - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- The comments panel keeps `data-yneko-settings-panel="comments"` and all `comment_avatar_url` / `comment_upload` field names unchanged.
- Existing calls to `yneko_reimu_render_admin_comment_gif_upload()` and `yneko_reimu_render_comment_upload_admin()` remain in the same visual section and order.
- This split does not touch comment upload AJAX handlers, review approval/rejection actions, nonce names, payload fields, or front-end comment behavior.
- The next round should split the users panel or perform an admin settings UI check, since users is now the last large panel left in `inc/settings/page.php`.

## 2026-06-04 Settings Users Panel Split

Goal: move the users settings panel into `inc/settings/panels.php` while preserving user badge/avatar-frame settings, review badge counts, admin review helper calls, and all profile/comment runtime behavior.

### Phases

1. Identify users panel dependencies and review badge usage - complete
2. Move users panel rendering into `inc/settings/panels.php` - complete
3. Replace page markup with a panel function call - complete
4. Verify PHP syntax, PHPCS, and complexity report impact - complete

### Decisions

- The users panel keeps `data-yneko-settings-panel="users"` and all `user_badges` / avatar upload field names unchanged.
- Existing calls to `yneko_reimu_render_user_badge_admin()` and `yneko_reimu_render_user_avatar_admin()` remain in the same visual section and order.
- Review badge counts are still provided by `yneko_reimu_admin_review_badge_counts()` and passed into the users panel renderer.
- This split does not touch profile AJAX handlers, avatar review actions, badge review actions, user meta keys, nonce names, payload fields, or front-end profile behavior.
- The next round should perform a focused admin settings UI/manual checklist and then decide whether the settings-page decomposition is complete for this phase.

## 2026-06-04 Settings Admin Contract Gate

Goal: add a reproducible settings admin contract check so future settings-page renderer changes cannot silently remove tabs, panels, key field names, repeatable row fields, or review helper sections.

### Phases

1. Identify settings admin structural contracts after panel extraction - complete
2. Add `tools/check-settings-admin-contract.mjs` - complete
3. Expose `npm run check:settings-admin` and include it in `npm run check` - complete
4. Document the new gate and run full verification - complete

### Decisions

- The contract gate verifies 10 settings tabs, 10 matching panels, all extracted panel renderer calls, key settings fields, repeatable friend/music row fields, admin GIF upload form ownership, and comments/users review helper calls.
- This is a static structural gate, not a replacement for manual WordPress admin UI testing.
- The gate keeps `PROJECT.md` / `AGENTS.md` local-only behavior unchanged and does not alter runtime theme behavior.
- The next round should perform a final completion audit against the original optimization plan and only mark the goal complete if current evidence proves all explicit requirements are satisfied.
