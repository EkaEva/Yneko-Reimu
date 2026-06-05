# Yneko-Reimu v0.2.5

## 中文

v0.2.5 是一次前台登录态工具条兼容与设置页整理版本，重点处理管理员登录浏览器访问前台时 Rank Math Analytics/PRO 提示条在页面顶部闪现的问题，并让 `Yneko-Reimu 设置` 更清晰、统一。

### 主要更新

- 新增后台开关“显示前台管理员工具条”，位于 `Yneko-Reimu 设置 -> 常规设置 -> 管理员体验`。
- 默认关闭该开关：管理员前台继续隐藏 WordPress 顶部工具条，并额外隐藏 Rank Math 前台 Analytics/PRO 工具提示，避免顶部短暂闪现。
- 开启该开关后，管理员前台恢复 WordPress admin bar，方便使用 Rank Math、Query Monitor、编辑入口等调试工具。
- 普通 GitHub/评论用户行为不变：前台不显示 admin bar，访问 `/wp-admin/` 仍会被重定向回前台。
- 在 `常规设置 -> 账号安全` 新增当前管理员账号的 TOTP 二次认证管理入口，可在后台生成密钥、扫码绑定或关闭二次认证；该入口复用前台个人资料弹窗的同一套用户 meta，不是全站强制 2FA 开关。
- 整理设置页信息架构：常规设置拆分为视觉预览、管理员体验、内置页面、站点资源兜底和站点展示链接；扩展页只保留前台增强和第三方资源。
- 为设置页引入统一的设置分组块、控件间距和复选框网格，让后台配置体验更易扫描。
- 修复评论设置、用户设置等面板中复选框与文字分行的问题，并收紧特殊标签/头像框管理行的响应式网格，避免右侧内容超出设置页边界。
- 扩展 GitHub OAuth/access contract gate，保护 admin bar 设置、Rank Math 前台工具条兼容选择器和默认关闭策略。
- 扩展 settings admin contract gate，保护设置分组结构、工具条开关归属和后台 TOTP 管理入口。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.5`。

### 说明

- 本版本不修改 Rank Math 后台 Analytics、Action Scheduler 或 WP-Cron 行为。
- 如果 Rank Math 后台仍提示 recurring action 调度失败，请单独检查 WP-Cron、Action Scheduler 队列、Rank Math Analytics 模块和服务器计划任务配置。

## English

v0.2.5 is a logged-in front-end toolbar compatibility and settings-page cleanup release. It addresses the Rank Math Analytics/PRO toolbar prompt flashing at the top of the front end for administrator browser sessions, while making `Yneko-Reimu Settings` clearer and more consistent.

### Highlights

- Added a new "Show front-end admin toolbar" setting under `Yneko-Reimu Settings -> General -> Administrator experience`.
- The setting is disabled by default: administrators still get a clean front end without the WordPress admin toolbar, and Rank Math front-end Analytics/PRO toolbar prompts are hidden to avoid the top-page flash.
- When enabled, administrators get the normal WordPress admin bar back on the front end for debugging tools such as Rank Math, Query Monitor, and edit links.
- Regular GitHub/comment users are unchanged: the front-end admin bar stays hidden and `/wp-admin/` access still redirects back to the front end.
- Added a current-administrator TOTP management entry under `General -> Account security`, allowing the admin to generate a secret, bind authenticator-app two-factor authentication by QR code, or disable it. It reuses the same user meta as the front-end profile modal and is not a site-wide forced-2FA switch.
- Reorganized the settings page: General now groups visual-preview entry, administrator experience, built-in pages, site resource fallbacks, and display links; Extensions now focuses on front-end enhancements and third-party resources.
- Added consistent settings group blocks, spacing, and checkbox grids so the admin page is easier to scan.
- Fixed checkbox labels wrapping away from their inputs in panels such as Comments and Users, and tightened the special badge/avatar-frame responsive grid so row controls no longer overflow the settings page.
- Extended the GitHub OAuth/access contract gate to protect the admin-toolbar setting, Rank Math front-end toolbar compatibility selectors, and the disabled-by-default behavior.
- Extended the settings admin contract gate to protect the grouped settings layout, toolbar-setting placement, and admin TOTP management entry.
- Synced the theme header, PHP constant, and npm package version to `0.2.5`.

### Notes

- This release does not modify Rank Math backend Analytics, Action Scheduler, or WP-Cron behavior.
- If Rank Math still reports that its recurring Analytics action cannot be scheduled, check WP-Cron, the Action Scheduler queue, the Rank Math Analytics module, and server cron configuration separately.
