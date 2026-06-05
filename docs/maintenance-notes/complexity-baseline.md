# PHP Complexity Baseline

Generated with `npm run report:php-complexity` during the v0.2.11 deep-maintenance release.

## Current Baseline

- Runtime PHP files scanned: 147
- Named functions scanned: 690
- Total lines: 17,838
- Nonblank lines: 15,649
- Approximate branch score: 5,802

## Largest Runtime Files

| Nonblank lines | Functions | Branch score | File |
| --- | --- | --- | --- |
| 431 | 19 | 107 | `inc/template-tags/content-tools.php` |
| 401 | 18 | 103 | `inc/comments/auth.php` |
| 382 | 8 | 11 | `inc/github-login/styles.php` |
| 378 | 19 | 106 | `inc/comments/mutations.php` |
| 377 | 24 | 115 | `inc/template-tags/layout-content.php` |
| 375 | 29 | 168 | `inc/security-auth-mail.php` |
| 349 | 15 | 83 | `inc/comments/profile.php` |
| 346 | 11 | 380 | `inc/settings/panels.php` |
| 344 | 22 | 110 | `inc/settings/admin.php` |
| 343 | 10 | 149 | `inc/post-meta.php` |

## Largest Functions

| Lines | Branch score | Location | Function |
| --- | --- | --- | --- |
| 184 | 3 | `inc/settings/schema/defaults.php:6` | `yneko_reimu_settings_defaults` |
| 157 | 10 | `inc/customizer/social.php:6` | `yneko_reimu_register_customizer_social_section` |
| 153 | 84 | `inc/migrations.php:207` | `yneko_reimu_migrate_unified_settings` |
| 146 | 4 | `inc/customizer/preset.php:6` | `yneko_reimu_register_customizer_preset_section` |
| 139 | 138 | `inc/comments/modals.php:119` | `yneko_reimu_profile_modal_html` |
| 103 | 53 | `inc/comments/rendering/external.php:66` | `yneko_reimu_render_external_comment_panel` |
| 97 | 84 | `inc/comments/modals.php:14` | `yneko_reimu_login_modal_html` |
| 96 | 3 | `inc/customizer/visual.php:6` | `yneko_reimu_register_customizer_visual_section` |
| 95 | 13 | `inc/template-tags/content-tools.php:6` | `yneko_reimu_home_category_capsules` |
| 94 | 28 | `inc/comments/uploads/admin.php:6` | `yneko_reimu_admin_comment_gif_upload_action` |

## Meaningful Movement Since v0.2.8

- `inc/i18n.php`, `inc/comments/rendering.php`, and `inc/comments/badges.php` no longer appear in the largest-file list after focused internal splits.
- `yneko_reimu_render_settings_page()`, `yneko_reimu_sanitize_settings()`, and `yneko_reimu_github_login_enqueue_styles()` no longer appear in the largest-function list.
- Remaining hotspots are mostly renderers or broad integration modules; future work should continue preserving public settings keys, hooks, AJAX contracts, markup anchors, and classic script behavior.

## Governance Policy

- This is a non-failing baseline, not a hard threshold.
- Do not combine broad complexity refactors with feature work or security fixes.
- When touching a hotspot, prefer a focused internal split that preserves function names, hooks, settings keys, AJAX actions, markup anchors, and package boundaries.
- Re-run `npm run report:php-complexity` after hotspot work and mention meaningful movement in the PR.

## Recommended Follow-Up Targets

- Continue reducing render-only settings panels only when field names, defaults, and save behavior remain stable.
- Consider a focused future pass for long Customizer section functions only if it preserves setting/control IDs and registration order.
- Keep comments/profile request handlers behind static contracts and manual WordPress QA before further runtime or PHP request-flow splits.
