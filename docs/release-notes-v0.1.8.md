# Yneko-Reimu v0.1.8

## 安全提醒

- 请不要继续使用 v0.1.6 或更早版本。这些版本在管理员已登录浏览器会话中启动 GitHub 登录时，可能把授权的 GitHub 账号误绑定到管理员账户。
- v0.1.7 已修复该风险；v0.1.8 继续保留普通 GitHub 登录与“绑定/重新绑定 GitHub”入口分离的安全行为。

## 更新亮点

- 评论区图片/GIF 工具支持登录用户直接上传文件；未登录访客仍只能粘贴图片链接，并会看到登录后可上传的提示。
- 评论上传改为临时文件流程：上传时先进入 `wp-content/uploads/yneko-reimu-comments/tmp/`，不立即创建媒体附件；评论发布并通过审核后，实际引用的文件才会转入正式目录并注册为隐藏附件。
- 未提交、未通过、删除或标记垃圾的评论临时文件会被清理；超过 24 小时未使用的临时文件也会通过 WP-Cron 自动清理，降低数据库和上传目录污染。
- 评论图片与 GIF 上传默认大小上限统一改为 `1MB`，管理员仍可在 Yneko-Reimu 设置页中调整。
- Yneko-Reimu 设置页新增评论上传开关、图片/GIF 大小限制、管理员多选上传 GIF，以及按“后台上传 GIF / 用户评论 GIF / 用户评论图片”分组的评论上传管理区。
- 管理员上传的 GIF 会直接进入公共 GIF 表情库；用户评论 GIF 在评论通过后进入待审核，管理员批准后才会进入游客和登录用户都可使用的 GIF 面板。
- 评论区图片和 GIF 显示最大限制为 `200x200`，避免大尺寸动图撑开评论布局。
- 删除评论上传管理区中的附件后，前台评论会自动显示主题内置的缺失图片占位图，不再出现破图图标。
- 如果 WordPress 后台未开启“任何人都可以注册”，前台评论登录弹窗只显示 GitHub 登录入口，不再显示 WordPress 用户名/密码表单。
- 只有公共 GIF 库中的单个 GIF、且没有其它文字内容的评论会自动通过审核；同一用户、邮箱或 IP 一小时内不能重复发布相同的纯文字评论、单图片评论或单 GIF 评论。

## 修复

- 修复评论预览区域的对齐与展开方式，使其直接在评论框下方展开。
- 修复 emoji、GIF、图片浮层面板的交互：面板向下展开，点击面板外任意区域会关闭，点击面板内不会误关闭。
- 修复图片按钮允许选择 GIF 的问题；GIF 只能通过 GIF 按钮上传。
- 修复管理员 GIF 上传表单嵌套在设置保存表单内导致上传失败的问题，并支持一次选择多个 GIF。
- 修复用户上传 GIF 后立即出现在后台管理区的问题；现在只有评论成功发布并通过审核后才进入待审核列表。
- 修复评论上传管理动作在 `admin_init` 阶段可能误拦普通后台请求或 AJAX 请求的问题。
- 修复媒体库隐藏评论上传附件时对当前后台 screen 的空值访问风险。
- 补齐评论 GIF 面板空状态文案与新增评论上传文案的前端国际化配置。

## 说明

- 本版本不会迁移既有评论内容。
- 既有评论中已经写入的旧图片地址仍按原内容显示；如果对应的是主题管理的上传附件且附件已被删除，前台会使用缺失图片占位图兜底。
- 评论上传附件默认从普通媒体库列表隐藏，可通过主题设置页的评论上传管理区处理。

## Security Notice

- Do not continue using v0.1.6 or earlier. Those versions may accidentally bind a GitHub account to an administrator account when GitHub login is started from an already-authenticated administrator browser session.
- v0.1.7 fixed that risk, and v0.1.8 keeps normal GitHub login separate from the explicit GitHub bind/rebind entry.

## Highlights

- Logged-in users can now upload images and GIFs from the comment toolbar. Guests can still paste image URLs and will see a login-to-upload prompt.
- Comment uploads now use a temporary-file flow: files first go under `wp-content/uploads/yneko-reimu-comments/tmp/` without creating Media Library attachments; only files referenced by approved comments are moved into the permanent upload directory and registered as hidden attachments.
- Temporary uploads are cleaned when comments are rejected, deleted, or marked as spam. Unused temporary files older than 24 hours are also cleaned by WP-Cron.
- The default upload limit for both images and GIFs is now `1MB`, adjustable from the Yneko-Reimu settings page.
- The settings page now includes upload toggles, size limits, administrator multi-GIF upload, and an upload manager grouped by administrator GIFs, user comment GIFs, and user comment images.
- Administrator-uploaded GIFs enter the public picker immediately. User-submitted GIFs become pending after comment approval and only enter the public picker after administrator approval.
- Comment images and GIFs are displayed within `200x200` so large animations do not stretch the layout.
- If a managed comment-upload attachment is deleted, front-end comments fall back to the bundled missing-image placeholder instead of showing a broken image.
- When WordPress registration is disabled, the comment login modal only shows GitHub login and hides the WordPress username/password form.
- A single GIF from the public GIF picker, with no extra text, is auto-approved. The same user, email, or IP cannot repeat the same text-only, image-only, or GIF-only comment within one hour.

## Fixes

- Fixed the inline comment preview alignment and expansion behavior.
- Fixed emoji/GIF/image popover behavior: popovers open downward, close on outside click, and stay open when interacting inside the panel.
- Fixed image upload accepting GIF files. GIFs must now use the GIF upload button.
- Fixed administrator GIF uploads failing because the upload form was nested inside the main settings form, and added multi-file upload support.
- Fixed user-uploaded GIFs appearing in the upload manager before the related comment is successfully submitted and approved.
- Fixed comment upload admin actions so they no longer interrupt unrelated admin or AJAX requests.
- Hardened the Media Library hiding filter against missing admin screen data.
- Added missing front-end i18n strings for comment upload and empty GIF picker states.

## Notes

- This release does not migrate existing comments.
- Existing comment image URLs remain as written. Managed theme upload URLs fall back to the bundled placeholder when their attachment has been deleted.
- Comment upload attachments are hidden from the default Media Library list and should be managed from the Yneko-Reimu comment upload manager.
