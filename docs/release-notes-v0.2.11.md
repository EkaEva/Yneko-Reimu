# Yneko-Reimu v0.2.11

## 中文

v0.2.11 是一次深度维护重构版本，重点清理构建日志噪音，并降低 i18n、评论渲染/徽章、设置页、设置 sanitizer 和 GitHub 登录样式的复杂度热点。本版本不新增功能，也不改变公开主题接口。

### 主要更新

- 清理 classic build 配置中已被 `codeSplitting: false` 忽略的 `inlineDynamicImports` 选项，构建日志不再输出该非阻塞警告。
- 将 i18n 入口拆成设置/语言、URL、本地化文章关系、请求处理和查询过滤模块，同时保留 `/en/` URL、rewrite、locale filter、REST/main query filter、翻译 meta key 和 sitemap/SEO 调用点。
- 将评论渲染与用户徽章拆成内部模块，保留评论 DOM anchor、CSS selector、nonce、AJAX response shape、用户 meta key 和审核流程。
- 将设置页 callback 的页面上下文、tab 导航、常规 tab、浮动保存条和隐藏上传表单拆成内部 renderer，同时保留设置页 callback 和所有 `yneko_reimu_settings[...]` 字段名。
- 将 `yneko_reimu_sanitize_settings()` 的分组 sanitizer 拆成内部 helper，但继续保留它作为唯一注册回调，并保留默认值、legacy fallback 和保存行为。
- 将 GitHub 登录 inline CSS 拆到 `inc/github-login/styles.php` 的专用 CSS helper，保留 `yneko-reimu-github-login` style handle、登录页/前台 enqueue hook、按钮 markup、popup 和 OAuth contract。
- 更新 PHP 复杂度 baseline 和相关 contract gates，覆盖新的内部模块边界。

### 说明

- 公开函数名、设置 key、post meta key、AJAX/action/hook、URL、脚本/样式 handle、classic script 加载方式和 release ZIP 边界保持兼容。
- 本版本发布前完成了本地静态验证、PHPCS、npm audit、完整 PHP 语法检查、构建、运行时烟测、打包和 ZIP 边界校验。

## English

v0.2.11 is a deep maintenance refactor release. It removes build-log noise and reduces complexity hotspots in i18n, comment rendering/badges, the settings page, the settings sanitizer, and GitHub login styling. This release adds no features and does not change public theme interfaces.

### Highlights

- Removed ignored `inlineDynamicImports` options from the classic build config so the non-blocking warning no longer appears.
- Split i18n internals into settings/language, URL, translated-post relation, request handling, and query-filter modules while preserving `/en/` URLs, rewrites, locale filters, REST/main query filters, translation meta keys, and sitemap/SEO call sites.
- Split comment rendering and user badge helpers into internal modules while preserving comment DOM anchors, CSS selectors, nonces, AJAX response shapes, user meta keys, and review flows.
- Split settings page context, navigation tabs, the General tab, floating submit bar, and hidden upload form into internal renderers while keeping the registered settings page callback and all `yneko_reimu_settings[...]` field names.
- Split grouped sanitizer helpers out of `yneko_reimu_sanitize_settings()` while keeping it as the only registered callback and preserving defaults, legacy fallbacks, and save behavior.
- Moved GitHub login inline CSS into dedicated helpers in `inc/github-login/styles.php` while preserving the `yneko-reimu-github-login` style handle, login/front-end enqueue hooks, button markup, popup behavior, and OAuth contract.
- Updated the PHP complexity baseline and contract gates for the new internal module boundaries.

### Notes

- Public function names, setting keys, post meta keys, AJAX/action/hook names, URLs, script/style handles, classic script loading, and the release ZIP boundary remain compatible.
- This release passed local static validation, PHPCS, npm audit, full PHP syntax linting, build, runtime smoke tests, packaging, and ZIP boundary validation before release.
