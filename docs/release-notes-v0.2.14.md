# Yneko-Reimu v0.2.14

## 中文

v0.2.14 是一次深度维护与高风险流降险版本。它继续收敛评论、登录、个人资料、上传和 PJAX 的维护风险，把大请求处理器和运行时协调逻辑拆成更清晰的内部边界，同时保持公开接口和 Classic Hybrid 加载方式不变。

### 主要更新

- 拆分评论登录/注册/找回密码内部服务边界，保留现有 AJAX action、nonce、请求字段、错误语义、登录状态刷新和 GitHub OAuth 入口行为。
- 拆分个人资料 payload、保存、状态、邮箱验证码、头像和 TOTP 内部模块，保留 profile modal、状态轮询、临时 response-only 状态、用户 meta key 和 JSON response shape。
- 拆分评论提交、编辑、删除、点赞、可见性和审核状态内部模块，保留评论表单、回复、排序、load more、owner actions、上传审核状态和现有 DOM anchor。
- 把评论上传前端流程移入 focused source module，继续构建到 lazy classic `assets/dist/reimu-comments.js`，并保留 `window.ReimuCommentsRuntime`、懒加载触发条件、上传替换/discard 和审核状态刷新行为。
- 将 PJAX 替换流程整理成 capture、detach、replace、sync、restore、rebind 和 verification 生命周期，保留 `window.ReimuWP.init()`、`window.ReimuWP.navigate()`、link exclusion、script replay、APlayer preservation、modal restoration 和 classic script compatibility。
- 加强 comments/profile、auth-security、PJAX runtime 和 runtime smoke contract gates，使懒加载触发、重复初始化防抖、登录状态刷新、profile 临时状态、PJAX modal 恢复、评论表单 rebind 和 comment upload runtime 边界受静态检查保护。
- 更新维护记录和 PHP 复杂度 baseline：`inc/comments/auth.php`、`inc/comments/mutations.php`、`inc/comments/profile.php` 和 General settings panel 已退出主要复杂度热点列表。

### 说明

- 本版本不新增、不重命名、不删除公开接口。
- 公开设置 key、AJAX action、nonce name、payload 字段、JSON response shape、post meta key、hook、URL、模板路径、虚拟页面 slug、DOM anchor、script/style handle、`window.REIMU_CONFIG`、`window.ReimuWP`、`window.ReimuCommentsRuntime` 和 classic script 加载方式保持兼容。
- 发布前必须完成自动检查、包边界校验和 WordPress 手动 QA。手动 QA 覆盖登录、注册、找回密码、GitHub popup、profile 保存/TOTP/头像/标签/邮箱验证码、评论提交/回复/编辑/删除/点赞/排序/load more、图片/GIF 上传，以及 PJAX 后 modal、评论、上传、状态轮询不重复绑定。

## English

v0.2.14 is a deep maintenance and risk-reduction release. It lowers maintenance risk around comments, authentication, profile, uploads, and PJAX by moving large request handlers and runtime coordination code behind clearer internal boundaries while preserving the public theme surface and Classic Hybrid loading model.

### Highlights

- Split the comments authentication internals for login state, login, registration, and lost-password flows while preserving AJAX actions, nonces, request fields, error semantics, login-state refresh, and GitHub OAuth entry behavior.
- Split profile payload, save, status, email-code, avatar, and TOTP internals while preserving the profile modal, status polling, temporary response-only statuses, user meta keys, and JSON response shapes.
- Split comment submit, edit, delete, like, visibility, and review-status internals while preserving the comment form, replies, sorting, load more, owner actions, upload review status, and existing DOM anchors.
- Moved comment-upload front-end orchestration into a focused source module while continuing to build the lazy classic `assets/dist/reimu-comments.js` runtime and preserving `window.ReimuCommentsRuntime`, lazy-load triggers, upload replace/discard behavior, and review-status refresh.
- Reworked PJAX replacement into capture, detach, replace, sync, restore, rebind, and verification lifecycle steps while preserving `window.ReimuWP.init()`, `window.ReimuWP.navigate()`, link exclusions, script replay, APlayer preservation, modal restoration, and classic script compatibility.
- Strengthened comments/profile, auth-security, PJAX runtime, and runtime smoke contract gates so lazy loading, duplicate-init guards, login-state refresh, temporary profile statuses, PJAX modal restoration, comment form rebinding, and comment-upload runtime boundaries stay protected.
- Updated maintenance notes and the PHP complexity baseline: `inc/comments/auth.php`, `inc/comments/mutations.php`, `inc/comments/profile.php`, and the General settings panel no longer appear as primary complexity hotspots.

### Notes

- This release does not add, rename, or remove public interfaces.
- Public setting keys, AJAX actions, nonce names, payload fields, JSON response shapes, post meta keys, hooks, URLs, template paths, virtual page slugs, DOM anchors, script/style handles, `window.REIMU_CONFIG`, `window.ReimuWP`, `window.ReimuCommentsRuntime`, and classic script loading remain compatible.
- Automated checks, package-boundary validation, and WordPress manual QA are required before publishing. Manual QA covers login, registration, lost password, GitHub popup, profile save/TOTP/avatar/tags/email code, comment submit/reply/edit/delete/like/sort/load more, image/GIF uploads, and PJAX modal/comment/upload/status polling without duplicate bindings.
