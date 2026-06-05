# Maintenance History Summary

This summary replaces the long root planning logs that were previously tracked in Git. It keeps the parts that are useful for public contributors without preserving every local execution note.

## Major Milestones

- v0.1.12 hardened SEO compatibility, account-enumeration handling, search-index privacy, image attributes, APlayer loading, accessibility basics, and release packaging hygiene.
- v0.1.13 added Customizer-managed article sharing and sidebar social links, virtual-page share output, profile/avatar review feedback, selectable custom tags, and admin review badges.
- v0.1.15 introduced local development guardrails, asset size/package checks, admin JavaScript source ownership, and the first front-end/PHP module boundaries.
- v0.2.0 promoted the architecture work into a larger quality-gate milestone with full PHP lint coverage, release-readiness checks, runtime `readme.txt`, package boundary checks, and expanded QA documentation.
- v0.2.1 through v0.2.3 split front-end runtime/style assets, Customizer/enqueue/template-tags/comments/GitHub OAuth internals, and added contract gates for high-risk public surfaces.
- v0.2.4 focused on comment interaction and WordPress login-page visual fixes, including a theme confirmation dialog and GitHub OAuth popup recovery.
- v0.2.5 added administrator toolbar compatibility, backend TOTP/recovery-code management, authentication email rate limiting, security alert logging, SVG upload controls, comment IP region lookup controls, and a configurable-surface audit.
- v0.2.6 tightened comments/profile/upload internals, split high-density settings panels, and added a PJAX/runtime contract gate.

## Established Guardrails

- The installable runtime theme remains `theme/Yneko-Reimu`.
- Public settings keys, AJAX actions, nonce names, request fields, post meta keys, template paths, virtual page slugs, hooks, filters, URLs, and classic script loading are compatibility-sensitive.
- Release packages are generated from a whitelist and must exclude source assets, tools, local-only files, planning files, dependency directories, and `assets/dist/manifest.json`.
- `npm run check` is the broad local quality gate. It includes JavaScript syntax checks, contract checks, release readiness, CSS split checks, build, asset hygiene, i18n message coverage, size/classic-script checks, and PHPCS through Composer tooling.
- `npm run check:package` inspects the newest validation ZIP and confirms development-only files are absent.

## Known Environment Notes

- Composer may be unavailable in some local Windows environments; when it is unavailable, record the limitation and rely on CI for PHPCS/WPCS.
- Local validation ZIPs are generated under `releases/` and are intentionally ignored.
- Full browser/WordPress QA is still needed for changes that affect comments/profile/auth, uploads, settings UI, PJAX, OAuth, or email/TOTP flows.
