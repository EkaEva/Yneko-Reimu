# Yneko-Reimu v0.1.12

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.12 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。
- 本版本新增前台登录、找回密码和验证码发送的统一错误文案与冷却策略，降低账号枚举风险。

## 更新亮点

- 重构后台配置体验：`Yneko-Reimu 设置` 作为主控制台管理数据、服务、功能开关和列表；`自定义 -> Yneko-Reimu 视觉预览` 保留图片、颜色、布局和文章显示等适合实时预览的配置。
- 精简 Customizer 重复项：友链、音乐曲目、搜索、GitHub OAuth、评论上传、第三方服务等改为只在主设置页管理。
- 新增 SEO 插件兼容：检测 Rank Math、Yoast、AIOSEO、SEOPress、The SEO Framework 后，主题停用重复 meta、OG/Twitter 与 JSON-LD 输出，并补充英文首页 canonical、hreflang 和 Rank Math sitemap 兼容。
- 评论图片和 GIF 上传拆分为独立开关、独立审核、独立大小上限；图片默认 `1MB`，GIF 默认 `3MB`，临时文件默认保留 7 天。
- 评论上传管理支持待审图片/GIF、公共 GIF 库、本地 GIF 自动上传和从媒体库加入 GIF；批准、移出库和删除文件的行为更清晰。
- 登录用户可以编辑和删除自己的评论，并使用新的编辑/删除图标；评论代码块改为深色背景，支持带语言名的 Markdown 围栏。
- 允许管理员上传 SVG 作为站点 Logo 和站点图标，并在后台提示 SVG 安全风险。
- 主题扩展默认更轻，加载动画保持默认开启；APlayer 启用后首次进入页面即显示，但默认 `preload=metadata`，减少首屏完整音频下载。
- 本地搜索索引默认不再公开全文，只输出标题、摘要、分类、标签和 URL；全文索引改为显式开关。
- 侧栏新增可配置的主题内置小组件：标签云、项目、近期文章、近期评论、归档、分类，并支持排序；WordPress 原生小工具区默认关闭。
- 源码目录瘦身：删除本地 WordPress 开发站残留、上游源码快照、旧大图和多张默认横幅变体，默认横幅保留 `assets/images/banner.webp` 一张。

## 修复

- 修复语言切换后登录、注册、忘记密码和个人资料弹窗仍停留在旧语言的问题。
- 修复登录/退出后评论区上传图片、GIF 按钮和文件选择状态必须刷新才同步的问题。
- 修复英文登录和个人资料弹窗部分按钮或文案缺失的问题。
- 修复评论重复提交只显示“评论提交失败”的问题，重复内容现在返回明确提示。
- 修复后台评论上传管理中批准或删除文件时可能出现“链接已过期”的 nonce 问题。
- 修复卡片图片缺少原生 `loading`、`decoding`、`width`、`height` 属性的问题。
- 修复 APlayer 图标按钮缺少可访问名称的问题。
- 修复英文首页 canonical 指向中文首页，以及英文文章 sitemap URL 与 canonical 不一致的兼容问题。
- 修复主题自带 meta description 和 JSON-LD 与主流 SEO 插件重复输出的问题。
- 修复本地搜索索引默认暴露全文内容的问题。
- 修复旧 `hero` 命名残留，统一改为 banner 语义。

## 说明

- 本版本不会删除既有主题设置、评论、用户、友链、曲目或媒体库数据。
- 后台配置入口已重新分工：视觉和布局项继续使用 WordPress `theme_mod`，数据、服务和列表项继续使用 `yneko_reimu_settings`。
- 上传 SVG 前请确认文件来源可信。聊天软件分享图建议使用 JPG 或 PNG `og:image`，不要依赖 SVG 或 WebP 站点图标。
- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.1.12-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.1.12.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.12 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.
- This release also unifies front-end login, password-reset, and verification-code error messages with cooldowns to reduce account enumeration risk.

## Highlights

- Reworked the admin configuration model: `Yneko-Reimu Settings` is now the main control panel for data, services, feature switches, and lists; `Customize -> Yneko-Reimu Visual Preview` keeps image, color, layout, and display options that benefit from live preview.
- Removed duplicated Customizer controls for friend links, music tracks, search, GitHub OAuth, comment uploads, and third-party services.
- Added SEO plugin compatibility. When Rank Math, Yoast, AIOSEO, SEOPress, or The SEO Framework is active, theme meta, OG/Twitter tags, and JSON-LD are suppressed, while multilingual canonical, hreflang, and Rank Math sitemap compatibility remain available.
- Split comment image and GIF uploads into separate enable, review, and size-limit controls. Images default to `1MB`, GIFs default to `3MB`, and temporary uploads default to 7 days.
- Improved the comment upload manager with pending image/GIF review, public GIF library management, local GIF auto-upload, and adding GIFs from the Media Library.
- Logged-in users can edit and delete their own comments. Comment code blocks now render with a dark background and accept language-tagged Markdown fences.
- Administrators can upload SVG files for the site logo and site icon, with a security warning in the admin.
- Theme extension defaults are lighter while the loader remains on by default. APlayer is visible on first load when enabled, but uses `preload=metadata`.
- Local search no longer exposes full post content by default; full-content indexing is now an explicit option.
- Added configurable built-in sidebar widgets for tag cloud, projects, recent posts, recent comments, archives, and categories, with manual ordering. Native WordPress widget areas are off by default.
- Slimmed the source tree by removing local WordPress development leftovers, the upstream source snapshot, older large images, and responsive default banner variants. The bundled default banner is now `assets/images/banner.webp`.

## Fixes

- Fixed login, register, lost-password, and profile modals keeping the previous language after switching languages.
- Fixed comment image/GIF upload controls requiring a full refresh after login or logout state changes.
- Fixed missing English labels/buttons in login and profile modals.
- Fixed duplicate comments only showing a generic front-end failure; duplicates now return a clear message.
- Fixed expired-link nonce failures when approving or deleting comment uploads in the admin.
- Fixed post-card images missing native `loading`, `decoding`, `width`, and `height` attributes.
- Fixed APlayer icon buttons lacking accessible names.
- Fixed English home canonical and English post sitemap URL compatibility with Rank Math.
- Fixed duplicated theme meta description and JSON-LD output when a main SEO plugin is active.
- Fixed local search exposing full post content by default.
- Renamed leftover `hero` terminology to the theme's banner terminology.

## Notes

- This release does not delete existing settings, comments, users, friend links, music tracks, or Media Library data.
- Visual/layout options continue to use WordPress `theme_mod`; data, services, and lists continue to use `yneko_reimu_settings`.
- Only upload trusted SVG files. For social sharing, use a JPG or PNG `og:image` instead of relying on an SVG or WebP site icon.
- Local validation packages use timestamped names such as `Yneko-Reimu-v0.1.12-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.1.12.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
