# Yneko-Reimu Optimization Findings

- Repo is clean at start.
- Current build is a lightweight Node copy/concat script.
- Current release package uses a whitelist and excludes development-only source directories.
- Earlier release ZIP checks showed large PNG fallbacks; these are now candidates for source slimming when WebP fallbacks exist.
- Frontend has no jQuery dependency on the public theme script; admin settings registers a small inline script with WordPress `jquery` dependency.
- Theme is currently Classic Hybrid: PHP templates plus `theme.json`, editor styles, and block support.

## 2026-06-02 Online Theme Audit Findings

- Live homepage `https://yneko.com/` loads successfully with title `Yneko - Yneko的博客`; no browser console warnings/errors were observed in the initial desktop check.
- Desktop and 390px mobile viewport do not show obvious horizontal overflow. Mobile viewport reports `scrollWidth` equal to `clientWidth`.
- Homepage head loads theme CSS plus Google Fonts, loader CSS, APlayer CSS/JS, mouse-firework JS, GA, WordPress emoji JS, Cloudflare beacon, and theme JS.
- Performance API resource entries were unavailable in the in-app browser page context, so asset sizing needs to be checked via page asset capability, HTTP requests, or source inspection instead.
- Images on the homepage are mostly WebP uploads. The hero and repeated category/post card images observed did not expose `loading="lazy"`; only search popup background had lazy loading.
- Several fixed layers are present in DOM on mobile even while hidden or inactive: loader, mobile nav/sidebar, search popup, login modal, mouse-firework canvas.
- Controls audit shows several empty-text buttons without `aria-label` in the collected top controls list; likely from search suggestions/login form/tab UI or icon-only buttons.
- Semantic baseline is decent on homepage: one `h1`, one `main`, six `article` elements, and two JSON-LD scripts were observed.
- Follow-up control audit identified the unnamed visible buttons as APlayer controls, not first-party theme templates.
- Page asset capability confirmed observed live assets include Google Fonts, Ali icon font, GA, Cloudflare RUM, mouse-firework, APlayer, `search.json`, an LRC file, and `audio.mp3`.
- Local theme default features keep preloader, custom cursor, and generated search enabled by default. APlayer/firework are disabled by default but enabled on the live site.
- `content-card.php` outputs card images with `class="lazyload"` but no native `loading`, `decoding`, `width`, or `height` attributes.
- `search-index.php` exposes up to 300 published posts with stripped full content in `/search.json`.
- Frontend AJAX login distinguishes missing user from wrong password; password reset code sender also reports missing users. This can leak account existence.

## 2026-06-02 Repair Findings

- Local cleanup verification shows no `yneko-wp-local` Docker containers, volumes, or networks remain, and `E:\GitProject\VS Code\Blog\wp-local` no longer exists.
- `releases` now only keeps `Yneko-Reimu-v0.1.12.zip`.
- Rank Math compatibility should own canonical/meta/schema on production; theme meta/schema now only output when no common SEO plugin is active.
- Rank Math sitemap hooks used: `rank_math/sitemap/xml_post_url`, `rank_math/sitemap/entry`, and `rank_math/sitemap/page_content`; the page sitemap content hook appends the `/en/` homepage entry instead of replacing existing content.
- Local Composer is still unavailable, so PHP lint/WPCS cannot be run in this environment.
- `npm install` completed with 2 moderate advisories; no forced audit fix was applied.

## 2026-06-02 Live v0.1.12 Verification

- Live `style.css` reports `Version: 0.1.12`; front-end assets load with `?ver=0.1.12...`.
- `/`, `/en/`, a Chinese post, and an English post each output one meta description and one JSON-LD script.
- `/en/` canonical is now `https://yneko.com/en/`, and English post canonical/OG URL point to `/en/blog/.../`.
- Hreflang is present on home and paired posts with `zh-CN`, `en`, and `x-default`.
- `/search.json` and `/en/search.json` no longer expose a `content` field by default.
- Homepage post-card images now have `loading="lazy"`, `decoding="async"`, `width`, and `height`; sidebar/logo/related-post images are separate templates and still lack full native dimensions.
- APlayer controls have accessible labels after initialization.
- Remaining SEO issue: `post-sitemap.xml` still lists English posts as `/blog/*-en/`, and `page-sitemap.xml` still lacks `/en/`.
- Remaining performance issue: with the player visible on initial load, the page still requests `audio.mp3` and `.lrc` before user interaction.

## 2026-06-02 Live v0.1.13 Verification

- Front-end assets load as `?ver=0.1.13...`, confirming the rebuilt JS/CSS assets are live.
- No-interaction homepage load no longer requests `audio.mp3` or `.lrc`; `#aplayer` stays deferred and no audio element is created before user input.
- Direct `style.css` still reports `Version: 0.1.12`, even though the front-end asset version is `0.1.13`; the running PHP constant appears updated, but the stylesheet header file on origin did not reflect the latest zip header.
- `post-sitemap.xml` still lists English posts as `/blog/*-en/`.
- `page-sitemap.xml` still lacks `https://yneko.com/en/`.
- Sitemap responses are `cf-cache-status: DYNAMIC` with no-store headers, so the stale sitemap is more likely Rank Math internal sitemap storage/cache or a sitemap generation hook path issue than Cloudflare edge cache.

## 2026-06-02 Version and Source Slimming Findings

- Public version fields were reset to `0.1.12` because the GitHub version line has not caught up.
- Local verification packages now use `Yneko-Reimu-v0.1.12-YYYYMMDD-HHMM.zip`.
- `vendor-src/reimu-upstream` was removed from the repository; builds continue from `theme/Yneko-Reimu/assets/src/yneko-reimu-base.css`.
- Large PNG fallbacks `assets/images/banner.png` and `assets/images/search-bg.png` were removed; WebP assets remain.
- No local `wp-local`, Docker Compose, or WordPress development-site files remain in the repository.
