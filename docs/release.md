# Release

Use GitHub Releases as the primary distribution channel.

## Local Package

```bash
npm run package
```

The package script builds assets first, then copies runtime files from `theme/Yneko-Reimu` into `releases/Yneko-Reimu.zip`.

For a versioned package:

```powershell
pwsh tools/package-theme.ps1 -Version v0.2.2
```

## Package Boundaries

The ZIP includes PHP templates, `inc/`, `template-parts/`, translations, runtime images, minified assets, `style.css`, `theme.json`, `screenshot.png`, `readme.txt`, and public docs/credits.

The ZIP excludes development source mirrors, source CSS/JS, build tools, `manifest.json`, cache folders, large PNG background fallbacks, and local release artifacts.

Before packaging a public release, make sure `theme/Yneko-Reimu/screenshot.png` has been regenerated as a standard `1200x900` PNG and that `style.css` / `readme.txt` compatibility fields match the version actually tested.

## CI

`.github/workflows/release-package.yml` runs:

- JavaScript syntax checks.
- Vite build and i18n generation.
- Composer install.
- PHPCS/WPCS lint.
- Release ZIP packaging.

Tag pushes matching `v*.*.*` upload the package to the GitHub Release.

Release notes are read from `docs/release-notes-<tag>.md` when that file exists. For the current release, tag `v0.2.2` uses `docs/release-notes-v0.2.2.md`.
