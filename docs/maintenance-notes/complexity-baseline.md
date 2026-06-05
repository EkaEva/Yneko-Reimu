# PHP Complexity Baseline

Generated with `npm run report:php-complexity` during the v0.2.7 maintainability release.

## Current Baseline

- Runtime PHP files scanned: 96
- Named functions scanned: 670
- Total lines: 17,409
- Nonblank lines: 15,308
- Approximate branch score: 5,768

## Largest Runtime Files

| Nonblank lines | Functions | Branch score | File |
| --- | --- | --- | --- |
| 989 | 54 | 410 | `inc/comments.php` |
| 901 | 45 | 376 | `inc/settings/schema.php` |
| 783 | 57 | 282 | `inc/i18n.php` |
| 734 | 11 | 31 | `inc/customizer.php` |
| 732 | 48 | 276 | `inc/comments/uploads.php` |
| 548 | 18 | 291 | `inc/comments/rendering.php` |
| 538 | 24 | 214 | `inc/enqueue.php` |
| 431 | 19 | 107 | `inc/template-tags/content-tools.php` |
| 401 | 18 | 103 | `inc/comments/auth.php` |
| 378 | 19 | 106 | `inc/comments/mutations.php` |

## Largest Functions

| Lines | Branch score | Location | Function |
| --- | --- | --- | --- |
| 346 | 4 | `inc/github-login/rendering.php:42` | `yneko_reimu_github_login_enqueue_styles` |
| 184 | 3 | `inc/settings/schema.php:6` | `yneko_reimu_settings_defaults` |
| 157 | 10 | `inc/customizer.php:542` | `yneko_reimu_register_customizer_social_section` |
| 153 | 84 | `inc/migrations.php:207` | `yneko_reimu_migrate_unified_settings` |
| 149 | 219 | `inc/settings/page.php:6` | `yneko_reimu_render_settings_page` |
| 139 | 138 | `inc/comments/modals.php:119` | `yneko_reimu_profile_modal_html` |
| 137 | 164 | `inc/settings/schema.php:508` | `yneko_reimu_sanitize_settings` |

## Governance Policy

- This is a non-failing baseline, not a hard threshold.
- Do not combine broad complexity refactors with feature work or security fixes.
- When touching a hotspot, prefer a focused internal split that preserves function names, hooks, settings keys, AJAX actions, markup anchors, and package boundaries.
- Re-run `npm run report:php-complexity` after hotspot work and mention meaningful movement in the PR.

## Recommended Follow-Up Targets

- Split settings schema defaults/sanitization into smaller internal data groups without changing the stored `yneko_reimu_settings` option.
- Continue reducing comments/profile files only when the change is backed by existing contract checks and manual QA.
- Move Customizer social-section registration into smaller helpers while preserving setting/control IDs.
- Consider moving long inline OAuth/login styles into a maintained source asset if the release can preserve output behavior and login-page compatibility.
