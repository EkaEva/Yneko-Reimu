# Contributing to Yneko-Reimu

Thanks for helping make Yneko-Reimu easier to maintain. This repository ships a WordPress Classic Hybrid theme from `theme/Yneko-Reimu`; repository tooling, docs, release archives, and local agent notes stay outside that runtime theme.

## Setup

- Use Node.js 24 and install JavaScript dependencies with `npm ci`.
- Use PHP 8.0 or later for local syntax checks.
- Use Composer for PHPCS/WPCS: `composer install`, then `npm run lint:php`.
- Upload or test the generated theme ZIP, not the GitHub source archive.

## Branches And Scope

- Use focused branches. Maintainers use the `codex/` prefix for agent work.
- Keep PRs scoped to one subsystem or one release stage.
- Do not perform unrelated refactors, formatting sweeps, or metadata churn.
- Do not commit local-only files such as `PROJECT.md`, `AGENTS.md`, local planning files, `node_modules`, `vendor`, `wp-local`, or `releases`.

## Public Interface Guardrails

Before changing any setting, AJAX handler, template, post meta key, hook, shortcode, rewrite/query var, or public URL, verify whether it is documented or already shipped.

Do not rename or remove these without a compatibility plan:

- Saved settings and `theme_mod` keys.
- Post meta keys.
- AJAX action names, nonce names, and request fields.
- Public filters/actions documented in `docs/hooks.md`.
- Template paths, virtual page slugs, shortcodes, and public URLs.
- Classic front-end script compatibility for WordPress script enqueueing.

New settings need a default value, sanitizer, UI location, migration decision, and a note about front-end loading impact.

## High-Risk Areas

Treat these areas as security-sensitive and regression-prone:

- Comment submit, edit, delete, sorting, upload, and review flows.
- Avatar, GIF, image upload, and admin review flows.
- Login, registration, password reset, profile save, TOTP, recovery codes, and GitHub OAuth callback flows.
- PJAX, lazy runtime loading, and front-end config replay.
- Package, release, CI, and dependency scripts.

## Required Local Checks

Run the smallest relevant check while developing, then broader checks before handoff:

```bash
npm run check:js
npm run build
npm run check:size
npm audit --audit-level=moderate
npm run check
npm run package
npm run check:package
```

When Composer is available:

```bash
composer install
npm run lint:php
```

When PHP is available and PHP files changed, run a full syntax pass over `theme/Yneko-Reimu/**/*.php`.

## Pull Request Checklist

Every PR should describe:

- What changed and why.
- Whether any public interface changed.
- Which checks were run locally.
- Manual QA or screenshots for UI, auth, upload, comment, or PJAX changes.
- Whether the release ZIP boundary changed.

If a check cannot run locally, explain why in the PR.

## Dependency Updates

- Security updates have priority.
- Major npm, Composer, or GitHub Actions upgrades should be separate PRs.
- Do not run forced audit fixes or broad dependency updates without explaining the risk and compatibility impact.
- Keep `npm audit --audit-level=moderate` passing.
