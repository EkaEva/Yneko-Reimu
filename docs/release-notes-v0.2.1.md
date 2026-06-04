# Yneko-Reimu v0.2.1

## 中文

v0.2.1 是一次发布就绪与本地 QA 补强版本，重点修复审查中发现的发布元数据、直接访问保护、截图标准和安装包说明问题。

### 主要更新

- 补齐所有运行时 PHP 模板的 `ABSPATH` 防直访保护。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.1`。
- 将 `Tested up to` 更新到已验证的 WordPress 7.0 线。
- 新增运行时 `readme.txt`，随主题 ZIP 一起发布安装、隐私/远程资源、版权来源和许可证说明。
- 新增 `npm run check:release-readiness`，检查 PHP guard、主题头字段、运行时 readme 和 `1200x900` 发布截图。
- 新增 Customizer 契约检查，为后续视觉预览设置拆分锁定 panel、section、setting 和 sanitizer。
- 新增 enqueue 契约检查，锁定前台 script/style 句柄、`REIMU_CONFIG` 字段和 nonce 名称。
- 新增 comments/profile 契约检查，锁定登录、资料、评论上传、评论变更和审核状态轮询相关的 AJAX action、nonce、配置键、DOM 选择器和 CSS 锚点。
- 拆分 `inc/enqueue.php` 的前台资源加载函数，降低后续调整资源配置时的维护风险。
- 新增 CSS 拆分计划门禁，记录评论/Profile、播放器、代码块、PhotoSwipe、分享和搜索样式的目标拆分边界与预算。
- 将侧栏 APlayer 播放器增强样式拆分为条件加载的 `reimu-player.css`，降低主 `reimu.css` 体积。
- 将 PhotoSwipe 图片灯箱增强样式拆分为条件加载的 `reimu-photoswipe.css`，与现有 PhotoSwipe 功能开关和懒加载运行时保持一致。
- 将文章分享和微信弹窗增强样式拆分为页面上下文加载的 `reimu-share.css`，与现有懒加载分享运行时保持一致。
- 将代码块/YML 编辑器、虚拟页 highlight 和 Mermaid 内容增强样式拆分为内容上下文加载的 `reimu-code.css`。
- 将搜索页表单、搜索弹窗状态和结果标签增强样式拆分为 `reimu-search.css`，搜索运行时仍保持交互懒加载。
- 将评论、登录弹窗和个人资料弹窗样式拆分为全局加载的 `reimu-comments.css`；评论/Profile 的 AJAX 与运行时逻辑仍保留在主经典脚本中。
- 将登录/个人资料弹窗渲染拆分到内部 `inc/comments/modals.php` 模块，保留原函数名和前端 DOM 合同。
- 扩展发布包检查，要求 ZIP 必须包含运行时 `readme.txt`。
- 替换发布截图为标准 `1200x900` PNG，并移除未使用的 `screenshot.webp`。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.2.1-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.2.1.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。
- 本轮不迁移 OAuth、评论增强、统计或分享功能到插件；这些能力仍按 GitHub Release 主题定位保留为可配置增强。

## English

v0.2.1 is a release-readiness and local-QA hardening release focused on metadata, direct-access protection, screenshot standards, and installable-package documentation.

### Highlights

- Completed `ABSPATH` direct-access guards across runtime PHP templates.
- Synced the theme header, PHP constant, and npm package version to `0.2.1`.
- Updated `Tested up to` to the verified WordPress 7.0 line.
- Added a runtime `readme.txt` with installation, privacy/remote-resource, credit, and license notes in the theme ZIP.
- Added `npm run check:release-readiness` for PHP guards, theme header fields, runtime readme, and the `1200x900` release screenshot.
- Added a Customizer contract check to lock panel, section, setting, and sanitizer contracts before later visual-preview decomposition.
- Added an enqueue contract check for front-end script/style handles, `REIMU_CONFIG` fields, and nonce names.
- Added a comments/profile contract check for AJAX actions, nonces, config keys, DOM selectors, and CSS anchors used by login, profile, comment upload, comment mutation, and review-status polling flows.
- Split the `inc/enqueue.php` front-end asset loader into focused helpers to reduce future resource-configuration maintenance risk.
- Added a CSS split-plan gate covering target boundaries and budgets for comments/profile, player, code content, PhotoSwipe, share, and search styles.
- Split sidebar APlayer enhancement styles into conditional `reimu-player.css`, reducing the main `reimu.css` payload.
- Split PhotoSwipe lightbox enhancement styles into conditional `reimu-photoswipe.css`, aligned with the existing PhotoSwipe feature gate and lazy runtime.
- Split article share and Weixin popup enhancement styles into page-context `reimu-share.css`, aligned with the existing lazy share runtime.
- Split code/YML editor, virtual-page highlight, and Mermaid content enhancement styles into content-context `reimu-code.css`.
- Split search form, search-popup state, and result-label enhancement styles into `reimu-search.css` while keeping the search runtime interaction-lazy.
- Split comments, login modal, and profile modal styles into a global `reimu-comments.css`; comments/profile AJAX and runtime logic remain in the main classic script.
- Split login/profile modal rendering into the internal `inc/comments/modals.php` module while preserving function names and front-end DOM contracts.
- Extended package checks so release ZIPs must include runtime `readme.txt`.
- Replaced the release screenshot with a standard `1200x900` PNG and removed the unused `screenshot.webp`.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.2.1-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.2.1.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
- This release keeps OAuth, comment enhancements, statistics, and sharing as configurable GitHub Release theme enhancements rather than migrating them to plugins.
