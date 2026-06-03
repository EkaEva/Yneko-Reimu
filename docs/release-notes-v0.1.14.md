# Yneko-Reimu v0.1.14

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.14 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。
- 主题层继续提供 WordPress 指纹、作者枚举和基础安全响应头兜底。`readme.html`、`license.txt`、登录入口保护、目录访问和 Cloudflare/WAF 规则仍建议在服务器或 CDN 层处理。

## 更新亮点

- 新增文章底部分享按钮系统，支持 8 种服务；默认启用 QQ 和微信，微信分享会生成二维码卡片。
- Customizer 的“分享与社交链接”重整为两段：上方管理文章分享按钮，下方管理 31 种侧栏社交图标。侧栏社交默认只启用 GitHub，其它链接可先填写但不显示。
- 侧栏社交新增 Bilibili 和小红书图标；GitHub 侧栏项新增右上角 GitHub 三角标开关，默认开启。
- 常规设置新增内置项目、归档、关于、友链页面开关，默认全部开启；关闭后对应内置路径返回 404，并从主题默认导航和内置菜单链接中移除。
- 评论区“按热度”改为讨论串综合热度排序，综合父评论点赞、回复数、回复点赞和最近活跃时间；只重排一级评论串，回复仍跟随父评论。
- 评论点赞改为服务端主导 toggle，并加入登录用户和游客匿名 cookie 去重；历史点赞数保留，去重从本版本开始生效。
- 评论图片/GIF 审核状态整理为待审核、已通过、已撤销、已驳回；开启审核后，待审核和驳回媒体只对站长、管理员、编辑和评论本人可见。
- 评论媒体统一限制为一条评论最多 1 个图片或 GIF。重复添加会询问是否替换，未提交就被替换的上传文件会自动清理。
- 后台删除评论媒体时，纯图片或纯 GIF 评论会直接删除；图文评论只移除对应媒体并保留文字，不再用“图片走丢了”占位图表示后台删除。
- 用户标签审核优化：已通过标签只改颜色不会重新审核；改名会保留旧标签并把新名称送审；新增自定义标签会进入待审核列表。
- 用户名下方现在统一显示头像、标签、评论的审核状态提示；审核中常驻，已更新和不通过会短暂显示后自动标记已读。
- 后台评论设置、用户设置及对应审核区域显示待处理数量角标；左侧“外观”和“Yneko-Reimu 设置”菜单也会显示待审核总数。
- 个人资料弹窗优化头像上传、邮箱只读显示、自定义标签勾选对齐，并在弹窗入口和弹窗内禁用鼠标烟花，减少点击延迟。
- 评论区登录用户左侧身份栏底部新增“退出”按钮，保留头像右上角快捷退出按钮。

## 修复

- 修复文章分享图标在部分页面按全屏居中而不是正文区域居中的问题。
- 修复文章分享图标 hover 时显示文本输入光标的问题。
- 修复配置 X、Email、RSS 后侧栏图标空白的问题。
- 修复文章分享没有出现在关于、项目、友链等主题页面对应位置的问题。
- 修复评论区整段轮询刷新可能导致评论区偶发消失的问题；本版本只保留轻量状态更新，不再周期性替换 `#comments`。
- 修复用户自定义标签审核通过后前台标签和个人资料弹窗不同步的问题。
- 修复标签、头像、评论审核通过或驳回后，用户名下方状态需要刷新页面才更新的问题。
- 修复“标签审核中”数量角标显示不完整的问题。
- 修复后台“驳回后文件清理时间”未标明小时单位的问题。
- 修复个人资料弹窗上传头像后按钮文字被图片链接替换的问题。
- 修复邮箱只读字段 hover/focus 时仍像可编辑输入框的问题。
- 修复自定义标签勾选框和输入框没有垂直对齐的问题。

## 说明

- 本版本不会删除既有主题设置、评论、用户、友链、曲目或媒体库数据。
- 已通过的旧自定义标签会在读取或保存时自动补稳定 ID，不需要单独运行数据库迁移脚本。
- 评论媒体新规则只限制图片/GIF 类媒体，不影响普通文字、链接、emoji 或公共 GIF 库已有内容。
- 评论区不再整段轮询刷新。后台审核后的评论内容变化需要刷新页面才能完整同步；用户名下方审核提示会继续轻量更新。
- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.1.14-YYYYMMDD-HHMM.zip`。
- GitHub Actions 会为本 tag 生成 `Yneko-Reimu-v0.1.14.zip`，请上传 Release 附件中的主题 ZIP，不要上传 GitHub 自动源码包。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.14 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.
- Theme-level fallbacks for WordPress fingerprinting, author enumeration, and basic security headers remain in place. Static files such as `readme.html` and `license.txt`, login-route protection, directory access, and Cloudflare/WAF rules should still be handled at the server or CDN layer.

## Highlights

- Added post share buttons for 8 services. QQ and WeChat are enabled by default, and WeChat sharing generates a QR-card popup.
- Reworked the Customizer `Sharing and social links` section into post sharing and sidebar social groups. Sidebar social supports 31 services, with only GitHub enabled by default.
- Added Bilibili and Xiaohongshu sidebar social icons. The GitHub sidebar item now also controls the GitHub corner ribbon switch, enabled by default.
- Added General settings switches for the built-in Projects, Archives, About, and Friend Links pages. They are all enabled by default; disabled built-in paths return 404 and are removed from theme navigation.
- Upgraded comment `By popularity` sorting to a thread-level score based on parent likes, reply count, reply likes, and recent activity. Only top-level threads are reordered.
- Comment likes are now toggled by the server and deduplicated by user ID for logged-in users or an anonymous cookie token for visitors. Existing visible counts are preserved.
- Comment image/GIF review now uses pending, approved, revoked, and rejected states. Reviewed media is visible only to privileged users and the comment author until approved.
- A comment can now keep at most one image or GIF. Adding another media item asks whether to replace the current one, and replaced unsubmitted uploads are cleaned automatically.
- Deleting reviewed comment media now removes the media content. Image-only or GIF-only comments are deleted; mixed text/media comments keep the text.
- User badge review now avoids unnecessary review when an approved label only changes color; renaming an approved label keeps the old label visible while the new name waits for review.
- Avatar, badge, and comment review notices are shown below the username. Pending notices remain, while updated/rejected notices disappear after being marked as read.
- Admin review count badges now appear on the relevant settings tabs, review sections, and the WordPress admin menu entries for Appearance and Yneko-Reimu Settings.
- Improved the profile modal around avatar uploads, read-only email styling, custom badge checkbox alignment, and mouse-firework exclusions inside the modal.
- Added a bottom `Logout` button to the logged-in comment identity column while keeping the existing avatar-corner quick logout button.

## Fixes

- Fixed post share buttons centering against the full viewport instead of the content area on some pages.
- Fixed share buttons showing a text cursor on hover.
- Fixed blank sidebar icons after configuring X, Email, or RSS.
- Fixed share buttons missing from About, Projects, and Friend Links placement areas.
- Fixed the comment area sometimes disappearing because of whole-`#comments` polling refreshes. The theme now keeps only lightweight status updates.
- Fixed approved custom badges not updating on the front end or in the profile modal.
- Fixed avatar, badge, and comment review results requiring a full page refresh before username status notices updated.
- Fixed the `Badge pending review` count marker being clipped.
- Fixed the rejected-file cleanup setting missing its hours unit.
- Fixed the profile avatar upload button text being replaced by the uploaded image URL.
- Fixed the read-only email field still behaving like an editable input on hover/focus.
- Fixed custom badge checkboxes not aligning vertically with their inputs.

## Notes

- This release does not delete existing theme settings, comments, users, friend links, music tracks, or Media Library data.
- Existing approved custom badges receive stable IDs automatically when read or saved. No manual database migration is required.
- The one-media rule only applies to image/GIF media and does not affect text, links, emoji, or existing public GIF library items.
- The comment area no longer performs whole-section polling. Review result notices still update lightly, but full comment content changes after admin review may require a page refresh.
- Local validation packages use timestamped names such as `Yneko-Reimu-v0.1.14-YYYYMMDD-HHMM.zip`.
- GitHub Actions will generate `Yneko-Reimu-v0.1.14.zip` for this tag. Upload the theme ZIP release asset, not GitHub's automatic source archive.
