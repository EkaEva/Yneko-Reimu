# Theme Check Notes

Yneko-Reimu targets GitHub Release distribution rather than the WordPress.org theme directory. WordPress.org review rules are still useful quality guidance, but plugin-territory features are intentionally retained as optional theme integrations.

## Required Local Checks

```bash
npm run check:js
npm run build
composer install
npm run lint:php
npm run package
```

## Intentional Project Choices

- The theme remains Classic Hybrid, not a full Block Theme.
- GitHub OAuth, third-party comments, music, statistics, and visual effects are optional and off by default where they add weight.
- JSON-LD schema is lightweight and can be disabled with `yneko_reimu_schema_enabled`.
- Release packaging is whitelist-based to prevent development files from entering the installable ZIP.

## Manual Checks

- Activate the generated ZIP on a local WordPress site.
- Check homepage, single post, page, archive, search, 404, comments, mobile navigation, and virtual pages.
- Verify that disabled integrations do not enqueue external scripts on a fresh install.
- Check keyboard navigation for menus, search popup, language switcher, and comments.
