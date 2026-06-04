=== Yneko-Reimu ===
Contributors: EkaEva
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 0.2.4
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
