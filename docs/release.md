# Release

Use GitHub Releases as the primary distribution channel.

## Local Package

```bash
npm run package
```

The package script builds assets first, then copies runtime files from `theme/Yneko-Reimu` into `releases/Yneko-Reimu.zip`.

For a versioned package:

```powershell
pwsh tools/package-theme.ps1 -Version v0.1.2
```

## Package Boundaries

The ZIP includes PHP templates, `inc/`, `template-parts/`, translations, runtime images, and minified assets.

The ZIP excludes development source mirrors, source CSS/JS, build tools, `manifest.json`, cache folders, large PNG background fallbacks, and local release artifacts.

## CI

`.github/workflows/release-package.yml` runs:

- JavaScript syntax checks.
- Vite build and i18n generation.
- Composer install.
- PHPCS/WPCS lint.
- Release ZIP packaging.

Tag pushes matching `v*.*.*` upload the package to the GitHub Release.
