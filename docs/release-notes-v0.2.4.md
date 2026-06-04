# Yneko-Reimu v0.2.4

## 中文

v0.2.4 第一阶段是一次评论交互和登录页样式修复版本，重点替换前台评论区的浏览器原生确认框，并修复 WordPress 登录页密码显示/隐藏按钮的对齐和边框问题。

### 主要更新

- 将评论区“图片 + GIF <= 1”规则触发的媒体替换确认改为主题样式弹窗，取消时保留当前评论内容，确认时清空旧图片/GIF 并继续插入新媒体。
- 将前台评论删除确认改为同一套主题样式弹窗，确认后继续走原有 `yneko_reimu_delete_comment` AJAX 流程。
- 保留后台设置页上传文件删除的原生确认框；本阶段只替换前台评论体验。
- 修复 WordPress 登录页密码显示/隐藏按钮不居中、出现方框的问题，并保留 hover/focus 可见状态。
- 进一步收紧 WordPress 登录页密码显示/隐藏按钮样式，避免其继承后台默认按钮皮肤导致圆形外框或位置偏移。
- 修复评论正文和预览中的图片/GIF 继承居中排版的问题，媒体内容现在随评论文本靠左显示。
- 二次修正截图反馈：评论区 `.wl-content` 图片/GIF 使用更高优先级的左对齐覆盖；登录页密码图标改为 top/bottom 自动居中并微调 dashicons 视觉基线。
- 扩展 comments/profile 与 GitHub OAuth contract gate，保护前台主题确认弹窗和登录页 `.wp-hide-pw` 样式。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.4`。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.2.4-YYYYMMDD-HHMM.zip`。
- 本轮只生成开发验证包，不创建 `v0.2.4` tag；正式 GitHub Release 后续单独执行。

## English

v0.2.4 phase 1 is a comment interaction and login-page style fix release, focused on replacing browser-native front-end comment confirmations and correcting the WordPress login password visibility button alignment/frame.

### Highlights

- Replaced the comment media replacement confirmation for the "image + GIF <= 1" rule with the theme-styled dialog. Cancel keeps the current comment content; confirm clears the old image/GIF and inserts the new media.
- Replaced front-end comment delete confirmation with the same theme-styled dialog while preserving the existing `yneko_reimu_delete_comment` AJAX flow.
- Kept the admin settings upload-file delete confirmation unchanged; this phase only covers front-end comment interactions.
- Fixed the WordPress login password visibility button so it is centered and no longer renders with an unwanted button frame, while keeping hover/focus states visible.
- Tightened the WordPress login password visibility button override so it cannot inherit the default admin button skin, circular frame, or offset.
- Fixed comment-body and preview media alignment so images/GIFs follow the left-aligned comment text instead of inheriting centered layout.
- Added a second visual pass after screenshot review: comment `.wl-content` media now uses a stronger left-alignment override, and the login password icon uses top/bottom centering with a dashicons baseline adjustment.
- Extended the comments/profile and GitHub OAuth contract gates to protect the front-end theme confirm dialog and login-page `.wp-hide-pw` styling.
- Synced the theme header, PHP constant, and npm package version to `0.2.4`.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.2.4-YYYYMMDD-HHMM.zip`.
- This round creates a local development validation package only. Do not create the `v0.2.4` tag until the formal GitHub Release step is requested.
