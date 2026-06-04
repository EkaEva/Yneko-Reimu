# Yneko-Reimu v0.2.3

## 中文

v0.2.3 是一次 Comments 渲染层维护性拆分版本，重点继续降低 `inc/comments.php` 的复杂度，同时保持评论、资料、登录、上传、PJAX 和远程请求行为不变。

### 主要更新

- 将评论表单工具栏、访客字段、登录链接、当前用户身份 HTML、评论头像、作者链接、Markdown 渲染、UA/IP badge、评论列表 callback 和外部评论面板渲染拆分到内部 `inc/comments/rendering.php` 模块。
- 保留 `inc/comments.php` 作为评论/Profile 统一入口，并继续由入口加载 `uploads.php`、`modals.php`、`auth.php`、`profile.php`、`mutations.php` 和新的 `rendering.php`。
- 保持评论 DOM class、data attribute、动态 nonce、`wp_list_comments()` callback、外部评论服务脚本输出、`ipwho.is` 地区查询与缓存行为不变。
- 扩展 `npm run check:comments-profile`，覆盖新的 PHP 渲染模块边界、评论 markup 锚点、动态 nonce 和外部评论面板锚点。
- 将主题版本、PHP 常量、npm 包版本同步到 `0.2.3`。

### 验证提示

- 本地验证包使用时间戳命名，例如 `Yneko-Reimu-v0.2.3-YYYYMMDD-HHMM.zip`。
- 本轮只生成开发验证包，不创建 `v0.2.3` tag；正式 GitHub Release 后续单独执行。

## English

v0.2.3 is a comments-rendering maintainability split focused on reducing `inc/comments.php` complexity while preserving comment, profile, login, upload, PJAX, and remote-request behavior.

### Highlights

- Split comment toolbar, guest fields, login link, current-user identity HTML, comment avatars, author links, Markdown rendering, UA/IP badges, the comment-list callback, and external comment panel rendering into the internal `inc/comments/rendering.php` module.
- Kept `inc/comments.php` as the comments/profile entrypoint, still loading `uploads.php`, `modals.php`, `auth.php`, `profile.php`, `mutations.php`, and the new `rendering.php`.
- Preserved comment DOM classes, data attributes, dynamic nonces, the `wp_list_comments()` callback, external comment service script output, and the existing `ipwho.is` region lookup/cache behavior.
- Extended `npm run check:comments-profile` to cover the new PHP rendering module boundary, comment markup anchors, dynamic nonces, and external comment panel anchors.
- Synced the theme header, PHP constant, and npm package version to `0.2.3`.

### Verification Notes

- Local validation packages use timestamped names such as `Yneko-Reimu-v0.2.3-YYYYMMDD-HHMM.zip`.
- This round creates a local development validation package only. Do not create the `v0.2.3` tag until the formal GitHub Release step is requested.
