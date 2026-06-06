# Release

Use GitHub Releases as the primary distribution channel.

## Local Package

```bash
npm run package
```

The package script builds assets first, then creates a timestamped local validation ZIP such as `releases/Yneko-Reimu-v0.2.15-YYYYMMDD-HHMM.zip`.

For a versioned package:

```powershell
pwsh tools/package-theme.ps1 -Version v0.2.15
```

For a stable release artifact name, used by GitHub Actions:

```powershell
pwsh tools/package-theme.ps1 -OutputName Yneko-Reimu-v0.2.15.zip
```

`npm run package` uses a Node wrapper that calls PowerShell 7 (`pwsh`) on Linux/macOS/CI and falls back to Windows PowerShell on Windows. Local development expects Node.js 24, npm with `package-lock.json`, PHP 8.0+, and Composer for PHPCS/WPCS.

## Package Boundaries

The ZIP includes PHP templates, `inc/`, `template-parts/`, translations, runtime images, minified assets, `style.css`, `theme.json`, `screenshot.png`, runtime `readme.txt`, runtime `LICENSE.txt`, `NOTICE.md`, and the current version release notes.

The ZIP excludes repository README files, historical release notes, development/QA/maintenance docs, development source mirrors, source CSS/JS, build tools, `manifest.json`, source maps, gettext source files (`.po`/`.pot`), cache folders, duplicated Vite-copied image assets, large PNG background fallbacks, local E2E QA config/tests, and local release artifacts.

GitHub Release update checks expect the stable release asset to be named `Yneko-Reimu-vX.Y.Z.zip`. Do not rely on GitHub's generated source archives for theme updates.

Before packaging a public release, make sure `theme/Yneko-Reimu/screenshot.png` has been regenerated as a standard `1200x900` PNG and that `style.css` / `readme.txt` compatibility fields match the version actually tested.

## CI

`.github/workflows/release-package.yml` runs:

- Node dependency installation with `npm ci`.
- Composer dependency installation.
- Full `npm run check`.
- `npm audit --audit-level=moderate`.
- Release ZIP packaging.
- Release ZIP package validation.

Tag pushes matching `v*.*.*` upload the package to the GitHub Release.

Release notes are read from `docs/release-notes-<tag>.md` when that file exists. For the current release, tag `v0.2.15` uses `docs/release-notes-v0.2.15.md`.
