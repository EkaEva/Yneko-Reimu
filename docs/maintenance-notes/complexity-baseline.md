# PHP Complexity Baseline

Generated with `npm run report:php-complexity` during the v0.2.8 maintainability-closure release.

## Current Baseline

- Runtime PHP files scanned: 125
- Named functions scanned: 670
- Total lines: 17,587
- Nonblank lines: 15,453
- Approximate branch score: 5,768

## Largest Runtime Files

| Nonblank lines | Functions | Branch score | File |
| --- | --- | --- | --- |
| 783 | 57 | 282 | `inc/i18n.php` |
| 548 | 18 | 291 | `inc/comments/rendering.php` |
| 456 | 26 | 176 | `inc/comments/badges.php` |
| 431 | 19 | 107 | `inc/template-tags/content-tools.php` |
| 401 | 18 | 103 | `inc/comments/auth.php` |
| 378 | 19 | 106 | `inc/comments/mutations.php` |
| 378 | 4 | 13 | `inc/github-login/rendering.php` |
| 377 | 24 | 115 | `inc/template-tags/layout-content.php` |
| 375 | 29 | 168 | `inc/security-auth-mail.php` |
| 349 | 15 | 83 | `inc/comments/profile.php` |

## Largest Functions

| Lines | Branch score | Location | Function |
| --- | --- | --- | --- |
| 346 | 4 | `inc/github-login/rendering.php:42` | `yneko_reimu_github_login_enqueue_styles` |
| 184 | 3 | `inc/settings/schema/defaults.php:6` | `yneko_reimu_settings_defaults` |
| 157 | 10 | `inc/customizer/social.php:6` | `yneko_reimu_register_customizer_social_section` |
| 153 | 84 | `inc/migrations.php:207` | `yneko_reimu_migrate_unified_settings` |
| 149 | 219 | `inc/settings/page.php:6` | `yneko_reimu_render_settings_page` |
| 146 | 4 | `inc/customizer/preset.php:6` | `yneko_reimu_register_customizer_preset_section` |
| 139 | 138 | `inc/comments/modals.php:119` | `yneko_reimu_profile_modal_html` |
| 137 | 164 | `inc/settings/schema/sanitizers.php:171` | `yneko_reimu_sanitize_settings` |

## Governance Policy

- This is a non-failing baseline, not a hard threshold.
- Do not combine broad complexity refactors with feature work or security fixes.
- When touching a hotspot, prefer a focused internal split that preserves function names, hooks, settings keys, AJAX actions, markup anchors, and package boundaries.
- Re-run `npm run report:php-complexity` after hotspot work and mention meaningful movement in the PR.

## Recommended Follow-Up Targets

- Continue reducing comments/profile JavaScript only when the change is backed by existing contract checks and manual WordPress QA.
- Consider a focused future pass for long Customizer section functions only if it preserves setting/control IDs and registration order.
- Consider moving long inline OAuth/login styles into a maintained source asset if the release can preserve output behavior and login-page compatibility.
