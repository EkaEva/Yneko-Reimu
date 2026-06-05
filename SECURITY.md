# Security Policy

Yneko-Reimu includes code paths for authentication, profile updates, comment uploads, GitHub OAuth, TOTP, and administrator review flows. Please report security issues privately so maintainers can verify and fix them before details are public.

## Supported Versions

Security fixes target the latest released version and the current `main` branch.

| Version | Supported |
| --- | --- |
| Latest GitHub Release | Yes |
| `main` | Yes, before the next release |
| Older releases | Best effort only |

## Reporting A Vulnerability

Use GitHub's private vulnerability reporting for this repository when available. If it is not available, contact the maintainer privately through the GitHub profile linked from the repository.

Do not open a public issue for suspected vulnerabilities in:

- Login, registration, password reset, TOTP, recovery-code, or GitHub OAuth flows.
- Comment submit, edit, delete, upload, review, or media cleanup flows.
- Avatar/GIF/image upload validation or administrator review flows.
- Capability checks, nonce checks, or ownership checks.
- Package/release scripts that could include local-only files or secrets.

Please include:

- Theme version or commit.
- WordPress and PHP versions.
- Relevant plugins, especially cache, SEO, auth, upload, CDN, or security plugins.
- Reproduction steps and expected impact.
- Logs, screenshots, or proof-of-concept details when safe to share privately.

## Response Expectations

Maintainers aim to acknowledge reports within 7 days. Valid issues will be triaged by severity, fixed on `main`, and released through a GitHub Release ZIP. Public disclosure should wait until a fixed release is available, unless coordinated otherwise.

## Security Checks

Contributors should keep these checks passing:

```bash
npm run check
npm audit --audit-level=moderate
npm run package
npm run check:package
```

When Composer is available, run:

```bash
composer install
npm run lint:php
```
