# Local E2E QA

Yneko-Reimu has static contract checks for public WordPress surfaces and a local Playwright suite for browser-level smoke coverage. The E2E suite is intentionally local-only for now; it is not part of the required GitHub Actions quality gate.

## Requirements

- Node.js 24 with dependencies installed by `npm ci`.
- Docker Desktop running before starting `wp-env`.
- Playwright browser binaries installed with `npx playwright install chromium` if they are not already present.

## Commands

```bash
npm run wp-env -- start --update
npm run qa:e2e:start
npm run qa:e2e:seed
npm run qa:e2e
npm run qa:e2e:stop
```

Use `npm run qa:e2e:start` when normal `wp-env start --update` fails on Windows while building Composer/PHPUnit images. Use `npm run qa:e2e:headed` when you need to watch the browser. The default WordPress URL is `http://localhost:8888`; override it with `YNEKO_E2E_BASE_URL` when connecting Playwright to another compatible local site.

## Seeded Site

`npm run qa:e2e:seed` activates the local theme, enables pretty permalinks, opens comments, creates fixed administrator and subscriber accounts, creates `Reimu E2E Post`, and enables the smallest settings needed for comments/profile/upload/PJAX/admin/editor checks.

Seeded administrator:

- Login: `admin`
- Password: `password`

Seeded subscriber:

- Email: `reimu-user@example.test`
- Password: `password`

Important seeded paths:

- `/reimu-e2e-post/`
- `/reimu-e2e-page/`
- `/projects/`

## Covered Scenarios

- Homepage, single post, search, 404, and virtual projects page load without browser runtime errors.
- Comment login modal accepts the seeded user and refreshes the front-end identity area.
- Profile modal saves ordinary profile fields and refreshes the front-end identity area.
- The inactive TOTP setup panel remains hidden until setup is intentionally requested.
- Comment upload controls render when uploads are enabled.
- AJAX comment submission inserts the comment without a full page reload.
- PJAX navigation keeps the lazy comments runtime available after moving between pages.
- The Yneko-Reimu settings page loads its admin script, tab panels, and administrator TOTP controls.
- The block editor loads `assets/dist/reimu-editor.css` and exposes the Reimu pattern/style registrations added in v0.2.16.

## Manual QA Still Required

Keep using the manual QA matrix in `docs/comments-profile-contract.md` for changes that touch request handlers, upload review, real media promotion, email-code delivery, TOTP verification, GitHub OAuth, administrator review actions, release packaging, or the short-lived profile "updated" inline status. The local E2E suite is a regression net, not a replacement for those checks.

## Troubleshooting

- If `wp-env` cannot start, confirm Docker Desktop is running and no other service is using ports 8888 or 8889.
- If the Docker build fails while installing Composer in a `wp-env` CLI image, retry after confirming `https://getcomposer.org/installer` and `https://composer.github.io/installer.sig` are reachable from Docker; a failed signature fetch can remove `/tmp/composer-setup.php` before the install step.
- If the Docker build fails with `/.composer does not exist and could not be created` on Windows, run `npm run qa:e2e:start`. The wrapper applies a generated-cache workaround for numeric Windows usernames before starting the containers.
- If the seed command fails, run `npm run wp-env -- start --update` again and retry `npm run qa:e2e:seed`.
- If Playwright reports missing browsers, run `npx playwright install chromium`.
- If tests fail after a UI or contract change, inspect `test-results/` for traces, screenshots, and videos.
