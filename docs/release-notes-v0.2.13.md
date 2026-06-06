# Yneko-Reimu v0.2.13

## 中文

v0.2.13 是一次复杂度热点清理版本。它按阶段降低设置页、评论/个人资料、PJAX 主运行时、迁移、Customizer、模板标签和 GitHub 登录样式中的维护成本，不新增默认启用功能，也不改变公开主题接口。

### 主要更新

- 拆分设置页和后台 renderer 热点，保留现有 tab、字段名、option key、nonce URL、TOTP 管理控件、审核操作、分组布局和单一保存模型。
- 拆分登录/个人资料 modal PHP renderer，同时保留 `yneko_reimu_login_modal_html()`、`yneko_reimu_profile_modal_html()`、modal ID/class、form 字段、data 属性、ARIA 关联和社交登录 hook 位置。
- 继续拆分评论/个人资料前端源模块，保留 lazy `assets/dist/reimu-comments.js` 运行时、触发锚点、`window.ReimuCommentsRuntime`、AJAX payload、nonce 刷新和审核状态轮询行为。
- 拆分 PJAX/main runtime helper，覆盖 runtime loader、PJAX 工具、config replay、script replay、metadata sync、modal restoration 和 lazy runtime loading，同时保留 `window.ReimuWP.init()`、classic script 输出和 PJAX 排除规则。
- 拆分 legacy migration 和 Customizer 长函数，保留旧设置迁移行为、Customizer section/control ID、`theme_mod` key、sanitizer callback、注册顺序、恢复默认分组和 inline CSS 输出行为。
- 拆分低风险模板标签显示 helper 和 GitHub 登录 layout CSS helper，保留公开 helper 名称、shortcode、transient key、style handle、enqueue hook 和视觉输出合同。
- 更新 PHP 复杂度 baseline、开发文档、release 文档、runtime changelog 以及相关 contract gates，使新的内部模块边界受静态检查保护。

### 说明

- 本版本是维护性发布：不新增默认启用功能，不重命名公开接口，不故意调整前端视觉。
- 公开设置 key、AJAX action、nonce name、post meta key、hook、URL、模板路径、虚拟页面 slug、脚本/样式 handle、`window.REIMU_CONFIG`、`window.ReimuWP`、`window.ReimuCommentsRuntime` 和 classic script 加载方式保持兼容。
- 本版本发布前完成了分阶段本地验证包、静态 contract checks、PHPCS、npm audit、完整 PHP 语法检查、构建、运行时烟测、打包和 ZIP 边界校验。
- 本地 WordPress 手动 QA 仍应在生产发布前覆盖评论/个人资料/auth/upload/PJAX、设置保存、审核操作和 Customizer 预览/保存/恢复默认流程。

## English

v0.2.13 is a complexity-hotspot cleanup release. It reduces maintainability pressure across the settings page, comments/profile, PJAX main runtime, migrations, Customizer, template tags, and GitHub login styles in staged commits. It adds no default-enabled features and does not change public theme interfaces.

### Highlights

- Split settings/admin renderer hotspots while preserving tabs, field names, option keys, nonce URLs, TOTP admin controls, review actions, grouped layout, and the single settings save model.
- Split login/profile modal PHP renderers while keeping `yneko_reimu_login_modal_html()`, `yneko_reimu_profile_modal_html()`, modal IDs/classes, form fields, data attributes, ARIA hooks, and social-login hook placement.
- Further split comments/profile front-end source modules while preserving the lazy `assets/dist/reimu-comments.js` runtime, trigger anchors, `window.ReimuCommentsRuntime`, AJAX payloads, nonce refresh, and review-status polling behavior.
- Split PJAX/main runtime helpers for runtime loading, PJAX utilities, config replay, script replay, metadata sync, modal restoration, and lazy runtime loading while preserving `window.ReimuWP.init()`, classic script output, and PJAX exclusion rules.
- Split legacy migration and Customizer long functions while preserving legacy settings migration behavior, Customizer section/control IDs, `theme_mod` keys, sanitizer callbacks, registration order, restore-default groups, and inline CSS output behavior.
- Split lower-risk template tag display helpers and GitHub login layout CSS helpers while preserving public helper names, shortcode behavior, transient keys, style handles, enqueue hooks, and front-end output contracts.
- Updated the PHP complexity baseline, development docs, release docs, runtime changelog, and related contract gates for the new internal helper boundaries.

### Notes

- This is a maintenance release: no new default-enabled features, no public interface renames, and no intentional visual redesign.
- Public setting keys, AJAX actions, nonce names, post meta keys, hooks, URLs, template paths, virtual page slugs, script/style handles, `window.REIMU_CONFIG`, `window.ReimuWP`, `window.ReimuCommentsRuntime`, and classic script loading remain compatible.
- The release passed staged local validation packages, static contract checks, PHPCS, npm audit, full PHP syntax linting, build, runtime smoke tests, packaging, and ZIP boundary validation before release.
- Before production rollout, local WordPress manual QA should still cover comments/profile/auth/upload/PJAX, settings save flows, review actions, and Customizer preview/save/restore-default flows.
