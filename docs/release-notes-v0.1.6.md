# Yneko-Reimu v0.1.6

## Highlights

- Fixed front-end i18n loading so English pages use the bundled English translations for footer, comments, tag cloud, TOC, and post navigation strings.
- Refreshed multilingual rewrite rules on version/config changes so English category and archive URLs resolve after uploading a new theme build.
- Reworked archive-like pages, including category, tag, and date archives, to use the year-grouped archive timeline instead of the home card layout.
- Improved desktop and mobile article sidebar behavior to better match upstream Reimu, including TOC/common-sidebar switching, internal scrolling, and player layout stability.
- Updated TOC activation to follow upstream behavior: only the active item and its parent chain remain expanded, click navigation locks scroll updates during the jump, and mobile TOC clicks close the drawer.
- Added numbered rows in the Yneko-Reimu settings page for friend links and music tracks. Music numbering matches the front-end APlayer list order.

## Fixes

- Fixed a ZIP packaging issue by normalizing archive entry paths for WordPress theme uploads.
- Fixed custom banner responsive sources so custom banner images are used consistently across breakpoints.
- Fixed mobile drawer clipping and duplicate-scrollbar regressions while keeping long TOC lists scrollable.
- Fixed the theme header metadata for WordPress installation compatibility.

## Notes

- No database migration is required.
- Existing friend-link and music settings keep their current data structure; numbering is display-only in the settings UI.
