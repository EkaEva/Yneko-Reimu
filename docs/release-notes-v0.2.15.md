# Yneko-Reimu v0.2.15

## 中文

v0.2.15 是一次发布体验和本地 QA 增强版本。它新增 GitHub Release 一键更新检测，让站点可以通过 WordPress 原生主题更新流程安装正式 Release ZIP；同时补上本地 WordPress + Playwright E2E 回归测试，并修正 E2E 暴露出的语言和 TOTP 设置边界问题。

### 主要更新

- 新增 GitHub Release 更新检测器，接入 WordPress 原生主题更新 transient；后台检测到新正式版本后，可一键下载并安装 Release 附件中的 `Yneko-Reimu-vX.Y.Z.zip`。
- 只读取 GitHub 正式 Release，跳过 draft 和 prerelease，并只接受命名匹配的正式主题 ZIP，不使用 GitHub 自动源码包。
- 在“常规设置”新增 GitHub Release 更新检测开关和缓存时间设置；默认开启，缓存时间默认 360 分钟，可临时调到 5 分钟方便测试，最大 4320 分钟。
- 新增本地 WordPress + Playwright E2E QA 底座，覆盖页面加载、登录、评论、资料弹窗、TOTP 设置、上传入口、自定义鼠标、中文文案和 PJAX 初始化。
- 加固前台 AJAX 语言上下文，避免中文登录 2FA 提示或资料/TOTP 标签在双语场景下落到英文。
- 加固资料弹窗 TOTP 设置状态，避免异步资料刷新清掉正在进行的认证器设置 UI。
- 收紧安装包边界：只保留运行时文档、版权说明和当前版本 release notes；E2E 配置、测试文件、开发工具、源码目录、Playwright 输出、计划文件、构建 manifest、gettext 源文件和重复构建图片不进入主题 ZIP。

### 说明

- 本版本新增设置分组 `updates`，不修改既有设置 key、AJAX action、nonce、模板路径、URL、script/style handle、`window.REIMU_CONFIG` 或 classic script 加载方式。
- 更新检测只在后台更新检查/WordPress 更新流程中运行，失败时静默，不影响前台访问。
- GitHub Release 发布时仍需上传正式主题 ZIP 附件；不要依赖 GitHub 自动生成的 Source code ZIP。

## English

v0.2.15 improves release UX and local QA. It adds a GitHub Release updater so sites can install stable release ZIPs through WordPress native theme updates, adds local WordPress + Playwright E2E coverage, and hardens language/TOTP edge cases exposed by that QA flow.

### Highlights

- Added a GitHub Release updater that feeds WordPress native theme update checks and installs the `Yneko-Reimu-vX.Y.Z.zip` Release asset.
- Checks stable GitHub Releases only, skipping drafts and prereleases, and rejects GitHub-generated source archives.
- Added General settings for enabling GitHub Release update checks and configuring the cache window; defaults are enabled and 360 minutes, with a 5-minute minimum for local testing and a 4320-minute maximum.
- Added local WordPress + Playwright E2E QA for page loading, login, comments, profile modal, TOTP setup, upload controls, custom cursor, Chinese text, and PJAX initialization.
- Hardened front-end AJAX language context so Chinese login 2FA prompts and profile/TOTP labels stay in the active language.
- Preserved in-progress profile TOTP setup state when asynchronous profile data refreshes arrive.
- Tightened installable ZIP boundaries: packages keep only runtime docs, notices, and the current release notes; E2E config, tests, development tools, source assets, Playwright output, planning files, the build manifest, gettext source files, and duplicated build-copied images stay out of theme packages.

### Notes

- This release adds the `updates` settings group without changing existing setting keys, AJAX actions, nonces, template paths, URLs, script/style handles, `window.REIMU_CONFIG`, or classic script loading.
- Update checks run only in admin/update contexts and fail silently when GitHub is unreachable.
- GitHub Releases must still include the formal theme ZIP asset; do not rely on GitHub's generated source archives.
