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

## 2026-06-04 Final Optimization Completion Audit

Goal: close the v0.1.15 follow-up optimization plan by verifying the implemented guardrails, module splits, package hygiene, local-only file policy, and no-tag release constraint before marking the current plan complete.

### Phases

1. Verify full quality gates and audit status - complete
2. Regenerate and inspect the final v0.1.15 validation package - complete
3. Confirm local-only files and no-tag policy - complete
4. Record remaining manual QA boundary - complete

### Decisions

- The current optimization plan is complete based on local automated evidence and static contracts.
- `PROJECT.md` and `AGENTS.md` remain local-only through `.git/info/exclude` and must not be committed or packaged.
- No `v0.1.15` tag is created or pushed because the user explicitly reserved the tag for later work.
- Manual WordPress admin UI/browser QA was not performed in this environment; the settings admin contract gate and comments/profile contract checklist remain the protection until a real WordPress admin session is available.

## 2026-06-04 Local WordPress QA Pass

Goal: run the previously deferred real WordPress admin/front-end QA pass against the current v0.1.15 theme, focusing on the extracted settings panels, admin settings JavaScript, classic front-end runtime compatibility, and comments/profile contract.

### Phases

1. Prepare a local-only WordPress test environment - complete
2. Install and activate the current theme build - complete
3. Verify admin settings tabs, panels, repeatable controls, and review sections - complete
4. Verify front-end runtime basics, lazy search/share/photoswipe loading, and comments/profile smoke paths - complete
5. Record QA evidence and remaining limitations - complete

### Decisions

- Use `.gitignore`-excluded `wp-local/` or another local-only path for Docker/WordPress state.
- Do not commit Docker state, WordPress uploads, credentials, or local QA scaffolding unless a later public dev-environment plan explicitly approves it.
- Do not create or push the `v0.1.15` tag during this QA pass.
- Manual QA found and fixed one admin settings regression: adding a friend/music repeatable row did not refresh its row heading because `refreshNumbers()` ignored the repeatable element when it was passed as the root.
- Comments/profile request handlers remain in the main runtime; this QA pass validates smoke paths but does not unlock a broad comments/profile lazy-runtime split by itself.

## 2026-06-04 Comments/Profile Review Flow QA

Goal: extend the local WordPress QA pass from smoke coverage into comments/profile review-state coverage, without moving AJAX handlers, changing settings keys, changing nonce names, or creating the `v0.1.15` tag.

### Phases

1. Enable local comment upload, avatar upload, and user badge review switches - complete
2. Exercise logged-in profile save and user badge review status paths - complete
3. Exercise comment media upload/review state and admin review sections - complete
4. Verify admin approve/reject/revoke links or record limitations - complete
5. Record QA findings, run relevant checks, and push only public records/fixes - complete

### Decisions

- This round is a QA/de-risking pass, not a new source split.
- Local helper scripts under `wp-local/` remain untracked and are not release artifacts.
- Any bug fix must preserve `window.REIMU_CONFIG`, `window.ReimuWP`, AJAX action names, nonces, meta keys, and template paths.
- If browser file upload cannot be exercised reliably, use seeded WordPress state to verify admin review surfaces and server-side review actions.
- This QA round found and fixed an empty English translation for the profile-save comment-badge-review success message.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint, `npm run package`, and `npm run check:package`.
- The next round should add an i18n completeness guard for high-impact AJAX/user-facing strings so empty `en_US` translations cannot silently erase messages again.

## 2026-06-04 I18n Message Contract Gate

Goal: add a focused i18n quality gate so high-impact AJAX/profile/comment/review messages cannot ship with an empty `en_US` translation after `npm run build` regenerates gettext files.

### Phases

1. Identify high-impact message scope from comments/profile/review flows - complete
2. Add a dependency-free i18n message contract checker - complete
3. Wire the checker into `npm run check` and public development docs - complete
4. Run full verification and package checks - complete
5. Commit and push public changes only; do not create the `v0.1.15` tag - complete

### Decisions

- This gate is intentionally scoped to user-visible feedback in auth, profile, comment, upload, and review flows.
- The gate does not require every historical `en_US.po` string to be translated.
- The source of truth remains `tools/build-i18n.mjs`; the checker validates generated `theme/Yneko-Reimu/languages/en_US.po`.
- Adding a new high-impact AJAX/review message should require adding it to the checker and to the English translation table in the same change.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint, `npm run package`, and `npm run check:package`.
- The next round should either expand the i18n contract to email/OAuth security messages or move to another high-risk QA surface such as GitHub OAuth callback behavior.

## 2026-06-04 I18n Email/OAuth Contract Expansion

Goal: expand the focused i18n quality gate from auth/profile/comment/review messages into email verification, password reset, profile email/TOTP security feedback, and GitHub OAuth callback messages without changing runtime behavior or public interfaces.

### Phases

1. Identify email/OAuth high-impact message scope - complete
2. Add selected messages to `tools/check-i18n-messages.mjs` - complete
3. Add missing English translations to `tools/build-i18n.mjs` and regenerate gettext files - complete
4. Update public development docs and persistent records - complete
5. Run full verification, package checks, commit, and push without creating the `v0.1.15` tag - complete

### Decisions

- The i18n contract now covers 57 high-impact messages across login, registration email codes, lost-password codes, profile email/password/TOTP feedback, comment/profile review, upload, and GitHub OAuth callback flows.
- This remains a focused contract, not a full `en_US.po` completeness requirement.
- GitHub OAuth callback handlers, AJAX action names, nonce names, settings keys, user meta keys, template paths, and `window.ReimuWP` are unchanged.
- `tools/build-i18n.mjs` remains the English translation source of truth; generated `.po`/`.mo` files are checked after regeneration.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1117.zip` and reported 134 entries with no forbidden development files.
- The next round should perform GitHub OAuth callback QA/error-path review or add a static contract for OAuth public endpoints and settings keys.

## 2026-06-04 GitHub OAuth Static Contract Gate

Goal: add a dependency-free static contract check for GitHub OAuth public endpoints, settings keys, compatibility fallbacks, popup integration, and security-sensitive callback surfaces without changing OAuth runtime behavior.

### Phases

1. Identify GitHub OAuth public interface and compatibility surface - complete
2. Add `tools/check-github-oauth-contract.mjs` - complete
3. Add `npm run check:github-oauth` and wire it into `npm run check` - complete
4. Update development docs and persistent records - complete
5. Run full verification, package checks, commit, and push without creating the `v0.1.15` tag - complete

### Decisions

- The OAuth contract gate checks login form actions, callback/bind URL actions, bind nonce, state transient naming, redirect validation, GitHub OAuth scope/endpoints, settings option keys, legacy option fallback, user meta compatibility keys, popup selector/message type, and high-impact OAuth error strings.
- This is a static structural gate. It does not replace a real GitHub OAuth App callback test.
- The gate is intentionally focused on public/compatibility contracts and does not assert every internal implementation detail in `inc/github-login.php`.
- No settings keys, login actions, nonce names, meta keys, filters, template paths, or front-end globals were changed.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1125.zip` and reported 134 entries with no forbidden development files.
- The next round should perform OAuth error-path QA with local/stubbed callback states, or document a manual GitHub OAuth staging checklist.

## 2026-06-04 GitHub OAuth Error-Path QA

Goal: exercise GitHub OAuth callback and account-linking error paths in the local WordPress QA environment without changing OAuth runtime behavior, committing local helper scripts, or creating the `v0.1.15` tag.

### Phases

1. Check local WordPress QA environment and OAuth settings state - complete
2. Verify callback missing-response, unconfigured login, expired state, redirect, token failure, API failure, invalid profile, no linked account, existing email, and bind conflict paths - complete
3. Add public OAuth QA checklist documentation and persistent records - complete
4. Run verification and package checks - complete
5. Commit and push public records/docs only; do not create the `v0.1.15` tag - in progress

### Decisions

- Local helper scripts were created only under ignored `wp-local/` and copied into the WordPress container for this QA pass.
- Host requests to `127.0.0.1:8095/wp-login.php?action=...` returned a proxy-level 502, but the same requests inside the WordPress container reached Apache/WordPress and returned the expected WordPress HTTP statuses. OAuth error-path assertions therefore used container-internal requests and PHP bootstrap scripts.
- The local site was reset to empty OAuth settings after the QA pass.
- Added `docs/github-oauth-qa.md` to document static, local/stubbed, and staging/real-app QA coverage.
- No settings keys, login actions, nonce names, meta keys, template paths, front-end globals, or OAuth runtime behavior were changed.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1135.zip` and reported 135 entries with no forbidden development files.
- The next round should either do real GitHub OAuth happy-path staging QA or move to another remaining high-risk surface such as email/TOTP QA.

## 2026-06-04 Email and TOTP QA

Goal: exercise registration email codes, password-reset email codes, profile email changes, authenticator TOTP setup, and 2FA login in the local WordPress QA environment without changing public AJAX actions, nonce names, settings keys, user meta keys, template paths, or creating the `v0.1.15` tag.

### Phases

1. Confirm email/TOTP public interfaces and local QA method - complete
2. Run local WordPress/stubbed `wp_mail` QA for register, lost-password, profile email, TOTP, and 2FA login paths - complete
3. Fix any QA-exposed localization contract gaps and document the checklist - complete
4. Run full verification and package checks - complete
5. Commit and push public changes only; do not create the `v0.1.15` tag - complete

### Decisions

- Local helper scripts stay under ignored `wp-local/` and are not public repository files or release artifacts.
- This round preserves all auth/profile AJAX action names, nonce names, payload fields, transient naming, TOTP user meta keys, front-end globals, and login/profile runtime behavior.
- QA found a real `en_US` localization bug: verification email subject/body templates were empty translations, which produced blank registration/password-reset/profile-email messages on English sites.
- The fix adds the three verification email subjects, three code lines, and three expiry/body lines to `tools/build-i18n.mjs` and `tools/check-i18n-messages.mjs`; the focused i18n contract now covers 66 high-impact messages.
- Added `docs/email-totp-qa.md` and linked it from `docs/development.md`.
- Verification passed: local stubbed Email/TOTP QA, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1151.zip` and reported 136 entries with no forbidden development files.
- The next round should perform staging/manual SMTP and browser-modal QA for email/TOTP, or move to another remaining high-risk area such as real GitHub OAuth happy-path staging.

## 2026-06-04 Real SMTP and Browser Email/TOTP QA

Goal: verify the remaining Email/TOTP release-blocking paths with real SMTP delivery through a local mail-capture service and browser-level modal interaction, then check whether GitHub OAuth happy-path QA can run with real OAuth credentials.

### Phases

1. Start a local mail-capture SMTP service and wire WordPress `wp_mail()` to it - complete
2. Verify browser registration code send/countdown/mail/register flow - complete
3. Verify browser password-reset code send/countdown/mail/reset flow - complete
4. Verify browser profile email code send/countdown/mail/save flow - complete
5. Verify browser profile TOTP generation, QR display, save, and login 2FA step - complete
6. Check real GitHub OAuth happy-path prerequisites - complete
7. Record evidence, limitations, and next release-blocking work - complete

### Decisions

- Mailpit was started as a local-only Docker container on the WordPress Docker network; WordPress used a local-only `wp-local/mailpit-smtp.php` mu-plugin to send real SMTP mail to Mailpit.
- Local helper scripts and Mailpit configuration remain under ignored `wp-local/` or container state and must not be committed or packaged.
- Browser QA used the real theme login/profile modals. The in-app browser backend could not use clipboard-based text filling, so text entry used raw keyboard input where needed; profile login was bootstrapped with the existing local `login-as.php` helper only after verifying password reset state, to avoid spending the round on browser-input tool limitations.
- Verified real SMTP delivery for registration, password-reset, and profile-email verification messages, including non-empty subject/body, six-digit code, and five-minute expiry text.
- Verified UI countdown states for registration, password reset, and profile email code buttons.
- Verified profile modal displays current/new email fields, TOTP enablement, generated secret, and visible QR image.
- Verified TOTP save succeeds and a later login requires the two-factor step, rejects password-only login by asking for a code, and succeeds with the current generated TOTP code.
- Real GitHub OAuth happy-path QA could not run in this environment because no real GitHub OAuth Client ID/Secret or staging callback credentials are configured in WordPress settings or environment variables.
- Verification passed after record updates: `npm run check` and `npm audit --audit-level=moderate`.
- The next round needs user-provided/staging OAuth credentials and a callback URL registered in a GitHub OAuth App, or confirmation to skip real GitHub OAuth happy-path until release staging.

## 2026-06-04 GitHub OAuth Happy-Path Prerequisite Audit

Goal: make concrete progress on the remaining GitHub OAuth real happy-path requirement by checking for usable credentials, callback/tunnel prerequisites, and documenting the exact staging runbook if the environment still cannot execute the flow.

### Phases

1. Reconfirm clean repo, no `v0.1.15` tag, and local WordPress/OAuth state - complete
2. Check WordPress GitHub OAuth settings for real Client ID/Secret - complete
3. Check local environment/GitHub CLI/tunnel tooling for real OAuth prerequisites - complete
4. Update GitHub OAuth QA runbook with required inputs and observable success signals - complete
5. Run focused verification and push public records without creating a tag - complete

### Decisions

- WordPress settings still have no GitHub OAuth Client ID or Client Secret.
- No OAuth credential environment variables are present.
- GitHub CLI is authenticated as `EkaEva`, but that repository token is not an OAuth App Client Secret and cannot prove the theme's real OAuth happy path.
- No local tunnel tool such as `ngrok`, `cloudflared`, or `localtunnel` was found, so a public callback URL is also unavailable in this environment.
- The real happy-path requirement remains unverified until credentials and a matching callback URL are provided.
- `docs/github-oauth-qa.md` now includes the required inputs, callback URL format, popup/non-popup/bind success signals, and current blocked status.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, and `git diff --check`.

## 2026-06-04 GitHub OAuth Real Happy-Path QA

Goal: complete the remaining real GitHub OAuth happy-path QA using user-provided local OAuth App credentials and the registered localhost callback, while keeping credentials and local helper files out of Git and avoiding the `v0.1.15` tag.

### Phases

1. Configure local WordPress OAuth settings and localhost callback proxy - complete
2. Verify non-popup auto-create/login flow against GitHub - complete
3. Verify account bind flow for the existing `qauser` account - complete
4. Verify popup login close/postMessage/refresh behavior - complete
5. Verify linked non-popup login returns to the same WordPress user - complete
6. Record public evidence without secrets and run final pre-release checks - complete

### Decisions

- The OAuth Client Secret is local-only QA input and must not be written to public docs, planning records, commits, or release packages.
- A local-only proxy exposed WordPress at `http://localhost:8080` to match the registered callback URL.
- The legacy callback action `yneko_github_callback` was used and remains compatible.
- Real OAuth success evidence now covers GitHub authorization, callback token/API exchange, auto-create login, existing-account bind, popup modal refresh, and linked non-popup login.
- Current and legacy GitHub user meta were both considered while preparing the bind QA path.
- No settings keys, login actions, nonce names, meta keys, template paths, front-end globals, or OAuth runtime behavior were changed.
- Final verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.1.15-20260604-1242.zip` and reported 136 entries with no forbidden development files.
- Do not create or push the `v0.1.15` tag during this QA closure.

## 2026-06-04 v0.2.0 Version Line

Goal: promote the completed architecture, quality-gate, Email/TOTP, and GitHub OAuth QA milestone from the reserved `v0.1.15` patch line to `v0.2.0`, without creating a tag until the user explicitly asks for release tagging.

### Phases

1. Inventory current `0.1.15` / `v0.1.15` release-facing references - complete
2. Update package/theme version fields and release-facing docs to `0.2.0` / `v0.2.0` - complete
3. Regenerate build/package and verify the `v0.2.0` validation ZIP - complete
4. Commit and push public version-line changes without creating `v0.2.0` tag - in progress

### Decisions

- Treat historical `v0.1.15-YYYYMMDD-HHMM` package names in progress logs as audit history, not current release instructions.
- Rename the release notes file to `docs/release-notes-v0.2.0.md`.
- Keep the no-tag policy until the user explicitly confirms final release tagging.
- Verification passed after the version-line change: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.0-20260604-1254.zip` and reported 136 entries with no forbidden development files.

## 2026-06-04 Release Readiness Hardening

Goal: implement the post-audit GitHub Release professionalization pass by closing direct-access guard gaps, adding runtime release metadata, clarifying license/credit posture, and turning release-readiness basics into static gates while leaving the release screenshot for manual regeneration.

### Phases

1. Add missing `ABSPATH` guards and update compatibility metadata - complete
2. Add runtime `readme.txt` and package inclusion - complete
3. Add release-readiness and package gates for guard/readme/screenshot/style metadata - complete
4. Update public docs, release notes, and persistent records - complete
5. Run verification and package checks after user-provided screenshot replacement - complete

### Decisions

- Keep the project distributed through GitHub Releases rather than targeting strict WordPress.org theme-directory submission in this pass.
- Keep the theme license as MIT while documenting WordPress GPL ecosystem and dependency-license responsibilities for redistributors.
- Use the user-provided `C:\Users\86135\Downloads\screenshot.png` as the refreshed release screenshot after confirming it is `1200x900`.
- Treat the `1200x900` screenshot as a release-readiness gate so future release checks fail loudly if the screenshot drifts.
- Do not split comments/profile, Customizer, or enqueue internals in this pass; record complexity hotspots and use static gates first to avoid changing high-risk public interfaces.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP readme/screenshot spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.0-20260604-1537.zip` and reported 137 entries with no forbidden development files.

## 2026-06-04 v0.2.1 Version and Template Smoke QA

Goal: promote the release-readiness hardening work to `v0.2.1`, restore the local WordPress QA URL after OAuth staging, and smoke-test the top-level wrapper templates that received `ABSPATH` guards.

### Phases

1. Inspect current version references and local WordPress QA state - complete
2. Update package/theme/runtime readme/README/release docs to `0.2.1` / `v0.2.1` - complete
3. Restore local WordPress QA URL and smoke-test homepage/category/tag/author/search wrappers - complete
4. Run full verification, package checks, and record results - complete

### Decisions

- Do not create or push a `v0.2.1` tag unless the user explicitly asks for release tagging.
- Keep historical `v0.2.0` package names in planning records as audit history.
- Use `--noproxy "*" ` for host-side local WordPress smoke requests because the system proxy can return 502 for `127.0.0.1:8095`.
- Restoring local `home` and `siteurl` from the previous OAuth QA `localhost:8080` value to `http://127.0.0.1:8095` is local-only QA state and not a repository change.
- Verification passed: local no-proxy WordPress wrapper smoke checks, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP style/readme/screenshot spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1550.zip` and reported 138 entries with no forbidden development files.

## 2026-06-04 Customizer Contract Prep

Goal: start the maintenance-hotspot work on `inc/customizer.php` without changing any Customizer public keys by adding a static contract gate and reducing the public customize callback to a thin entrypoint.

### Phases

1. Inspect Customizer section and setting structure - complete
2. Add a low-risk Customizer contract gate - complete
3. Thin the public `yneko_reimu_customize_register()` callback while preserving the hook name - complete
4. Run verification and define the next section-helper split - complete

### Decisions

- Do not rename any `theme_mod`, option-backed Customizer setting, section ID, panel ID, sanitizer callback, or control ID.
- Keep `yneko_reimu_customize_register` as the public `customize_register` callback.
- Use this round to add protection before a larger Customizer section-helper extraction.
- The next round should split `yneko_reimu_register_customizer_sections()` into focused helpers for preset/sidebar/visual/images/cards/articles/social/footer-virtual sections.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, `npm run report:php-complexity`, targeted and full PHP syntax lint, `npm run package`, `npm run check:package`, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1556.zip` and reported 138 entries with no forbidden development files.

## 2026-06-04 Customizer Helper Split and Missing Image Refresh

Goal: complete the next maintenance-hotspot pass by replacing the missing-comment image asset and splitting the Customizer visual-preview registration into focused internal helpers without changing public Customizer keys.

### Phases

1. Replace runtime `comment-missing.webp` with the user-provided image - complete
2. Split Customizer registration into panel/section helper functions - complete
3. Verify Customizer contract, PHP syntax, build, package, and ZIP contents - complete
4. Record complexity results and define the next optimization round - complete

### Decisions

- Use `C:\Users\86135\Downloads\comment-missing.webp` as the runtime missing-image fallback at `theme/Yneko-Reimu/assets/images/comment-missing.webp`.
- Preserve all Customizer public contracts: `customize_register` callback, panel IDs, section IDs, setting IDs, option-backed setting names, sanitizer callbacks, and control IDs.
- Keep helpers in `inc/customizer.php` for this pass instead of introducing a new include file; this reduces movement while removing the single large registration function.
- `yneko_reimu_register_customizer_sections()` is now a dispatcher for panel, preset, sidebar widgets, visual, images, cards, articles, social, and footer/virtual page helpers.
- Complexity after the split: `inc/customizer.php` remains 733 nonblank lines, but the former single 739-line Customizer registration function is gone. The largest Customizer helpers are now `yneko_reimu_register_customizer_social_section` at 157 lines and `yneko_reimu_register_customizer_preset_section` at 146 lines.
- Verification passed: source/target image SHA256 match, `php -l` for `inc/customizer.php`, `npm run check:customizer`, `npm run report:php-complexity`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1614.zip` and reported 138 entries with no forbidden development files. ZIP spot check confirmed `assets/images/comment-missing.webp` is included at 10,234 bytes and `screenshot.webp`, `assets/src`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should split `inc/enqueue.php` by extracting asset configuration/context helpers around `yneko_reimu_enqueue_assets()` while preserving script/style handles, localized config keys, lazy runtime URLs, and classic script compatibility.

## 2026-06-04 Enqueue Contract and Helper Split

Goal: reduce the `inc/enqueue.php` maintenance hotspot by protecting front-end asset/config contracts and splitting `yneko_reimu_enqueue_assets()` into focused internal helpers without changing runtime behavior.

### Phases

1. Identify enqueue public handles, asset URLs, config keys, and nonce contracts - complete
2. Add a static enqueue contract gate and wire it into `npm run check` - complete
3. Split style, search config, i18n/config, optional vendor, and main runtime helper blocks - complete
4. Run verification, package checks, and record the next optimization target - complete

### Decisions

- Preserve the public `wp_enqueue_scripts` hook callback `yneko_reimu_enqueue_assets`.
- Preserve script/style handles, third-party package paths/versions, `window.REIMU_CONFIG` shape, nonce names, lazy runtime URLs, and classic script behavior.
- Keep the split in `inc/enqueue.php` for this pass instead of introducing an include file; the goal is lower-risk function-level decomposition.
- Add `tools/check-enqueue-contract.mjs` and `npm run check:enqueue` so future resource changes fail loudly if they remove key handles, config fields, asset paths, or nonce names.
- `yneko_reimu_enqueue_assets()` is now a dispatcher that calls helpers for theme styles, search config, front-end i18n/config, optional vendor assets, and the main runtime.
- Complexity after the split: `yneko_reimu_enqueue_assets()` dropped out of the largest-function list. `inc/enqueue.php` is now 520 nonblank lines and 23 functions; the former 287-line enqueue function is split across smaller helpers.
- Verification passed: targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run report:php-complexity`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1621.zip` and reported 138 entries with no forbidden development files. ZIP spot check confirmed `inc/enqueue.php` and runtime `readme.txt` are included, while `tools/check-enqueue-contract.mjs`, `assets/src`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should address the remaining asset-budget risk by auditing CSS split candidates and adding a CSS/component budget contract before moving any styles out of the main `reimu.css`.

## 2026-06-04 CSS Split Plan Gate

Goal: reduce CSS asset-budget risk by documenting page/feature stylesheet split candidates and adding a static contract before moving any CSS out of the main `reimu.css` bundle.

### Phases

1. Inspect CSS build inputs, current output size, and high-density selector groups - complete
2. Add a machine-readable CSS split candidate plan with target outputs and budgets - complete
3. Add a static CSS split contract gate and wire it into `npm run check` - complete
4. Run verification, package checks, and record the first actual CSS split target - complete

### Decisions

- Keep the runtime CSS loading unchanged in this round; `reimu.css` remains the only main theme stylesheet besides `loader.css` and vendor styles.
- Add `tools/css-split-plan.mjs` with planned split candidates for comments/profile, APlayer, code content, PhotoSwipe, share, and search.
- Add `tools/check-css-split-plan.mjs` and `npm run check:css-split` to verify candidate selectors still exist in source CSS, each candidate has a trigger/gate/target output/budget, and the main `reimu.css` 220 KB budget remains enforced.
- Current source CSS inputs are `assets/src/yneko-reimu-base.css` at about 67.6 KB and `assets/src/yneko-reimu-adapter.css` at about 125.7 KB. Current built `assets/dist/reimu.css` is 210,179 bytes, about 205.3 KB against the 220 KB budget.
- Selector density makes comments/profile the biggest opportunity, but it is also the highest-risk surface. APlayer is a safer first actual CSS split because PHP already knows when the player is enabled and its selector block is relatively isolated.
- Verification passed: `npm run check:css-split`, `npm run check:js`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1628.zip` and reported 138 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu.css` is included, while `tools/css-split-plan.mjs`, `tools/check-css-split-plan.mjs`, source CSS, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should implement the first actual CSS split for APlayer/player styles as `assets/dist/reimu-player.css`, enqueue it only when `yneko_reimu_player_enabled()` / Meting player context requires it, and lower the main CSS budget if the split creates meaningful headroom.

## 2026-06-04 APlayer CSS Runtime Split

Goal: create the first actual CSS split by moving sidebar APlayer/player enhancement styles out of the main `reimu.css` bundle and loading them only when the player feature is active.

### Phases

1. Identify safe APlayer CSS boundaries and avoid high-risk compressed upstream base edits - complete
2. Move sidebar/player enhancement selectors into `assets/src/reimu-player.css` - complete
3. Build `assets/dist/reimu-player.css`, conditionally enqueue it, and update size/contract gates - complete
4. Run verification, package checks, and record the next CSS split target - complete

### Decisions

- Move the isolated sidebar/player enhancement block from `assets/src/yneko-reimu-adapter.css` to `assets/src/reimu-player.css`.
- Keep the compressed upstream/base `.aplayer` rules in the main CSS for now to avoid high-risk surgery inside the single-line reference snapshot.
- Generate `assets/dist/reimu-player.css` through `tools/build-reimu.mjs` and record it in `assets/dist/manifest.json`.
- Enqueue `yneko-reimu-player` only inside the existing `$enable_aplayer` branch, with `yneko-reimu-aplayer` as its dependency.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 220 KB to 212 KB, and the new `assets/dist/reimu-player.css` has a 20 KB budget.
- Update enqueue and CSS split contracts so the new handle, output path, source CSS, and selector ownership are protected.
- Build result: `assets/dist/reimu.css` is now 200,770 bytes / 196.1 KB against the 212 KB budget, and `assets/dist/reimu-player.css` is 9,505 bytes / 9.3 KB against the 20 KB budget.
- Verification passed: `npm run build`, targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run check:css-split`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1637.zip` and reported 139 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu.css` and `assets/dist/reimu-player.css` are included, while `assets/src/reimu-player.css`, CSS split tools, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should split PhotoSwipe enhancement styles into `assets/dist/reimu-photoswipe.css`, because PhotoSwipe already has a lazy JS runtime, feature gate, and isolated selector block.

## 2026-06-04 PhotoSwipe CSS Runtime Split

Goal: continue CSS asset-budget work by moving PhotoSwipe lightbox enhancement styles out of the main `reimu.css` bundle and loading them only when the PhotoSwipe feature is active.

### Phases

1. Identify the safe PhotoSwipe selector block and preserve generic article image/cursor rules - complete
2. Move PhotoSwipe enhancement selectors into `assets/src/reimu-photoswipe.css` - complete
3. Build `assets/dist/reimu-photoswipe.css`, conditionally enqueue it, and update size/contract gates - complete
4. Run verification, package checks, and record the next CSS split target - complete

### Decisions

- Move only the isolated `.reimu-photoswipe-*` and `.article-entry .reimu-photoswipe-item` selectors; keep generic `.wp-block-image`, dark-mode image, `.pswp__img`, and article gallery cursor rules in the main CSS.
- Generate `assets/dist/reimu-photoswipe.css` through `tools/build-reimu.mjs` and record `assets/src/reimu-photoswipe.css` in the build manifest `cssSources`.
- Preserve the existing vendor handle `yneko-reimu-photoswipe`; add `yneko-reimu-photoswipe-enhance` as the theme enhancement stylesheet with the vendor stylesheet as its dependency.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 212 KB to 208 KB, while `assets/dist/reimu-photoswipe.css` has a 12 KB budget.
- Build result after the split: `assets/dist/reimu.css` is 198,949 bytes / 194.3 KB against the 208 KB budget, and `assets/dist/reimu-photoswipe.css` is 1,842 bytes / 1.8 KB against the 12 KB budget.
- Verification passed: `npm run build`, targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run check:css-split`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1645.zip` and reported 140 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu.css`, `assets/dist/reimu-player.css`, and `assets/dist/reimu-photoswipe.css` are included, while `assets/src/reimu-photoswipe.css`, `assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should split article share/Weixin popup styles into `assets/dist/reimu-share.css`, because share already has a lazy JS runtime and a compact `.share-*` selector block.

## 2026-06-04 Share CSS Runtime Split

Goal: continue CSS asset-budget work by moving article share and Weixin popup enhancement styles out of the main `reimu.css` bundle and loading them only when share markup is expected on the current page.

### Phases

1. Identify the safe share selector block and actual share output path - complete
2. Move article share and Weixin popup enhancement selectors into `assets/src/reimu-share.css` - complete
3. Build `assets/dist/reimu-share.css`, conditionally enqueue it, and update size/contract gates - complete
4. Run verification, package checks, and record the next CSS split target - complete

### Decisions

- Move only the readable adapter CSS share layout and Weixin popup enhancement block; keep compressed upstream/base share rules and shared sidebar/social icon glyph/color rules in the main CSS.
- Use `template-parts/meta/post-share.php` and `yneko_reimu_share_links()` as the PHP evidence for whether share markup can render.
- Add `yneko_reimu_should_enqueue_share_styles()` as an internal helper that checks singular or virtual-page share context without adding a new public setting.
- Generate `assets/dist/reimu-share.css` through `tools/build-reimu.mjs` and record `assets/src/reimu-share.css` in the build manifest `cssSources`.
- Enqueue `yneko-reimu-share` after `yneko-reimu-main` only when `yneko_reimu_should_enqueue_share_styles()` is true.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 208 KB to 204 KB, while `assets/dist/reimu-share.css` has a 14 KB budget.
- Build result after the split: `assets/dist/reimu.css` is 195,989 bytes / 191.4 KB against the 204 KB budget, and `assets/dist/reimu-share.css` is 2,940 bytes / 2.9 KB against the 14 KB budget.
- Verification passed: `npm run build`, targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run check:css-split`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1653.zip` and reported 141 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu.css`, `assets/dist/reimu-player.css`, `assets/dist/reimu-photoswipe.css`, and `assets/dist/reimu-share.css` are included, while `assets/src/reimu-share.css`, `assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should split code/content enhancement styles into `assets/dist/reimu-code.css`, focusing on YML/code editor, virtual-page highlight, and Mermaid content selectors while keeping generic article typography in the main CSS.

## 2026-06-04 Code/Content CSS Runtime Split

Goal: continue CSS asset-budget work by moving code editor, virtual-page highlight, and Mermaid enhancement styles out of the main `reimu.css` bundle while preserving generic article typography and code runtime behavior.

### Phases

1. Identify safe code/content selector boundaries and keep generic article rules in main CSS - complete
2. Move code editor, virtual highlight, and Mermaid enhancement selectors into `assets/src/reimu-code.css` - complete
3. Build `assets/dist/reimu-code.css`, enqueue it in singular/virtual content contexts, and update size/contract gates - complete
4. Run verification, package checks, and record the next optimization target - complete

### Decisions

- Move only the readable adapter CSS block for `.reimu-yml-editor`, `.article-entry .wp-block-code.reimu-yml-editor`, `.reimu-virtual-page .highlight`, and `.article-entry .mermaid`.
- Keep generic article typography, base code colors, broad cursor selectors, `.wp-block-image img`, `.wp-block-gallery img`, and compressed upstream/base CSS in the main stylesheet.
- Generate `assets/dist/reimu-code.css` through `tools/build-reimu.mjs` and record `assets/src/reimu-code.css` in the build manifest `cssSources`.
- Enqueue `yneko-reimu-code` after `yneko-reimu-main` on singular and virtual-page contexts. This is conservative enough to preserve JavaScript-generated code-editor styling without fragile post-content scans.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 204 KB to 200 KB, while `assets/dist/reimu-code.css` has a 24 KB budget.
- Build result after the split: `assets/dist/reimu.css` is 192,348 bytes / 187.8 KB against the 200 KB budget, and `assets/dist/reimu-code.css` is 3,601 bytes / 3.5 KB against the 24 KB budget.
- Verification passed: `npm run build`, targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run check:css-split`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1703.zip` and reported 142 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu-code.css` is included, while `assets/src/reimu-code.css`, `assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should audit the remaining CSS split plan and either split search popup styles into `assets/dist/reimu-search.css` or move to comments/profile only after confirming a safer boundary.

## 2026-06-04 Search CSS Runtime Split

Goal: finish the low-risk CSS split backlog by moving readable search-page and search-popup enhancement styles out of the main `reimu.css` bundle while leaving compressed upstream popup layout in the main CSS.

### Phases

1. Audit search selectors across base CSS, adapter CSS, templates, and lazy search runtime - complete
2. Move safe readable search enhancement selectors into `assets/src/reimu-search.css` - complete
3. Build `assets/dist/reimu-search.css`, enqueue it with the global search template context, and update size/contract gates - complete
4. Run verification, package checks, and record the next optimization target - complete

### Decisions

- Move only readable adapter rules for `.reimu-search-form`, `.reimu-hit-type`, `body.search-popup-on`, `#reimu-search-input input`, and `.site-search .reimu-bg`.
- Keep compressed upstream/base popup layout and search result list styles in the main CSS because they are embedded in the single-line reference snapshot and are riskier to cut manually.
- Enqueue `yneko-reimu-search` globally as a dependency of `yneko-reimu-main`, because the `.site-search` template is globally rendered and the search result form can appear outside singular contexts.
- Keep the existing lazy search JavaScript runtime unchanged: `assets/dist/reimu-search.js` still loads on search interaction.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 200 KB to 198 KB, while `assets/dist/reimu-search.css` has a 16 KB budget.
- Build result after the split: `assets/dist/reimu.css` is 191,405 bytes / 186.9 KB against the 198 KB budget, and `assets/dist/reimu-search.css` is 1,289 bytes / 1.3 KB against the 16 KB budget.
- Verification passed: `npm run build`, targeted `php -l` for `inc/enqueue.php`, `npm run check:enqueue`, `npm run check:css-split`, `npm run check:size`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1713.zip` and reported 143 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu-search.css` is included, while `assets/src/reimu-search.css`, `assets/dist/manifest.json`, CSS split tools, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should move from low-risk CSS assets to the remaining maintenance hotspot: audit comments/profile CSS and runtime contracts before any `reimu-comments.css` or comments/profile runtime split.

## 2026-06-04 Comments/Profile Static Contract Gate

Goal: turn the existing comments/profile runtime contract into an automated quality gate before any CSS or runtime movement in the auth/comment/profile surface.

### Phases

1. Re-audit comments/profile AJAX actions, nonce names, config keys, DOM selectors, source modules, and CSS anchors - complete
2. Add `tools/check-comments-profile-contract.mjs` and wire it into `npm run check` - complete
3. Update development docs, release notes, and planning records - complete
4. Run verification, package checks, and record the next safe comments/profile step - complete

### Decisions

- Do not move comments/profile runtime code in this pass.
- Protect the existing `assets/src/reimu.js` source-module boundaries while keeping all request handlers in the main classic `assets/dist/reimu.js` runtime.
- Treat `yneko_reimu_profile_avatar_upload` as a preserved PHP compatibility endpoint, while the current front-end save path uploads `avatar_file` through `yneko_reimu_profile_save`.
- Add `npm run check:comments-profile` so future changes fail if they remove key AJAX actions, nonce creation/verification, `REIMU_CONFIG` keys, request payload fields, DOM selector anchors, source module boundaries, or CSS selectors.
- Verification passed: `npm run check:comments-profile`, `npm run check:js`, `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Final package check used `Yneko-Reimu-v0.2.1-20260604-1723.zip` and reported 143 entries with no forbidden development files. ZIP spot check confirmed runtime CSS/readme/docs files are included while `tools/check-comments-profile-contract.mjs`, `assets/src`, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should use this gate to split comments/profile CSS only if the boundary can stay stylesheet-only; otherwise move to documentation finalization and release checklist cleanup.

## 2026-06-04 Comments/Profile CSS Runtime Split

Goal: move comments/profile/auth modal styles out of the main `reimu.css` bundle while keeping all comments/profile AJAX handlers and front-end runtime logic in the main classic script.

### Phases

1. Confirm comments/profile CSS boundaries and global modal rendering constraints - complete
2. Move stylesheet-only comments/profile rules into `assets/src/reimu-comments.css` - complete
3. Build `assets/dist/reimu-comments.css`, enqueue it globally, and update size/contract gates - complete
4. Run verification, package checks, and record the final completion-audit target - complete

### Decisions

- Keep comments/profile JavaScript and PHP request handlers unchanged. This pass moves CSS only.
- Enqueue `yneko-reimu-comments` globally because `footer.php` can render the login/profile modal shell outside singular comment pages.
- Keep generic `.reimu-load-more` styles in the main stylesheet because they are also used by the projects virtual page.
- Keep mobile comment rules that live inside a mixed project/comment media query in the main stylesheet for now, avoiding a brittle split of an already shared responsive block.
- Generate `assets/dist/reimu-comments.css` through `tools/build-reimu.mjs` and record `assets/src/reimu-comments.css` in the build manifest `cssSources`.
- Update `npm run check:size` so the main `assets/dist/reimu.css` budget drops from 198 KB to 150 KB, while `assets/dist/reimu-comments.css` has a 52 KB budget.
- Build result after the split: `assets/dist/reimu.css` is 142,448 bytes / 139.1 KB against the 150 KB budget, and `assets/dist/reimu-comments.css` is 48,592 bytes / 47.5 KB against the 52 KB budget.
- Verification passed: `npm run check`, `npm audit --audit-level=moderate`, full PHP syntax lint over 73 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Package check used `Yneko-Reimu-v0.2.1-20260604-1738.zip` and reported 144 entries with no forbidden development files. ZIP spot check confirmed `assets/dist/reimu-comments.css`, `assets/dist/reimu.css`, runtime `readme.txt`, and `docs/development.md` are included, while `assets/src/reimu-comments.css`, CSS/check tools, `assets/dist/manifest.json`, `PROJECT.md`, and `AGENTS.md` are absent.
- Next round should complete a plan-closure audit against the original “Yneko-Reimu 当前不足审查与优化计划” and decide whether any remaining items require code changes or only documented follow-up work.

## 2026-06-04 Comments/Profile Modal Renderer Split

Goal: finish the last low-risk maintainability pass by moving request-free login/profile modal rendering out of the large comments entrypoint while preserving every public comment/profile contract.

### Phases

1. Inspect the comments modal renderer split and contract coverage - complete
2. Add the new internal modal module to the comments/profile contract gate - complete
3. Update development docs, release notes, and planning records - complete
4. Run full verification, package checks, and close the original audit plan - complete

### Decisions

- Keep `inc/comments.php` as the comments/profile PHP entrypoint.
- Move only `yneko_reimu_login_modal()`, `yneko_reimu_login_modal_html()`, `yneko_reimu_profile_modal()`, and `yneko_reimu_profile_modal_html()` into internal `inc/comments/modals.php`.
- Preserve function names, modal IDs, classes, data attributes, form field names, hooks, AJAX actions, nonces, and front-end runtime behavior.
- Update `npm run check:comments-profile` so it reads `inc/comments/modals.php` before checking DOM selector contracts.
- Targeted checks passed: `php -l` for `inc/comments.php` and `inc/comments/modals.php`, `npm run check:comments-profile`, `npm run check:release-readiness`, and `npm run report:php-complexity`.
- Complexity report after the split scans 74 PHP files. `inc/comments.php` is reduced to 2764 nonblank lines, while modal rendering is isolated in `inc/comments/modals.php`.
- Final verification passed: `npm run check`, `npm audit --audit-level=moderate`, full `php -l` over 74 theme PHP files, `npm run package`, `npm run check:package`, ZIP spot check, and `git diff --check`.
- Final validation ZIP is `Yneko-Reimu-v0.2.1-20260604-1751.zip` with 145 entries and no forbidden development files. It includes `inc/comments/modals.php`, `assets/dist/reimu-comments.css`, runtime `readme.txt`, and `screenshot.png`, while excluding source CSS, build manifest, tools, `PROJECT.md`, and `AGENTS.md`.
- The original “Yneko-Reimu 当前不足审查与优化计划” is complete for the GitHub Release professional theme target. Remaining high-risk comments/profile request-handler decomposition and a possible future WordPress.org plugin-boundary migration are documented future work, not required to close this plan.
