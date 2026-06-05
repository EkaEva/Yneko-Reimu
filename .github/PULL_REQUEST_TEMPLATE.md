## Summary

- 

## Public Interface Impact

Check any public contract touched by this PR:

- [ ] Settings, `theme_mod`, or option keys
- [ ] Post meta keys
- [ ] AJAX action names, nonce names, or request fields
- [ ] Hooks, filters, shortcodes, rewrite rules, query vars, or URLs
- [ ] Template paths or virtual page slugs
- [ ] Front-end globals, classic script compatibility, or release ZIP contents
- [ ] No public interface changes

If any public interface changed, describe the compatibility or migration path:


## Checks Run

- [ ] `npm run check:js`
- [ ] `npm run build`
- [ ] `npm run check:size`
- [ ] `npm audit --audit-level=moderate`
- [ ] `npm run check`
- [ ] `npm run package`
- [ ] `npm run check:package`
- [ ] `npm run lint:php` / Composer PHPCS
- [ ] Full `php -l` over changed/runtime PHP files

Checks not run and reason:


## Manual QA

Required for UI, comments, uploads, auth, OAuth, settings, PJAX, or release changes.

- WordPress version:
- PHP version:
- Browser/device:
- Theme version or commit:
- QA notes:

Screenshots or recordings:


## Package Impact

- [ ] Release ZIP root remains `Yneko-Reimu`
- [ ] `assets/src`, `node_modules`, `vendor`, tools, local planning files, and `assets/dist/manifest.json` stay out of the ZIP
- [ ] Runtime files needed by WordPress are included
