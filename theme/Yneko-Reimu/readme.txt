=== Yneko-Reimu ===
Contributors: EkaEva
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 0.2.8
License: MIT
License URI: https://github.com/EkaEva/Yneko-Reimu/blob/main/LICENSE

Yneko-Reimu is a WordPress Classic Hybrid theme inspired by hexo-theme-reimu and distributed through GitHub Releases.

== Description ==

Yneko-Reimu ports the Reimu-style blog layout, cards, sidebar, archives, search, comments, login modal, optional music player, optional visual effects, GitHub project display, and bilingual Chinese/English content helpers into a WordPress-native Classic Hybrid theme.

The installable theme is intended for GitHub Release distribution, not the WordPress.org theme directory. Optional integrations such as GitHub OAuth, third-party comments, statistics, music, search indexing, visual effects, KaTeX, Mermaid, PhotoSwipe, and related enhancements are controlled by theme settings and should remain disabled or conditionally loaded unless configured.

== Installation ==

1. Download the release ZIP named like `Yneko-Reimu-vX.Y.Z-YYYYMMDD-HHMM.zip`.
2. In WordPress admin, open Appearance > Themes > Add New > Upload Theme.
3. Upload the ZIP, install, and activate Yneko-Reimu.
4. Configure data/service settings in Appearance > Yneko-Reimu Settings.
5. Configure visual preview settings in Appearance > Customize > Yneko-Reimu Visual Preview.

== Privacy And Remote Resources ==

Fresh installs default to a lighter front end. Heavy or third-party features such as Google Analytics, Cloudflare RUM, Giscus, Live2D, APlayer/Meting, KaTeX, Mermaid, PhotoSwipe, mouse effects, and custom cursor behavior are disabled by default or loaded only when their settings and page context require them.

GitHub OAuth uses the GitHub OAuth and API endpoints only when the site owner configures OAuth credentials and a visitor chooses GitHub login or account binding. Search indexes, comment uploads, avatar uploads, email verification, and TOTP flows store data in WordPress using the theme's documented settings and review controls.

== Credits And Licensing ==

Yneko-Reimu theme code is released under the MIT License. WordPress itself is GPL-licensed; redistributors should follow WordPress ecosystem and dependency license requirements when packaging a complete site or derivative distribution.

The visual language and interaction ideas are adapted from D-Sketon's hexo-theme-reimu, also MIT licensed. Cursor artwork is credited to Tianyang EdSky and remains owned by its original creator; verify the original creator's permission before redistributing, commercializing, or replacing those cursor assets. See NOTICE.md for full credits.

== Development And Release Checks ==

Development sources live outside the runtime theme package. Before release, run:

`npm run check`
`npm audit --audit-level=moderate`
`npm run package`
`npm run check:package`

The release ZIP excludes source assets, build tools, local planning files, dependency directories, and `assets/dist/manifest.json`.

Theme images ship as files under `assets/images` or generated runtime files under `assets/dist`. Standalone SVG icons live under `assets/images/icons`; small UI SVG components may remain inline, but runtime PHP/CSS/JS must not contain base64 `data:image` assets.

== Changelog ==

= 0.2.8 =

* Completes the high-risk comments/profile/upload PHP module split while preserving AJAX actions, nonces, request fields, meta keys, JSON shapes, and classic front-end behavior.
* Splits settings schema, enqueue/resource helpers, and Customizer section registration into focused internal modules while preserving stored options, theme_mod keys, handles, and Customizer IDs.
* Updates contract gates so settings schema, enqueue, Customizer, config surface, comments/profile, and PJAX/runtime checks aggregate the new module boundaries.
* Documents the comments/profile front-end runtime split as deferred until a dedicated WordPress manual QA pass is available.

= 0.2.7 =

* Adds contributor onboarding, GitHub PR/issue templates, CODEOWNERS, SECURITY.md, and Dependabot configuration.
* Adds a PR/main quality workflow and aligns release CI with the full local quality gate, audit, package, and package-boundary checks.
* Moves public maintenance history into docs/maintenance-notes while keeping root agent planning files out of Git and release ZIPs.
* Adds cross-platform npm packaging through a Node wrapper around the existing PowerShell package script.
* Adds npm run test:runtime for fast smoke checks against built classic scripts and key PJAX/lazy-runtime/comments-profile anchors.
* Records a non-failing PHP complexity baseline for future maintainability work.

= 0.2.6 =

* Splits comment upload review/cleanup helpers and profile-save helper steps into focused internal modules while preserving AJAX actions, nonces, request fields, meta keys, upload paths, and JSON response shapes.
* Splits high-density settings panels into focused Users, Security, and Music panel modules while preserving settings tabs, option keys, form fields, nonces, and save behavior.
* Adds a PJAX/runtime stability contract check covering PJAX link exclusions, config replay, lazy search/share/PhotoSwipe runtimes, APlayer preservation, modal restoration, content enhancers, and comment rebind guards.
* Extends contract checks and development docs so future maintenance work keeps comments/profile, settings/admin, config-surface, auth-security, GitHub OAuth, and PJAX/runtime interfaces stable.

= 0.2.5 =

* Adds a disabled-by-default setting to restore the front-end WordPress admin toolbar for administrators when debugging is needed.
* Keeps the clean front-end default by hiding the WordPress admin toolbar and Rank Math front-end analytics/pro toolbar prompts while the setting is off.
* Adds configurable authentication email rate limits and security alerts for registration, lost-password, profile email verification, and native wp-login.php register/lost-password requests.
* Adds Security -> Media and privacy controls for administrator SVG uploads and comment IP region lookup, preserving existing defaults while allowing privacy-first sites to disable ipwho.is requests.
* Adds a configurable-surface audit gate so admin UI, Customizer, developer hooks, and internal compatibility values stay clearly classified.

= 0.2.4 =

* Replaced front-end comment media-replacement and comment-delete browser confirmations with the theme-styled confirmation dialog.
* Fixed the WordPress login password visibility button alignment and removed the unwanted button frame.

= 0.2.3 =
* Splits comments rendering helpers into an internal PHP module while preserving existing markup, nonces, callbacks, and external comment output.
* Extends the comments/profile contract gate to cover the new rendering module boundary.

= 0.2.2 =
* Fixes PJAX navigation into posts and virtual pages by keeping share/footer enhancement styles available before share markup is inserted.
* Keeps the share JavaScript runtime lazy-loaded on pages that actually render share markup.
* Splits comments/profile/auth AJAX handlers into internal PHP modules while preserving existing actions, nonces, payloads, and response shapes.

= 0.2.1 =
* Adds release-readiness hardening for PHP direct-access guards, runtime metadata, package checks, and the standard release screenshot.
* Keeps the GitHub Release distribution target while documenting privacy, remote resources, credits, and license expectations in the runtime theme package.
* Adds image/SVG resource hygiene checks so build outputs keep cacheable image files instead of base64 image payloads.

= 0.2.0 =
* Promotes the completed architecture, quality-gate, Email/TOTP, and real GitHub OAuth QA milestone.
* Adds stronger runtime splitting, PHP module boundaries, package hygiene, and high-impact i18n/OAuth quality gates.
