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
4. Commit, push, and tag release - pending
