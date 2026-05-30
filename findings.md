# Yneko-Reimu Optimization Findings

- Repo is clean at start.
- Current build is a lightweight Node copy/concat script.
- Current release package uses a whitelist and excludes `assets/src` and `assets/reimu-upstream`.
- Largest release ZIP entries are `screenshot.png`, `assets/images/banner.png`, and `assets/images/search-bg.png`.
- Frontend has no jQuery dependency on the public theme script; admin settings registers a small inline script with WordPress `jquery` dependency.
- Theme is currently Classic Hybrid: PHP templates plus `theme.json`, editor styles, and block support.
