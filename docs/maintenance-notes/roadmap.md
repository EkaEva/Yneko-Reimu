# Maintenance Roadmap

This roadmap is non-binding guidance for future releases. It captures follow-up areas identified during earlier optimization and stability work.

## Priority Areas

- Expand runtime testing beyond static contract checks. Start with fast smoke coverage for classic script parsing, PJAX config replay, modal anchors, lazy runtime globals, and comment/profile DOM anchors.
- Continue reducing PHP complexity without changing public contracts. Current recurring hotspots include settings page rendering, settings schema sanitization, Customizer social controls, comments modals, comment rendering, and upload helpers.
- Use `complexity-baseline.md` as the current non-failing PHP complexity reference. New work should avoid increasing the largest hotspots unless a compatibility or security reason is documented.
- Keep comments/profile/auth runtime movement conservative until automated and manual QA cover login, registration, lost password, profile save, TOTP, upload review, comment mutation, and review-status polling paths.
- Improve cross-platform contributor tooling so packaging and validation commands behave consistently on Windows, Linux, and macOS.
- Keep dependency updates isolated. Security updates should land first; major npm, Composer, or GitHub Actions upgrades should be reviewed separately from feature work.

## Release Discipline

- Break broad maintenance releases into small pushed stages.
- After each stage, run the relevant checks, create a local validation package, run `npm run check:package`, commit, and push before continuing.
- Do not tag a release until final validation passes.
- Keep generated release ZIPs out of Git; upload release artifacts through GitHub Releases.

## Public Interface Discipline

- New settings need defaults, sanitizers, UI ownership, migration decisions, and front-end loading notes.
- Public hooks should be documented in `docs/hooks.md`; undocumented internal hooks may be refactored when covered by contract checks.
- If a shipped setting, endpoint, hook, template path, virtual slug, or URL must change, document the compatibility or migration path in the same release.
