# Yneko-Reimu v0.2.5

## 中文

v0.2.5 是一次前台登录态工具条兼容、设置页整理和认证邮件安全收口版本，重点处理管理员登录浏览器访问前台时 Rank Math Analytics/PRO 提示条在页面顶部闪现的问题，补齐后台 TOTP 管理和恢复码，并为注册/忘记密码/资料邮箱验证码增加可配置风控。

### 主要更新

- 新增后台开关“显示前台管理员工具条”，位于 `Yneko-Reimu 设置 -> 常规设置 -> 管理员体验`。
- 默认关闭该开关：管理员前台继续隐藏 WordPress 顶部工具条，并额外隐藏 Rank Math 前台 Analytics/PRO 工具提示，避免顶部短暂闪现。
- 开启该开关后，管理员前台恢复 WordPress admin bar，方便使用 Rank Math、Query Monitor、编辑入口等调试工具。
- 普通 GitHub/评论用户行为不变：前台不显示 admin bar，访问 `/wp-admin/` 仍会被重定向回前台。
- 在 `常规设置 -> 账号安全` 新增当前管理员账号的 TOTP 二次认证管理入口，可在后台生成密钥、扫码绑定或关闭二次认证；该入口复用前台个人资料弹窗的同一套用户 meta，不是全站强制 2FA 开关。
- 启用 TOTP 的账号现在通过 WordPress 后台 `wp-login.php` 密码登录时也需要输入认证器验证码；前台评论登录原有二次认证行为保持不变。
- `wp-login.php` 二次认证输入说明补充“未开启二次认证可留空”，避免未启用用户误解。
- 启用 TOTP 时自动生成一次性恢复码；恢复码明文只显示一次，数据库只保存哈希，每个恢复码使用后立即失效。
- 后台账号安全区的启用按钮会在开启成功后切换为关闭按钮，减少重复按钮；关闭 TOTP 时会清空启用标记、旧密钥、待确认密钥和恢复码。
- 整理设置页信息架构：常规设置拆分为视觉预览、管理员体验、内置页面、站点资源兜底和站点展示链接；扩展页只保留前台增强和第三方资源。
- 为设置页引入统一的设置分组块、控件间距和复选框网格，让后台配置体验更易扫描。
- 修复评论设置、用户设置等面板中复选框与文字分行的问题，并收紧特殊标签/头像框管理行的响应式网格，避免右侧内容超出设置页边界。
- 新增 `安全设置` tab，用于管理注册、忘记密码、资料邮箱验证码的认证邮件风控。
- 默认开启主题级认证邮件保护，覆盖主题前台 AJAX 验证码接口，以及 WordPress 原生 `wp-login.php` 注册/忘记密码入口。
- 新增同一邮箱、同一 IP、同一设备、全站每日预算和同一请求冷却限额；默认值分别为邮箱 `3/小时、8/天`，IP `10/小时、30/天`，设备 `5/小时、15/天`，全站每日 `100` 封，冷却 `60` 秒。
- 设备限制使用随机 Cookie `yneko_reimu_auth_device`，有效期 180 天；服务端只保存哈希，不做浏览器指纹。
- 新增安全报警日志：限额阻断、全站预算达到预警阈值、`wp_mail()` 失败会写入最近 100 条事件，并在设置页 tab 和 Appearance 菜单显示未处理角标。
- 安全报警默认只显示后台角标并写入 `error_log`；管理员邮件报警默认关闭，避免攻击时制造报警邮件风暴。
- 扩展 GitHub OAuth/access contract gate，保护 admin bar 设置、Rank Math 前台工具条兼容选择器和默认关闭策略。
- 扩展 settings admin、comments/profile 和 auth-security contract gate，保护设置分组结构、工具条开关归属、后台 TOTP 管理入口和认证邮件风控合同。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.5`。

### 说明

- 本版本不修改 Rank Math 后台 Analytics、Action Scheduler 或 WP-Cron 行为。
- 如果 Rank Math 后台仍提示 recurring action 调度失败，请单独检查 WP-Cron、Action Scheduler 队列、Rank Math Analytics 模块和服务器计划任务配置。

## English

v0.2.5 is a logged-in front-end toolbar compatibility, settings-page cleanup, and authentication-email security hardening release. It addresses the Rank Math Analytics/PRO toolbar prompt flashing at the top of the front end for administrator browser sessions, adds backend TOTP/recovery-code management, and introduces configurable rate limiting for registration, lost-password, and profile email verification codes.

### Highlights

- Added a new "Show front-end admin toolbar" setting under `Yneko-Reimu Settings -> General -> Administrator experience`.
- The setting is disabled by default: administrators still get a clean front end without the WordPress admin toolbar, and Rank Math front-end Analytics/PRO toolbar prompts are hidden to avoid the top-page flash.
- When enabled, administrators get the normal WordPress admin bar back on the front end for debugging tools such as Rank Math, Query Monitor, and edit links.
- Regular GitHub/comment users are unchanged: the front-end admin bar stays hidden and `/wp-admin/` access still redirects back to the front end.
- Added a current-administrator TOTP management entry under `General -> Account security`, allowing the admin to generate a secret, bind authenticator-app two-factor authentication by QR code, or disable it. It reuses the same user meta as the front-end profile modal and is not a site-wide forced-2FA switch.
- Accounts with TOTP enabled now also require an authenticator code during WordPress `wp-login.php` password login; the existing front-end comment-login 2FA behavior is unchanged.
- The `wp-login.php` two-factor field now explicitly says it can be left empty when two-factor authentication is not enabled.
- Enabling TOTP now automatically generates one-time recovery codes. Plain-text recovery codes are shown only once, only hashes are stored, and each code is invalidated immediately after use.
- The admin account-security enable button now switches into the disable action after TOTP is enabled, reducing duplicate buttons. Disabling TOTP clears the enabled flag, old secret, pending secret, and recovery codes.
- Reorganized the settings page: General now groups visual-preview entry, administrator experience, built-in pages, site resource fallbacks, and display links; Extensions now focuses on front-end enhancements and third-party resources.
- Added consistent settings group blocks, spacing, and checkbox grids so the admin page is easier to scan.
- Fixed checkbox labels wrapping away from their inputs in panels such as Comments and Users, and tightened the special badge/avatar-frame responsive grid so row controls no longer overflow the settings page.
- Added a `Security` tab for authentication email guard settings covering registration, lost-password, and profile email verification codes.
- Enabled theme-level authentication email protection by default for both theme front-end AJAX verification-code endpoints and native WordPress `wp-login.php` registration/lost-password endpoints.
- Added same-email, same-IP, same-device, global daily budget, and same-request cooldown limits. Defaults are email `3/hour, 8/day`, IP `10/hour, 30/day`, device `5/hour, 15/day`, global `100/day`, and `60` seconds cooldown.
- Device limits use the random `yneko_reimu_auth_device` cookie with a 180-day lifetime. The server stores only hashes and does not use browser fingerprinting.
- Added security alert logging: rate-limit blocks, global-budget warnings, and `wp_mail()` failures are kept in the latest 100 events and shown as unhandled badges on the settings tab and Appearance menu.
- Security alerts default to admin badges plus `error_log`; admin email alerts are disabled by default to avoid alert-mail storms during attacks.
- Extended the GitHub OAuth/access contract gate to protect the admin-toolbar setting, Rank Math front-end toolbar compatibility selectors, and the disabled-by-default behavior.
- Extended the settings admin, comments/profile, and auth-security contract gates to protect grouped settings layout, toolbar-setting placement, admin TOTP management, and authentication email guard behavior.
- Synced the theme header, PHP constant, and npm package version to `0.2.5`.

### Notes

- This release does not modify Rank Math backend Analytics, Action Scheduler, or WP-Cron behavior.
- If Rank Math still reports that its recurring Analytics action cannot be scheduled, check WP-Cron, the Action Scheduler queue, the Rank Math Analytics module, and server cron configuration separately.
