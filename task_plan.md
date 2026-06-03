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
