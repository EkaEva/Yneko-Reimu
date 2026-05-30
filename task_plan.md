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
