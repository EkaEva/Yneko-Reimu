# PHP Complexity Baseline

Generated with `npm run report:php-complexity` during the v0.2.13 complexity-hotspot cleanup release.

## Current Baseline

- Runtime PHP files scanned: 152
- Named functions scanned: 812
- Total lines: 19,160
- Nonblank lines: 16,794
- Approximate branch score: 6,133

## Largest Runtime Files

| Nonblank lines | Functions | Branch score | File |
| --- | --- | --- | --- |
| 401 | 18 | 103 | `inc/comments/auth.php` |
| 400 | 11 | 14 | `inc/github-login/styles.php` |
| 378 | 19 | 106 | `inc/comments/mutations.php` |
| 377 | 24 | 115 | `inc/template-tags/layout-content.php` |
| 375 | 29 | 168 | `inc/security-auth-mail.php` |
| 368 | 23 | 149 | `inc/migrations.php` |
| 368 | 12 | 328 | `inc/settings/renderers.php` |
| 349 | 15 | 83 | `inc/comments/profile.php` |
| 346 | 11 | 380 | `inc/settings/panels.php` |
| 346 | 18 | 94 | `inc/template-tags/content-tools.php` |

## Largest Functions

| Lines | Branch score | Location | Function |
| --- | --- | --- | --- |
| 184 | 3 | `inc/settings/schema/defaults.php:6` | `yneko_reimu_settings_defaults` |
| 103 | 53 | `inc/comments/rendering/external.php:66` | `yneko_reimu_render_external_comment_panel` |
| 96 | 3 | `inc/customizer/visual.php:6` | `yneko_reimu_register_customizer_visual_section` |
| 94 | 28 | `inc/comments/uploads/admin.php:6` | `yneko_reimu_admin_comment_gif_upload_action` |
| 92 | 13 | `inc/comments/profile.php:296` | `yneko_reimu_ajax_profile_save` |
| 90 | 57 | `inc/post-meta.php:122` | `yneko_reimu_render_post_options_meta_box` |
| 86 | 3 | `inc/customizer/sidebar-widgets.php:6` | `yneko_reimu_register_customizer_sidebar_widgets_section` |
| 86 | 1 | `inc/customizer/footer-virtual.php:6` | `yneko_reimu_register_customizer_footer_virtual_sections` |
| 84 | 83 | `inc/settings/renderers.php:304` | `yneko_reimu_render_user_badge_admin` |
| 82 | 25 | `inc/post-meta.php:269` | `yneko_reimu_save_post_options` |

## Meaningful Movement Since v0.2.12

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
- Consider focused future passes for `inc/comments/auth.php`, `inc/comments/mutations.php`, `inc/template-tags/layout-content.php`, and `inc/security-auth-mail.php` only when preserving AJAX, auth, comment, and display contracts is straightforward.
- Settings defaults remain intentionally broad; split them only with a migration-aware plan that preserves default values, sanitizer expectations, and fallback readers.
