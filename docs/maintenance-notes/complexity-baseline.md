# PHP Complexity Baseline

Generated with `npm run report:php-complexity` during the v0.2.18 theme-updater observability release prep.

## Current Baseline

- Runtime PHP files scanned: 214
- Named functions scanned: 1052
- Total lines: 20,928
- Nonblank lines: 18,251
- Approximate branch score: 6,671

## Largest Runtime Files

| Nonblank lines | Functions | Branch score | File |
| --- | --- | --- | --- |
| 400 | 11 | 14 | `inc/github-login/styles.php` |
| 368 | 23 | 149 | `inc/migrations.php` |
| 320 | 19 | 295 | `inc/comments/modals.php` |
| 319 | 19 | 140 | `inc/comments/avatars.php` |
| 318 | 25 | 99 | `inc/theme-updater.php` |
| 296 | 13 | 128 | `inc/comments/profile-save.php` |
| 263 | 14 | 119 | `inc/enqueue/head.php` |
| 258 | 16 | 86 | `inc/i18n/requests.php` |
| 256 | 21 | 303 | `inc/settings/page/general.php` |
| 236 | 16 | 75 | `inc/enqueue/favicon.php` |

## Largest Functions

| Lines | Branch score | Location | Function |
| --- | --- | --- | --- |
| 84 | 13 | `inc/comments/profile/save.php:6` | `yneko_reimu_ajax_profile_save` |
| 82 | 15 | `inc/comments/auth/login.php:6` | `yneko_reimu_ajax_login` |
| 81 | 1 | `inc/enqueue/config.php:43` | `yneko_reimu_frontend_i18n` |
| 81 | 1 | `inc/github-login/styles.php:236` | `yneko_reimu_github_login_password_css` |
| 80 | 24 | `inc/comments/profile-save.php:74` | `yneko_reimu_profile_save_prepare_tags` |
| 77 | 2 | `inc/customizer/articles.php:6` | `yneko_reimu_register_customizer_articles_section` |
| 75 | 27 | `inc/enqueue/config.php:125` | `yneko_reimu_build_frontend_config` |
| 74 | 12 | `inc/theme-updater.php:122` | `yneko_reimu_theme_updater_fetch_status` |
| 74 | 9 | `inc/schema.php:14` | `yneko_reimu_schema_graph` |
| 70 | 13 | `inc/enqueue/head.php:110` | `yneko_reimu_typography_layout_css` |

## v0.2.18 QA Baseline Note

- v0.2.18 adds admin-only updater observability. The baseline increase is concentrated in `inc/theme-updater.php` and `inc/settings/page/general.php`, where structured update status, failure reasons, cache timing, and admin-only check/clear actions now live.
- The update installer path remains WordPress-native through `site_transient_update_themes`; no front-end runtime surface or public theme update package naming rule changes.

## v0.2.17 QA Baseline Note

- v0.2.17 does not intentionally refactor runtime PHP. The slight baseline increase since v0.2.14 comes from later released features such as the GitHub Release updater, local E2E QA tooling, and block-editor writing support.
- The quality-hardening work expands local Playwright coverage for the Yneko-Reimu settings page and block editor contracts while preserving the current PHP hotspot profile.

## Meaningful Movement Since v0.2.13

- Comments/auth was split behind `inc/comments/auth.php` into login-state, login, registration, lost-password, session, and shared helper modules. The entrypoint preserves existing AJAX actions, nonce checks, payload fields, response shapes, and GitHub OAuth adjacency while dropping out of the largest-file list.
- Comments/profile was split behind `inc/comments/profile.php` into TOTP, payload, status, email, avatar, and save modules. The profile save callback now lives in `inc/comments/profile/save.php`, while profile modal, user meta keys, temporary response-only statuses, and review-status behavior remain compatible.
- Comments/mutations was split behind `inc/comments/mutations.php` into visibility, likes, owner management, submit, and review-status modules. Existing submit/edit/delete/like actions, comment form anchors, owner checks, and review-state synchronization remain intact.
- Comments upload front-end orchestration moved into `assets/src/reimu/comment-upload.js`, keeping the lazy classic `assets/dist/reimu-comments.js` output and `window.ReimuCommentsRuntime` contract unchanged.
- Comments rendering Stage 1 moved comment item context/render helpers, external comment panel renderers, and administrator GIF/upload action helpers behind the existing entrypoints. `yneko_reimu_comment_callback()`, `yneko_reimu_render_external_comment_panel()`, and `yneko_reimu_admin_comment_gif_upload_action()` no longer appear in the largest-function or highest-branch-score lists.
- Settings Stage 2 moved settings admin menu/UI/TOTP/review-count/asset helpers, panel bodies, repeatable renderers, user avatar/badge cards, and comment upload review cards behind focused modules. `inc/settings/renderers.php`, `inc/settings/panels.php`, and `inc/settings/admin.php` no longer appear in the largest-file list; the locked settings renderer and panel hotspots no longer appear in the largest-function or highest-branch-score top ten.
- Security auth mail Stage 3 moved auth-security defaults/sanitizers, request context/device cookie, transient counters, event logging/email alerts, admin alert actions, and native `wp-login.php` filters behind focused modules. `inc/security-auth-mail.php` no longer appears in the largest-file list, and its guard functions no longer appear in the largest-function or highest-branch-score top ten while preserving `auth_security` settings, cookie, transient/option naming, hooks, and generic error boundaries.
- Post meta and template-tags Stage 4 moved the editor meta box/save flow, layout/content helpers, navigation/virtual-page helpers, and content-tool helpers behind focused modules. `inc/post-meta.php`, `inc/template-tags/layout-content.php`, `inc/template-tags/navigation-virtual.php`, and `inc/template-tags/content-tools.php` no longer appear in the largest-file list, and the locked post-meta render/save functions no longer appear in the largest-function or highest-branch-score top ten while preserving post meta keys, virtual slugs, hooks, shortcodes, template output contracts, and transient names.
- Customizer/defaults Stage 5 moved settings defaults into focused defaults modules and split remaining long Visual, Sidebar Widgets, and Footer/Virtual Customizer registration functions into same-file helpers. `inc/settings/schema/defaults.php` no longer appears in the largest-file list, and `yneko_reimu_settings_defaults()`, `yneko_reimu_register_customizer_visual_section()`, `yneko_reimu_register_customizer_sidebar_widgets_section()`, and `yneko_reimu_register_customizer_footer_virtual_sections()` no longer appear in the largest-function top ten while preserving default values, theme_mod keys, section/control IDs, sanitizers, and registration order.
- PJAX replacement is now expressed as explicit capture, detach, replace, sync, restore, and verification steps. The public `window.ReimuWP.init()` and `window.ReimuWP.navigate()` behavior, link exclusions, script replay, APlayer preservation, modal restoration, and lazy runtime rebinding remain stable.
- The General settings panel was split into focused helper groups behind the existing renderer entrypoint, removing it from the highest branch-score hotspot list while preserving field names, option keys, tabs, and save behavior.
- Contract checks now aggregate the new comments auth/profile/mutation modules, comment upload runtime source, and PJAX lifecycle anchors so internal moves keep the high-risk comments/profile/PJAX surface guarded.

## Earlier Movement Since v0.2.12

- Settings/admin renderer hotspots were split behind existing renderer entrypoints. Security, Users, Music, and comment upload review renderers no longer dominate the branch-score list, while tab IDs, field names, option keys, nonce URLs, TOTP controls, review actions, and the single settings save model remain intact.
- Login/profile modal renderers were split behind `yneko_reimu_login_modal_html()` and `yneko_reimu_profile_modal_html()`. Both entrypoints left the largest-function and highest-branch lists while modal IDs/classes, form fields, data attributes, ARIA anchors, and social-login hook placement stayed compatible.
- Comments/profile front-end source modules and PJAX/main runtime helpers were split without changing the public classic runtimes, `window.REIMU_CONFIG`, `window.ReimuWP`, `window.ReimuCommentsRuntime`, AJAX payloads, nonce refresh behavior, PJAX exclusions, or lazy runtime loading contracts.
- `yneko_reimu_migrate_unified_settings()` no longer appears in the largest-function list after moving legacy setting groups into focused migration helpers.
- Long Customizer functions for Social, Preset, Visual Assets, and Typography/Layout no longer appear in the largest-function list after same-module helper splits that preserve section/control IDs, `theme_mod` keys, sanitizer callbacks, defaults, and registration order.
- `inc/template-tags/content-tools.php` dropped from 431 to 346 nonblank lines after moving home category capsule defaults and compatibility fallbacks into `inc/template-tags/content-tools/home-categories.php`.
- `yneko_reimu_home_category_capsules()` and `yneko_reimu_github_login_layout_css()` no longer appear in the largest-function list after lower-risk display/style helper splits.

## Governance Policy

- This is a non-failing baseline, not a hard threshold.
- Do not combine broad complexity refactors with feature work or security fixes.
- When touching a hotspot, prefer a focused internal split that preserves function names, hooks, settings keys, AJAX actions, markup anchors, and package boundaries.
- Re-run `npm run report:php-complexity` after hotspot work and mention meaningful movement in release notes or PR context.

## Recommended Follow-Up Targets

- Keep high-risk comments/auth/profile/upload request handlers behind static contracts and manual WordPress QA before any further request-flow split.
- Consider focused future passes for enqueue display helpers only when preserving setting IDs, default values, sanitizer expectations, and front-end output contracts is straightforward.
- Settings defaults now have focused helper modules; future changes should keep defaults, sanitizer expectations, and fallback readers synchronized.
